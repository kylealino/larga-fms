<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Dispatch_Model extends Model
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    // ==============================
    // GENERATE DISPATCH CODE
    // ==============================
    private function generateDispatchCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_dispatch WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'DSP-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GENERATE CHECKLIST CODE
    // ==============================
    private function generateChecklistCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_dispatch_checklist WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'CHK-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GENERATE EXPENSE CODE
    // ==============================
    private function generateExpenseCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_dispatch_expenses WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'EXP-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GET ASSIGNED TRIPS FOR DISPATCH
    // ==============================
    public function getAssignedTrips()
    {
        return $this->db->query("
            SELECT t.*, 
                   c.customer_name,
                   a.driver_name,
                   a.helper_name,
                   a.truck_plate,
                   a.tractor_plate,
                   a.chassis_plate,
                   a.vehicle_type,
                   a.vendor_name,
                   d.dispatch_id,
                   d.dispatch_status,
                   CASE WHEN d.dispatch_id IS NOT NULL THEN 1 ELSE 0 END as has_dispatch
            FROM tbl_trips t
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_trip_assignments a ON t.trip_id = a.trip_id
            LEFT JOIN tbl_dispatch d ON t.trip_id = d.trip_id
            WHERE t.trip_status IN ('ASSIGNED', 'DISPATCHED', 'IN_TRANSIT', 'DELIVERED', 'COMPLETED')
            ORDER BY t.scheduled_date DESC
        ")->getResultArray();
    }

    // ==============================
    // GET DISPATCH BY TRIP
    // ==============================
    public function getDispatchByTrip($trip_id)
    {
        $query = $this->db->query("
            SELECT d.*,
                   t.trip_code,
                   t.origin,
                   t.destination,
                   t.trip_status,
                   c.customer_name,
                   a.driver_name,
                   a.helper_name,
                   a.truck_plate,
                   a.tractor_plate,
                   a.chassis_plate,
                   a.vehicle_type,
                   a.vendor_name
            FROM tbl_dispatch d
            LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_trip_assignments a ON t.trip_id = a.trip_id
            WHERE d.trip_id = ?
            LIMIT 1
        ", [$trip_id]);
        return $query->getRowArray();
    }

    // ==============================
    // GET DISPATCH BY ID
    // ==============================
    public function getDispatch($dispatch_id)
    {
        $query = $this->db->query("
            SELECT d.*,
                   t.trip_code,
                   t.origin,
                   t.destination,
                   t.trip_status,
                   c.customer_name,
                   a.driver_name,
                   a.helper_name,
                   a.truck_plate,
                   a.tractor_plate,
                   a.chassis_plate,
                   a.vehicle_type,
                   a.vendor_name
            FROM tbl_dispatch d
            LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_trip_assignments a ON t.trip_id = a.trip_id
            WHERE d.dispatch_id = ?
        ", [$dispatch_id]);
        return $query->getRowArray();
    }

    // ==============================
    // CHECKLIST METHODS
    // ==============================
    public function getChecklistByDispatch($dispatch_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_dispatch_checklist 
            WHERE dispatch_id = ?
            ORDER BY checklist_id ASC
        ", [$dispatch_id])->getResultArray();
    }

    public function getChecklistItem($checklist_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_dispatch_checklist WHERE checklist_id = ?", [$checklist_id]);
        return $query->getRowArray();
    }

    public function saveChecklistItem()
    {
        $checklist_code = $this->generateChecklistCode();
        $dispatch_id = $this->request->getPost('dispatch_id');
        $trip_id = $this->request->getPost('trip_id');
        $item_name = $this->request->getPost('item_name');
        $available_quantity = $this->request->getPost('available_quantity') ?: 0;
        $required_quantity = $this->request->getPost('required_quantity') ?: 0;
        $condition_before = $this->request->getPost('condition_before') ?: 'GOOD';
        $checked = $this->request->getPost('checked') ?: 1;
        $accountable_person = $this->request->getPost('accountable_person');
        $checklist_status = $this->request->getPost('checklist_status') ?: 'COMPLETE';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_dispatch_checklist`(
                `checklist_code`, `dispatch_id`, `trip_id`, `item_name`,
                `available_quantity`, `required_quantity`, `condition_before`,
                `checked`, `accountable_person`, `checklist_status`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $checklist_code, $dispatch_id, $trip_id, $item_name,
                $available_quantity, $required_quantity, $condition_before,
                $checked, $accountable_person, $checklist_status, $remarks, $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Checklist Item Added Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding checklist item.'];
        }
    }

    public function updateChecklistItem()
    {
        $checklist_id = $this->request->getPost('checklist_id');
        $item_name = $this->request->getPost('item_name');
        $available_quantity = $this->request->getPost('available_quantity') ?: 0;
        $required_quantity = $this->request->getPost('required_quantity') ?: 0;
        $condition_before = $this->request->getPost('condition_before') ?: 'GOOD';
        $checked = $this->request->getPost('checked') ?: 1;
        $accountable_person = $this->request->getPost('accountable_person');
        $checklist_status = $this->request->getPost('checklist_status') ?: 'COMPLETE';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_dispatch_checklist`
            SET 
                `item_name` = ?, `available_quantity` = ?, `required_quantity` = ?,
                `condition_before` = ?, `checked` = ?, `accountable_person` = ?,
                `checklist_status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `checklist_id` = ?
            ",
            [
                $item_name, $available_quantity, $required_quantity,
                $condition_before, $checked, $accountable_person,
                $checklist_status, $remarks, $checklist_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Checklist Item Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating checklist item.'];
        }
    }

    public function deleteChecklistItem()
    {
        $checklist_id = $this->request->getPost('checklist_id');
        
        $query = $this->db->query("DELETE FROM `tbl_dispatch_checklist` WHERE `checklist_id` = ?", [$checklist_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Checklist Item Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting checklist item.'];
        }
    }

    // ==============================
    // EXPENSES METHODS
    // ==============================
    public function getExpensesByDispatch($dispatch_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_dispatch_expenses 
            WHERE dispatch_id = ?
            ORDER BY expense_date DESC
        ", [$dispatch_id])->getResultArray();
    }

    public function getExpense($expense_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_dispatch_expenses WHERE expense_id = ?", [$expense_id]);
        return $query->getRowArray();
    }

    public function saveExpense()
    {
        $expense_code = $this->generateExpenseCode();
        $dispatch_id = $this->request->getPost('dispatch_id');
        $trip_id = $this->request->getPost('trip_id');
        $expense_date = $this->request->getPost('expense_date') ?: date('Y-m-d');
        $expense_type = $this->request->getPost('expense_type');
        $description = $this->request->getPost('description');
        $amount = $this->request->getPost('amount') ?: 0;
        $paid_by = $this->request->getPost('paid_by');
        $reference_no = $this->request->getPost('reference_no');
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_dispatch_expenses`(
                `expense_code`, `dispatch_id`, `trip_id`, `expense_date`,
                `expense_type`, `description`, `amount`, `paid_by`,
                `reference_no`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $expense_code, $dispatch_id, $trip_id, $expense_date,
                $expense_type, $description, $amount, $paid_by,
                $reference_no, $remarks, $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Expense Added Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding expense.'];
        }
    }

    public function updateExpense()
    {
        $expense_id = $this->request->getPost('expense_id');
        $expense_date = $this->request->getPost('expense_date');
        $expense_type = $this->request->getPost('expense_type');
        $description = $this->request->getPost('description');
        $amount = $this->request->getPost('amount') ?: 0;
        $paid_by = $this->request->getPost('paid_by');
        $reference_no = $this->request->getPost('reference_no');
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_dispatch_expenses`
            SET 
                `expense_date` = ?, `expense_type` = ?, `description` = ?,
                `amount` = ?, `paid_by` = ?, `reference_no` = ?, `remarks` = ?,
                `updated_at` = NOW()
            WHERE `expense_id` = ?
            ",
            [
                $expense_date, $expense_type, $description,
                $amount, $paid_by, $reference_no, $remarks,
                $expense_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Expense Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating expense.'];
        }
    }

    public function deleteExpense()
    {
        $expense_id = $this->request->getPost('expense_id');
        
        $query = $this->db->query("DELETE FROM `tbl_dispatch_expenses` WHERE `expense_id` = ?", [$expense_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Expense Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting expense.'];
        }
    }

    // ==============================
    // SAVE DISPATCH
    // ==============================
    public function saveDispatch()
    {
        $dispatch_code = $this->generateDispatchCode();
        $trip_id = $this->request->getPost('trip_id');
        $dispatch_date = $this->request->getPost('dispatch_date') ?: date('Y-m-d');
        $dispatch_time = $this->request->getPost('dispatch_time');
        $truck = $this->request->getPost('truck');
        $driver = $this->request->getPost('driver');
        $helper = $this->request->getPost('helper');
        $origin = $this->request->getPost('origin');
        $destination = $this->request->getPost('destination');
        $odometer_out = $this->request->getPost('odometer_out') ?: 0;
        $fuel_level_out = $this->request->getPost('fuel_level_out') ?: 0;
        $container_required = $this->request->getPost('container_required') ?: 0;
        $container_number = $this->request->getPost('container_number');
        $container_type = $this->request->getPost('container_type');
        $container_description = $this->request->getPost('container_description');
        $container_markings = $this->request->getPost('container_markings');
        $container_reference = $this->request->getPost('container_reference');
        $container_release_port = $this->request->getPost('container_release_port');
        $container_release_date = $this->request->getPost('container_release_date');
        $container_release_time = $this->request->getPost('container_release_time');
        $container_return_required = $this->request->getPost('container_return_required') ?: 0;
        $container_return_date = $this->request->getPost('container_return_date');
        $container_return_time = $this->request->getPost('container_return_time');
        $container_return_port = $this->request->getPost('container_return_port');
        $container_return_odometer = $this->request->getPost('container_return_odometer') ?: 0;
        $container_return_status = $this->request->getPost('container_return_status') ?: 'NOT_APPLICABLE';
        $container_return_proof = $this->request->getPost('container_return_proof');
        $dispatcher_name = $this->request->getPost('dispatcher_name');
        $dispatch_status = $this->request->getPost('dispatch_status') ?: 'DISPATCHED';
        $actual_delivery_date = $this->request->getPost('actual_delivery_date');
        $actual_delivery_time = $this->request->getPost('actual_delivery_time');
        $odometer_in = $this->request->getPost('odometer_in') ?: 0;
        $fuel_level_in = $this->request->getPost('fuel_level_in') ?: 0;
        $total_distance = $this->request->getPost('total_distance') ?: 0;
        $fuel_consumed = $this->request->getPost('fuel_consumed') ?: 0;
        $delay_reason = $this->request->getPost('delay_reason');
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_dispatch`(
                `dispatch_code`, `trip_id`, `dispatch_date`, `dispatch_time`,
                `truck`, `driver`, `helper`, `origin`, `destination`,
                `odometer_out`, `fuel_level_out`,
                `container_required`, `container_number`, `container_type`,
                `container_description`, `container_markings`, `container_reference`,
                `container_release_port`, `container_release_date`, `container_release_time`,
                `container_return_required`, `container_return_date`, `container_return_time`,
                `container_return_port`, `container_return_odometer`, `container_return_status`,
                `container_return_proof`, `dispatcher_name`, `dispatch_status`,
                `actual_delivery_date`, `actual_delivery_time`,
                `odometer_in`, `fuel_level_in`, `total_distance`, `fuel_consumed`,
                `delay_reason`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $dispatch_code, $trip_id, $dispatch_date, $dispatch_time,
                $truck, $driver, $helper, $origin, $destination,
                $odometer_out, $fuel_level_out,
                $container_required, $container_number, $container_type,
                $container_description, $container_markings, $container_reference,
                $container_release_port, $container_release_date, $container_release_time,
                $container_return_required, $container_return_date, $container_return_time,
                $container_return_port, $container_return_odometer, $container_return_status,
                $container_return_proof, $dispatcher_name, $dispatch_status,
                $actual_delivery_date, $actual_delivery_time,
                $odometer_in, $fuel_level_in, $total_distance, $fuel_consumed,
                $delay_reason, $remarks, $this->cuser
            ]
        );

        if ($query) {
            $dispatch_id = $this->db->insertID();
            
            // Update trip status
            $this->db->query("UPDATE tbl_trips SET trip_status = ? WHERE trip_id = ?", [$dispatch_status, $trip_id]);
            
            return ['status' => 'success', 'message' => 'Dispatch Saved Successfully!', 'dispatch_id' => $dispatch_id];
        } else {
            $error = $this->db->error();
            log_message('error', 'Dispatch Save Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while saving dispatch.'];
        }
    }

    // ==============================
    // UPDATE DISPATCH
    // ==============================
    public function updateDispatch()
    {
        $dispatch_id = $this->request->getPost('dispatch_id');
        $trip_id = $this->request->getPost('trip_id');
        $dispatch_date = $this->request->getPost('dispatch_date');
        $dispatch_time = $this->request->getPost('dispatch_time');
        $truck = $this->request->getPost('truck');
        $driver = $this->request->getPost('driver');
        $helper = $this->request->getPost('helper');
        $origin = $this->request->getPost('origin');
        $destination = $this->request->getPost('destination');
        $odometer_out = $this->request->getPost('odometer_out') ?: 0;
        $fuel_level_out = $this->request->getPost('fuel_level_out') ?: 0;
        $container_required = $this->request->getPost('container_required') ?: 0;
        $container_number = $this->request->getPost('container_number');
        $container_type = $this->request->getPost('container_type');
        $container_description = $this->request->getPost('container_description');
        $container_markings = $this->request->getPost('container_markings');
        $container_reference = $this->request->getPost('container_reference');
        $container_release_port = $this->request->getPost('container_release_port');
        $container_release_date = $this->request->getPost('container_release_date');
        $container_release_time = $this->request->getPost('container_release_time');
        $container_return_required = $this->request->getPost('container_return_required') ?: 0;
        $container_return_date = $this->request->getPost('container_return_date');
        $container_return_time = $this->request->getPost('container_return_time');
        $container_return_port = $this->request->getPost('container_return_port');
        $container_return_odometer = $this->request->getPost('container_return_odometer') ?: 0;
        $container_return_status = $this->request->getPost('container_return_status') ?: 'NOT_APPLICABLE';
        $container_return_proof = $this->request->getPost('container_return_proof');
        $dispatcher_name = $this->request->getPost('dispatcher_name');
        $dispatch_status = $this->request->getPost('dispatch_status');
        $actual_delivery_date = $this->request->getPost('actual_delivery_date');
        $actual_delivery_time = $this->request->getPost('actual_delivery_time');
        $odometer_in = $this->request->getPost('odometer_in') ?: 0;
        $fuel_level_in = $this->request->getPost('fuel_level_in') ?: 0;
        $total_distance = $this->request->getPost('total_distance') ?: 0;
        $fuel_consumed = $this->request->getPost('fuel_consumed') ?: 0;
        $delay_reason = $this->request->getPost('delay_reason');
        $remarks = $this->request->getPost('remarks');

        // Same container-return gate as the last-waypoint-arrival flow: don't let a
        // manual "Completed" pick skip past a container that hasn't been returned yet.
        $completionBlocked = false;
        if ($dispatch_status === 'COMPLETED' && (int) $container_return_required === 1 && $container_return_status !== 'RETURNED') {
            $dispatch_status = 'DELIVERED';
            $completionBlocked = true;
        }

        $query = $this->db->query("
            UPDATE `tbl_dispatch`
            SET
                `dispatch_date` = ?, `dispatch_time` = ?,
                `truck` = ?, `driver` = ?, `helper` = ?,
                `origin` = ?, `destination` = ?,
                `odometer_out` = ?, `fuel_level_out` = ?,
                `container_required` = ?, `container_number` = ?,
                `container_type` = ?, `container_description` = ?,
                `container_markings` = ?, `container_reference` = ?,
                `container_release_port` = ?, `container_release_date` = ?,
                `container_release_time` = ?,
                `container_return_required` = ?, `container_return_date` = ?,
                `container_return_time` = ?, `container_return_port` = ?,
                `container_return_odometer` = ?, `container_return_status` = ?,
                `container_return_proof` = ?, `dispatcher_name` = ?,
                `dispatch_status` = ?, `actual_delivery_date` = ?,
                `actual_delivery_time` = ?, `odometer_in` = ?,
                `fuel_level_in` = ?, `total_distance` = ?,
                `fuel_consumed` = ?, `delay_reason` = ?, `remarks` = ?,
                `updated_at` = NOW()
            WHERE `dispatch_id` = ?
            ",
            [
                $dispatch_date, $dispatch_time,
                $truck, $driver, $helper,
                $origin, $destination,
                $odometer_out, $fuel_level_out,
                $container_required, $container_number,
                $container_type, $container_description,
                $container_markings, $container_reference,
                $container_release_port, $container_release_date,
                $container_release_time,
                $container_return_required, $container_return_date,
                $container_return_time, $container_return_port,
                $container_return_odometer, $container_return_status,
                $container_return_proof, $dispatcher_name,
                $dispatch_status, $actual_delivery_date,
                $actual_delivery_time, $odometer_in,
                $fuel_level_in, $total_distance,
                $fuel_consumed, $delay_reason, $remarks,
                $dispatch_id
            ]
        );

        if ($query) {
            // Update trip status
            $this->db->query("UPDATE tbl_trips SET trip_status = ? WHERE trip_id = ?", [$dispatch_status, $trip_id]);

            if ($dispatch_status === 'COMPLETED') {
                $this->releaseTripResources($trip_id);
            }

            if ($completionBlocked) {
                return ['status' => 'success', 'message' => 'Dispatch Updated — container return is still pending, so it was kept at Delivered instead of Completed.'];
            }
            return ['status' => 'success', 'message' => 'Dispatch Updated Successfully!'];
        } else {
            $error = $this->db->error();
            log_message('error', 'Dispatch Update Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while updating dispatch.'];
        }
    }

    // ==============================
    // DELETE DISPATCH
    // ==============================
    public function deleteDispatch()
    {
        $dispatch_id = $this->request->getPost('dispatch_id');
        $trip_id = $this->request->getPost('trip_id');

        // Delete checklist and expenses first
        $this->db->query("DELETE FROM `tbl_dispatch_checklist` WHERE `dispatch_id` = ?", [$dispatch_id]);
        $this->db->query("DELETE FROM `tbl_dispatch_expenses` WHERE `dispatch_id` = ?", [$dispatch_id]);

        $query = $this->db->query("DELETE FROM `tbl_dispatch` WHERE `dispatch_id` = ?", [$dispatch_id]);

        if ($query) {
            // Update trip status back to ASSIGNED
            $this->db->query("UPDATE tbl_trips SET trip_status = 'ASSIGNED' WHERE trip_id = ?", [$trip_id]);
            return ['status' => 'success', 'message' => 'Dispatch Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting dispatch.'];
        }
    }

    // ==============================
    // WAYPOINT TRACKING METHODS
    // ==============================
    public function getWaypointsWithTracking($trip_id)
    {
        return $this->db->query("
            SELECT w.*,
                   w.actual_arrival,
                   w.actual_departure,
                   w.waypoint_status,
                   w.arrival_remarks,
                   w.departure_remarks
            FROM tbl_trip_waypoints w
            WHERE w.trip_id = ?
            ORDER BY w.sequence ASC
        ", [$trip_id])->getResultArray();
    }

    public function getWaypointWithTracking($waypoint_id)
    {
        $query = $this->db->query("
            SELECT w.*,
                   w.actual_arrival,
                   w.actual_departure,
                   w.waypoint_status,
                   w.arrival_remarks,
                   w.departure_remarks
            FROM tbl_trip_waypoints w
            WHERE w.waypoint_id = ?
        ", [$waypoint_id]);
        return $query->getRowArray();
    }

    public function updateWaypointArrival()
    {
        $waypoint_id = $this->request->getPost('waypoint_id');
        $actual_arrival = $this->request->getPost('actual_arrival');
        $arrival_remarks = $this->request->getPost('arrival_remarks');

        if(!$actual_arrival) {
            return ['status' => 'error', 'message' => 'Please select arrival date/time'];
        }

        $waypoint = $this->db->query("SELECT trip_id, sequence, waypoint_type FROM tbl_trip_waypoints WHERE waypoint_id = ?", [$waypoint_id])->getRow();

        $isLastWaypoint = false;
        if ($waypoint) {
            $lastSeq = $this->db->query("SELECT MAX(sequence) as max_seq FROM tbl_trip_waypoints WHERE trip_id = ?", [$waypoint->trip_id])->getRow();
            $isLastWaypoint = $lastSeq && (int) $waypoint->sequence === (int) $lastSeq->max_seq;
        }

        $waypoint_status = $isLastWaypoint ? 'COMPLETED' : 'ARRIVED';

        $query = $this->db->query("
            UPDATE `tbl_trip_waypoints`
            SET
                `actual_arrival` = ?,
                `waypoint_status` = ?,
                `arrival_remarks` = ?,
                `updated_at` = NOW()
            WHERE `waypoint_id` = ?
        ", [$actual_arrival, $waypoint_status, $arrival_remarks, $waypoint_id]);

        if ($query) {
            // Arriving at the container return stop closes out the container return sub-task
            if ($waypoint && in_array($waypoint->waypoint_type, ['PORT_TERMINAL', 'RETURN_POINT'])) {
                $ts = strtotime(str_replace('T', ' ', $actual_arrival));
                $this->db->query("
                    UPDATE tbl_dispatch
                    SET container_return_status = 'RETURNED', container_return_date = ?, container_return_time = ?, updated_at = NOW()
                    WHERE trip_id = ? AND container_return_required = 1 AND container_return_status != 'RETURNED'
                ", [date('Y-m-d', $ts), date('H:i:s', $ts), $waypoint->trip_id]);
            }

            if ($isLastWaypoint) {
                $completed = $this->completeTripResources($waypoint->trip_id, $actual_arrival);
                if ($completed) {
                    return ['status' => 'success', 'message' => 'Last waypoint reached — trip completed, truck/driver/helper are now available.'];
                }
                return ['status' => 'success', 'message' => 'Last waypoint reached, but container return is still pending — trip stays at Delivered until the container is returned.'];
            }
            return ['status' => 'success', 'message' => 'Arrival recorded successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while recording arrival.'];
        }
    }

    // ==============================
    // COMPLETE TRIP: sync trip/dispatch status and free up resources
    // (gated on container return, when one is required)
    // ==============================
    private function completeTripResources($trip_id, $actual_arrival)
    {
        $dispatch = $this->db->query("
            SELECT dispatch_id, container_return_required, container_return_status
            FROM tbl_dispatch WHERE trip_id = ?
        ", [$trip_id])->getRow();

        if ($dispatch && (int) $dispatch->container_return_required === 1 && $dispatch->container_return_status !== 'RETURNED') {
            return false;
        }

        $this->db->query("UPDATE tbl_trips SET trip_status = 'COMPLETED' WHERE trip_id = ?", [$trip_id]);

        $ts = strtotime(str_replace('T', ' ', $actual_arrival));
        $arrivalDate = date('Y-m-d', $ts);
        $arrivalTime = date('H:i:s', $ts);

        if ($dispatch) {
            $this->db->query("
                UPDATE tbl_dispatch
                SET dispatch_status = 'COMPLETED', actual_delivery_date = ?, actual_delivery_time = ?, updated_at = NOW()
                WHERE dispatch_id = ?
            ", [$arrivalDate, $arrivalTime, $dispatch->dispatch_id]);
        }

        $this->releaseTripResources($trip_id);

        return true;
    }

    // ==============================
    // RELEASE TRIP RESOURCES: free the truck/tractor/chassis/driver/helper
    // assigned to a trip back to AVAILABLE. Called whenever a trip/dispatch
    // is marked COMPLETED, whether via last-waypoint-arrival or a manual
    // dispatch edit.
    // ==============================
    private function releaseTripResources($trip_id)
    {
        $assignment = $this->db->query("
            SELECT truck_plate, tractor_plate, chassis_plate, driver_name, helper_name
            FROM tbl_trip_assignments
            WHERE trip_id = ?
            ORDER BY assignment_id DESC
            LIMIT 1
        ", [$trip_id])->getRow();

        if ($assignment) {
            foreach ([$assignment->truck_plate, $assignment->tractor_plate, $assignment->chassis_plate] as $plate) {
                if (!empty($plate)) {
                    $this->db->query("UPDATE tbl_trucks SET truck_status = 'AVAILABLE' WHERE plate_number = ?", [$plate]);
                }
            }
            if (!empty($assignment->driver_name)) {
                $this->db->query("UPDATE tbl_drivers SET driver_status = 'AVAILABLE' WHERE driver_name = ?", [$assignment->driver_name]);
            }
            if (!empty($assignment->helper_name)) {
                $this->db->query("UPDATE tbl_helpers SET helper_status = 'AVAILABLE' WHERE helper_name = ?", [$assignment->helper_name]);
            }
        }
    }

    public function updateWaypointDeparture()
    {
        $waypoint_id = $this->request->getPost('waypoint_id');
        $actual_departure = $this->request->getPost('actual_departure');
        $departure_remarks = $this->request->getPost('departure_remarks');
        
        if(!$actual_departure) {
            return ['status' => 'error', 'message' => 'Please select departure date/time'];
        }
        
        $query = $this->db->query("
            UPDATE `tbl_trip_waypoints`
            SET 
                `actual_departure` = ?,
                `waypoint_status` = 'DEPARTED',
                `departure_remarks` = ?,
                `updated_at` = NOW()
            WHERE `waypoint_id` = ?
        ", [$actual_departure, $departure_remarks, $waypoint_id]);
        
        if ($query) {
            return ['status' => 'success', 'message' => 'Departure recorded successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while recording departure.'];
        }
    }
}