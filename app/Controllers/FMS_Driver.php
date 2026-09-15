<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Driver extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->driverModel = model('App\Models\FMS_Driver_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('fms/driver/driver-main');
                break;

            case 'SAVE': 
                $result = $this->driverModel->saveDriver();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->driverModel->updateDriver();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->driverModel->deleteDriver();
                echo json_encode($result);
                break;

            case 'GET_DRIVER':
                $driver_id = $this->request->getPost('driver_id');
                $driver = $this->driverModel->getDriver($driver_id);
                echo json_encode($driver);
                break;

            case 'SAVE_SKILLSET': 
                $result = $this->driverModel->saveSkillset();
                echo json_encode($result);
                break;

            case 'EDIT_SKILLSET': 
                $result = $this->driverModel->updateSkillset();
                echo json_encode($result);
                break;

            case 'DELETE_SKILLSET': 
                $result = $this->driverModel->deleteSkillset();
                echo json_encode($result);
                break;

            case 'GET_SKILLSETS':
                $driver_id = $this->request->getPost('driver_id');
                $skillsets = $this->driverModel->getSkillsets($driver_id);
                echo json_encode($skillsets);
                break;

            case 'GET_SKILLSET':
                $skillset_id = $this->request->getPost('skillset_id');
                $skillset = $this->driverModel->getSkillset($skillset_id);
                echo json_encode($skillset);
                break;

            default:
                return view('fms/driver/driver-main');
                break;
        }
    }
}