<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_DeliveryReceipt extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->drModel = model('App\Models\FMS_DeliveryReceipt_Model');
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
                return view('fms/delivery_receipt/dr-main');
                break;

            // ==============================
            // DR CRUD
            // ==============================
            case 'SAVE_DR':
                $result = $this->drModel->saveDR();
                echo json_encode($result);
                break;

            case 'UPDATE_DR':
                $result = $this->drModel->updateDR();
                echo json_encode($result);
                break;

            case 'DELETE_DR':
                $result = $this->drModel->deleteDR();
                echo json_encode($result);
                break;

            case 'GET_DR':
                $dr_id = $this->request->getPost('dr_id');
                $dr = $this->drModel->getDR($dr_id);
                echo json_encode($dr);
                break;

            case 'GET_DR_BY_TRIP':
                $trip_id = $this->request->getPost('trip_id');
                $dr = $this->drModel->getDRByTrip($trip_id);
                echo json_encode($dr);
                break;

            case 'GET_ALL_DRS':
                $drs = $this->drModel->getAllDRs();
                echo json_encode($drs);
                break;

            case 'GET_DISPATCHED_TRIPS_FOR_DR':
                $trips = $this->drModel->getDispatchedTripsForDR();
                echo json_encode($trips);
                break;

            case 'CREATE_DR_FROM_TRIP':
                $result = $this->drModel->createDRFromTrip();
                echo json_encode($result);
                break;

            case 'GET_TRIP_FOR_DR':
                $trip_id = $this->request->getPost('trip_id');
                $trip = $this->drModel->getTripForDR($trip_id);
                echo json_encode($trip);
                break;

            case 'GET_TRIP_CARGO_ITEMS':
                $trip_id = $this->request->getPost('trip_id');
                $items = $this->drModel->getTripCargoItems($trip_id);
                echo json_encode($items);
                break;

            // ==============================
            // DR ITEMS
            // ==============================
            case 'GET_DR_ITEMS':
                $dr_id = $this->request->getPost('dr_id');
                if($dr_id) {
                    $items = $this->drModel->getDRItems($dr_id);
                    echo json_encode($items);
                } else {
                    echo json_encode([]);
                }
                break;

            case 'GET_DR_ITEM':
                $item_id = $this->request->getPost('item_id');
                $item = $this->drModel->getDRItem($item_id);
                echo json_encode($item);
                break;

            case 'SAVE_DR_ITEM':
                $result = $this->drModel->saveDRItem();
                echo json_encode($result);
                break;

            case 'UPDATE_DR_ITEM':
                $result = $this->drModel->updateDRItem();
                echo json_encode($result);
                break;

            case 'DELETE_DR_ITEM':
                $result = $this->drModel->deleteDRItem();
                echo json_encode($result);
                break;

            // ==============================
            // POD
            // ==============================
            case 'GET_POD':
                $dr_id = $this->request->getPost('dr_id');
                $pod = $this->drModel->getPOD($dr_id);
                echo json_encode($pod);
                break;

            case 'SAVE_POD':
                $result = $this->drModel->savePOD();
                echo json_encode($result);
                break;

            case 'UPDATE_POD':
                $result = $this->drModel->updatePOD();
                echo json_encode($result);
                break;

            case 'UPLOAD_POD_FILE':
                $result = $this->drModel->uploadPODFile();
                echo json_encode($result);
                break;

            case 'UPLOAD_SIGNATURE':
                $result = $this->drModel->uploadSignature();
                echo json_encode($result);
                break;

            // ==============================
            // PDF
            // ==============================
            case 'PRINT-DR': 
                return view('fms/delivery_receipt/dr-pdf');
                break;

            default:
                return view('fms/delivery_receipt/dr-main');
                break;
        }
    }
}