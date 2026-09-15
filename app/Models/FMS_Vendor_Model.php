<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Vendor_Model extends Model
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
    // GENERATE VENDOR CODE
    // ==============================
    private function generateVendorCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_vendors WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'VND-' . $year . '-';
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ==============================
    // SAVE VENDOR
    // ==============================
    public function saveVendor() 
    { 
        $vendor_code = $this->generateVendorCode();
        $vendor_name = $this->request->getPost('vendor_name');
        $vendor_type = $this->request->getPost('vendor_type');
        $contact_person = $this->request->getPost('contact_person');
        $contact_number = $this->request->getPost('contact_number');
        $email_address = $this->request->getPost('email_address');
        $address = $this->request->getPost('address');
        $payment_terms = $this->request->getPost('payment_terms');
        $vendor_status = $this->request->getPost('vendor_status') ?: 'ACTIVE';
        $remarks = $this->request->getPost('remarks');

        // Check if vendor name already exists
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_vendors WHERE vendor_name = ?", [$vendor_name])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Vendor name already exists!'];
        }

        $query = $this->db->query("
            INSERT INTO `tbl_vendors`(
                `vendor_code`,
                `vendor_name`,
                `vendor_type`,
                `contact_person`,
                `contact_number`,
                `email_address`,
                `address`,
                `payment_terms`,
                `vendor_status`,
                `remarks`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $vendor_code,
                $vendor_name,
                $vendor_type,
                $contact_person,
                $contact_number,
                $email_address,
                $address,
                $payment_terms,
                $vendor_status,
                $remarks,
                $this->cuser
            ]
        );

        $vendor_id = $this->db->insertID();

        if ($query && $vendor_id) {
            return ['status' => 'success', 'message' => 'Vendor Saved Successfully!', 'vendor_id' => $vendor_id];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        }
    }

    // ==============================
    // UPDATE VENDOR
    // ==============================
    public function updateVendor() 
    { 
        $vendor_id = $this->request->getPost('vendor_id');
        $vendor_name = $this->request->getPost('vendor_name');
        $vendor_type = $this->request->getPost('vendor_type');
        $contact_person = $this->request->getPost('contact_person');
        $contact_number = $this->request->getPost('contact_number');
        $email_address = $this->request->getPost('email_address');
        $address = $this->request->getPost('address');
        $payment_terms = $this->request->getPost('payment_terms');
        $vendor_status = $this->request->getPost('vendor_status') ?: 'ACTIVE';
        $remarks = $this->request->getPost('remarks');

        // Check if vendor name already exists for a different vendor
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_vendors WHERE vendor_name = ? AND vendor_id != ?", [$vendor_name, $vendor_id])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Vendor name already exists!'];
        }

        $query = $this->db->query("
            UPDATE `tbl_vendors`
            SET 
                `vendor_name` = ?,
                `vendor_type` = ?,
                `contact_person` = ?,
                `contact_number` = ?,
                `email_address` = ?,
                `address` = ?,
                `payment_terms` = ?,
                `vendor_status` = ?,
                `remarks` = ?,
                `updated_at` = NOW()
            WHERE `vendor_id` = ?
            ", 
            [
                $vendor_name,
                $vendor_type,
                $contact_person,
                $contact_number,
                $email_address,
                $address,
                $payment_terms,
                $vendor_status,
                $remarks,
                $vendor_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Vendor Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    // ==============================
    // DELETE VENDOR
    // ==============================
    public function deleteVendor() 
    { 
        $vendor_id = $this->request->getPost('vendor_id');

        $query = $this->db->query("DELETE FROM `tbl_vendors` WHERE `vendor_id` = ?", [$vendor_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Vendor Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }

    // ==============================
    // SAVE VENDOR SERVICE
    // ==============================
    public function saveService() 
    { 
        $vendor_id = $this->request->getPost('vendor_id');
        $service_type = $this->request->getPost('service_type');
        $rate_type = $this->request->getPost('rate_type');
        $rate_amount = $this->request->getPost('rate_amount') ?: 0;
        $effective_date = $this->request->getPost('effective_date');
        $service_status = $this->request->getPost('service_status') ?: 'ACTIVE';
        $remarks = $this->request->getPost('remarks');

        if(!$service_type) {
            return ['status' => 'error', 'message' => 'Service type is required!'];
        }

        $query = $this->db->query("
            INSERT INTO `tbl_vendor_services`(
                `vendor_id`,
                `service_type`,
                `rate_type`,
                `rate_amount`,
                `effective_date`,
                `service_status`,
                `remarks`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $vendor_id,
                $service_type,
                $rate_type,
                $rate_amount,
                $effective_date,
                $service_status,
                $remarks,
                $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Service Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving service.'];
        }
    }

    // ==============================
    // UPDATE VENDOR SERVICE
    // ==============================
    public function updateService() 
    { 
        $service_id = $this->request->getPost('service_id');
        $service_type = $this->request->getPost('service_type');
        $rate_type = $this->request->getPost('rate_type');
        $rate_amount = $this->request->getPost('rate_amount') ?: 0;
        $effective_date = $this->request->getPost('effective_date');
        $service_status = $this->request->getPost('service_status') ?: 'ACTIVE';
        $remarks = $this->request->getPost('remarks');

        if(!$service_type) {
            return ['status' => 'error', 'message' => 'Service type is required!'];
        }

        $query = $this->db->query("
            UPDATE `tbl_vendor_services`
            SET 
                `service_type` = ?,
                `rate_type` = ?,
                `rate_amount` = ?,
                `effective_date` = ?,
                `service_status` = ?,
                `remarks` = ?,
                `updated_at` = NOW()
            WHERE `service_id` = ?
            ", 
            [
                $service_type,
                $rate_type,
                $rate_amount,
                $effective_date,
                $service_status,
                $remarks,
                $service_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Service Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating service.'];
        }
    }

    // ==============================
    // DELETE VENDOR SERVICE
    // ==============================
    public function deleteService() 
    { 
        $service_id = $this->request->getPost('service_id');

        $query = $this->db->query("DELETE FROM `tbl_vendor_services` WHERE `service_id` = ?", [$service_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Service Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting service.'];
        }
    }

    // ==============================
    // GET SERVICES BY VENDOR
    // ==============================
    public function getServices($vendor_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_vendor_services WHERE vendor_id = ? ORDER BY service_id DESC", [$vendor_id]);
        return $query->getResultArray();
    }

    // ==============================
    // GET SINGLE SERVICE
    // ==============================
    public function getService($service_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_vendor_services WHERE service_id = ?", [$service_id]);
        return $query->getRowArray();
    }

    // ==============================
    // GET VENDOR WITH SERVICES (for trip assignment)
    // ==============================
    public function getVendorWithServices($vendor_id)
    {
        $vendor = $this->db->query("SELECT * FROM tbl_vendors WHERE vendor_id = ?", [$vendor_id])->getRowArray();
        if($vendor) {
            $vendor['services'] = $this->getServices($vendor_id);
        }
        return $vendor;
    }
}