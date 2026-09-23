<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$invoice_id = $this->request->getPostGet('invoice_id');
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');
require APPPATH . 'ThirdParty/fpdf/fpdf.php';

$formattedDate = date("F j, Y");

// ==============================
// LAYOUT CONSTANTS
// ==============================
$MARGIN_L  = 15;
$MARGIN_R  = 15;
$PAGE_W    = 210;
$CONTENT_W = $PAGE_W - $MARGIN_L - $MARGIN_R;
$SIG_Y     = 225;
$FOOTER_Y  = 280;

// ==============================
// FETCH INVOICE + BILLING + CUSTOMER
// ==============================
$data = $this->db->query("
    SELECT i.*,
           c.customer_name, c.business_address, c.contact_person, c.contact_number,
           b.billing_code, b.billing_basis, b.rate, b.quantity, b.service_type,
           t.trip_code
    FROM tbl_invoices i
    LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
    LEFT JOIN tbl_billing b ON i.billing_id = b.billing_id
    LEFT JOIN tbl_trips t ON b.trip_id = t.trip_id
    WHERE i.invoice_id = ?
    LIMIT 1
", [$invoice_id])->getRowArray();

if (!$data) {
    exit('Invoice not found.');
}

$invoice_code   = $data['invoice_code'];
$invoice_date   = !empty($data['invoice_date']) ? date("F j, Y", strtotime($data['invoice_date'])) : '';
$due_date       = !empty($data['due_date']) ? date("F j, Y", strtotime($data['due_date'])) : '—';
$customer_name  = $data['customer_name'];
$business_address = $data['business_address'];
$contact_person = $data['contact_person'];
$contact_number = $data['contact_number'];
$billing_code   = $data['billing_code'];
$trip_code      = $data['trip_code'];
$service_type   = $data['service_type'];
$payment_terms  = $data['payment_terms'];
$invoice_status = $data['invoice_status'];

// ==============================
// BUILD PDF
// ==============================
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins($MARGIN_L, 12, $MARGIN_R);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetTitle('Invoice - ' . $invoice_code);

$Y = 15;

// ============================================
// COMPANY HEADER
// ============================================
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

// ============================================
// DOCUMENT TITLE
// ============================================
$Y = $pdf->GetY() + 6;
$pdf->SetFont('Helvetica', 'B', 15);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 8, 'SALES INVOICE', 0, 1, 'C');

// ============================================
// INVOICE NO / DATE / DUE DATE
// ============================================
$Y = $pdf->GetY() + 4;
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(30, 41, 59);

