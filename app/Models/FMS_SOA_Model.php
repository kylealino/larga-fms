<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_SOA_Model extends Model
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
    // GENERATE ADJUSTMENT CODE
    // ==============================
    private function generateAdjustmentCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_customer_adjustments WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        return 'ADJ-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GET CUSTOMERS (for picker)
    // ==============================
    public function getCustomers()
    {
        return $this->db->query("
            SELECT customer_id, customer_code, customer_name FROM tbl_customers
            WHERE status = 'ACTIVE'
            ORDER BY customer_name ASC
        ")->getResultArray();
    }

    // ==============================
    // GET STATEMENT OF ACCOUNT
    // Debit = charges (invoices), Credit = payments + credit adjustments
    // ==============================
    public function getStatementOfAccount()
    {
        $customer_id = $this->request->getPost('customer_id');
        $date_from = $this->request->getPost('date_from');
        $date_to = $this->request->getPost('date_to') ?: date('Y-m-d');

        if (!$customer_id) {
            return ['status' => 'error', 'message' => 'Please select a customer.'];
        }
        if (!$date_from) {
            $date_from = date('Y-m-01');
        }

        $customer = $this->db->query("SELECT customer_id, customer_code, customer_name FROM tbl_customers WHERE customer_id = ?", [$customer_id])->getRow();
        if (!$customer) {
            return ['status' => 'error', 'message' => 'Customer not found.'];
        }

        // ------------------------------
        // Beginning balance: everything before date_from
        // ------------------------------
        $priorDebits = $this->db->query("
            SELECT COALESCE(SUM(total_amount),0) as total FROM tbl_invoices
            WHERE customer_id = ? AND invoice_date < ? AND invoice_status NOT IN ('CANCELLED','VOID')
        ", [$customer_id, $date_from])->getRow()->total;

        $priorCredits = $this->db->query("
            SELECT COALESCE(SUM(amount_paid),0) as total FROM tbl_payments
            WHERE customer_id = ? AND payment_date < ? AND payment_status = 'CLEARED'
        ", [$customer_id, $date_from])->getRow()->total;

        $priorAdjDebits = $this->db->query("
            SELECT COALESCE(SUM(amount),0) as total FROM tbl_customer_adjustments
            WHERE customer_id = ? AND adjustment_date < ? AND adjustment_type = 'DEBIT'
        ", [$customer_id, $date_from])->getRow()->total;

        $priorAdjCredits = $this->db->query("
            SELECT COALESCE(SUM(amount),0) as total FROM tbl_customer_adjustments
            WHERE customer_id = ? AND adjustment_date < ? AND adjustment_type = 'CREDIT'
        ", [$customer_id, $date_from])->getRow()->total;

        $beginning_balance = ($priorDebits + $priorAdjDebits) - ($priorCredits + $priorAdjCredits);

        // ------------------------------
        // Transactions within range (invoices, payments, adjustments)
        // ------------------------------
        $transactions = $this->db->query("
            SELECT transaction_date, reference_number, description, debit, credit, sort_order FROM (
                SELECT invoice_date as transaction_date, invoice_code as reference_number,
                       CONCAT('Sales Invoice') as description,
                       total_amount as debit, 0 as credit, 1 as sort_order
                FROM tbl_invoices
                WHERE customer_id = ? AND invoice_date BETWEEN ? AND ? AND invoice_status NOT IN ('CANCELLED','VOID')

                UNION ALL

                SELECT payment_date as transaction_date, receipt_number as reference_number,
                       CONCAT('Payment Received - ', REPLACE(payment_method, '_', ' ')) as description,
                       0 as debit, amount_paid as credit, 2 as sort_order
                FROM tbl_payments
                WHERE customer_id = ? AND payment_date BETWEEN ? AND ? AND payment_status = 'CLEARED'

                UNION ALL

                SELECT adjustment_date as transaction_date, adjustment_code as reference_number,
                       CONCAT('Adjustment - ', COALESCE(reason, adjustment_type)) as description,
                       CASE WHEN adjustment_type = 'DEBIT' THEN amount ELSE 0 END as debit,
                       CASE WHEN adjustment_type = 'CREDIT' THEN amount ELSE 0 END as credit,
                       3 as sort_order
                FROM tbl_customer_adjustments
                WHERE customer_id = ? AND adjustment_date BETWEEN ? AND ?
            ) t
            ORDER BY transaction_date ASC, sort_order ASC
        ", [
            $customer_id, $date_from, $date_to,
            $customer_id, $date_from, $date_to,
            $customer_id, $date_from, $date_to
        ])->getResultArray();

        $running = $beginning_balance;
        $total_debit = 0;
        $total_credit = 0;
        foreach ($transactions as &$t) {
            $running += $t['debit'] - $t['credit'];
            $t['running_balance'] = $running;
            $total_debit += $t['debit'];
            $total_credit += $t['credit'];
        }
        unset($t);

        return [
            'status' => 'success',
            'customer' => $customer,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'beginning_balance' => $beginning_balance,
            'transactions' => $transactions,
            'total_debit' => $total_debit,
            'total_credit' => $total_credit,
            'ending_balance' => $running,
        ];
    }

    // ==============================
    // ADJUSTMENTS
    // ==============================
    public function getAdjustments($customer_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_customer_adjustments WHERE customer_id = ? ORDER BY adjustment_date DESC, adjustment_id DESC
        ", [$customer_id])->getResultArray();
    }

    public function saveAdjustment()
    {
        $customer_id = $this->request->getPost('customer_id');
        $adjustment_date = $this->request->getPost('adjustment_date') ?: date('Y-m-d');
        $adjustment_type = $this->request->getPost('adjustment_type') ?: 'DEBIT';
        $amount = $this->request->getPost('amount') ?: 0;
        $reason = $this->request->getPost('reason');
        $reference_number = $this->request->getPost('reference_number');
        $remarks = $this->request->getPost('remarks');

        if (!$customer_id || $amount <= 0) {
            return ['status' => 'error', 'message' => 'Please select a customer and enter a valid amount.'];
        }

        $adjustment_code = $this->generateAdjustmentCode();

        $query = $this->db->query("
            INSERT INTO `tbl_customer_adjustments`(
                `adjustment_code`, `customer_id`, `adjustment_date`, `adjustment_type`,
                `amount`, `reason`, `reference_number`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [$adjustment_code, $customer_id, $adjustment_date, $adjustment_type, $amount, $reason, $reference_number, $remarks, $this->cuser]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Adjustment Recorded Successfully!'];
        } else {
            $error = $this->db->error();
            log_message('error', 'Adjustment Save Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while recording adjustment.'];
        }
    }

    public function deleteAdjustment()
    {
        $adjustment_id = $this->request->getPost('adjustment_id');
        $query = $this->db->query("DELETE FROM `tbl_customer_adjustments` WHERE `adjustment_id` = ?", [$adjustment_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Adjustment Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting adjustment.'];
        }
    }
}
