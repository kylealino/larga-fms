<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Truck_Model extends Model
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
    // GENERATE TRUCK CODE
    // ==============================
    private function generateTruckCode($config)
    {
        $year = date('Y');
        $prefix = '';
        switch($config) {
            case 'RIGID': $prefix = 'TRK'; break;
            case 'TRACTOR': $prefix = 'TRC'; break;
            case 'TRAILER': $prefix = 'CHS'; break;
            default: $prefix = 'TRK';
        }
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_trucks WHERE vehicle_config = ? AND YEAR(created_at) = ?", [$config, $year]);
        $count = $query->getRow()->total + 1;
        return $prefix . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ==============================
    // UPLOAD TRUCK IMAGE
    // ==============================
    private function uploadTruckImage($truck_id)
    {
        $file = $this->request->getFile('truck_image');
        $filename = '';
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'TRK_' . $truck_id . '_' . time() . '.' . $file->getExtension();
            $uploadPath = ROOTPATH . 'public/uploads/trucks/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $newName);
            $filename = 'uploads/trucks/' . $newName;
        }
        
        return $filename;
    }

    // ==============================
    // UPLOAD DOCUMENT ATTACHMENT
    // ==============================
    private function uploadDocumentAttachment($truck_id)
    {
        $file = $this->request->getFile('document_attachment');
        $filename = '';
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'DOC_' . $truck_id . '_' . time() . '.' . $file->getExtension();
            $uploadPath = ROOTPATH . 'public/uploads/truck_documents/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $newName);
            $filename = 'uploads/truck_documents/' . $newName;
        }
        
        return $filename;
    }

    // ==============================
    // SAVE TRUCK
    // ==============================
    public function saveTruck() 
    { 
        $vehicle_config = $this->request->getPost('vehicle_config') ?: 'RIGID';
        $truck_code = $this->generateTruckCode($vehicle_config);
        $plate_number = $this->request->getPost('plate_number');
        $mv_file_number = $this->request->getPost('mv_file_number');
        $vehicle_type = $this->request->getPost('vehicle_type');
        $body_type = $this->request->getPost('body_type');
        $make = $this->request->getPost('make');
        $model = $this->request->getPost('model');
        $model_year = $this->request->getPost('model_year');
        $chassis_number = $this->request->getPost('chassis_number');
        $engine_number = $this->request->getPost('engine_number');
        $fuel_type = $this->request->getPost('fuel_type');
        $fuel_tank_capacity = $this->request->getPost('fuel_tank_capacity') ?: 0;
        $load_capacity = $this->request->getPost('load_capacity') ?: 0;
        $current_odometer = $this->request->getPost('current_odometer') ?: 0;
        $acquisition_date = $this->request->getPost('acquisition_date');
        $acquired_from = $this->request->getPost('acquired_from');
        $acquired_from_branch = $this->request->getPost('acquired_from_branch');
        $account_manager = $this->request->getPost('account_manager');
        $ownership = $this->request->getPost('ownership') ?: 'COMPANY-OWNED';
        $truck_status = $this->request->getPost('truck_status') ?: 'AVAILABLE';
        $remarks = $this->request->getPost('remarks');

        // Check if plate number already exists
        if($plate_number) {
            $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_trucks WHERE plate_number = ?", [$plate_number])->getRow();
            if($check->count > 0) {
                return ['status' => 'error', 'message' => 'Plate number already exists!'];
            }
        }

        // Check if chassis number already exists
        if($chassis_number) {
            $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_trucks WHERE chassis_number = ?", [$chassis_number])->getRow();
            if($check->count > 0) {
                return ['status' => 'error', 'message' => 'Chassis number already exists!'];
            }
        }

        $this->db->transStart();

        $query = $this->db->query("
            INSERT INTO `tbl_trucks`(
                `truck_code`,
                `vehicle_config`,
                `plate_number`,
                `mv_file_number`,
                `vehicle_type`,
                `body_type`,
                `make`,
                `model`,
                `model_year`,
                `chassis_number`,
                `engine_number`,
                `fuel_type`,
                `fuel_tank_capacity`,
                `load_capacity`,
                `current_odometer`,
                `acquisition_date`,
                `acquired_from`,
                `acquired_from_branch`,
                `account_manager`,
                `ownership`,
                `truck_status`,
                `remarks`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $truck_code,
                $vehicle_config,
                $plate_number,
                $mv_file_number,
                $vehicle_type,
                $body_type,
                $make,
                $model,
                $model_year,
                $chassis_number,
                $engine_number,
                $fuel_type,
                $fuel_tank_capacity,
                $load_capacity,
                $current_odometer,
                $acquisition_date,
                $acquired_from,
                $acquired_from_branch,
                $account_manager,
                $ownership,
                $truck_status,
                $remarks,
                $this->cuser
            ]
        );

        $truck_id = $this->db->insertID();

        // Upload truck image
        if($query && $truck_id) {
            $truck_image = $this->uploadTruckImage($truck_id);
            if($truck_image) {
                $this->db->query("UPDATE tbl_trucks SET truck_image = ? WHERE truck_id = ?", [$truck_image, $truck_id]);
            }
        }

        if ($query && $truck_id) {
            $this->db->transComplete();
            return ['status' => 'success', 'message' => 'Truck Saved Successfully!', 'truck_id' => $truck_id];
        } else {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        }
    }

    // ==============================
    // UPDATE TRUCK
    // ==============================
    public function updateTruck() 
    { 
        $truck_id = $this->request->getPost('truck_id');
        $vehicle_config = $this->request->getPost('vehicle_config') ?: 'RIGID';
        $plate_number = $this->request->getPost('plate_number');
        $mv_file_number = $this->request->getPost('mv_file_number');
        $vehicle_type = $this->request->getPost('vehicle_type');
        $body_type = $this->request->getPost('body_type');
        $make = $this->request->getPost('make');
        $model = $this->request->getPost('model');
        $model_year = $this->request->getPost('model_year');
        $chassis_number = $this->request->getPost('chassis_number');
        $engine_number = $this->request->getPost('engine_number');
        $fuel_type = $this->request->getPost('fuel_type');
        $fuel_tank_capacity = $this->request->getPost('fuel_tank_capacity') ?: 0;
        $load_capacity = $this->request->getPost('load_capacity') ?: 0;
        $current_odometer = $this->request->getPost('current_odometer') ?: 0;
        $acquisition_date = $this->request->getPost('acquisition_date');
        $acquired_from = $this->request->getPost('acquired_from');
        $acquired_from_branch = $this->request->getPost('acquired_from_branch');
        $account_manager = $this->request->getPost('account_manager');
        $ownership = $this->request->getPost('ownership') ?: 'COMPANY-OWNED';
        $truck_status = $this->request->getPost('truck_status') ?: 'AVAILABLE';
        $remarks = $this->request->getPost('remarks');

        // Check if plate number already exists for a different truck
        if($plate_number) {
            $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_trucks WHERE plate_number = ? AND truck_id != ?", [$plate_number, $truck_id])->getRow();
            if($check->count > 0) {
                return ['status' => 'error', 'message' => 'Plate number already exists!'];
            }
        }

        // Check if chassis number already exists for a different truck
        if($chassis_number) {
            $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_trucks WHERE chassis_number = ? AND truck_id != ?", [$chassis_number, $truck_id])->getRow();
            if($check->count > 0) {
                return ['status' => 'error', 'message' => 'Chassis number already exists!'];
            }
        }

        // Get existing truck data for file management
        $existing = $this->db->query("SELECT truck_image FROM tbl_trucks WHERE truck_id = ?", [$truck_id])->getRowArray();
        $truck_image = $existing ? $existing['truck_image'] : '';

        // Handle truck image upload
        $file = $this->request->getFile('truck_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if($truck_image && file_exists(ROOTPATH . 'public/' . $truck_image)) {
                unlink(ROOTPATH . 'public/' . $truck_image);
            }
            $truck_image = $this->uploadTruckImage($truck_id);
        }

        $query = $this->db->query("
            UPDATE `tbl_trucks`
            SET 
                `vehicle_config` = ?,
                `plate_number` = ?,
                `mv_file_number` = ?,
                `vehicle_type` = ?,
                `body_type` = ?,
                `make` = ?,
                `model` = ?,
                `model_year` = ?,
                `chassis_number` = ?,
                `engine_number` = ?,
                `fuel_type` = ?,
                `fuel_tank_capacity` = ?,
                `load_capacity` = ?,
                `current_odometer` = ?,
                `acquisition_date` = ?,
                `acquired_from` = ?,
                `acquired_from_branch` = ?,
                `account_manager` = ?,
                `ownership` = ?,
                `truck_status` = ?,
                `remarks` = ?,
                `truck_image` = ?,
                `updated_at` = NOW()
            WHERE `truck_id` = ?
            ", 
            [
                $vehicle_config,
                $plate_number,
                $mv_file_number,
                $vehicle_type,
                $body_type,
                $make,
                $model,
                $model_year,
                $chassis_number,
                $engine_number,
                $fuel_type,
                $fuel_tank_capacity,
                $load_capacity,
                $current_odometer,
                $acquisition_date,
                $acquired_from,
                $acquired_from_branch,
                $account_manager,
                $ownership,
                $truck_status,
                $remarks,
                $truck_image,
                $truck_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Truck Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    // ==============================
    // DELETE TRUCK
    // ==============================
    public function deleteTruck() 
    { 
        $truck_id = $this->request->getPost('truck_id');

        // Get attachments to delete files
        $truck = $this->db->query("SELECT truck_image FROM tbl_trucks WHERE truck_id = ?", [$truck_id])->getRowArray();
        
        if($truck && $truck['truck_image'] && file_exists(ROOTPATH . 'public/' . $truck['truck_image'])) {
            unlink(ROOTPATH . 'public/' . $truck['truck_image']);
        }

        $query = $this->db->query("DELETE FROM `tbl_trucks` WHERE `truck_id` = ?", [$truck_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Truck Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }

    // ==============================
    // GET SINGLE TRUCK
    // ==============================
    public function getTruck($truck_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_trucks WHERE truck_id = ?", [$truck_id]);
        return $query->getRowArray();
    }

    // ==============================
    // SAVE TRUCK DOCUMENT
    // ==============================
    public function saveDocument() 
    { 
        $truck_id = $this->request->getPost('truck_id');
        $document_type = $this->request->getPost('document_type');
        $document_number = $this->request->getPost('document_number');
        $issue_date = $this->request->getPost('issue_date');
        $expiration_date = $this->request->getPost('expiration_date');
        $provider_name = $this->request->getPost('provider_name');
        $policy_number = $this->request->getPost('policy_number');
        $coverage_type = $this->request->getPost('coverage_type');
        $premium = $this->request->getPost('premium') ?: 0;
        $coverage_amount = $this->request->getPost('coverage_amount') ?: 0;
        $rate = $this->request->getPost('rate') ?: 0;
        $remarks = $this->request->getPost('remarks');

        // Auto-calculate document status
        $document_status = 'VALID';
        if($expiration_date) {
            $today = new \DateTime();
            $expiry = new \DateTime($expiration_date);
            $daysDiff = $today->diff($expiry)->days;
            if($expiry < $today) {
                $document_status = 'EXPIRED';
            } elseif($daysDiff <= 30) {
                $document_status = 'EXPIRING';
            }
        }

        // Handle file upload
        $attachment = $this->uploadDocumentAttachment($truck_id);

        $query = $this->db->query("
            INSERT INTO `tbl_truck_documents`(
                `truck_id`,
                `document_type`,
                `document_number`,
                `issue_date`,
                `expiration_date`,
                `document_status`,
                `provider_name`,
                `policy_number`,
                `coverage_type`,
                `premium`,
                `coverage_amount`,
                `rate`,
                `document_attachment`,
                `remarks`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $truck_id,
                $document_type,
                $document_number,
                $issue_date,
                $expiration_date,
                $document_status,
                $provider_name,
                $policy_number,
                $coverage_type,
                $premium,
                $coverage_amount,
                $rate,
                $attachment,
                $remarks,
                $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Document Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving document.'];
        }
    }

    // ==============================
    // UPDATE TRUCK DOCUMENT
    // ==============================
    public function updateDocument() 
    { 
        $document_id = $this->request->getPost('document_id');
        $truck_id = $this->request->getPost('truck_id');
        $document_type = $this->request->getPost('document_type');
        $document_number = $this->request->getPost('document_number');
        $issue_date = $this->request->getPost('issue_date');
        $expiration_date = $this->request->getPost('expiration_date');
        $provider_name = $this->request->getPost('provider_name');
        $policy_number = $this->request->getPost('policy_number');
        $coverage_type = $this->request->getPost('coverage_type');
        $premium = $this->request->getPost('premium') ?: 0;
        $coverage_amount = $this->request->getPost('coverage_amount') ?: 0;
        $rate = $this->request->getPost('rate') ?: 0;
        $remarks = $this->request->getPost('remarks');

        // Auto-calculate document status
        $document_status = 'VALID';
        if($expiration_date) {
            $today = new \DateTime();
            $expiry = new \DateTime($expiration_date);
            $daysDiff = $today->diff($expiry)->days;
            if($expiry < $today) {
                $document_status = 'EXPIRED';
            } elseif($daysDiff <= 30) {
                $document_status = 'EXPIRING';
            }
        }

        // Get existing document for attachment
        $existing = $this->db->query("SELECT document_attachment FROM tbl_truck_documents WHERE document_id = ?", [$document_id])->getRowArray();
        $attachment = $existing ? $existing['document_attachment'] : '';

        // Handle file upload
        $file = $this->request->getFile('document_attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if($attachment && file_exists(ROOTPATH . 'public/' . $attachment)) {
                unlink(ROOTPATH . 'public/' . $attachment);
            }
            $attachment = $this->uploadDocumentAttachment($truck_id);
        }

        $query = $this->db->query("
            UPDATE `tbl_truck_documents`
            SET 
                `document_type` = ?,
                `document_number` = ?,
                `issue_date` = ?,
                `expiration_date` = ?,
                `document_status` = ?,
                `provider_name` = ?,
                `policy_number` = ?,
                `coverage_type` = ?,
                `premium` = ?,
                `coverage_amount` = ?,
                `rate` = ?,
                `document_attachment` = ?,
                `remarks` = ?,
                `updated_at` = NOW()
            WHERE `document_id` = ?
            ", 
            [
                $document_type,
                $document_number,
                $issue_date,
                $expiration_date,
                $document_status,
                $provider_name,
                $policy_number,
                $coverage_type,
                $premium,
                $coverage_amount,
                $rate,
                $attachment,
                $remarks,
                $document_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Document Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating document.'];
        }
    }

    // ==============================
    // DELETE TRUCK DOCUMENT
    // ==============================
    public function deleteDocument() 
    { 
        $document_id = $this->request->getPost('document_id');

        // Get attachment to delete file
        $doc = $this->db->query("SELECT document_attachment FROM tbl_truck_documents WHERE document_id = ?", [$document_id])->getRowArray();
        if($doc && $doc['document_attachment'] && file_exists(ROOTPATH . 'public/' . $doc['document_attachment'])) {
            unlink(ROOTPATH . 'public/' . $doc['document_attachment']);
        }

        $query = $this->db->query("DELETE FROM `tbl_truck_documents` WHERE `document_id` = ?", [$document_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Document Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting document.'];
        }
    }

    // ==============================
    // GET DOCUMENTS BY TRUCK
    // ==============================
    public function getDocuments($truck_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_truck_documents WHERE truck_id = ? ORDER BY document_id DESC", [$truck_id]);
        return $query->getResultArray();
    }

    // ==============================
    // GET SINGLE DOCUMENT
    // ==============================
    public function getDocument($document_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_truck_documents WHERE document_id = ?", [$document_id]);
        return $query->getRowArray();
    }

    // ==============================
    // GET TRUCK DELIVERY HISTORY
    // ==============================
    public function getTruckHistory($truck_id)
    {
        $truck = $this->db->query("SELECT plate_number FROM tbl_trucks WHERE truck_id = ?", [$truck_id])->getRow();
        if (!$truck || !$truck->plate_number) return [];

        return $this->db->query("
            SELECT t.trip_code, t.origin, t.destination, t.actual_delivery_date,
                   c.customer_name,
                   a.driver_name, a.helper_name, a.assignment_date,
                   dr.dr_code, dr.dr_status, dr.dr_date
            FROM tbl_trip_assignments a
            JOIN tbl_trips t ON a.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_delivery_receipts dr ON dr.trip_id = t.trip_id
            WHERE (a.truck_plate = ? OR a.tractor_plate = ? OR a.chassis_plate = ?)
              AND t.trip_status = 'COMPLETED'
            ORDER BY t.actual_delivery_date DESC, a.assignment_id DESC
        ", [$truck->plate_number, $truck->plate_number, $truck->plate_number])->getResultArray();
    }
}