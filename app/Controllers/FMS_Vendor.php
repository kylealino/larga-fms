<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Vendor extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->vendorModel = model('App\Models\FMS_Vendor_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('fms/vendor/vendor-main');
                break;

            case 'SAVE': 
                $result = $this->vendorModel->saveVendor();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->vendorModel->updateVendor();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->vendorModel->deleteVendor();
                echo json_encode($result);
                break;

            case 'GET_VENDOR':
                $vendor_id = $this->request->getPost('vendor_id');
                $vendor = $this->vendorModel->getVendorWithServices($vendor_id);
                echo json_encode($vendor);
                break;

            case 'SAVE_SERVICE': 
                $result = $this->vendorModel->saveService();
                echo json_encode($result);
                break;

            case 'EDIT_SERVICE': 
                $result = $this->vendorModel->updateService();
                echo json_encode($result);
                break;

            case 'DELETE_SERVICE': 
                $result = $this->vendorModel->deleteService();
                echo json_encode($result);
                break;

            case 'GET_SERVICES':
                $vendor_id = $this->request->getPost('vendor_id');
                $services = $this->vendorModel->getServices($vendor_id);
                echo json_encode($services);
                break;

            case 'GET_SERVICE':
                $service_id = $this->request->getPost('service_id');
                $service = $this->vendorModel->getService($service_id);
                echo json_encode($service);
                break;

            default:
                return view('fms/vendor/vendor-main');
                break;
        }
    }
}