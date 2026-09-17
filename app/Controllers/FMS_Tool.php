<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Tool extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->toolModel = model('App\Models\FMS_Tool_Model');
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
                return view('fms/tool/tool-main');
                break;

            // ==============================
            // TOOL CRUD
            // ==============================
            case 'SAVE_TOOL':
                try { $result = $this->toolModel->saveTool(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_TOOL':
                try { $result = $this->toolModel->updateTool(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_TOOL':
                try { $result = $this->toolModel->deleteTool(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_TOOL':
                try {
                    $tool_id = $this->request->getPost('tool_id');
                    $result = $this->toolModel->getTool($tool_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'GET_ALL_TOOLS':
                try { $result = $this->toolModel->getAllTools(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // ISSUANCE CRUD
            // ==============================
            case 'SAVE_ISSUANCE':
                try { $result = $this->toolModel->saveIssuance(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'RETURN_TOOL':
                try { $result = $this->toolModel->returnTool(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_ISSUANCE':
                try { $result = $this->toolModel->deleteIssuance(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_ISSUANCE':
                try {
                    $issuance_id = $this->request->getPost('issuance_id');
                    $result = $this->toolModel->getIssuance($issuance_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'GET_ALL_ISSUANCES':
                try { $result = $this->toolModel->getAllIssuances(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // TOOL JOURNEY
            // ==============================
            case 'GET_TOOL_JOURNEY':
                try {
                    $tool_id = $this->request->getPost('tool_id');
                    $result = $this->toolModel->getToolJourney($tool_id);
                } catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // LOOKUPS
            // ==============================
            case 'GET_TRUCKS':
                try { $result = $this->toolModel->getTrucks(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_AVAILABLE_TOOLS':
                try { $result = $this->toolModel->getAvailableTools(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_CATEGORIES':
                try { $result = $this->toolModel->getCategories(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            default:
                return view('fms/tool/tool-main');
                break;
        }
    }
}