<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Maintenance extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->mtModel = model('App\Models\FMS_Maintenance_Model');
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

        // Force JSON output for non-view actions
        if ($meaction && $meaction !== 'MAIN') {
            header('Content-Type: application/json');
            ini_set('display_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        }

        switch ($meaction) {
            case 'MAIN': 
                return view('fms/maintenance/maintenance-main');
                break;

            // ==============================
            // SCHEDULE CRUD
            // ==============================
            case 'SAVE_SCHEDULE':
                try { $result = $this->mtModel->saveSchedule(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_SCHEDULE':
                try { $result = $this->mtModel->updateSchedule(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_SCHEDULE':
                try { $result = $this->mtModel->deleteSchedule(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_SCHEDULE':
                try {
                    $schedule_id = $this->request->getPost('schedule_id');
                    $result = $this->mtModel->getSchedule($schedule_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'GET_ALL_SCHEDULES':
                try { $result = $this->mtModel->getAllSchedules(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // RECORD CRUD
            // ==============================
            case 'SAVE_RECORD':
                try { $result = $this->mtModel->saveRecord(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_RECORD':
                try { $result = $this->mtModel->updateRecord(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_RECORD':
                try { $result = $this->mtModel->deleteRecord(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_RECORD':
                try {
                    $record_id = $this->request->getPost('record_id');
                    $result = $this->mtModel->getRecord($record_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'GET_ALL_RECORDS':
                try { $result = $this->mtModel->getAllRecords(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            // ==============================
            // PARTS CRUD
            // ==============================
            case 'GET_PARTS':
                try {
                    $record_id = $this->request->getPost('record_id');
                    $result = $this->mtModel->getParts($record_id);
                } catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_PART':
                try {
                    $part_id = $this->request->getPost('part_id');
                    $result = $this->mtModel->getPart($part_id);
                } catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'SAVE_PART':
                try { $result = $this->mtModel->savePart(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_PART':
                try { $result = $this->mtModel->updatePart(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_PART':
                try { $result = $this->mtModel->deletePart(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            // ==============================
            // LOOKUPS
            // ==============================
            case 'GET_TRUCKS':
                try { $result = $this->mtModel->getTrucks(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            default:
                return view('fms/maintenance/maintenance-main');
                break;
        }
    }
}