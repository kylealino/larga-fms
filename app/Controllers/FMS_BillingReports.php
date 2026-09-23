<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_BillingReports extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->reportsModel = model('App\Models\FMS_BillingReports_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index()
    {
        $meaction = $this->request->getPostGet('meaction');

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        switch ($meaction) {
            case 'MAIN':
                return view('fms/reports/billing-reports-main');
                break;

            case 'GET_CUSTOMERS':
                try { $result = $this->reportsModel->getCustomers(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_REPORT':
                try { $result = $this->buildReport(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'PRINT_REPORT':
                return $this->printReport();
                break;

            default:
                return view('fms/reports/billing-reports-main');
                break;
        }
    }

    // ==============================
    // BUILD REPORT DATA (AJAX, on-screen)
    // NOTE: report_type 'statement_of_account' is intentionally NOT handled here.
    // The Statement of Account tab, in the view/JS, links out directly to the
    // existing `statementofaccount` route (FMS_SOA) instead of being rebuilt here.
    // ==============================
    private function buildReport()
    {
        $report_type = $this->request->getPostGet('report_type');
        [$date_from, $date_to, $customer_id, $status] = $this->getFilters();

        switch ($report_type) {
            case 'billing_summary':
                $data = $this->reportsModel->getBillingSummary($date_from, $date_to, $customer_id, $status);
                $html = view('fms/reports/_report-table', ['columns' => $data['columns'], 'rows' => $data['rows']]);
                return ['status' => 'success', 'html' => $html, 'stats' => $data['stats'], 'title' => $data['title']];

            case 'invoice_report':
                $data = $this->reportsModel->getInvoiceReport($date_from, $date_to, $customer_id, $status);
                $html = view('fms/reports/_report-table', ['columns' => $data['columns'], 'rows' => $data['rows']]);
                return ['status' => 'success', 'html' => $html, 'stats' => $data['stats'], 'title' => $data['title']];

            case 'payment_report':
                $data = $this->reportsModel->getPaymentReport($date_from, $date_to, $customer_id, $status);
                $html = view('fms/reports/_report-table', ['columns' => $data['columns'], 'rows' => $data['rows']]);
                return ['status' => 'success', 'html' => $html, 'stats' => $data['stats'], 'title' => $data['title']];

            case 'accounts_receivable':
                $data = $this->reportsModel->getAccountsReceivable($customer_id);
                $html = view('fms/reports/_report-table', ['columns' => $data['columns'], 'rows' => $data['rows']]);
                return ['status' => 'success', 'html' => $html, 'stats' => $data['stats'], 'title' => $data['title']];

            case 'ar_aging':
                $data = $this->reportsModel->getARAging();
                $html = $this->renderAgingMatrixHtml($data);
                return ['status' => 'success', 'html' => $html, 'stats' => $data['stats'], 'title' => $data['title']];

            default:
                return ['status' => 'error', 'message' => 'Unknown report type.'];
        }
    }

    // ==============================
    // AR AGING — MATRIX HTML (on-screen)
    // Only used by this one report, so it is rendered inline here instead of
    // through a shared partial (the shared partial is flat-table only).
    // ==============================
    private function renderAgingMatrixHtml($data)
    {
        $bucketColumns = $data['bucketColumns'];
        $rows = $data['rows'];
        $grandTotals = $data['grandTotals'];

        $html = '<thead><tr><th>' . esc($data['rowLabel']) . '</th>';
        foreach ($bucketColumns as $bc) {
            $html .= '<th class="text-end">' . esc($bc['label']) . '</th>';
        }
        $html .= '<th class="text-end">Total</th></tr></thead><tbody>';

        if (!empty($rows)) {
            foreach ($rows as $row) {
                $html .= '<tr><td>' . esc($row[$data['rowLabelKey']] ?? '—') . '</td>';
                foreach ($bucketColumns as $bc) {
                    $val = $row[$bc['key']] ?? 0;
                    $html .= '<td class="text-end">&#8369;' . number_format((float) $val, 2) . '</td>';
                }
                $html .= '<td class="text-end"><strong>&#8369;' . number_format((float) ($row['total'] ?? 0), 2) . '</strong></td></tr>';
            }
            $html .= '<tr style="font-weight:700;background:#f1f5f9;"><td>GRAND TOTAL</td>';
            foreach ($bucketColumns as $bc) {
                $val = $grandTotals[$bc['key']] ?? 0;
                $html .= '<td class="text-end">&#8369;' . number_format((float) $val, 2) . '</td>';
            }
            $html .= '<td class="text-end">&#8369;' . number_format((float) ($grandTotals['total'] ?? 0), 2) . '</td></tr>';
        }
        $html .= '</tbody>';

        return $html;
    }

    // ==============================
    // PRINT REPORT (PDF)
    // ==============================
    private function printReport()
    {
        $report_type = $this->request->getPostGet('report_type');
        [$date_from, $date_to, $customer_id, $status] = $this->getFilters();
        $subtitle = date('M d, Y', strtotime($date_from)) . ' - ' . date('M d, Y', strtotime($date_to));

        switch ($report_type) {
            case 'billing_summary':
                $data = $this->reportsModel->getBillingSummary($date_from, $date_to, $customer_id, $status);
                return view('fms/reports/report-table-pdf', ['title' => $data['title'], 'subtitle' => $subtitle, 'columns' => $data['columns'], 'rows' => $data['rows'], 'totals' => $data['totals']]);

            case 'invoice_report':
                $data = $this->reportsModel->getInvoiceReport($date_from, $date_to, $customer_id, $status);
                return view('fms/reports/report-table-pdf', ['title' => $data['title'], 'subtitle' => $subtitle, 'columns' => $data['columns'], 'rows' => $data['rows'], 'totals' => $data['totals']]);

            case 'payment_report':
                $data = $this->reportsModel->getPaymentReport($date_from, $date_to, $customer_id, $status);
                return view('fms/reports/report-table-pdf', ['title' => $data['title'], 'subtitle' => $subtitle, 'columns' => $data['columns'], 'rows' => $data['rows'], 'totals' => $data['totals']]);

            case 'accounts_receivable':
                $data = $this->reportsModel->getAccountsReceivable($customer_id);
                return view('fms/reports/report-table-pdf', ['title' => $data['title'], 'subtitle' => 'As of ' . date('M d, Y'), 'columns' => $data['columns'], 'rows' => $data['rows'], 'totals' => $data['totals']]);

            case 'ar_aging':
                $data = $this->reportsModel->getARAging();
                return view('fms/reports/report-matrix-pdf', [
                    'title' => $data['title'],
                    'subtitle' => 'As of ' . date('M d, Y'),
                    'rowLabelKey' => $data['rowLabelKey'],
                    'rowLabel' => $data['rowLabel'],
                    'bucketColumns' => $data['bucketColumns'],
                    'rows' => $data['rows'],
                    'totals' => $data['grandTotals'],
                ]);

            default:
                echo 'Unknown report type.';
                exit;
        }
    }

    // ==============================
    // FILTER HELPERS
    // ==============================
    private function getFilters()
    {
        $date_from = $this->request->getPostGet('date_from') ?: date('Y-m-01');
        $date_to = $this->request->getPostGet('date_to') ?: date('Y-m-d');
        $customer_id = $this->request->getPostGet('customer_id') ?: null;
        $status = $this->request->getPostGet('status') ?: null;

        return [$date_from, $date_to, $customer_id, $status];
    }
}
