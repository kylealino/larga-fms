<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Trip_Model extends Model
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
    // GENERATE TRIP CODE
    // ==============================
    private function generateTripCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_trips WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'TRP-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GET AVAILABLE TRUCKS
    // ==============================
    public function getAvailableTrucks()
    {
        return $this->db->query("
            SELECT truck_id, plate_number, vehicle_config, vehicle_type 
            FROM tbl_trucks 
            WHERE truck_status = 'AVAILABLE' AND vehicle_config = 'RIGID'
            ORDER BY plate_number
        ")->getResultArray();
    }

    // ==============================
    // GET AVAILABLE TRACTORS
    // ==============================
    public function getAvailableTractors()
    {
        return $this->db->query("
            SELECT truck_id, plate_number, vehicle_config, vehicle_type 
            FROM tbl_trucks 
            WHERE truck_status = 'AVAILABLE' AND vehicle_config = 'TRACTOR'
            ORDER BY plate_number
        ")->getResultArray();
    }

    // ==============================
    // GET AVAILABLE CHASSIS
    // ==============================
    public function getAvailableChassis()
    {
        return $this->db->query("
            SELECT truck_id, plate_number, vehicle_config, vehicle_type, body_type
            FROM tbl_trucks 
            WHERE truck_status = 'AVAILABLE' AND vehicle_config = 'TRAILER'
            ORDER BY plate_number
        ")->getResultArray();
    }

    // ==============================
    // GET AVAILABLE DRIVERS
    // ==============================
    public function getAvailableDrivers()
    {
        return $this->db->query("
            SELECT driver_id, driver_name 
            FROM tbl_drivers 
            WHERE driver_status = 'AVAILABLE' 
            ORDER BY driver_name
        ")->getResultArray();
    }

    // ==============================
    // GET AVAILABLE HELPERS
    // ==============================
    public function getAvailableHelpers()
    {
        return $this->db->query("
            SELECT helper_id, helper_name 
            FROM tbl_helpers 
            WHERE helper_status = 'AVAILABLE' 
            ORDER BY helper_name
        ")->getResultArray();
    }

    // ==============================
    // GET ACTIVE VENDORS
    // ==============================
    public function getActiveVendors()
    {
        return $this->db->query("
            SELECT vendor_id, vendor_name, vendor_type
            FROM tbl_vendors 
            WHERE vendor_status = 'ACTIVE' 
            ORDER BY vendor_name
        ")->getResultArray();
    }

    // ==============================
    // SAVE TRIP
    // ==============================
    public function saveTrip()
    {
        $trip_code = $this->generateTripCode();
        $customer_id = $this->request->getPost('customer_id');
        $booking_reference = $this->request->getPost('booking_reference');
        $service_type = $this->request->getPost('service_type');
        $trip_type = $this->request->getPost('trip_type') ?: 'ONE_WAY';
        $priority = $this->request->getPost('priority') ?: 'NORMAL';
        $scheduled_date = $this->request->getPost('scheduled_date');
        $pickup_date = $this->request->getPost('pickup_date');
        $expected_delivery_date = $this->request->getPost('expected_delivery_date');
        $origin = $this->request->getPost('origin');
        $destination = $this->request->getPost('destination');
        $cargo_description = $this->request->getPost('cargo_description');
        $quantity = $this->request->getPost('quantity') ?: 0;
        $unit = $this->request->getPost('unit');
        $estimated_weight = $this->request->getPost('estimated_weight') ?: 0;
        $special_instructions = $this->request->getPost('special_instructions');
        $trip_status = $this->request->getPost('trip_status') ?: 'SCHEDULED';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_trips`(
                `trip_code`, `customer_id`, `booking_reference`, `service_type`,
                `trip_type`, `priority`, `scheduled_date`, `pickup_date`,
                `expected_delivery_date`, `origin`, `destination`, `cargo_description`,
                `quantity`, `unit`, `estimated_weight`, `special_instructions`,
                `trip_status`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $trip_code, $customer_id, $booking_reference, $service_type,
                $trip_type, $priority, $scheduled_date, $pickup_date,
                $expected_delivery_date, $origin, $destination, $cargo_description,
                $quantity, $unit, $estimated_weight, $special_instructions,
                $trip_status, $remarks, $this->cuser
            ]
        );

        if ($query) {
            $trip_id = $this->db->insertID();
            return ['status' => 'success', 'message' => 'Trip Saved Successfully!', 'trip_id' => $trip_id];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        }
    }

    // ==============================
    // UPDATE TRIP
    // ==============================
    public function updateTrip()
    {
        $trip_id = $this->request->getPost('trip_id');
        $customer_id = $this->request->getPost('customer_id');
        $booking_reference = $this->request->getPost('booking_reference');
        $service_type = $this->request->getPost('service_type');
        $trip_type = $this->request->getPost('trip_type') ?: 'ONE_WAY';
        $priority = $this->request->getPost('priority') ?: 'NORMAL';
        $scheduled_date = $this->request->getPost('scheduled_date');
        $pickup_date = $this->request->getPost('pickup_date');
        $expected_delivery_date = $this->request->getPost('expected_delivery_date');
        $origin = $this->request->getPost('origin');
        $destination = $this->request->getPost('destination');
        $cargo_description = $this->request->getPost('cargo_description');
        $quantity = $this->request->getPost('quantity') ?: 0;
        $unit = $this->request->getPost('unit');
        $estimated_weight = $this->request->getPost('estimated_weight') ?: 0;
        $special_instructions = $this->request->getPost('special_instructions');
        $trip_status = $this->request->getPost('trip_status') ?: 'SCHEDULED';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_trips`
            SET 
                `customer_id` = ?, `booking_reference` = ?, `service_type` = ?,
                `trip_type` = ?, `priority` = ?, `scheduled_date` = ?,
                `pickup_date` = ?, `expected_delivery_date` = ?, `origin` = ?,
                `destination` = ?, `cargo_description` = ?, `quantity` = ?,
                `unit` = ?, `estimated_weight` = ?, `special_instructions` = ?,
                `trip_status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `trip_id` = ?
            ",
            [
                $customer_id, $booking_reference, $service_type,
                $trip_type, $priority, $scheduled_date,
                $pickup_date, $expected_delivery_date, $origin,
                $destination, $cargo_description, $quantity,
                $unit, $estimated_weight, $special_instructions,
                $trip_status, $remarks, $trip_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Trip Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    // ==============================
    // DELETE TRIP
    // ==============================
    public function deleteTrip()
    {
        $trip_id = $this->request->getPost('trip_id');

        // Delete assignment and waypoints first
        $this->db->query("DELETE FROM `tbl_trip_assignments` WHERE `trip_id` = ?", [$trip_id]);
        $this->db->query("DELETE FROM `tbl_trip_waypoints` WHERE `trip_id` = ?", [$trip_id]);

        $query = $this->db->query("DELETE FROM `tbl_trips` WHERE `trip_id` = ?", [$trip_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Trip Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }

    // ==============================
    // GET SINGLE TRIP
    // ==============================
    public function getTrip($trip_id)
    {
        $query = $this->db->query("
            SELECT t.*, c.customer_name
            FROM tbl_trips t
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            WHERE t.trip_id = ?
        ", [$trip_id]);
        return $query->getRowArray();
    }

    // ==============================
    // ASSIGNMENT METHODS
    // ==============================

    // ==============================
    // GET ASSIGNMENT BY TRIP
    // ==============================
    public function getAssignment($trip_id)
    {
        $query = $this->db->query("
            SELECT * FROM tbl_trip_assignments 
            WHERE trip_id = ? 
            LIMIT 1
        ", [$trip_id]);
        return $query->getRowArray();
    }

    // ==============================
    // SAVE ASSIGNMENT
    // ==============================
    public function saveAssignment()
    {
        $trip_id = $this->request->getPost('trip_id');
        $vehicle_type = $this->request->getPost('vehicle_type');
        $truck_plate = $this->request->getPost('truck_plate');
        $tractor_plate = $this->request->getPost('tractor_plate');
        $chassis_plate = $this->request->getPost('chassis_plate');
        $chassis_type = $this->request->getPost('chassis_type') ?: 'OWNED';
        $vendor_name = $this->request->getPost('vendor_name');
        $rental_rate = $this->request->getPost('rental_rate') ?: 0;
        $rental_start_date = $this->request->getPost('rental_start_date');
        $rental_end_date = $this->request->getPost('rental_end_date');
        $rental_agreement_no = $this->request->getPost('rental_agreement_no');
        $vendor_contact_person = $this->request->getPost('vendor_contact_person');
        $vendor_contact_number = $this->request->getPost('vendor_contact_number');
        $driver_name = $this->request->getPost('driver_name');
        $helper_name = $this->request->getPost('helper_name');
        $assignment_date = $this->request->getPost('assignment_date') ?: date('Y-m-d');
        $dispatch_time = $this->request->getPost('dispatch_time');
        $dispatch_location = $this->request->getPost('dispatch_location');
        $odometer_before_trip = $this->request->getPost('odometer_before_trip') ?: 0;
        $fuel_level = $this->request->getPost('fuel_level') ?: 0;
        $assignment_status = $this->request->getPost('assignment_status') ?: 'ASSIGNED';
        $remarks = $this->request->getPost('remarks');

        // For RENTED_ALL, set chassis_type to RENTED
        if($vehicle_type == 'RENTED_ALL') {
            $chassis_type = 'RENTED';
        }

        $query = $this->db->query("
            INSERT INTO `tbl_trip_assignments`(
                `trip_id`, `vehicle_type`, `truck_plate`, `tractor_plate`, `chassis_plate`,
                `chassis_type`, `vendor_name`, `rental_rate`, `rental_start_date`,
                `rental_end_date`, `rental_agreement_no`, `vendor_contact_person`,
                `vendor_contact_number`, `driver_name`, `helper_name`, `assignment_date`,
                `dispatch_time`, `dispatch_location`, `odometer_before_trip`,
                `fuel_level`, `assignment_status`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $trip_id, $vehicle_type, $truck_plate, $tractor_plate, $chassis_plate,
                $chassis_type, $vendor_name, $rental_rate, $rental_start_date,
                $rental_end_date, $rental_agreement_no, $vendor_contact_person,
                $vendor_contact_number, $driver_name, $helper_name, $assignment_date,
                $dispatch_time, $dispatch_location, $odometer_before_trip,
                $fuel_level, $assignment_status, $remarks, $this->cuser
            ]
        );

        if ($query) {
            $this->db->query("UPDATE tbl_trips SET has_assignment = 1, trip_status = 'ASSIGNED' WHERE trip_id = ?", [$trip_id]);
            return ['status' => 'success', 'message' => 'Assignment Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving assignment.'];
        }
    }

    // ==============================
    // UPDATE ASSIGNMENT
    // ==============================
    public function updateAssignment()
    {
        $assignment_id = $this->request->getPost('assignment_id');
        $trip_id = $this->request->getPost('trip_id');
        $vehicle_type = $this->request->getPost('vehicle_type');
        $truck_plate = $this->request->getPost('truck_plate');
        $tractor_plate = $this->request->getPost('tractor_plate');
        $chassis_plate = $this->request->getPost('chassis_plate');
        $chassis_type = $this->request->getPost('chassis_type') ?: 'OWNED';
        $vendor_name = $this->request->getPost('vendor_name');
        $rental_rate = $this->request->getPost('rental_rate') ?: 0;
        $rental_start_date = $this->request->getPost('rental_start_date');
        $rental_end_date = $this->request->getPost('rental_end_date');
        $rental_agreement_no = $this->request->getPost('rental_agreement_no');
        $vendor_contact_person = $this->request->getPost('vendor_contact_person');
        $vendor_contact_number = $this->request->getPost('vendor_contact_number');
        $driver_name = $this->request->getPost('driver_name');
        $helper_name = $this->request->getPost('helper_name');
        $assignment_date = $this->request->getPost('assignment_date') ?: date('Y-m-d');
        $dispatch_time = $this->request->getPost('dispatch_time');
        $dispatch_location = $this->request->getPost('dispatch_location');
        $odometer_before_trip = $this->request->getPost('odometer_before_trip') ?: 0;
        $fuel_level = $this->request->getPost('fuel_level') ?: 0;
        $assignment_status = $this->request->getPost('assignment_status') ?: 'ASSIGNED';
        $remarks = $this->request->getPost('remarks');

        // For RENTED_ALL, set chassis_type to RENTED
        if($vehicle_type == 'RENTED_ALL') {
            $chassis_type = 'RENTED';
        }

        $query = $this->db->query("
            UPDATE `tbl_trip_assignments`
            SET 
                `vehicle_type` = ?, `truck_plate` = ?, `tractor_plate` = ?, `chassis_plate` = ?,
                `chassis_type` = ?, `vendor_name` = ?, `rental_rate` = ?,
                `rental_start_date` = ?, `rental_end_date` = ?, `rental_agreement_no` = ?,
                `vendor_contact_person` = ?, `vendor_contact_number` = ?,
                `driver_name` = ?, `helper_name` = ?, `assignment_date` = ?,
                `dispatch_time` = ?, `dispatch_location` = ?, `odometer_before_trip` = ?,
                `fuel_level` = ?, `assignment_status` = ?, `remarks` = ?,
                `updated_at` = NOW()
            WHERE `assignment_id` = ?
            ",
            [
                $vehicle_type, $truck_plate, $tractor_plate, $chassis_plate,
                $chassis_type, $vendor_name, $rental_rate,
                $rental_start_date, $rental_end_date, $rental_agreement_no,
                $vendor_contact_person, $vendor_contact_number,
                $driver_name, $helper_name, $assignment_date,
                $dispatch_time, $dispatch_location, $odometer_before_trip,
                $fuel_level, $assignment_status, $remarks,
                $assignment_id
            ]
        );

        if ($query) {
            $this->db->query("UPDATE tbl_trips SET has_assignment = 1, trip_status = 'ASSIGNED' WHERE trip_id = ?", [$trip_id]);
            return ['status' => 'success', 'message' => 'Assignment Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating assignment.'];
        }
    }

    // ==============================
    // DELETE ASSIGNMENT
    // ==============================
    public function deleteAssignment()
    {
        $assignment_id = $this->request->getPost('assignment_id');
        $trip_id = $this->request->getPost('trip_id');

        $query = $this->db->query("DELETE FROM `tbl_trip_assignments` WHERE `assignment_id` = ?", [$assignment_id]);

        if ($query) {
            $this->db->query("UPDATE tbl_trips SET has_assignment = 0 WHERE trip_id = ?", [$trip_id]);
            return ['status' => 'success', 'message' => 'Assignment Removed Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while removing assignment.'];
        }
    }

    // ==============================
    // WAYPOINT METHODS
    // ==============================

    // ==============================
    // SAVE WAYPOINT
    // ==============================
    public function saveWaypoint()
    {
        $trip_id = $this->request->getPost('trip_id');
        $sequence = $this->request->getPost('sequence');
        $waypoint_type = $this->request->getPost('waypoint_type');
        $waypoint_name = $this->request->getPost('waypoint_name');
        $address = $this->request->getPost('address');
        $city = $this->request->getPost('city');
        $province = $this->request->getPost('province');
        $expected_arrival = $this->request->getPost('expected_arrival');
        $expected_departure = $this->request->getPost('expected_departure');
        $remarks = $this->request->getPost('remarks');

        if(!$waypoint_name) {
            return ['status' => 'error', 'message' => 'Please enter waypoint name!'];
        }

        if(empty($waypoint_type)) {
            return ['status' => 'error', 'message' => 'Please select a waypoint type!'];
        }

        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_trip_waypoints WHERE trip_id = ? AND sequence = ?", [$trip_id, $sequence])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Sequence number already exists! Please use a different number.'];
        }

        $expected_arrival = !empty($expected_arrival) ? $expected_arrival : NULL;
        $expected_departure = !empty($expected_departure) ? $expected_departure : NULL;

        $query = $this->db->query("
            INSERT INTO `tbl_trip_waypoints`(
                `trip_id`, `sequence`, `waypoint_type`, `waypoint_name`,
                `address`, `city`, `province`, `expected_arrival`,
                `expected_departure`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $trip_id, $sequence, $waypoint_type, $waypoint_name,
                $address, $city, $province, $expected_arrival,
                $expected_departure, $remarks, $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Waypoint Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving waypoint.'];
        }
    }

    // ==============================
    // UPDATE WAYPOINT
    // ==============================
    public function updateWaypoint()
    {
        $waypoint_id = $this->request->getPost('waypoint_id');
        $trip_id = $this->request->getPost('trip_id');
        $sequence = $this->request->getPost('sequence');
        $waypoint_type = $this->request->getPost('waypoint_type');
        $waypoint_name = $this->request->getPost('waypoint_name');
        $address = $this->request->getPost('address');
        $city = $this->request->getPost('city');
        $province = $this->request->getPost('province');
        $expected_arrival = $this->request->getPost('expected_arrival');
        $expected_departure = $this->request->getPost('expected_departure');
        $remarks = $this->request->getPost('remarks');

        if(empty($waypoint_type)) {
            return ['status' => 'error', 'message' => 'Please select a waypoint type!'];
        }

        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_trip_waypoints WHERE trip_id = ? AND sequence = ? AND waypoint_id != ?", [$trip_id, $sequence, $waypoint_id])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Sequence number already exists! Please use a different number.'];
        }

        $expected_arrival = !empty($expected_arrival) ? $expected_arrival : NULL;
        $expected_departure = !empty($expected_departure) ? $expected_departure : NULL;

        $query = $this->db->query("
            UPDATE `tbl_trip_waypoints`
            SET 
                `sequence` = ?,
                `waypoint_type` = ?,
                `waypoint_name` = ?,
                `address` = ?,
                `city` = ?,
                `province` = ?,
                `expected_arrival` = ?,
                `expected_departure` = ?,
                `remarks` = ?,
                `updated_at` = NOW()
            WHERE `waypoint_id` = ?
            ",
            [
                $sequence, $waypoint_type, $waypoint_name,
                $address, $city, $province, $expected_arrival,
                $expected_departure, $remarks, $waypoint_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Waypoint Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating waypoint.'];
        }
    }

    // ==============================
    // DELETE WAYPOINT
    // ==============================
    public function deleteWaypoint()
    {
        $waypoint_id = $this->request->getPost('waypoint_id');
        
        $query = $this->db->query("DELETE FROM `tbl_trip_waypoints` WHERE `waypoint_id` = ?", [$waypoint_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Waypoint Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting waypoint.'];
        }
    }

    // ==============================
    // GET WAYPOINTS BY TRIP
    // ==============================
    public function getWaypointsByTrip($trip_id)
    {
        $query = $this->db->query("
            SELECT * FROM tbl_trip_waypoints 
            WHERE trip_id = ? 
            ORDER BY sequence ASC
        ", [$trip_id]);
        
        return $query->getResultArray();
    }

    // ==============================
    // GET SINGLE WAYPOINT
    // ==============================
    public function getWaypoint($waypoint_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_trip_waypoints WHERE waypoint_id = ?", [$waypoint_id]);
        return $query->getRowArray();
    }
}