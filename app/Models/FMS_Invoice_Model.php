<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Invoice_Model extends Model
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    // ==============================
    // GENERATE INVOICE CODE
    // ==============================
    private function generateInvoiceCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_invoices WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        return 'INV-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // DUE DATE FROM PAYMENT TERMS (e.g. "30 Days", "COD")
    // ==============================
    private function computeDueDate($invoice_date, $payment_terms)
    {
        $days = 30;
        if ($payment_terms && preg_match('/(\d+)/', $payment_terms, $m)) {
            $days = (int) $m[1];
        } elseif (strtoupper((string) $payment_terms) === 'COD') {
            $days = 0;
        }
        return date('Y-m-d', strtotime($invoice_date . " +{$days} days"));
    }

    // ==============================
    // GET BILLINGS WITHOUT AN INVOICE YET
    // ==============================
    public function getInvoiceableBillings()
    {
        return $this->db->query("
            SELECT b.billing_id, b.billing_code, b.billing_date, b.customer_id, b.total,
                   c.customer_name, c.payment_terms
            FROM tbl_billing b
            LEFT JOIN tbl_customers c ON b.customer_id = c.customer_id
            WHERE b.billing_status != 'CANCELLED'
              AND NOT EXISTS (
                  SELECT 1 FROM tbl_invoices i WHERE i.billing_id = b.billing_id
              )
            ORDER BY b.billing_date DESC
        ")->getResultArray();
    }

    // ==============================
    // GET ALL INVOICES (with live OVERDUE flag)
    // ==============================
    public function getAllInvoices()
    {
        return $this->db->query("
            SELECT i.*,
                   c.customer_name,
                   b.billing_code,
                   CASE
                       WHEN i.invoice_status IN ('PAID','CANCELLED','VOID') THEN i.invoice_status
                       WHEN i.due_date IS NOT NULL AND i.due_date < CURDATE() AND i.outstanding_balance > 0 THEN 'OVERDUE'
                       ELSE i.invoice_status
                   END as display_status
            FROM tbl_invoices i
            LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
            LEFT JOIN tbl_billing b ON i.billing_id = b.billing_id
            ORDER BY i.invoice_date DESC, i.invoice_id DESC
        ")->getResultArray();
    }

    // ==============================
    // GET SINGLE INVOICE
    // ==============================
    public function getInvoice($invoice_id)
    {
        $query = $this->db->query("
            SELECT i.*,
                   c.customer_name, c.business_address, c.contact_person, c.contact_number,
                   b.billing_code, b.billing_basis, b.rate, b.quantity
            FROM tbl_invoices i
            LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
            LEFT JOIN tbl_billing b ON i.billing_id = b.billing_id
            WHERE i.invoice_id = ?
        ", [$invoice_id]);
        return $query->getRowArray();
    }

    // ==============================
    // CREATE INVOICE FROM BILLING
    // ==============================
    public function createInvoiceFromBilling()
    {
        $billing_id = $this->request->getPost('billing_id');
        $invoice_date = $this->request->getPost('invoice_date') ?: date('Y-m-d');
        $payment_terms = $this->request->getPost('payment_terms');
        $remarks = $this->request->getPost('remarks');

        if (!$billing_id) {
            return ['status' => 'error', 'message' => 'Missing billing reference.'];
        }

        $existing = $this->db->query("SELECT invoice_id FROM tbl_invoices WHERE billing_id = ? LIMIT 1", [$billing_id])->getRow();
        if ($existing) {
            return ['status' => 'error', 'message' => 'An invoice already exists for this billing.'];
        }

        $billing = $this->db->query("
            SELECT b.*, c.payment_terms as customer_payment_terms
            FROM tbl_billing b
            LEFT JOIN tbl_customers c ON b.customer_id = c.customer_id
            WHERE b.billing_id = ?
        ", [$billing_id])->getRow();

        if (!$billing) {
            return ['status' => 'error', 'message' => 'Billing not found.'];
        }

        $payment_terms = $payment_terms ?: ($billing->customer_payment_terms ?: '30 Days');
        $due_date = $this->computeDueDate($invoice_date, $payment_terms);
        $invoice_code = $this->generateInvoiceCode();

        $taxable_amount = $billing->subtotal - $billing->discount;
        $total_amount = $billing->total;

        $query = $this->db->query("
            INSERT INTO `tbl_invoices`(
                `invoice_code`, `customer_id`, `billing_id`, `invoice_date`, `due_date`, `payment_terms`,
                `subtotal`, `discount`, `taxable_amount`, `vat`, `other_charges`, `total_amount`,
                `amount_paid`, `outstanding_balance`, `invoice_status`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 'DRAFT', ?, ?)
        ", [
            $invoice_code, $billing->customer_id, $billing_id, $invoice_date, $due_date, $payment_terms,
            $billing->subtotal, $billing->discount, $taxable_amount, $billing->vat, $billing->other_charges, $total_amount,
            $total_amount, $remarks, $this->cuser
        ]);

        if ($query) {
            $invoice_id = $this->db->insertID();
            $this->db->query("UPDATE tbl_billing SET billing_status = 'INVOICED' WHERE billing_id = ?", [$billing_id]);
            return ['status' => 'success', 'message' => 'Invoice Generated Successfully!', 'invoice_id' => $invoice_id];
        } else {
            $error = $this->db->error();
            log_message('error', 'Invoice Create Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while generating invoice.'];
        }
    }

    // ==============================
    // UPDATE INVOICE
    // ==============================
    public function updateInvoice()
    {
        $invoice_id = $this->request->getPost('invoice_id');
        $invoice_date = $this->request->getPost('invoice_date');
        $due_date = $this->request->getPost('due_date');
        $payment_terms = $this->request->getPost('payment_terms');
        $discount = $this->request->getPost('discount') ?: 0;
        $invoice_status = $this->request->getPost('invoice_status') ?: 'DRAFT';
        $remarks = $this->request->getPost('remarks');

        $existing = $this->db->query("SELECT subtotal, vat, other_charges, amount_paid FROM tbl_invoices WHERE invoice_id = ?", [$invoice_id])->getRow();
        if (!$existing) {
            return ['status' => 'error', 'message' => 'Invoice not found.'];
        }

        $taxable_amount = $existing->subtotal - $discount;
        $total_amount = $taxable_amount + $existing->vat + $existing->other_charges;
        $outstanding_balance = $total_amount - $existing->amount_paid;

        $query = $this->db->query("
            UPDATE `tbl_invoices`
            SET
                `invoice_date` = ?, `due_date` = ?, `payment_terms` = ?,
                `discount` = ?, `taxable_amount` = ?, `total_amount` = ?, `outstanding_balance` = ?,
                `invoice_status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `invoice_id` = ?
        ", [
            $invoice_date, $due_date, $payment_terms,
            $discount, $taxable_amount, $total_amount, $outstanding_balance,
            $invoice_status, $remarks, $invoice_id
        ]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Invoice Updated Successfully!'];
        } else {
            $error = $this->db->error();
            log_message('error', 'Invoice Update Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while updating invoice.'];
        }
    }

    // ==============================
    // DELETE INVOICE (only DRAFT, no payments recorded)
    // ==============================
    public function deleteInvoice()
    {
        $invoice_id = $this->request->getPost('invoice_id');

        $invoice = $this->db->query("SELECT invoice_status, amount_paid, billing_id FROM tbl_invoices WHERE invoice_id = ?", [$invoice_id])->getRow();
        if (!$invoice) {
            return ['status' => 'error', 'message' => 'Invoice not found.'];
        }
        if ($invoice->invoice_status !== 'DRAFT' || $invoice->amount_paid > 0) {
            return ['status' => 'error', 'message' => 'Only draft invoices with no payments can be deleted.'];
        }

        $query = $this->db->query("DELETE FROM `tbl_invoices` WHERE `invoice_id` = ?", [$invoice_id]);

        if ($query) {
            $this->db->query("UPDATE tbl_billing SET billing_status = 'FOR_INVOICE' WHERE billing_id = ?", [$invoice->billing_id]);
            return ['status' => 'success', 'message' => 'Invoice Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting invoice.'];
        }
    }
}
