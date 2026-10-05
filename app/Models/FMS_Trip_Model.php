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
        $query = $this->db->query("SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(trip_code, '-', -1) AS UNSIGNED)), 0) as total FROM tbl_trips WHERE trip_code LIKE ?", ['%-' . $year . '-%']);
        $count = $query->getRow()->total + 1;
        $prefix = 'TRP-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GET AVAILABLE TRUCKS
    // ==============================
    public function getAvailableTrucks()
    {
        $trip_id = $this->request->getPost('trip_id');
        return $this->db->query("
            SELECT truck_id, plate_number, vehicle_config, vehicle_type
            FROM tbl_trucks
            WHERE vehicle_config = 'RIGID'
              AND (truck_status = 'AVAILABLE' OR truck_id = (SELECT truck_id FROM tbl_trip_assignments WHERE trip_id = ? LIMIT 1))
            ORDER BY plate_number
        ", [$trip_id])->getResultArray();
    }

    // ==============================
    // GET AVAILABLE TRACTORS
    // ==============================
    public function getAvailableTractors()
    {
        $trip_id = $this->request->getPost('trip_id');
        return $this->db->query("
            SELECT truck_id, plate_number, vehicle_config, vehicle_type
            FROM tbl_trucks
            WHERE vehicle_config = 'TRACTOR'
              AND (truck_status = 'AVAILABLE' OR truck_id = (SELECT tractor_id FROM tbl_trip_assignments WHERE trip_id = ? LIMIT 1))
            ORDER BY plate_number
        ", [$trip_id])->getResultArray();
    }

    // ==============================
    // GET AVAILABLE CHASSIS
    // ==============================
    public function getAvailableChassis()
    {
        $trip_id = $this->request->getPost('trip_id');
        return $this->db->query("
            SELECT truck_id, plate_number, vehicle_config, vehicle_type, body_type
            FROM tbl_trucks
            WHERE vehicle_config = 'TRAILER'
              AND (truck_status = 'AVAILABLE' OR truck_id = (SELECT chassis_id FROM tbl_trip_assignments WHERE trip_id = ? LIMIT 1))
            ORDER BY plate_number
        ", [$trip_id])->getResultArray();
    }

    // ==============================
    // GET AVAILABLE DRIVERS
    // ==============================
    public function getAvailableDrivers()
    {
        $trip_id = $this->request->getPost('trip_id');
        return $this->db->query("
            SELECT driver_id, driver_name
            FROM tbl_drivers
            WHERE driver_status = 'AVAILABLE' OR driver_id = (SELECT driver_id FROM tbl_trip_assignments WHERE trip_id = ? LIMIT 1)
            ORDER BY driver_name
        ", [$trip_id])->getResultArray();
    }

    // ==============================
    // GET AVAILABLE HELPERS
    // ==============================
    public function getAvailableHelpers()
    {
        $trip_id = $this->request->getPost('trip_id');
        return $this->db->query("
            SELECT helper_id, helper_name
            FROM tbl_helpers
            WHERE helper_status = 'AVAILABLE' OR helper_id = (SELECT helper_id FROM tbl_trip_assignments WHERE trip_id = ? LIMIT 1)
            ORDER BY helper_name
        ", [$trip_id])->getResultArray();
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

            // Cargo items added in the New Trip modal are staged client-side (no trip_id
            // exists yet to save them against) and sent together with the trip in one
            // request, so they land in the same "Save Trip" click instead of a separate
            // step afterward. Falls back to auto-creating a single item from the summary
            // fields above when none were explicitly staged (e.g. an older/simpler caller).
            $cargo_items_json = $this->request->getPost('cargo_items_json');
            $cargoItems = $cargo_items_json ? json_decode($cargo_items_json, true) : null;

            if (is_array($cargoItems) && count($cargoItems) > 0) {
                foreach ($cargoItems as $item) {
                    $item_description = trim($item['item_description'] ?? '');
                    if ($item_description === '') {
                        continue;
                    }
                    $this->db->query("
                        INSERT INTO `tbl_trip_cargo_items`(
                            `trip_id`, `item_description`, `quantity`, `unit`, `weight`, `remarks`, `created_by`
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?)",
                        [
                            $trip_id, $item_description,
                            $item['quantity'] ?? 0, $item['unit'] ?? '', $item['weight'] ?? 0,
                            $item['remarks'] ?? '', $this->cuser
                        ]
                    );
                }
            } elseif (!empty($cargo_description)) {
                $this->db->query("
                    INSERT INTO `tbl_trip_cargo_items`(
                        `trip_id`, `item_description`, `quantity`, `unit`, `weight`, `created_by`
                    )
                    VALUES (?, ?, ?, ?, ?, ?)",
                    [$trip_id, $cargo_description, $quantity, $unit, $estimated_weight, $this->cuser]
                );
            }

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

        // Dispatch/DR/billing hang off the trip — don't orphan them
        $dispatched = $this->db->query("SELECT COUNT(*) as total FROM tbl_dispatch WHERE trip_id = ?", [$trip_id])->getRow()->total;
        if ($dispatched > 0) {
            return ['status' => 'error', 'message' => 'Cannot delete a trip that has already been dispatched. Delete its dispatch first.'];
        }

        // Free the assigned truck/driver/helper before the assignment goes
        $old = $this->db->query("
            SELECT truck_id, tractor_id, chassis_id, driver_id, helper_id
            FROM tbl_trip_assignments WHERE trip_id = ?
        ", [$trip_id])->getRow();
        if ($old) {
            $this->setResourceStatus([$old->truck_id, $old->tractor_id, $old->chassis_id], $old->driver_id, $old->helper_id, 'AVAILABLE');
        }

        // Delete assignment, waypoints, and cargo items first
        $this->db->query("DELETE FROM `tbl_trip_assignments` WHERE `trip_id` = ?", [$trip_id]);
        $this->db->query("DELETE FROM `tbl_trip_waypoints` WHERE `trip_id` = ?", [$trip_id]);
        $this->db->query("DELETE FROM `tbl_trip_cargo_items` WHERE `trip_id` = ?", [$trip_id]);

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
            SELECT t.*, c.customer_name,
                   EXISTS(SELECT 1 FROM tbl_trip_assignments a WHERE a.trip_id = t.trip_id) AS has_assignment
            FROM tbl_trips t
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            WHERE t.trip_id = ?
        ", [$trip_id]);
        return $query->getRowArray();
    }

    // ==============================
    // ASSIGNMENT METHODS
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

    public function saveAssignment()
    {
        $trip_id = $this->request->getPost('trip_id');
        $vehicle_type = $this->request->getPost('vehicle_type');
        $truck_id = $this->request->getPost('truck_id') ?: null;
        $tractor_id = $this->request->getPost('tractor_id') ?: null;
        $chassis_id = $this->request->getPost('chassis_id') ?: null;
        $chassis_type = $this->request->getPost('chassis_type') ?: 'OWNED';
        $vendor_id = $this->request->getPost('vendor_id') ?: null;
        $rental_rate = $this->request->getPost('rental_rate') ?: 0;
        $rental_start_date = $this->request->getPost('rental_start_date');
        $rental_end_date = $this->request->getPost('rental_end_date');
        $rental_agreement_no = $this->request->getPost('rental_agreement_no');
        $vendor_contact_person = $this->request->getPost('vendor_contact_person');
        $vendor_contact_number = $this->request->getPost('vendor_contact_number');
        $driver_id = $this->request->getPost('driver_id') ?: null;
        $helper_id = $this->request->getPost('helper_id') ?: null;
        $assignment_date = $this->request->getPost('assignment_date') ?: date('Y-m-d');
        $dispatch_time = $this->request->getPost('dispatch_time');
        $dispatch_location = $this->request->getPost('dispatch_location');
        $odometer_before_trip = $this->request->getPost('odometer_before_trip') ?: 0;
        $fuel_level = $this->request->getPost('fuel_level') ?: 0;
        $remarks = $this->request->getPost('remarks');

        if($vehicle_type == 'RENTED_ALL') {
            $chassis_type = 'RENTED';
        }

        // Name/plate snapshots kept for history
        $snap = $this->getResourceSnapshot($truck_id, $tractor_id, $chassis_id, $vendor_id, $driver_id, $helper_id);

        $query = $this->db->query("
            INSERT INTO `tbl_trip_assignments`(
                `trip_id`, `vehicle_type`, `truck_id`, `truck_plate`, `tractor_id`, `tractor_plate`,
                `chassis_id`, `chassis_plate`, `chassis_type`, `vendor_id`, `vendor_name`, `rental_rate`,
                `rental_start_date`, `rental_end_date`, `rental_agreement_no`, `vendor_contact_person`,
                `vendor_contact_number`, `driver_id`, `driver_name`, `helper_id`, `helper_name`, `assignment_date`,
                `dispatch_time`, `dispatch_location`, `odometer_before_trip`,
                `fuel_level`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $trip_id, $vehicle_type, $truck_id, $snap['truck_plate'], $tractor_id, $snap['tractor_plate'],
                $chassis_id, $snap['chassis_plate'], $chassis_type, $vendor_id, $snap['vendor_name'], $rental_rate,
                $rental_start_date, $rental_end_date, $rental_agreement_no, $vendor_contact_person,
                $vendor_contact_number, $driver_id, $snap['driver_name'], $helper_id, $snap['helper_name'], $assignment_date,
                $dispatch_time, $dispatch_location, $odometer_before_trip,
                $fuel_level, $remarks, $this->cuser
            ]
        );

        if ($query) {
            // Only advance a trip that hasn't started yet — never rewind a dispatched trip
            $this->db->query("UPDATE tbl_trips SET trip_status = 'ASSIGNED' WHERE trip_id = ? AND trip_status IN ('DRAFT','SCHEDULED')", [$trip_id]);

            $this->setResourceStatus([$truck_id, $tractor_id, $chassis_id], $driver_id, $helper_id, 'ASSIGNED');

            return ['status' => 'success', 'message' => 'Assignment Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving assignment.'];
        }
    }

    public function updateAssignment()
    {
        $assignment_id = $this->request->getPost('assignment_id');
        $trip_id = $this->request->getPost('trip_id');
        $vehicle_type = $this->request->getPost('vehicle_type');
        $truck_id = $this->request->getPost('truck_id') ?: null;
        $tractor_id = $this->request->getPost('tractor_id') ?: null;
        $chassis_id = $this->request->getPost('chassis_id') ?: null;
        $chassis_type = $this->request->getPost('chassis_type') ?: 'OWNED';
        $vendor_id = $this->request->getPost('vendor_id') ?: null;
        $rental_rate = $this->request->getPost('rental_rate') ?: 0;
        $rental_start_date = $this->request->getPost('rental_start_date');
        $rental_end_date = $this->request->getPost('rental_end_date');
        $rental_agreement_no = $this->request->getPost('rental_agreement_no');
        $vendor_contact_person = $this->request->getPost('vendor_contact_person');
        $vendor_contact_number = $this->request->getPost('vendor_contact_number');
        $driver_id = $this->request->getPost('driver_id') ?: null;
        $helper_id = $this->request->getPost('helper_id') ?: null;
        $assignment_date = $this->request->getPost('assignment_date') ?: date('Y-m-d');
        $dispatch_time = $this->request->getPost('dispatch_time');
        $dispatch_location = $this->request->getPost('dispatch_location');
        $odometer_before_trip = $this->request->getPost('odometer_before_trip') ?: 0;
        $fuel_level = $this->request->getPost('fuel_level') ?: 0;
        $remarks = $this->request->getPost('remarks');

        if($vehicle_type == 'RENTED_ALL') {
            $chassis_type = 'RENTED';
        }

        $old = $this->db->query("
            SELECT truck_id, tractor_id, chassis_id, driver_id, helper_id
            FROM tbl_trip_assignments WHERE assignment_id = ?
        ", [$assignment_id])->getRow();

        $snap = $this->getResourceSnapshot($truck_id, $tractor_id, $chassis_id, $vendor_id, $driver_id, $helper_id);

        $query = $this->db->query("
            UPDATE `tbl_trip_assignments`
            SET
                `vehicle_type` = ?, `truck_id` = ?, `truck_plate` = ?, `tractor_id` = ?, `tractor_plate` = ?,
                `chassis_id` = ?, `chassis_plate` = ?, `chassis_type` = ?, `vendor_id` = ?, `vendor_name` = ?,
                `rental_rate` = ?, `rental_start_date` = ?, `rental_end_date` = ?, `rental_agreement_no` = ?,
                `vendor_contact_person` = ?, `vendor_contact_number` = ?,
                `driver_id` = ?, `driver_name` = ?, `helper_id` = ?, `helper_name` = ?, `assignment_date` = ?,
                `dispatch_time` = ?, `dispatch_location` = ?, `odometer_before_trip` = ?,
                `fuel_level` = ?, `remarks` = ?,
                `updated_at` = NOW()
            WHERE `assignment_id` = ?
            ",
            [
                $vehicle_type, $truck_id, $snap['truck_plate'], $tractor_id, $snap['tractor_plate'],
                $chassis_id, $snap['chassis_plate'], $chassis_type, $vendor_id, $snap['vendor_name'],
                $rental_rate, $rental_start_date, $rental_end_date, $rental_agreement_no,
                $vendor_contact_person, $vendor_contact_number,
                $driver_id, $snap['driver_name'], $helper_id, $snap['helper_name'], $assignment_date,
                $dispatch_time, $dispatch_location, $odometer_before_trip,
                $fuel_level, $remarks,
                $assignment_id
            ]
        );

        if ($query) {
            $this->db->query("UPDATE tbl_trips SET trip_status = 'ASSIGNED' WHERE trip_id = ? AND trip_status IN ('DRAFT','SCHEDULED')", [$trip_id]);

            // Release resources swapped out during this update
            if ($old) {
                $this->setResourceStatus(
                    array_diff(array_filter([$old->truck_id, $old->tractor_id, $old->chassis_id]), [$truck_id, $tractor_id, $chassis_id]),
                    $old->driver_id != $driver_id ? $old->driver_id : null,
                    $old->helper_id != $helper_id ? $old->helper_id : null,
                    'AVAILABLE'
                );
            }

            // Mark newly/still assigned resources
            $this->setResourceStatus([$truck_id, $tractor_id, $chassis_id], $driver_id, $helper_id, 'ASSIGNED');

            return ['status' => 'success', 'message' => 'Assignment Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating assignment.'];
        }
    }

    public function deleteAssignment()
    {
        $assignment_id = $this->request->getPost('assignment_id');
        $trip_id = $this->request->getPost('trip_id');

        $old = $this->db->query("
            SELECT truck_id, tractor_id, chassis_id, driver_id, helper_id
            FROM tbl_trip_assignments WHERE assignment_id = ?
        ", [$assignment_id])->getRow();

        $query = $this->db->query("DELETE FROM `tbl_trip_assignments` WHERE `assignment_id` = ?", [$assignment_id]);

        if ($query) {
            // Free the resources and step the trip back to SCHEDULED
            if ($old) {
                $this->setResourceStatus([$old->truck_id, $old->tractor_id, $old->chassis_id], $old->driver_id, $old->helper_id, 'AVAILABLE');
            }
            $this->db->query("UPDATE tbl_trips SET trip_status = 'SCHEDULED' WHERE trip_id = ? AND trip_status = 'ASSIGNED'", [$trip_id]);
            return ['status' => 'success', 'message' => 'Assignment Removed Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while removing assignment.'];
        }
    }

    // ==============================
    // RESOURCE SNAPSHOT: plate/name text stored alongside the IDs
    // ==============================
    private function getResourceSnapshot($truck_id, $tractor_id, $chassis_id, $vendor_id, $driver_id, $helper_id)
    {
        $plate = function($id) {
            return $id ? ($this->db->query("SELECT plate_number FROM tbl_trucks WHERE truck_id = ?", [$id])->getRow()->plate_number ?? null) : null;
        };

        return [
            'truck_plate'   => $plate($truck_id),
            'tractor_plate' => $plate($tractor_id),
            'chassis_plate' => $plate($chassis_id),
            'vendor_name'   => $vendor_id ? ($this->db->query("SELECT vendor_name FROM tbl_vendors WHERE vendor_id = ?", [$vendor_id])->getRow()->vendor_name ?? null) : null,
            'driver_name'   => $driver_id ? ($this->db->query("SELECT driver_name FROM tbl_drivers WHERE driver_id = ?", [$driver_id])->getRow()->driver_name ?? null) : null,
            'helper_name'   => $helper_id ? ($this->db->query("SELECT helper_name FROM tbl_helpers WHERE helper_id = ?", [$helper_id])->getRow()->helper_name ?? null) : null,
        ];
    }

    // ==============================
    // SET RESOURCE STATUS: trucks + driver + helper by ID
    // ==============================
    private function setResourceStatus($truck_ids, $driver_id, $helper_id, $status)
    {
        foreach (array_filter($truck_ids) as $id) {
            $this->db->query("UPDATE tbl_trucks SET truck_status = ? WHERE truck_id = ?", [$status, $id]);
        }
        if (!empty($driver_id)) {
            $this->db->query("UPDATE tbl_drivers SET driver_status = ? WHERE driver_id = ?", [$status, $driver_id]);
        }
        if (!empty($helper_id)) {
            $this->db->query("UPDATE tbl_helpers SET helper_status = ? WHERE helper_id = ?", [$status, $helper_id]);
        }
    }

    // ==============================
    // WAYPOINT METHODS
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

    public function getWaypointsByTrip($trip_id)
    {
        $query = $this->db->query("
            SELECT * FROM tbl_trip_waypoints 
            WHERE trip_id = ? 
            ORDER BY sequence ASC
        ", [$trip_id]);
        
        return $query->getResultArray();
    }

    public function getWaypoint($waypoint_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_trip_waypoints WHERE waypoint_id = ?", [$waypoint_id]);
        return $query->getRowArray();
    }

    // ==============================
    // CARGO ITEM METHODS
    // ==============================
    public function getCargoItems($trip_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_trip_cargo_items
            WHERE trip_id = ?
            ORDER BY item_id ASC
        ", [$trip_id])->getResultArray();
    }

    public function getCargoItem($item_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_trip_cargo_items WHERE item_id = ?", [$item_id]);
        return $query->getRowArray();
    }

    public function saveCargoItem()
    {
        $trip_id = $this->request->getPost('trip_id');
        $item_description = $this->request->getPost('item_description');
        $quantity = $this->request->getPost('quantity') ?: 0;
        $unit = $this->request->getPost('unit');
        $weight = $this->request->getPost('weight') ?: 0;
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_trip_cargo_items`(
                `trip_id`, `item_description`, `quantity`, `unit`, `weight`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$trip_id, $item_description, $quantity, $unit, $weight, $remarks, $this->cuser]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Cargo Item Added Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding cargo item.'];
        }
    }

    public function updateCargoItem()
    {
        $item_id = $this->request->getPost('item_id');
        $item_description = $this->request->getPost('item_description');
        $quantity = $this->request->getPost('quantity') ?: 0;
        $unit = $this->request->getPost('unit');
        $weight = $this->request->getPost('weight') ?: 0;
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_trip_cargo_items`
            SET 
                `item_description` = ?, `quantity` = ?, `unit` = ?, `weight` = ?, 
                `remarks` = ?, `updated_at` = NOW()
            WHERE `item_id` = ?
            ",
            [$item_description, $quantity, $unit, $weight, $remarks, $item_id]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Cargo Item Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating cargo item.'];
        }
    }

    public function deleteCargoItem()
    {
        $item_id = $this->request->getPost('item_id');
        $query = $this->db->query("DELETE FROM `tbl_trip_cargo_items` WHERE `item_id` = ?", [$item_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Cargo Item Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting cargo item.'];
        }
    }
}