<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Supply_Model extends Model
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
    private function generateSupplyCode()
    {
        $year = date('Y');
        $q = $this->db->query("SELECT COUNT(*) as total FROM tbl_supplies WHERE YEAR(created_at) = ?", [$year]);
        return 'SUP-' . $year . '-' . str_pad($q->getRow()->total + 1, 6, '0', STR_PAD_LEFT);
    }

    private function generateTransactionCode()
    {
        $year = date('Y');
        $q = $this->db->query("SELECT COUNT(*) as total FROM tbl_supply_transactions WHERE YEAR(created_at) = ?", [$year]);
        return 'STX-' . $year . '-' . str_pad($q->getRow()->total + 1, 6, '0', STR_PAD_LEFT);
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

    public function getCategories()
    {
        return $this->db->query("
            SELECT DISTINCT category FROM tbl_supplies 
            WHERE category IS NOT NULL AND category != ''
            ORDER BY category
        ")->getResultArray();
    }

    // ==============================
    // SUPPLY CRUD
    // ==============================
    public function getAllSupplies()
    {
        return $this->db->query("
            SELECT * FROM tbl_supplies
            ORDER BY supply_name ASC
        ")->getResultArray();
    }

    public function getSupply($supply_id)
    {
        $q = $this->db->query("SELECT * FROM tbl_supplies WHERE supply_id = ?", [$supply_id]);
        return $q->getRowArray();
    }

    public function saveSupply()
    {
        $supply_code = $this->generateSupplyCode();
        $supply_name = $this->request->getPost('supply_name');
        $category = $this->request->getPost('category');
        $unit = $this->request->getPost('unit');
        $current_stock = $this->request->getPost('current_stock') ?: 0;
        $minimum_stock = $this->request->getPost('minimum_stock') ?: 0;
        $reorder_level = $this->request->getPost('reorder_level') ?: 0;
        $unit_cost = $this->request->getPost('unit_cost') ?: 0;
        $supplier = $this->request->getPost('supplier');
        $storage_location = $this->request->getPost('storage_location');
        $status = $this->computeStatus($current_stock, $minimum_stock);
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_supplies`(
                `supply_code`, `supply_name`, `category`, `unit`,
                `current_stock`, `minimum_stock`, `reorder_level`,
                `unit_cost`, `supplier`, `storage_location`, `status`,
                `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $supply_code, $supply_name, $category, $unit,
            $current_stock, $minimum_stock, $reorder_level,
            $unit_cost, $supplier, $storage_location, $status,
            $remarks, $this->cuser
        ]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Supply Saved Successfully!', 'supply_code' => $supply_code];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving supply.'];
        }
    }

    public function updateSupply()
    {
        $supply_id = $this->request->getPost('supply_id');
        $supply_name = $this->request->getPost('supply_name');
        $category = $this->request->getPost('category');
        $unit = $this->request->getPost('unit');
        $current_stock = $this->request->getPost('current_stock') ?: 0;
        $minimum_stock = $this->request->getPost('minimum_stock') ?: 0;
        $reorder_level = $this->request->getPost('reorder_level') ?: 0;
        $unit_cost = $this->request->getPost('unit_cost') ?: 0;
        $supplier = $this->request->getPost('supplier');
        $storage_location = $this->request->getPost('storage_location');
        $status = $this->computeStatus($current_stock, $minimum_stock);
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_supplies` SET
                `supply_name` = ?, `category` = ?, `unit` = ?,
                `current_stock` = ?, `minimum_stock` = ?, `reorder_level` = ?,
                `unit_cost` = ?, `supplier` = ?, `storage_location` = ?,
                `status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `supply_id` = ?
        ", [
            $supply_name, $category, $unit,
            $current_stock, $minimum_stock, $reorder_level,
            $unit_cost, $supplier, $storage_location,
            $status, $remarks, $supply_id
        ]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Supply Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating supply.'];
        }
    }

    public function deleteSupply()
    {
        $supply_id = $this->request->getPost('supply_id');
        $query = $this->db->query("DELETE FROM `tbl_supplies` WHERE `supply_id` = ?", [$supply_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Supply Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting supply.'];
        }
    }

    // ==============================
    // TRANSACTIONS
    // ==============================
    public function getAllTransactions()
    {
        return $this->db->query("
            SELECT * FROM tbl_supply_transactions
            ORDER BY transaction_date DESC, transaction_id DESC
        ")->getResultArray();
    }

    public function getTransaction($transaction_id)
    {
        $q = $this->db->query("SELECT * FROM tbl_supply_transactions WHERE transaction_id = ?", [$transaction_id]);
        return $q->getRowArray();
    }

    public function saveTransaction()
    {
        $transaction_code = $this->generateTransactionCode();
        $transaction_type = $this->request->getPost('transaction_type');
        $transaction_date = $this->request->getPost('transaction_date') ?: date('Y-m-d');
        $supply_id = $this->request->getPost('supply_id');
        $quantity = $this->request->getPost('quantity') ?: 0;
        $unit_cost = $this->request->getPost('unit_cost') ?: 0;
        $reference_number = $this->request->getPost('reference_number');
        $reference_module = $this->request->getPost('reference_module');
        $supplier_vendor = $this->request->getPost('supplier_vendor');
        $issued_to = $this->request->getPost('issued_to');
        $truck_id = $this->request->getPost('truck_id') ?: null;
        $truck_plate = $this->request->getPost('truck_plate');
        $purpose = $this->request->getPost('purpose');
        $remarks = $this->request->getPost('remarks');

        // Fetch supply details
        $supply = $this->db->query("SELECT * FROM tbl_supplies WHERE supply_id = ?", [$supply_id])->getRow();
        if (!$supply) {
            return ['status' => 'error', 'message' => 'Supply not found.'];
        }

        $previous_stock = floatval($supply->current_stock);
        $supply_code = $supply->supply_code;
        $supply_name = $supply->supply_name;
        $unit = $supply->unit;

        // Determine stock direction
        // STOCK_IN, RETURN → add to stock
        // STOCK_OUT, DAMAGED, DISPOSAL → subtract from stock
        // ADJUSTMENT → user provides final value in `quantity` (set directly)

        $new_stock = $previous_stock;

        if ($transaction_type == 'STOCK_IN' || $transaction_type == 'RETURN') {
            $new_stock = $previous_stock + floatval($quantity);
        } elseif ($transaction_type == 'STOCK_OUT' || $transaction_type == 'DAMAGED' || $transaction_type == 'DISPOSAL') {
            $new_stock = $previous_stock - floatval($quantity);
            if ($new_stock < 0) $new_stock = 0;
        } elseif ($transaction_type == 'ADJUSTMENT') {
            // In adjustment, `quantity` is the NEW stock value
            $new_stock = floatval($quantity);
        }

        $total_cost = floatval($quantity) * floatval($unit_cost);

        $query = $this->db->query("
            INSERT INTO `tbl_supply_transactions`(
                `transaction_code`, `transaction_type`, `transaction_date`,
                `supply_id`, `supply_code`, `supply_name`,
                `quantity`, `unit`, `unit_cost`, `total_cost`,
                `previous_stock`, `new_stock`, `reference_number`, `reference_module`,
                `supplier_vendor`, `issued_to`, `truck_id`, `truck_plate`,
                `purpose`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $transaction_code, $transaction_type, $transaction_date,
            $supply_id, $supply_code, $supply_name,
            $quantity, $unit, $unit_cost, $total_cost,
            $previous_stock, $new_stock, $reference_number, $reference_module,
            $supplier_vendor, $issued_to, $truck_id, $truck_plate,
            $purpose, $remarks, $this->cuser
        ]);

        if ($query) {
            // Update supply current stock + status
            $new_status = $this->computeStatus($new_stock, $supply->minimum_stock);
            $this->db->query("
                UPDATE tbl_supplies 
                SET current_stock = ?, status = ?, updated_at = NOW()
                WHERE supply_id = ?
            ", [$new_stock, $new_status, $supply_id]);

            return ['status' => 'success', 'message' => 'Transaction Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving transaction.'];
        }
    }

    public function deleteTransaction()
    {
        $transaction_id = $this->request->getPost('transaction_id');

        // Get transaction details to reverse stock
        $txn = $this->db->query("SELECT * FROM tbl_supply_transactions WHERE transaction_id = ?", [$transaction_id])->getRow();
        if (!$txn) {
            return ['status' => 'error', 'message' => 'Transaction not found.'];
        }

        $query = $this->db->query("DELETE FROM `tbl_supply_transactions` WHERE `transaction_id` = ?", [$transaction_id]);

        if ($query) {
            // Recompute current stock from all remaining transactions
            $this->recalcSupplyStock($txn->supply_id);
            return ['status' => 'success', 'message' => 'Transaction Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting transaction.'];
        }
    }

    // ==============================
    // SUPPLY JOURNEY
    // ==============================
    public function getSupplyJourney($supply_id)
    {
        $journey = [];

        $supply = $this->db->query("SELECT * FROM tbl_supplies WHERE supply_id = ?", [$supply_id])->getRowArray();
        if ($supply) {
            $journey[] = [
                'date' => $supply['created_at'],
                'type' => 'REGISTERED',
                'description' => 'Supply registered: ' . $supply['supply_code'],
                'details' => 'Name: ' . $supply['supply_name'] . ' | Unit: ' . ($supply['unit'] ?: '—') . ' | Unit Cost: ₱' . number_format($supply['unit_cost'], 2)
            ];
        }

        $txns = $this->db->query("
            SELECT * FROM tbl_supply_transactions WHERE supply_id = ? ORDER BY transaction_date ASC, transaction_id ASC
        ", [$supply_id])->getResultArray();

        foreach ($txns as $t) {
            $typeLabels = [
                'STOCK_IN'   => 'Stock In',
                'STOCK_OUT'  => 'Stock Out',
                'RETURN'     => 'Return',
                'ADJUSTMENT' => 'Adjustment',
                'DAMAGED'    => 'Damaged',
                'DISPOSAL'   => 'Disposal'
            ];

            $journey[] = [
                'date' => $t['transaction_date'],
                'type' => strtoupper(str_replace('_', ' ', $t['transaction_type'])),
                'description' => $typeLabels[$t['transaction_type']] . ' — ' . number_format($t['quantity'], 2) . ' ' . ($t['unit'] ?: ''),
                'details' => 'Stock: ' . number_format($t['previous_stock'], 2) . ' → ' . number_format($t['new_stock'], 2) . ' | Ref: ' . ($t['reference_number'] ?: '—') . ' | ' . ($t['remarks'] ?: '')
            ];
        }

        usort($journey, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $journey;
    }

    // ==============================
    // HELPERS
    // ==============================
    private function computeStatus($current_stock, $minimum_stock)
    {
        $current_stock = floatval($current_stock);
        $minimum_stock = floatval($minimum_stock);

        if ($current_stock <= 0) return 'OUT_OF_STOCK';
        if ($current_stock <= $minimum_stock) return 'LOW_STOCK';
        return 'IN_STOCK';
    }

    private function recalcSupplyStock($supply_id)
    {
        // Recompute current stock by summing effects of all transactions
        $txns = $this->db->query("
            SELECT * FROM tbl_supply_transactions WHERE supply_id = ? ORDER BY transaction_id ASC
        ", [$supply_id])->getResultArray();

        $current = 0;

        foreach ($txns as $t) {
            if ($t['transaction_type'] == 'STOCK_IN' || $t['transaction_type'] == 'RETURN') {
                $current += floatval($t['quantity']);
            } elseif ($t['transaction_type'] == 'STOCK_OUT' || $t['transaction_type'] == 'DAMAGED' || $t['transaction_type'] == 'DISPOSAL') {
                $current -= floatval($t['quantity']);
                if ($current < 0) $current = 0;
            } elseif ($t['transaction_type'] == 'ADJUSTMENT') {
                $current = floatval($t['quantity']);
            }
        }

        $supply = $this->db->query("SELECT minimum_stock FROM tbl_supplies WHERE supply_id = ?", [$supply_id])->getRow();
        $new_status = $this->computeStatus($current, $supply->minimum_stock ?? 0);

        $this->db->query("
            UPDATE tbl_supplies 
            SET current_stock = ?, status = ?, updated_at = NOW()
            WHERE supply_id = ?
        ", [$current, $new_status, $supply_id]);
    }
}