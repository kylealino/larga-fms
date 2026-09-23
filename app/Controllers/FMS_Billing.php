<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Billing extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->billingModel = model('App\Models\FMS_Billing_Model');
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
                return view('fms/billing/billing-main');
                break;

            // ==============================
            // BILLING CRUD
            // ==============================
            case 'GET_BILLABLE_DRS':
                try { $result = $this->billingModel->getBillableDRs(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'CREATE_BILLING_FROM_DR':
                try { $result = $this->billingModel->createBillingFromDR(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_ALL_BILLINGS':
                try { $result = $this->billingModel->getAllBillings(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_BILLING':
                $billing_id = $this->request->getPost('billing_id');
                try { $result = $this->billingModel->getBilling($billing_id); }
                catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'UPDATE_BILLING':
                try { $result = $this->billingModel->updateBilling(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_BILLING':
                try { $result = $this->billingModel->deleteBilling(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            // ==============================
            // ADDITIONAL CHARGES
            // ==============================
            case 'GET_CHARGES':
                $billing_id = $this->request->getPost('billing_id');
                if ($billing_id) {
                    try { $result = $this->billingModel->getCharges($billing_id); }
                    catch (\Throwable $e) { $result = []; }
                    echo json_encode($result);
                } else {
                    echo json_encode([]);
                }
                break;

            case 'GET_CHARGE':
                $charge_id = $this->request->getPost('charge_id');
                try { $result = $this->billingModel->getCharge($charge_id); }
                catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'SAVE_CHARGE':
                try { $result = $this->billingModel->saveCharge(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_CHARGE':
                try { $result = $this->billingModel->updateCharge(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_CHARGE':
                try { $result = $this->billingModel->deleteCharge(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            default:
                return view('fms/billing/billing-main');
                break;
        }
    }
}
