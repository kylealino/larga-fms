<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_OperationsReports extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->model = model('App\Models\FMS_OperationsReports_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    // ==============================
    // REPORT TYPE DISPATCH MAP
    // ==============================
    private function resolveReportData($report_type, $filters)
    {
        switch ($report_type) {
            case 'trip_report':
                return $this->model->getTripReport($filters);
            case 'dispatch_report':
                return $this->model->getDispatchReport($filters);
            case 'truck_utilization':
                return $this->model->getTruckUtilizationReport($filters);
            case 'driver_performance':
                return $this->model->getDriverPerformanceReport($filters);
            case 'helper_assignment':
                return $this->model->getHelperAssignmentReport($filters);
            case 'fuel_consumption':
                return $this->model->getFuelConsumptionReport($filters);
            case 'fuel_cost':
                return $this->model->getFuelCostReport($filters);
            default:
                return $this->model->getTripReport($filters);
        }
    }

    private function buildFilters()
    {
        return [
            'date_from' => $this->request->getPostGet('date_from') ?: date('Y-m-01'),
            'date_to' => $this->request->getPostGet('date_to') ?: date('Y-m-d'),
            'status' => $this->request->getPostGet('status') ?: null,
            'truck_id' => $this->request->getPostGet('truck_id') ?: null,
        ];
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
                return view('fms/reports/operations-reports-main');
                break;

            case 'GET_REPORT':
                try {
                    $report_type = $this->request->getPostGet('report_type');
                    $filters = $this->buildFilters();
                    $data = $this->resolveReportData($report_type, $filters);
                    $html = view('fms/reports/_report-table', ['columns' => $data['columns'], 'rows' => $data['rows']]);
                    $result = ['status' => 'success', 'html' => $html, 'stats' => $data['stats'], 'title' => $data['title']];
                } catch (\Throwable $e) {
                    $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
                }
                echo json_encode($result);
                break;

            case 'PRINT_REPORT':
                $report_type = $this->request->getPostGet('report_type');
                $filters = $this->buildFilters();
                $data = $this->resolveReportData($report_type, $filters);
                $subtitle = date('M d, Y', strtotime($filters['date_from'])) . ' - ' . date('M d, Y', strtotime($filters['date_to']));
                return view('fms/reports/report-table-pdf', [
                    'title' => $data['title'],
                    'subtitle' => $subtitle,
                    'columns' => $data['columns'],
                    'rows' => $data['rows'],
                    'totals' => $data['totals'] ?? null,
                ]);
                break;

            default:
                return view('fms/reports/operations-reports-main');
                break;
        }
    }
}
