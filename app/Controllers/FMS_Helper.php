<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Helper extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->helperModel = model('App\Models\FMS_Helper_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('fms/helper/helper-main');
                break;

            case 'SAVE': 
                $result = $this->helperModel->saveHelper();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->helperModel->updateHelper();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->helperModel->deleteHelper();
                echo json_encode($result);
                break;

            case 'GET_HELPER':
                $helper_id = $this->request->getPost('helper_id');
                $helper = $this->helperModel->getHelper($helper_id);
                echo json_encode($helper);
                break;

            case 'GET_HELPER_HISTORY':
                $helper_id = $this->request->getPost('helper_id');
                $history = $this->helperModel->getHelperHistory($helper_id);
                echo json_encode($history);
                break;

            default:
                return view('fms/helper/helper-main');
                break;
        }
    }
}