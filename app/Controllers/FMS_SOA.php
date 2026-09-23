<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_SOA extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->soaModel = model('App\Models\FMS_SOA_Model');
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
                return view('fms/soa/soa-main');
                break;

            case 'GET_CUSTOMERS':
                try { $result = $this->soaModel->getCustomers(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_SOA':
                try { $result = $this->soaModel->getStatementOfAccount(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_ADJUSTMENTS':
                $customer_id = $this->request->getPost('customer_id');
                try { $result = $this->soaModel->getAdjustments($customer_id); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'SAVE_ADJUSTMENT':
                try { $result = $this->soaModel->saveAdjustment(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_ADJUSTMENT':
                try { $result = $this->soaModel->deleteAdjustment(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'PRINT-SOA':
                return view('fms/soa/soa-pdf');
                break;

            default:
                return view('fms/soa/soa-main');
                break;
        }
    }
}
