<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class BayStatusController extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->bayStatusModel = model('App\Models\BayStatusModel');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('baystatus/baystatus-main');
                break;

            case 'SAVE': 
                $result = $this->bayStatusModel->saveBayStatus();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->bayStatusModel->updateBayStatus();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->bayStatusModel->deleteBayStatus();
                echo json_encode($result);
                break;

            case 'ADD_BAY':
                $result = $this->bayStatusModel->addNewBay();
                echo json_encode($result);
                break;

            default:
                return view('baystatus/baystatus-main');
                break;
        }
    }
}