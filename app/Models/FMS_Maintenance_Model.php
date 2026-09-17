<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Maintenance_Model extends Model
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
    // GENERATE CODES
    // ==============================
    private function generateScheduleCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_maintenance_schedules WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        return 'MTS-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    private function generateRecordCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_maintenance_records WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        return 'MTR-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // LOOKUPS
    // ==============================
    public function getTrucks()
    {
        return $this->db->query("
            SELECT truck_id, truck_code, plate_number, make, model, current_odometer
            FROM tbl_trucks
            WHERE truck_status NOT IN ('RETIRED','OUT OF SERVICE')
            ORDER BY plate_number
        ")->getResultArray();
    }

    // ==============================
    // SCHEDULES
    // ==============================
    public function getAllSchedules()
    {
        return $this->db->query("
            SELECT s.*,
                   t.plate_number,
                   t.make,
                   t.model,
                   t.current_odometer AS truck_current_odometer
            FROM tbl_maintenance_schedules s
            LEFT JOIN tbl_trucks t ON s.truck_id = t.truck_id
            ORDER BY s.scheduled_date DESC, s.schedule_id DESC
        ")->getResultArray();
    }

    public function getSchedule($schedule_id)
    {
        $query = $this->db->query("
            SELECT s.*,
                   t.plate_number,
                   t.make,
                   t.model
            FROM tbl_maintenance_schedules s
            LEFT JOIN tbl_trucks t ON s.truck_id = t.truck_id
            WHERE s.schedule_id = ?
        ", [$schedule_id]);
        return $query->getRowArray();
    }

    public function saveSchedule()
    {
        $schedule_code = $this->generateScheduleCode();
        $truck_id = $this->request->getPost('truck_id');
        $truck_plate = $this->request->getPost('truck_plate');
        $maintenance_type = $this->request->getPost('maintenance_type') ?: 'PREVENTIVE';
        $service_type = $this->request->getPost('service_type');
        $scheduled_date = $this->request->getPost('scheduled_date') ?: date('Y-m-d');
        $current_odometer = $this->request->getPost('current_odometer') ?: 0;
        $service_interval = $this->request->getPost('service_interval') ?: 0;
        $next_service_odometer = $this->request->getPost('next_service_odometer') ?: 0;
        $technician = $this->request->getPost('technician');
        $priority = $this->request->getPost('priority') ?: 'NORMAL';
        $status = $this->request->getPost('status') ?: 'SCHEDULED';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_maintenance_schedules`(
                `schedule_code`, `truck_id`, `truck_plate`, `maintenance_type`, `service_type`,
                `scheduled_date`, `current_odometer`, `service_interval`, `next_service_odometer`,
                `technician`, `priority`, `status`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $schedule_code, $truck_id, $truck_plate, $maintenance_type, $service_type,
                $scheduled_date, $current_odometer, $service_interval, $next_service_odometer,
                $technician, $priority, $status, $remarks, $this->cuser
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Maintenance Schedule Saved Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while saving schedule.'];
        }
    }

    public function updateSchedule()
    {
        $schedule_id = $this->request->getPost('schedule_id');
        $truck_id = $this->request->getPost('truck_id');
        $truck_plate = $this->request->getPost('truck_plate');
        $maintenance_type = $this->request->getPost('maintenance_type') ?: 'PREVENTIVE';
        $service_type = $this->request->getPost('service_type');
        $scheduled_date = $this->request->getPost('scheduled_date');
        $current_odometer = $this->request->getPost('current_odometer') ?: 0;
        $service_interval = $this->request->getPost('service_interval') ?: 0;
        $next_service_odometer = $this->request->getPost('next_service_odometer') ?: 0;
        $technician = $this->request->getPost('technician');
        $priority = $this->request->getPost('priority') ?: 'NORMAL';
        $status = $this->request->getPost('status') ?: 'SCHEDULED';
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            UPDATE `tbl_maintenance_schedules`
            SET 
                `truck_id` = ?, `truck_plate` = ?, `maintenance_type` = ?, `service_type` = ?,
                `scheduled_date` = ?, `current_odometer` = ?, `service_interval` = ?,
                `next_service_odometer` = ?, `technician` = ?, `priority` = ?,
                `status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `schedule_id` = ?
            ",
            [
                $truck_id, $truck_plate, $maintenance_type, $service_type,
                $scheduled_date, $current_odometer, $service_interval,
                $next_service_odometer, $technician, $priority,
                $status, $remarks, $schedule_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Maintenance Schedule Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating schedule.'];
        }
    }

    public function deleteSchedule()
    {
        $schedule_id = $this->request->getPost('schedule_id');
        $query = $this->db->query("DELETE FROM `tbl_maintenance_schedules` WHERE `schedule_id` = ?", [$schedule_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Maintenance Schedule Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting schedule.'];
        }
    }

    // ==============================
    // RECORDS
    // ==============================
    public function getAllRecords()
    {
        return $this->db->query("
            SELECT r.*,
                   t.plate_number,
                   t.make,
                   t.model
            FROM tbl_maintenance_records r
            LEFT JOIN tbl_trucks t ON r.truck_id = t.truck_id
            ORDER BY r.maintenance_date DESC, r.record_id DESC
        ")->getResultArray();
    }

    public function getRecord($record_id)
    {
        $query = $this->db->query("
            SELECT r.*,
                   t.plate_number,
                   t.make,
                   t.model
            FROM tbl_maintenance_records r
            LEFT JOIN tbl_trucks t ON r.truck_id = t.truck_id
            WHERE r.record_id = ?
        ", [$record_id]);
        return $query->getRowArray();
    }

    public function saveRecord()
    {
        $record_code = $this->generateRecordCode();
        $schedule_id = $this->request->getPost('schedule_id') ?: null;
        $truck_id = $this->request->getPost('truck_id');
        $truck_plate = $this->request->getPost('truck_plate');
        $maintenance_date = $this->request->getPost('maintenance_date') ?: date('Y-m-d');
        $odometer = $this->request->getPost('odometer') ?: 0;
        $maintenance_type = $this->request->getPost('maintenance_type') ?: 'PREVENTIVE';
        $service_category = $this->request->getPost('service_category');
        $problem_reason = $this->request->getPost('problem_reason');
        $work_performed = $this->request->getPost('work_performed');
        $technician = $this->request->getPost('technician');
        $labor_cost = $this->request->getPost('labor_cost') ?: 0;
        $other_cost = $this->request->getPost('other_cost') ?: 0;
        $downtime_hours = $this->request->getPost('downtime_hours') ?: 0;
        $status = $this->request->getPost('status') ?: 'COMPLETED';
        $remarks = $this->request->getPost('remarks');

        $submitted_parts = $this->request->getPost('parts');
        $parts_total = 0;

        if (!empty($submitted_parts) && is_array($submitted_parts)) {
            foreach ($submitted_parts as $p) {
                $qty = floatval($p['quantity'] ?? 0);
                $unit = floatval($p['unit_cost'] ?? 0);
                $parts_total += $qty * $unit;
            }
        }

        $total_cost = floatval($labor_cost) + $parts_total + floatval($other_cost);

        $query = $this->db->query("
            INSERT INTO `tbl_maintenance_records`(
                `record_code`, `schedule_id`, `truck_id`, `truck_plate`, `maintenance_date`,
                `odometer`, `maintenance_type`, `service_category`, `problem_reason`,
                `work_performed`, `technician`, `labor_cost`, `parts_cost`, `other_cost`,
                `total_cost`, `downtime_hours`, `status`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $record_code, $schedule_id, $truck_id, $truck_plate, $maintenance_date,
                $odometer, $maintenance_type, $service_category, $problem_reason,
                $work_performed, $technician, $labor_cost, $parts_total, $other_cost,
                $total_cost, $downtime_hours, $status, $remarks, $this->cuser
            ]
        );

        if ($query) {
            $record_id = $this->db->insertID();

            if (!empty($submitted_parts) && is_array($submitted_parts)) {
                foreach ($submitted_parts as $p) {
                    $qty = floatval($p['quantity'] ?? 0);
                    $unit = floatval($p['unit_cost'] ?? 0);
                    $total = $qty * $unit;

                    $this->db->query("
                        INSERT INTO `tbl_maintenance_parts`(
                            `record_id`, `part_name`, `quantity`, `unit_cost`,
                            `total_cost`, `supplier`, `warranty`, `remarks`, `created_by`
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ", [
                        $record_id,
                        $p['part_name'] ?? '',
                        $qty,
                        $unit,
                        $total,
                        $p['supplier'] ?? '',
                        $p['warranty'] ?? '',
                        $p['remarks'] ?? '',
                        $this->cuser
                    ]);
                }
            }

            if ($schedule_id) {
                $this->db->query("
                    UPDATE tbl_maintenance_schedules 
                    SET status = 'COMPLETED' 
                    WHERE schedule_id = ?
                ", [$schedule_id]);
            }

            return ['status' => 'success', 'message' => 'Maintenance Record Saved Successfully!', 'record_id' => $record_id];
        } else {
            $error = $this->db->error();
            log_message('error', 'Maintenance Record Save Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while saving record.'];
        }
    }

    public function updateRecord()
    {
        $record_id = $this->request->getPost('record_id');
        $truck_id = $this->request->getPost('truck_id');
        $truck_plate = $this->request->getPost('truck_plate');
        $maintenance_date = $this->request->getPost('maintenance_date');
        $odometer = $this->request->getPost('odometer') ?: 0;
        $maintenance_type = $this->request->getPost('maintenance_type') ?: 'PREVENTIVE';
        $service_category = $this->request->getPost('service_category');
        $problem_reason = $this->request->getPost('problem_reason');
        $work_performed = $this->request->getPost('work_performed');
        $technician = $this->request->getPost('technician');
        $labor_cost = $this->request->getPost('labor_cost') ?: 0;
        $other_cost = $this->request->getPost('other_cost') ?: 0;
        $downtime_hours = $this->request->getPost('downtime_hours') ?: 0;
        $status = $this->request->getPost('status') ?: 'COMPLETED';
        $remarks = $this->request->getPost('remarks');

        $row = $this->db->query("
            SELECT COALESCE(SUM(total_cost), 0) AS parts_total
            FROM tbl_maintenance_parts
            WHERE record_id = ?
        ", [$record_id])->getRow();
        $parts_total = $row ? floatval($row->parts_total) : 0;

        $total_cost = floatval($labor_cost) + $parts_total + floatval($other_cost);

        $query = $this->db->query("
            UPDATE `tbl_maintenance_records`
            SET 
                `truck_id` = ?, `truck_plate` = ?, `maintenance_date` = ?,
                `odometer` = ?, `maintenance_type` = ?, `service_category` = ?,
                `problem_reason` = ?, `work_performed` = ?, `technician` = ?,
                `labor_cost` = ?, `parts_cost` = ?, `other_cost` = ?,
                `total_cost` = ?, `downtime_hours` = ?, `status` = ?,
                `remarks` = ?, `updated_at` = NOW()
            WHERE `record_id` = ?
            ",
            [
                $truck_id, $truck_plate, $maintenance_date,
                $odometer, $maintenance_type, $service_category,
                $problem_reason, $work_performed, $technician,
                $labor_cost, $parts_total, $other_cost,
                $total_cost, $downtime_hours, $status,
                $remarks, $record_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Maintenance Record Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating record.'];
        }
    }

    public function deleteRecord()
    {
        $record_id = $this->request->getPost('record_id');

        $this->db->query("DELETE FROM `tbl_maintenance_parts` WHERE `record_id` = ?", [$record_id]);
        $query = $this->db->query("DELETE FROM `tbl_maintenance_records` WHERE `record_id` = ?", [$record_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Maintenance Record Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting record.'];
        }
    }

    // ==============================
    // PARTS
    // ==============================
    public function getParts($record_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_maintenance_parts
            WHERE record_id = ?
            ORDER BY part_id ASC
        ", [$record_id])->getResultArray();
    }

    public function getPart($part_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_maintenance_parts WHERE part_id = ?", [$part_id]);
        return $query->getRowArray();
    }

    public function savePart()
    {
        $record_id = $this->request->getPost('record_id');
        $part_name = $this->request->getPost('part_name');
        $quantity = $this->request->getPost('quantity') ?: 1;
        $unit_cost = $this->request->getPost('unit_cost') ?: 0;
        $supplier = $this->request->getPost('supplier');
        $warranty = $this->request->getPost('warranty');
        $remarks = $this->request->getPost('remarks');

        $total_cost = floatval($quantity) * floatval($unit_cost);

        $query = $this->db->query("
            INSERT INTO `tbl_maintenance_parts`(
                `record_id`, `part_name`, `quantity`, `unit_cost`,
                `total_cost`, `supplier`, `warranty`, `remarks`, `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $record_id, $part_name, $quantity, $unit_cost,
                $total_cost, $supplier, $warranty, $remarks, $this->cuser
            ]
        );

        if ($query) {
            $this->recalcPartsCost($record_id);
            return ['status' => 'success', 'message' => 'Part Added Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding part.'];
        }
    }

    public function updatePart()
    {
        $part_id = $this->request->getPost('part_id');
        $part_name = $this->request->getPost('part_name');
        $quantity = $this->request->getPost('quantity') ?: 1;
        $unit_cost = $this->request->getPost('unit_cost') ?: 0;
        $supplier = $this->request->getPost('supplier');
        $warranty = $this->request->getPost('warranty');
        $remarks = $this->request->getPost('remarks');

        $total_cost = floatval($quantity) * floatval($unit_cost);

        $row = $this->db->query("SELECT record_id FROM tbl_maintenance_parts WHERE part_id = ?", [$part_id])->getRow();
        $record_id = $row ? $row->record_id : null;

        $query = $this->db->query("
            UPDATE `tbl_maintenance_parts`
            SET 
                `part_name` = ?, `quantity` = ?, `unit_cost` = ?,
                `total_cost` = ?, `supplier` = ?, `warranty` = ?, `remarks` = ?,
                `updated_at` = NOW()
            WHERE `part_id` = ?
            ",
            [
                $part_name, $quantity, $unit_cost,
                $total_cost, $supplier, $warranty, $remarks, $part_id
            ]
        );

        if ($query) {
            if ($record_id) $this->recalcPartsCost($record_id);
            return ['status' => 'success', 'message' => 'Part Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating part.'];
        }
    }

    public function deletePart()
    {
        $part_id = $this->request->getPost('part_id');

        $row = $this->db->query("SELECT record_id FROM tbl_maintenance_parts WHERE part_id = ?", [$part_id])->getRow();
        $record_id = $row ? $row->record_id : null;

        $query = $this->db->query("DELETE FROM `tbl_maintenance_parts` WHERE `part_id` = ?", [$part_id]);

        if ($query) {
            if ($record_id) $this->recalcPartsCost($record_id);
            return ['status' => 'success', 'message' => 'Part Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting part.'];
        }
    }

    private function recalcPartsCost($record_id)
    {
        $row = $this->db->query("
            SELECT COALESCE(SUM(total_cost), 0) AS parts_total
            FROM tbl_maintenance_parts
            WHERE record_id = ?
        ", [$record_id])->getRow();

        $parts_total = $row ? floatval($row->parts_total) : 0;

        $rec = $this->db->query("
            SELECT labor_cost, other_cost FROM tbl_maintenance_records WHERE record_id = ?
        ", [$record_id])->getRow();

        if ($rec) {
            $labor = floatval($rec->labor_cost);
            $other = floatval($rec->other_cost);
            $total = $labor + $parts_total + $other;

            $this->db->query("
                UPDATE tbl_maintenance_records
                SET parts_cost = ?, total_cost = ?, updated_at = NOW()
                WHERE record_id = ?
            ", [$parts_total, $total, $record_id]);
        }
    }
}