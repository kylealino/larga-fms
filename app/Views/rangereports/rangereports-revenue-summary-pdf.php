<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$from_date = $this->request->getGet('from_date');
$to_date = $this->request->getGet('to_date');
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');
require APPPATH . 'ThirdParty/fpdf/fpdf.php';

if(empty($from_date)) {
    $from_date = date('Y-m-01');
}
if(empty($to_date)) {
    $to_date = date('Y-m-d');
}

$formattedFrom = date('F d, Y', strtotime($from_date));
$formattedTo = date('F d, Y', strtotime($to_date));

// ==============================
// GET REVENUE DATA
// ==============================
$revenue_data = $this->db->query("
    SELECT 
        SUM(rangefee_amount) as total_range_fee,
        SUM(targetboard_amount) as total_targetboard,
        SUM(ammunition_amount) as total_ammunition,
        SUM(total_amount) as total_revenue,
        COUNT(*) as total_transactions
    FROM tbl_transactions
    WHERE transaction_date BETWEEN '$from_date' AND '$to_date'
    AND status != 'CANCELLED'
")->getRow();

// Revenue by Shooter Type
$revenue_by_type = $this->db->query("
    SELECT 
        shooter_type,
        COUNT(*) as total_transactions,
        SUM(total_amount) as total_revenue
    FROM tbl_transactions
    WHERE transaction_date BETWEEN '$from_date' AND '$to_date'
    AND status != 'CANCELLED'
    GROUP BY shooter_type
")->getResultArray();

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

$pdf->SetTitle('Revenue Summary');

// Header
$pdf->SetFont('Times', 'B', 18);
$pdf->Cell(0, 10, 'QCPD SHOOTING RANGE', 0, 1, 'C');
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 8, 'REVENUE SUMMARY', 0, 1, 'C');
$pdf->SetFont('Times', 'I', 11);
$pdf->Cell(0, 6, $formattedFrom . ' - ' . $formattedTo, 0, 1, 'C');
$pdf->Ln(10);

// Summary Box - Using 50/50 split of available width
$pdf->SetFont('Times', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell($available_width / 2, 10, 'Total Transactions:', 0, 0, 'L');
$pdf->Cell($available_width / 2, 10, number_format($revenue_data->total_transactions), 0, 1, 'L');
$pdf->Cell($available_width / 2, 10, 'Total Revenue:', 0, 0, 'L');
$pdf->Cell($available_width / 2, 10, 'P' . number_format($revenue_data->total_revenue, 2), 0, 1, 'L');
$pdf->Ln(5);

// Revenue Breakdown
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 10, 'REVENUE BREAKDOWN', 0, 1, 'L');
$pdf->Ln(3);

// Column widths that sum to 170mm
$col1 = 70;  // Revenue Source
$col2 = 50;  // Amount
$col3 = 50;  // Percentage
// Total: 70 + 50 + 50 = 170mm

$pdf->SetFont('Times', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell($col1, 8, 'Revenue Source', 1, 0, 'C', true);
$pdf->Cell($col2, 8, 'Amount', 1, 0, 'C', true);
$pdf->Cell($col3, 8, 'Percentage', 1, 1, 'C', true);

$pdf->SetFont('Times', '', 10);
$items = [
    ['Range Fee', $revenue_data->total_range_fee],
    ['Target Board', $revenue_data->total_targetboard],
    ['Ammunition', $revenue_data->total_ammunition]
];

foreach($items as $item) {
    $pct = $revenue_data->total_revenue > 0 ? ($item[1] / $revenue_data->total_revenue) * 100 : 0;
    $pdf->Cell($col1, 7, $item[0], 1, 0, 'L');
    $pdf->Cell($col2, 7, 'P' . number_format($item[1], 2), 1, 0, 'R');
    $pdf->Cell($col3, 7, number_format($pct, 1) . '%', 1, 1, 'C');
}

// Revenue by Type
$pdf->Ln(8);
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 10, 'REVENUE BY SHOOTER TYPE', 0, 1, 'L');
$pdf->Ln(3);

// Column widths that sum to 170mm
$col1 = 42;  // Type
$col2 = 42;  // Transactions
$col3 = 43;  // Revenue
$col4 = 43;  // Avg per Trans
// Total: 42 + 42 + 43 + 43 = 170mm

$pdf->SetFont('Times', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell($col1, 8, 'Type', 1, 0, 'C', true);
$pdf->Cell($col2, 8, 'Transactions', 1, 0, 'C', true);
$pdf->Cell($col3, 8, 'Revenue', 1, 0, 'C', true);
$pdf->Cell($col4, 8, 'Avg per Trans', 1, 1, 'C', true);

$pdf->SetFont('Times', '', 10);
foreach($revenue_by_type as $type) {
    $avg = $type['total_transactions'] > 0 ? $type['total_revenue'] / $type['total_transactions'] : 0;
    $pdf->Cell($col1, 7, $type['shooter_type'], 1, 0, 'L');
    $pdf->Cell($col2, 7, number_format($type['total_transactions']), 1, 0, 'C');
    $pdf->Cell($col3, 7, 'P' . number_format($type['total_revenue'], 2), 1, 0, 'R');
    $pdf->Cell($col4, 7, 'P' . number_format($avg, 2), 1, 1, 'R');
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