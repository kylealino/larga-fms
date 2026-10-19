<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$invoice_id = $this->request->getPostGet('invoice_id');
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');
require APPPATH . 'ThirdParty/fpdf/fpdf.php';

$company = config('Company');
$formattedDate = date("F j, Y");

// ==============================
// LAYOUT CONSTANTS
// ==============================
$MARGIN_L  = 15;
$MARGIN_R  = 15;
$PAGE_W    = 210;
$CONTENT_W = $PAGE_W - $MARGIN_L - $MARGIN_R;
$SIG_Y     = 250;
$FOOTER_Y  = 280;

// ==============================
// FETCH INVOICE + BILLING + CUSTOMER + TRIP/DR REFERENCES
// ==============================
$data = $this->db->query("
    SELECT i.*,
           c.customer_name, c.business_address, c.billing_address, c.contact_person, c.contact_number, c.tin as customer_tin,
           b.billing_code, b.billing_basis, b.rate, b.quantity, b.service_type,
           t.trip_id, t.trip_code, t.booking_reference, t.client_reference, t.erp_bookslip_id, t.origin, t.destination,
           dr.dr_code
    FROM tbl_invoices i
    LEFT JOIN tbl_customers c ON i.customer_id = c.customer_id
    LEFT JOIN tbl_billing b ON i.billing_id = b.billing_id
    LEFT JOIN tbl_trips t ON b.trip_id = t.trip_id
    LEFT JOIN tbl_delivery_receipts dr ON b.dr_id = dr.dr_id
    WHERE i.invoice_id = ?
    LIMIT 1
", [$invoice_id])->getRowArray();

if (!$data) {
    exit('Invoice not found.');
}

$invoice_code   = $data['invoice_code'];
$invoice_date   = !empty($data['invoice_date']) ? date("F j, Y", strtotime($data['invoice_date'])) : '';
$due_date       = !empty($data['due_date']) ? date("F j, Y", strtotime($data['due_date'])) : '-';
$customer_name  = $data['customer_name'];
$bill_address   = $data['billing_address'] ?: $data['business_address'];
$contact_person = $data['contact_person'];
$contact_number = $data['contact_number'];
$billing_code   = $data['billing_code'];
$trip_code      = $data['trip_code'];
$service_type   = $data['service_type'];
$payment_terms  = $data['payment_terms'];
$invoice_status = $data['invoice_status'];

// ==============================
// LINE ITEMS
// ==============================
// Service: the ERP booking quote lines when the billed amount still equals the quote, otherwise one service line
$subtotal = (float) $data['subtotal'];
$basisLabel = ['PER_TRIP' => 'trip', 'PER_KILOMETER' => 'km', 'PER_HOUR' => 'hr', 'PER_DAY' => 'day', 'FIXED_RATE' => 'lot'];
$serviceRef = trim(($trip_code ?: '') . ($data['dr_code'] ? '  /  ' . $data['dr_code'] : ''));
$route = trim(($data['origin'] ?: '') . ($data['destination'] ? '  to  ' . $data['destination'] : ''));

$items = [];
$quote = $data['erp_bookslip_id']
    ? $this->db->query("SELECT description, quantity, selling_php FROM tbl_trip_booking_charges WHERE trip_id = ? ORDER BY charge_id", [$data['trip_id']])->getResultArray()
    : [];
$quoteTotal = array_sum(array_map(fn($q) => (float) $q['selling_php'] * (float) $q['quantity'], $quote));
if ($quote && abs($quoteTotal - $subtotal) < 0.01) {
    foreach ($quote as $q) {
        $items[] = ['desc' => ucwords(strtolower($q['description']), " /-("), 'sub' => '', 'qty' => (float) $q['quantity'], 'unit' => '', 'price' => (float) $q['selling_php'], 'amount' => (float) $q['selling_php'] * (float) $q['quantity']];
    }
    $items[0]['sub'] = trim(($service_type ?: 'Trucking Service') . '  -  ' . $serviceRef);
    $items[0]['route'] = $route;
} else {
    $items[] = [
        'desc' => ($service_type ?: 'Trucking Service') . ($serviceRef ? '  -  ' . $serviceRef : ''),
        'sub' => '', 'route' => $route,
        'qty' => (float) $data['quantity'], 'unit' => $basisLabel[$data['billing_basis']] ?? '',
        'price' => (float) $data['rate'], 'amount' => $subtotal,
    ];
}

$charges = $this->db->query("SELECT c.charge_type, c.description, c.amount, e.expense_code, e.reference_no FROM tbl_billing_charges c LEFT JOIN tbl_dispatch_expenses e ON e.expense_id = c.expense_id WHERE c.billing_id = ? ORDER BY c.charge_id", [$data['billing_id']])->getResultArray();

// ==============================
// BUILD PDF
// ==============================
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins($MARGIN_L, 12, $MARGIN_R);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetTitle('Invoice - ' . $invoice_code);

// Shorten text with "..." so it never runs past its cell (uses the current font)
$fit = function($text, $w) use (&$pdf) {
    $text = (string) $text;
    if ($pdf->GetStringWidth($text) <= $w - 2) return $text;
    while ($text !== '' && $pdf->GetStringWidth($text . '...') > $w - 2) $text = substr($text, 0, -1);
    return rtrim($text) . '...';
};
// Footer on every page
$footer = function() use (&$pdf, $MARGIN_L, $MARGIN_R, $PAGE_W, $CONTENT_W, $FOOTER_Y, $formattedDate, $invoice_code) {
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Line($MARGIN_L, $FOOTER_Y - 3, $PAGE_W - $MARGIN_R, $FOOTER_Y - 3);

    $pdf->SetFont('Helvetica', 'I', 8);
    $pdf->SetTextColor(100, 116, 139);
    $pdf->SetXY($MARGIN_L, $FOOTER_Y);
    $pdf->Cell($CONTENT_W, 4, 'This is a system-generated document from Larga Fleet Management System.', 0, 1, 'C');

    $pdf->SetFont('Helvetica', '', 7);
    $pdf->SetXY($MARGIN_L, $FOOTER_Y + 4);
    $pdf->Cell($CONTENT_W, 3, 'Generated on ' . $formattedDate . '  |  Invoice: ' . $invoice_code . '  |  Page ' . $pdf->PageNo() . ' of {nb}', 0, 1, 'C');
};
// Continue on a new page when the next block would not fit
$ensure = function($h) use (&$pdf, &$Y, $footer) {
    if ($Y + $h > 270) { $footer(); $pdf->AddPage(); $Y = 15; }
};

$Y = 12;

// ============================================
// COMPANY HEADER (name, address, TIN)
// ============================================
$pdf->SetFont('Helvetica', 'B', 18);
$pdf->SetTextColor(26, 107, 176);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 8, $company->name, 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 9);
$pdf->SetTextColor(100, 116, 139);
if ($company->address !== '') {
    $pdf->SetX($MARGIN_L);
    $pdf->Cell($CONTENT_W, 4, $company->address, 0, 1, 'C');
}
$pdf->SetX($MARGIN_L);
$pdf->Cell($CONTENT_W, 4, $company->tagline . '  |  Contact: ' . $company->phone . '  |  ' . $company->email, 0, 1, 'C');
$pdf->SetX($MARGIN_L);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell($CONTENT_W, 5, 'VAT Reg. TIN: ' . ($company->tin !== '' ? $company->tin : '______________________'), 0, 1, 'C');

