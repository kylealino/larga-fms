<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Helper_Model extends Model
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
    // GENERATE HELPER CODE
    // ==============================
    private function generateHelperCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_helpers WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'HLP-' . $year . '-';
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ==============================
    // UPLOAD PROFILE PICTURE
    // ==============================
    private function uploadProfilePicture($helper_id)
    {
        $file = $this->request->getFile('profile_picture');
        $filename = '';
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'HLP_' . $helper_id . '_' . time() . '.' . $file->getExtension();
            $uploadPath = ROOTPATH . 'public/uploads/helpers/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $newName);
            $filename = 'uploads/helpers/' . $newName;
        }
        
        return $filename;
    }

    // ==============================
    // UPLOAD LICENSE ATTACHMENT
    // ==============================
    private function uploadLicenseAttachment($helper_id)
    {
        $file = $this->request->getFile('license_attachment');
        $filename = '';
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'HLP_LIC_' . $helper_id . '_' . time() . '.' . $file->getExtension();
            $uploadPath = ROOTPATH . 'public/uploads/helper_licenses/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $newName);
            $filename = 'uploads/helper_licenses/' . $newName;
        }
        
        return $filename;
    }

    // ==============================
    // SAVE HELPER
    // ==============================
    public function saveHelper() 
    { 
        $helper_code = $this->generateHelperCode();
        $helper_name = $this->request->getPost('helper_name');
        $contact_number = $this->request->getPost('contact_number');
        $address = $this->request->getPost('address');
        $employment_type = $this->request->getPost('employment_type');
        $date_hired = $this->request->getPost('date_hired');
        $helper_status = $this->request->getPost('helper_status') ?: 'AVAILABLE';
        $emergency_contact = $this->request->getPost('emergency_contact');
        $emergency_contact_number = $this->request->getPost('emergency_contact_number');
        
        // License fields (optional)
        $license_number = $this->request->getPost('license_number');
        $license_type = $this->request->getPost('license_type');
        $restriction_code = $this->request->getPost('restriction_code');
        $expiration_date = $this->request->getPost('expiration_date');
        $has_license = (!empty($license_number)) ? 'YES' : 'NO';

        // Auto-calculate license status
        $license_status = 'NONE';
        if($has_license == 'YES') {
            $license_status = 'VALID';
            if($expiration_date) {
                $today = new \DateTime();
                $expiry = new \DateTime($expiration_date);
                $daysDiff = $today->diff($expiry)->days;
                if($expiry < $today) {
                    $license_status = 'EXPIRED';
                } elseif($daysDiff <= 30) {
                    $license_status = 'EXPIRING';
                }
            }
        }

        // Check if helper name already exists
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_helpers WHERE helper_name = ?", [$helper_name])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Helper name already exists!'];
        }

        $this->db->transStart();

        // Insert Helper
        $query = $this->db->query("
            INSERT INTO `tbl_helpers`(
                `helper_code`,
                `helper_name`,
                `contact_number`,
                `address`,
                `employment_type`,
                `date_hired`,
                `helper_status`,
                `emergency_contact`,
                `emergency_contact_number`,
                `license_number`,
                `license_type`,
                `restriction_code`,
                `expiration_date`,
                `license_status`,
                `has_license`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $helper_code,
                $helper_name,
                $contact_number,
                $address,
                $employment_type,
                $date_hired,
                $helper_status,
                $emergency_contact,
                $emergency_contact_number,
                $license_number,
                $license_type,
                $restriction_code,
                $expiration_date,
                $license_status,
                $has_license,
                $this->cuser
            ]
        );

        $helper_id = $this->db->insertID();

        // Upload profile picture
        if($query && $helper_id) {
            $profile_pic = $this->uploadProfilePicture($helper_id);
            if($profile_pic) {
                $this->db->query("UPDATE tbl_helpers SET profile_picture = ? WHERE helper_id = ?", [$profile_pic, $helper_id]);
            }
            
            // Upload license attachment if license exists
            if($has_license == 'YES') {
                $attachment_path = $this->uploadLicenseAttachment($helper_id);
                if($attachment_path) {
                    $this->db->query("UPDATE tbl_helpers SET license_attachment = ? WHERE helper_id = ?", [$attachment_path, $helper_id]);
                }
            }
        }

        if ($query && $helper_id) {
            $this->db->transComplete();
            return ['status' => 'success', 'message' => 'Helper Saved Successfully!', 'helper_id' => $helper_id];
        } else {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        }
    }

    // ==============================
    // UPDATE HELPER
    // ==============================
    public function updateHelper() 
    { 
        $helper_id = $this->request->getPost('helper_id');
        $helper_name = $this->request->getPost('helper_name');
        $contact_number = $this->request->getPost('contact_number');
        $address = $this->request->getPost('address');
        $employment_type = $this->request->getPost('employment_type');
        $date_hired = $this->request->getPost('date_hired');
        $helper_status = $this->request->getPost('helper_status') ?: 'AVAILABLE';
        $emergency_contact = $this->request->getPost('emergency_contact');
        $emergency_contact_number = $this->request->getPost('emergency_contact_number');
        
        // License fields (optional)
        $license_number = $this->request->getPost('license_number');
        $license_type = $this->request->getPost('license_type');
        $restriction_code = $this->request->getPost('restriction_code');
        $expiration_date = $this->request->getPost('expiration_date');
        $has_license = (!empty($license_number)) ? 'YES' : 'NO';

        // Auto-calculate license status
        $license_status = 'NONE';
        if($has_license == 'YES') {
            $license_status = 'VALID';
            if($expiration_date) {
                $today = new \DateTime();
                $expiry = new \DateTime($expiration_date);
                $daysDiff = $today->diff($expiry)->days;
                if($expiry < $today) {
                    $license_status = 'EXPIRED';
                } elseif($daysDiff <= 30) {
                    $license_status = 'EXPIRING';
                }
            }
        }

        // Check if helper name already exists for a different helper
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_helpers WHERE helper_name = ? AND helper_id != ?", [$helper_name, $helper_id])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Helper name already exists!'];
        }

        // Get existing helper data for file management
        $existing = $this->db->query("SELECT profile_picture, license_attachment FROM tbl_helpers WHERE helper_id = ?", [$helper_id])->getRowArray();
        $profile_pic = $existing ? $existing['profile_picture'] : '';
        $license_attachment = $existing ? $existing['license_attachment'] : '';

        // Handle profile picture upload
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old profile picture if exists
            if($profile_pic && file_exists(ROOTPATH . 'public/' . $profile_pic)) {
                unlink(ROOTPATH . 'public/' . $profile_pic);
            }
            $profile_pic = $this->uploadProfilePicture($helper_id);
        }

        // Handle license attachment upload
        $file_lic = $this->request->getFile('license_attachment');
        if ($file_lic && $file_lic->isValid() && !$file_lic->hasMoved()) {
            // Delete old license attachment if exists
            if($license_attachment && file_exists(ROOTPATH . 'public/' . $license_attachment)) {
                unlink(ROOTPATH . 'public/' . $license_attachment);
            }
            $license_attachment = $this->uploadLicenseAttachment($helper_id);
        }

        $query = $this->db->query("
            UPDATE `tbl_helpers`
            SET 
                `helper_name` = ?,
                `contact_number` = ?,
                `address` = ?,
                `employment_type` = ?,
                `date_hired` = ?,
                `helper_status` = ?,
                `emergency_contact` = ?,
                `emergency_contact_number` = ?,
                `profile_picture` = ?,
                `license_number` = ?,
                `license_type` = ?,
                `restriction_code` = ?,
                `expiration_date` = ?,
                `license_status` = ?,
                `has_license` = ?,
                `license_attachment` = ?,
                `updated_at` = NOW()
            WHERE `helper_id` = ?
            ", 
            [
                $helper_name,
                $contact_number,
                $address,
                $employment_type,
                $date_hired,
                $helper_status,
                $emergency_contact,
                $emergency_contact_number,
                $profile_pic,
                $license_number,
                $license_type,
                $restriction_code,
                $expiration_date,
                $license_status,
                $has_license,
                $license_attachment,
                $helper_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Helper Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    // ==============================
    // DELETE HELPER
    // ==============================
    public function deleteHelper() 
    { 
        $helper_id = $this->request->getPost('helper_id');

        // Get attachments to delete files
        $helper = $this->db->query("SELECT profile_picture, license_attachment FROM tbl_helpers WHERE helper_id = ?", [$helper_id])->getRowArray();
        
        if($helper) {
            if($helper['profile_picture'] && file_exists(ROOTPATH . 'public/' . $helper['profile_picture'])) {
                unlink(ROOTPATH . 'public/' . $helper['profile_picture']);
            }
            if($helper['license_attachment'] && file_exists(ROOTPATH . 'public/' . $helper['license_attachment'])) {
                unlink(ROOTPATH . 'public/' . $helper['license_attachment']);
            }
        }

        $query = $this->db->query("DELETE FROM `tbl_helpers` WHERE `helper_id` = ?", [$helper_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Helper Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }

    // ==============================
    // GET SINGLE HELPER (for edit)
    // ==============================
    public function getHelper($helper_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_helpers WHERE helper_id = ?", [$helper_id]);
        return $query->getRowArray();
    }

    // ==============================
    // GET HELPER DELIVERY HISTORY
    // ==============================
    public function getHelperHistory($helper_id)
    {
        $helper = $this->db->query("SELECT helper_name FROM tbl_helpers WHERE helper_id = ?", [$helper_id])->getRow();
        if (!$helper || !$helper->helper_name) return [];

        return $this->db->query("
            SELECT t.trip_code, t.origin, t.destination, t.actual_delivery_date,
                   c.customer_name,
                   a.truck_plate, a.tractor_plate, a.chassis_plate, a.driver_name, a.assignment_date,
                   dr.dr_code, dr.dr_status, dr.dr_date
            FROM tbl_trip_assignments a
            JOIN tbl_trips t ON a.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_delivery_receipts dr ON dr.trip_id = t.trip_id
            WHERE a.helper_name = ?
              AND t.trip_status = 'COMPLETED'
            ORDER BY t.actual_delivery_date DESC, a.assignment_id DESC
        ", [$helper->helper_name])->getResultArray();
    }
}