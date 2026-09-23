<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Tool_Model extends Model
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
    // CODE GENERATORS
    // ==============================
    private function generateToolCode()
    {
        $year = date('Y');
        $q = $this->db->query("SELECT COUNT(*) as total FROM tbl_tools WHERE YEAR(created_at) = ?", [$year]);
        return 'TL-' . $year . '-' . str_pad($q->getRow()->total + 1, 6, '0', STR_PAD_LEFT);
    }

    private function generateIssuanceCode()
    {
        $year = date('Y');
        $q = $this->db->query("SELECT COUNT(*) as total FROM tbl_tool_issuances WHERE YEAR(created_at) = ?", [$year]);
        return 'TIS-' . $year . '-' . str_pad($q->getRow()->total + 1, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // LOOKUPS
    // ==============================
    public function getTrucks()
    {
        return $this->db->query("
            SELECT truck_id, truck_code, plate_number, make, model
            FROM tbl_trucks
            WHERE truck_status NOT IN ('RETIRED','OUT OF SERVICE')
            ORDER BY plate_number
        ")->getResultArray();
    }

    public function getAvailableTools()
    {
        return $this->db->query("
            SELECT tool_id, tool_code, tool_name, category, brand, model, serial_number, 
                   tool_condition, quantity, quantity_on_hand
            FROM tbl_tools
            WHERE availability IN ('AVAILABLE','ASSIGNED')
              AND quantity_on_hand > 0
            ORDER BY tool_name
        ")->getResultArray();
    }

    public function getCategories()
    {
        return $this->db->query("
            SELECT DISTINCT category FROM tbl_tools 
            WHERE category IS NOT NULL AND category != ''
            ORDER BY category
        ")->getResultArray();
    }

    // ==============================
    // GET OPEN ASSIGNEES PER TOOL
    // (Aggregates all open issuances grouped by tool)
    // ==============================
    public function getOpenAssignees($tool_id = null)
    {
        $where = $tool_id ? "AND ti.tool_id = ?" : "";
        $params = $tool_id ? [$tool_id] : [];

        $rows = $this->db->query("
            SELECT ti.tool_id, ti.issued_to, ti.quantity_pending, ti.truck_plate
            FROM tbl_tool_issuances ti
            WHERE ti.quantity_pending > 0
            $where
            ORDER BY ti.issued_to ASC
        ", $params)->getResultArray();

        $grouped = [];
        foreach ($rows as $r) {
            $tid = $r['tool_id'];
            if (!isset($grouped[$tid])) $grouped[$tid] = [];
            $grouped[$tid][] = [
                'name'     => $r['issued_to'],
                'quantity' => intval($r['quantity_pending']),
                'truck'    => $r['truck_plate'],
            ];
        }

        return $grouped;
    }

    // ==============================
    // TOOL CRUD
    // ==============================
    public function getAllTools()
    {
        return $this->db->query("
            SELECT * FROM tbl_tools
            ORDER BY tool_name ASC
        ")->getResultArray();
    }

    public function getTool($tool_id)
    {
        $q = $this->db->query("SELECT * FROM tbl_tools WHERE tool_id = ?", [$tool_id]);
        return $q->getRowArray();
    }

    public function saveTool()
    {
        $tool_code = $this->generateToolCode();
        $tool_name = $this->request->getPost('tool_name');
        $category = $this->request->getPost('category');
        $brand = $this->request->getPost('brand');
        $model = $this->request->getPost('model');
        $serial_number = $this->request->getPost('serial_number');
        $purchase_date = $this->request->getPost('purchase_date') ?: null;
        $purchase_cost = $this->request->getPost('purchase_cost') ?: 0;
        $quantity_post = $this->request->getPost('quantity');
        $quantity = ($quantity_post === null || $quantity_post === '') ? 1 : intval($quantity_post);
        $quantity_on_hand = $quantity;
        $current_location = $this->request->getPost('current_location');
        $tool_condition = $this->request->getPost('tool_condition') ?: 'GOOD';
        $availability = $this->request->getPost('availability') ?: 'AVAILABLE';
        $assigned_to = $this->request->getPost('assigned_to');
        $truck_id = $this->request->getPost('truck_id') ?: null;
        $truck_plate = $this->request->getPost('truck_plate');
        $remarks = $this->request->getPost('remarks');

        if (in_array($availability, ['RETIRED','LOST'])) {
            $quantity_on_hand = 0;
        }

        if (!empty($serial_number)) {
            $dup = $this->db->query("SELECT tool_id FROM tbl_tools WHERE serial_number = ? LIMIT 1", [$serial_number])->getRow();
            if ($dup) {
                return ['status' => 'error', 'message' => 'A tool with this serial number already exists.'];
            }
        }

        $query = $this->db->query("
            INSERT INTO `tbl_tools`(
                `tool_code`, `tool_name`, `category`, `brand`, `model`, `serial_number`,
                `purchase_date`, `purchase_cost`, `quantity`, `quantity_on_hand`,
                `current_location`, `tool_condition`,
                `availability`, `assigned_to`, `truck_id`, `truck_plate`,
                `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $tool_code, $tool_name, $category, $brand, $model, $serial_number,
            $purchase_date, $purchase_cost, $quantity, $quantity_on_hand,
            $current_location, $tool_condition,
            $availability, $assigned_to, $truck_id, $truck_plate,
            $remarks, $this->cuser
        ]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Tool Saved Successfully!', 'tool_code' => $tool_code];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving tool.'];
        }
    }

    public function updateTool()
    {
        $tool_id = $this->request->getPost('tool_id');
        $tool_name = $this->request->getPost('tool_name');
        $category = $this->request->getPost('category');
        $brand = $this->request->getPost('brand');
        $model = $this->request->getPost('model');
        $serial_number = $this->request->getPost('serial_number');
        $purchase_date = $this->request->getPost('purchase_date') ?: null;
        $purchase_cost = $this->request->getPost('purchase_cost') ?: 0;
        $current_location = $this->request->getPost('current_location');
        $tool_condition = $this->request->getPost('tool_condition') ?: 'GOOD';
        $availability = $this->request->getPost('availability') ?: 'AVAILABLE';
        $assigned_to = $this->request->getPost('assigned_to');
        $truck_id = $this->request->getPost('truck_id') ?: null;
        $truck_plate = $this->request->getPost('truck_plate');
        $remarks = $this->request->getPost('remarks');

        $existing = $this->db->query("SELECT quantity, quantity_on_hand FROM tbl_tools WHERE tool_id = ?", [$tool_id])->getRow();
        if (!$existing) {
            return ['status' => 'error', 'message' => 'Tool not found.'];
        }

        if (!empty($serial_number)) {
            $dup = $this->db->query("SELECT tool_id FROM tbl_tools WHERE serial_number = ? AND tool_id != ? LIMIT 1", [$serial_number, $tool_id])->getRow();
            if ($dup) {
                return ['status' => 'error', 'message' => 'A tool with this serial number already exists.'];
            }
        }

        $old_qty = intval($existing->quantity);
        $old_on_hand = intval($existing->quantity_on_hand);
        $issued_out = $old_qty - $old_on_hand;

        $new_qty = $this->request->getPost('quantity');
        if ($new_qty === null || $new_qty === '') {
            $new_qty = $old_qty;
        } else {
            $new_qty = intval($new_qty);
        }

        if ($new_qty < $issued_out) {
            return ['status' => 'error', 'message' => 'New total quantity (' . $new_qty . ') cannot be less than currently issued (' . $issued_out . ').'];
        }

        $new_on_hand = $new_qty - $issued_out;

        if (in_array($availability, ['RETIRED','LOST'])) {
            $new_on_hand = 0;
        } elseif ($issued_out > 0) {
            $availability = 'ASSIGNED';
        } elseif ($new_on_hand == $new_qty && $new_qty > 0) {
            if (!in_array($availability, ['UNDER_REPAIR','DAMAGED'])) {
                $availability = 'AVAILABLE';
            }
        }

        $query = $this->db->query("
            UPDATE `tbl_tools` SET
                `tool_name` = ?, `category` = ?, `brand` = ?, `model` = ?, `serial_number` = ?,
                `purchase_date` = ?, `purchase_cost` = ?,
                `quantity` = ?, `quantity_on_hand` = ?,
                `current_location` = ?, `tool_condition` = ?,
                `availability` = ?, `assigned_to` = ?, `truck_id` = ?, `truck_plate` = ?,
                `remarks` = ?, `updated_at` = NOW()
            WHERE `tool_id` = ?
        ", [
            $tool_name, $category, $brand, $model, $serial_number,
            $purchase_date, $purchase_cost,
            $new_qty, $new_on_hand,
            $current_location, $tool_condition,
            $availability, $assigned_to, $truck_id, $truck_plate,
            $remarks, $tool_id
        ]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Tool Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating tool.'];
        }
    }

    public function deleteTool()
    {
        $tool_id = $this->request->getPost('tool_id');

        $openCount = $this->db->query("
            SELECT COALESCE(SUM(quantity_pending), 0) as pending
            FROM tbl_tool_issuances
            WHERE tool_id = ? AND quantity_pending > 0
        ", [$tool_id])->getRow()->pending;

        if (intval($openCount) > 0) {
            return ['status' => 'error', 'message' => 'Cannot delete: ' . intval($openCount) . ' unit(s) of this tool are still issued out.'];
        }

        $query = $this->db->query("DELETE FROM `tbl_tools` WHERE `tool_id` = ?", [$tool_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Tool Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting tool.'];
        }
    }

    // ==============================
    // ISSUANCE CRUD
    // ==============================
    public function getAllIssuances()
    {
        return $this->db->query("
            SELECT * FROM tbl_tool_issuances
            ORDER BY issue_date DESC, issuance_id DESC
        ")->getResultArray();
    }

    public function getIssuance($issuance_id)
    {
        $q = $this->db->query("SELECT * FROM tbl_tool_issuances WHERE issuance_id = ?", [$issuance_id]);
        return $q->getRowArray();
    }

    public function saveIssuance()
    {
        $issuance_code = $this->generateIssuanceCode();
        $tool_id = $this->request->getPost('tool_id');
        $tool_code = $this->request->getPost('tool_code');
        $tool_name = $this->request->getPost('tool_name');
        $issued_to = $this->request->getPost('issued_to');
        $issued_to_type = $this->request->getPost('issued_to_type');
        $issue_date = $this->request->getPost('issue_date') ?: date('Y-m-d');
        $issue_time = $this->request->getPost('issue_time') ?: date('H:i:s');
        $expected_return = $this->request->getPost('expected_return') ?: null;
        $condition_before = $this->request->getPost('condition_before') ?: 'GOOD';
        $purpose = $this->request->getPost('purpose');
        $reference_number = $this->request->getPost('reference_number');
        $truck_id = $this->request->getPost('truck_id') ?: null;
        $truck_plate = $this->request->getPost('truck_plate');
        $remarks = $this->request->getPost('remarks');
        $quantity_issued = intval($this->request->getPost('quantity_issued')) ?: 1;

        $tool = $this->db->query("SELECT * FROM tbl_tools WHERE tool_id = ?", [$tool_id])->getRow();
        if (!$tool) {
            return ['status' => 'error', 'message' => 'Tool not found.'];
        }

        if ($quantity_issued < 1) {
            return ['status' => 'error', 'message' => 'Quantity must be at least 1.'];
        }

        if ($quantity_issued > intval($tool->quantity_on_hand)) {
            return ['status' => 'error', 'message' => 'Only ' . intval($tool->quantity_on_hand) . ' available on hand.'];
        }

        $query = $this->db->query("
            INSERT INTO `tbl_tool_issuances`(
                `issuance_code`, `tool_id`, `tool_code`, `tool_name`,
                `quantity_issued`, `quantity_returned`, `quantity_pending`,
                `issued_to`, `issued_to_type`, `issue_date`, `issue_time`,
                `expected_return`, `condition_before`, `purpose`, `reference_number`,
                `truck_id`, `truck_plate`, `issuance_status`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ISSUED', ?, ?)
        ", [
            $issuance_code, $tool_id, $tool_code, $tool_name,
            $quantity_issued, $quantity_issued,
            $issued_to, $issued_to_type, $issue_date, $issue_time,
            $expected_return, $condition_before, $purpose, $reference_number,
            $truck_id, $truck_plate, $remarks, $this->cuser
        ]);

        if ($query) {
            $issuance_id = $this->db->insertID();

            $new_on_hand = intval($tool->quantity_on_hand) - $quantity_issued;
            $totalQty = intval($tool->quantity);
            $new_availability = ($new_on_hand < $totalQty) ? 'ASSIGNED' : 'AVAILABLE';

            if (in_array($tool->availability, ['RETIRED','LOST','DAMAGED'])) {
                $new_availability = $tool->availability;
            }

            $this->db->query("
                UPDATE tbl_tools 
                SET quantity_on_hand = ?, availability = ?, updated_at = NOW()
                WHERE tool_id = ?
            ", [$new_on_hand, $new_availability, $tool_id]);

            // Store last assigned_to on the tool
            $this->db->query("
                UPDATE tbl_tools 
                SET assigned_to = ?, truck_id = ?, truck_plate = ?, updated_at = NOW()
                WHERE tool_id = ?
            ", [$issued_to, $truck_id, $truck_plate, $tool_id]);

            return ['status' => 'success', 'message' => 'Tool Issued Successfully!', 'issuance_id' => $issuance_id];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while issuing tool.'];
        }
    }

    public function returnTool()
    {
        $issuance_id = $this->request->getPost('issuance_id');
        $actual_return = $this->request->getPost('actual_return') ?: date('Y-m-d');
        $return_time = $this->request->getPost('return_time') ?: date('H:i:s');
        $condition_after = $this->request->getPost('condition_after') ?: 'GOOD';
        $returned_by = $this->request->getPost('returned_by');
        $received_by = $this->request->getPost('received_by');
        $return_remarks = $this->request->getPost('return_remarks');
        $quantity_return = intval($this->request->getPost('quantity_return'));

        $iss = $this->db->query("SELECT * FROM tbl_tool_issuances WHERE issuance_id = ?", [$issuance_id])->getRow();
        if (!$iss) {
            return ['status' => 'error', 'message' => 'Issuance not found.'];
        }

        $pending = intval($iss->quantity_pending);
        if ($pending <= 0) {
            return ['status' => 'error', 'message' => 'This issuance is already fully returned.'];
        }

        if ($quantity_return < 1) {
            return ['status' => 'error', 'message' => 'Please enter a quantity to return.'];
        }

        if ($quantity_return > $pending) {
            return ['status' => 'error', 'message' => 'Cannot return more than ' . $pending . ' pending.'];
        }

        $new_returned = intval($iss->quantity_returned) + $quantity_return;
        $new_pending = intval($iss->quantity_pending) - $quantity_return;
        $new_status = ($new_pending == 0) ? 'RETURNED' : 'ISSUED';

        $query = $this->db->query("
            UPDATE `tbl_tool_issuances` SET
                `quantity_returned` = ?, `quantity_pending` = ?,
                `actual_return` = CASE WHEN ? = 0 THEN ? ELSE `actual_return` END,
                `return_time` = CASE WHEN ? = 0 THEN ? ELSE `return_time` END,
                `condition_after` = ?,
                `returned_by` = ?, `received_by` = ?, `return_remarks` = ?,
                `issuance_status` = ?,
                `updated_at` = NOW()
            WHERE `issuance_id` = ?
        ", [
            $new_returned, $new_pending,
            $new_pending, $actual_return,
            $new_pending, $return_time,
            $condition_after,
            $returned_by, $received_by, $return_remarks,
            $new_status,
            $issuance_id
        ]);

        if ($query) {
            $tool = $this->db->query("SELECT * FROM tbl_tools WHERE tool_id = ?", [$iss->tool_id])->getRow();
            if ($tool) {
                $new_on_hand = intval($tool->quantity_on_hand) + $quantity_return;
                $totalQty = intval($tool->quantity);

                $new_availability = 'AVAILABLE';
                if ($condition_after == 'DAMAGED' || $condition_after == 'POOR') {
                    $new_availability = 'DAMAGED';
                } elseif ($new_on_hand < $totalQty) {
                    $new_availability = 'ASSIGNED';
                }

                $new_condition = $tool->tool_condition;
                if ($new_pending == 0) {
                    $new_condition = $condition_after;
                }

                $this->db->query("
                    UPDATE tbl_tools SET
                        quantity_on_hand = ?,
                        availability = ?,
                        tool_condition = ?,
                        updated_at = NOW()
                    WHERE tool_id = ?
                ", [$new_on_hand, $new_availability, $new_condition, $iss->tool_id]);

                // Re-verify
                $openCount = $this->db->query("
                    SELECT COALESCE(SUM(quantity_pending), 0) as pending
                    FROM tbl_tool_issuances
                    WHERE tool_id = ? AND quantity_pending > 0
                ", [$iss->tool_id])->getRow()->pending;

                if (intval($openCount) == 0) {
                    if ($new_availability == 'AVAILABLE' || $new_availability == 'DAMAGED') {
                        $this->db->query("
                            UPDATE tbl_tools 
                            SET assigned_to = NULL, truck_id = NULL, truck_plate = NULL 
                            WHERE tool_id = ?
                        ", [$iss->tool_id]);
                    }
                } elseif ($new_availability !== 'DAMAGED') {
                    // Other units are still out — reassign, but don't clobber a just-set DAMAGED flag
                    $this->db->query("
                        UPDATE tbl_tools SET availability = 'ASSIGNED' WHERE tool_id = ?
                    ", [$iss->tool_id]);
                }
            }

            $msg = ($new_pending == 0) 
                ? 'All ' . $new_returned . ' item(s) returned.' 
                : $quantity_return . ' returned, ' . $new_pending . ' still out.';

            return ['status' => 'success', 'message' => $msg];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while returning tool.'];
        }
    }

    public function deleteIssuance()
    {
        $issuance_id = $this->request->getPost('issuance_id');

        $iss = $this->db->query("SELECT * FROM tbl_tool_issuances WHERE issuance_id = ?", [$issuance_id])->getRow();

        $query = $this->db->query("DELETE FROM `tbl_tool_issuances` WHERE `issuance_id` = ?", [$issuance_id]);

        if ($query) {
            if ($iss) {
                $tool = $this->db->query("SELECT * FROM tbl_tools WHERE tool_id = ?", [$iss->tool_id])->getRow();
                if ($tool) {
                    $restore = intval($iss->quantity_pending);
                    $new_on_hand = intval($tool->quantity_on_hand) + $restore;

                    $openCount = $this->db->query("
                        SELECT COALESCE(SUM(quantity_pending), 0) as pending
                        FROM tbl_tool_issuances
                        WHERE tool_id = ? AND quantity_pending > 0
                    ", [$iss->tool_id])->getRow()->pending;

                    $new_availability = 'AVAILABLE';
                    if (intval($openCount) > 0) {
                        $new_availability = 'ASSIGNED';
                    }

                    if (in_array($tool->tool_condition, ['DAMAGED','POOR'])) {
                        $new_availability = 'DAMAGED';
                    }

                    if (intval($openCount) == 0) {
                        $this->db->query("
                            UPDATE tbl_tools 
                            SET quantity_on_hand = ?, availability = ?, 
                                assigned_to = NULL, truck_id = NULL, truck_plate = NULL, 
                                updated_at = NOW()
                            WHERE tool_id = ?
                        ", [$new_on_hand, $new_availability, $iss->tool_id]);
                    } else {
                        $this->db->query("
                            UPDATE tbl_tools 
                            SET quantity_on_hand = ?, availability = ?, updated_at = NOW()
                            WHERE tool_id = ?
                        ", [$new_on_hand, $new_availability, $iss->tool_id]);
                    }
                }
            }
            return ['status' => 'success', 'message' => 'Issuance Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting tool.'];
        }
    }

    // ==============================
    // TOOL JOURNEY
    // ==============================
    public function getToolJourney($tool_id)
    {
        $journey = [];

        $tool = $this->db->query("SELECT * FROM tbl_tools WHERE tool_id = ?", [$tool_id])->getRowArray();
        if ($tool) {
            $journey[] = [
                'date' => $tool['created_at'],
                'type' => 'REGISTERED',
                'description' => 'Tool registered: ' . $tool['tool_code'],
                'details' => 'Name: ' . $tool['tool_name'] 
                    . ' | Qty: ' . intval($tool['quantity']) 
                    . ' | Cost: ₱' . number_format($tool['purchase_cost'], 2)
            ];
        }

        $issuances = $this->db->query("
            SELECT * FROM tbl_tool_issuances WHERE tool_id = ? ORDER BY issue_date ASC, issuance_id ASC
        ", [$tool_id])->getResultArray();

        foreach ($issuances as $i) {
            $journey[] = [
                'date' => $i['issue_date'],
                'type' => 'ISSUED',
                'description' => 'Issued ' . intval($i['quantity_issued']) . ' item(s) to ' . $i['issued_to'],
                'details' => 'Code: ' . $i['issuance_code'] 
                    . ' | Expected: ' . ($i['expected_return'] ?: '—') 
                    . ' | Condition: ' . $i['condition_before'] 
                    . ($i['truck_plate'] ? ' | Truck: ' . $i['truck_plate'] : '')
            ];

            if (intval($i['quantity_returned']) > 0) {
                $journey[] = [
                    'date' => $i['actual_return'] ?: $i['issue_date'],
                    'type' => 'RETURNED',
                    'description' => 'Returned ' . intval($i['quantity_returned']) . ' item(s)',
                    'details' => 'Code: ' . $i['issuance_code']
                        . ' | Condition After: ' . ($i['condition_after'] ?: '—')
                        . ' | Received by: ' . ($i['received_by'] ?: '—')
                ];
            }

            if (intval($i['quantity_pending']) > 0 && $i['expected_return'] && strtotime($i['expected_return']) < time()) {
                $journey[] = [
                    'date' => date('Y-m-d'),
                    'type' => 'OVERDUE',
                    'description' => intval($i['quantity_pending']) . ' item(s) overdue',
                    'details' => 'Still with: ' . $i['issued_to'] . ' | Expected back: ' . $i['expected_return']
                ];
            }
        }

        usort($journey, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $journey;
    }
}