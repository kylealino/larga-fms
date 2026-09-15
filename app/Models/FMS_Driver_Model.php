<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Driver_Model extends Model
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
    // GENERATE DRIVER CODE
    // ==============================
    private function generateDriverCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_drivers WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        $prefix = 'DRV-' . $year . '-';
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ==============================
    // UPLOAD PROFILE PICTURE
    // ==============================
    private function uploadProfilePicture($driver_id)
    {
        $file = $this->request->getFile('profile_picture');
        $filename = '';
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'DRV_' . $driver_id . '_' . time() . '.' . $file->getExtension();
            $uploadPath = ROOTPATH . 'public/uploads/drivers/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $newName);
            $filename = 'uploads/drivers/' . $newName;
        }
        
        return $filename;
    }

    // ==============================
    // UPLOAD LICENSE ATTACHMENT
    // ==============================
    private function uploadLicenseAttachment($driver_id)
    {
        $file = $this->request->getFile('license_attachment');
        $filename = '';
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'DRV_LIC_' . $driver_id . '_' . time() . '.' . $file->getExtension();
            $uploadPath = ROOTPATH . 'public/uploads/driver_licenses/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $newName);
            $filename = 'uploads/driver_licenses/' . $newName;
        }
        
        return $filename;
    }

    // ==============================
    // SAVE DRIVER
    // ==============================
    public function saveDriver() 
    { 
        $driver_code = $this->generateDriverCode();
        $driver_name = $this->request->getPost('driver_name');
        $contact_number = $this->request->getPost('contact_number');
        $address = $this->request->getPost('address');
        $employment_type = $this->request->getPost('employment_type');
        $date_hired = $this->request->getPost('date_hired');
        $driver_status = $this->request->getPost('driver_status') ?: 'AVAILABLE';
        $emergency_contact = $this->request->getPost('emergency_contact');
        $emergency_contact_number = $this->request->getPost('emergency_contact_number');
        
        // Qualifications & Experience
        $years_experience = $this->request->getPost('years_experience') ?: 0;
        $heavy_vehicle_experience = $this->request->getPost('heavy_vehicle_experience') ?: 0;
        $tractor_head_experience = $this->request->getPost('tractor_head_experience') ?: 0;
        $ten_wheeler_experience = $this->request->getPost('ten_wheeler_experience') ?: 0;
        $long_distance_experience = $this->request->getPost('long_distance_experience') ?: 'NO';
        $city_urban_experience = $this->request->getPost('city_urban_experience') ?: 'NO';
        $highway_experience = $this->request->getPost('highway_experience') ?: 'NO';
        $route_experience = $this->request->getPost('route_experience');
        $cargo_handling_experience = $this->request->getPost('cargo_handling_experience');
        $defensive_driving_training = $this->request->getPost('defensive_driving_training') ?: 'NO';
        $safety_training = $this->request->getPost('safety_training') ?: 'NO';
        $other_certifications = $this->request->getPost('other_certifications');
        $training_expiration_date = $this->request->getPost('training_expiration_date');
        $qualification_remarks = $this->request->getPost('qualification_remarks');
        
        // Rating
        $overall_rating = $this->request->getPost('overall_rating') ?: 0;
        $rating_date = $this->request->getPost('rating_date');
        $evaluated_by = $this->request->getPost('evaluated_by');
        $evaluation_remarks = $this->request->getPost('evaluation_remarks');
        
        // License fields (optional)
        $license_number = $this->request->getPost('license_number');
        $license_type = $this->request->getPost('license_type');
        $restriction_code = $this->request->getPost('restriction_code');
        $issue_date = $this->request->getPost('issue_date');
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

        // Check if driver name already exists
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_drivers WHERE driver_name = ?", [$driver_name])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Driver name already exists!'];
        }

        $this->db->transStart();

        // Insert Driver
        $query = $this->db->query("
            INSERT INTO `tbl_drivers`(
                `driver_code`,
                `driver_name`,
                `contact_number`,
                `address`,
                `employment_type`,
                `date_hired`,
                `driver_status`,
                `emergency_contact`,
                `emergency_contact_number`,
                `years_experience`,
                `heavy_vehicle_experience`,
                `tractor_head_experience`,
                `ten_wheeler_experience`,
                `long_distance_experience`,
                `city_urban_experience`,
                `highway_experience`,
                `route_experience`,
                `cargo_handling_experience`,
                `defensive_driving_training`,
                `safety_training`,
                `other_certifications`,
                `training_expiration_date`,
                `qualification_remarks`,
                `overall_rating`,
                `rating_date`,
                `evaluated_by`,
                `evaluation_remarks`,
                `license_number`,
                `license_type`,
                `restriction_code`,
                `issue_date`,
                `expiration_date`,
                `license_status`,
                `has_license`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $driver_code,
                $driver_name,
                $contact_number,
                $address,
                $employment_type,
                $date_hired,
                $driver_status,
                $emergency_contact,
                $emergency_contact_number,
                $years_experience,
                $heavy_vehicle_experience,
                $tractor_head_experience,
                $ten_wheeler_experience,
                $long_distance_experience,
                $city_urban_experience,
                $highway_experience,
                $route_experience,
                $cargo_handling_experience,
                $defensive_driving_training,
                $safety_training,
                $other_certifications,
                $training_expiration_date,
                $qualification_remarks,
                $overall_rating,
                $rating_date,
                $evaluated_by,
                $evaluation_remarks,
                $license_number,
                $license_type,
                $restriction_code,
                $issue_date,
                $expiration_date,
                $license_status,
                $has_license,
                $this->cuser
            ]
        );

        $driver_id = $this->db->insertID();

        // Upload profile picture
        if($query && $driver_id) {
            $profile_pic = $this->uploadProfilePicture($driver_id);
            if($profile_pic) {
                $this->db->query("UPDATE tbl_drivers SET profile_picture = ? WHERE driver_id = ?", [$profile_pic, $driver_id]);
            }
            
            // Upload license attachment if license exists
            if($has_license == 'YES') {
                $attachment_path = $this->uploadLicenseAttachment($driver_id);
                if($attachment_path) {
                    $this->db->query("UPDATE tbl_drivers SET license_attachment = ? WHERE driver_id = ?", [$attachment_path, $driver_id]);
                }
            }
        }

        if ($query && $driver_id) {
            $this->db->transComplete();
            return ['status' => 'success', 'message' => 'Driver Saved Successfully!', 'driver_id' => $driver_id];
        } else {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        }
    }

    // ==============================
    // UPDATE DRIVER
    // ==============================
    public function updateDriver() 
    { 
        $driver_id = $this->request->getPost('driver_id');
        $driver_name = $this->request->getPost('driver_name');
        $contact_number = $this->request->getPost('contact_number');
        $address = $this->request->getPost('address');
        $employment_type = $this->request->getPost('employment_type');
        $date_hired = $this->request->getPost('date_hired');
        $driver_status = $this->request->getPost('driver_status') ?: 'AVAILABLE';
        $emergency_contact = $this->request->getPost('emergency_contact');
        $emergency_contact_number = $this->request->getPost('emergency_contact_number');
        
        // Qualifications & Experience
        $years_experience = $this->request->getPost('years_experience') ?: 0;
        $heavy_vehicle_experience = $this->request->getPost('heavy_vehicle_experience') ?: 0;
        $tractor_head_experience = $this->request->getPost('tractor_head_experience') ?: 0;
        $ten_wheeler_experience = $this->request->getPost('ten_wheeler_experience') ?: 0;
        $long_distance_experience = $this->request->getPost('long_distance_experience') ?: 'NO';
        $city_urban_experience = $this->request->getPost('city_urban_experience') ?: 'NO';
        $highway_experience = $this->request->getPost('highway_experience') ?: 'NO';
        $route_experience = $this->request->getPost('route_experience');
        $cargo_handling_experience = $this->request->getPost('cargo_handling_experience');
        $defensive_driving_training = $this->request->getPost('defensive_driving_training') ?: 'NO';
        $safety_training = $this->request->getPost('safety_training') ?: 'NO';
        $other_certifications = $this->request->getPost('other_certifications');
        $training_expiration_date = $this->request->getPost('training_expiration_date');
        $qualification_remarks = $this->request->getPost('qualification_remarks');
        
        // Rating
        $overall_rating = $this->request->getPost('overall_rating') ?: 0;
        $rating_date = $this->request->getPost('rating_date');
        $evaluated_by = $this->request->getPost('evaluated_by');
        $evaluation_remarks = $this->request->getPost('evaluation_remarks');
        
        // License fields (optional)
        $license_number = $this->request->getPost('license_number');
        $license_type = $this->request->getPost('license_type');
        $restriction_code = $this->request->getPost('restriction_code');
        $issue_date = $this->request->getPost('issue_date');
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

        // Check if driver name already exists for a different driver
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_drivers WHERE driver_name = ? AND driver_id != ?", [$driver_name, $driver_id])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Driver name already exists!'];
        }

        // Get existing driver data for file management
        $existing = $this->db->query("SELECT profile_picture, license_attachment FROM tbl_drivers WHERE driver_id = ?", [$driver_id])->getRowArray();
        $profile_pic = $existing ? $existing['profile_picture'] : '';
        $license_attachment = $existing ? $existing['license_attachment'] : '';

        // Handle profile picture upload
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old profile picture if exists
            if($profile_pic && file_exists(ROOTPATH . 'public/' . $profile_pic)) {
                unlink(ROOTPATH . 'public/' . $profile_pic);
            }
            $profile_pic = $this->uploadProfilePicture($driver_id);
        }

        // Handle license attachment upload
        $file_lic = $this->request->getFile('license_attachment');
        if ($file_lic && $file_lic->isValid() && !$file_lic->hasMoved()) {
            // Delete old license attachment if exists
            if($license_attachment && file_exists(ROOTPATH . 'public/' . $license_attachment)) {
                unlink(ROOTPATH . 'public/' . $license_attachment);
            }
            $license_attachment = $this->uploadLicenseAttachment($driver_id);
        }

        $query = $this->db->query("
            UPDATE `tbl_drivers`
            SET 
                `driver_name` = ?,
                `contact_number` = ?,
                `address` = ?,
                `employment_type` = ?,
                `date_hired` = ?,
                `driver_status` = ?,
                `emergency_contact` = ?,
                `emergency_contact_number` = ?,
                `profile_picture` = ?,
                `years_experience` = ?,
                `heavy_vehicle_experience` = ?,
                `tractor_head_experience` = ?,
                `ten_wheeler_experience` = ?,
                `long_distance_experience` = ?,
                `city_urban_experience` = ?,
                `highway_experience` = ?,
                `route_experience` = ?,
                `cargo_handling_experience` = ?,
                `defensive_driving_training` = ?,
                `safety_training` = ?,
                `other_certifications` = ?,
                `training_expiration_date` = ?,
                `qualification_remarks` = ?,
                `overall_rating` = ?,
                `rating_date` = ?,
                `evaluated_by` = ?,
                `evaluation_remarks` = ?,
                `license_number` = ?,
                `license_type` = ?,
                `restriction_code` = ?,
                `issue_date` = ?,
                `expiration_date` = ?,
                `license_status` = ?,
                `has_license` = ?,
                `license_attachment` = ?,
                `updated_at` = NOW()
            WHERE `driver_id` = ?
            ", 
            [
                $driver_name,
                $contact_number,
                $address,
                $employment_type,
                $date_hired,
                $driver_status,
                $emergency_contact,
                $emergency_contact_number,
                $profile_pic,
                $years_experience,
                $heavy_vehicle_experience,
                $tractor_head_experience,
                $ten_wheeler_experience,
                $long_distance_experience,
                $city_urban_experience,
                $highway_experience,
                $route_experience,
                $cargo_handling_experience,
                $defensive_driving_training,
                $safety_training,
                $other_certifications,
                $training_expiration_date,
                $qualification_remarks,
                $overall_rating,
                $rating_date,
                $evaluated_by,
                $evaluation_remarks,
                $license_number,
                $license_type,
                $restriction_code,
                $issue_date,
                $expiration_date,
                $license_status,
                $has_license,
                $license_attachment,
                $driver_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Driver Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    // ==============================
    // DELETE DRIVER
    // ==============================
    public function deleteDriver() 
    { 
        $driver_id = $this->request->getPost('driver_id');

        // Get attachments to delete files
        $driver = $this->db->query("SELECT profile_picture, license_attachment FROM tbl_drivers WHERE driver_id = ?", [$driver_id])->getRowArray();
        
        if($driver) {
            if($driver['profile_picture'] && file_exists(ROOTPATH . 'public/' . $driver['profile_picture'])) {
                unlink(ROOTPATH . 'public/' . $driver['profile_picture']);
            }
            if($driver['license_attachment'] && file_exists(ROOTPATH . 'public/' . $driver['license_attachment'])) {
                unlink(ROOTPATH . 'public/' . $driver['license_attachment']);
            }
        }

        $query = $this->db->query("DELETE FROM `tbl_drivers` WHERE `driver_id` = ?", [$driver_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Driver Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }

    // ==============================
    // GET SINGLE DRIVER (for view)
    // ==============================
    public function getDriver($driver_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_drivers WHERE driver_id = ?", [$driver_id]);
        return $query->getRowArray();
    }

    // ==============================
    // SAVE DRIVER SKILLSET
    // ==============================
    public function saveSkillset() 
    { 
        $driver_id = $this->request->getPost('driver_id');
        $skill_name = $this->request->getPost('skill_name');
        $rating = $this->request->getPost('rating');
        $rating_date = $this->request->getPost('rating_date') ?: date('Y-m-d');
        $evaluated_by = $this->request->getPost('evaluated_by');
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_driver_skillsets`(
                `driver_id`,
                `skill_name`,
                `rating`,
                `rating_date`,
                `evaluated_by`,
                `remarks`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)", 
            [
                $driver_id,
                $skill_name,
                $rating,
                $rating_date,
                $evaluated_by,
                $remarks,
                $this->cuser
            ]
        );

        // Update overall rating
        if ($query) {
            $this->calculateOverallRating($driver_id);
            return ['status' => 'success', 'message' => 'Skillset Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving skillset.'];
        }
    }

    // ==============================
    // UPDATE DRIVER SKILLSET
    // ==============================
    public function updateSkillset() 
    { 
        $skillset_id = $this->request->getPost('skillset_id');
        $driver_id = $this->request->getPost('driver_id');
        $skill_name = $this->request->getPost('skill_name');
        $rating = $this->request->getPost('rating');
        $rating_date = $this->request->getPost('rating_date') ?: date('Y-m-d');
        $evaluated_by = $this->request->getPost('evaluated_by');
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_driver_skillsets`
            SET 
                `skill_name` = ?,
                `rating` = ?,
                `rating_date` = ?,
                `evaluated_by` = ?,
                `remarks` = ?,
                `updated_at` = NOW()
            WHERE `skillset_id` = ?
            ", 
            [
                $skill_name,
                $rating,
                $rating_date,
                $evaluated_by,
                $remarks,
                $skillset_id
            ]
        );

        if ($query) {
            $this->calculateOverallRating($driver_id);
            return ['status' => 'success', 'message' => 'Skillset Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating skillset.'];
        }
    }

    // ==============================
    // DELETE DRIVER SKILLSET
    // ==============================
    public function deleteSkillset() 
    { 
        $skillset_id = $this->request->getPost('skillset_id');
        $driver_id = $this->request->getPost('driver_id');

        $query = $this->db->query("DELETE FROM `tbl_driver_skillsets` WHERE `skillset_id` = ?", [$skillset_id]);

        if ($query) {
            $this->calculateOverallRating($driver_id);
            return ['status' => 'success', 'message' => 'Skillset Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting skillset.'];
        }
    }

    // ==============================
    // GET SKILLSETS BY DRIVER
    // ==============================
    public function getSkillsets($driver_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_driver_skillsets WHERE driver_id = ? ORDER BY skillset_id DESC", [$driver_id]);
        return $query->getResultArray();
    }

    // ==============================
    // GET SINGLE SKILLSET
    // ==============================
    public function getSkillset($skillset_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_driver_skillsets WHERE skillset_id = ?", [$skillset_id]);
        return $query->getRowArray();
    }

    // ==============================
    // CALCULATE OVERALL RATING
    // ==============================
    public function calculateOverallRating($driver_id)
    {
        $query = $this->db->query("SELECT AVG(rating) as avg_rating FROM tbl_driver_skillsets WHERE driver_id = ?", [$driver_id]);
        $avg = $query->getRow()->avg_rating;
        
        $overall_rating = $avg ? round($avg, 1) : 0;
        
        $this->db->query("UPDATE tbl_drivers SET overall_rating = ? WHERE driver_id = ?", [$overall_rating, $driver_id]);
        
        return $overall_rating;
    }
}