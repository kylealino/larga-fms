<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_UserManagement_Model extends Model
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
    // GENERATE ROLE CODE
    // ==============================
    private function generateRoleCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(role_code, '-', -1) AS UNSIGNED)), 0) as total FROM tbl_roles WHERE role_code LIKE ?", ['%-' . $year . '-%']);
        $count = $query->getRow()->total + 1;
        return 'ROLE-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // USERS — LIST / GET
    // ==============================
    public function getAllUsers()
    {
        return $this->db->query("
            SELECT u.recid, u.username, u.full_name, u.division, u.section, u.position,
                   u.role_id, r.role_name, u.is_active, u.added_at, u.added_by, u.updated_at
            FROM myua_user u
            LEFT JOIN tbl_roles r ON u.role_id = r.role_id
            ORDER BY u.full_name ASC
        ")->getResultArray();
    }

    public function getUser($recid)
    {
        $query = $this->db->query("
            SELECT u.recid, u.username, u.full_name, u.division, u.section, u.position,
                   u.role_id, r.role_name, u.is_active
            FROM myua_user u
            LEFT JOIN tbl_roles r ON u.role_id = r.role_id
            WHERE u.recid = ?
        ", [$recid]);
        return $query->getRowArray();
    }

    // ==============================
    // USERS — CREATE
    // ==============================
    public function createUser()
    {
        $username = trim($this->request->getPost('username'));
        $password = $this->request->getPost('password');
        $full_name = $this->request->getPost('full_name');
        $division = $this->request->getPost('division') ?: '';
        $section = $this->request->getPost('section') ?: '';
        $position = $this->request->getPost('position') ?: '';
        $role_id = $this->request->getPost('role_id') ?: null;
        $is_active = $this->request->getPost('is_active') !== null ? (int) $this->request->getPost('is_active') : 1;

        if (!$username || !$password || !$full_name) {
            return ['status' => 'error', 'message' => 'Username, password, and full name are required.'];
        }

        $existing = $this->db->query("SELECT recid FROM myua_user WHERE username = ? LIMIT 1", [$username])->getRow();
        if ($existing) {
            return ['status' => 'error', 'message' => 'That username is already taken.'];
        }

        $query = $this->db->query("
            INSERT INTO `myua_user`(
                `username`, `hash_password`, `hash_value`, `full_name`, `division`, `section`, `position`,
                `role_id`, `cert_tag`, `is_ppmp_signatory`, `added_by`, `is_active`
            ) VALUES (?, SHA2(?, 512), '', ?, ?, ?, ?, ?, 0, 0, ?, ?)
        ", [$username, $password, $full_name, $division, $section, $position, $role_id, $this->cuser, $is_active]);

        if ($query) {
            return ['status' => 'success', 'message' => 'User Created Successfully!', 'recid' => $this->db->insertID()];
        } else {
            $error = $this->db->error();
            log_message('error', 'User Create Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while creating the user.'];
        }
    }

    // ==============================
    // USERS — UPDATE
    // ==============================
    public function updateUser()
    {
        $recid = $this->request->getPost('recid');
        $username = trim($this->request->getPost('username'));
        $password = $this->request->getPost('password');
        $full_name = $this->request->getPost('full_name');
        $division = $this->request->getPost('division') ?: '';
        $section = $this->request->getPost('section') ?: '';
        $position = $this->request->getPost('position') ?: '';
        $role_id = $this->request->getPost('role_id') ?: null;
        $is_active = $this->request->getPost('is_active') !== null ? (int) $this->request->getPost('is_active') : 1;

        if (!$recid || !$username || !$full_name) {
            return ['status' => 'error', 'message' => 'Username and full name are required.'];
        }

        $existing = $this->db->query("SELECT recid FROM myua_user WHERE username = ? AND recid != ? LIMIT 1", [$username, $recid])->getRow();
        if ($existing) {
            return ['status' => 'error', 'message' => 'That username is already taken.'];
        }

        if (!empty($password)) {
            $query = $this->db->query("
                UPDATE `myua_user`
                SET `username` = ?, `hash_password` = SHA2(?, 512), `full_name` = ?, `division` = ?,
                    `section` = ?, `position` = ?, `role_id` = ?, `is_active` = ?
                WHERE `recid` = ?
            ", [$username, $password, $full_name, $division, $section, $position, $role_id, $is_active, $recid]);
        } else {
            $query = $this->db->query("
                UPDATE `myua_user`
                SET `username` = ?, `full_name` = ?, `division` = ?,
                    `section` = ?, `position` = ?, `role_id` = ?, `is_active` = ?
                WHERE `recid` = ?
            ", [$username, $full_name, $division, $section, $position, $role_id, $is_active, $recid]);
        }

        if ($query) {
            return ['status' => 'success', 'message' => 'User Updated Successfully!'];
        } else {
            $error = $this->db->error();
            log_message('error', 'User Update Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while updating the user.'];
        }
    }

    // ==============================
    // USERS — DELETE
    // ==============================
    public function deleteUser($cuser)
    {
        $recid = $this->request->getPost('recid');

        $target = $this->db->query("SELECT username FROM myua_user WHERE recid = ?", [$recid])->getRow();
        if (!$target) {
            return ['status' => 'error', 'message' => 'User not found.'];
        }
        if (strcasecmp($target->username, (string) $cuser) === 0) {
            return ['status' => 'error', 'message' => 'You cannot delete your own account.'];
        }

        $query = $this->db->query("DELETE FROM `myua_user` WHERE `recid` = ?", [$recid]);

        if ($query) {
            return ['status' => 'success', 'message' => 'User Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting the user.'];
        }
    }

    // ==============================
    // ROLES — LIST / GET
    // ==============================
    public function getActiveRoles()
    {
        return $this->db->query("SELECT role_id, role_name FROM tbl_roles WHERE role_status = 'ACTIVE' ORDER BY role_name ASC")->getResultArray();
    }

    public function getAllRoles()
    {
        return $this->db->query("
            SELECT r.role_id, r.role_code, r.role_name, r.description, r.role_status, r.created_at,
                   (SELECT COUNT(*) FROM myua_user u WHERE u.role_id = r.role_id) AS user_count
            FROM tbl_roles r
            ORDER BY r.role_id ASC
        ")->getResultArray();
    }

    public function getRole($role_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_roles WHERE role_id = ?", [$role_id]);
        return $query->getRowArray();
    }

    // ==============================
    // ROLES — CREATE
    // ==============================
    public function createRole()
    {
        $role_name = trim($this->request->getPost('role_name'));
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status') ?: 'ACTIVE';

        if (!$role_name) {
            return ['status' => 'error', 'message' => 'Role name is required.'];
        }

        $existing = $this->db->query("SELECT role_id FROM tbl_roles WHERE role_name = ? LIMIT 1", [$role_name])->getRow();
        if ($existing) {
            return ['status' => 'error', 'message' => 'A role with that name already exists.'];
        }

        $role_code = $this->generateRoleCode();

        $query = $this->db->query("
            INSERT INTO `tbl_roles`(`role_code`, `role_name`, `description`, `role_status`, `created_by`)
            VALUES (?, ?, ?, ?, ?)
        ", [$role_code, $role_name, $description, $status, $this->cuser]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Role Created Successfully!', 'role_id' => $this->db->insertID()];
        } else {
            $error = $this->db->error();
            log_message('error', 'Role Create Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while creating the role.'];
        }
    }

    // ==============================
    // ROLES — UPDATE
    // ==============================
    public function updateRole()
    {
        $role_id = $this->request->getPost('role_id');
        $role_name = trim($this->request->getPost('role_name'));
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status') ?: 'ACTIVE';

        if (!$role_id || !$role_name) {
            return ['status' => 'error', 'message' => 'Role name is required.'];
        }

        $existing = $this->db->query("SELECT role_id FROM tbl_roles WHERE role_name = ? AND role_id != ? LIMIT 1", [$role_name, $role_id])->getRow();
        if ($existing) {
            return ['status' => 'error', 'message' => 'A role with that name already exists.'];
        }

        $query = $this->db->query("
            UPDATE `tbl_roles` SET `role_name` = ?, `description` = ?, `role_status` = ?, `updated_at` = NOW()
            WHERE `role_id` = ?
        ", [$role_name, $description, $status, $role_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Role Updated Successfully!'];
        } else {
            $error = $this->db->error();
            log_message('error', 'Role Update Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while updating the role.'];
        }
    }

    // ==============================
    // ROLES — DELETE
    // ==============================
    public function deleteRole()
    {
        $role_id = $this->request->getPost('role_id');

        $role = $this->db->query("SELECT role_name FROM tbl_roles WHERE role_id = ?", [$role_id])->getRow();
        if (!$role) {
            return ['status' => 'error', 'message' => 'Role not found.'];
        }
        if (strcasecmp($role->role_name, 'Administrator') === 0) {
            return ['status' => 'error', 'message' => 'The Administrator role cannot be deleted.'];
        }

        $inUse = $this->db->query("SELECT COUNT(*) as total FROM myua_user WHERE role_id = ?", [$role_id])->getRow()->total;
        if ($inUse > 0) {
            return ['status' => 'error', 'message' => 'Cannot delete a role that is still assigned to ' . $inUse . ' user(s).'];
        }

        $this->db->query("DELETE FROM `tbl_role_permissions` WHERE `role_id` = ?", [$role_id]);
        $query = $this->db->query("DELETE FROM `tbl_roles` WHERE `role_id` = ?", [$role_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Role Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting the role.'];
        }
    }

    // ==============================
    // PERMISSION MATRIX — GET (modules + this role's current flags)
    // ==============================
    public function getModulesMatrix($role_id)
    {
        return $this->db->query("
            SELECT m.module_id, m.module_key, m.module_name, m.module_group,
                   COALESCE(p.can_view, 0) AS can_view,
                   COALESCE(p.can_add, 0) AS can_add,
                   COALESCE(p.can_edit, 0) AS can_edit,
                   COALESCE(p.can_delete, 0) AS can_delete
            FROM tbl_modules m
            LEFT JOIN tbl_role_permissions p ON p.module_id = m.module_id AND p.role_id = ?
            WHERE m.module_status = 'ACTIVE'
            ORDER BY m.sort_order ASC
        ", [$role_id])->getResultArray();
    }

    // ==============================
    // PERMISSION MATRIX — SAVE
    // permissions_json: { "<module_id>": { "view":0/1, "add":0/1, "edit":0/1, "delete":0/1 }, ... }
    // ==============================
    public function savePermissions()
    {
        $role_id = $this->request->getPost('role_id');
        $permissions = json_decode((string) $this->request->getPost('permissions_json'), true);

        if (!$role_id || !is_array($permissions)) {
            return ['status' => 'error', 'message' => 'Missing role or permission data.'];
        }

        $role = $this->db->query("SELECT role_id FROM tbl_roles WHERE role_id = ?", [$role_id])->getRow();
        if (!$role) {
            return ['status' => 'error', 'message' => 'Role not found.'];
        }

        $this->db->transStart();

        $this->db->query("DELETE FROM `tbl_role_permissions` WHERE `role_id` = ?", [$role_id]);

        foreach ($permissions as $module_id => $flags) {
            $can_view = !empty($flags['view']) ? 1 : 0;
            $can_add = !empty($flags['add']) ? 1 : 0;
            $can_edit = !empty($flags['edit']) ? 1 : 0;
            $can_delete = !empty($flags['delete']) ? 1 : 0;

            if (!$can_view && !$can_add && !$can_edit && !$can_delete) {
                continue;
            }

            $this->db->query("
                INSERT INTO `tbl_role_permissions`(`role_id`, `module_id`, `can_view`, `can_add`, `can_edit`, `can_delete`)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [$role_id, (int) $module_id, $can_view, $can_add, $can_edit, $can_delete]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus()) {
            return ['status' => 'success', 'message' => 'Permissions Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving permissions.'];
        }
    }

    // ==============================
    // DASHBOARD WIDGETS — GET (widgets + this role's current visibility,
    // falling back to each widget's default_visible when unset)
    // ==============================
    public function getDashboardWidgetsMatrix($role_id)
    {
        return $this->db->query("
            SELECT w.widget_id, w.widget_key, w.widget_name, w.widget_group, w.description,
                   COALESCE(r.is_visible, w.default_visible) AS is_visible
            FROM tbl_dashboard_widgets w
            LEFT JOIN tbl_role_dashboard_widgets r ON r.widget_id = w.widget_id AND r.role_id = ?
            WHERE w.widget_status = 'ACTIVE'
            ORDER BY w.sort_order ASC
        ", [$role_id])->getResultArray();
    }

    // ==============================
    // DASHBOARD WIDGETS — SAVE
    // widgets_json: { "<widget_id>": 0|1, ... } — every widget the matrix showed
    // is expected, so the role's customization is always fully explicit.
    // ==============================
    public function saveDashboardWidgets()
    {
        $role_id = $this->request->getPost('role_id');
        $widgets = json_decode((string) $this->request->getPost('widgets_json'), true);

        if (!$role_id || !is_array($widgets)) {
            return ['status' => 'error', 'message' => 'Missing role or widget data.'];
        }

        $role = $this->db->query("SELECT role_id FROM tbl_roles WHERE role_id = ?", [$role_id])->getRow();
        if (!$role) {
            return ['status' => 'error', 'message' => 'Role not found.'];
        }

        $this->db->transStart();

        $this->db->query("DELETE FROM `tbl_role_dashboard_widgets` WHERE `role_id` = ?", [$role_id]);

        foreach ($widgets as $widget_id => $is_visible) {
            $this->db->query("
                INSERT INTO `tbl_role_dashboard_widgets`(`role_id`, `widget_id`, `is_visible`)
                VALUES (?, ?, ?)
            ", [$role_id, (int) $widget_id, !empty($is_visible) ? 1 : 0]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus()) {
            return ['status' => 'success', 'message' => 'Dashboard Widgets Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving dashboard widgets.'];
        }
    }
}
