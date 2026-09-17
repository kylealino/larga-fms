<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Dispatch extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->dispatchModel = model('App\Models\FMS_Dispatch_Model');
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
                return view('fms/dispatch/dispatch-main');
                break;

            // DISPATCH CRUD
            case 'SAVE_DISPATCH':
                $result = $this->dispatchModel->saveDispatch();
                echo json_encode($result);
                break;

            case 'UPDATE_DISPATCH':
                $result = $this->dispatchModel->updateDispatch();
                echo json_encode($result);
                break;

            case 'DELETE_DISPATCH':
                $result = $this->dispatchModel->deleteDispatch();
                echo json_encode($result);
                break;

            case 'GET_DISPATCH':
                $dispatch_id = $this->request->getPost('dispatch_id');
                $dispatch = $this->dispatchModel->getDispatch($dispatch_id);
                echo json_encode($dispatch);
                break;

            case 'GET_DISPATCH_BY_TRIP':
                $trip_id = $this->request->getPost('trip_id');
                $dispatch = $this->dispatchModel->getDispatchByTrip($trip_id);
                echo json_encode($dispatch);
                break;

            case 'GET_ASSIGNED_TRIPS':
                $trips = $this->dispatchModel->getAssignedTrips();
                echo json_encode($trips);
                break;

            // CHECKLIST CRUD
            case 'GET_CHECKLIST':
                $dispatch_id = $this->request->getPost('dispatch_id');
                if($dispatch_id) {
                    $checklist = $this->dispatchModel->getChecklistByDispatch($dispatch_id);
                    echo json_encode($checklist);
                } else {
                    echo json_encode([]);
                }
                break;

            case 'GET_CHECKLIST_ITEM':
                $checklist_id = $this->request->getPost('checklist_id');
                $item = $this->dispatchModel->getChecklistItem($checklist_id);
                echo json_encode($item);
                break;

            case 'SAVE_CHECKLIST':
                $result = $this->dispatchModel->saveChecklistItem();
                echo json_encode($result);
                break;

            case 'UPDATE_CHECKLIST':
                $result = $this->dispatchModel->updateChecklistItem();
                echo json_encode($result);
                break;

            case 'DELETE_CHECKLIST':
                $result = $this->dispatchModel->deleteChecklistItem();
                echo json_encode($result);
                break;

            // EXPENSES CRUD
            case 'GET_EXPENSES':
                $dispatch_id = $this->request->getPost('dispatch_id');
                if($dispatch_id) {
                    $expenses = $this->dispatchModel->getExpensesByDispatch($dispatch_id);
                    echo json_encode($expenses);
                } else {
                    echo json_encode([]);
                }
                break;

            case 'GET_EXPENSE':
                $expense_id = $this->request->getPost('expense_id');
                $expense = $this->dispatchModel->getExpense($expense_id);
                echo json_encode($expense);
                break;

            case 'SAVE_EXPENSE':
                $result = $this->dispatchModel->saveExpense();
                echo json_encode($result);
                break;

            case 'UPDATE_EXPENSE':
                $result = $this->dispatchModel->updateExpense();
                echo json_encode($result);
                break;

            case 'DELETE_EXPENSE':
                $result = $this->dispatchModel->deleteExpense();
                echo json_encode($result);
                break;

            // WAYPOINT TRACKING
            case 'GET_WAYPOINTS_TRACKING':
                $trip_id = $this->request->getPost('trip_id');
                $waypoints = $this->dispatchModel->getWaypointsWithTracking($trip_id);
                echo json_encode($waypoints);
                break;

            case 'GET_WAYPOINT_TRACKING':
                $waypoint_id = $this->request->getPost('waypoint_id');
                $waypoint = $this->dispatchModel->getWaypointWithTracking($waypoint_id);
                echo json_encode($waypoint);
                break;

            case 'UPDATE_WAYPOINT_ARRIVAL':
                $result = $this->dispatchModel->updateWaypointArrival();
                echo json_encode($result);
                break;

            case 'UPDATE_WAYPOINT_DEPARTURE':
                $result = $this->dispatchModel->updateWaypointDeparture();
                echo json_encode($result);
                break;
                
            case 'PRINT-DR-PREVIEW':
                return view('fms/dispatch/dispatch-pdf-preview');
                break;

            default:
                return view('fms/dispatch/dispatch-main');
                break;
        }
    }
}