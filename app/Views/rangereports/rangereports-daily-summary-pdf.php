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
// GET DAILY SUMMARY DATA
// ==============================
$daily_data = $this->db->query("
    SELECT 
        DATE(transaction_date) as date,
        COUNT(*) as total_transactions,
        SUM(total_amount) as total_revenue,
        SUM(rangefee_amount) as total_range_fee,
        SUM(targetboard_amount) as total_targetboard,
        SUM(ammunition_amount) as total_ammunition,
        SUM(CASE WHEN shooter_type = 'PNP' THEN 1 ELSE 0 END) as pnp_count,
        SUM(CASE WHEN shooter_type = 'CIVILIAN' THEN 1 ELSE 0 END) as civilian_count
    FROM tbl_transactions
    WHERE transaction_date BETWEEN '$from_date' AND '$to_date'
    AND status != 'CANCELLED'
    GROUP BY DATE(transaction_date)
    ORDER BY date ASC
")->getResultArray();

// Get detailed transactions for the period
$transactions = $this->db->query("
    SELECT 
        transaction_id,
        transaction_date,
        checkin_time,
        checkout_time,
        shooter_name,
        shooter_type,
        stage_id,
        rangefee_amount,
        targetboard_amount,
        ammunition_amount,
        total_amount,
        status
    FROM tbl_transactions
    WHERE transaction_date BETWEEN '$from_date' AND '$to_date'
    AND status != 'CANCELLED'
    ORDER BY transaction_date ASC, checkin_time ASC
")->getResultArray();

$total_transactions = 0;
$total_revenue = 0;
$total_range_fee = 0;
$total_targetboard = 0;
$total_ammunition = 0;
$total_pnp = 0;
$total_civilian = 0;

foreach($daily_data as $row) {
    $total_transactions += $row['total_transactions'];
    $total_revenue += $row['total_revenue'];
    $total_range_fee += $row['total_range_fee'];
    $total_targetboard += $row['total_targetboard'];
    $total_ammunition += $row['total_ammunition'];
    $total_pnp += $row['pnp_count'];
    $total_civilian += $row['civilian_count'];
}

// ============================================ //
// CUSTOM PDF WITH FIXED MARGINS
// ============================================ //
class PDF extends FPDF
{
    function Header()
    {
        // No header needed
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, 'Page ' . $this->PageNo() . ' of {nb}', 0, 1, 'R');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetTitle('Daily Transaction Summary');

// Set margins - consistent left and right
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 20);

// ============================================ //
// HEADER
// ============================================ //
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, 'QCPD SHOOTING RANGE', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'DAILY TRANSACTION SUMMARY', 0, 1, 'C');
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 6, $formattedFrom . ' - ' . $formattedTo, 0, 1, 'C');
$pdf->Ln(4);

