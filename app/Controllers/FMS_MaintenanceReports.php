<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_MaintenanceReports extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->model = model('App\Models\FMS_MaintenanceReports_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    // ==============================
    // REPORT TYPE -> MODEL METHOD MAP
    // ==============================
    private $reportMethods = [
        'maintenance_history'     => 'getMaintenanceHistory',
        'maintenance_cost'        => 'getMaintenanceCost',
        'upcoming_maintenance'    => 'getUpcomingMaintenance',
        'parts_replacement'       => 'getPartsReplacement',
        'registration_expiration' => 'getRegistrationExpiration',
        'insurance_expiration'    => 'getInsuranceExpiration',
        'tools_inventory'         => 'getToolsInventory',
        'tool_issuance'           => 'getToolIssuance',
        'supplies_inventory'      => 'getSuppliesInventory',
        'stock_movement'          => 'getStockMovement',
        'low_stock'               => 'getLowStock',
    ];

    private $snapshotReports = [
        'tools_inventory',
        'supplies_inventory',
        'low_stock',
        'registration_expiration',
        'insurance_expiration',
    ];

    private function getFilters()
    {
        $report_type = $this->request->getPostGet('report_type') ?: 'maintenance_history';
        $date_from = $this->request->getPostGet('date_from') ?: date('Y-m-01');
        $date_to = $this->request->getPostGet('date_to') ?: date('Y-m-d');
        $status = $this->request->getPostGet('status') ?: '';
        $truck_id = $this->request->getPostGet('truck_id') ?: '';

        return [
            'report_type' => $report_type,
            'date_from'   => $date_from,
            'date_to'     => $date_to,
            'status'      => $status,
            'truck_id'    => $truck_id,
        ];
    }

    private function runReport($filters)
    {
        $report_type = $filters['report_type'];

        if (!isset($this->reportMethods[$report_type])) {
            return null;
        }

        $methodName = $this->reportMethods[$report_type];
        return $this->model->{$methodName}($filters);
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
                return view('fms/reports/maintenance-reports-main');
                break;

            case 'GET_REPORT':
                try {
                    $filters = $this->getFilters();
                    $data = $this->runReport($filters);

                    if (!$data) {
                        echo json_encode(['status' => 'error', 'message' => 'Unknown report type.']);
                        break;
                    }

                    $html = view('fms/reports/_report-table', ['columns' => $data['columns'], 'rows' => $data['rows']]);
                    echo json_encode([
                        'status' => 'success',
                        'html'   => $html,
                        'stats'  => $data['stats'],
                        'title'  => $data['title'],
                    ]);
                } catch (\Throwable $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                break;

            case 'PRINT_REPORT':
                $filters = $this->getFilters();
                $data = $this->runReport($filters);

                if (!$data) {
                    echo 'Unknown report type.';
                    break;
                }

                $subtitle = in_array($filters['report_type'], $this->snapshotReports)
                    ? 'As of ' . date('M d, Y')
                    : date('M d, Y', strtotime($filters['date_from'])) . ' - ' . date('M d, Y', strtotime($filters['date_to']));

                return view('fms/reports/report-table-pdf', [
                    'title'    => $data['title'],
                    'subtitle' => $subtitle,
                    'columns'  => $data['columns'],
                    'rows'     => $data['rows'],
                    'totals'   => $data['totals'] ?? null,
                ]);
                break;

            default:
                return view('fms/reports/maintenance-reports-main');
                break;
        }
    }
}
