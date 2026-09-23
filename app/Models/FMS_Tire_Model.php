<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Tire_Model extends Model
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
    // CODE GENERATORS
    // ==============================
    private function generateTireCode()
    {
        $year = date('Y');
        $q = $this->db->query("SELECT COUNT(*) as total FROM tbl_tires WHERE YEAR(created_at) = ?", [$year]);
        return 'TIR-' . $year . '-' . str_pad($q->getRow()->total + 1, 6, '0', STR_PAD_LEFT);
    }

    private function generateTransactionCode()
    {
        $year = date('Y');
        $q = $this->db->query("SELECT COUNT(*) as total FROM tbl_tire_transactions WHERE YEAR(created_at) = ?", [$year]);
        return 'TTX-' . $year . '-' . str_pad($q->getRow()->total + 1, 6, '0', STR_PAD_LEFT);
    }

    private function generateInstallationCode()
    {
        $year = date('Y');
        $q = $this->db->query("SELECT COUNT(*) as total FROM tbl_tire_installations WHERE YEAR(created_at) = ?", [$year]);
        return 'TIN-' . $year . '-' . str_pad($q->getRow()->total + 1, 6, '0', STR_PAD_LEFT);
    }

    private function generateDisposalCode()
    {
        $year = date('Y');
        $q = $this->db->query("SELECT COUNT(*) as total FROM tbl_tire_disposals WHERE YEAR(created_at) = ?", [$year]);
        return 'TDS-' . $year . '-' . str_pad($q->getRow()->total + 1, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // LOOKUPS
    // ==============================
    public function getTrucks()
    {
        return $this->db->query("
            SELECT truck_id, truck_code, plate_number, make, model, current_odometer
            FROM tbl_trucks
            WHERE truck_status NOT IN ('RETIRED','OUT OF SERVICE')
            ORDER BY plate_number
        ")->getResultArray();
    }

    public function getAvailableTires()
    {
        return $this->db->query("
            SELECT tire_id, tire_code, serial_number, model, brand, size, tread_depth_mm
            FROM tbl_tires
            WHERE tire_status IN ('IN_STOCK','REMOVED')
            ORDER BY tire_code
        ")->getResultArray();
    }

    // ==============================
    // TIRE CRUD
    // ==============================
    public function getAllTires()
    {
        return $this->db->query("
            SELECT * FROM tbl_tires
            ORDER BY tire_id DESC
        ")->getResultArray();
    }

    public function getTire($tire_id)
    {
        $q = $this->db->query("SELECT * FROM tbl_tires WHERE tire_id = ?", [$tire_id]);
        return $q->getRowArray();
    }

    public function saveTire()
    {
        $tire_code = $this->generateTireCode();
        $serial_number = $this->request->getPost('serial_number');
        $model = $this->request->getPost('model');
        $brand = $this->request->getPost('brand');
        $size = $this->request->getPost('size');
        $life_expectancy_km = $this->request->getPost('life_expectancy_km') ?: 0;
        $tread_depth_mm = $this->request->getPost('tread_depth_mm') ?: 0;
        $price = $this->request->getPost('price') ?: 0;
        $date_of_manufacture = $this->request->getPost('date_of_manufacture') ?: null;
        $supplier = $this->request->getPost('supplier');
        $tire_status = $this->request->getPost('tire_status') ?: 'IN_STOCK';
        $remarks = $this->request->getPost('remarks');

        if (!empty($serial_number)) {
            $dup = $this->db->query("SELECT tire_id FROM tbl_tires WHERE serial_number = ? LIMIT 1", [$serial_number])->getRow();
            if ($dup) {
                return ['status' => 'error', 'message' => 'A tire with this serial number already exists.'];
            }
        }

        $query = $this->db->query("
            INSERT INTO `tbl_tires`(
                `tire_code`, `serial_number`, `model`, `brand`, `size`,
                `life_expectancy_km`, `tread_depth_mm`, `price`, `date_of_manufacture`,
                `supplier`, `tire_status`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $tire_code, $serial_number, $model, $brand, $size,
            $life_expectancy_km, $tread_depth_mm, $price, $date_of_manufacture,
            $supplier, $tire_status, $remarks, $this->cuser
        ]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Tire Saved Successfully!', 'tire_code' => $tire_code];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving tire.'];
        }
    }

    public function updateTire()
    {
        $tire_id = $this->request->getPost('tire_id');
        $serial_number = $this->request->getPost('serial_number');
        $model = $this->request->getPost('model');
        $brand = $this->request->getPost('brand');
        $size = $this->request->getPost('size');
        $life_expectancy_km = $this->request->getPost('life_expectancy_km') ?: 0;
        $tread_depth_mm = $this->request->getPost('tread_depth_mm') ?: 0;
        $price = $this->request->getPost('price') ?: 0;
        $date_of_manufacture = $this->request->getPost('date_of_manufacture') ?: null;
        $supplier = $this->request->getPost('supplier');
        $tire_status = $this->request->getPost('tire_status');
        $remarks = $this->request->getPost('remarks');

        if (!empty($serial_number)) {
            $dup = $this->db->query("SELECT tire_id FROM tbl_tires WHERE serial_number = ? AND tire_id != ? LIMIT 1", [$serial_number, $tire_id])->getRow();
            if ($dup) {
                return ['status' => 'error', 'message' => 'A tire with this serial number already exists.'];
            }
        }

        $query = $this->db->query("
            UPDATE `tbl_tires` SET
                `serial_number` = ?, `model` = ?, `brand` = ?, `size` = ?,
                `life_expectancy_km` = ?, `tread_depth_mm` = ?, `price` = ?,
                `date_of_manufacture` = ?, `supplier` = ?, `tire_status` = ?,
                `remarks` = ?, `updated_at` = NOW()
            WHERE `tire_id` = ?
        ", [
            $serial_number, $model, $brand, $size,
            $life_expectancy_km, $tread_depth_mm, $price,
            $date_of_manufacture, $supplier, $tire_status,
            $remarks, $tire_id
        ]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Tire Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating tire.'];
        }
    }

    public function deleteTire()
    {
        $tire_id = $this->request->getPost('tire_id');

        $activeInstall = $this->db->query("
            SELECT installation_id FROM tbl_tire_installations WHERE tire_id = ? AND status = 'ACTIVE' LIMIT 1
        ", [$tire_id])->getRow();

        if ($activeInstall) {
            return ['status' => 'error', 'message' => 'Cannot delete: this tire is currently installed on a truck.'];
        }

        $query = $this->db->query("DELETE FROM `tbl_tires` WHERE `tire_id` = ?", [$tire_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Tire Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting tire.'];
        }
    }

    // ==============================
    // TRANSACTIONS
    // ==============================
    public function getAllTransactions()
    {
        return $this->db->query("
            SELECT * FROM tbl_tire_transactions
            ORDER BY transaction_date DESC, transaction_id DESC
        ")->getResultArray();
    }

    public function getTransaction($transaction_id)
    {
        $q = $this->db->query("SELECT * FROM tbl_tire_transactions WHERE transaction_id = ?", [$transaction_id]);
        return $q->getRowArray();
    }

    public function saveTransaction()
    {
        $transaction_code = $this->generateTransactionCode();
        $transaction_type = $this->request->getPost('transaction_type');
        $transaction_date = $this->request->getPost('transaction_date') ?: date('Y-m-d');
        $tire_id = $this->request->getPost('tire_id');
        $tire_code = $this->request->getPost('tire_code');
        $quantity_post = $this->request->getPost('quantity');
        $quantity = ($quantity_post === null || $quantity_post === '') ? 1 : $quantity_post;
        $price = $this->request->getPost('price') ?: 0;
        $purpose = $this->request->getPost('purpose');
        $truck_id = $this->request->getPost('truck_id') ?: null;
        $truck_plate = $this->request->getPost('truck_plate');
        $supplier_vendor = $this->request->getPost('supplier_vendor');
        $released_by = $this->request->getPost('released_by');
        $reference_number = $this->request->getPost('reference_number');
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_tire_transactions`(
                `transaction_code`, `transaction_type`, `transaction_date`, `tire_id`, `tire_code`,
                `quantity`, `price`, `purpose`, `truck_id`, `truck_plate`,
                `supplier_vendor`, `released_by`, `reference_number`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $transaction_code, $transaction_type, $transaction_date, $tire_id, $tire_code,
            $quantity, $price, $purpose, $truck_id, $truck_plate,
            $supplier_vendor, $released_by, $reference_number, $remarks, $this->cuser
        ]);

        if ($query) {
            // If transaction OUT for installation, mark tire as removed from stock (still requires install action)
            if ($transaction_type == 'OUT') {
                $this->db->query("UPDATE tbl_tires SET tire_status = 'REMOVED' WHERE tire_id = ?", [$tire_id]);
            }
            return ['status' => 'success', 'message' => 'Transaction Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving transaction.'];
        }
    }

    public function deleteTransaction()
    {
        $transaction_id = $this->request->getPost('transaction_id');
        $query = $this->db->query("DELETE FROM `tbl_tire_transactions` WHERE `transaction_id` = ?", [$transaction_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Transaction Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting transaction.'];
        }
    }

    // ==============================
    // INSTALLATIONS
    // ==============================
    public function getAllInstallations()
    {
        return $this->db->query("
            SELECT i.*,
                   t.plate_number AS truck_plate_live,
                   t.make,
                   t.model
            FROM tbl_tire_installations i
            LEFT JOIN tbl_trucks t ON i.truck_id = t.truck_id
            ORDER BY i.installation_date DESC, i.installation_id DESC
        ")->getResultArray();
    }

    public function getInstallation($installation_id)
    {
        $q = $this->db->query("
            SELECT * FROM tbl_tire_installations WHERE installation_id = ?
        ", [$installation_id]);
        return $q->getRowArray();
    }

    public function saveInstallation()
    {
        $installation_code = $this->generateInstallationCode();
        $tire_id = $this->request->getPost('tire_id');
        $tire_code = $this->request->getPost('tire_code');
        $truck_id = $this->request->getPost('truck_id');
        $truck_plate = $this->request->getPost('truck_plate');
        $position = $this->request->getPost('position');
        $installation_date = $this->request->getPost('installation_date') ?: date('Y-m-d');
        $installation_odometer = $this->request->getPost('installation_odometer') ?: 0;
        $installed_by = $this->request->getPost('installed_by');
        $installation_remarks = $this->request->getPost('installation_remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_tire_installations`(
                `installation_code`, `tire_id`, `tire_code`, `truck_id`, `truck_plate`,
                `position`, `installation_date`, `installation_odometer`,
                `installed_by`, `installation_remarks`, `status`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE', ?)
        ", [
            $installation_code, $tire_id, $tire_code, $truck_id, $truck_plate,
            $position, $installation_date, $installation_odometer,
            $installed_by, $installation_remarks, $this->cuser
        ]);

        if ($query) {
            // Mark tire as INSTALLED and record current odometer
            $this->db->query("
                UPDATE tbl_tires 
                SET tire_status = 'INSTALLED', current_odometer = ?
                WHERE tire_id = ?
            ", [$installation_odometer, $tire_id]);

            return ['status' => 'success', 'message' => 'Tire Installed Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while installing tire.'];
        }
    }

    public function removeInstallation()
    {
        $installation_id = $this->request->getPost('installation_id');
        $removed_date = $this->request->getPost('removed_date') ?: date('Y-m-d');
        $removal_odometer = $this->request->getPost('removal_odometer') ?: 0;
        $reason_for_removal = $this->request->getPost('reason_for_removal');
        $tire_condition_on_removal = $this->request->getPost('tire_condition_on_removal');
        $removed_by = $this->request->getPost('removed_by');
        $removal_remarks = $this->request->getPost('removal_remarks');

        // Get installation info
        $inst = $this->db->query("SELECT * FROM tbl_tire_installations WHERE installation_id = ?", [$installation_id])->getRow();
        if (!$inst) {
            return ['status' => 'error', 'message' => 'Installation not found.'];
        }

        // Calculate distance used
        $distance_used_km = floatval($removal_odometer) - floatval($inst->installation_odometer);

        $query = $this->db->query("
            UPDATE `tbl_tire_installations` SET
                `status` = 'REMOVED',
                `removed_date` = ?,
                `removal_odometer` = ?,
                `distance_used_km` = ?,
                `reason_for_removal` = ?,
                `tire_condition_on_removal` = ?,
                `removed_by` = ?,
                `removal_remarks` = ?,
                `updated_at` = NOW()
            WHERE `installation_id` = ?
        ", [
            $removed_date, $removal_odometer, $distance_used_km,
            $reason_for_removal, $tire_condition_on_removal,
            $removed_by, $removal_remarks, $installation_id
        ]);

        if ($query) {
            // Update tire: set as REMOVED, add to total distance, update odometer
            $this->db->query("
                UPDATE tbl_tires 
                SET tire_status = 'REMOVED',
                    current_odometer = ?,
                    total_distance_km = total_distance_km + ?
                WHERE tire_id = ?
            ", [$removal_odometer, $distance_used_km, $inst->tire_id]);

            return ['status' => 'success', 'message' => 'Tire Removed Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while removing tire.'];
        }
    }

    public function deleteInstallation()
    {
        $installation_id = $this->request->getPost('installation_id');
        $query = $this->db->query("DELETE FROM `tbl_tire_installations` WHERE `installation_id` = ?", [$installation_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Installation Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting installation.'];
        }
    }

    // ==============================
    // DISPOSALS
    // ==============================
    public function getAllDisposals()
    {
        return $this->db->query("
            SELECT * FROM tbl_tire_disposals
            ORDER BY disposal_date DESC, disposal_id DESC
        ")->getResultArray();
    }

    public function saveDisposal()
    {
        $disposal_code = $this->generateDisposalCode();
        $tire_id = $this->request->getPost('tire_id');
        $tire_code = $this->request->getPost('tire_code');
        $disposal_date = $this->request->getPost('disposal_date') ?: date('Y-m-d');
        $disposal_reason = $this->request->getPost('disposal_reason');
        $final_mileage = $this->request->getPost('final_mileage') ?: 0;
        $disposal_details = $this->request->getPost('disposal_details');
        $final_status = $this->request->getPost('final_status') ?: 'DISPOSED';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_tire_disposals`(
                `disposal_code`, `tire_id`, `tire_code`, `disposal_date`,
                `disposal_reason`, `final_mileage`, `disposal_details`,
                `final_status`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $disposal_code, $tire_id, $tire_code, $disposal_date,
            $disposal_reason, $final_mileage, $disposal_details,
            $final_status, $remarks, $this->cuser
        ]);

        if ($query) {
            // Mark tire as DISPOSED
            $this->db->query("
                UPDATE tbl_tires 
                SET tire_status = 'DISPOSED'
                WHERE tire_id = ?
            ", [$tire_id]);

            return ['status' => 'success', 'message' => 'Tire Disposed Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while disposing tire.'];
        }
    }

    public function deleteDisposal()
    {
        $disposal_id = $this->request->getPost('disposal_id');
        $query = $this->db->query("DELETE FROM `tbl_tire_disposals` WHERE `disposal_id` = ?", [$disposal_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Disposal Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting disposal.'];
        }
    }

    // ==============================
    // TIRE JOURNEY (full history)
    // ==============================
    public function getTireJourney($tire_id)
    {
        $journey = [];

        // 1. Tire master
        $tire = $this->db->query("SELECT * FROM tbl_tires WHERE tire_id = ?", [$tire_id])->getRowArray();
        if ($tire) {
            $journey[] = [
                'date' => $tire['created_at'],
                'type' => 'PURCHASE',
                'description' => 'Tire registered: ' . $tire['tire_code'],
                'details' => 'Serial: ' . $tire['serial_number'] . ' | Brand: ' . ($tire['brand'] ?: '—') . ' | Price: ₱' . number_format($tire['price'], 2)
            ];
        }

        // 2. Transactions
        $trans = $this->db->query("
            SELECT * FROM tbl_tire_transactions WHERE tire_id = ? ORDER BY transaction_date ASC
        ", [$tire_id])->getResultArray();
        foreach ($trans as $t) {
            $journey[] = [
                'date' => $t['transaction_date'],
                'type' => 'INVENTORY ' . $t['transaction_type'],
                'description' => $t['transaction_type'] == 'IN' ? 'Received into inventory' : 'Released from inventory',
                'details' => 'Ref: ' . ($t['reference_number'] ?: '—') . ' | Qty: ' . $t['quantity'] . ' | Vendor: ' . ($t['supplier_vendor'] ?: '—')
            ];
        }

        // 3. Installations
        $installs = $this->db->query("
            SELECT * FROM tbl_tire_installations WHERE tire_id = ? ORDER BY installation_date ASC
        ", [$tire_id])->getResultArray();
        foreach ($installs as $i) {
            $journey[] = [
                'date' => $i['installation_date'],
                'type' => 'INSTALLED',
                'description' => 'Installed on ' . ($i['truck_plate'] ?: 'Truck #' . $i['truck_id']),
                'details' => 'Position: ' . ($i['position'] ?: '—') . ' | Odometer: ' . number_format($i['installation_odometer'], 0) . ' km | By: ' . ($i['installed_by'] ?: '—')
            ];
            if ($i['status'] == 'REMOVED' && $i['removed_date']) {
                $journey[] = [
                    'date' => $i['removed_date'],
                    'type' => 'REMOVED',
                    'description' => 'Removed from ' . ($i['truck_plate'] ?: 'Truck #' . $i['truck_id']),
                    'details' => 'Reason: ' . ($i['reason_for_removal'] ?: '—') . ' | Distance: ' . number_format($i['distance_used_km'], 0) . ' km | Condition: ' . ($i['tire_condition_on_removal'] ?: '—')
                ];
            }
        }

        // 4. Disposals
        $disps = $this->db->query("
            SELECT * FROM tbl_tire_disposals WHERE tire_id = ? ORDER BY disposal_date ASC
        ", [$tire_id])->getResultArray();
        foreach ($disps as $d) {
            $journey[] = [
                'date' => $d['disposal_date'],
                'type' => 'DISPOSED',
                'description' => 'Tire disposed',
                'details' => 'Reason: ' . ($d['disposal_reason'] ?: '—') . ' | Final: ' . number_format($d['final_mileage'], 0) . ' km | Status: ' . $d['final_status']
            ];
        }

        // Sort by date
        usort($journey, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $journey;
    }
}