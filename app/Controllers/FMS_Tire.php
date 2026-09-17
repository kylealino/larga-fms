<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Tire extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->tireModel = model('App\Models\FMS_Tire_Model');
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
                return view('fms/tire/tire-main');
                break;

            // ==============================
            // TIRE CRUD
            // ==============================
            case 'SAVE_TIRE':
                try { $result = $this->tireModel->saveTire(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_TIRE':
                try { $result = $this->tireModel->updateTire(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_TIRE':
                try { $result = $this->tireModel->deleteTire(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_TIRE':
                try {
                    $tire_id = $this->request->getPost('tire_id');
                    $result = $this->tireModel->getTire($tire_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'GET_ALL_TIRES':
                try { $result = $this->tireModel->getAllTires(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // TRANSACTIONS (IN / OUT)
            // ==============================
            case 'SAVE_TRANSACTION':
                try { $result = $this->tireModel->saveTransaction(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_TRANSACTION':
                try { $result = $this->tireModel->deleteTransaction(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_ALL_TRANSACTIONS':
                try { $result = $this->tireModel->getAllTransactions(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_TRANSACTION':
                try {
                    $transaction_id = $this->request->getPost('transaction_id');
                    $result = $this->tireModel->getTransaction($transaction_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            // ==============================
            // INSTALLATIONS
            // ==============================
            case 'SAVE_INSTALLATION':
                try { $result = $this->tireModel->saveInstallation(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'REMOVE_INSTALLATION':
                try { $result = $this->tireModel->removeInstallation(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_INSTALLATION':
                try { $result = $this->tireModel->deleteInstallation(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_ALL_INSTALLATIONS':
                try { $result = $this->tireModel->getAllInstallations(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_INSTALLATION':
                try {
                    $installation_id = $this->request->getPost('installation_id');
                    $result = $this->tireModel->getInstallation($installation_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            // ==============================
            // DISPOSALS
            // ==============================
            case 'SAVE_DISPOSAL':
                try { $result = $this->tireModel->saveDisposal(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_DISPOSAL':
                try { $result = $this->tireModel->deleteDisposal(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_ALL_DISPOSALS':
                try { $result = $this->tireModel->getAllDisposals(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // TIRE JOURNEY
            // ==============================
            case 'GET_TIRE_JOURNEY':
                try {
                    $tire_id = $this->request->getPost('tire_id');
                    $result = $this->tireModel->getTireJourney($tire_id);
                } catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // LOOKUPS
            // ==============================
            case 'GET_TRUCKS':
                try { $result = $this->tireModel->getTrucks(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_AVAILABLE_TIRES':
                try { $result = $this->tireModel->getAvailableTires(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            default:
                return view('fms/tire/tire-main');
                break;
        }
    }
}