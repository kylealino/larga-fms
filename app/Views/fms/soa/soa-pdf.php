<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$customer_id = $this->request->getPostGet('customer_id');
$date_from = $this->request->getPostGet('date_from') ?: date('Y-m-01');
$date_to = $this->request->getPostGet('date_to') ?: date('Y-m-d');
require APPPATH . 'ThirdParty/fpdf/fpdf.php';

$formattedDate = date("F j, Y");

$MARGIN_L  = 15;
$MARGIN_R  = 15;
$PAGE_W    = 210;
$PAGE_H    = 297;
$CONTENT_W = $PAGE_W - $MARGIN_L - $MARGIN_R;
$FOOTER_Y  = 280;

$customer = $this->db->query("SELECT * FROM tbl_customers WHERE customer_id = ?", [$customer_id])->getRowArray();
if (!$customer) {
    exit('Customer not found.');
}

// ==============================
// BEGINNING BALANCE
// ==============================
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

// ==============================
// TRANSACTIONS
// ==============================
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
foreach ($transactions as &$t) {
    $running += $t['debit'] - $t['credit'];
    $t['running_balance'] = $running;
}
unset($t);
$ending_balance = $running;

// ==============================
// BUILD PDF
// ==============================
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins($MARGIN_L, 12, $MARGIN_R);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();
$pdf->SetTitle('Statement of Account - ' . $customer['customer_name']);

$Y = 15;

$pdf->SetFont('Helvetica', 'B', 18);
$pdf->SetTextColor(26, 107, 176);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 8, 'LARGA FLEET MANAGEMENT', 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 9);
$pdf->SetTextColor(100, 116, 139);
$pdf->SetXY($MARGIN_L, $pdf->GetY());
$pdf->Cell($CONTENT_W, 4, 'Trucking & Logistics Services  |  Contact: (02) 1234-5678  |  info@largafleet.com', 0, 1, 'C');

$Y = $pdf->GetY() + 3;
$pdf->SetDrawColor(26, 107, 176);
$pdf->SetLineWidth(0.5);
$pdf->Line($MARGIN_L, $Y, $PAGE_W - $MARGIN_R, $Y);
$pdf->SetLineWidth(0.2);

$Y = $pdf->GetY() + 6;
$pdf->SetFont('Helvetica', 'B', 15);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 8, 'STATEMENT OF ACCOUNT', 0, 1, 'C');

$Y = $pdf->GetY() + 4;
$pdf->SetXY($MARGIN_L, $Y);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(28, 5, 'Customer:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($CONTENT_W - 28, 5, $customer['customer_name'], 0, 1, 'L');

$pdf->SetXY($MARGIN_L, $pdf->GetY());
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(28, 5, 'Period:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($CONTENT_W - 28, 5, date('M j, Y', strtotime($date_from)) . ' - ' . date('M j, Y', strtotime($date_to)), 0, 1, 'L');

$Y = $pdf->GetY() + 3;
$pdf->Line($MARGIN_L, $Y, $PAGE_W - $MARGIN_R, $Y);
$Y += 4;

// ============================================
// LEDGER TABLE
// ============================================
$colDate = 22; $colRef = 30; $colDesc = 58; $colDebit = 25; $colCredit = 25; $colBal = $CONTENT_W - $colDate - $colRef - $colDesc - $colDebit - $colCredit;

$pdf->SetXY($MARGIN_L, $Y);
$pdf->SetFillColor(26, 107, 176);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell($colDate, 7, 'Date', 1, 0, 'C', true);
$pdf->Cell($colRef, 7, 'Reference #', 1, 0, 'C', true);
$pdf->Cell($colDesc, 7, 'Description', 1, 0, 'C', true);
$pdf->Cell($colDebit, 7, 'Debit', 1, 0, 'C', true);
$pdf->Cell($colCredit, 7, 'Credit', 1, 0, 'C', true);
$pdf->Cell($colBal, 7, 'Balance', 1, 1, 'C', true);

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetFillColor(232, 242, 250);
$pdf->Cell($colDate + $colRef + $colDesc, 6, '  Beginning Balance', 1, 0, 'L', true);
$pdf->Cell($colDebit, 6, '', 1, 0, 'C', true);
$pdf->Cell($colCredit, 6, '', 1, 0, 'C', true);
$pdf->Cell($colBal, 6, 'PHP ' . number_format($beginning_balance, 2), 1, 1, 'R', true);

$pdf->SetFont('Helvetica', '', 8);
foreach ($transactions as $t) {
    $pdf->Cell($colDate, 6, date('M d, Y', strtotime($t['transaction_date'])), 1, 0, 'C');
    $pdf->Cell($colRef, 6, substr($t['reference_number'], 0, 18), 1, 0, 'C');
    $pdf->Cell($colDesc, 6, substr($t['description'], 0, 38), 1, 0, 'L');
    $pdf->Cell($colDebit, 6, $t['debit'] > 0 ? number_format($t['debit'], 2) : '-', 1, 0, 'R');
    $pdf->Cell($colCredit, 6, $t['credit'] > 0 ? number_format($t['credit'], 2) : '-', 1, 0, 'R');
    $pdf->Cell($colBal, 6, number_format($t['running_balance'], 2), 1, 1, 'R');
}

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetFillColor(232, 242, 250);
$pdf->Cell($colDate + $colRef + $colDesc, 7, '  ENDING BALANCE', 1, 0, 'L', true);
$totalDebit = array_sum(array_column($transactions, 'debit'));
$totalCredit = array_sum(array_column($transactions, 'credit'));
$pdf->Cell($colDebit, 7, number_format($totalDebit, 2), 1, 0, 'R', true);
$pdf->Cell($colCredit, 7, number_format($totalCredit, 2), 1, 0, 'R', true);
$pdf->Cell($colBal, 7, 'PHP ' . number_format($ending_balance, 2), 1, 1, 'R', true);

// ============================================
// FOOTER
// ============================================
// Disable auto page break before positioning the footer — otherwise its own
// Cell() calls can cross the page-break trigger and spawn a stray extra page.
$pdf->SetAutoPageBreak(false);
if ($pdf->GetY() < $PAGE_H - 25) {
    $pdf->SetY(-25);
} else {
    $pdf->Ln(4);
}
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line($MARGIN_L, $pdf->GetY(), $PAGE_W - $MARGIN_R, $pdf->GetY());

$pdf->SetFont('Helvetica', 'I', 8);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell($CONTENT_W, 4, 'This is a system-generated document from Larga Fleet Management System.', 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 7);
$pdf->Cell($CONTENT_W, 3, 'Generated on ' . $formattedDate, 0, 1, 'C');

$pdf->Output();
exit;
?>
