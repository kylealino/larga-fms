<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_AR extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->arModel = model('App\Models\FMS_AR_Model');
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
                return view('fms/ar/ar-main');
                break;

            case 'GET_RECEIVABLES':
                try { $result = $this->arModel->getReceivables(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            default:
                return view('fms/ar/ar-main');
                break;
        }
    }
}
