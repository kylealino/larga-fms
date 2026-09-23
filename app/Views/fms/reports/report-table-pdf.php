<?php
// Shared flat-table PDF renderer for all Reports modules.
// Expects (set by the calling controller before returning this view):
//   $title     string   e.g. "Trip Report"
//   $subtitle  string   e.g. "Sep 01, 2026 - Sep 23, 2026"
//   $columns   array    [['key'=>'trip_code','label'=>'Trip Code','align'=>'left','format'=>'text','width'=>25], ...]
//   $rows      array    list of assoc arrays keyed like $columns[]['key']
//   $totals    array|null  optional ['label' => 'TOTAL', 'values' => ['total_amount' => 12345.00, ...]]
require APPPATH . 'ThirdParty/fpdf/fpdf.php';

$formattedDate = date('F j, Y g:i A');

$MARGIN_L  = 12;
$MARGIN_R  = 12;
$PAGE_W    = 297; // Landscape A4 — report tables tend to be wide
$PAGE_H    = 210;
$CONTENT_W = $PAGE_W - $MARGIN_L - $MARGIN_R;
$columns   = $columns ?? [];
$rows      = $rows ?? [];
$totals    = $totals ?? null;

function __pdfCellText($col, $row)
{
    $val = $row[$col['key']] ?? null;
    $format = $col['format'] ?? 'text';
    if ($val === null || $val === '') {
        return '-';
    }
    switch ($format) {
        case 'currency':
            return 'PHP ' . number_format((float) $val, 2);
        case 'number':
            return number_format((float) $val, 2);
        case 'date':
            $ts = strtotime($val);
            return $ts ? date('M d, Y', $ts) : '-';
        case 'badge':
            return str_replace('_', ' ', (string) $val);
        default:
            return (string) $val;
    }
}

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

// Column widths: honor explicit 'width', spread the remainder evenly across the rest
$explicitTotal = 0;
$flexCount = 0;
foreach ($columns as $col) {
    if (isset($col['width'])) {
        $explicitTotal += $col['width'];
    } else {
        $flexCount++;
    }
}
$flexWidth = $flexCount > 0 ? max(18, ($CONTENT_W - $explicitTotal) / $flexCount) : 0;

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetFillColor(26, 107, 176);
$pdf->SetTextColor(255, 255, 255);
foreach ($columns as $col) {
    $w = $col['width'] ?? $flexWidth;
    $align = ($col['align'] ?? 'left') === 'right' ? 'R' : (($col['align'] ?? 'left') === 'center' ? 'C' : 'L');
    $pdf->Cell($w, 7, $col['label'], 1, 0, $align === 'L' ? 'L' : $align, true);
}
$pdf->Ln();

$pdf->SetFont('Helvetica', '', 7.5);
$pdf->SetTextColor(30, 41, 59);
$fill = false;
if (empty($rows)) {
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell($CONTENT_W, 8, 'No records found for the selected filters.', 1, 1, 'C');
} else {
    foreach ($rows as $row) {
        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 249 : 255, $fill ? 252 : 255);
        foreach ($columns as $col) {
            $w = $col['width'] ?? $flexWidth;
            $align = ($col['align'] ?? 'left') === 'right' ? 'R' : (($col['align'] ?? 'left') === 'center' ? 'C' : 'L');
            $text = __pdfCellText($col, $row);
            $pdf->Cell($w, 6, substr($text, 0, 60), 1, 0, $align, true);
        }
        $pdf->Ln();
        $fill = !$fill;
    }
}

if ($totals) {
    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->SetFillColor(232, 242, 250);
    $first = true;
    foreach ($columns as $col) {
        $w = $col['width'] ?? $flexWidth;
        $align = ($col['align'] ?? 'left') === 'right' ? 'R' : (($col['align'] ?? 'left') === 'center' ? 'C' : 'L');
        if ($first) {
            $pdf->Cell($w, 7, $totals['label'] ?? 'TOTAL', 1, 0, 'L', true);
            $first = false;
        } elseif (isset($totals['values'][$col['key']])) {
            $val = $totals['values'][$col['key']];
            $text = ($col['format'] ?? 'text') === 'currency' ? 'PHP ' . number_format((float) $val, 2) : number_format((float) $val, 2);
            $pdf->Cell($w, 7, $text, 1, 0, $align, true);
        } else {
            $pdf->Cell($w, 7, '', 1, 0, 'L', true);
        }
    }
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
