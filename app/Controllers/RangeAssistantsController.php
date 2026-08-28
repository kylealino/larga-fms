<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class RangeAssistantsController extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->assistantModel = model('App\Models\RangeAssistantsModel');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('rangeassistants/rangeassistants-main');
                break;

            case 'SAVE': 
                $result = $this->assistantModel->saveAssistant();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->assistantModel->updateAssistant();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->assistantModel->deleteAssistant();
                echo json_encode($result);
                break;

            default:
                return view('rangeassistants/rangeassistants-main');
                break;
        }
    }
}