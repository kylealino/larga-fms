<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Truck extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->truckModel = model('App\Models\FMS_Truck_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('fms/truck/truck-main');
                break;

            case 'SAVE': 
                $result = $this->truckModel->saveTruck();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->truckModel->updateTruck();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->truckModel->deleteTruck();
                echo json_encode($result);
                break;

            case 'GET_TRUCK':
                $truck_id = $this->request->getPost('truck_id');
                $truck = $this->truckModel->getTruck($truck_id);
                echo json_encode($truck);
                break;

            case 'SAVE_DOCUMENT': 
                $result = $this->truckModel->saveDocument();
                echo json_encode($result);
                break;

            case 'EDIT_DOCUMENT': 
                $result = $this->truckModel->updateDocument();
                echo json_encode($result);
                break;

            case 'DELETE_DOCUMENT': 
                $result = $this->truckModel->deleteDocument();
                echo json_encode($result);
                break;

            case 'GET_DOCUMENTS':
                $truck_id = $this->request->getPost('truck_id');
                $documents = $this->truckModel->getDocuments($truck_id);
                echo json_encode($documents);
                break;

            case 'GET_DOCUMENT':
                $document_id = $this->request->getPost('document_id');
                $document = $this->truckModel->getDocument($document_id);
                echo json_encode($document);
                break;

            case 'GET_TRUCK_HISTORY':
                $truck_id = $this->request->getPost('truck_id');
                $history = $this->truckModel->getTruckHistory($truck_id);
                echo json_encode($history);
                break;

            default:
                return view('fms/truck/truck-main');
                break;
        }
    }
}