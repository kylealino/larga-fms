<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$dr_id = $this->request->getPostGet('dr_id');
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');
require APPPATH . 'ThirdParty/fpdf/fpdf.php';

$currentDate   = date("Y-m-d");
$formattedDate = date("F j, Y", strtotime($currentDate));

// ==============================
// LAYOUT CONSTANTS
// ==============================
$MARGIN_L  = 15;
$MARGIN_R  = 15;
$PAGE_W    = 210;
$PAGE_H    = 297;
$CONTENT_W = $PAGE_W - $MARGIN_L - $MARGIN_R;

// Y positions for fixed blocks
$SIG_Y    = 225;   // signature line Y
$FOOTER_Y = 280;   // footer Y

// ==============================
// FETCH DR HEADER
// ==============================
$query = $this->db->query("
    SELECT 
        d.dr_code, d.dr_date, d.dr_time, d.truck, d.driver, d.helper,
        d.origin, d.destination,
        d.container_required, d.container_number, d.container_type, d.container_reference,
        d.dr_status, d.remarks,
        t.trip_code,
        c.customer_name, c.business_address, c.contact_person, c.contact_number,
        dp.dispatch_code
    FROM tbl_delivery_receipts d
    LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
    LEFT JOIN tbl_customers c ON d.customer_id = c.customer_id
    LEFT JOIN tbl_dispatch dp ON d.dispatch_id = dp.dispatch_id
    WHERE d.dr_id = '$dr_id'
");
$data = $query->getRowArray();

if (!$data) {
    exit('Delivery Receipt not found.');
}

$dr_code             = $data['dr_code'];
$dr_date             = !empty($data['dr_date']) ? date("F j, Y", strtotime($data['dr_date'])) : '';
$dr_time             = !empty($data['dr_time']) ? date("h:i A", strtotime($data['dr_time'])) : '';
$trip_code           = $data['trip_code'];
$customer_name       = $data['customer_name'];
$business_address    = $data['business_address'];
$contact_person      = $data['contact_person'];
$contact_number      = $data['contact_number'];
$truck               = $data['truck'];
$driver              = $data['driver'];
$helper              = $data['helper'];
$origin              = $data['origin'];
$destination         = $data['destination'];
$container_required  = $data['container_required'];
$container_number    = $data['container_number'];
$container_type      = $data['container_type'];
$container_reference = $data['container_reference'];
$dr_status           = ucwords(str_replace('_', ' ', $data['dr_status']));
$remarks             = $data['remarks'];

// ==============================
// FETCH ITEMS
// ==============================
$items = $this->db->query("
    SELECT * FROM tbl_delivery_receipt_items
    WHERE dr_id = '$dr_id'
    ORDER BY item_id ASC
")->getResultArray();

// ==============================
// FETCH POD
// ==============================
$pod = $this->db->query("
    SELECT * FROM tbl_delivery_receipt_pod
    WHERE dr_id = '$dr_id'
")->getRowArray();

// ==============================
// BUILD PDF
// ==============================
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins($MARGIN_L, 12, $MARGIN_R);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetTitle('Delivery Receipt - ' . $dr_code);

$Y = 15;

// ============================================
// COMPANY HEADER
// ============================================
$pdf->SetFont('Helvetica', 'B', 18);
$pdf->SetTextColor(26, 107, 176);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 8, 'LARGA FMS', 0, 1, 'C');

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
$pdf->Cell($CONTENT_W, 8, 'DELIVERY RECEIPT', 0, 1, 'C');

// ============================================
// DR NO / DATE LINE
// ============================================
$Y = $pdf->GetY() + 4;
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(30, 41, 59);

$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell(25, 5, 'DR No:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor(26, 107, 176);
$pdf->Cell(65, 5, $dr_code, 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetXY($MARGIN_L + 90, $Y);
$pdf->Cell(25, 5, 'Date:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(65, 5, $dr_date, 0, 1, 'R');

$Y = $pdf->GetY() + 1;
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell(25, 5, 'Trip No:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(65, 5, $trip_code, 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetXY($MARGIN_L + 90, $Y);
$pdf->Cell(25, 5, 'Time:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(65, 5, $dr_time, 0, 1, 'R');

$Y = $pdf->GetY() + 3;
$pdf->Line($MARGIN_L, $Y, $PAGE_W - $MARGIN_R, $Y);
$Y += 4;

// ============================================
// CUSTOMER INFORMATION
// ============================================
$pdf->SetFillColor(232, 242, 250);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(15, 90, 153);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 7, '  CUSTOMER INFORMATION', 0, 1, 'L', true);

$Y = $pdf->GetY();
$pdf->SetFont('Helvetica', '', 10);
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
$pdf->MultiCell($CONTENT_W - 32, 5, $business_address ?: $destination, 0, 'L');

$Y = $pdf->GetY();
$pdf->SetXY($MARGIN_L + 2, $Y);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(30, 5, 'Contact:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($CONTENT_W - 32, 5, ($contact_person ?: '—') . '  |  ' . ($contact_number ?: '—'), 0, 1, 'L');

$Y = $pdf->GetY() + 3;

// ============================================
// DELIVERY INFORMATION
// ============================================
$pdf->SetFillColor(232, 242, 250);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(15, 90, 153);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 7, '  DELIVERY INFORMATION', 0, 1, 'L', true);

$Y = $pdf->GetY();
$col1X  = $MARGIN_L + 2;
$col2X  = $MARGIN_L + 90;
$labelW = 25;
$valueW = 60;

$pdf->SetTextColor(30, 41, 59);

$pdf->SetXY($col1X, $Y + 1);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell($labelW, 5, 'Truck:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($valueW, 5, $truck, 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetXY($col2X, $Y + 1);
$pdf->Cell($labelW, 5, 'Origin:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($valueW, 5, $origin, 0, 1, 'L');

$Y = $pdf->GetY();
$pdf->SetXY($col1X, $Y);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell($labelW, 5, 'Driver:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($valueW, 5, $driver, 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetXY($col2X, $Y);
$pdf->Cell($labelW, 5, 'Destination:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($valueW, 5, $destination, 0, 1, 'L');

$Y = $pdf->GetY();
$pdf->SetXY($col1X, $Y);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell($labelW, 5, 'Helper:', 0, 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell($valueW, 5, $helper, 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetXY($col2X, $Y);
$pdf->Cell($labelW, 5, 'Status:', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(26, 107, 176);
$pdf->Cell($valueW, 5, strtoupper($dr_status), 0, 1, 'L');
$pdf->SetTextColor(30, 41, 59);

$Y = $pdf->GetY() + 3;

// ============================================
// CONTAINER INFORMATION (if applicable)
// ============================================
if (!empty($container_required)) {
    $pdf->SetFillColor(232, 242, 250);
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetTextColor(15, 90, 153);
    $pdf->SetXY($MARGIN_L, $Y);
    $pdf->Cell($CONTENT_W, 7, '  CONTAINER INFORMATION', 0, 1, 'L', true);

    $Y = $pdf->GetY();
    $pdf->SetTextColor(30, 41, 59);

    $pdf->SetXY($col1X, $Y + 1);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell($labelW, 5, 'Container No:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW, 5, $container_number, 0, 0, 'L');

    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetXY($col2X, $Y + 1);
    $pdf->Cell($labelW, 5, 'Type:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW, 5, $container_type, 0, 1, 'L');

    $Y = $pdf->GetY();
    $pdf->SetXY($col1X, $Y);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell($labelW, 5, 'Reference:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW + 55, 5, $container_reference, 0, 1, 'L');

    $Y = $pdf->GetY() + 3;
}

// ============================================
// DELIVERY ITEMS TABLE
// ============================================
$pdf->SetFillColor(232, 242, 250);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(15, 90, 153);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell($CONTENT_W, 7, '  DELIVERY ITEMS', 0, 1, 'L', true);

$Y = $pdf->GetY();

// ---- Table header ----
$pdf->SetFillColor(26, 107, 176);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetXY($MARGIN_L, $Y);

$pdf->Cell(10, 7, '#', 1, 0, 'C', true);
$pdf->Cell(58, 7, 'Item Description', 1, 0, 'C', true);
$pdf->Cell(18, 7, 'Dispatch', 1, 0, 'C', true);
$pdf->Cell(18, 7, 'Delivered', 1, 0, 'C', true);
$pdf->Cell(16, 7, 'Short', 1, 0, 'C', true);
$pdf->Cell(16, 7, 'Damage', 1, 0, 'C', true);
$pdf->Cell(14, 7, 'Unit', 1, 0, 'C', true);
$pdf->Cell(16, 7, 'Weight', 1, 0, 'C', true);
$pdf->Cell(14, 7, 'Condition', 1, 1, 'C', true);

$Y = $pdf->GetY();

// ---- Table rows ----
$pdf->SetTextColor(30, 41, 59);
$pdf->SetFont('Helvetica', '', 9);

$total_dispatch  = 0;
$total_delivered = 0;
$i = 1;

if (count($items) > 0) {
    foreach ($items as $item) {
        $total_dispatch  += floatval($item['quantity_dispatched']);
        $total_delivered += floatval($item['quantity_delivered']);

        $pdf->SetXY($MARGIN_L, $Y);
        $pdf->Cell(10, 6, $i, 1, 0, 'C');
        $pdf->Cell(58, 6, substr($item['item_description'], 0, 42), 1, 0, 'L');
        $pdf->Cell(18, 6, number_format($item['quantity_dispatched'], 2), 1, 0, 'R');
        $pdf->Cell(18, 6, number_format($item['quantity_delivered'], 2), 1, 0, 'R');
        $pdf->Cell(16, 6, number_format($item['quantity_shortage'], 2), 1, 0, 'R');
        $pdf->Cell(16, 6, number_format($item['quantity_damaged'], 2), 1, 0, 'R');
        $pdf->Cell(14, 6, $item['unit'], 1, 0, 'C');
        $pdf->Cell(16, 6, number_format($item['weight'], 2), 1, 0, 'R');
        $pdf->Cell(14, 6, ucfirst(strtolower($item['condition_on_arrival'])), 1, 1, 'C');

        $Y = $pdf->GetY();
        $i++;
    }
} else {
    $pdf->SetXY($MARGIN_L, $Y);
    $pdf->Cell($CONTENT_W, 6, 'No items recorded.', 1, 1, 'C');
    $Y = $pdf->GetY();
}

// ---- Totals row ----
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetFillColor(232, 242, 250);
$pdf->SetXY($MARGIN_L, $Y);
$pdf->Cell(68, 7, 'TOTAL', 1, 0, 'R', true);
$pdf->Cell(18, 7, number_format($total_dispatch, 2), 1, 0, 'R', true);
$pdf->Cell(18, 7, number_format($total_delivered, 2), 1, 0, 'R', true);
$pdf->Cell(16, 7, '', 1, 0, 'C', true);
$pdf->Cell(16, 7, '', 1, 0, 'C', true);
$pdf->Cell(14, 7, '', 1, 0, 'C', true);
$pdf->Cell(16, 7, '', 1, 0, 'C', true);
$pdf->Cell(14, 7, '', 1, 1, 'C', true);

$Y = $pdf->GetY() + 4;

// ============================================
// PROOF OF DELIVERY
// ============================================
if ($pod && (!empty($pod['received_by']) || !empty($pod['date_received']))) {
    $pod_date = !empty($pod['date_received']) ? date("F j, Y", strtotime($pod['date_received'])) : '—';
    $pod_time = !empty($pod['time_received']) ? date("h:i A", strtotime($pod['time_received'])) : '—';
    $pod_cond = ucfirst(strtolower($pod['delivery_condition']));

    $pdf->SetFillColor(232, 242, 250);
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetTextColor(15, 90, 153);
    $pdf->SetXY($MARGIN_L, $Y);
    $pdf->Cell($CONTENT_W, 7, '  PROOF OF DELIVERY', 0, 1, 'L', true);

    $Y = $pdf->GetY();
    $pdf->SetTextColor(30, 41, 59);

    $pdf->SetXY($col1X, $Y + 1);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell($labelW, 5, 'Received By:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW, 5, $pod['received_by'], 0, 0, 'L');

    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetXY($col2X, $Y + 1);
    $pdf->Cell($labelW, 5, 'Position:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW, 5, $pod['received_by_position'], 0, 1, 'L');

    $Y = $pdf->GetY();
    $pdf->SetXY($col1X, $Y);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell($labelW, 5, 'Date:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW, 5, $pod_date, 0, 0, 'L');

    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetXY($col2X, $Y);
    $pdf->Cell($labelW, 5, 'Time:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW, 5, $pod_time, 0, 1, 'L');

    $Y = $pdf->GetY();
    $pdf->SetXY($col1X, $Y);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell($labelW, 5, 'Qty Received:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell($valueW, 5, $pod['quantity_received'], 0, 0, 'L');

    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetXY($col2X, $Y);
    $pdf->Cell($labelW, 5, 'Condition:', 0, 0, 'L');
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetTextColor(26, 107, 176);
    $pdf->Cell($valueW, 5, strtoupper($pod_cond), 0, 1, 'L');
    $pdf->SetTextColor(30, 41, 59);

    if (!empty($pod['remarks'])) {
        $Y = $pdf->GetY();
        $pdf->SetXY($col1X, $Y);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell($labelW, 5, 'Remarks:', 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->MultiCell($CONTENT_W - 32, 5, $pod['remarks'], 0, 'L');
        $Y = $pdf->GetY();
    }

    $Y = $pdf->GetY() + 3;
}

// ============================================
// SIGNATURE BLOCK (LEFT-ALIGNED, BIGGER)
// ============================================
$boxW = 100;
$lineY = $SIG_Y;

// Customer signature image (aspect-ratio aware)
if ($pod && !empty($pod['customer_signature'])) {
    $sigPath = FCPATH . $pod['customer_signature'];
    if (file_exists($sigPath)) {
        $ext = strtolower(pathinfo($sigPath, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            // Read natural dimensions of the signature image
            $imgInfo = @getimagesize($sigPath);
            $imgW = $imgInfo ? $imgInfo[0] : 0;
            $imgH = $imgInfo ? $imgInfo[1] : 0;

            // Max allowed box on the PDF (mm)
            $maxW = 70;   // max width
            $maxH = 25;   // max height (prevents tall signatures)

            $drawW = $maxW;
            $drawH = 0;   // 0 = auto compute from ratio

            if ($imgW > 0 && $imgH > 0) {
                // Compute aspect ratio
                $ratio = $imgH / $imgW;   // height per unit width

                // Start with max width; check if height exceeds max
                $drawW = $maxW;
                $calcH = $drawW * $ratio;

                if ($calcH > $maxH) {
                    // Height exceeds limit — scale down width to fit maxH
                    $drawH = $maxH;
                    $drawW = $drawH / $ratio;
                }
            }

            // Center the image within the 70mm box
            $offsetX = (70 - $drawW) / 2;

            $pdf->Image(
                $sigPath,
                $MARGIN_L + 5 + $offsetX,
                $SIG_Y - 28 + (25 - ($drawH ?: $drawW * ($imgH / max($imgW, 1)))) / 2,
                $drawW,
                $drawH
            );
        }
    }
}

$pdf->SetDrawColor(120, 120, 120);
$pdf->SetLineWidth(0.3);

// Signature line (left-aligned)
$pdf->Line($MARGIN_L, $lineY, $MARGIN_L + $boxW, $lineY);

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetXY($MARGIN_L, $lineY + 1);
$pdf->Cell($boxW, 4, 'Received By / Authorized Signature', 0, 0, 'C');

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
$pdf->Cell($CONTENT_W, 3, 'Generated on ' . $formattedDate . '  |  ' . $dr_code, 0, 1, 'C');

$pdf->Output();
exit;
?>