$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell(28, 5, 'Invoice No:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(62, 5, $invoice_code, 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetXY($MARGIN_L + 90, $Y);
$pdf->Cell(28, 5, 'Invoice Date:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(62, 5, $invoice_date, 0, 1, 'R');

$Y = $pdf->GetY() + 1;
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell(28, 5, 'Due Date:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(62, 5, $due_date, 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetXY($MARGIN_L + 90, $Y);
$pdf->Cell(28, 5, 'Terms:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(62, 5, $payment_terms ?: '—', 0, 1, 'R');

$Y = $pdf->GetY() + 3;
$pdf->Line($MARGIN_L, $Y, $PAGE_W - $MARGIN_R, $Y);
$Y += 4;

// ============================================
// BILL TO
// ============================================
$pdf->SetFillColor(232, 242, 250);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(15, 90, 153);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 7, '  BILL TO', 0, 1, 'L', true);

$Y = $pdf->GetY();
$pdf->SetTextColor(30, 41, 59);

$pdf->SetXY($MARGIN_L + 2, $Y + 1);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(30, 5, 'Customer:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(0, 5, $customer_name, 0, 1, 'L');

$Y = $pdf->GetY();
$pdf->SetXY($MARGIN_L + 2, $Y);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(30, 5, 'Address:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell($CONTENT_W - 32, 5, $business_address ?: '—', 0, 'L');

$Y = $pdf->GetY();
$pdf->SetXY($MARGIN_L + 2, $Y);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(30, 5, 'Contact:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($CONTENT_W - 32, 5, ($contact_person ?: '—') . '  |  ' . ($contact_number ?: '—'), 0, 1, 'L');

$Y = $pdf->GetY() + 3;

// ============================================
// REFERENCE
// ============================================
$pdf->SetFillColor(232, 242, 250);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(15, 90, 153);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 7, '  REFERENCE', 0, 1, 'L', true);

$Y = $pdf->GetY();
$col1X = $MARGIN_L + 2;
$col2X = $MARGIN_L + 90;
$labelW = 28;
$valueW = 58;

$pdf->SetTextColor(30, 41, 59);

$pdf->SetXY($col1X, $Y + 1);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell($labelW, 5, 'Billing No:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($valueW, 5, $billing_code ?: '—', 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetXY($col2X, $Y + 1);
$pdf->Cell($labelW, 5, 'Trip No:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($valueW, 5, $trip_code ?: '—', 0, 1, 'L');

$Y = $pdf->GetY();
$pdf->SetXY($col1X, $Y);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell($labelW, 5, 'Service Type:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($valueW, 5, $service_type ?: '—', 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetXY($col2X, $Y);
$pdf->Cell($labelW, 5, 'Status:', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(26, 107, 176);
$pdf->Cell($valueW, 5, str_replace('_', ' ', $invoice_status), 0, 1, 'L');
$pdf->SetTextColor(30, 41, 59);

$Y = $pdf->GetY() + 3;

// ============================================
// AMOUNT SUMMARY TABLE
// ============================================
$pdf->SetFillColor(232, 242, 250);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(15, 90, 153);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 7, '  AMOUNT SUMMARY', 0, 1, 'L', true);

$Y = $pdf->GetY();
$labelColW = 130;
$amtColW = $CONTENT_W - $labelColW;

$rows = [
    ['Subtotal', $data['subtotal'], false],
    ['Discount', $data['discount'], false],
    ['Taxable Amount', $data['taxable_amount'], false],
    ['VAT', $data['vat'], false],
    ['Other Charges', $data['other_charges'], false],
    ['TOTAL AMOUNT', $data['total_amount'], true],
    ['Amount Paid', $data['amount_paid'], false],
    ['OUTSTANDING BALANCE', $data['outstanding_balance'], true],
];

$pdf->SetTextColor(30, 41, 59);
foreach ($rows as $row) {
    list($label, $amount, $emphasize) = $row;
    $pdf->SetXY($MARGIN_L, $Y);
    $pdf->SetFont('Helvetica', $emphasize ? 'B' : '', 10);
    if ($emphasize) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
    $pdf->Cell($labelColW, 7, $label, 1, 0, 'L', true);
    $pdf->Cell($amtColW, 7, 'PHP ' . number_format((float) $amount, 2), 1, 1, 'R', true);
    $Y = $pdf->GetY();
}

// ============================================
// SIGNATURE BLOCK
// ============================================
$boxW = 100;
$lineY = $SIG_Y;

$pdf->SetDrawColor(120, 120, 120);
$pdf->SetLineWidth(0.3);

$pdf->Line($MARGIN_L, $lineY, $MARGIN_L + $boxW, $lineY);

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetXY($MARGIN_L, $lineY + 1);
$pdf->Cell($boxW, 4, 'Prepared By / Authorized Signature', 0, 0, 'C');

$pdf->SetFont('Helvetica', '', 8);
$pdf->SetTextColor(100, 116, 139);
$pdf->SetXY($MARGIN_L, $lineY + 5);
$pdf->Cell($boxW, 3, 'Name / Signature / Date', 0, 0, 'C');

// ============================================
// FOOTER
// ============================================
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line($MARGIN_L, $FOOTER_Y - 3, $PAGE_W - $MARGIN_R, $FOOTER_Y - 3);

$pdf->SetFont('Helvetica', 'I', 8);
$pdf->SetTextColor(100, 116, 139);
$pdf->SetXY($MARGIN_L, $FOOTER_Y);
$pdf->Cell($CONTENT_W, 4, 'This is a system-generated document from Larga Fleet Management System.', 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 7);
$pdf->SetXY($MARGIN_L, $FOOTER_Y + 4);
$pdf->Cell($CONTENT_W, 3, 'Generated on ' . $formattedDate . '  |  Invoice: ' . $invoice_code, 0, 1, 'C');

$pdf->Output();
exit;
?>
