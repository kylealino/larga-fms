<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Dispatch_Model extends Model
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
    // GENERATE DISPATCH CODE
    // ==============================
    private function generateDispatchCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(dispatch_code, '-', -1) AS UNSIGNED)), 0) as total FROM tbl_dispatch WHERE dispatch_code LIKE ?", ['%-' . $year . '-%']);
        $count = $query->getRow()->total + 1;
        $prefix = 'DSP-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GENERATE CHECKLIST CODE
    // ==============================
    private function generateChecklistCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(checklist_code, '-', -1) AS UNSIGNED)), 0) as total FROM tbl_dispatch_checklist WHERE checklist_code LIKE ?", ['%-' . $year . '-%']);
        $count = $query->getRow()->total + 1;
        $prefix = 'CHK-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GENERATE EXPENSE CODE
    // ==============================
    private function generateExpenseCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(expense_code, '-', -1) AS UNSIGNED)), 0) as total FROM tbl_dispatch_expenses WHERE expense_code LIKE ?", ['%-' . $year . '-%']);
        $count = $query->getRow()->total + 1;
        $prefix = 'EXP-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GENERATE INSPECTION CODE
    // ==============================
    private function generateInspectionCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(inspection_code, '-', -1) AS UNSIGNED)), 0) as total FROM tbl_dispatch_inspections WHERE inspection_code LIKE ?", ['%-' . $year . '-%']);
        $count = $query->getRow()->total + 1;
        $prefix = 'INS-' . $year . '-';
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GET ASSIGNED TRIPS FOR DISPATCH
    // ==============================
    public function getAssignedTrips()
    {
        return $this->db->query("
            SELECT t.*, 
                   c.customer_name,
                   a.driver_name,
                   a.helper_name,
                   a.truck_plate,
                   a.tractor_plate,
                   a.chassis_plate,
                   a.vehicle_type,
                   a.vendor_name,
                   d.dispatch_id,
                   CASE WHEN d.dispatch_id IS NOT NULL THEN 1 ELSE 0 END as has_dispatch
            FROM tbl_trips t
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_trip_assignments a ON t.trip_id = a.trip_id
            LEFT JOIN tbl_dispatch d ON t.trip_id = d.trip_id
            WHERE t.trip_status IN ('ASSIGNED', 'DISPATCHED', 'IN_TRANSIT', 'DELIVERED', 'COMPLETED')
            ORDER BY t.scheduled_date DESC
        ")->getResultArray();
    }

    // ==============================
    // GET DISPATCH BY TRIP
    // ==============================
    public function getDispatchByTrip($trip_id)
    {
        $query = $this->db->query("
            SELECT d.*,
                   t.trip_code,
                   t.origin,
                   t.destination,
                   t.trip_status,
                   c.customer_name,
                   a.driver_name,
                   a.helper_name,
                   a.truck_plate,
                   a.tractor_plate,
                   a.chassis_plate,
                   a.vehicle_type,
                   a.vendor_name
            FROM tbl_dispatch d
            LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_trip_assignments a ON t.trip_id = a.trip_id
            WHERE d.trip_id = ?
            LIMIT 1
        ", [$trip_id]);
        return $query->getRowArray();
    }

    // ==============================
    // GET DISPATCH BY ID
    // ==============================
    public function getDispatch($dispatch_id)
    {
        $query = $this->db->query("
            SELECT d.*,
                   t.trip_code,
                   t.origin,
                   t.destination,
                   t.trip_status,
                   c.customer_name,
                   a.driver_name,
                   a.helper_name,
                   a.truck_plate,
                   a.tractor_plate,
                   a.chassis_plate,
                   a.vehicle_type,
                   a.vendor_name
            FROM tbl_dispatch d
            LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_trip_assignments a ON t.trip_id = a.trip_id
            WHERE d.dispatch_id = ?
        ", [$dispatch_id]);
        return $query->getRowArray();
    }

    // ==============================
    // CHECKLIST METHODS
    // ==============================
    public function getChecklistByDispatch($dispatch_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_dispatch_checklist 
            WHERE dispatch_id = ?
            ORDER BY checklist_id ASC
        ", [$dispatch_id])->getResultArray();
    }

    public function getChecklistItem($checklist_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_dispatch_checklist WHERE checklist_id = ?", [$checklist_id]);
        return $query->getRowArray();
    }

    public function saveChecklistItem()
    {
        $checklist_code = $this->generateChecklistCode();
        $dispatch_id = $this->request->getPost('dispatch_id');
        $trip_id = $this->request->getPost('trip_id');
        $item_name = $this->request->getPost('item_name');
        $available_quantity = $this->request->getPost('available_quantity') ?: 0;
        $required_quantity = $this->request->getPost('required_quantity') ?: 0;
        $condition_before = $this->request->getPost('condition_before') ?: 'GOOD';
        $checked = $this->request->getPost('checked') ?: 1;
        $accountable_person = $this->request->getPost('accountable_person');
        $checklist_status = $this->request->getPost('checklist_status') ?: 'COMPLETE';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_dispatch_checklist`(
                `checklist_code`, `dispatch_id`, `trip_id`, `item_name`,
                `available_quantity`, `required_quantity`, `condition_before`,
                `checked`, `accountable_person`, `checklist_status`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $checklist_code, $dispatch_id, $trip_id, $item_name,
                $available_quantity, $required_quantity, $condition_before,
                $checked, $accountable_person, $checklist_status, $remarks, $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Checklist Item Added Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding checklist item.'];
        }
    }

    public function updateChecklistItem()
    {
        $checklist_id = $this->request->getPost('checklist_id');
        $item_name = $this->request->getPost('item_name');
        $available_quantity = $this->request->getPost('available_quantity') ?: 0;
        $required_quantity = $this->request->getPost('required_quantity') ?: 0;
        $condition_before = $this->request->getPost('condition_before') ?: 'GOOD';
        $checked = $this->request->getPost('checked') ?: 1;
        $accountable_person = $this->request->getPost('accountable_person');
        $checklist_status = $this->request->getPost('checklist_status') ?: 'COMPLETE';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_dispatch_checklist`
            SET 
                `item_name` = ?, `available_quantity` = ?, `required_quantity` = ?,
                `condition_before` = ?, `checked` = ?, `accountable_person` = ?,
                `checklist_status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `checklist_id` = ?
            ",
            [
                $item_name, $available_quantity, $required_quantity,
                $condition_before, $checked, $accountable_person,
                $checklist_status, $remarks, $checklist_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Checklist Item Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating checklist item.'];
        }
    }

    public function deleteChecklistItem()
    {
        $checklist_id = $this->request->getPost('checklist_id');
        
        $query = $this->db->query("DELETE FROM `tbl_dispatch_checklist` WHERE `checklist_id` = ?", [$checklist_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Checklist Item Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting checklist item.'];
        }
    }

    // ==============================
    // EXPENSES METHODS
    // ==============================
    public function getExpensesByDispatch($dispatch_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_dispatch_expenses 
            WHERE dispatch_id = ?
            ORDER BY expense_date DESC
        ", [$dispatch_id])->getResultArray();
    }

    public function getExpense($expense_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_dispatch_expenses WHERE expense_id = ?", [$expense_id]);
        return $query->getRowArray();
    }

    public function saveExpense()
    {
        $expense_code = $this->generateExpenseCode();
        $dispatch_id = $this->request->getPost('dispatch_id');
        $trip_id = $this->request->getPost('trip_id');
        $expense_date = $this->request->getPost('expense_date') ?: date('Y-m-d');
        $expense_type = $this->request->getPost('expense_type');
        $description = $this->request->getPost('description');
        $amount = $this->request->getPost('amount') ?: 0;
        $paid_by = $this->request->getPost('paid_by');
        $reference_no = $this->request->getPost('reference_no');
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_dispatch_expenses`(
                `expense_code`, `dispatch_id`, `trip_id`, `expense_date`,
                `expense_type`, `description`, `amount`, `paid_by`,
                `reference_no`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $expense_code, $dispatch_id, $trip_id, $expense_date,
                $expense_type, $description, $amount, $paid_by,
                $reference_no, $remarks, $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Expense Added Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding expense.'];
        }
    }

    public function updateExpense()
    {
        $expense_id = $this->request->getPost('expense_id');
        $expense_date = $this->request->getPost('expense_date');
        $expense_type = $this->request->getPost('expense_type');
        $description = $this->request->getPost('description');
        $amount = $this->request->getPost('amount') ?: 0;
        $paid_by = $this->request->getPost('paid_by');
        $reference_no = $this->request->getPost('reference_no');
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_dispatch_expenses`
            SET 
                `expense_date` = ?, `expense_type` = ?, `description` = ?,
                `amount` = ?, `paid_by` = ?, `reference_no` = ?, `remarks` = ?,
                `updated_at` = NOW()
            WHERE `expense_id` = ?
            ",
            [
                $expense_date, $expense_type, $description,
                $amount, $paid_by, $reference_no, $remarks,
                $expense_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Expense Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating expense.'];
        }
    }

    public function deleteExpense()
    {
        $expense_id = $this->request->getPost('expense_id');
        
        $query = $this->db->query("DELETE FROM `tbl_dispatch_expenses` WHERE `expense_id` = ?", [$expense_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Expense Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting expense.'];
        }
    }

    // ==============================
    // PRE-TRIP INSPECTION
    // ==============================
    public function getInspection($dispatch_id)
    {
        $row = $this->db->query("SELECT * FROM tbl_dispatch_inspections WHERE dispatch_id = ?", [$dispatch_id])->getRowArray();

        // Driver who must sign = the driver currently assigned to the trip
        $assigned = $this->db->query("
            SELECT a.driver_id, a.driver_name, d.truck
            FROM tbl_dispatch d
            LEFT JOIN tbl_trip_assignments a ON a.trip_id = d.trip_id
            WHERE d.dispatch_id = ?
        ", [$dispatch_id])->getRowArray();

        $gate = $this->checkInspection($row);
        return [
            'inspection' => $row,
            'assigned_driver_id' => $assigned['driver_id'] ?? null,
            'assigned_driver_name' => $assigned['driver_name'] ?? null,
            'truck' => $assigned['truck'] ?? null,
            'cleared' => $gate['cleared'],
            'missing' => $gate['missing'],
        ];
    }

    // Is the trip cleared to move past DISPATCHED? (used here and by the DR model)
    public function getInspectionGate($trip_id)
    {
        $row = $this->db->query("
            SELECT i.* FROM tbl_dispatch_inspections i
            JOIN tbl_dispatch d ON d.dispatch_id = i.dispatch_id
            WHERE d.trip_id = ?
            ORDER BY i.inspection_id DESC LIMIT 1
        ", [$trip_id])->getRowArray();
        $gate = $this->checkInspection($row);
        $gate['message'] = $gate['cleared'] ? '' : 'Pre-trip inspection not cleared: ' . implode(', ', $gate['missing']) . '.';
        return $gate;
    }

    private function checkInspection($row)
    {
        $missing = [];
        if (!$row) {
            return ['cleared' => false, 'missing' => ['inspection form', 'driver signature', 'inspector signature']];
        }
        if (empty($row['inspection_form'])) $missing[] = 'inspection form';
        if (empty($row['driver_signature'])) $missing[] = 'driver signature';
        if (empty($row['inspector_signature'])) $missing[] = 'inspector signature';
        if ($row['final_status'] === 'REQUIRES_REPAIR') $missing[] = 'vehicle is marked Requires Repair';
        return ['cleared' => count($missing) === 0, 'missing' => $missing];
    }

    // Trip status is still before the road (no inspection needed yet)
    private function tripCancelled($trip_id)
    {
        return $this->db->query("SELECT trip_status FROM tbl_trips WHERE trip_id = ?", [$trip_id])->getRow()->trip_status === 'CANCELLED';
    }

    private function tripNotStarted($trip_id)
    {
        $trip = $this->db->query("SELECT trip_status FROM tbl_trips WHERE trip_id = ?", [$trip_id])->getRow();
        return $trip && in_array($trip->trip_status, ['ASSIGNED', 'DISPATCHED']);
    }

    // Create the inspection row on first touch (upload / sign / save)
    private function ensureInspection($dispatch_id)
    {
        $row = $this->db->query("SELECT * FROM tbl_dispatch_inspections WHERE dispatch_id = ?", [$dispatch_id])->getRow();
        if ($row) return $row;

        $dispatch = $this->db->query("SELECT dispatch_id, trip_id, truck FROM tbl_dispatch WHERE dispatch_id = ?", [$dispatch_id])->getRow();
        if (!$dispatch) return null;
        $driver = $this->db->query("SELECT driver_id, driver_name FROM tbl_trip_assignments WHERE trip_id = ? LIMIT 1", [$dispatch->trip_id])->getRow();

        $this->db->query("
            INSERT INTO `tbl_dispatch_inspections`(
                `inspection_code`, `dispatch_id`, `trip_id`, `inspection_date`, `inspection_time`,
                `truck_plate`, `driver_id`, `driver_name`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $this->generateInspectionCode(), $dispatch_id, $dispatch->trip_id, date('Y-m-d'), date('H:i:s'),
            $dispatch->truck, $driver->driver_id ?? null, $driver->driver_name ?? null, $this->cuser
        ]);

        return $this->db->query("SELECT * FROM tbl_dispatch_inspections WHERE dispatch_id = ?", [$dispatch_id])->getRow();
    }

    public function saveInspection()
    {
        $dispatch_id = $this->request->getPost('dispatch_id');
        $inspection_date = $this->request->getPost('inspection_date') ?: date('Y-m-d');
        $inspection_time = $this->request->getPost('inspection_time');
        $inspector_name = trim((string) $this->request->getPost('inspector_name'));
        $final_status = $this->request->getPost('final_status') === 'REQUIRES_REPAIR' ? 'REQUIRES_REPAIR' : 'SAFE';
        $defects_found = $this->request->getPost('defects_found');
        $remarks = $this->request->getPost('remarks');

        if (!$dispatch_id) {
            return ['status' => 'error', 'message' => 'Please save the dispatch first.'];
        }
        if ($inspector_name === '') {
            return ['status' => 'error', 'message' => 'Please enter the inspector name.'];
        }

        $old = $this->ensureInspection($dispatch_id);
        if (!$old) {
            return ['status' => 'error', 'message' => 'Dispatch not found.'];
        }

        // Signatures attest to the result — a changed result needs fresh signatures
        $resign = $old->final_status !== $final_status && ($old->driver_signature || $old->inspector_signature);

        $query = $this->db->query("
            UPDATE `tbl_dispatch_inspections`
            SET `inspection_date` = ?, `inspection_time` = ?, `inspector_name` = ?,
                `final_status` = ?, `defects_found` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `inspection_id` = ?
        ", [$inspection_date, $inspection_time, $inspector_name, $final_status, $defects_found, $remarks, $old->inspection_id]);

        if ($resign) {
            $this->db->query("
                UPDATE tbl_dispatch_inspections
                SET driver_signature = NULL, driver_signed_at = NULL, inspector_signature = NULL, inspector_signed_at = NULL
                WHERE inspection_id = ?
            ", [$old->inspection_id]);
        }

        if ($query) {
            $message = $resign
                ? 'Inspection Saved — final status changed, so the driver and inspector must sign again.'
                : 'Inspection Saved Successfully!';
            return ['status' => 'success', 'message' => $message];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving inspection.'];
        }
    }

    public function uploadInspectionForm()
    {
        $dispatch_id = $this->request->getPost('dispatch_id');
        $file = $this->request->getFile('file');

        if (!$dispatch_id) {
            return ['status' => 'error', 'message' => 'Please save the dispatch first.'];
        }
        if (!$file || !$file->isValid()) {
            return ['status' => 'error', 'message' => 'No valid file uploaded.'];
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'])) {
            return ['status' => 'error', 'message' => 'File type not allowed. Only JPG, PNG, GIF, WEBP, PDF.'];
        }
        if ($file->getSize() > 10 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'File too large. Max 10MB.'];
        }

        $row = $this->ensureInspection($dispatch_id);
        if (!$row) {
            return ['status' => 'error', 'message' => 'Dispatch not found.'];
        }

        $uploadPath = FCPATH . 'uploads/dispatch_inspections/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = 'INSP_' . $dispatch_id . '_FORM_' . time() . '.' . $ext;
        if (!$file->move($uploadPath, $newName)) {
            return ['status' => 'error', 'message' => 'Failed to move uploaded file.'];
        }

        $relativePath = 'uploads/dispatch_inspections/' . $newName;
        $this->db->query("UPDATE tbl_dispatch_inspections SET inspection_form = ?, updated_at = NOW() WHERE inspection_id = ?", [$relativePath, $row->inspection_id]);

        return ['status' => 'success', 'message' => 'Inspection form uploaded!', 'path' => $relativePath];
    }

    // ==============================
    // SIGN INSPECTION (drawn base64 or uploaded image) — signer: driver | inspector
    // ==============================
    public function signInspection()
    {
        $dispatch_id = $this->request->getPost('dispatch_id');
        $signer = $this->request->getPost('signer');
        $image_data = $this->request->getPost('signature_data');
        $file = $this->request->getFile('file');

        if (!$dispatch_id) {
            return ['status' => 'error', 'message' => 'Please save the dispatch first.'];
        }
        if (!in_array($signer, ['driver', 'inspector'])) {
            return ['status' => 'error', 'message' => 'Invalid signer.'];
        }

        $row = $this->ensureInspection($dispatch_id);
        if (!$row) {
            return ['status' => 'error', 'message' => 'Dispatch not found.'];
        }

        if ($signer === 'inspector' && empty($row->inspector_name)) {
            return ['status' => 'error', 'message' => 'Enter and save the inspector name before signing.'];
        }

        // The driver signing must be the driver assigned to the trip
        // (our driver by ID, or the vendor's driver by name on a rented package)
        $driver = null;
        if ($signer === 'driver') {
            $driver = $this->db->query("SELECT driver_id, driver_name FROM tbl_trip_assignments WHERE trip_id = ? LIMIT 1", [$row->trip_id])->getRow();
            if (!$driver || (!$driver->driver_id && !$driver->driver_name)) {
                return ['status' => 'error', 'message' => 'No driver is assigned to this trip.'];
            }
        }

        $uploadPath = FCPATH . 'uploads/dispatch_inspections/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $base = 'INSP_' . $dispatch_id . '_' . strtoupper($signer) . '_SIGNATURE_' . time();

        if ($image_data) {
            if (!preg_match('/^data:image\/(png|jpeg);base64,/', $image_data, $matches)) {
                return ['status' => 'error', 'message' => 'Invalid signature format.'];
            }
            $ext = $matches[1] === 'jpeg' ? 'jpg' : 'png';
            $decoded = base64_decode(str_replace(' ', '+', preg_replace('/^data:image\/(png|jpeg);base64,/', '', $image_data)));
            if (!$decoded || strlen($decoded) < 100) {
                return ['status' => 'error', 'message' => 'Signature is empty.'];
            }
            if (strlen($decoded) > 2 * 1024 * 1024) {
                return ['status' => 'error', 'message' => 'Signature image too large.'];
            }
            if (file_put_contents($uploadPath . $base . '.' . $ext, $decoded) === false) {
                return ['status' => 'error', 'message' => 'Failed to save signature file.'];
            }
        } elseif ($file && $file->isValid()) {
            $ext = strtolower($file->getClientExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return ['status' => 'error', 'message' => 'Signature must be an image (JPG, PNG, GIF, WEBP).'];
            }
            if ($file->getSize() > 2 * 1024 * 1024) {
                return ['status' => 'error', 'message' => 'Signature image too large.'];
            }
            if (!$file->move($uploadPath, $base . '.' . $ext)) {
                return ['status' => 'error', 'message' => 'Failed to move uploaded file.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Missing signature.'];
        }

        $relativePath = 'uploads/dispatch_inspections/' . $base . '.' . $ext;
        if ($signer === 'driver') {
            $this->db->query("
                UPDATE tbl_dispatch_inspections
                SET driver_signature = ?, driver_signed_at = NOW(), driver_id = ?, driver_name = ?, updated_at = NOW()
                WHERE inspection_id = ?
            ", [$relativePath, $driver->driver_id, $driver->driver_name, $row->inspection_id]);
        } else {
            $this->db->query("
                UPDATE tbl_dispatch_inspections
                SET inspector_signature = ?, inspector_signed_at = NOW(), updated_at = NOW()
                WHERE inspection_id = ?
            ", [$relativePath, $row->inspection_id]);
        }

        return ['status' => 'success', 'message' => ucfirst($signer) . ' signature saved!', 'path' => $relativePath];
    }

    public function removeInspectionFile()
    {
        $dispatch_id = $this->request->getPost('dispatch_id');
        $field = $this->request->getPost('field');

        $map = [
            'inspection_form' => "inspection_form = NULL",
            'driver_signature' => "driver_signature = NULL, driver_signed_at = NULL",
            'inspector_signature' => "inspector_signature = NULL, inspector_signed_at = NULL",
        ];
        if (!isset($map[$field])) {
            return ['status' => 'error', 'message' => 'Invalid field.'];
        }

        $query = $this->db->query("UPDATE tbl_dispatch_inspections SET {$map[$field]}, updated_at = NOW() WHERE dispatch_id = ?", [$dispatch_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Removed.'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while removing.'];
        }
    }

    // ==============================
    // SAVE DISPATCH
    // ==============================
    public function saveDispatch()
    {
        $dispatch_code = $this->generateDispatchCode();
        $trip_id = $this->request->getPost('trip_id');
        $dispatch_date = $this->request->getPost('dispatch_date') ?: date('Y-m-d');
        $dispatch_time = $this->request->getPost('dispatch_time');
        $truck = $this->request->getPost('truck');
        $driver = $this->request->getPost('driver');
        $helper = $this->request->getPost('helper');
        $origin = $this->request->getPost('origin');
        $destination = $this->request->getPost('destination');
        $odometer_out = $this->request->getPost('odometer_out') ?: 0;
        $fuel_level_out = $this->request->getPost('fuel_level_out') ?: 0;
        $container_required = $this->request->getPost('container_required') ?: 0;
        $container_number = $this->request->getPost('container_number');
        $container_type = $this->request->getPost('container_type');
        $container_description = $this->request->getPost('container_description');
        $container_markings = $this->request->getPost('container_markings');
        $container_reference = $this->request->getPost('container_reference');
        $container_release_port = $this->request->getPost('container_release_port');
        $container_release_date = $this->request->getPost('container_release_date');
        $container_release_time = $this->request->getPost('container_release_time');
        $container_return_required = $this->request->getPost('container_return_required') ?: 0;
        $container_return_date = $this->request->getPost('container_return_date');
        $container_return_time = $this->request->getPost('container_return_time');
        $container_return_port = $this->request->getPost('container_return_port');
        $container_return_odometer = $this->request->getPost('container_return_odometer') ?: 0;
        $container_return_status = $this->request->getPost('container_return_status') ?: 'NOT_APPLICABLE';
        $container_return_proof = $this->request->getPost('container_return_proof');
        $dispatcher_name = $this->request->getPost('dispatcher_name');
        $trip_status = $this->request->getPost('trip_status') ?: 'DISPATCHED';
        $actual_delivery_date = $this->request->getPost('actual_delivery_date');
        $actual_delivery_time = $this->request->getPost('actual_delivery_time');
        $odometer_in = $this->request->getPost('odometer_in') ?: 0;
        $fuel_level_in = $this->request->getPost('fuel_level_in') ?: 0;
        $total_distance = $this->request->getPost('total_distance') ?: 0;
        $fuel_consumed = $this->request->getPost('fuel_consumed') ?: 0;
        $delay_reason = $this->request->getPost('delay_reason');
        $remarks = $this->request->getPost('remarks');

        // A new dispatch has no pre-trip inspection yet — it can only start as Dispatched
        if (!in_array($trip_status, ['DISPATCHED', 'CANCELLED'])) {
            return ['status' => 'error', 'message' => 'Save the dispatch as Dispatched first, then complete the pre-trip inspection before moving it to ' . ucwords(strtolower(str_replace('_', ' ', $trip_status))) . '.'];
        }

        $query = $this->db->query("
            INSERT INTO `tbl_dispatch`(
                `dispatch_code`, `trip_id`, `dispatch_date`, `dispatch_time`,
                `truck`, `driver`, `helper`, `origin`, `destination`,
                `odometer_out`, `fuel_level_out`,
                `container_required`, `container_number`, `container_type`,
                `container_description`, `container_markings`, `container_reference`,
                `container_release_port`, `container_release_date`, `container_release_time`,
                `container_return_required`, `container_return_date`, `container_return_time`,
                `container_return_port`, `container_return_odometer`, `container_return_status`,
                `container_return_proof`, `dispatcher_name`,
                `actual_delivery_date`, `actual_delivery_time`,
                `odometer_in`, `fuel_level_in`, `total_distance`, `fuel_consumed`,
                `delay_reason`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $dispatch_code, $trip_id, $dispatch_date, $dispatch_time,
                $truck, $driver, $helper, $origin, $destination,
                $odometer_out, $fuel_level_out,
                $container_required, $container_number, $container_type,
                $container_description, $container_markings, $container_reference,
                $container_release_port, $container_release_date, $container_release_time,
                $container_return_required, $container_return_date, $container_return_time,
                $container_return_port, $container_return_odometer, $container_return_status,
                $container_return_proof, $dispatcher_name,
                $actual_delivery_date, $actual_delivery_time,
                $odometer_in, $fuel_level_in, $total_distance, $fuel_consumed,
                $delay_reason, $remarks, $this->cuser
            ]
        );

        if ($query) {
            $dispatch_id = $this->db->insertID();
            
            // Trip status is the single lifecycle
            $this->db->query("UPDATE tbl_trips SET trip_status = ? WHERE trip_id = ?", [$trip_status, $trip_id]);

            if (in_array($trip_status, ['COMPLETED', 'CANCELLED'])) {
                $this->releaseTripResources($trip_id);
            }

            return ['status' => 'success', 'message' => 'Dispatch Saved Successfully!', 'dispatch_id' => $dispatch_id];
        } else {
            $error = $this->db->error();
            log_message('error', 'Dispatch Save Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while saving dispatch.'];
        }
    }

    // ==============================
    // UPDATE DISPATCH
    // ==============================
    public function updateDispatch()
    {
        $dispatch_id = $this->request->getPost('dispatch_id');
        $trip_id = $this->request->getPost('trip_id');
        $dispatch_date = $this->request->getPost('dispatch_date');
        $dispatch_time = $this->request->getPost('dispatch_time');
        $truck = $this->request->getPost('truck');
        $driver = $this->request->getPost('driver');
        $helper = $this->request->getPost('helper');
        $origin = $this->request->getPost('origin');
        $destination = $this->request->getPost('destination');
        $odometer_out = $this->request->getPost('odometer_out') ?: 0;
        $fuel_level_out = $this->request->getPost('fuel_level_out') ?: 0;
        $container_required = $this->request->getPost('container_required') ?: 0;
        $container_number = $this->request->getPost('container_number');
        $container_type = $this->request->getPost('container_type');
        $container_description = $this->request->getPost('container_description');
        $container_markings = $this->request->getPost('container_markings');
        $container_reference = $this->request->getPost('container_reference');
        $container_release_port = $this->request->getPost('container_release_port');
        $container_release_date = $this->request->getPost('container_release_date');
        $container_release_time = $this->request->getPost('container_release_time');
        $container_return_required = $this->request->getPost('container_return_required') ?: 0;
        $container_return_date = $this->request->getPost('container_return_date');
        $container_return_time = $this->request->getPost('container_return_time');
        $container_return_port = $this->request->getPost('container_return_port');
        $container_return_odometer = $this->request->getPost('container_return_odometer') ?: 0;
        $container_return_status = $this->request->getPost('container_return_status') ?: 'NOT_APPLICABLE';
        $container_return_proof = $this->request->getPost('container_return_proof');
        $dispatcher_name = $this->request->getPost('dispatcher_name');
        $trip_status = $this->request->getPost('trip_status') ?: 'DISPATCHED';
        $actual_delivery_date = $this->request->getPost('actual_delivery_date');
        $actual_delivery_time = $this->request->getPost('actual_delivery_time');
        $odometer_in = $this->request->getPost('odometer_in') ?: 0;
        $fuel_level_in = $this->request->getPost('fuel_level_in') ?: 0;
        $total_distance = $this->request->getPost('total_distance') ?: 0;
        $fuel_consumed = $this->request->getPost('fuel_consumed') ?: 0;
        $delay_reason = $this->request->getPost('delay_reason');
        $remarks = $this->request->getPost('remarks');

        // Pre-trip inspection gate: the truck can't go on the road until it's cleared
        if (in_array($trip_status, ['IN_TRANSIT', 'DELIVERED', 'COMPLETED']) && $this->tripNotStarted($trip_id)) {
            $gate = $this->getInspectionGate($trip_id);
            if (!$gate['cleared']) {
                return ['status' => 'error', 'message' => $gate['message']];
            }
        }

        // Same container-return gate as the last-waypoint-arrival flow: don't let a
        // manual "Completed" pick skip past a container that hasn't been returned yet.
        $completionBlocked = false;
        if ($trip_status === 'COMPLETED' && (int) $container_return_required === 1 && $container_return_status !== 'RETURNED') {
            $trip_status = 'DELIVERED';
            $completionBlocked = true;
        }

        $query = $this->db->query("
            UPDATE `tbl_dispatch`
            SET
                `dispatch_date` = ?, `dispatch_time` = ?,
                `truck` = ?, `driver` = ?, `helper` = ?,
                `origin` = ?, `destination` = ?,
                `odometer_out` = ?, `fuel_level_out` = ?,
                `container_required` = ?, `container_number` = ?,
                `container_type` = ?, `container_description` = ?,
                `container_markings` = ?, `container_reference` = ?,
                `container_release_port` = ?, `container_release_date` = ?,
                `container_release_time` = ?,
                `container_return_required` = ?, `container_return_date` = ?,
                `container_return_time` = ?, `container_return_port` = ?,
                `container_return_odometer` = ?, `container_return_status` = ?,
                `container_return_proof` = ?, `dispatcher_name` = ?,
                `actual_delivery_date` = ?,
                `actual_delivery_time` = ?, `odometer_in` = ?,
                `fuel_level_in` = ?, `total_distance` = ?,
                `fuel_consumed` = ?, `delay_reason` = ?, `remarks` = ?,
                `updated_at` = NOW()
            WHERE `dispatch_id` = ?
            ",
            [
                $dispatch_date, $dispatch_time,
                $truck, $driver, $helper,
                $origin, $destination,
                $odometer_out, $fuel_level_out,
                $container_required, $container_number,
                $container_type, $container_description,
                $container_markings, $container_reference,
                $container_release_port, $container_release_date,
                $container_release_time,
                $container_return_required, $container_return_date,
                $container_return_time, $container_return_port,
                $container_return_odometer, $container_return_status,
                $container_return_proof, $dispatcher_name,
                $actual_delivery_date,
                $actual_delivery_time, $odometer_in,
                $fuel_level_in, $total_distance,
                $fuel_consumed, $delay_reason, $remarks,
                $dispatch_id
            ]
        );

        if ($query) {
            // Trip status is the single lifecycle
            $this->db->query("UPDATE tbl_trips SET trip_status = ? WHERE trip_id = ?", [$trip_status, $trip_id]);

            if (in_array($trip_status, ['COMPLETED', 'CANCELLED'])) {
                $this->releaseTripResources($trip_id);
            }

            if ($completionBlocked) {
                return ['status' => 'success', 'message' => 'Dispatch Updated — container return is still pending, so it was kept at Delivered instead of Completed.'];
            }
            return ['status' => 'success', 'message' => 'Dispatch Updated Successfully!'];
        } else {
            $error = $this->db->error();
            log_message('error', 'Dispatch Update Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while updating dispatch.'];
        }
    }

    // ==============================
    // DELETE DISPATCH
    // ==============================
    public function deleteDispatch()
    {
        $dispatch_id = $this->request->getPost('dispatch_id');
        $trip_id = $this->request->getPost('trip_id');

        $hasDR = $this->db->query("SELECT COUNT(*) as total FROM tbl_delivery_receipts WHERE dispatch_id = ?", [$dispatch_id])->getRow()->total;
        if ($hasDR > 0) {
            return ['status' => 'error', 'message' => 'Cannot delete a dispatch that already has a delivery receipt.'];
        }

        // Delete checklist and expenses first
        $this->db->query("DELETE FROM `tbl_dispatch_checklist` WHERE `dispatch_id` = ?", [$dispatch_id]);
        $this->db->query("DELETE FROM `tbl_dispatch_expenses` WHERE `dispatch_id` = ?", [$dispatch_id]);

        $query = $this->db->query("DELETE FROM `tbl_dispatch` WHERE `dispatch_id` = ?", [$dispatch_id]);

        if ($query) {
            // Update trip status back to ASSIGNED
            $this->db->query("UPDATE tbl_trips SET trip_status = 'ASSIGNED' WHERE trip_id = ?", [$trip_id]);
            return ['status' => 'success', 'message' => 'Dispatch Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting dispatch.'];
        }
    }

    // ==============================
    // WAYPOINT TRACKING METHODS
    // ==============================
    public function getWaypointsWithTracking($trip_id)
    {
        return $this->db->query("
            SELECT w.*,
                   w.actual_arrival,
                   w.actual_departure,
                   w.waypoint_status,
                   w.arrival_remarks,
                   w.departure_remarks
            FROM tbl_trip_waypoints w
            WHERE w.trip_id = ?
            ORDER BY w.sequence ASC
        ", [$trip_id])->getResultArray();
    }

    public function getWaypointWithTracking($waypoint_id)
    {
        $query = $this->db->query("
            SELECT w.*,
                   w.actual_arrival,
                   w.actual_departure,
                   w.waypoint_status,
                   w.arrival_remarks,
                   w.departure_remarks
            FROM tbl_trip_waypoints w
            WHERE w.waypoint_id = ?
        ", [$waypoint_id]);
        return $query->getRowArray();
    }

    public function updateWaypointArrival()
    {
        $waypoint_id = $this->request->getPost('waypoint_id');
        $actual_arrival = $this->request->getPost('actual_arrival');
        $arrival_remarks = $this->request->getPost('arrival_remarks');

        if(!$actual_arrival) {
            return ['status' => 'error', 'message' => 'Please select arrival date/time'];
        }

        $waypoint = $this->db->query("SELECT trip_id, sequence, waypoint_type FROM tbl_trip_waypoints WHERE waypoint_id = ?", [$waypoint_id])->getRow();

        if ($waypoint && $this->tripCancelled($waypoint->trip_id)) {
            return ['status' => 'error', 'message' => 'This trip was cancelled — stops can no longer be recorded.'];
        }
        if ($waypoint && $this->tripNotStarted($waypoint->trip_id)) {
            $gate = $this->getInspectionGate($waypoint->trip_id);
            if (!$gate['cleared']) {
                return ['status' => 'error', 'message' => $gate['message']];
            }
        }

        $isLastWaypoint = false;
        if ($waypoint) {
            $lastSeq = $this->db->query("SELECT MAX(sequence) as max_seq FROM tbl_trip_waypoints WHERE trip_id = ?", [$waypoint->trip_id])->getRow();
            $isLastWaypoint = $lastSeq && (int) $waypoint->sequence === (int) $lastSeq->max_seq;
        }

        $waypoint_status = $isLastWaypoint ? 'COMPLETED' : 'ARRIVED';

        $query = $this->db->query("
            UPDATE `tbl_trip_waypoints`
            SET
                `actual_arrival` = ?,
                `waypoint_status` = ?,
                `arrival_remarks` = ?,
                `updated_at` = NOW()
            WHERE `waypoint_id` = ?
        ", [$actual_arrival, $waypoint_status, $arrival_remarks, $waypoint_id]);

        if ($query) {
            // Arriving at the container return stop closes out the container return sub-task —
            // but only after the cargo was delivered. A port stop *before* delivery is the
            // container pickup, not its return.
            $deliveredBefore = $waypoint && $this->db->query("
                SELECT COUNT(*) as total FROM tbl_trip_waypoints
                WHERE trip_id = ? AND sequence < ? AND waypoint_type IN ('DELIVERY_DESTINATION','CLIENT_WAREHOUSE')
            ", [$waypoint->trip_id, $waypoint->sequence])->getRow()->total > 0;
            if ($deliveredBefore && in_array($waypoint->waypoint_type, ['PORT_TERMINAL', 'RETURN_POINT'])) {
                $ts = strtotime(str_replace('T', ' ', $actual_arrival));
                $this->db->query("
                    UPDATE tbl_dispatch
                    SET container_return_status = 'RETURNED', container_return_date = ?, container_return_time = ?, updated_at = NOW()
                    WHERE trip_id = ? AND container_return_required = 1 AND container_return_status != 'RETURNED'
                ", [date('Y-m-d', $ts), date('H:i:s', $ts), $waypoint->trip_id]);
            }

            if ($isLastWaypoint) {
                $completed = $this->completeTripResources($waypoint->trip_id, $actual_arrival);
                if ($completed) {
                    $rented = $this->db->query("SELECT vehicle_type FROM tbl_trip_assignments WHERE trip_id = ? LIMIT 1", [$waypoint->trip_id])->getRow();
                    if ($rented && $rented->vehicle_type === 'RENTED_ALL') {
                        return ['status' => 'success', 'message' => 'Last waypoint reached — trip completed. The vendor package unit and crew are released back to the vendor.'];
                    }
                    return ['status' => 'success', 'message' => 'Last waypoint reached — trip completed, truck/driver/helper are now available.'];
                }
                return ['status' => 'success', 'message' => 'Last waypoint reached, but container return is still pending — trip stays at Delivered until the container is returned.'];
            }
            return ['status' => 'success', 'message' => 'Arrival recorded successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while recording arrival.'];
        }
    }

    // ==============================
    // COMPLETE TRIP: sync trip/dispatch status and free up resources
    // (gated on container return, when one is required)
    // ==============================
    private function completeTripResources($trip_id, $actual_arrival)
    {
        $dispatch = $this->db->query("
            SELECT dispatch_id, container_return_required, container_return_status
            FROM tbl_dispatch WHERE trip_id = ?
        ", [$trip_id])->getRow();

        if ($dispatch && (int) $dispatch->container_return_required === 1 && $dispatch->container_return_status !== 'RETURNED') {
            return false;
        }

        $this->db->query("UPDATE tbl_trips SET trip_status = 'COMPLETED' WHERE trip_id = ?", [$trip_id]);

        $ts = strtotime(str_replace('T', ' ', $actual_arrival));
        $arrivalDate = date('Y-m-d', $ts);
        $arrivalTime = date('H:i:s', $ts);

        if ($dispatch) {
            $this->db->query("
                UPDATE tbl_dispatch
                SET actual_delivery_date = ?, actual_delivery_time = ?, updated_at = NOW()
                WHERE dispatch_id = ?
            ", [$arrivalDate, $arrivalTime, $dispatch->dispatch_id]);
        }

        $this->releaseTripResources($trip_id);

        return true;
    }

    // ==============================
    // RELEASE TRIP RESOURCES: free the truck/tractor/chassis/driver/helper
    // (only those still in a trip status — a status changed by hand during the trip,
    // e.g. UNDER_MAINTENANCE or ON_LEAVE, is kept)
    // assigned to a trip back to AVAILABLE. Called whenever a trip is marked
    // COMPLETED or CANCELLED, whether via last-waypoint-arrival or a manual
    // dispatch edit.
    // ==============================
    private function releaseTripResources($trip_id)
    {
        $assignment = $this->db->query("
            SELECT truck_id, tractor_id, chassis_id, driver_id, helper_id
            FROM tbl_trip_assignments
            WHERE trip_id = ?
            ORDER BY assignment_id DESC
            LIMIT 1
        ", [$trip_id])->getRow();

        if ($assignment) {
            foreach (array_filter([$assignment->truck_id, $assignment->tractor_id, $assignment->chassis_id]) as $truck_id) {
                $this->db->query("UPDATE tbl_trucks SET truck_status = 'AVAILABLE' WHERE truck_id = ? AND truck_status IN ('ASSIGNED','DISPATCHED','IN_TRANSIT','RETURNING')", [$truck_id]);
            }
            if (!empty($assignment->driver_id)) {
                $this->db->query("UPDATE tbl_drivers SET driver_status = 'AVAILABLE' WHERE driver_id = ? AND driver_status IN ('ASSIGNED','ON_TRIP','RETURNING')", [$assignment->driver_id]);
            }
            if (!empty($assignment->helper_id)) {
                $this->db->query("UPDATE tbl_helpers SET helper_status = 'AVAILABLE' WHERE helper_id = ? AND helper_status IN ('ASSIGNED','ON_TRIP')", [$assignment->helper_id]);
            }
        }
    }

    public function updateWaypointDeparture()
    {
        $waypoint_id = $this->request->getPost('waypoint_id');
        $actual_departure = $this->request->getPost('actual_departure');
        $departure_remarks = $this->request->getPost('departure_remarks');
        
        if(!$actual_departure) {
            return ['status' => 'error', 'message' => 'Please select departure date/time'];
        }

        $waypoint = $this->db->query("SELECT trip_id FROM tbl_trip_waypoints WHERE waypoint_id = ?", [$waypoint_id])->getRow();
        if ($waypoint && $this->tripCancelled($waypoint->trip_id)) {
            return ['status' => 'error', 'message' => 'This trip was cancelled — stops can no longer be recorded.'];
        }
        if ($waypoint && $this->tripNotStarted($waypoint->trip_id)) {
            $gate = $this->getInspectionGate($waypoint->trip_id);
            if (!$gate['cleared']) {
                return ['status' => 'error', 'message' => $gate['message']];
            }
        }
        
        $query = $this->db->query("
            UPDATE `tbl_trip_waypoints`
            SET 
                `actual_departure` = ?,
                `waypoint_status` = 'DEPARTED',
                `departure_remarks` = ?,
                `updated_at` = NOW()
            WHERE `waypoint_id` = ?
        ", [$actual_departure, $departure_remarks, $waypoint_id]);
        
        if ($query) {
            // Leaving a stop means the truck is on the road
            if ($waypoint) {
                $this->db->query("UPDATE tbl_trips SET trip_status = 'IN_TRANSIT' WHERE trip_id = ? AND trip_status = 'DISPATCHED'", [$waypoint->trip_id]);
            }
            return ['status' => 'success', 'message' => 'Departure recorded successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while recording departure.'];
        }
    }
}