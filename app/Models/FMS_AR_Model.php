<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_AR_Model extends Model
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
    // GET RECEIVABLES (open invoices with aging)
    // ==============================
    public function getReceivables()
    {
        return $this->db->query("
            SELECT i.invoice_id, i.invoice_code, i.invoice_date, i.due_date,
                   i.total_amount, i.amount_paid, i.outstanding_balance, i.invoice_status,
                   c.customer_id, c.customer_name,
                   DATEDIFF(CURDATE(), i.due_date) as days_overdue,
                   CASE
                       WHEN i.due_date IS NULL OR DATEDIFF(CURDATE(), i.due_date) <= 0 THEN 'CURRENT'
                       WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 1 AND 30 THEN 'DAYS_1_30'
                       WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 31 AND 60 THEN 'DAYS_31_60'
                       WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 61 AND 90 THEN 'DAYS_61_90'
                       WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 91 AND 120 THEN 'DAYS_91_120'
                       ELSE 'OVER_120'
                   END as aging_bucket
            FROM tbl_invoices i
            LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
            WHERE i.outstanding_balance > 0
              AND i.invoice_status NOT IN ('CANCELLED','VOID')
            ORDER BY i.due_date ASC
        ")->getResultArray();
    }

    // ==============================
    // AGING SUMMARY (totals per bucket)
    // ==============================
    public function getAgingSummary()
    {
        $row = $this->db->query("
            SELECT
                COALESCE(SUM(CASE WHEN due_date IS NULL OR DATEDIFF(CURDATE(), due_date) <= 0 THEN outstanding_balance ELSE 0 END), 0) as current_amt,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 1 AND 30 THEN outstanding_balance ELSE 0 END), 0) as days_1_30,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 31 AND 60 THEN outstanding_balance ELSE 0 END), 0) as days_31_60,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 61 AND 90 THEN outstanding_balance ELSE 0 END), 0) as days_61_90,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 91 AND 120 THEN outstanding_balance ELSE 0 END), 0) as days_91_120,
                COALESCE(SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) > 120 THEN outstanding_balance ELSE 0 END), 0) as over_120,
                COALESCE(SUM(outstanding_balance), 0) as total_ar
            FROM tbl_invoices
            WHERE outstanding_balance > 0
              AND invoice_status NOT IN ('CANCELLED','VOID')
        ")->getRowArray();

        return $row;
    }
}
