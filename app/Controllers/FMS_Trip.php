<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Trip extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->tripModel = model('App\Models\FMS_Trip_Model');
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
                return view('fms/trip/trip-main');
                break;

            // TRIP CRUD
            case 'SAVE': 
                $result = $this->tripModel->saveTrip();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->tripModel->updateTrip();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->tripModel->deleteTrip();
                echo json_encode($result);
                break;

            case 'GET_TRIP':
                $trip_id = $this->request->getPost('trip_id');
                $trip = $this->tripModel->getTrip($trip_id);
                echo json_encode($trip);
                break;

            // ASSIGNMENT CRUD
            case 'GET_ASSIGNMENT':
                $trip_id = $this->request->getPost('trip_id');
                $assignment = $this->tripModel->getAssignment($trip_id);
                echo json_encode($assignment);
                break;

            case 'SAVE_ASSIGNMENT':
                $result = $this->tripModel->saveAssignment();
                echo json_encode($result);
                break;

            case 'UPDATE_ASSIGNMENT':
                $result = $this->tripModel->updateAssignment();
                echo json_encode($result);
                break;

            case 'DELETE_ASSIGNMENT':
                $result = $this->tripModel->deleteAssignment();
                echo json_encode($result);
                break;

            // RESOURCE LOADING
            case 'GET_AVAILABLE_TRUCKS':
                $result = $this->tripModel->getAvailableTrucks();
                echo json_encode($result);
                break;

            case 'GET_AVAILABLE_TRACTORS':
                $result = $this->tripModel->getAvailableTractors();
                echo json_encode($result);
                break;

            case 'GET_AVAILABLE_CHASSIS':
                $result = $this->tripModel->getAvailableChassis();
                echo json_encode($result);
                break;

            case 'GET_AVAILABLE_DRIVERS':
                $result = $this->tripModel->getAvailableDrivers();
                echo json_encode($result);
                break;

            case 'GET_AVAILABLE_HELPERS':
                $result = $this->tripModel->getAvailableHelpers();
                echo json_encode($result);
                break;

            case 'GET_ACTIVE_VENDORS':
                $result = $this->tripModel->getActiveVendors();
                echo json_encode($result);
                break;

            // WAYPOINT CRUD
            case 'SAVE_WAYPOINT':
                $result = $this->tripModel->saveWaypoint();
                echo json_encode($result);
                break;

            case 'EDIT_WAYPOINT':
                $result = $this->tripModel->updateWaypoint();
                echo json_encode($result);
                break;

            case 'DELETE_WAYPOINT':
                $result = $this->tripModel->deleteWaypoint();
                echo json_encode($result);
                break;

            case 'GET_WAYPOINTS':
                $trip_id = $this->request->getPost('trip_id');
                $waypoints = $this->tripModel->getWaypointsByTrip($trip_id);
                echo json_encode($waypoints);
                break;

            case 'GET_WAYPOINT':
                $waypoint_id = $this->request->getPost('waypoint_id');
                $waypoint = $this->tripModel->getWaypoint($waypoint_id);
                echo json_encode($waypoint);
                break;

            // CARGO ITEMS
            case 'GET_CARGO_ITEMS':
                $trip_id = $this->request->getPost('trip_id');
                $items = $this->tripModel->getCargoItems($trip_id);
                echo json_encode($items);
                break;

            case 'GET_CARGO_ITEM':
                $item_id = $this->request->getPost('item_id');
                $item = $this->tripModel->getCargoItem($item_id);
                echo json_encode($item);
                break;

            case 'SAVE_CARGO_ITEM':
                $result = $this->tripModel->saveCargoItem();
                echo json_encode($result);
                break;

            case 'UPDATE_CARGO_ITEM':
                $result = $this->tripModel->updateCargoItem();
                echo json_encode($result);
                break;

            case 'DELETE_CARGO_ITEM':
                $result = $this->tripModel->deleteCargoItem();
                echo json_encode($result);
                break;

            default:
                return view('fms/trip/trip-main');
                break;
        }
    }
}