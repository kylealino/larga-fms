<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_BillingReports_Model extends Model
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
    // GET CUSTOMERS (for filter dropdown)
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
    // REPORT #1 — BILLING SUMMARY
    // ==============================
    public function getBillingSummary($date_from, $date_to, $customer_id = null, $status = null)
    {
        $where = "WHERE b.billing_date BETWEEN ? AND ?";
        $params = [$date_from, $date_to];

        if (!empty($customer_id)) {
            $where .= " AND b.customer_id = ?";
            $params[] = $customer_id;
        }
        if (!empty($status)) {
            $where .= " AND b.billing_status = ?";
            $params[] = $status;
        }

        $rows = $this->db->query("
            SELECT b.billing_id, b.billing_code, b.billing_date, b.billing_basis,
                   b.subtotal, b.vat, b.total, b.billing_status,
                   c.customer_id, c.customer_name
            FROM tbl_billing b
            LEFT JOIN tbl_customers c ON b.customer_id = c.customer_id
            $where
            ORDER BY b.billing_date DESC
        ", $params)->getResultArray();

        $columns = [
            ['key'=>'billing_code','label'=>'Billing #','align'=>'left','format'=>'text'],
            ['key'=>'customer_name','label'=>'Customer','align'=>'left','format'=>'text'],
            ['key'=>'billing_date','label'=>'Billing Date','align'=>'left','format'=>'date'],
            ['key'=>'billing_basis','label'=>'Basis','align'=>'left','format'=>'text'],
            ['key'=>'subtotal','label'=>'Subtotal','align'=>'right','format'=>'currency'],
            ['key'=>'vat','label'=>'VAT','align'=>'right','format'=>'currency'],
            ['key'=>'total','label'=>'Total','align'=>'right','format'=>'currency'],
            ['key'=>'billing_status','label'=>'Status','align'=>'center','format'=>'badge','badge_map'=>[
                'DRAFT'=>'badge-secondary','FOR_INVOICE'=>'badge-warning','INVOICED'=>'badge-success','CANCELLED'=>'badge-danger',
            ]],
        ];

        $sum_subtotal = 0; $sum_vat = 0; $sum_total = 0;
        $draft_count = 0; $invoiced_count = 0;
        foreach ($rows as $r) {
            $sum_subtotal += (float) $r['subtotal'];
            $sum_vat += (float) $r['vat'];
            $sum_total += (float) $r['total'];
            if ($r['billing_status'] === 'DRAFT') $draft_count++;
            if ($r['billing_status'] === 'INVOICED') $invoiced_count++;
        }

        return [
            'title' => 'Billing Summary',
            'columns' => $columns,
            'rows' => $rows,
            'totals' => ['label'=>'TOTAL','values'=>['subtotal'=>$sum_subtotal,'vat'=>$sum_vat,'total'=>$sum_total]],
            'stats' => [
                ['label'=>'Total Billings','value'=>count($rows),'icon'=>'bi-receipt'],
                ['label'=>'Total Amount','value'=>'&#8369;'.number_format($sum_total, 2),'icon'=>'bi-cash-stack'],
                ['label'=>'Draft','value'=>$draft_count,'icon'=>'bi-file-earmark'],
                ['label'=>'Invoiced','value'=>$invoiced_count,'icon'=>'bi-check-circle'],
            ],
        ];
    }

    // ==============================
    // REPORT #2 — INVOICE REPORT (live OVERDUE flag)
    // ==============================
    public function getInvoiceReport($date_from, $date_to, $customer_id = null, $status = null)
    {
        $where = "WHERE i.invoice_date BETWEEN ? AND ?";
        $params = [$date_from, $date_to];

        if (!empty($customer_id)) {
            $where .= " AND i.customer_id = ?";
            $params[] = $customer_id;
        }

        $rows = $this->db->query("
            SELECT i.invoice_id, i.invoice_code, i.invoice_date, i.due_date,
                   i.total_amount, i.outstanding_balance,
                   c.customer_id, c.customer_name,
                   CASE
                       WHEN i.invoice_status IN ('PAID','CANCELLED','VOID','DRAFT') THEN i.invoice_status
                       WHEN i.due_date IS NOT NULL AND i.due_date < CURDATE() AND i.outstanding_balance > 0 THEN 'OVERDUE'
                       ELSE i.invoice_status
                   END as display_status
            FROM tbl_invoices i
            LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
            $where
            ORDER BY i.invoice_date DESC, i.invoice_id DESC
        ", $params)->getResultArray();

        if (!empty($status)) {
            $rows = array_values(array_filter($rows, function ($r) use ($status) {
                return $r['display_status'] === $status;
            }));
        }

        $columns = [
            ['key'=>'invoice_code','label'=>'Invoice #','align'=>'left','format'=>'text'],
            ['key'=>'customer_name','label'=>'Customer','align'=>'left','format'=>'text'],
            ['key'=>'invoice_date','label'=>'Invoice Date','align'=>'left','format'=>'date'],
            ['key'=>'due_date','label'=>'Due Date','align'=>'left','format'=>'date'],
            ['key'=>'total_amount','label'=>'Total','align'=>'right','format'=>'currency'],
            ['key'=>'outstanding_balance','label'=>'Outstanding','align'=>'right','format'=>'currency'],
            ['key'=>'display_status','label'=>'Status','align'=>'center','format'=>'badge','badge_map'=>[
                'DRAFT'=>'badge-secondary','ISSUED'=>'badge-info','PARTIALLY_PAID'=>'badge-warning',
                'PAID'=>'badge-success','OVERDUE'=>'badge-danger','CANCELLED'=>'badge-secondary','VOID'=>'badge-secondary',
            ]],
        ];

        $sum_total = 0; $sum_outstanding = 0; $overdue_count = 0;
        foreach ($rows as $r) {
            $sum_total += (float) $r['total_amount'];
            $sum_outstanding += (float) $r['outstanding_balance'];
            if ($r['display_status'] === 'OVERDUE') $overdue_count++;
        }

        return [
            'title' => 'Invoice Report',
            'columns' => $columns,
            'rows' => $rows,
            'totals' => ['label'=>'TOTAL','values'=>['total_amount'=>$sum_total,'outstanding_balance'=>$sum_outstanding]],
            'stats' => [
                ['label'=>'Total Invoices','value'=>count($rows),'icon'=>'bi-file-earmark-text'],
                ['label'=>'Total Invoiced','value'=>'&#8369;'.number_format($sum_total, 2),'icon'=>'bi-cash-stack'],
                ['label'=>'Total Outstanding','value'=>'&#8369;'.number_format($sum_outstanding, 2),'icon'=>'bi-wallet2'],
                ['label'=>'Overdue','value'=>$overdue_count,'icon'=>'bi-exclamation-triangle'],
            ],
        ];
    }

    // ==============================
    // REPORT #3 — PAYMENT REPORT
    // ==============================
    public function getPaymentReport($date_from, $date_to, $customer_id = null, $status = null)
    {
        $where = "WHERE p.payment_date BETWEEN ? AND ?";
        $params = [$date_from, $date_to];

        if (!empty($customer_id)) {
            $where .= " AND p.customer_id = ?";
            $params[] = $customer_id;
        }
        if (!empty($status)) {
            $where .= " AND p.payment_status = ?";
            $params[] = $status;
        }

        $rows = $this->db->query("
            SELECT p.payment_id, p.receipt_number, p.payment_date, p.payment_method,
                   p.amount_paid, p.payment_status,
                   c.customer_id, c.customer_name,
                   i.invoice_code
            FROM tbl_payments p
            LEFT JOIN tbl_customers c ON p.customer_id = c.customer_id
            LEFT JOIN tbl_invoices i ON p.invoice_id = i.invoice_id
            $where
            ORDER BY p.payment_date DESC
        ", $params)->getResultArray();

        $columns = [
            ['key'=>'receipt_number','label'=>'Receipt #','align'=>'left','format'=>'text'],
            ['key'=>'customer_name','label'=>'Customer','align'=>'left','format'=>'text'],
            ['key'=>'invoice_code','label'=>'Invoice #','align'=>'left','format'=>'text'],
            ['key'=>'payment_date','label'=>'Payment Date','align'=>'left','format'=>'date'],
            ['key'=>'payment_method','label'=>'Method','align'=>'center','format'=>'badge','badge_map'=>[
                'CASH'=>'badge-success','BANK_TRANSFER'=>'badge-info','CHECK'=>'badge-warning',
                'ONLINE_TRANSFER'=>'badge-info','OTHER'=>'badge-secondary',
            ]],
            ['key'=>'amount_paid','label'=>'Amount','align'=>'right','format'=>'currency'],
            ['key'=>'payment_status','label'=>'Status','align'=>'center','format'=>'badge','badge_map'=>[
                'PENDING'=>'badge-warning','CLEARED'=>'badge-success','BOUNCED'=>'badge-danger','CANCELLED'=>'badge-secondary',
            ]],
        ];

        $sum_amount = 0; $sum_collected = 0; $pending_count = 0;
        foreach ($rows as $r) {
            $sum_amount += (float) $r['amount_paid'];
            if ($r['payment_status'] === 'CLEARED') $sum_collected += (float) $r['amount_paid'];
            if ($r['payment_status'] === 'PENDING') $pending_count++;
        }

        return [
            'title' => 'Payment Report',
            'columns' => $columns,
            'rows' => $rows,
            'totals' => ['label'=>'TOTAL','values'=>['amount_paid'=>$sum_amount]],
            'stats' => [
                ['label'=>'Total Payments','value'=>count($rows),'icon'=>'bi-receipt-cutoff'],
                ['label'=>'Total Collected','value'=>'&#8369;'.number_format($sum_collected, 2),'icon'=>'bi-cash-stack'],
                ['label'=>'Pending','value'=>$pending_count,'icon'=>'bi-hourglass-split'],
            ],
        ];
    }

    // ==============================
    // REPORT #4 — ACCOUNTS RECEIVABLE
    // (query copied verbatim from FMS_AR_Model::getReceivables())
    // ==============================
    public function getAccountsReceivable($customer_id = null)
    {
        $rows = $this->db->query("
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

        if (!empty($customer_id)) {
            $rows = array_values(array_filter($rows, function ($r) use ($customer_id) {
                return (string) $r['customer_id'] === (string) $customer_id;
            }));
        }

        $columns = [
            ['key'=>'invoice_code','label'=>'Invoice #','align'=>'left','format'=>'text'],
            ['key'=>'customer_name','label'=>'Customer','align'=>'left','format'=>'text'],
            ['key'=>'invoice_date','label'=>'Invoice Date','align'=>'left','format'=>'date'],
            ['key'=>'due_date','label'=>'Due Date','align'=>'left','format'=>'date'],
            ['key'=>'total_amount','label'=>'Total','align'=>'right','format'=>'currency'],
            ['key'=>'outstanding_balance','label'=>'Balance','align'=>'right','format'=>'currency'],
            ['key'=>'aging_bucket','label'=>'Aging','align'=>'center','format'=>'badge','badge_map'=>[
                'CURRENT'=>'badge-success','DAYS_1_30'=>'badge-info','DAYS_31_60'=>'badge-warning',
                'DAYS_61_90'=>'badge-warning','DAYS_91_120'=>'badge-danger','OVER_120'=>'badge-danger',
            ]],
        ];

        $sum_balance = 0; $overdue_count = 0;
        foreach ($rows as $r) {
            $sum_balance += (float) $r['outstanding_balance'];
            if ((int) $r['days_overdue'] > 0) $overdue_count++;
        }

        return [
            'title' => 'Accounts Receivable',
            'columns' => $columns,
            'rows' => $rows,
            'totals' => ['label'=>'TOTAL','values'=>['outstanding_balance'=>$sum_balance]],
            'stats' => [
                ['label'=>'Total AR','value'=>'&#8369;'.number_format($sum_balance, 2),'icon'=>'bi-cash-stack'],
                ['label'=>'Open Invoices','value'=>count($rows),'icon'=>'bi-file-earmark-text'],
                ['label'=>'Overdue','value'=>$overdue_count,'icon'=>'bi-exclamation-triangle'],
            ],
        ];
    }

    // ==============================
    // REPORT #5 — AR AGING (matrix, grouped by customer)
    // bucket logic reused from FMS_AR_Model::getAgingSummary()/getReceivables()
    // Point-in-time snapshot — ignores date_from/date_to filters.
    // ==============================
    public function getARAging()
    {
        $rows = $this->db->query("
            SELECT customer_id, customer_name,
                SUM(CASE WHEN aging_bucket = 'CURRENT' THEN outstanding_balance ELSE 0 END) as current_amt,
                SUM(CASE WHEN aging_bucket = 'DAYS_1_30' THEN outstanding_balance ELSE 0 END) as days_1_30,
                SUM(CASE WHEN aging_bucket = 'DAYS_31_60' THEN outstanding_balance ELSE 0 END) as days_31_60,
                SUM(CASE WHEN aging_bucket = 'DAYS_61_90' THEN outstanding_balance ELSE 0 END) as days_61_90,
                SUM(CASE WHEN aging_bucket = 'DAYS_91_120' THEN outstanding_balance ELSE 0 END) as days_91_120,
                SUM(CASE WHEN aging_bucket = 'OVER_120' THEN outstanding_balance ELSE 0 END) as over_120,
                SUM(outstanding_balance) as total
            FROM (
                SELECT i.invoice_id, i.outstanding_balance, c.customer_id, c.customer_name,
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
            ) x
            GROUP BY customer_id, customer_name
            ORDER BY total DESC
        ")->getResultArray();

        $bucketColumns = [
            ['key'=>'current_amt','label'=>'Current'],
            ['key'=>'days_1_30','label'=>'1-30 Days'],
            ['key'=>'days_31_60','label'=>'31-60 Days'],
            ['key'=>'days_61_90','label'=>'61-90 Days'],
            ['key'=>'days_91_120','label'=>'91-120 Days'],
            ['key'=>'over_120','label'=>'Over 120'],
        ];

        $grandTotals = ['current_amt'=>0,'days_1_30'=>0,'days_31_60'=>0,'days_61_90'=>0,'days_91_120'=>0,'over_120'=>0,'total'=>0];
        foreach ($rows as $r) {
            foreach ($grandTotals as $k => $v) {
                $grandTotals[$k] += (float) $r[$k];
            }
        }

        return [
            'title' => 'AR Aging',
            'rowLabelKey' => 'customer_name',
            'rowLabel' => 'Customer',
            'bucketColumns' => $bucketColumns,
            'rows' => $rows,
            'grandTotals' => $grandTotals,
            'stats' => [
                ['label'=>'Total AR','value'=>'&#8369;'.number_format($grandTotals['total'], 2),'icon'=>'bi-cash-stack'],
                ['label'=>'Customers with Balance','value'=>count($rows),'icon'=>'bi-people'],
                ['label'=>'Over 120 Days','value'=>'&#8369;'.number_format($grandTotals['over_120'], 2),'icon'=>'bi-exclamation-triangle'],
            ],
        ];
    }
}
