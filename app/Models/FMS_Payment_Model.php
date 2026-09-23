<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Payment_Model extends Model
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
    // GENERATE RECEIPT NUMBER
    // ==============================
    private function generateReceiptNumber()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_payments WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        return 'PAY-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GET INVOICES WITH AN OUTSTANDING BALANCE
    // ==============================
    public function getPayableInvoices()
    {
        return $this->db->query("
            SELECT i.invoice_id, i.invoice_code, i.customer_id, i.total_amount, i.amount_paid, i.outstanding_balance,
                   c.customer_name
            FROM tbl_invoices i
            LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
            WHERE i.outstanding_balance > 0
              AND i.invoice_status NOT IN ('CANCELLED','VOID')
            ORDER BY i.invoice_date DESC
        ")->getResultArray();
    }

    // ==============================
    // GET ALL PAYMENTS
    // ==============================
    public function getAllPayments()
    {
        return $this->db->query("
            SELECT p.*, c.customer_name, i.invoice_code
            FROM tbl_payments p
            LEFT JOIN tbl_customers c ON p.customer_id = c.customer_id
            LEFT JOIN tbl_invoices i ON p.invoice_id = i.invoice_id
            ORDER BY p.payment_date DESC, p.payment_id DESC
        ")->getResultArray();
    }

    // ==============================
    // GET SINGLE PAYMENT
    // ==============================
    public function getPayment($payment_id)
    {
        $query = $this->db->query("
            SELECT p.*, c.customer_name,
                   i.invoice_code, i.total_amount as invoice_total, i.outstanding_balance as invoice_balance
            FROM tbl_payments p
            LEFT JOIN tbl_customers c ON p.customer_id = c.customer_id
            LEFT JOIN tbl_invoices i ON p.invoice_id = i.invoice_id
            WHERE p.payment_id = ?
        ", [$payment_id]);
        return $query->getRowArray();
    }

    // ==============================
    // SAVE PAYMENT
    // ==============================
    public function savePayment()
    {
        $invoice_id = $this->request->getPost('invoice_id');
        $payment_date = $this->request->getPost('payment_date') ?: date('Y-m-d');
        $amount_paid = $this->request->getPost('amount_paid') ?: 0;
        $payment_method = $this->request->getPost('payment_method') ?: 'CASH';
        $bank = $this->request->getPost('bank');
        $reference_number = $this->request->getPost('reference_number');
        $payment_status = $this->request->getPost('payment_status') ?: 'CLEARED';
        $remarks = $this->request->getPost('remarks');

        if (!$invoice_id || $amount_paid <= 0) {
            return ['status' => 'error', 'message' => 'Please select an invoice and enter a valid amount.'];
        }

        $invoice = $this->db->query("SELECT customer_id FROM tbl_invoices WHERE invoice_id = ?", [$invoice_id])->getRow();
        if (!$invoice) {
            return ['status' => 'error', 'message' => 'Invoice not found.'];
        }

        $receipt_number = $this->generateReceiptNumber();

        $query = $this->db->query("
            INSERT INTO `tbl_payments`(
                `receipt_number`, `customer_id`, `invoice_id`, `payment_date`, `amount_paid`,
                `payment_method`, `bank`, `reference_number`, `payment_status`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $receipt_number, $invoice->customer_id, $invoice_id, $payment_date, $amount_paid,
            $payment_method, $bank, $reference_number, $payment_status, $remarks, $this->cuser
        ]);

        if ($query) {
            $payment_id = $this->db->insertID();
            $this->recalculateInvoicePayments($invoice_id);
            return ['status' => 'success', 'message' => 'Payment Recorded Successfully!', 'payment_id' => $payment_id];
        } else {
            $error = $this->db->error();
            log_message('error', 'Payment Save Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while recording payment.'];
        }
    }

    // ==============================
    // UPDATE PAYMENT
    // ==============================
    public function updatePayment()
    {
        $payment_id = $this->request->getPost('payment_id');
        $payment_date = $this->request->getPost('payment_date');
        $amount_paid = $this->request->getPost('amount_paid') ?: 0;
        $payment_method = $this->request->getPost('payment_method') ?: 'CASH';
        $bank = $this->request->getPost('bank');
        $reference_number = $this->request->getPost('reference_number');
        $payment_status = $this->request->getPost('payment_status') ?: 'CLEARED';
        $remarks = $this->request->getPost('remarks');

        $existing = $this->db->query("SELECT invoice_id FROM tbl_payments WHERE payment_id = ?", [$payment_id])->getRow();
        if (!$existing) {
            return ['status' => 'error', 'message' => 'Payment not found.'];
        }

        $query = $this->db->query("
            UPDATE `tbl_payments`
            SET
                `payment_date` = ?, `amount_paid` = ?, `payment_method` = ?, `bank` = ?,
                `reference_number` = ?, `payment_status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `payment_id` = ?
        ", [
            $payment_date, $amount_paid, $payment_method, $bank,
            $reference_number, $payment_status, $remarks, $payment_id
        ]);

        if ($query) {
            $this->recalculateInvoicePayments($existing->invoice_id);
            return ['status' => 'success', 'message' => 'Payment Updated Successfully!'];
        } else {
            $error = $this->db->error();
            log_message('error', 'Payment Update Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while updating payment.'];
        }
    }

    // ==============================
    // DELETE PAYMENT
    // ==============================
    public function deletePayment()
    {
        $payment_id = $this->request->getPost('payment_id');

        $existing = $this->db->query("SELECT invoice_id FROM tbl_payments WHERE payment_id = ?", [$payment_id])->getRow();
        if (!$existing) {
            return ['status' => 'error', 'message' => 'Payment not found.'];
        }

        $query = $this->db->query("DELETE FROM `tbl_payments` WHERE `payment_id` = ?", [$payment_id]);

        if ($query) {
            $this->recalculateInvoicePayments($existing->invoice_id);
            return ['status' => 'success', 'message' => 'Payment Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting payment.'];
        }
    }

    // ==============================
    // UPLOAD RECEIPT ATTACHMENT
    // ==============================
    public function uploadReceipt()
    {
        $payment_id = $this->request->getPost('payment_id');

        if (!$payment_id) {
            return ['status' => 'error', 'message' => 'Missing payment reference.'];
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return ['status' => 'error', 'message' => 'No valid file uploaded.'];
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, $allowedExt)) {
            return ['status' => 'error', 'message' => 'File type not allowed. Only JPG, PNG, GIF, WEBP, PDF.'];
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'File too large. Max 5MB.'];
        }

        $newName = 'PAY_' . $payment_id . '_RECEIPT_' . time() . '.' . $ext;
        $uploadPath = FCPATH . 'uploads/payments/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($file->move($uploadPath, $newName)) {
            $relativePath = 'uploads/payments/' . $newName;
            $this->db->query("UPDATE tbl_payments SET receipt_attachment = ?, updated_at = NOW() WHERE payment_id = ?", [$relativePath, $payment_id]);

            return ['status' => 'success', 'message' => 'Receipt uploaded successfully!', 'path' => $relativePath];
        } else {
            return ['status' => 'error', 'message' => 'Failed to move uploaded file.'];
        }
    }

    // ==============================
    // RECALCULATE INVOICE PAYMENTS (sum of CLEARED payments)
    // ==============================
    private function recalculateInvoicePayments($invoice_id)
    {
        $invoice = $this->db->query("SELECT total_amount, invoice_status FROM tbl_invoices WHERE invoice_id = ?", [$invoice_id])->getRow();
        if (!$invoice) return;

        if (in_array($invoice->invoice_status, ['CANCELLED', 'VOID'])) return;

        $paid = $this->db->query("
            SELECT COALESCE(SUM(amount_paid),0) as total FROM tbl_payments
            WHERE invoice_id = ? AND payment_status = 'CLEARED'
        ", [$invoice_id])->getRow()->total;

        $balance = $invoice->total_amount - $paid;

        if ($balance <= 0) {
            $status = 'PAID';
        } elseif ($paid > 0) {
            $status = 'PARTIALLY_PAID';
        } else {
            $status = in_array($invoice->invoice_status, ['PAID', 'PARTIALLY_PAID']) ? 'ISSUED' : $invoice->invoice_status;
        }

        $this->db->query("
            UPDATE tbl_invoices
            SET amount_paid = ?, outstanding_balance = ?, invoice_status = ?, updated_at = NOW()
            WHERE invoice_id = ?
        ", [$paid, $balance, $status, $invoice_id]);
    }
}
