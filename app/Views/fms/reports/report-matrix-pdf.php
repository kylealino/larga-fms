<?php
// Shared matrix PDF renderer (row = entity, columns = buckets) — used by AR Aging.
// Expects:
//   $title         string
//   $subtitle      string
//   $rowLabelKey   string   key in each $rows[] holding the row label (e.g. 'customer_name')
//   $rowLabel      string   column header for the row-label column (e.g. 'Customer')
//   $bucketColumns array    [['key'=>'current_amt','label'=>'Current'], ...]
//   $rows          array    [ ['customer_name'=>'ABC Corp','current_amt'=>1000.00,...,'total'=>1500.00], ... ]
//   $totals        array    ['current_amt'=>..., ..., 'total'=>...]  grand totals row
require APPPATH . 'ThirdParty/fpdf/fpdf.php';

$formattedDate = date('F j, Y g:i A');

$MARGIN_L  = 12;
$MARGIN_R  = 12;
$PAGE_W    = 297;
$PAGE_H    = 210;
$CONTENT_W = $PAGE_W - $MARGIN_L - $MARGIN_R;
$bucketColumns = $bucketColumns ?? [];
$rows = $rows ?? [];
$rowLabelKey = $rowLabelKey ?? 'label';
$rowLabel = $rowLabel ?? 'Name';

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins($MARGIN_L, 12, $MARGIN_R);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();
$pdf->SetTitle($title ?? 'Report');

$pdf->SetFont('Helvetica', 'B', 16);
$pdf->SetTextColor(26, 107, 176);
$pdf->SetXY($MARGIN_L, 14);
$pdf->Cell($CONTENT_W, 7, 'LARGA FLEET MANAGEMENT', 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 8);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell($CONTENT_W, 4, 'Trucking & Logistics Services', 0, 1, 'C');

$Y = $pdf->GetY() + 2;
$pdf->SetDrawColor(26, 107, 176);
$pdf->SetLineWidth(0.5);
$pdf->Line($MARGIN_L, $Y, $PAGE_W - $MARGIN_R, $Y);
$pdf->SetLineWidth(0.2);

$pdf->SetY($Y + 4);
$pdf->SetFont('Helvetica', 'B', 13);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell($CONTENT_W, 7, strtoupper($title ?? 'Report'), 0, 1, 'C');

if (!empty($subtitle)) {
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->SetTextColor(100, 116, 139);
    $pdf->Cell($CONTENT_W, 5, $subtitle, 0, 1, 'C');
}
$pdf->Ln(3);

$labelW = 55;
$bucketW = ($CONTENT_W - $labelW - 25) / max(1, count($bucketColumns));
$totalW = 25;

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetFillColor(26, 107, 176);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell($labelW, 7, $rowLabel, 1, 0, 'L', true);
foreach ($bucketColumns as $bc) {
    $pdf->Cell($bucketW, 7, $bc['label'], 1, 0, 'C', true);
}
$pdf->Cell($totalW, 7, 'Total', 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetFont('Helvetica', '', 7.5);
$pdf->SetTextColor(30, 41, 59);
$fill = false;
if (empty($rows)) {
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell($CONTENT_W, 8, 'No records found.', 1, 1, 'C');
} else {
    foreach ($rows as $row) {
        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 249 : 255, $fill ? 252 : 255);
        $pdf->Cell($labelW, 6, substr($row[$rowLabelKey] ?? '-', 0, 32), 1, 0, 'L', true);
        foreach ($bucketColumns as $bc) {
            $val = $row[$bc['key']] ?? 0;
            $pdf->Cell($bucketW, 6, number_format((float) $val, 2), 1, 0, 'R', true);
        }
        $pdf->Cell($totalW, 6, number_format((float) ($row['total'] ?? 0), 2), 1, 0, 'R', true);
        $pdf->Ln();
        $fill = !$fill;
    }
}

if (!empty($totals)) {
    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->SetFillColor(232, 242, 250);
    $pdf->Cell($labelW, 7, 'GRAND TOTAL', 1, 0, 'L', true);
    foreach ($bucketColumns as $bc) {
        $val = $totals[$bc['key']] ?? 0;
        $pdf->Cell($bucketW, 7, number_format((float) $val, 2), 1, 0, 'R', true);
    }
    $pdf->Cell($totalW, 7, number_format((float) ($totals['total'] ?? 0), 2), 1, 0, 'R', true);
    $pdf->Ln();
}

$pdf->SetAutoPageBreak(false);
if ($pdf->GetY() < $PAGE_H - 20) {
    $pdf->SetY(-20);
} else {
    $pdf->Ln(4);
}
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line($MARGIN_L, $pdf->GetY(), $PAGE_W - $MARGIN_R, $pdf->GetY());
$pdf->SetFont('Helvetica', 'I', 7);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell($CONTENT_W, 4, 'This is a system-generated report from Larga Fleet Management System.', 0, 1, 'C');
$pdf->SetFont('Helvetica', '', 6.5);
$pdf->Cell($CONTENT_W, 3, 'Generated on ' . $formattedDate, 0, 1, 'C');

$pdf->Output();
exit;
