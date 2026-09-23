<?php

namespace App\Models;

use CodeIgniter\Model;

class FMS_OperationsReports_Model extends Model
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
    }

    // ==============================
    // TRIP REPORT
    // ==============================
    public function getTripReport($filters)
    {
        $where = "WHERE t.scheduled_date BETWEEN ? AND ?";
        $binds = [$filters['date_from'], $filters['date_to']];

        if (!empty($filters['status'])) {
            $where .= " AND t.trip_status = ?";
            $binds[] = $filters['status'];
        }

        $rows = $this->db->query("
            SELECT t.trip_id, t.trip_code, t.customer_id, t.origin, t.destination,
                   t.scheduled_date, t.trip_status, t.service_type,
                   c.customer_name
            FROM tbl_trips t
            LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
            {$where}
            ORDER BY t.scheduled_date DESC
        ", $binds)->getResultArray();

        $total = count($rows);
        $completed = 0;
        $cancelled = 0;
        $inTransit = 0;
        foreach ($rows as $row) {
            if ($row['trip_status'] === 'COMPLETED') $completed++;
            if ($row['trip_status'] === 'CANCELLED') $cancelled++;
            if ($row['trip_status'] === 'IN_TRANSIT') $inTransit++;
        }

        $badgeMap = [
            'DRAFT' => 'badge-secondary', 'SCHEDULED' => 'badge-info', 'ASSIGNED' => 'badge-info',
            'DISPATCHED' => 'badge-primary', 'IN_TRANSIT' => 'badge-warning', 'DELIVERED' => 'badge-success',
            'COMPLETED' => 'badge-success', 'CANCELLED' => 'badge-danger',
        ];

        return [
            'title' => 'Trip Report',
            'columns' => [
                ['key' => 'trip_code', 'label' => 'Trip Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'customer_name', 'label' => 'Customer', 'align' => 'left', 'format' => 'text'],
                ['key' => 'origin', 'label' => 'Origin', 'align' => 'left', 'format' => 'text'],
                ['key' => 'destination', 'label' => 'Destination', 'align' => 'left', 'format' => 'text'],
                ['key' => 'scheduled_date', 'label' => 'Scheduled Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'trip_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
                ['key' => 'service_type', 'label' => 'Service Type', 'align' => 'left', 'format' => 'text'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Trips', 'value' => $total, 'icon' => 'bi-signpost-2'],
                ['label' => 'Completed', 'value' => $completed, 'icon' => 'bi-check-circle'],
                ['label' => 'Cancelled', 'value' => $cancelled, 'icon' => 'bi-x-circle'],
                ['label' => 'In Transit', 'value' => $inTransit, 'icon' => 'bi-truck'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // DISPATCH REPORT
    // ==============================
    public function getDispatchReport($filters)
    {
        $where = "WHERE d.dispatch_date BETWEEN ? AND ?";
        $binds = [$filters['date_from'], $filters['date_to']];

        if (!empty($filters['status'])) {
            $where .= " AND d.dispatch_status = ?";
            $binds[] = $filters['status'];
        }

        $rows = $this->db->query("
            SELECT d.dispatch_id, d.dispatch_code, d.trip_id, d.dispatch_date, d.driver, d.truck,
                   d.total_distance, d.dispatch_status,
                   tr.trip_code
            FROM tbl_dispatch d
            LEFT JOIN tbl_trips tr ON d.trip_id = tr.trip_id
            {$where}
            ORDER BY d.dispatch_date DESC
        ", $binds)->getResultArray();

        $total = count($rows);
        $completed = 0;
        $inTransit = 0;
        $totalDistance = 0;
        foreach ($rows as $row) {
            if ($row['dispatch_status'] === 'COMPLETED') $completed++;
            if ($row['dispatch_status'] === 'IN_TRANSIT') $inTransit++;
            $totalDistance += (float) ($row['total_distance'] ?? 0);
        }

        $badgeMap = [
            'PENDING' => 'badge-secondary', 'DISPATCHED' => 'badge-primary', 'IN_TRANSIT' => 'badge-warning',
            'DELIVERED' => 'badge-success', 'COMPLETED' => 'badge-success', 'CANCELLED' => 'badge-danger',
        ];

        return [
            'title' => 'Dispatch Report',
            'columns' => [
                ['key' => 'dispatch_code', 'label' => 'Dispatch Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'trip_code', 'label' => 'Trip Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'driver', 'label' => 'Driver', 'align' => 'left', 'format' => 'text'],
                ['key' => 'truck', 'label' => 'Truck', 'align' => 'left', 'format' => 'text'],
                ['key' => 'dispatch_date', 'label' => 'Dispatch Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'total_distance', 'label' => 'Distance', 'align' => 'right', 'format' => 'number'],
                ['key' => 'dispatch_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Dispatches', 'value' => $total, 'icon' => 'bi-truck'],
                ['label' => 'Completed', 'value' => $completed, 'icon' => 'bi-check-circle'],
                ['label' => 'In Transit', 'value' => $inTransit, 'icon' => 'bi-signpost-2'],
                ['label' => 'Total Distance', 'value' => number_format($totalDistance, 2), 'icon' => 'bi-map'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // TRUCK UTILIZATION
    // ==============================
    public function getTruckUtilizationReport($filters)
    {
        $rows = $this->db->query("
            SELECT tk.truck_id, tk.truck_code, tk.plate_number, tk.vehicle_type, tk.truck_status,
                   COALESCE(dsub.dispatch_count, 0) as dispatch_count,
                   COALESCE(dsub.total_distance, 0) as total_distance
            FROM tbl_trucks tk
            LEFT JOIN (
                SELECT truck, COUNT(*) as dispatch_count, COALESCE(SUM(total_distance),0) as total_distance
                FROM tbl_dispatch
                WHERE dispatch_date BETWEEN ? AND ?
                GROUP BY truck
            ) dsub ON dsub.truck = tk.plate_number
            ORDER BY dispatch_count DESC, tk.truck_code ASC
        ", [$filters['date_from'], $filters['date_to']])->getResultArray();

        $total = count($rows);
        $active = 0;
        $fleetDistance = 0;
        foreach ($rows as $row) {
            if (in_array($row['truck_status'], ['ACTIVE', 'DISPATCHED', 'IN_USE'])) $active++;
            $fleetDistance += (float) ($row['total_distance'] ?? 0);
        }

        $badgeMap = [
            'ACTIVE' => 'badge-success', 'AVAILABLE' => 'badge-success', 'DISPATCHED' => 'badge-primary',
            'IN_USE' => 'badge-primary', 'MAINTENANCE' => 'badge-warning', 'INACTIVE' => 'badge-secondary',
            'DECOMMISSIONED' => 'badge-danger',
        ];

        return [
            'title' => 'Truck Utilization Report',
            'columns' => [
                ['key' => 'truck_code', 'label' => 'Truck Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'plate_number', 'label' => 'Plate Number', 'align' => 'left', 'format' => 'text'],
                ['key' => 'vehicle_type', 'label' => 'Vehicle Type', 'align' => 'left', 'format' => 'text'],
                ['key' => 'truck_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
                ['key' => 'dispatch_count', 'label' => 'Dispatches', 'align' => 'right', 'format' => 'number'],
                ['key' => 'total_distance', 'label' => 'Distance', 'align' => 'right', 'format' => 'number'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Trucks', 'value' => $total, 'icon' => 'bi-truck'],
                ['label' => 'Active/Dispatched', 'value' => $active, 'icon' => 'bi-check-circle'],
                ['label' => 'Fleet Distance', 'value' => number_format($fleetDistance, 2), 'icon' => 'bi-map'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // DRIVER PERFORMANCE
    // ==============================
    public function getDriverPerformanceReport($filters)
    {
        $rows = $this->db->query("
            SELECT dv.driver_id, dv.driver_name, dv.driver_status, dv.overall_rating, dv.years_experience,
                   COALESCE(dsub.trips_completed, 0) as trips_completed
            FROM tbl_drivers dv
            LEFT JOIN (
                SELECT driver, COUNT(*) as trips_completed
                FROM tbl_dispatch
                WHERE dispatch_date BETWEEN ? AND ?
                GROUP BY driver
            ) dsub ON dsub.driver = dv.driver_name
            ORDER BY trips_completed DESC, dv.driver_name ASC
        ", [$filters['date_from'], $filters['date_to']])->getResultArray();

        $total = count($rows);
        $ratingSum = 0;
        $ratingCount = 0;
        $topPerformers = 0;
        foreach ($rows as $row) {
            if ($row['overall_rating'] !== null) {
                $ratingSum += (float) $row['overall_rating'];
                $ratingCount++;
                if ((float) $row['overall_rating'] >= 4.5) $topPerformers++;
            }
        }
        $avgRating = $ratingCount > 0 ? round($ratingSum / $ratingCount, 2) : 0;

        $badgeMap = [
            'ACTIVE' => 'badge-success', 'AVAILABLE' => 'badge-success', 'ON_TRIP' => 'badge-primary',
            'ON_LEAVE' => 'badge-warning', 'INACTIVE' => 'badge-secondary', 'SUSPENDED' => 'badge-danger',
        ];

        return [
            'title' => 'Driver Performance Report',
            'columns' => [
                ['key' => 'driver_name', 'label' => 'Driver', 'align' => 'left', 'format' => 'text'],
                ['key' => 'driver_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
                ['key' => 'overall_rating', 'label' => 'Rating', 'align' => 'right', 'format' => 'number'],
                ['key' => 'years_experience', 'label' => 'Experience (yrs)', 'align' => 'right', 'format' => 'number'],
                ['key' => 'trips_completed', 'label' => 'Trips Completed', 'align' => 'right', 'format' => 'number'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Drivers', 'value' => $total, 'icon' => 'bi-person-badge'],
                ['label' => 'Average Rating', 'value' => $avgRating, 'icon' => 'bi-star'],
                ['label' => 'Top Performers', 'value' => $topPerformers, 'icon' => 'bi-trophy'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // HELPER ASSIGNMENT
    // ==============================
    public function getHelperAssignmentReport($filters)
    {
        $rows = $this->db->query("
            SELECT h.helper_id, h.helper_name, h.helper_status, h.employment_type, h.date_hired,
                   COALESCE(dsub.assignments_count, 0) as assignments_count
            FROM tbl_helpers h
            LEFT JOIN (
                SELECT helper, COUNT(*) as assignments_count
                FROM tbl_dispatch
                WHERE dispatch_date BETWEEN ? AND ?
                GROUP BY helper
            ) dsub ON dsub.helper = h.helper_name
            ORDER BY assignments_count DESC, h.helper_name ASC
        ", [$filters['date_from'], $filters['date_to']])->getResultArray();

        $total = count($rows);
        $assigned = 0;
        $totalAssignments = 0;
        foreach ($rows as $row) {
            if (in_array($row['helper_status'], ['ACTIVE', 'ASSIGNED', 'ON_TRIP'])) $assigned++;
            $totalAssignments += (int) ($row['assignments_count'] ?? 0);
        }

        $badgeMap = [
            'ACTIVE' => 'badge-success', 'AVAILABLE' => 'badge-success', 'ON_TRIP' => 'badge-primary',
            'ASSIGNED' => 'badge-primary', 'ON_LEAVE' => 'badge-warning', 'INACTIVE' => 'badge-secondary',
        ];

        return [
            'title' => 'Helper Assignment Report',
            'columns' => [
                ['key' => 'helper_name', 'label' => 'Helper', 'align' => 'left', 'format' => 'text'],
                ['key' => 'helper_status', 'label' => 'Status', 'align' => 'center', 'format' => 'badge', 'badge_map' => $badgeMap],
                ['key' => 'employment_type', 'label' => 'Employment Type', 'align' => 'left', 'format' => 'text'],
                ['key' => 'date_hired', 'label' => 'Date Hired', 'align' => 'left', 'format' => 'date'],
                ['key' => 'assignments_count', 'label' => 'Assignments', 'align' => 'right', 'format' => 'number'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Helpers', 'value' => $total, 'icon' => 'bi-people'],
                ['label' => 'Currently Assigned', 'value' => $assigned, 'icon' => 'bi-person-check'],
                ['label' => 'Total Assignments', 'value' => $totalAssignments, 'icon' => 'bi-clipboard-check'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // FUEL CONSUMPTION
    // ==============================
    public function getFuelConsumptionReport($filters)
    {
        $rows = $this->db->query("
            SELECT d.dispatch_id, d.dispatch_code, d.truck, d.dispatch_date, d.total_distance,
                   d.fuel_level_out, d.fuel_level_in, d.fuel_consumed
            FROM tbl_dispatch d
            WHERE d.dispatch_date BETWEEN ? AND ?
              AND d.fuel_consumed IS NOT NULL
            ORDER BY d.dispatch_date DESC
        ", [$filters['date_from'], $filters['date_to']])->getResultArray();

        $total = count($rows);
        $sum = 0;
        foreach ($rows as $row) {
            $sum += (float) ($row['fuel_consumed'] ?? 0);
        }
        $avg = $total > 0 ? round($sum / $total, 2) : 0;

        return [
            'title' => 'Fuel Consumption Report',
            'columns' => [
                ['key' => 'dispatch_code', 'label' => 'Dispatch Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'truck', 'label' => 'Truck', 'align' => 'left', 'format' => 'text'],
                ['key' => 'dispatch_date', 'label' => 'Dispatch Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'total_distance', 'label' => 'Distance', 'align' => 'right', 'format' => 'number'],
                ['key' => 'fuel_level_out', 'label' => 'Fuel Out', 'align' => 'right', 'format' => 'number'],
                ['key' => 'fuel_level_in', 'label' => 'Fuel In', 'align' => 'right', 'format' => 'number'],
                ['key' => 'fuel_consumed', 'label' => 'Fuel Consumed', 'align' => 'right', 'format' => 'number'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Fuel Consumed', 'value' => number_format($sum, 2), 'icon' => 'bi-fuel-pump'],
                ['label' => 'Average per Dispatch', 'value' => number_format($avg, 2), 'icon' => 'bi-graph-up'],
                ['label' => 'Records', 'value' => $total, 'icon' => 'bi-list-ol'],
            ],
            'totals' => null,
        ];
    }

    // ==============================
    // FUEL COST
    // ==============================
    public function getFuelCostReport($filters)
    {
        $rows = $this->db->query("
            SELECT e.expense_id, e.expense_code, e.trip_id, e.expense_date, e.description,
                   e.amount, e.paid_by,
                   t.trip_code
            FROM tbl_dispatch_expenses e
            LEFT JOIN tbl_trips t ON e.trip_id = t.trip_id
            WHERE e.expense_type = 'FUEL'
              AND e.expense_date BETWEEN ? AND ?
            ORDER BY e.expense_date DESC
        ", [$filters['date_from'], $filters['date_to']])->getResultArray();

        $total = count($rows);
        $sum = 0;
        foreach ($rows as $row) {
            $sum += (float) ($row['amount'] ?? 0);
        }
        $avg = $total > 0 ? round($sum / $total, 2) : 0;

        return [
            'title' => 'Fuel Cost Report',
            'columns' => [
                ['key' => 'expense_code', 'label' => 'Expense Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'trip_code', 'label' => 'Trip Code', 'align' => 'left', 'format' => 'text'],
                ['key' => 'expense_date', 'label' => 'Expense Date', 'align' => 'left', 'format' => 'date'],
                ['key' => 'description', 'label' => 'Description', 'align' => 'left', 'format' => 'text'],
                ['key' => 'paid_by', 'label' => 'Paid By', 'align' => 'left', 'format' => 'text'],
                ['key' => 'amount', 'label' => 'Amount', 'align' => 'right', 'format' => 'currency'],
            ],
            'rows' => $rows,
            'stats' => [
                ['label' => 'Total Fuel Cost', 'value' => '₱' . number_format($sum, 2), 'icon' => 'bi-cash-coin'],
                ['label' => 'Records', 'value' => $total, 'icon' => 'bi-list-ol'],
                ['label' => 'Average per Entry', 'value' => '₱' . number_format($avg, 2), 'icon' => 'bi-graph-up'],
            ],
            'totals' => ['label' => 'TOTAL', 'values' => ['amount' => $sum]],
        ];
    }
}