$Y = $pdf->GetY() + 2;
$pdf->SetDrawColor(26, 107, 176);
$pdf->SetLineWidth(0.5);
$pdf->Line($MARGIN_L, $Y, $PAGE_W - $MARGIN_R, $Y);
$pdf->SetLineWidth(0.2);

// ============================================
// DOCUMENT TITLE
// ============================================
$Y += 4;
$pdf->SetFont('Helvetica', 'B', 15);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 8, 'SALES INVOICE', 0, 1, 'C');

// ============================================
// INVOICE NO / DATE / DUE DATE
// ============================================
$Y = $pdf->GetY() + 3;
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
$pdf->Cell(62, 5, $payment_terms ?: '-', 0, 1, 'R');

$Y = $pdf->GetY() + 3;
$pdf->Line($MARGIN_L, $Y, $PAGE_W - $MARGIN_R, $Y);
$Y += 3;

// ============================================
// BILL TO (name, address, TIN, contact)
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

$pdf->SetX($MARGIN_L + 2);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(30, 5, 'Address:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell($CONTENT_W - 32, 5, $bill_address ?: '-', 0, 'L');

$Y = $pdf->GetY();
$pdf->SetXY($MARGIN_L + 2, $Y);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(30, 5, 'TIN:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(58, 5, $data['customer_tin'] ?: '-', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(20, 5, 'Contact:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($CONTENT_W - 110, 5, $fit(($contact_person ?: '-') . '  |  ' . ($contact_number ?: '-'), $CONTENT_W - 110), 0, 1, 'L');

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

$refs = [
    ['Billing No:', $billing_code, 'Trip No:', $trip_code],
    ['DR No:', $data['dr_code'], 'Status:', null],
];
if ($data['booking_reference'] || $data['client_reference']) {
    $refs[] = ['Booking Ref:', $data['booking_reference'], 'Client Ref:', $data['client_reference']];
}

$pdf->SetTextColor(30, 41, 59);
$Y += 1;
foreach ($refs as [$l1, $v1, $l2, $v2]) {
    $pdf->SetXY($col1X, $Y);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell($labelW, 5, $l1, 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW, 5, $fit($v1 ?: '-', $valueW), 0, 0, 'L');

    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetXY($col2X, $Y);
    $pdf->Cell($labelW, 5, $l2, 0, 0, 'L');
    if ($l2 === 'Status:') {
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(26, 107, 176);
        $pdf->Cell($valueW, 5, str_replace('_', ' ', $invoice_status), 0, 1, 'L');
        $pdf->SetTextColor(30, 41, 59);
    } else {
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell($valueW, 5, $fit($v2 ?: '-', $valueW), 0, 1, 'L');
    }
    $Y = $pdf->GetY();
}

$Y += 3;

// ============================================
// LINE ITEMS TABLE
// ============================================
$wDesc = 98; $wQty = 18; $wPrice = 32; $wAmt = $CONTENT_W - $wDesc - $wQty - $wPrice;

$tableHeader = function() use (&$pdf, &$Y, $MARGIN_L, $wDesc, $wQty, $wPrice, $wAmt) {
    $pdf->SetFillColor(26, 107, 176);
    $pdf->SetDrawColor(26, 107, 176);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetXY($MARGIN_L, $Y);
    $pdf->Cell($wDesc, 7, 'Description', 1, 0, 'L', true);
    $pdf->Cell($wQty, 7, 'Qty', 1, 0, 'C', true);
    $pdf->Cell($wPrice, 7, 'Unit Price', 1, 0, 'R', true);
    $pdf->Cell($wAmt, 7, 'Amount', 1, 1, 'R', true);
    $Y = $pdf->GetY();
    $pdf->SetTextColor(30, 41, 59);
};
$tableHeader();

$itemRow = function($desc, $sub, $qty, $price, $amount) use (&$pdf, &$Y, $MARGIN_L, $wDesc, $wQty, $wPrice, $wAmt, $fit, $ensure, $tableHeader) {
    $h = $sub !== '' ? 11 : 7;
    $before = $Y; $ensure($h); if ($Y !== $before) $tableHeader();
    $pdf->SetDrawColor(203, 213, 225);
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->SetXY($MARGIN_L, $Y);
    $pdf->Cell($wDesc, $h, '', 1, 0, 'L');
    $pdf->Cell($wQty, $h, $qty, 1, 0, 'C');
    $pdf->Cell($wPrice, $h, $price, 1, 0, 'R');
    $pdf->Cell($wAmt, $h, $amount, 1, 1, 'R');
    // description (+ grey second line) drawn inside the bordered cell
    $pdf->SetXY($MARGIN_L + 1, $Y + 1);
    $pdf->Cell($wDesc - 2, 5, $fit($desc, $wDesc - 1), 0, 2, 'L');
    if ($sub !== '') {
        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell($wDesc - 2, 4, $fit($sub, $wDesc - 1), 0, 0, 'L');
        $pdf->SetTextColor(30, 41, 59);
    }
    $Y += $h;
};
$money = fn($n) => number_format((float) $n, 2);
$qtyFmt = fn($q, $u = '') => rtrim(rtrim(number_format((float) $q, 2), '0'), '.') . ($u ? ' ' . $u : '');

foreach ($items as $it) {
    $sub = trim(($it['sub'] ?? '') . (($it['sub'] ?? '') !== '' && ($it['route'] ?? '') !== '' ? '  |  ' : '') . ($it['route'] ?? ''));
    $itemRow($it['desc'], $sub, $qtyFmt($it['qty'], $it['unit']), $money($it['price']), $money($it['amount']));
}
foreach ($charges as $c) {
    $label = ucwords(strtolower(str_replace('_', ' ', $c['charge_type'])));
    $desc = $c['description'] && strcasecmp($c['description'], $label) !== 0 ? $label . ' - ' . $c['description'] : $label;
    $itemRow($desc, $c['expense_code'] ? 'Reimbursable cost (' . $c['expense_code'] . ($c['reference_no'] ? ', ref ' . $c['reference_no'] : '') . ')' : 'Additional charge', '1', $money($c['amount']), $money($c['amount']));
}

// ============================================
// TOTALS (right-aligned under the Amount column)
// ============================================
$Y += 2;
$taxable = (float) $data['taxable_amount'];
$vatPct = rtrim(rtrim(number_format($company->vatRate * 100, 2), '0'), '.');
$rows = [
    ['Total Sales (VAT exclusive)', $data['subtotal'], false],
];
if ((float) $data['discount'] > 0) $rows[] = ['Less: Discount', -1 * (float) $data['discount'], false];
$rows[] = ['VATable Sales', $taxable, false];
$rows[] = ["VAT ({$vatPct}%)", $data['vat'], false];
if ((float) $data['other_charges'] > 0) $rows[] = ['Additional Charges', $data['other_charges'], false];
$rows[] = ['TOTAL AMOUNT DUE', $data['total_amount'], true];
$rows[] = ['Amount Paid', $data['amount_paid'], false];
$rows[] = ['OUTSTANDING BALANCE', $data['outstanding_balance'], true];

$labelColW = 60;
$amtColW = 40;
$totX = $PAGE_W - $MARGIN_R - $labelColW - $amtColW;
$ensure(count($rows) * 6.5);
$pdf->SetDrawColor(203, 213, 225);
$pdf->SetTextColor(30, 41, 59);
foreach ($rows as [$label, $amount, $emphasize]) {
    $pdf->SetXY($totX, $Y);
    $pdf->SetFont('Helvetica', $emphasize ? 'B' : '', 9.5);
    if ($emphasize) { $pdf->SetFillColor(232, 242, 250); } else { $pdf->SetFillColor(255, 255, 255); }
    $pdf->Cell($labelColW, 6.5, $label, 1, 0, 'L', true);
    $pdf->Cell($amtColW, 6.5, 'PHP ' . ((float) $amount < 0 ? '(' . $money(abs($amount)) . ')' : $money($amount)), 1, 1, 'R', true);
    $Y = $pdf->GetY();
}

// ============================================
// SIGNATURE BLOCK (pushed to the next page only if the items reach it)
// ============================================
$boxW = 100;
if ($Y > $SIG_Y - 8) { $footer(); $pdf->AddPage(); }
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
$footer();

$pdf->Output();
exit;
?>
