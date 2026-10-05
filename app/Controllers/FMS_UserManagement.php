<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_UserManagement extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->userModel = model('App\Models\FMS_UserManagement_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
        helper('permission');
    }

    public function index()
    {
        $meaction = $this->request->getPostGet('meaction');

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        switch ($meaction) {
            case 'MAIN':
                return view('fms/usermanagement/usermanagement-main');
                break;

            // ==============================
            // USERS CRUD
            // ==============================
            case 'GET_ALL_USERS':
                try { $result = $this->userModel->getAllUsers(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_USER':
                $recid = $this->request->getPost('recid');
                try { $result = $this->userModel->getUser($recid); }
                catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'CREATE_USER':
                if (!user_can('usermanagement', 'add')) { echo json_encode(['status' => 'error', 'message' => 'You do not have permission to add users.']); break; }
                try { $result = $this->userModel->createUser(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_USER':
                if (!user_can('usermanagement', 'edit')) { echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit users.']); break; }
                try { $result = $this->userModel->updateUser(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_USER':
                if (!user_can('usermanagement', 'delete')) { echo json_encode(['status' => 'error', 'message' => 'You do not have permission to delete users.']); break; }
                try { $result = $this->userModel->deleteUser($this->cuser); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            // ==============================
            // ROLES CRUD
            // ==============================
            case 'GET_ROLES_DROPDOWN':
                try { $result = $this->userModel->getActiveRoles(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_ALL_ROLES':
                try { $result = $this->userModel->getAllRoles(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_ROLE':
                $role_id = $this->request->getPost('role_id');
                try { $result = $this->userModel->getRole($role_id); }
                catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'CREATE_ROLE':
                if (!user_can('usermanagement', 'add')) { echo json_encode(['status' => 'error', 'message' => 'You do not have permission to add roles.']); break; }
                try { $result = $this->userModel->createRole(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_ROLE':
                if (!user_can('usermanagement', 'edit')) { echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit roles.']); break; }
                try { $result = $this->userModel->updateRole(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_ROLE':
                if (!user_can('usermanagement', 'delete')) { echo json_encode(['status' => 'error', 'message' => 'You do not have permission to delete roles.']); break; }
                try { $result = $this->userModel->deleteRole(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            // ==============================
            // PERMISSION MATRIX
            // ==============================
            case 'GET_MODULES_MATRIX':
                $role_id = $this->request->getPost('role_id');
                try { $result = $this->userModel->getModulesMatrix($role_id); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'SAVE_ROLE_PERMISSIONS':
                if (!user_can('usermanagement', 'edit')) { echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit role permissions.']); break; }
                try { $result = $this->userModel->savePermissions(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            // ==============================
            // DASHBOARD WIDGET CUSTOMIZATION
            // ==============================
            case 'GET_DASHBOARD_WIDGETS':
                $role_id = $this->request->getPost('role_id');
                try { $result = $this->userModel->getDashboardWidgetsMatrix($role_id); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'SAVE_DASHBOARD_WIDGETS':
                if (!user_can('usermanagement', 'edit')) { echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit dashboard widgets.']); break; }
                try { $result = $this->userModel->saveDashboardWidgets(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            default:
                return view('fms/usermanagement/usermanagement-main');
                break;
        }
    }
}
