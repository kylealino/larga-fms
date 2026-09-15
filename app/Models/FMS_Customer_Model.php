<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Customer_Model extends Model
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
    // GENERATE CUSTOMER CODE
    // ==============================
    private function generateCustomerCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_customers WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'CUST-' . $year . '-';
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ==============================
    // SAVE CUSTOMER
    // ==============================
    public function saveCustomer() 
    { 
        $customer_code = $this->generateCustomerCode();
        $customer_name = $this->request->getPost('customer_name');
        $customer_type = $this->request->getPost('customer_type');
        $tin = $this->request->getPost('tin');
        $contact_person = $this->request->getPost('contact_person');
        $contact_position = $this->request->getPost('contact_position');
        $contact_number = $this->request->getPost('contact_number');
        $email_address = $this->request->getPost('email_address');
        $business_address = $this->request->getPost('business_address');
        $billing_address = $this->request->getPost('billing_address');
        $payment_terms = $this->request->getPost('payment_terms');
        $credit_limit = $this->request->getPost('credit_limit') ?: 0;
        $status = $this->request->getPost('status') ?: 'ACTIVE';
        $remarks = $this->request->getPost('remarks');

        // Check if customer name already exists
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_customers WHERE customer_name = ?", [$customer_name])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Customer name already exists!'];
        }

        $query = $this->db->query("
            INSERT INTO `tbl_customers`(
                `customer_code`,
                `customer_name`,
                `customer_type`,
                `tin`,
                `contact_person`,
                `contact_position`,
                `contact_number`,
                `email_address`,
                `business_address`,
                `billing_address`,
                `payment_terms`,
                `credit_limit`,
                `status`,
                `remarks`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $customer_code,
                $customer_name,
                $customer_type,
                $tin,
                $contact_person,
                $contact_position,
                $contact_number,
                $email_address,
                $business_address,
                $billing_address,
                $payment_terms,
                $credit_limit,
                $status,
                $remarks,
                $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Customer Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        }
    }

    // ==============================
    // UPDATE CUSTOMER
    // ==============================
    public function updateCustomer() 
    { 
        $customer_id = $this->request->getPost('customer_id');
        $customer_name = $this->request->getPost('customer_name');
        $customer_type = $this->request->getPost('customer_type');
        $tin = $this->request->getPost('tin');
        $contact_person = $this->request->getPost('contact_person');
        $contact_position = $this->request->getPost('contact_position');
        $contact_number = $this->request->getPost('contact_number');
        $email_address = $this->request->getPost('email_address');
        $business_address = $this->request->getPost('business_address');
        $billing_address = $this->request->getPost('billing_address');
        $payment_terms = $this->request->getPost('payment_terms');
        $credit_limit = $this->request->getPost('credit_limit') ?: 0;
        $status = $this->request->getPost('status') ?: 'ACTIVE';
        $remarks = $this->request->getPost('remarks');

        // Check if customer name already exists for a different customer
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_customers WHERE customer_name = ? AND customer_id != ?", [$customer_name, $customer_id])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Customer name already exists!'];
        }

        $query = $this->db->query("
            UPDATE `tbl_customers`
            SET 
                `customer_name` = ?,
                `customer_type` = ?,
                `tin` = ?,
                `contact_person` = ?,
                `contact_position` = ?,
                `contact_number` = ?,
                `email_address` = ?,
                `business_address` = ?,
                `billing_address` = ?,
                `payment_terms` = ?,
                `credit_limit` = ?,
                `status` = ?,
                `remarks` = ?,
                `updated_at` = NOW()
            WHERE `customer_id` = ?
            ", 
            [
                $customer_name,
                $customer_type,
                $tin,
                $contact_person,
                $contact_position,
                $contact_number,
                $email_address,
                $business_address,
                $billing_address,
                $payment_terms,
                $credit_limit,
                $status,
                $remarks,
                $customer_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Customer Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    // ==============================
    // DELETE CUSTOMER
    // ==============================
    public function deleteCustomer() 
    { 
        $customer_id = $this->request->getPost('customer_id');

        $query = $this->db->query("DELETE FROM `tbl_customers` WHERE `customer_id` = ?", [$customer_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Customer Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }

    // ==============================
    // SAVE CUSTOMER LOCATION
    // ==============================
    public function saveLocation() 
    { 
        $customer_id = $this->request->getPost('customer_id');
        $location_name = $this->request->getPost('location_name');
        $address = $this->request->getPost('address');
        $city = $this->request->getPost('city');
        $province = $this->request->getPost('province');
        $contact_person = $this->request->getPost('contact_person');
        $contact_number = $this->request->getPost('contact_number');
        $special_instructions = $this->request->getPost('special_instructions');
        $status = $this->request->getPost('status') ?: 'ACTIVE';

        $query = $this->db->query("
            INSERT INTO `tbl_customer_locations`(
                `customer_id`,
                `location_name`,
                `address`,
                `city`,
                `province`,
                `contact_person`,
                `contact_number`,
                `special_instructions`,
                `status`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $customer_id,
                $location_name,
                $address,
                $city,
                $province,
                $contact_person,
                $contact_number,
                $special_instructions,
                $status,
                $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Location Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving location.'];
        }
    }

    // ==============================
    // UPDATE CUSTOMER LOCATION
    // ==============================
    public function updateLocation() 
    { 
        $location_id = $this->request->getPost('location_id');
        $location_name = $this->request->getPost('location_name');
        $address = $this->request->getPost('address');
        $city = $this->request->getPost('city');
        $province = $this->request->getPost('province');
        $contact_person = $this->request->getPost('contact_person');
        $contact_number = $this->request->getPost('contact_number');
        $special_instructions = $this->request->getPost('special_instructions');
        $status = $this->request->getPost('status') ?: 'ACTIVE';

        $query = $this->db->query("
            UPDATE `tbl_customer_locations`
            SET 
                `location_name` = ?,
                `address` = ?,
                `city` = ?,
                `province` = ?,
                `contact_person` = ?,
                `contact_number` = ?,
                `special_instructions` = ?,
                `status` = ?,
                `updated_at` = NOW()
            WHERE `location_id` = ?
            ", 
            [
                $location_name,
                $address,
                $city,
                $province,
                $contact_person,
                $contact_number,
                $special_instructions,
                $status,
                $location_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Location Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating location.'];
        }
    }

    // ==============================
    // DELETE CUSTOMER LOCATION
    // ==============================
    public function deleteLocation() 
    { 
        $location_id = $this->request->getPost('location_id');

        $query = $this->db->query("DELETE FROM `tbl_customer_locations` WHERE `location_id` = ?", [$location_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Location Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting location.'];
        }
    }

    // ==============================
    // GET LOCATIONS BY CUSTOMER
    // ==============================
    public function getLocations($customer_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_customer_locations WHERE customer_id = ? ORDER BY location_id DESC", [$customer_id]);
        return $query->getResultArray();
    }

    // ==============================
    // GET SINGLE LOCATION
    // ==============================
    public function getLocation($location_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_customer_locations WHERE location_id = ?", [$location_id]);
        return $query->getRowArray();
    }

    // ==============================
    // GET SINGLE CUSTOMER (ADD THIS)
    // ==============================
    public function getCustomer($customer_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_customers WHERE customer_id = ?", [$customer_id]);
        return $query->getRowArray();
    }
}