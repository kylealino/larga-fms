<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$shooter = $this->request->getGet('shooter');
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');
require APPPATH . 'ThirdParty/fpdf/fpdf.php';

// ==============================
// GET SHOOTER HISTORY DATA
// ==============================
$history_data = $this->db->query("
    SELECT 
        transaction_id,
        transaction_date,
        checkin_time,
        checkout_time,
        stage_id,
        total_amount,
        status
    FROM tbl_transactions
    WHERE shooter_name = '$shooter'
    AND status != 'CANCELLED'
    ORDER BY transaction_date DESC, checkin_time DESC
")->getResultArray();

$total_transactions = count($history_data);
$total_spent = $this->db->query("
    SELECT SUM(total_amount) as total 
    FROM tbl_transactions 
    WHERE shooter_name = '$shooter' 
    AND status != 'CANCELLED'
")->getRow()->total;

$first_visit = $this->db->query("
    SELECT MIN(transaction_date) as first 
    FROM tbl_transactions 
    WHERE shooter_name = '$shooter'
")->getRow()->first;

$last_visit = $this->db->query("
    SELECT MAX(transaction_date) as last 
    FROM tbl_transactions 
    WHERE shooter_name = '$shooter'
")->getRow()->last;

$pdf = new FPDF('P', 'mm', 'A4');
// REMOVED: $pdf->AliasNbPages();
$pdf->AddPage();

// ==============================
// SET EQUAL MARGINS (20mm each side)
// ==============================
$left_margin = 20;
$right_margin = 20;
$pdf->SetLeftMargin($left_margin);
$pdf->SetRightMargin($right_margin);
$pdf->SetAutoPageBreak(true, 20);

// Available width = 210 - 20 - 20 = 170mm
$available_width = 170;

$pdf->SetTitle('Shooter History - ' . $shooter);

// Header
$pdf->SetFont('Times', 'B', 18);
$pdf->Cell(0, 10, 'QCPD SHOOTING RANGE', 0, 1, 'C');
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 8, 'SHOOTER HISTORY REPORT', 0, 1, 'C');
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 8, 'Shooter: ' . $shooter, 0, 1, 'C');
$pdf->SetFont('Times', 'I', 10);
$pdf->Cell(0, 6, 'Generated: ' . date('F d, Y h:i A'), 0, 1, 'C');
$pdf->Ln(5);

// Summary - Adjusted to fit within available width
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell($available_width / 4, 8, 'Total Visits:', 0, 0, 'L');
$pdf->SetFont('Times', '', 10);
$pdf->Cell($available_width / 4, 8, number_format($total_transactions), 0, 0, 'L');
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell($available_width / 4, 8, 'Total Spent:', 0, 0, 'L');
$pdf->SetFont('Times', '', 10);
$pdf->Cell($available_width / 4, 8, 'P' . number_format($total_spent, 2), 0, 1, 'L');

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell($available_width / 4, 8, 'First Visit:', 0, 0, 'L');
$pdf->SetFont('Times', '', 10);
$pdf->Cell($available_width / 4, 8, $first_visit ? date('F d, Y', strtotime($first_visit)) : 'N/A', 0, 0, 'L');
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell($available_width / 4, 8, 'Last Visit:', 0, 0, 'L');
$pdf->SetFont('Times', '', 10);
$pdf->Cell($available_width / 4, 8, $last_visit ? date('F d, Y', strtotime($last_visit)) : 'N/A', 0, 1, 'L');
$pdf->Ln(5);

// ==============================
// TABLE HEADER - Column widths sum to 170mm
// ==============================
$pdf->SetFont('Times', 'B', 9);
$pdf->SetFillColor(220, 220, 220);

// Column widths for portrait (total = 170mm)
$col_id = 22;
$col_date = 28;
$col_time_in = 24;
$col_time_out = 24;
$col_stage = 28;
$col_amount = 28;
$col_status = 16;
// Total: 22+28+24+24+28+28+16 = 170mm ✓

$pdf->Cell($col_id, 7, 'ID', 1, 0, 'C', true);
$pdf->Cell($col_date, 7, 'DATE', 1, 0, 'C', true);
$pdf->Cell($col_time_in, 7, 'TIME IN', 1, 0, 'C', true);
$pdf->Cell($col_time_out, 7, 'TIME OUT', 1, 0, 'C', true);
$pdf->Cell($col_stage, 7, 'STAGE', 1, 0, 'C', true);
$pdf->Cell($col_amount, 7, 'AMOUNT', 1, 0, 'C', true);
$pdf->Cell($col_status, 7, 'STATUS', 1, 1, 'C', true);

// Table Body
$pdf->SetFont('Times', '', 8);
foreach($history_data as $row) {
    $pdf->Cell($col_id, 6, '#' . $row['transaction_id'], 1, 0, 'C');
    $pdf->Cell($col_date, 6, date('m/d/Y', strtotime($row['transaction_date'])), 1, 0, 'C');
    $pdf->Cell($col_time_in, 6, date('h:i A', strtotime($row['checkin_time'])), 1, 0, 'C');
    $pdf->Cell($col_time_out, 6, $row['checkout_time'] ? date('h:i A', strtotime($row['checkout_time'])) : '—', 1, 0, 'C');
    $pdf->Cell($col_stage, 6, 'Stage ' . $row['stage_id'], 1, 0, 'C');
    $pdf->Cell($col_amount, 6, 'P' . number_format($row['total_amount'], 2), 1, 0, 'R');
    $pdf->SetFont('Times', '', 6);
    $pdf->Cell($col_status, 6, $row['status'], 1, 1, 'C');
}

// ==============================
// FOOTER - PREVENT BLANK 2ND PAGE
// ==============================
// Get current Y position
$current_y = $pdf->GetY();

// Check if we need to add footer on current page or if it will create a new page
if($current_y > 180) {
    // If we're near the bottom, adjust footer position
    $pdf->SetY(-20);
} else {
    // Add small space before footer
    $pdf->Ln(5);
}

// Footer - using PageNo() directly instead of {nb}
$pdf->SetFont('Times', 'I', 8);
$pdf->Cell(0, 5, 'Generated by: ' . $this->cuser . ' | ' . date('Y-m-d h:i A'), 0, 0, 'L');
$pdf->Cell(0, 5, 'Page ' . $pdf->PageNo() . ' of 1', 0, 1, 'R');

$pdf->Output();
exit;
?>