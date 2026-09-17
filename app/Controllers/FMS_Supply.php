<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Supply extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->supplyModel = model('App\Models\FMS_Supply_Model');
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

        if ($meaction && $meaction !== 'MAIN') {
            header('Content-Type: application/json');
            ini_set('display_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        }

        switch ($meaction) {
            case 'MAIN': 
                return view('fms/supply/supply-main');
                break;

            // ==============================
            // SUPPLY CRUD
            // ==============================
            case 'SAVE_SUPPLY':
                try { $result = $this->supplyModel->saveSupply(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_SUPPLY':
                try { $result = $this->supplyModel->updateSupply(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_SUPPLY':
                try { $result = $this->supplyModel->deleteSupply(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_SUPPLY':
                try {
                    $supply_id = $this->request->getPost('supply_id');
                    $result = $this->supplyModel->getSupply($supply_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'GET_ALL_SUPPLIES':
                try { $result = $this->supplyModel->getAllSupplies(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // TRANSACTION CRUD
            // ==============================
            case 'SAVE_TRANSACTION':
                try { $result = $this->supplyModel->saveTransaction(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_TRANSACTION':
                try { $result = $this->supplyModel->deleteTransaction(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_ALL_TRANSACTIONS':
                try { $result = $this->supplyModel->getAllTransactions(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_TRANSACTION':
                try {
                    $transaction_id = $this->request->getPost('transaction_id');
                    $result = $this->supplyModel->getTransaction($transaction_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            // ==============================
            // SUPPLY JOURNEY
            // ==============================
            case 'GET_SUPPLY_JOURNEY':
                try {
                    $supply_id = $this->request->getPost('supply_id');
                    $result = $this->supplyModel->getSupplyJourney($supply_id);
                } catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // LOOKUPS
            // ==============================
            case 'GET_TRUCKS':
                try { $result = $this->supplyModel->getTrucks(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_CATEGORIES':
                try { $result = $this->supplyModel->getCategories(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            default:
                return view('fms/supply/supply-main');
                break;
        }
    }
}