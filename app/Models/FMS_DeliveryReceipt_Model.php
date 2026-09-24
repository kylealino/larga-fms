<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_DeliveryReceipt_Model extends Model
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
    // GENERATE DR CODE
    // ==============================
    private function generateDRCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_delivery_receipts WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'DR-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GET ALL DELIVERY RECEIPTS (latest POD only)
    // ==============================
    public function getAllDRs()
    {
        return $this->db->query("
            SELECT d.*,
                   t.trip_code,
                   c.customer_name,
                   dp.dispatch_code,
                   pod.received_by,
                   pod.date_received,
                   pod.delivery_condition
            FROM tbl_delivery_receipts d
            LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON d.customer_id = c.customer_id
            LEFT JOIN tbl_dispatch dp ON d.dispatch_id = dp.dispatch_id
            LEFT JOIN tbl_delivery_receipt_pod pod ON pod.pod_id = (
                SELECT MAX(p2.pod_id) FROM tbl_delivery_receipt_pod p2 WHERE p2.dr_id = d.dr_id
            )
            ORDER BY d.dr_date DESC, d.dr_id DESC
        ")->getResultArray();
    }

    // ==============================
    // GET DISPATCHED TRIPS WITHOUT EXISTING DR
    // ==============================
    public function getDispatchedTripsForDR()
    {
        return $this->db->query("
            SELECT t.trip_id,
                   t.trip_code,
                   t.customer_id,
                   t.destination,
                   c.customer_name,
                   d.dispatch_id,
                   d.dispatch_date,
                   d.dispatch_status,
                   d.truck,
                   d.driver,
                   d.helper,
                   d.origin AS dispatch_origin,
                   d.destination AS dispatch_destination,
                   d.container_required,
                   d.container_number,
                   d.container_type,
                   d.container_reference
            FROM tbl_trips t
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_dispatch d ON t.trip_id = d.trip_id
            WHERE d.dispatch_id IS NOT NULL
              AND t.trip_status IN ('DISPATCHED','IN_TRANSIT','DELIVERED','COMPLETED')
              AND NOT EXISTS (
                  SELECT 1 FROM tbl_delivery_receipts dr WHERE dr.dispatch_id = d.dispatch_id
              )
            ORDER BY d.dispatch_date DESC
        ")->getResultArray();
    }

    // ==============================
    // GET TRIP + DISPATCH INFO FOR NEW DR
    // ==============================
    public function getTripForDR($trip_id)
    {
        $query = $this->db->query("
            SELECT t.trip_id,
                   t.trip_code,
                   t.customer_id,
                   t.origin,
                   t.destination,
                   c.customer_name,
                   d.dispatch_id,
                   d.truck,
                   d.driver,
                   d.helper,
                   d.container_required,
                   d.container_number,
                   d.container_type,
                   d.container_reference
            FROM tbl_trips t
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_dispatch d ON t.trip_id = d.trip_id
            WHERE t.trip_id = ?
            LIMIT 1
        ", [$trip_id]);
        return $query->getRowArray();
    }

    // ==============================
    // GET TRIP CARGO ITEMS (for DR preview)
    // ==============================
    public function getTripCargoItems($trip_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_trip_cargo_items
            WHERE trip_id = ?
            ORDER BY item_id ASC
        ", [$trip_id])->getResultArray();
    }

    // ==============================
    // CREATE DR FROM TRIP/DISPATCH
    // ==============================
    public function createDRFromTrip()
    {
        $trip_id = $this->request->getPost('trip_id');
        $dispatch_id = $this->request->getPost('dispatch_id');

        if(!$trip_id || !$dispatch_id) {
            return ['status' => 'error', 'message' => 'Missing trip or dispatch reference.'];
        }

        $existing = $this->db->query("
            SELECT dr_id FROM tbl_delivery_receipts WHERE dispatch_id = ? LIMIT 1
        ", [$dispatch_id])->getRow();
        if($existing) {
            return ['status' => 'error', 'message' => 'A Delivery Receipt already exists for this dispatch.'];
        }

        $info = $this->db->query("
            SELECT t.trip_id, t.customer_id, t.origin, t.destination,
                   d.dispatch_id, d.truck, d.driver, d.helper,
                   d.container_required, d.container_number, d.container_type, d.container_reference
            FROM tbl_trips t
            LEFT JOIN tbl_dispatch d ON t.trip_id = d.trip_id
            WHERE t.trip_id = ? AND d.dispatch_id = ?
            LIMIT 1
        ", [$trip_id, $dispatch_id])->getRow();

        if(!$info) {
            return ['status' => 'error', 'message' => 'Trip or dispatch not found.'];
        }

        $dr_code = $this->generateDRCode();

        $query = $this->db->query("
            INSERT INTO `tbl_delivery_receipts`(
                `dr_code`, `trip_id`, `dispatch_id`, `customer_id`, `dr_date`, `dr_time`,
                `truck`, `driver`, `helper`, `origin`, `destination`,
                `container_required`, `container_number`, `container_type`, `container_reference`,
                `dr_status`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $dr_code,
            $info->trip_id,
            $info->dispatch_id,
            $info->customer_id,
            date('Y-m-d'),
            date('H:i:s'),
            $info->truck,
            $info->driver,
            $info->helper,
            $info->origin,
            $info->destination,
            $info->container_required ?: 0,
            $info->container_number,
            $info->container_type,
            $info->container_reference,
            'PENDING',
            'Created from dispatch ' . $dispatch_id,
            $this->cuser
        ]);

        if ($query) {
            $dr_id = $this->db->insertID();

            // Auto-copy cargo items from trip
            $cargoItems = $this->db->query("
                SELECT * FROM tbl_trip_cargo_items
                WHERE trip_id = ?
                ORDER BY item_id ASC
            ", [$trip_id])->getResultArray();

            foreach ($cargoItems as $ci) {
                $this->db->query("
                    INSERT INTO `tbl_delivery_receipt_items`(
                        `dr_id`, `item_description`, `quantity_dispatched`, `quantity_delivered`,
                        `quantity_shortage`, `quantity_damaged`, `unit`, `weight`,
                        `condition_on_arrival`, `remarks`, `created_by`
                    )
                    VALUES (?, ?, ?, ?, 0, 0, ?, ?, 'GOOD', ?, ?)
                ", [
                    $dr_id,
                    $ci['item_description'],
                    $ci['quantity'],
                    $ci['quantity'],
                    $ci['unit'],
                    $ci['weight'],
                    $ci['remarks'],
                    $this->cuser
                ]);
            }

            return ['status' => 'success', 'message' => 'Delivery Receipt Created Successfully!', 'dr_id' => $dr_id];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while creating delivery receipt.'];
        }
    }

    // ==============================
    // GET DR BY ID
    // ==============================
    public function getDR($dr_id)
    {
        $query = $this->db->query("
            SELECT d.*,
                   t.trip_code,
                   t.origin AS trip_origin,
                   t.destination AS trip_destination,
                   t.cargo_description,
                   t.quantity AS trip_quantity,
                   t.unit AS trip_unit,
                   t.estimated_weight,
                   c.customer_name,
                   dp.dispatch_code,
                   dp.dispatch_date,
                   dp.truck AS dispatch_truck,
                   dp.driver AS dispatch_driver,
                   dp.helper AS dispatch_helper,
                   dp.container_required AS dispatch_container_required,
                   dp.container_number AS dispatch_container_number,
                   dp.container_type AS dispatch_container_type,
                   dp.container_reference AS dispatch_container_reference
            FROM tbl_delivery_receipts d
            LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON d.customer_id = c.customer_id
            LEFT JOIN tbl_dispatch dp ON d.dispatch_id = dp.dispatch_id
            WHERE d.dr_id = ?
        ", [$dr_id]);
        return $query->getRowArray();
    }

    // ==============================
    // GET DR BY TRIP
    // ==============================
    public function getDRByTrip($trip_id)
    {
        $query = $this->db->query("
            SELECT d.*,
                   t.trip_code,
                   t.origin AS trip_origin,
                   t.destination AS trip_destination,
                   t.cargo_description,
                   t.quantity AS trip_quantity,
                   t.unit AS trip_unit,
                   t.estimated_weight,
                   c.customer_name,
                   dp.dispatch_code,
                   dp.dispatch_date,
                   dp.truck AS dispatch_truck,
                   dp.driver AS dispatch_driver,
                   dp.helper AS dispatch_helper,
                   dp.container_required AS dispatch_container_required,
                   dp.container_number AS dispatch_container_number,
                   dp.container_type AS dispatch_container_type,
                   dp.container_reference AS dispatch_container_reference
            FROM tbl_delivery_receipts d
            LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON d.customer_id = c.customer_id
            LEFT JOIN tbl_dispatch dp ON d.dispatch_id = dp.dispatch_id
            WHERE d.trip_id = ?
            LIMIT 1
        ", [$trip_id]);
        return $query->getRowArray();
    }

    // ==============================
    // SAVE DR
    // ==============================
    public function saveDR()
    {
        $dr_code = $this->generateDRCode();
        $trip_id = $this->request->getPost('trip_id');
        $dispatch_id = $this->request->getPost('dispatch_id') ?: null;
        $customer_id = $this->request->getPost('customer_id');
        $dr_date = $this->request->getPost('dr_date') ?: date('Y-m-d');
        $dr_time = $this->request->getPost('dr_time');
        $truck = $this->request->getPost('truck');
        $driver = $this->request->getPost('driver');
        $helper = $this->request->getPost('helper');
        $origin = $this->request->getPost('origin');
        $destination = $this->request->getPost('destination');
        $container_required = $this->request->getPost('container_required') ?: 0;
        $container_number = $this->request->getPost('container_number');
        $container_type = $this->request->getPost('container_type');
        $container_reference = $this->request->getPost('container_reference');
        $dr_status = $this->request->getPost('dr_status') ?: 'PENDING';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_delivery_receipts`(
                `dr_code`, `trip_id`, `dispatch_id`, `customer_id`, `dr_date`, `dr_time`,
                `truck`, `driver`, `helper`, `origin`, `destination`,
                `container_required`, `container_number`, `container_type`, `container_reference`,
                `dr_status`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $dr_code, $trip_id, $dispatch_id, $customer_id, $dr_date, $dr_time,
                $truck, $driver, $helper, $origin, $destination,
                $container_required, $container_number, $container_type, $container_reference,
                $dr_status, $remarks, $this->cuser
            ]
        );

        if ($query) {
            $dr_id = $this->db->insertID();

            $submitted_items = $this->request->getPost('dr_items');
            $has_submitted_items = false;

            if (!empty($submitted_items) && is_array($submitted_items)) {
                $has_submitted_items = true;

                foreach ($submitted_items as $item) {
                    $this->db->query("
                        INSERT INTO `tbl_delivery_receipt_items`(
                            `dr_id`, `item_description`, `quantity_dispatched`, `quantity_delivered`,
                            `quantity_shortage`, `quantity_damaged`, `unit`, `weight`,
                            `condition_on_arrival`, `remarks`, `created_by`
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ", [
                        $dr_id,
                        $item['item_description'] ?? '',
                        $item['quantity_dispatched'] ?? 0,
                        $item['quantity_delivered'] ?? 0,
                        $item['quantity_shortage'] ?? 0,
                        $item['quantity_damaged'] ?? 0,
                        $item['unit'] ?? '',
                        $item['weight'] ?? 0,
                        $item['condition_on_arrival'] ?? 'GOOD',
                        $item['remarks'] ?? '',
                        $this->cuser
                    ]);
                }
            }

            if (!$has_submitted_items && !empty($trip_id)) {
                $cargoItems = $this->db->query("
                    SELECT * FROM tbl_trip_cargo_items
                    WHERE trip_id = ?
                    ORDER BY item_id ASC
                ", [$trip_id])->getResultArray();

                foreach ($cargoItems as $ci) {
                    $this->db->query("
                        INSERT INTO `tbl_delivery_receipt_items`(
                            `dr_id`, `item_description`, `quantity_dispatched`, `quantity_delivered`,
                            `quantity_shortage`, `quantity_damaged`, `unit`, `weight`,
                            `condition_on_arrival`, `remarks`, `created_by`
                        )
                        VALUES (?, ?, ?, ?, 0, 0, ?, ?, 'GOOD', ?, ?)
                    ", [
                        $dr_id,
                        $ci['item_description'],
                        $ci['quantity'],
                        $ci['quantity'],
                        $ci['unit'],
                        $ci['weight'],
                        $ci['remarks'],
                        $this->cuser
                    ]);
                }
            }

            return ['status' => 'success', 'message' => 'Delivery Receipt Saved Successfully!', 'dr_id' => $dr_id];
        } else {
            $error = $this->db->error();
            log_message('error', 'DR Save Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while saving delivery receipt.'];
        }
    }

    // ==============================
    // UPDATE DR
    // ==============================
    public function updateDR()
    {
        $dr_id = $this->request->getPost('dr_id');
        $dr_date = $this->request->getPost('dr_date');
        $dr_time = $this->request->getPost('dr_time');
        $truck = $this->request->getPost('truck');
        $driver = $this->request->getPost('driver');
        $helper = $this->request->getPost('helper');
        $origin = $this->request->getPost('origin');
        $destination = $this->request->getPost('destination');
        $container_required = $this->request->getPost('container_required') ?: 0;
        $container_number = $this->request->getPost('container_number');
        $container_type = $this->request->getPost('container_type');
        $container_reference = $this->request->getPost('container_reference');
        $dr_status = $this->request->getPost('dr_status');
        $remarks = $this->request->getPost('remarks');

        if ($dr_status === 'DELIVERED') {
            $hasShortfall = $this->db->query("
                SELECT COUNT(*) as cnt FROM tbl_delivery_receipt_items
                WHERE dr_id = ? AND (quantity_shortage > 0 OR quantity_damaged > 0)
            ", [$dr_id])->getRow()->cnt;
            if ($hasShortfall > 0) {
                $dr_status = 'PARTIALLY_DELIVERED';
            }
        }

        $query = $this->db->query("
            UPDATE `tbl_delivery_receipts`
            SET 
                `dr_date` = ?, `dr_time` = ?,
                `truck` = ?, `driver` = ?, `helper` = ?,
                `origin` = ?, `destination` = ?,
                `container_required` = ?, `container_number` = ?,
                `container_type` = ?, `container_reference` = ?,
                `dr_status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `dr_id` = ?
            ",
            [
                $dr_date, $dr_time,
                $truck, $driver, $helper,
                $origin, $destination,
                $container_required, $container_number,
                $container_type, $container_reference,
                $dr_status, $remarks,
                $dr_id
            ]
        );

        if ($query) {
            // Cargo delivered — mark dispatch/trip as DELIVERED. Actual COMPLETED (and
            // freeing the truck/driver/helper) happens once the crew is back, via the
            // last-waypoint-arrival flow in FMS_Dispatch_Model, so a tractor still en
            // route to return a container isn't marked done early.
            if ($dr_status === 'DELIVERED' || $dr_status === 'PARTIALLY_DELIVERED') {
                // dispatch/trip status track the truck's journey (which completed either way);
                // tbl_dispatch.dispatch_status and tbl_trips.trip_status have no PARTIALLY_DELIVERED
                // value of their own — the shortage/damage outcome lives on the DR record itself.
                $dr = $this->db->query("SELECT dispatch_id, trip_id FROM tbl_delivery_receipts WHERE dr_id = ?", [$dr_id])->getRow();
                if ($dr && $dr->dispatch_id) {
                    $this->db->query("
                        UPDATE tbl_dispatch
                        SET dispatch_status = 'DELIVERED', actual_delivery_date = ?, actual_delivery_time = ?, updated_at = NOW()
                        WHERE dispatch_id = ?
                    ", [$dr_date, $dr_time, $dr->dispatch_id]);

                    $this->db->query("UPDATE tbl_trips SET trip_status = 'DELIVERED' WHERE trip_id = ?", [$dr->trip_id]);
                }
            }

            $message = $dr_status === 'PARTIALLY_DELIVERED'
                ? 'Delivery Receipt Updated — marked as Partially Delivered due to recorded shortage/damage.'
                : 'Delivery Receipt Updated Successfully!';

            return ['status' => 'success', 'message' => $message];
        } else {
            $error = $this->db->error();
            log_message('error', 'DR Update Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while updating delivery receipt.'];
        }
    }

    // ==============================
    // DELETE DR
    // ==============================
    public function deleteDR()
    {
        $dr_id = $this->request->getPost('dr_id');

        $this->db->query("DELETE FROM `tbl_delivery_receipt_items` WHERE `dr_id` = ?", [$dr_id]);
        $this->db->query("DELETE FROM `tbl_delivery_receipt_pod` WHERE `dr_id` = ?", [$dr_id]);

        $query = $this->db->query("DELETE FROM `tbl_delivery_receipts` WHERE `dr_id` = ?", [$dr_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Delivery Receipt Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting delivery receipt.'];
        }
    }

    // ==============================
    // DR ITEMS METHODS
    // ==============================
    public function getDRItems($dr_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_delivery_receipt_items 
            WHERE dr_id = ?
            ORDER BY item_id ASC
        ", [$dr_id])->getResultArray();
    }

    public function getDRItem($item_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_delivery_receipt_items WHERE item_id = ?", [$item_id]);
        return $query->getRowArray();
    }

    public function saveDRItem()
    {
        $dr_id = $this->request->getPost('dr_id');
        $item_description = $this->request->getPost('item_description');
        $quantity_dispatched = $this->request->getPost('quantity_dispatched') ?: 0;
        $quantity_delivered = $this->request->getPost('quantity_delivered') ?: 0;
        $quantity_shortage = $this->request->getPost('quantity_shortage') ?: 0;
        $quantity_damaged = $this->request->getPost('quantity_damaged') ?: 0;
        $unit = $this->request->getPost('unit');
        $weight = $this->request->getPost('weight') ?: 0;
        $condition_on_arrival = $this->request->getPost('condition_on_arrival') ?: 'GOOD';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_delivery_receipt_items`(
                `dr_id`, `item_description`, `quantity_dispatched`, `quantity_delivered`,
                `quantity_shortage`, `quantity_damaged`, `unit`, `weight`,
                `condition_on_arrival`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $dr_id, $item_description, $quantity_dispatched, $quantity_delivered,
                $quantity_shortage, $quantity_damaged, $unit, $weight,
                $condition_on_arrival, $remarks, $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'DR Item Added Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding DR item.'];
        }
    }

    public function updateDRItem()
    {
        $item_id = $this->request->getPost('item_id');
        $item_description = $this->request->getPost('item_description');
        $quantity_dispatched = $this->request->getPost('quantity_dispatched') ?: 0;
        $quantity_delivered = $this->request->getPost('quantity_delivered') ?: 0;
        $quantity_shortage = $this->request->getPost('quantity_shortage') ?: 0;
        $quantity_damaged = $this->request->getPost('quantity_damaged') ?: 0;
        $unit = $this->request->getPost('unit');
        $weight = $this->request->getPost('weight') ?: 0;
        $condition_on_arrival = $this->request->getPost('condition_on_arrival') ?: 'GOOD';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_delivery_receipt_items`
            SET 
                `item_description` = ?, `quantity_dispatched` = ?, `quantity_delivered` = ?,
                `quantity_shortage` = ?, `quantity_damaged` = ?, `unit` = ?, `weight` = ?,
                `condition_on_arrival` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `item_id` = ?
            ",
            [
                $item_description, $quantity_dispatched, $quantity_delivered,
                $quantity_shortage, $quantity_damaged, $unit, $weight,
                $condition_on_arrival, $remarks, $item_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'DR Item Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating DR item.'];
        }
    }

    public function deleteDRItem()
    {
        $item_id = $this->request->getPost('item_id');
        
        $query = $this->db->query("DELETE FROM `tbl_delivery_receipt_items` WHERE `item_id` = ?", [$item_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'DR Item Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting DR item.'];
        }
    }

    // ==============================
    // POD METHODS
    // ==============================
    public function getPOD($dr_id)
    {
        // Fetch the LATEST POD row for this DR
        $query = $this->db->query("
            SELECT * FROM tbl_delivery_receipt_pod 
            WHERE dr_id = ? 
            ORDER BY pod_id DESC
            LIMIT 1
        ", [$dr_id]);
        return $query->getRowArray();
    }

    // ==============================
    // SAVE POD (UPDATE if exists, INSERT if not)
    // ==============================
    public function savePOD()
    {
        $dr_id = $this->request->getPost('dr_id');
        $received_by = $this->request->getPost('received_by');
        $received_by_position = $this->request->getPost('received_by_position');
        $date_received = $this->request->getPost('date_received');
        $time_received = $this->request->getPost('time_received');
        $quantity_received = $this->request->getPost('quantity_received');
        $delivery_condition = $this->request->getPost('delivery_condition') ?: 'GOOD';
        $customer_signature = $this->request->getPost('customer_signature');
        $delivery_photo = $this->request->getPost('delivery_photo');
        $signed_dr = $this->request->getPost('signed_dr');
        $supporting_documents = $this->request->getPost('supporting_documents');
        $remarks = $this->request->getPost('remarks');

        // Check if a POD row already exists for this DR
        $existing = $this->db->query("
            SELECT pod_id FROM tbl_delivery_receipt_pod 
            WHERE dr_id = ? 
            ORDER BY pod_id DESC 
            LIMIT 1
        ", [$dr_id])->getRow();

        if ($existing) {
            // UPDATE the existing row
            $query = $this->db->query("
                UPDATE `tbl_delivery_receipt_pod`
                SET 
                    `received_by` = ?, `received_by_position` = ?, `date_received` = ?,
                    `time_received` = ?, `quantity_received` = ?, `delivery_condition` = ?,
                    `customer_signature` = COALESCE(NULLIF(?, ''), `customer_signature`),
                    `delivery_photo` = COALESCE(NULLIF(?, ''), `delivery_photo`),
                    `signed_dr` = COALESCE(NULLIF(?, ''), `signed_dr`),
                    `supporting_documents` = COALESCE(NULLIF(?, ''), `supporting_documents`),
                    `remarks` = ?, `updated_at` = NOW()
                WHERE `pod_id` = ?
                ",
                [
                    $received_by, $received_by_position, $date_received,
                    $time_received, $quantity_received, $delivery_condition,
                    $customer_signature, $delivery_photo, $signed_dr,
                    $supporting_documents, $remarks, $existing->pod_id
                ]
            );

            if ($query) {
                return ['status' => 'success', 'message' => 'Proof of Delivery Saved Successfully!'];
            } else {
                $error = $this->db->error();
                log_message('error', 'POD Update Error: ' . print_r($error, true));
                return ['status' => 'error', 'message' => 'An error occurred while saving proof of delivery.'];
            }
        } else {
            // INSERT a new row
            $query = $this->db->query("
                INSERT INTO `tbl_delivery_receipt_pod`(
                    `dr_id`, `received_by`, `received_by_position`, `date_received`,
                    `time_received`, `quantity_received`, `delivery_condition`,
                    `customer_signature`, `delivery_photo`, `signed_dr`,
                    `supporting_documents`, `remarks`, `created_by`
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $dr_id, $received_by, $received_by_position, $date_received,
                    $time_received, $quantity_received, $delivery_condition,
                    $customer_signature, $delivery_photo, $signed_dr,
                    $supporting_documents, $remarks, $this->cuser
                ]
            );

            if ($query) {
                return ['status' => 'success', 'message' => 'Proof of Delivery Saved Successfully!'];
            } else {
                $error = $this->db->error();
                log_message('error', 'POD Save Error: ' . print_r($error, true));
                return ['status' => 'error', 'message' => 'An error occurred while saving proof of delivery.'];
            }
        }
    }

    // ==============================
    // UPDATE POD (kept for backward compatibility)
    // ==============================
    public function updatePOD()
    {
        // Delegate to savePOD — it now handles both update and insert
        return $this->savePOD();
    }

    // ==============================
    // UPLOAD POD FILE
    // ==============================
    public function uploadPODFile()
    {
        $dr_id = $this->request->getPost('dr_id');
        $file_type = $this->request->getPost('file_type');

        if (!$dr_id || !$file_type) {
            return ['status' => 'error', 'message' => 'Missing required parameters.'];
        }

        $allowed = ['customer_signature', 'delivery_photo', 'signed_dr', 'supporting_documents'];
        if (!in_array($file_type, $allowed)) {
            return ['status' => 'error', 'message' => 'Invalid file type.'];
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return ['status' => 'error', 'message' => 'No valid file uploaded.'];
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, $allowedExt)) {
            return ['status' => 'error', 'message' => 'File type not allowed. Only JPG, PNG, GIF, WEBP, PDF.'];
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'File too large. Max 5MB.'];
        }

        $newName = 'DR_' . $dr_id . '_' . strtoupper($file_type) . '_' . time() . '.' . $ext;
        $uploadPath = FCPATH . 'uploads/delivery_receipts/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($file->move($uploadPath, $newName)) {
            $relativePath = 'uploads/delivery_receipts/' . $newName;

            $existing = $this->db->query("
                SELECT pod_id FROM tbl_delivery_receipt_pod 
                WHERE dr_id = ? 
                ORDER BY pod_id DESC 
                LIMIT 1
            ", [$dr_id])->getRow();

            if ($existing) {
                $this->db->query("
                    UPDATE tbl_delivery_receipt_pod 
                    SET `$file_type` = ?, `updated_at` = NOW()
                    WHERE pod_id = ?
                ", [$relativePath, $existing->pod_id]);
            } else {
                $this->db->query("
                    INSERT INTO tbl_delivery_receipt_pod(dr_id, `$file_type`, created_by)
                    VALUES (?, ?, ?)
                ", [$dr_id, $relativePath, $this->cuser]);
            }

            return [
                'status' => 'success',
                'message' => 'File uploaded successfully!',
                'path' => $relativePath,
                'file_type' => $file_type
            ];
        } else {
            return ['status' => 'error', 'message' => 'Failed to move uploaded file.'];
        }
    }

    // ==============================
    // UPLOAD SIGNATURE (base64 from canvas)
    // ==============================
    public function uploadSignature()
    {
        $dr_id = $this->request->getPost('dr_id');
        $image_data = $this->request->getPost('signature_data');

        if (!$dr_id || !$image_data) {
            return ['status' => 'error', 'message' => 'Missing signature data.'];
        }

        if (!preg_match('/^data:image\/(png|jpeg);base64,/', $image_data, $matches)) {
            return ['status' => 'error', 'message' => 'Invalid signature format.'];
        }

        $ext = $matches[1] === 'jpeg' ? 'jpg' : 'png';
        $image_data = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $image_data);
        $image_data = str_replace(' ', '+', $image_data);
        $decoded = base64_decode($image_data);

        if (!$decoded || strlen($decoded) < 100) {
            return ['status' => 'error', 'message' => 'Signature is empty.'];
        }

        if (strlen($decoded) > 2 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'Signature image too large.'];
        }

        $newName = 'DR_' . $dr_id . '_SIGNATURE_' . time() . '.' . $ext;
        $uploadPath = FCPATH . 'uploads/delivery_receipts/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if (file_put_contents($uploadPath . $newName, $decoded) === false) {
            return ['status' => 'error', 'message' => 'Failed to save signature file.'];
        }

        $relativePath = 'uploads/delivery_receipts/' . $newName;

        $existing = $this->db->query("
            SELECT pod_id FROM tbl_delivery_receipt_pod 
            WHERE dr_id = ? 
            ORDER BY pod_id DESC 
            LIMIT 1
        ", [$dr_id])->getRow();

        if ($existing) {
            $this->db->query("
                UPDATE tbl_delivery_receipt_pod 
                SET customer_signature = ?, updated_at = NOW()
                WHERE pod_id = ?
            ", [$relativePath, $existing->pod_id]);
        } else {
            $this->db->query("
                INSERT INTO tbl_delivery_receipt_pod(dr_id, customer_signature, created_by)
                VALUES (?, ?, ?)
            ", [$dr_id, $relativePath, $this->cuser]);
        }

        return [
            'status' => 'success',
            'message' => 'Signature saved successfully!',
            'path' => $relativePath
        ];
    }
}