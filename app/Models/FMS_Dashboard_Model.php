<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Dashboard_Model extends Model
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // ==============================
    // PERIOD FILTER — every metric in this model is scoped by year, or by
    // year+month when a specific month is picked ($month = 'all' means year only).
    // ==============================

    // ==============================
    // FLEET STATS — trucks added within the period (created_at)
    // ==============================
    public function getFleetStats($year, $month = 'all')
    {
        $row = $this->db->query("
            SELECT
                COUNT(*) AS total_trucks,
                SUM(truck_status = 'AVAILABLE') AS available,
                SUM(truck_status IN ('DISPATCHED','IN_TRANSIT')) AS in_transit,
                SUM(truck_status = 'UNDER_MAINTENANCE') AS maintenance
            FROM tbl_trucks
            WHERE truck_status != 'RETIRED'
              AND YEAR(created_at) = ? AND (? = 'all' OR MONTH(created_at) = ?)
        ", [$year, $month, $month])->getRowArray();

        return [
            'total_trucks' => (int) $row['total_trucks'],
            'available'    => (int) $row['available'],
            'in_transit'   => (int) $row['in_transit'],
            'maintenance'  => (int) $row['maintenance'],
        ];
    }

    // ==============================
    // PERSONNEL STATS — drivers & helpers hired within the period (created_at)
    // ==============================
    public function getPersonnelStats($year, $month = 'all')
    {
        $drivers = $this->db->query("
            SELECT COUNT(*) AS total, SUM(driver_status = 'AVAILABLE') AS available
            FROM tbl_drivers
            WHERE driver_status != 'INACTIVE'
              AND YEAR(created_at) = ? AND (? = 'all' OR MONTH(created_at) = ?)
        ", [$year, $month, $month])->getRowArray();

        $helpers = $this->db->query("
            SELECT COUNT(*) AS total, SUM(helper_status = 'AVAILABLE') AS available
            FROM tbl_helpers
            WHERE helper_status != 'INACTIVE'
              AND YEAR(created_at) = ? AND (? = 'all' OR MONTH(created_at) = ?)
        ", [$year, $month, $month])->getRowArray();

        return [
            'total_drivers'     => (int) $drivers['total'],
            'available_drivers' => (int) $drivers['available'],
            'total_helpers'     => (int) $helpers['total'],
            'available_helpers' => (int) $helpers['available'],
        ];
    }

    // ==============================
    // TRIP STATS — scoped by trip.scheduled_date / dispatch.dispatch_date
    // ==============================
    public function getTripStats($year, $month = 'all')
    {
        $total = $this->db->query("
            SELECT COUNT(*) AS total FROM tbl_trips
            WHERE YEAR(scheduled_date) = ? AND (? = 'all' OR MONTH(scheduled_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        // Assigned but not yet dispatched
        $pending_dispatch = $this->db->query("
            SELECT COUNT(*) AS total FROM tbl_trips
            WHERE trip_status = 'ASSIGNED'
              AND YEAR(scheduled_date) = ? AND (? = 'all' OR MONTH(scheduled_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        $active = $this->db->query("
            SELECT COUNT(*) AS total FROM tbl_trips
            WHERE trip_status IN ('DISPATCHED','IN_TRANSIT')
              AND YEAR(scheduled_date) = ? AND (? = 'all' OR MONTH(scheduled_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        $completed = $this->db->query("
            SELECT COUNT(*) AS total FROM tbl_trips
            WHERE trip_status = 'COMPLETED'
              AND YEAR(scheduled_date) = ? AND (? = 'all' OR MONTH(scheduled_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        return [
            'total_trips'      => (int) $total,
            'pending_dispatch' => (int) $pending_dispatch,
            'active_trips'     => (int) $active,
            'completed_trips'  => (int) $completed,
        ];
    }

    // ==============================
    // BILLING & DELIVERY STATS — dr_date / billing_date / invoice_date
    // ==============================
    public function getBillingStats($year, $month = 'all')
    {
        $ongoing_trips = $this->db->query("
            SELECT COUNT(*) AS total FROM tbl_trips
            WHERE trip_status NOT IN ('COMPLETED','CANCELLED')
              AND YEAR(scheduled_date) = ? AND (? = 'all' OR MONTH(scheduled_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        $pending_drs = $this->db->query("
            SELECT COUNT(*) AS total FROM tbl_delivery_receipts
            WHERE dr_status NOT IN ('DELIVERED','CONTAINER_RETURNED','CANCELLED','FAILED_DELIVERY')
              AND YEAR(dr_date) = ? AND (? = 'all' OR MONTH(dr_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        $pending_billing = $this->db->query("
            SELECT COUNT(*) AS total FROM tbl_billing
            WHERE billing_status IN ('DRAFT','FOR_INVOICE')
              AND YEAR(billing_date) = ? AND (? = 'all' OR MONTH(billing_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        $outstanding = $this->db->query("
            SELECT COALESCE(SUM(outstanding_balance),0) AS total FROM tbl_invoices
            WHERE invoice_status NOT IN ('CANCELLED','VOID')
              AND YEAR(invoice_date) = ? AND (? = 'all' OR MONTH(invoice_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        return [
            'ongoing_trips'   => (int) $ongoing_trips,
            'pending_drs'     => (int) $pending_drs,
            'pending_billing' => (int) $pending_billing,
            'outstanding_ar'  => (float) $outstanding,
        ];
    }

    // ==============================
    // MONTHLY FUEL EXPENSE (₱) FOR THE GIVEN YEAR — always broken down by all
    // 12 months of the year regardless of the month filter (it's the chart's job
    // to show the year's shape); sourced from dispatch expenses tagged FUEL since
    // dispatch.fuel_consumed (L) is not populated in practice.
    // ==============================
    public function getMonthlyFuelExpense($year)
    {
        $rows = $this->db->query("
            SELECT MONTH(expense_date) AS m, SUM(amount) AS total
            FROM tbl_dispatch_expenses
            WHERE expense_type = 'FUEL' AND YEAR(expense_date) = ?
            GROUP BY MONTH(expense_date)
        ", [$year])->getResultArray();

        $byMonth = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $byMonth[(int) $row['m']] = (float) $row['total'];
        }

        return $byMonth;
    }

    // ==============================
    // FUEL EFFICIENCY — cost/km, consumption rate, top trucks by fuel cost,
    // scoped to the selected period via dispatch_date / expense_date
    // ==============================
    public function getFuelEfficiency($year, $month = 'all')
    {
        $totals = $this->db->query("
            SELECT
                COALESCE(SUM(fuel_consumed), 0) AS total_fuel,
                COALESCE(SUM(total_distance), 0) AS total_distance
            FROM tbl_dispatch
            WHERE fuel_consumed > 0 AND total_distance > 0
              AND YEAR(dispatch_date) = ? AND (? = 'all' OR MONTH(dispatch_date) = ?)
        ", [$year, $month, $month])->getRowArray();

        $totalFuelCost = $this->db->query("
            SELECT COALESCE(SUM(amount),0) AS total FROM tbl_dispatch_expenses
            WHERE expense_type = 'FUEL'
              AND YEAR(expense_date) = ? AND (? = 'all' OR MONTH(expense_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        $totalDistance = (float) $totals['total_distance'];
        $costPerKm = $totalDistance > 0 ? $totalFuelCost / $totalDistance : null;
        $consumptionRate = $totalDistance > 0 ? (float) $totals['total_fuel'] / $totalDistance : null;

        // tbl_dispatch_expenses has no truck column directly — pull truck plate via dispatch
        $byTruck = $this->db->query("
            SELECT d.truck, SUM(e.amount) AS total_cost
            FROM tbl_dispatch_expenses e
            INNER JOIN tbl_dispatch d ON d.dispatch_id = e.dispatch_id
            WHERE e.expense_type = 'FUEL' AND d.truck IS NOT NULL AND d.truck != ''
              AND YEAR(e.expense_date) = ? AND (? = 'all' OR MONTH(e.expense_date) = ?)
            GROUP BY d.truck
            ORDER BY total_cost DESC
            LIMIT 5
        ", [$year, $month, $month])->getResultArray();

        return [
            'cost_per_km'      => $costPerKm,
            'consumption_rate' => $consumptionRate,
            'by_truck'         => $byTruck,
        ];
    }

    // ==============================
    // MOST FREQUENT ROUTES (top 5, scoped to the selected period)
    // ==============================
    public function getFrequentRoutes($year, $month = 'all')
    {
        return $this->db->query("
            SELECT origin, destination, COUNT(*) AS total
            FROM tbl_trips
            WHERE origin IS NOT NULL AND destination IS NOT NULL AND origin != '' AND destination != ''
              AND YEAR(scheduled_date) = ? AND (? = 'all' OR MONTH(scheduled_date) = ?)
            GROUP BY origin, destination
            ORDER BY total DESC
            LIMIT 5
        ", [$year, $month, $month])->getResultArray();
    }

    // ==============================
    // SERVICE TYPE ANALYSIS — trips + revenue grouped by trip.service_type
    // ==============================
    public function getServiceTypeAnalysis($year, $month = 'all')
    {
        $rows = $this->db->query("
            SELECT
                CASE WHEN t.service_type IS NULL OR t.service_type = '' THEN 'Unspecified' ELSE t.service_type END AS service_type,
                COUNT(DISTINCT t.trip_id) AS trips,
                SUM(COALESCE((SELECT SUM(b.total) FROM tbl_billing b WHERE b.trip_id = t.trip_id), 0)) AS revenue
            FROM tbl_trips t
            WHERE YEAR(t.scheduled_date) = ? AND (? = 'all' OR MONTH(t.scheduled_date) = ?)
            GROUP BY service_type
            ORDER BY trips DESC
        ", [$year, $month, $month])->getResultArray();

        $totalTrips = array_sum(array_column($rows, 'trips'));
        foreach ($rows as &$row) {
            $row['trips'] = (int) $row['trips'];
            $row['revenue'] = (float) $row['revenue'];
            $row['percent'] = $totalTrips > 0 ? round(($row['trips'] / $totalTrips) * 100) : 0;
        }

        return $rows;
    }

    // ==============================
    // CUSTOMER ACTIVITY & PERFORMANCE (top 5 by billed amount)
    // Correlated subqueries (not joins) so multiple trips/billings per customer
    // don't fan out and inflate the sums.
    // ==============================
    public function getCustomerActivity($year, $month = 'all')
    {
        $sql = "
            SELECT
                c.customer_id, c.customer_name,
                (SELECT COUNT(*) FROM tbl_trips t WHERE t.customer_id = c.customer_id AND YEAR(t.scheduled_date) = ? AND (? = 'all' OR MONTH(t.scheduled_date) = ?)) AS trips,
                (SELECT COALESCE(SUM(b.total),0) FROM tbl_billing b WHERE b.customer_id = c.customer_id AND YEAR(b.billing_date) = ? AND (? = 'all' OR MONTH(b.billing_date) = ?)) AS billed,
                (SELECT COALESCE(SUM(i.amount_paid),0) FROM tbl_invoices i WHERE i.customer_id = c.customer_id AND YEAR(i.invoice_date) = ? AND (? = 'all' OR MONTH(i.invoice_date) = ?)) AS collected,
                (SELECT COALESCE(SUM(i.outstanding_balance),0) FROM tbl_invoices i WHERE i.customer_id = c.customer_id AND i.invoice_status NOT IN ('CANCELLED','VOID') AND YEAR(i.invoice_date) = ? AND (? = 'all' OR MONTH(i.invoice_date) = ?)) AS outstanding
            FROM tbl_customers c
            HAVING trips > 0 OR billed > 0
            ORDER BY billed DESC
            LIMIT 5
        ";

        $rows = $this->db->query($sql, [
            $year, $month, $month,
            $year, $month, $month,
            $year, $month, $month,
            $year, $month, $month,
        ])->getResultArray();

        foreach ($rows as &$row) {
            $row['trips'] = (int) $row['trips'];
            $row['billed'] = (float) $row['billed'];
            $row['collected'] = (float) $row['collected'];
            $row['outstanding'] = (float) $row['outstanding'];
        }

        return $rows;
    }

    // ==============================
    // DASHBOARD INSIGHTS — highlight callouts, all scoped to the same period
    // ==============================
    public function getInsights($year, $month = 'all')
    {
        $topCustomer = $this->db->query("
            SELECT c.customer_name, SUM(b.total) AS billed
            FROM tbl_customers c
            INNER JOIN tbl_billing b ON b.customer_id = c.customer_id
            WHERE YEAR(b.billing_date) = ? AND (? = 'all' OR MONTH(b.billing_date) = ?)
            GROUP BY c.customer_id, c.customer_name
            ORDER BY billed DESC LIMIT 1
        ", [$year, $month, $month])->getRowArray();

        $upcomingMaintenance = $this->db->query("
            SELECT COUNT(*) AS total FROM tbl_maintenance_schedules
            WHERE schedule_status IN ('SCHEDULED','OVERDUE')
              AND YEAR(scheduled_date) = ? AND (? = 'all' OR MONTH(scheduled_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        $expiringDocs = $this->db->query("
            SELECT
                (SELECT COUNT(*) FROM tbl_truck_documents WHERE expiration_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 30 DAY AND YEAR(expiration_date) = ? AND (? = 'all' OR MONTH(expiration_date) = ?)) +
                (SELECT COUNT(*) FROM tbl_drivers WHERE license_number <> '' AND expiration_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 30 DAY AND YEAR(expiration_date) = ? AND (? = 'all' OR MONTH(expiration_date) = ?)) +
                (SELECT COUNT(*) FROM tbl_helpers WHERE license_number <> '' AND expiration_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 30 DAY AND YEAR(expiration_date) = ? AND (? = 'all' OR MONTH(expiration_date) = ?)) AS total
        ", [$year, $month, $month, $year, $month, $month, $year, $month, $month])->getRow()->total;

        $outstanding = $this->db->query("
            SELECT COALESCE(SUM(outstanding_balance),0) AS total FROM tbl_invoices
            WHERE invoice_status NOT IN ('CANCELLED','VOID')
              AND YEAR(invoice_date) = ? AND (? = 'all' OR MONTH(invoice_date) = ?)
        ", [$year, $month, $month])->getRow()->total;

        $routes = $this->getFrequentRoutes($year, $month);
        $services = $this->getServiceTypeAnalysis($year, $month);
        $monthlyFuel = $this->getMonthlyFuelExpense($year);
        $periodFuelCost = $month !== 'all' ? ($monthlyFuel[(int) $month] ?? 0) : array_sum($monthlyFuel);

        return [
            'top_customer'         => $topCustomer['customer_name'] ?? null,
            'most_frequent_route'  => $routes[0] ?? null,
            'most_common_service'  => $services[0]['service_type'] ?? null,
            'period_fuel_cost'     => $periodFuelCost,
            'outstanding_ar'       => (float) $outstanding,
            'upcoming_maintenance' => (int) $upcomingMaintenance,
            'expiring_documents'   => (int) $expiringDocs,
        ];
    }
}
