<?php
namespace App\Models;
use CodeIgniter\Model;

class RangeAssistantsModel extends Model
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function saveAssistant() 
    { 
        $full_name = $this->request->getPost('full_name');
        $badge_number = $this->request->getPost('badge_number');
        $position = $this->request->getPost('position');
        $status = $this->request->getPost('status') ?: 'ACTIVE';

        // Check if badge number already exists
        if($badge_number) {
            $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_range_assistants WHERE badge_number = ?", [$badge_number])->getRow();
            if($check->count > 0) {
                return ['status' => 'error', 'message' => 'Badge Number already exists!'];
            }
        }

        $query = $this->db->query("
            INSERT INTO `tbl_range_assistants`(
                `full_name`,
                `badge_number`,
                `position`,
                `status`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?)", 
            [
                $full_name,
                $badge_number,
                $position,
                $status,
                $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Range Assistant Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        }
    }

    public function updateAssistant() 
    { 
        $assistant_id = $this->request->getPost('assistant_id');
        $full_name = $this->request->getPost('full_name');
        $badge_number = $this->request->getPost('badge_number');
        $position = $this->request->getPost('position');
        $status = $this->request->getPost('status') ?: 'ACTIVE';

        // Check if badge number already exists for a different assistant
        if($badge_number) {
            $check = $this->db->query("SELECT COUNT(*) as count FROM tbl_range_assistants WHERE badge_number = ? AND assistant_id != ?", [$badge_number, $assistant_id])->getRow();
            if($check->count > 0) {
                return ['status' => 'error', 'message' => 'Badge Number already exists!'];
            }
        }

        $query = $this->db->query("
            UPDATE `tbl_range_assistants`
            SET 
                `full_name` = ?,
                `badge_number` = ?,
                `position` = ?,
                `status` = ?,
                `updated_at` = NOW()
            WHERE `assistant_id` = ?
            ", 
            [
                $full_name,
                $badge_number,
                $position,
                $status,
                $assistant_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Range Assistant Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    public function deleteAssistant() 
    { 
        $assistant_id = $this->request->getPost('assistant_id');

        $query = $this->db->query("DELETE FROM `tbl_range_assistants` WHERE `assistant_id` = ?", [$assistant_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Range Assistant Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }
}