// ============================================ //
// SUMMARY
// ============================================ //
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 8, 'SUMMARY', 0, 1, 'L');
$pdf->Ln(1);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(45, 6, 'Total Transactions:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 6, number_format($total_transactions), 0, 0, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(35, 6, 'Total Revenue:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 6, 'P ' . number_format($total_revenue, 2), 0, 0, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(22, 6, 'PNP / Civilian:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 6, number_format($total_pnp) . ' / ' . number_format($total_civilian), 0, 1, 'L');

$pdf->Ln(4);

// ============================================ //
// TRANSACTION DETAILS TABLE - FIXED WIDTHS
// ============================================ //
// Fixed column widths that sum to exactly 180mm (210 - 30 margins)
$w_date = 17;
$w_time_in = 16;
$w_time_out = 16;
$w_shooter = 27;
$w_type = 13;
$w_stage = 10;
$w_range = 16;
$w_target = 16;
$w_ammo = 14;
$w_total = 18;
$w_status = 17;

// Verify total width = 180mm
// 17+16+16+27+13+10+16+16+14+18+17 = 180

$pdf->SetFont('Arial', 'B', 6.5);
$pdf->SetFillColor(220, 220, 220);

$pdf->Cell($w_date, 6, 'DATE', 1, 0, 'C', true);
$pdf->Cell($w_time_in, 6, 'TIME IN', 1, 0, 'C', true);
$pdf->Cell($w_time_out, 6, 'TIME OUT', 1, 0, 'C', true);
$pdf->Cell($w_shooter, 6, 'SHOOTER', 1, 0, 'C', true);
$pdf->Cell($w_type, 6, 'TYPE', 1, 0, 'C', true);
$pdf->Cell($w_stage, 6, 'STG', 1, 0, 'C', true);
$pdf->Cell($w_range, 6, 'RANGE', 1, 0, 'C', true);
$pdf->Cell($w_target, 6, 'TARGET', 1, 0, 'C', true);
$pdf->Cell($w_ammo, 6, 'AMMO', 1, 0, 'C', true);
$pdf->Cell($w_total, 6, 'TOTAL', 1, 0, 'C', true);
$pdf->Cell($w_status, 6, 'STATUS', 1, 1, 'C', true);

// Table Body
$pdf->SetFont('Arial', '', 6);
$prev_date = null;

foreach($transactions as $row) {
    $current_date = date('m/d/Y', strtotime($row['transaction_date']));
    $show_date = ($current_date != $prev_date);
    $prev_date = $current_date;
    
    $shooter_name = strlen($row['shooter_name']) > 10 ? substr($row['shooter_name'], 0, 8) . '..' : $row['shooter_name'];
    
    if($show_date) {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell($w_date, 5, $current_date, 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
    } else {
        $pdf->Cell($w_date, 5, '', 1, 0, 'C');
    }
    
    $pdf->Cell($w_time_in, 5, date('h:i A', strtotime($row['checkin_time'])), 1, 0, 'C');
    $pdf->Cell($w_time_out, 5, $row['checkout_time'] ? date('h:i A', strtotime($row['checkout_time'])) : '—', 1, 0, 'C');
    $pdf->Cell($w_shooter, 5, $shooter_name, 1, 0, 'L');
    $pdf->Cell($w_type, 5, substr($row['shooter_type'], 0, 4), 1, 0, 'C');
    $pdf->Cell($w_stage, 5, 'S' . $row['stage_id'], 1, 0, 'C');
    $pdf->Cell($w_range, 5, 'P' . number_format($row['rangefee_amount'], 2), 1, 0, 'R');
    $pdf->Cell($w_target, 5, 'P' . number_format($row['targetboard_amount'], 2), 1, 0, 'R');
    $pdf->Cell($w_ammo, 5, 'P' . number_format($row['ammunition_amount'], 2), 1, 0, 'R');
    $pdf->Cell($w_total, 5, 'P' . number_format($row['total_amount'], 2), 1, 0, 'R');
    $pdf->Cell($w_status, 5, substr($row['status'], 0, 4), 1, 1, 'C');
}

// TOTALS ROW
$pdf->SetFont('Arial', 'B', 6.5);
$pdf->SetFillColor(220, 220, 220);

$pdf->Cell($w_date, 5, '', 1, 0, 'C', true);
$pdf->Cell($w_time_in, 5, '', 1, 0, 'C', true);
$pdf->Cell($w_time_out, 5, '', 1, 0, 'C', true);
$pdf->Cell($w_shooter, 5, 'TOTALS:', 1, 0, 'R', true);
$pdf->Cell($w_type, 5, '', 1, 0, 'C', true);
$pdf->Cell($w_stage, 5, '', 1, 0, 'C', true);
$pdf->Cell($w_range, 5, 'P' . number_format($total_range_fee, 2), 1, 0, 'R', true);
$pdf->Cell($w_target, 5, 'P' . number_format($total_targetboard, 2), 1, 0, 'R', true);
$pdf->Cell($w_ammo, 5, 'P' . number_format($total_ammunition, 2), 1, 0, 'R', true);
$pdf->Cell($w_total, 5, 'P' . number_format($total_revenue, 2), 1, 0, 'R', true);
$pdf->Cell($w_status, 5, '', 1, 1, 'C', true);

$pdf->Ln(6);

// ============================================ //
// DAILY BREAKDOWN - FIXED WIDTHS
// ============================================ //
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 8, 'DAILY BREAKDOWN', 0, 1, 'L');
$pdf->Ln(2);

// Fixed column widths that sum to exactly 180mm
$w_db_date = 27;
$w_db_trans = 20;
$w_db_range = 23;
$w_db_target = 23;
$w_db_ammo = 20;
$w_db_revenue = 23;
$w_db_pnp = 16;
$w_db_civilian = 28;

// Verify total width = 180mm
// 27+20+23+23+20+23+16+28 = 180

$pdf->SetFont('Arial', 'B', 7);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell($w_db_date, 7, 'DATE', 1, 0, 'C', true);
$pdf->Cell($w_db_trans, 7, 'TRANS', 1, 0, 'C', true);
$pdf->Cell($w_db_range, 7, 'RANGE FEE', 1, 0, 'C', true);
$pdf->Cell($w_db_target, 7, 'TARGET BD', 1, 0, 'C', true);
$pdf->Cell($w_db_ammo, 7, 'AMMO', 1, 0, 'C', true);
$pdf->Cell($w_db_revenue, 7, 'REVENUE', 1, 0, 'C', true);
$pdf->Cell($w_db_pnp, 7, 'PNP', 1, 0, 'C', true);
$pdf->Cell($w_db_civilian, 7, 'CIVILIAN', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);
foreach($daily_data as $row) {
    $pdf->Cell($w_db_date, 6, date('m/d/Y', strtotime($row['date'])), 1, 0, 'C');
    $pdf->Cell($w_db_trans, 6, number_format($row['total_transactions']), 1, 0, 'C');
    $pdf->Cell($w_db_range, 6, 'P' . number_format($row['total_range_fee'], 2), 1, 0, 'R');
    $pdf->Cell($w_db_target, 6, 'P' . number_format($row['total_targetboard'], 2), 1, 0, 'R');
    $pdf->Cell($w_db_ammo, 6, 'P' . number_format($row['total_ammunition'], 2), 1, 0, 'R');
    $pdf->Cell($w_db_revenue, 6, 'P' . number_format($row['total_revenue'], 2), 1, 0, 'R');
    $pdf->Cell($w_db_pnp, 6, number_format($row['pnp_count']), 1, 0, 'C');
    $pdf->Cell($w_db_civilian, 6, number_format($row['civilian_count']), 1, 1, 'C');
}

// TOTALS ROW
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell($w_db_date, 7, 'TOTAL', 1, 0, 'C', true);
$pdf->Cell($w_db_trans, 7, number_format($total_transactions), 1, 0, 'C', true);
$pdf->Cell($w_db_range, 7, 'P' . number_format($total_range_fee, 2), 1, 0, 'R', true);
$pdf->Cell($w_db_target, 7, 'P' . number_format($total_targetboard, 2), 1, 0, 'R', true);
$pdf->Cell($w_db_ammo, 7, 'P' . number_format($total_ammunition, 2), 1, 0, 'R', true);
$pdf->Cell($w_db_revenue, 7, 'P' . number_format($total_revenue, 2), 1, 0, 'R', true);
$pdf->Cell($w_db_pnp, 7, number_format($total_pnp), 1, 0, 'C', true);
$pdf->Cell($w_db_civilian, 7, number_format($total_civilian), 1, 1, 'C', true);

$pdf->Output();
exit;
?>