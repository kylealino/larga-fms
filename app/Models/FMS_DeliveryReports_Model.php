<?php

namespace App\Models;

use CodeIgniter\Model;

class FMS_DeliveryReports_Model extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    // ==============================
    // 1. DELIVERY REPORT
    // ==============================
    public function getDeliveryReport($filters)
    {
        $date_from = $filters['date_from'];
        $date_to = $filters['date_to'];
        $status = $filters['status'] ?? null;

        $sql = "
            SELECT d.dr_id, d.dr_code, d.dr_date, d.dr_status, d.origin, d.destination,
                   c.customer_name
            FROM tbl_delivery_receipts d
            LEFT JOIN tbl_customers c ON d.customer_id = c.customer_id
            WHERE d.dr_date BETWEEN ? AND ?
        ";
        $params = [$date_from, $date_to];

        if (!empty($status)) {
            $sql .= " AND d.dr_status = ? ";
            $params[] = $status;
        }

        $sql .= " ORDER BY d.dr_date DESC, d.dr_id DESC ";

        $rows = $this->db->query($sql, $params)->getResultArray();

        $badgeMap = [
            'PENDING' => 'badge-secondary',
            'IN_TRANSIT' => 'badge-info',
            'ARRIVED' => 'badge-info',
            'DELIVERED' => 'badge-success',
            'PARTIALLY_DELIVERED' => 'badge-warning',
            'CONTAINER_FOR_RETURN' => 'badge-warning',
            'CONTAINER_RETURNED' => 'badge-success',
            'FAILED_DELIVERY' => 'badge-danger',
            'CANCELLED' => 'badge-danger',
        ];

        $columns = [
            ['key' => 'dr_code', 'label' => 'DR #', 'align' => 'left', 'format' => 'text'],
            ['key' => 'customer_name', 'label' => 'Customer', 'align' => 'left', 'format' => 'text'],
            ['key' => 'origin', 'label' => 'Origin', 'align' => 'left', 'format' => 'text'],
            ['key' => 'destination', 'label' => 'Destination', 'align' => 'left', 'format' => 'text'],
            ['key' => 'dr_date', 'label' => 'DR Date', 'align' => 'left', 'format' => 'date'],
            ['key' => 'dr_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
        ];

        $total = count($rows);
        $delivered = 0;
        $inTransit = 0;
        $failedOrPartial = 0;

        foreach ($rows as $row) {
            if ($row['dr_status'] === 'DELIVERED') $delivered++;
            if (in_array($row['dr_status'], ['IN_TRANSIT', 'ARRIVED'])) $inTransit++;
            if (in_array($row['dr_status'], ['FAILED_DELIVERY', 'PARTIALLY_DELIVERED'])) $failedOrPartial++;
        }

        $stats = [
            ['label' => 'Total DRs', 'value' => $total, 'icon' => 'bi-truck'],
            ['label' => 'Delivered', 'value' => $delivered, 'icon' => 'bi-check-circle'],
            ['label' => 'In Transit', 'value' => $inTransit, 'icon' => 'bi-clock-history'],
            ['label' => 'Failed / Partial', 'value' => $failedOrPartial, 'icon' => 'bi-exclamation-triangle'],
        ];

        return [
            'title' => 'Delivery Report',
            'columns' => $columns,
            'rows' => $rows,
            'stats' => $stats,
            'totals' => null,
        ];
    }

    // ==============================
    // 2. DR REPORT (ITEM / LINE LEVEL)
    // ==============================
    public function getDRReport($filters)
    {
        $date_from = $filters['date_from'];
        $date_to = $filters['date_to'];

        $sql = "
            SELECT i.item_id, i.dr_id, i.item_description, i.quantity_dispatched, i.quantity_delivered,
                   i.quantity_shortage, i.quantity_damaged, i.unit, i.condition_on_arrival,
                   d.dr_code, d.dr_date
            FROM tbl_delivery_receipt_items i
            JOIN tbl_delivery_receipts d ON i.dr_id = d.dr_id
            WHERE d.dr_date BETWEEN ? AND ?
            ORDER BY d.dr_date DESC, i.item_id DESC
        ";
        $rows = $this->db->query($sql, [$date_from, $date_to])->getResultArray();

        $badgeMap = [
            'GOOD' => 'badge-success',
            'FAIR' => 'badge-info',
            'POOR' => 'badge-warning',
            'DAMAGED' => 'badge-danger',
            'SHORT' => 'badge-warning',
            'REJECTED' => 'badge-danger',
        ];

        $columns = [
            ['key' => 'dr_code', 'label' => 'DR #', 'align' => 'left', 'format' => 'text'],
            ['key' => 'item_description', 'label' => 'Item Description', 'align' => 'left', 'format' => 'text'],
            ['key' => 'quantity_dispatched', 'label' => 'Qty Dispatched', 'align' => 'right', 'format' => 'number'],
            ['key' => 'quantity_delivered', 'label' => 'Qty Delivered', 'align' => 'right', 'format' => 'number'],
            ['key' => 'quantity_shortage', 'label' => 'Shortage', 'align' => 'right', 'format' => 'number'],
            ['key' => 'quantity_damaged', 'label' => 'Damaged', 'align' => 'right', 'format' => 'number'],
            ['key' => 'condition_on_arrival', 'label' => 'Condition', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
        ];

        $total = count($rows);
        $withShortage = 0;
        $withDamage = 0;

        foreach ($rows as $row) {
            if ((float) $row['quantity_shortage'] > 0) $withShortage++;
            if ((float) $row['quantity_damaged'] > 0) $withDamage++;
        }

        $stats = [
            ['label' => 'Total Line Items', 'value' => $total, 'icon' => 'bi-list-check'],
            ['label' => 'With Shortage', 'value' => $withShortage, 'icon' => 'bi-dash-circle'],
            ['label' => 'With Damage', 'value' => $withDamage, 'icon' => 'bi-exclamation-octagon'],
        ];

        return [
            'title' => 'DR Report',
            'columns' => $columns,
            'rows' => $rows,
            'stats' => $stats,
            'totals' => null,
        ];
    }

    // ==============================
    // 3. POD MONITORING
    // ==============================
    public function getPODMonitoring($filters)
    {
        $date_from = $filters['date_from'];
        $date_to = $filters['date_to'];

        $sql = "
            SELECT d.dr_id, d.dr_code, d.dr_date, d.dr_status, c.customer_name,
                   pod.pod_id, pod.received_by, pod.date_received
            FROM tbl_delivery_receipts d
            LEFT JOIN tbl_customers c ON d.customer_id = c.customer_id
            LEFT JOIN tbl_delivery_receipt_pod pod ON pod.pod_id = (
                SELECT MAX(p2.pod_id) FROM tbl_delivery_receipt_pod p2 WHERE p2.dr_id = d.dr_id
            )
            WHERE d.dr_date BETWEEN ? AND ?
            ORDER BY d.dr_date DESC, d.dr_id DESC
        ";
        $rows = $this->db->query($sql, [$date_from, $date_to])->getResultArray();

        $drStatusBadgeMap = [
            'PENDING' => 'badge-secondary',
            'IN_TRANSIT' => 'badge-info',
            'ARRIVED' => 'badge-info',
            'DELIVERED' => 'badge-success',
            'PARTIALLY_DELIVERED' => 'badge-warning',
            'CONTAINER_FOR_RETURN' => 'badge-warning',
            'CONTAINER_RETURNED' => 'badge-success',
            'FAILED_DELIVERY' => 'badge-danger',
            'CANCELLED' => 'badge-danger',
        ];

        $podBadgeMap = [
            'CAPTURED' => 'badge-success',
            'MISSING' => 'badge-danger',
        ];

        $captured = 0;
        $missing = 0;

        foreach ($rows as &$row) {
            if (!empty($row['pod_id'])) {
                $row['pod_status'] = 'CAPTURED';
                $row['received_by'] = $row['received_by'] ?: 'Missing';
                $captured++;
            } else {
                $row['pod_status'] = 'MISSING';
                $row['received_by'] = 'Missing';
                $missing++;
            }
        }
        unset($row);

        $columns = [
            ['key' => 'dr_code', 'label' => 'DR #', 'align' => 'left', 'format' => 'text'],
            ['key' => 'customer_name', 'label' => 'Customer', 'align' => 'left', 'format' => 'text'],
            ['key' => 'dr_date', 'label' => 'DR Date', 'align' => 'left', 'format' => 'date'],
            ['key' => 'dr_status', 'label' => 'DR Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $drStatusBadgeMap],
            ['key' => 'received_by', 'label' => 'Received By', 'align' => 'left', 'format' => 'text'],
            ['key' => 'date_received', 'label' => 'Date Received', 'align' => 'left', 'format' => 'date'],
            ['key' => 'pod_status', 'label' => 'POD Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $podBadgeMap],
        ];

        $stats = [
            ['label' => 'Total DRs', 'value' => count($rows), 'icon' => 'bi-truck'],
            ['label' => 'POD Captured', 'value' => $captured, 'icon' => 'bi-file-earmark-check'],
            ['label' => 'POD Missing', 'value' => $missing, 'icon' => 'bi-file-earmark-excel'],
        ];

        return [
            'title' => 'POD Monitoring',
            'columns' => $columns,
            'rows' => $rows,
            'stats' => $stats,
            'totals' => null,
        ];
    }

    // ==============================
    // 4. DELIVERY PERFORMANCE
    // ==============================
    public function getDeliveryPerformance($filters)
    {
        $date_from = $filters['date_from'];
        $date_to = $filters['date_to'];

        $sql = "
            SELECT t.trip_id, t.trip_code, t.expected_delivery_date, c.customer_name,
                   dp.actual_delivery_date AS dispatch_actual_delivery_date,
                   pod.date_received AS pod_date_received
            FROM tbl_trips t
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            LEFT JOIN tbl_dispatch dp ON t.trip_id = dp.trip_id
            LEFT JOIN tbl_delivery_receipts dr ON dr.trip_id = t.trip_id
            LEFT JOIN tbl_delivery_receipt_pod pod ON pod.pod_id = (
                SELECT MAX(p2.pod_id) FROM tbl_delivery_receipt_pod p2 WHERE p2.dr_id = dr.dr_id
            )
            WHERE t.expected_delivery_date BETWEEN ? AND ?
            ORDER BY t.expected_delivery_date DESC, t.trip_id DESC
        ";
        $rows = $this->db->query($sql, [$date_from, $date_to])->getResultArray();

        $badgeMap = [
            'ON_TIME' => 'badge-success',
            'LATE' => 'badge-danger',
            'UNKNOWN' => 'badge-secondary',
        ];

        $evaluated = 0;
        $onTime = 0;
        $late = 0;

        foreach ($rows as &$row) {
            $expected = $row['expected_delivery_date'];
            $actual = $row['dispatch_actual_delivery_date'] ?: $row['pod_date_received'];
            $row['actual_delivery_date'] = $actual;

            if (empty($expected) || empty($actual)) {
                $row['performance'] = 'UNKNOWN';
            } elseif (strtotime($actual) <= strtotime($expected)) {
                $row['performance'] = 'ON_TIME';
            } else {
                $row['performance'] = 'LATE';
            }

            if ($row['performance'] !== 'UNKNOWN') {
                $evaluated++;
                if ($row['performance'] === 'ON_TIME') $onTime++;
                if ($row['performance'] === 'LATE') $late++;
            }
        }
        unset($row);

        $onTimePct = $evaluated > 0 ? round(($onTime / $evaluated) * 100, 1) : 0;

        $columns = [
            ['key' => 'trip_code', 'label' => 'Trip Code', 'align' => 'left', 'format' => 'text'],
            ['key' => 'customer_name', 'label' => 'Customer', 'align' => 'left', 'format' => 'text'],
            ['key' => 'expected_delivery_date', 'label' => 'Expected Delivery', 'align' => 'left', 'format' => 'date'],
            ['key' => 'actual_delivery_date', 'label' => 'Actual Delivery', 'align' => 'left', 'format' => 'date'],
            ['key' => 'performance', 'label' => 'Performance', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
        ];

        $stats = [
            ['label' => 'Total Evaluated', 'value' => $evaluated, 'icon' => 'bi-speedometer2'],
            ['label' => 'On-Time %', 'value' => $onTimePct . '%', 'icon' => 'bi-check-circle'],
            ['label' => 'Late', 'value' => $late, 'icon' => 'bi-exclamation-triangle'],
        ];

        return [
            'title' => 'Delivery Performance',
            'columns' => $columns,
            'rows' => $rows,
            'stats' => $stats,
            'totals' => null,
        ];
    }

    // ==============================
    // 5. PARTIAL / FAILED DELIVERY
    // ==============================
    public function getPartialFailedDelivery($filters)
    {
        $date_from = $filters['date_from'];
        $date_to = $filters['date_to'];

        $sql = "
            SELECT d.dr_id, d.dr_code, d.dr_date, d.dr_status, d.remarks, c.customer_name
            FROM tbl_delivery_receipts d
            LEFT JOIN tbl_customers c ON d.customer_id = c.customer_id
            WHERE d.dr_status IN ('PARTIALLY_DELIVERED', 'FAILED_DELIVERY')
              AND d.dr_date BETWEEN ? AND ?
            ORDER BY d.dr_date DESC, d.dr_id DESC
        ";
        $rows = $this->db->query($sql, [$date_from, $date_to])->getResultArray();

        $badgeMap = [
            'PARTIALLY_DELIVERED' => 'badge-warning',
            'FAILED_DELIVERY' => 'badge-danger',
        ];

        $partial = 0;
        $failed = 0;

        foreach ($rows as $row) {
            if ($row['dr_status'] === 'PARTIALLY_DELIVERED') $partial++;
            if ($row['dr_status'] === 'FAILED_DELIVERY') $failed++;
        }

        $columns = [
            ['key' => 'dr_code', 'label' => 'DR #', 'align' => 'left', 'format' => 'text'],
            ['key' => 'customer_name', 'label' => 'Customer', 'align' => 'left', 'format' => 'text'],
            ['key' => 'dr_date', 'label' => 'DR Date', 'align' => 'left', 'format' => 'date'],
            ['key' => 'dr_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
            ['key' => 'remarks', 'label' => 'Remarks', 'align' => 'left', 'format' => 'text'],
        ];

        $stats = [
            ['label' => 'Total Partial / Failed', 'value' => count($rows), 'icon' => 'bi-exclamation-triangle'],
            ['label' => 'Partially Delivered', 'value' => $partial, 'icon' => 'bi-dash-circle'],
            ['label' => 'Failed Delivery', 'value' => $failed, 'icon' => 'bi-x-circle'],
        ];

        return [
            'title' => 'Partial / Failed Delivery',
            'columns' => $columns,
            'rows' => $rows,
            'stats' => $stats,
            'totals' => null,
        ];
    }
}
