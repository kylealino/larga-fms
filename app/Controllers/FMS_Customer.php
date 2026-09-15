<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Customer extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->customerModel = model('App\Models\FMS_Customer_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('fms/customer/customer-main');
                break;

            case 'SAVE': 
                $result = $this->customerModel->saveCustomer();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->customerModel->updateCustomer();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->customerModel->deleteCustomer();
                echo json_encode($result);
                break;

            // ==============================
            // ADD THIS CASE FOR VIEW CUSTOMER
            // ==============================
            case 'GET_CUSTOMER':
                $customer_id = $this->request->getPost('customer_id');
                $customer = $this->customerModel->getCustomer($customer_id);
                echo json_encode($customer);
                break;

            case 'SAVE_LOCATION': 
                $result = $this->customerModel->saveLocation();
                echo json_encode($result);
                break;

            case 'EDIT_LOCATION': 
                $result = $this->customerModel->updateLocation();
                echo json_encode($result);
                break;

            case 'DELETE_LOCATION': 
                $result = $this->customerModel->deleteLocation();
                echo json_encode($result);
                break;

            case 'GET_LOCATIONS':
                $customer_id = $this->request->getPost('customer_id');
                $locations = $this->customerModel->getLocations($customer_id);
                echo json_encode($locations);
                break;

            case 'GET_LOCATION':
                $location_id = $this->request->getPost('location_id');
                $location = $this->customerModel->getLocation($location_id);
                echo json_encode($location);
                break;

            default:
                return view('fms/customer/customer-main');
                break;
        }
    }
}