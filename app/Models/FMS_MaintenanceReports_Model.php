<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_MaintenanceReports_Model extends Model
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
    // 1. MAINTENANCE HISTORY
    // ==============================
    public function getMaintenanceHistory($filters)
    {
        $where = "WHERE r.maintenance_date BETWEEN ? AND ?";
        $params = [$filters['date_from'], $filters['date_to']];

        if (!empty($filters['status'])) {
            $where .= " AND r.status = ?";
            $params[] = $filters['status'];
        }

        $rows = $this->db->query("
            SELECT r.record_code, r.truck_plate, r.maintenance_date, r.maintenance_type,
                   r.service_category, r.technician, r.total_cost, r.status
            FROM tbl_maintenance_records r
            $where
            ORDER BY r.maintenance_date DESC, r.record_id DESC
        ", $params)->getResultArray();

        $totalCost = 0;
        $completed = 0;
        foreach ($rows as $row) {
            $totalCost += floatval($row['total_cost']);
            if ($row['status'] === 'COMPLETED') $completed++;
        }

        return [
            'title' => 'Maintenance History',
            'columns' => [
                ['key' => 'record_code', 'label' => 'Record #', 'align' => 'left', 'format' => 'text'],
                ['key' => 'truck_plate', 'label' => 'Truck', 'align' => 'left', 'format' => 'text'],
                ['key' => 'maintenance_date', 'label' => 'Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'maintenance_type', 'label' => 'Type', 'align' => 'left', 'format' => 'badge'],
                ['key' => 'service_category', 'label' => 'Category', 'align' => 'left', 'format' => 'text'],
                ['key' => 'technician', 'label' => 'Technician', 'align' => 'left', 'format' => 'text'],
                ['key' => 'total_cost', 'label' => 'Total Cost', 'align' => 'right', 'format' => 'currency'],
                ['key' => 'status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'COMPLETED' => 'badge-success', 'IN_PROGRESS' => 'badge-warning', 'PENDING' => 'badge-secondary', 'CANCELLED' => 'badge-danger'
                ]],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Records', 'value' => count($rows), 'icon' => 'bi-clipboard-data'],
                ['label' => 'Total Cost', 'value' => '&#8369;' . number_format($totalCost, 2), 'icon' => 'bi-cash-stack'],
                ['label' => 'Completed', 'value' => $completed, 'icon' => 'bi-check-circle'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // 2. MAINTENANCE COST (by truck)
    // ==============================
    public function getMaintenanceCost($filters)
    {
        $params = [$filters['date_from'], $filters['date_to']];

        $rows = $this->db->query("
            SELECT truck_plate,
                   COUNT(*) AS record_count,
                   SUM(labor_cost) AS labor_cost,
                   SUM(parts_cost) AS parts_cost,
                   SUM(other_cost) AS other_cost,
                   SUM(total_cost) AS total_cost
            FROM tbl_maintenance_records
            WHERE maintenance_date BETWEEN ? AND ?
            GROUP BY truck_plate
            ORDER BY total_cost DESC
        ", $params)->getResultArray();

        $grandLabor = 0; $grandParts = 0; $grandOther = 0; $grandTotal = 0; $recordCount = 0;
        foreach ($rows as $row) {
            $grandLabor += floatval($row['labor_cost']);
            $grandParts += floatval($row['parts_cost']);
            $grandOther += floatval($row['other_cost']);
            $grandTotal += floatval($row['total_cost']);
            $recordCount += intval($row['record_count']);
        }
        $avgCost = $recordCount > 0 ? $grandTotal / $recordCount : 0;
        $topTruck = !empty($rows) ? $rows[0]['truck_plate'] : '—';

        return [
            'title' => 'Maintenance Cost by Truck',
            'columns' => [
                ['key' => 'truck_plate', 'label' => 'Truck', 'align' => 'left', 'format' => 'text'],
                ['key' => 'record_count', 'label' => 'Records', 'align' => 'right', 'format' => 'number'],
                ['key' => 'labor_cost', 'label' => 'Labor Cost', 'align' => 'right', 'format' => 'currency'],
                ['key' => 'parts_cost', 'label' => 'Parts Cost', 'align' => 'right', 'format' => 'currency'],
                ['key' => 'other_cost', 'label' => 'Other Cost', 'align' => 'right', 'format' => 'currency'],
                ['key' => 'total_cost', 'label' => 'Total Cost', 'align' => 'right', 'format' => 'currency'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Maintenance Cost', 'value' => '&#8369;' . number_format($grandTotal, 2), 'icon' => 'bi-cash-stack'],
                ['label' => 'Average Cost / Record', 'value' => '&#8369;' . number_format($avgCost, 2), 'icon' => 'bi-graph-up'],
                ['label' => 'Highest Cost Truck', 'value' => $topTruck, 'icon' => 'bi-truck'],
            ],
            'totals' => [
                'label' => 'TOTAL',
                'values' => [
                    'labor_cost' => $grandLabor,
                    'parts_cost' => $grandParts,
                    'other_cost' => $grandOther,
                    'total_cost' => $grandTotal,
                ],
            ],
        ];
    }

    // ==============================
    // 3. UPCOMING MAINTENANCE
    // ==============================
    public function getUpcomingMaintenance($filters)
    {
        $where = "WHERE status = 'SCHEDULED'";
        $params = [];

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where .= " AND scheduled_date BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        }

        $rows = $this->db->query("
            SELECT schedule_code, truck_plate, maintenance_type, scheduled_date,
                   next_service_odometer, priority, status
            FROM tbl_maintenance_schedules
            $where
            ORDER BY scheduled_date ASC
        ", $params)->getResultArray();

        $dueSoon = 0; $highPriority = 0;
        foreach ($rows as $row) {
            if ($row['scheduled_date'] && strtotime($row['scheduled_date']) <= strtotime('+7 days')) $dueSoon++;
            if ($row['priority'] === 'HIGH') $highPriority++;
        }

        return [
            'title' => 'Upcoming Maintenance',
            'columns' => [
                ['key' => 'schedule_code', 'label' => 'Schedule #', 'align' => 'left', 'format' => 'text'],
                ['key' => 'truck_plate', 'label' => 'Truck', 'align' => 'left', 'format' => 'text'],
                ['key' => 'maintenance_type', 'label' => 'Type', 'align' => 'left', 'format' => 'badge'],
                ['key' => 'scheduled_date', 'label' => 'Scheduled Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'next_service_odometer', 'label' => 'Next Service Odo', 'align' => 'right', 'format' => 'number'],
                ['key' => 'priority', 'label' => 'Priority', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'HIGH' => 'badge-danger', 'NORMAL' => 'badge-info', 'LOW' => 'badge-secondary'
                ]],
                ['key' => 'status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'SCHEDULED' => 'badge-primary'
                ]],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Scheduled', 'value' => count($rows), 'icon' => 'bi-calendar-check'],
                ['label' => 'Due Within 7 Days', 'value' => $dueSoon, 'icon' => 'bi-alarm'],
                ['label' => 'High Priority', 'value' => $highPriority, 'icon' => 'bi-exclamation-triangle'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // 4. PARTS REPLACEMENT
    // ==============================
    public function getPartsReplacement($filters)
    {
        $params = [$filters['date_from'], $filters['date_to']];

        $rows = $this->db->query("
            SELECT p.part_name, r.truck_plate, r.maintenance_date, p.quantity,
                   p.unit_cost, p.total_cost, p.supplier
            FROM tbl_maintenance_parts p
            JOIN tbl_maintenance_records r ON p.record_id = r.record_id
            WHERE r.maintenance_date BETWEEN ? AND ?
            ORDER BY r.maintenance_date DESC
        ", $params)->getResultArray();

        $totalQty = 0; $totalCost = 0; $uniqueParts = [];
        foreach ($rows as $row) {
            $totalQty += floatval($row['quantity']);
            $totalCost += floatval($row['total_cost']);
            $uniqueParts[$row['part_name']] = true;
        }

        return [
            'title' => 'Parts Replacement',
            'columns' => [
                ['key' => 'part_name', 'label' => 'Part Name', 'align' => 'left', 'format' => 'text'],
                ['key' => 'truck_plate', 'label' => 'Truck', 'align' => 'left', 'format' => 'text'],
                ['key' => 'maintenance_date', 'label' => 'Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'quantity', 'label' => 'Qty', 'align' => 'right', 'format' => 'number'],
                ['key' => 'unit_cost', 'label' => 'Unit Cost', 'align' => 'right', 'format' => 'currency'],
                ['key' => 'total_cost', 'label' => 'Total Cost', 'align' => 'right', 'format' => 'currency'],
                ['key' => 'supplier', 'label' => 'Supplier', 'align' => 'left', 'format' => 'text'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Parts Replaced', 'value' => number_format($totalQty, 0), 'icon' => 'bi-gear'],
                ['label' => 'Total Parts Cost', 'value' => '&#8369;' . number_format($totalCost, 2), 'icon' => 'bi-cash-stack'],
                ['label' => 'Unique Part Types', 'value' => count($uniqueParts), 'icon' => 'bi-list-ul'],
            ],
            'totals' => [
                'label' => 'TOTAL',
                'values' => ['total_cost' => $totalCost],
            ],
        ];
    }

    // ==============================
    // 5. REGISTRATION EXPIRATION
    // ==============================
    public function getRegistrationExpiration($filters)
    {
        return $this->getDocumentExpiration('REGISTRATION', 'Registration Expiration', 'total registrations tracked');
    }

    // ==============================
    // 6. INSURANCE EXPIRATION
    // ==============================
    public function getInsuranceExpiration($filters)
    {
        $rows = $this->db->query("
            SELECT t.plate_number, d.provider_name, d.policy_number, d.coverage_amount,
                   d.issue_date, d.expiration_date
            FROM tbl_truck_documents d
            JOIN tbl_trucks t ON d.truck_id = t.truck_id
            WHERE d.document_type = 'INSURANCE'
            ORDER BY d.expiration_date ASC
        ")->getResultArray();

        $expiring = 0; $expired = 0;
        foreach ($rows as &$row) {
            $row['computed_status'] = $this->computeExpiryStatus($row['expiration_date']);
            if ($row['computed_status'] === 'EXPIRING') $expiring++;
            if ($row['computed_status'] === 'EXPIRED') $expired++;
        }
        unset($row);

        return [
            'title' => 'Insurance Expiration',
            'columns' => [
                ['key' => 'plate_number', 'label' => 'Plate #', 'align' => 'left', 'format' => 'text'],
                ['key' => 'provider_name', 'label' => 'Provider', 'align' => 'left', 'format' => 'text'],
                ['key' => 'policy_number', 'label' => 'Policy #', 'align' => 'left', 'format' => 'text'],
                ['key' => 'expiration_date', 'label' => 'Expiration Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'computed_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'VALID' => 'badge-success', 'EXPIRING' => 'badge-warning', 'EXPIRED' => 'badge-danger'
                ]],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Policies Tracked', 'value' => count($rows), 'icon' => 'bi-shield-check'],
                ['label' => 'Expiring Soon', 'value' => $expiring, 'icon' => 'bi-exclamation-circle'],
                ['label' => 'Expired', 'value' => $expired, 'icon' => 'bi-x-circle'],
            ],
            'totals' => null,
        ];
    }

    private function getDocumentExpiration($documentType, $title, $statLabel)
    {
        $rows = $this->db->query("
            SELECT t.plate_number, d.document_number, d.issue_date, d.expiration_date
            FROM tbl_truck_documents d
            JOIN tbl_trucks t ON d.truck_id = t.truck_id
            WHERE d.document_type = ?
            ORDER BY d.expiration_date ASC
        ", [$documentType])->getResultArray();

        $expiring = 0; $expired = 0;
        foreach ($rows as &$row) {
            $row['computed_status'] = $this->computeExpiryStatus($row['expiration_date']);
            if ($row['computed_status'] === 'EXPIRING') $expiring++;
            if ($row['computed_status'] === 'EXPIRED') $expired++;
        }
        unset($row);

        return [
            'title' => $title,
            'columns' => [
                ['key' => 'plate_number', 'label' => 'Plate #', 'align' => 'left', 'format' => 'text'],
                ['key' => 'document_number', 'label' => 'Document #', 'align' => 'left', 'format' => 'text'],
                ['key' => 'issue_date', 'label' => 'Issue Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'expiration_date', 'label' => 'Expiration Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'computed_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'VALID' => 'badge-success', 'EXPIRING' => 'badge-warning', 'EXPIRED' => 'badge-danger'
                ]],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => ucfirst($statLabel), 'value' => count($rows), 'icon' => 'bi-file-earmark-check'],
                ['label' => 'Expiring Soon', 'value' => $expiring, 'icon' => 'bi-exclamation-circle'],
                ['label' => 'Expired', 'value' => $expired, 'icon' => 'bi-x-circle'],
            ],
            'totals' => null,
        ];
    }

    private function computeExpiryStatus($expiration_date)
    {
        if (empty($expiration_date)) return 'VALID';
        $exp = strtotime($expiration_date);
        $today = strtotime(date('Y-m-d'));
        if ($exp < $today) return 'EXPIRED';
        if ($exp <= strtotime('+30 days', $today)) return 'EXPIRING';
        return 'VALID';
    }

    // ==============================
    // 7. TOOLS INVENTORY
    // ==============================
    public function getToolsInventory($filters)
    {
        $where = "";
        $params = [];
        if (!empty($filters['status'])) {
            $where = "WHERE availability = ?";
            $params[] = $filters['status'];
        }

        $rows = $this->db->query("
            SELECT tool_code, tool_name, category, quantity, quantity_on_hand, availability, tool_condition
            FROM tbl_tools
            $where
            ORDER BY tool_name ASC
        ", $params)->getResultArray();

        $totalQty = 0; $available = 0; $out = 0;
        foreach ($rows as $row) {
            $totalQty += floatval($row['quantity']);
            if ($row['availability'] === 'AVAILABLE') $available++;
            if ($row['availability'] === 'ASSIGNED') $out++;
        }

        return [
            'title' => 'Tools Inventory',
            'columns' => [
                ['key' => 'tool_code', 'label' => 'Tool Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'tool_name', 'label' => 'Tool Name', 'align' => 'left', 'format' => 'text'],
                ['key' => 'category', 'label' => 'Category', 'align' => 'left', 'format' => 'text'],
                ['key' => 'quantity', 'label' => 'Total Qty', 'align' => 'right', 'format' => 'number'],
                ['key' => 'quantity_on_hand', 'label' => 'On Hand', 'align' => 'right', 'format' => 'number'],
                ['key' => 'availability', 'label' => 'Availability', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'AVAILABLE' => 'badge-success', 'ASSIGNED' => 'badge-warning', 'UNDER_REPAIR' => 'badge-info',
                    'DAMAGED' => 'badge-danger', 'RETIRED' => 'badge-secondary', 'LOST' => 'badge-danger'
                ]],
                ['key' => 'tool_condition', 'label' => 'Condition', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'GOOD' => 'badge-success', 'FAIR' => 'badge-warning', 'POOR' => 'badge-danger', 'DAMAGED' => 'badge-danger'
                ]],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Tools', 'value' => number_format($totalQty, 0), 'icon' => 'bi-tools'],
                ['label' => 'Available', 'value' => $available, 'icon' => 'bi-check-circle'],
                ['label' => 'Assigned / Out', 'value' => $out, 'icon' => 'bi-box-arrow-right'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // 8. TOOL ISSUANCE
    // ==============================
    public function getToolIssuance($filters)
    {
        $where = "WHERE issue_date BETWEEN ? AND ?";
        $params = [$filters['date_from'], $filters['date_to']];

        if (!empty($filters['status'])) {
            $where .= " AND issuance_status = ?";
            $params[] = $filters['status'];
        }

        $rows = $this->db->query("
            SELECT issuance_code, tool_name, issued_to, issue_date, expected_return,
                   quantity_pending, issuance_status
            FROM tbl_tool_issuances
            $where
            ORDER BY issue_date DESC
        ", $params)->getResultArray();

        $currentlyOut = 0; $overdue = 0;
        foreach ($rows as &$row) {
            $isOverdue = intval($row['quantity_pending']) > 0 && !empty($row['expected_return']) && strtotime($row['expected_return']) < strtotime(date('Y-m-d'));
            if (intval($row['quantity_pending']) > 0) $currentlyOut++;
            if ($isOverdue) {
                $overdue++;
                $row['issuance_status'] = 'OVERDUE';
            }
        }
        unset($row);

        return [
            'title' => 'Tool Issuance',
            'columns' => [
                ['key' => 'issuance_code', 'label' => 'Issuance #', 'align' => 'left', 'format' => 'text'],
                ['key' => 'tool_name', 'label' => 'Tool', 'align' => 'left', 'format' => 'text'],
                ['key' => 'issued_to', 'label' => 'Issued To', 'align' => 'left', 'format' => 'text'],
                ['key' => 'issue_date', 'label' => 'Issue Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'expected_return', 'label' => 'Expected Return', 'align' => 'left', 'format' => 'date'],
                ['key' => 'quantity_pending', 'label' => 'Pending Qty', 'align' => 'right', 'format' => 'number'],
                ['key' => 'issuance_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'ISSUED' => 'badge-info', 'RETURNED' => 'badge-success', 'OVERDUE' => 'badge-danger'
                ]],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Issuances', 'value' => count($rows), 'icon' => 'bi-box-seam'],
                ['label' => 'Currently Out', 'value' => $currentlyOut, 'icon' => 'bi-arrow-right-circle'],
                ['label' => 'Overdue', 'value' => $overdue, 'icon' => 'bi-exclamation-triangle'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // 9. SUPPLIES INVENTORY
    // ==============================
    public function getSuppliesInventory($filters)
    {
        $where = "";
        $params = [];
        if (!empty($filters['status'])) {
            $where = "WHERE status = ?";
            $params[] = $filters['status'];
        }

        $rows = $this->db->query("
            SELECT supply_code, supply_name, category, current_stock, minimum_stock, unit_cost, status
            FROM tbl_supplies
            $where
            ORDER BY supply_name ASC
        ", $params)->getResultArray();

        $lowStock = 0; $outOfStock = 0;
        foreach ($rows as $row) {
            if ($row['status'] === 'LOW_STOCK') $lowStock++;
            if ($row['status'] === 'OUT_OF_STOCK') $outOfStock++;
        }

        return [
            'title' => 'Supplies Inventory',
            'columns' => [
                ['key' => 'supply_code', 'label' => 'Supply Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'supply_name', 'label' => 'Supply Name', 'align' => 'left', 'format' => 'text'],
                ['key' => 'category', 'label' => 'Category', 'align' => 'left', 'format' => 'text'],
                ['key' => 'current_stock', 'label' => 'Current Stock', 'align' => 'right', 'format' => 'number'],
                ['key' => 'minimum_stock', 'label' => 'Minimum Stock', 'align' => 'right', 'format' => 'number'],
                ['key' => 'unit_cost', 'label' => 'Unit Cost', 'align' => 'right', 'format' => 'currency'],
                ['key' => 'status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'IN_STOCK' => 'badge-success', 'LOW_STOCK' => 'badge-warning', 'OUT_OF_STOCK' => 'badge-danger'
                ]],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Supplies', 'value' => count($rows), 'icon' => 'bi-boxes'],
                ['label' => 'Low Stock', 'value' => $lowStock, 'icon' => 'bi-exclamation-circle'],
                ['label' => 'Out of Stock', 'value' => $outOfStock, 'icon' => 'bi-x-circle'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // 10. STOCK MOVEMENT
    // ==============================
    public function getStockMovement($filters)
    {
        $where = "WHERE transaction_date BETWEEN ? AND ?";
        $params = [$filters['date_from'], $filters['date_to']];

        if (!empty($filters['status'])) {
            $where .= " AND transaction_type = ?";
            $params[] = $filters['status'];
        }

        $rows = $this->db->query("
            SELECT transaction_code, supply_name, transaction_date, transaction_type,
                   quantity, previous_stock, new_stock
            FROM tbl_supply_transactions
            $where
            ORDER BY transaction_date DESC, transaction_id DESC
        ", $params)->getResultArray();

        $stockIn = 0; $stockOut = 0;
        foreach ($rows as $row) {
            if ($row['transaction_type'] === 'STOCK_IN') $stockIn++;
            if ($row['transaction_type'] === 'STOCK_OUT') $stockOut++;
        }

        return [
            'title' => 'Stock Movement',
            'columns' => [
                ['key' => 'transaction_code', 'label' => 'Transaction #', 'align' => 'left', 'format' => 'text'],
                ['key' => 'supply_name', 'label' => 'Supply', 'align' => 'left', 'format' => 'text'],
                ['key' => 'transaction_date', 'label' => 'Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'transaction_type', 'label' => 'Type', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'STOCK_IN' => 'badge-success', 'STOCK_OUT' => 'badge-warning', 'RETURN' => 'badge-info',
                    'ADJUSTMENT' => 'badge-secondary', 'DAMAGED' => 'badge-danger', 'DISPOSAL' => 'badge-danger'
                ]],
                ['key' => 'quantity', 'label' => 'Quantity', 'align' => 'right', 'format' => 'number'],
                ['key' => 'previous_stock', 'label' => 'Previous Stock', 'align' => 'right', 'format' => 'number'],
                ['key' => 'new_stock', 'label' => 'New Stock', 'align' => 'right', 'format' => 'number'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Transactions', 'value' => count($rows), 'icon' => 'bi-arrow-left-right'],
                ['label' => 'Stock In', 'value' => $stockIn, 'icon' => 'bi-box-arrow-in-down'],
                ['label' => 'Stock Out', 'value' => $stockOut, 'icon' => 'bi-box-arrow-up'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // 11. LOW STOCK
    // ==============================
    public function getLowStock($filters)
    {
        $rows = $this->db->query("
            SELECT supply_code, supply_name, category, current_stock, minimum_stock, status
            FROM tbl_supplies
            WHERE status IN ('LOW_STOCK','OUT_OF_STOCK')
            ORDER BY current_stock ASC
        ")->getResultArray();

        $lowStock = 0; $outOfStock = 0;
        foreach ($rows as $row) {
            if ($row['status'] === 'LOW_STOCK') $lowStock++;
            if ($row['status'] === 'OUT_OF_STOCK') $outOfStock++;
        }

        return [
            'title' => 'Low Stock Report',
            'columns' => [
                ['key' => 'supply_code', 'label' => 'Supply Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'supply_name', 'label' => 'Supply Name', 'align' => 'left', 'format' => 'text'],
                ['key' => 'category', 'label' => 'Category', 'align' => 'left', 'format' => 'text'],
                ['key' => 'current_stock', 'label' => 'Current Stock', 'align' => 'right', 'format' => 'number'],
                ['key' => 'minimum_stock', 'label' => 'Minimum Stock', 'align' => 'right', 'format' => 'number'],
                ['key' => 'status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => [
                    'LOW_STOCK' => 'badge-warning', 'OUT_OF_STOCK' => 'badge-danger'
                ]],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Low/Out Items', 'value' => count($rows), 'icon' => 'bi-exclamation-diamond'],
                ['label' => 'Out of Stock', 'value' => $outOfStock, 'icon' => 'bi-x-circle'],
                ['label' => 'Low Stock', 'value' => $lowStock, 'icon' => 'bi-exclamation-circle'],
            ],
            'totals' => null,
        ];
    }
}
