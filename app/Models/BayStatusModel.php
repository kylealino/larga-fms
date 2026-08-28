<?php
namespace App\Models;
use CodeIgniter\Model;

class BayStatusModel extends Model
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function saveBayStatus() 
    { 
        $stage_id = $this->request->getPost('stage_id');
        $status = $this->request->getPost('status');

        // Check if stage already exists
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_bay_status WHERE stage_id = ?", [$stage_id])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Stage ' . $stage_id . ' already exists!'];
        }

        $query = $this->db->query("
            INSERT INTO `tbl_bay_status`(
                `stage_id`,
                `status`
            )
            VALUES (?, ?)", 
            [
                $stage_id,
                $status
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Bay Status Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        }
    }

    public function updateBayStatus() 
    { 
        $bay_id = $this->request->getPost('bay_id');
        $stage_id = $this->request->getPost('stage_id');
        $status = $this->request->getPost('status');

        // Check if stage already exists for a different bay
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_bay_status WHERE stage_id = ? AND bay_id != ?", [$stage_id, $bay_id])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Stage ' . $stage_id . ' already exists!'];
        }

        $query = $this->db->query("
            UPDATE `tbl_bay_status`
            SET 
                `stage_id` = ?,
                `status` = ?,
                `updated_at` = NOW()
            WHERE `bay_id` = ?
            ", 
            [
                $stage_id,
                $status,
                $bay_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Bay Status Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    public function deleteBayStatus() 
    { 
        $bay_id = $this->request->getPost('bay_id');

        $query = $this->db->query("DELETE FROM `tbl_bay_status` WHERE `bay_id` = ?", [$bay_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Bay Status Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }

    public function addNewBay()
    {
        $stage_id = $this->request->getPost('stage_id');
        $status = $this->request->getPost('status') ?: 'AVAILABLE';

        // Check if stage already exists
        $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_bay_status WHERE stage_id = ?", [$stage_id])->getRow();
        if($check->count > 0) {
            return ['status' => 'error', 'message' => 'Stage ' . $stage_id . ' already exists!'];
        }

        $query = $this->db->query("
            INSERT INTO `tbl_bay_status`(
                `stage_id`,
                `status`
            )
            VALUES (?, ?)", 
            [
                $stage_id,
                $status
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Stage ' . $stage_id . ' added successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding the bay.'];
        }
    }
}