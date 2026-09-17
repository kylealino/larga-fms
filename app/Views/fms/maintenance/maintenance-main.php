<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

$trucks = $this->db->query("
    SELECT truck_id, truck_code, plate_number, make, model, current_odometer
    FROM tbl_trucks
    WHERE truck_status NOT IN ('RETIRED','OUT OF SERVICE')
    ORDER BY plate_number
")->getResultArray();

$schedules = $this->db->query("
    SELECT s.*,
           t.plate_number,
           t.make,
           t.model
    FROM tbl_maintenance_schedules s
    LEFT JOIN tbl_trucks t ON s.truck_id = t.truck_id
    ORDER BY s.scheduled_date DESC, s.schedule_id DESC
")->getResultArray();

$records = $this->db->query("
    SELECT r.*,
           t.plate_number,
           t.make,
           t.model
    FROM tbl_maintenance_records r
    LEFT JOIN tbl_trucks t ON r.truck_id = t.truck_id
    ORDER BY r.maintenance_date DESC, r.record_id DESC
")->getResultArray();

$total_scheduled = 0;
$total_in_progress = 0;
$total_overdue = 0;
$total_records = count($records);
$total_cost = 0;

foreach ($schedules as $s) {
    if ($s['status'] == 'SCHEDULED') $total_scheduled++;
    elseif ($s['status'] == 'IN_PROGRESS') $total_in_progress++;
    elseif ($s['status'] == 'OVERDUE') $total_overdue++;
}

foreach ($records as $r) {
    $total_cost += floatval($r['total_cost']);
}

echo view('templates/myheader.php');
?>

<style>
    :root {
        --primary: #1a6bb0;
        --primary-dark: #0f5a99;
        --primary-light: #e8f2fa;
        --danger: #dc2626;
        --danger-dark: #b91c1c;
        --success: #10b981;
        --warning: #f59e0b;
        --info: #3b82f6;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --shadow: 0 1px 3px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.08);
    }

    body { background: var(--gray-50); }

    .lrg-module-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 14px 24px;
        margin: -24px -24px 24px -24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .lrg-module-header .header-left { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 200px; }
    .lrg-module-header .header-left .module-icon {
        width: 40px; height: 40px;
        background: rgba(255,255,255,0.12); border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: #90cdf4;
        border: 1px solid rgba(255,255,255,0.08);
    }
    .lrg-module-header .header-left .module-info h4 {
        font-size: 17px; font-weight: 600; color: #ffffff; margin: 0;
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .lrg-module-header .header-left .module-info h4 .module-badge {
        font-size: 9px; font-weight: 600; padding: 2px 12px; border-radius: 4px;
        background: rgba(255,255,255,0.12); color: #bee3f8;
        border: 1px solid rgba(255,255,255,0.08);
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .header-actions { display: flex; align-items: center; gap: 10px; }
    .btn-header {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        color: #ffffff; padding: 6px 16px; border-radius: 6px;
        font-size: 12px; font-weight: 500; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px; cursor: pointer;
    }
    .btn-header:hover {
        background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.25);
        color: #ffffff; transform: translateY(-1px);
    }
    .btn-header-primary { background: #ffffff; border-color: #ffffff; color: var(--primary); }
    .btn-header-primary:hover { background: rgba(255,255,255,0.9); color: var(--primary-dark); }

    .stat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: #ffffff; border-radius: 12px;
        border: 1px solid var(--gray-200); padding: 16px 20px;
        transition: all 0.3s ease;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: var(--shadow); position: relative; overflow: hidden; cursor: pointer;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        opacity: 0; transition: opacity 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--gray-300); }
    .stat-card:hover::before { opacity: 1; }
    .stat-card.active { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(26,107,176,0.15), var(--shadow-md); }
    .stat-card.active::before { opacity: 1; }
    .stat-left .stat-label {
        font-size: 10px; font-weight: 600; color: var(--gray-500);
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;
    }
    .stat-left .stat-value { font-size: 24px; font-weight: 700; color: var(--gray-800); line-height: 1.2; }
    .stat-left .stat-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
    .stat-right { font-size: 32px; color: var(--primary); opacity: 0.08; line-height: 1; }

    .card { border-radius: 12px; border: 1px solid var(--gray-200); background: #ffffff; box-shadow: var(--shadow); margin-bottom: 20px; }
    .card-header {
        background: #ffffff; border-bottom: 1px solid var(--gray-200);
        padding: 14px 20px; border-radius: 12px 12px 0 0;
        display: flex; align-items: center; justify-content: space-between;
    }
    .card-header h6 { font-size: 13px; font-weight: 600; margin: 0; color: var(--gray-700); }
    .card-body { padding: 20px; }

    .form-label {
        font-size: 11px; font-weight: 600; color: var(--gray-600);
        margin-bottom: 4px; display: block;
        text-transform: uppercase; letter-spacing: 0.3px;
    }
    .form-label .required { color: var(--danger); margin-left: 2px; }
    .form-control, select.form-control {
        border: 1.5px solid var(--gray-200); border-radius: 8px;
        padding: 9px 12px; font-size: 13px; color: var(--gray-700);
        background: #ffffff; transition: all 0.2s; width: 100%; height: 40px;
    }
    .form-control:focus, select.form-control:focus {
        outline: none; border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(26,107,176,0.08);
    }
    textarea.form-control { height: auto; min-height: 60px; resize: vertical; }
    .form-control[readonly] { background: var(--gray-50); cursor: not-allowed; }

    .btn-primary {
        background: var(--primary); border: none; border-radius: 8px;
        padding: 9px 20px; font-size: 13px; font-weight: 600;
        transition: all 0.2s; color: #ffffff;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-primary:hover {
        background: var(--primary-dark); transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(26,107,176,0.3); color: #ffffff;
    }
    .btn-secondary {
        background: #ffffff; border: 1.5px solid var(--gray-200);
        border-radius: 8px; padding: 9px 20px; font-size: 13px;
        font-weight: 600; color: var(--gray-600);
        transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-secondary:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-warning {
        background: var(--warning); border: none; border-radius: 8px;
        padding: 9px 20px; font-size: 13px; font-weight: 600;
        transition: all 0.2s; color: #ffffff;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-warning:hover { background: #d97706; transform: translateY(-1px); color: #ffffff; }
    .btn-sm { padding: 5px 14px; font-size: 11px; }

    .table-wrap { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .table thead th {
        font-size: 10px; font-weight: 600; color: var(--gray-500);
        background: var(--gray-50); border-bottom: 1.5px solid var(--gray-200);
        padding: 10px 12px; text-transform: uppercase; letter-spacing: 0.5px;
        text-align: left; white-space: nowrap;
    }
    .table tbody td {
        font-size: 12px; color: var(--gray-700); padding: 10px 12px;
        border-bottom: 1px solid var(--gray-100); vertical-align: middle;
    }
    .table-hover tbody tr:hover { background: var(--gray-50); }

    .badge {
        font-size: 10px; font-weight: 600; padding: 3px 12px;
        border-radius: 20px; letter-spacing: 0.3px; display: inline-block;
    }
    .badge-success { background: var(--success); color: #ffffff; }
    .badge-danger { background: var(--danger); color: #ffffff; }
    .badge-warning { background: var(--warning); color: #ffffff; }
    .badge-primary { background: var(--primary); color: #ffffff; }
    .badge-secondary { background: var(--gray-500); color: #ffffff; }
    .badge-info { background: var(--info); color: #ffffff; }

    .action-group { display: flex; align-items: center; gap: 4px; justify-content: center; }
    .btn-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 6px; border: 1px solid transparent;
        background: transparent; cursor: pointer; transition: all 0.2s;
        color: var(--gray-500); font-size: 14px;
    }
    .btn-icon:hover { transform: scale(1.05); }
    .btn-icon-view { color: var(--info); } .btn-icon-view:hover { background: #dbeafe; border-color: #93c5fd; }
    .btn-icon-edit { color: var(--warning); } .btn-icon-edit:hover { background: #fef3c7; border-color: #fcd34d; }
    .btn-icon-delete { color: var(--danger); } .btn-icon-delete:hover { background: #fee2e2; border-color: #fca5a5; }
    .btn-icon-print { color: var(--primary); } .btn-icon-print:hover { background: var(--primary-light); border-color: var(--primary); }
    .btn-icon-gear { color: var(--success); } .btn-icon-gear:hover { background: #d1fae5; border-color: #6ee7b7; }

    .btn-toolbar {
        padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;
        border: 1px solid var(--gray-200); background: #ffffff; color: var(--gray-600);
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
    }
    .btn-toolbar:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-toolbar-primary { background: var(--primary); border-color: var(--primary); color: #ffffff; }
    .btn-toolbar-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); color: #ffffff; }

    .toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .toolbar-left { display: flex; align-items: center; gap: 8px; }
    .toolbar-right { display: flex; align-items: center; gap: 10px; }

    .nav-tabs-modern {
        display: flex; gap: 8px; margin-bottom: 16px;
        border-bottom: 1px solid var(--gray-200); padding-bottom: 2px;
    }
    .nav-tab-modern {
        padding: 8px 20px; font-size: 13px; font-weight: 600;
        color: var(--gray-500); cursor: pointer;
        border-bottom: 3px solid transparent; transition: all 0.2s;
        background: transparent; border: none; margin-bottom: -2px;
    }
    .nav-tab-modern:hover { color: var(--primary); }
    .nav-tab-modern.active { color: var(--primary); border-bottom-color: var(--primary); }

    .dataTables_wrapper { font-family: 'Inter', sans-serif; }
    .dataTables_filter { float: right; margin-bottom: 16px; }
    .dataTables_filter label { font-size: 12px; font-weight: 500; color: var(--gray-500); display: flex; align-items: center; gap: 8px; }
    .dataTables_filter input {
        width: 200px; padding: 6px 12px; border: 1.5px solid var(--gray-200);
        border-radius: 8px; font-size: 12px; transition: all 0.2s; outline: none;
    }
    .dataTables_filter input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,107,176,0.08); }
    .dataTables_paginate { float: right; margin-top: 16px; }
    .dataTables_paginate .paginate_button {
        padding: 4px 10px !important; margin: 0 2px !important; border-radius: 6px !important;
        border: 1px solid var(--gray-200) !important; background: #ffffff !important;
        color: var(--gray-600) !important; font-size: 11px !important; font-weight: 600 !important;
    }
    .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important; border-color: var(--primary) !important; color: #ffffff !important;
    }
    .dataTables_paginate .paginate_button:hover {
        background: var(--gray-50) !important; border-color: var(--gray-300) !important; color: var(--primary) !important;
    }
    .dataTables_info { float: left; font-size: 12px; color: var(--gray-500); margin-top: 16px; }

    .modal-content { border-radius: 16px; border: none; box-shadow: var(--shadow-lg); }
    .modal-header {
        border-bottom: 1px solid var(--gray-200); padding: 18px 24px;
        background: var(--gray-50); border-radius: 16px 16px 0 0;
    }
    .modal-header .modal-title { font-size: 16px; font-weight: 600; color: var(--gray-800); }
    .modal-header .modal-title i { color: var(--primary); }
    .modal-body { padding: 24px; }
    .modal-footer {
        border-top: 1px solid var(--gray-200); padding: 16px 24px; gap: 10px;
        background: var(--gray-50); border-radius: 0 0 16px 16px;
    }

    .empty-state { text-align: center; padding: 40px 20px; }
    .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state h5 { font-size: 16px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
    .empty-state p { font-size: 13px; color: var(--gray-400); }

    .item-form { background: var(--gray-50); border-radius: 8px; padding: 12px; margin-bottom: 12px; border: 1px solid var(--gray-200); }

    @media (max-width: 992px) {
        .lrg-module-header { flex-direction: column; align-items: stretch; padding: 16px 20px; }
        .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    }
    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .toolbar { flex-direction: column; align-items: stretch; }
    }
</style>

<div class="me-mt-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- HEADER -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon"><i class="bi bi-tools"></i></div>
        <div class="module-info">
            <h4>
                Preventive Maintenance
                <span class="module-badge">Operations</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

<!-- STATS -->
<div class="stat-grid">
    <div class="stat-card active" data-filter="all" onclick="filterMTTable('all')">
        <div class="stat-left">
            <div class="stat-label">Scheduled</div>
            <div class="stat-value"><?=$total_scheduled;?></div>
            <div class="stat-sub">Upcoming maintenance</div>
        </div>
        <div class="stat-right"><i class="bi bi-calendar-check"></i></div>
    </div>
    <div class="stat-card" data-filter="IN_PROGRESS" onclick="filterMTTable('IN_PROGRESS')">
        <div class="stat-left">
            <div class="stat-label">In Progress</div>
            <div class="stat-value"><?=$total_in_progress;?></div>
            <div class="stat-sub">Currently servicing</div>
        </div>
        <div class="stat-right"><i class="bi bi-wrench-adjustable"></i></div>
    </div>
    <div class="stat-card" data-filter="OVERDUE" onclick="filterMTTable('OVERDUE')">
        <div class="stat-left">
            <div class="stat-label">Overdue</div>
            <div class="stat-value"><?=$total_overdue;?></div>
            <div class="stat-sub">Past schedule</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-triangle"></i></div>
    </div>
    <div class="stat-card" data-filter="RECORDS" onclick="filterMTTable('RECORDS')">
        <div class="stat-left">
            <div class="stat-label">Records</div>
            <div class="stat-value"><?=$total_records;?></div>
            <div class="stat-sub">Completed work</div>
        </div>
        <div class="stat-right"><i class="bi bi-clipboard-check"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-left">
            <div class="stat-label">Total Cost</div>
            <div class="stat-value">₱<?=number_format($total_cost, 0);?></div>
            <div class="stat-sub">All records</div>
        </div>
        <div class="stat-right"><i class="bi bi-cash-coin"></i></div>
    </div>
</div>

<!-- TABS -->
<div class="nav-tabs-modern">
    <button class="nav-tab-modern active" id="tabSchedules" onclick="switchTab('schedules')">
        <i class="bi bi-calendar-check"></i> Maintenance Schedules
    </button>
    <button class="nav-tab-modern" id="tabRecords" onclick="switchTab('records')">
        <i class="bi bi-clipboard-check"></i> Maintenance Records
    </button>
</div>

<!-- SCHEDULES TAB -->
<div id="paneSchedules">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-calendar-check me-2"></i>Maintenance Schedules</h6>
            <div>
                <span class="badge badge-secondary" id="schedCount"><?=count($schedules);?> records</span>
                <button class="btn-toolbar btn-toolbar-primary ms-2" onclick="__MT.__openScheduleModal()">
                    <i class="bi bi-plus-circle"></i> New Schedule
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="scheduleTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="120">Code</th>
                            <th>Truck</th>
                            <th width="100">Type</th>
                            <th width="100">Service</th>
                            <th width="100">Scheduled</th>
                            <th width="100">Odometer</th>
                            <th width="100">Next Service</th>
                            <th width="120">Technician</th>
                            <th width="90">Priority</th>
                            <th width="100">Status</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($schedules) > 0): ?>
                            <?php foreach($schedules as $row): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['schedule_code'];?></span></td>
                                <td>
                                    <strong><?=$row['plate_number'] ?? $row['truck_plate'];?></strong><br>
                                    <small class="text-muted"><?=($row['make'] ?? '') . ' ' . ($row['model'] ?? '');?></small>
                                </td>
                                <td>
                                    <?php
                                    $mtClass = 'badge-info';
                                    if($row['maintenance_type'] == 'PREVENTIVE') $mtClass = 'badge-info';
                                    elseif($row['maintenance_type'] == 'CORRECTIVE') $mtClass = 'badge-warning';
                                    elseif($row['maintenance_type'] == 'EMERGENCY') $mtClass = 'badge-danger';
                                    ?>
                                    <span class="badge <?=$mtClass;?>"><?=$row['maintenance_type'];?></span>
                                </td>
                                <td><?=$row['service_type'] ?: '—';?></td>
                                <td><?=$row['scheduled_date'] ? date('M d, Y', strtotime($row['scheduled_date'])) : '—';?></td>
                                <td><?=number_format($row['current_odometer'], 0);?> km</td>
                                <td><?=number_format($row['next_service_odometer'], 0);?> km</td>
                                <td><?=$row['technician'] ?: '—';?></td>
                                <td>
                                    <?php
                                    $prClass = 'badge-info';
                                    if($row['priority'] == 'LOW') $prClass = 'badge-secondary';
                                    elseif($row['priority'] == 'NORMAL') $prClass = 'badge-info';
                                    elseif($row['priority'] == 'HIGH') $prClass = 'badge-warning';
                                    elseif($row['priority'] == 'URGENT') $prClass = 'badge-danger';
                                    ?>
                                    <span class="badge <?=$prClass;?>"><?=$row['priority'];?></span>
                                </td>
                                <td>
                                    <?php
                                    $stClass = 'badge-secondary';
                                    if($row['status'] == 'SCHEDULED') $stClass = 'badge-info';
                                    elseif($row['status'] == 'IN_PROGRESS') $stClass = 'badge-warning';
                                    elseif($row['status'] == 'COMPLETED') $stClass = 'badge-success';
                                    elseif($row['status'] == 'CANCELLED') $stClass = 'badge-secondary';
                                    elseif($row['status'] == 'OVERDUE') $stClass = 'badge-danger';
                                    ?>
                                    <span class="badge <?=$stClass;?>"><?=str_replace('_', ' ', $row['status']);?></span>
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button class="btn-icon btn-icon-gear" onclick="__MT.__createRecordFromSchedule(<?=$row['schedule_id'];?>)" title="Record Maintenance">
                                            <i class="bi bi-clipboard-plus"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-edit" onclick="__MT.__openScheduleModal(<?=$row['schedule_id'];?>)" title="Edit Schedule">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-delete" onclick="__MT.__deleteSchedule(<?=$row['schedule_id'];?>)" title="Delete Schedule">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(count($schedules) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-calendar-check"></i>
                <h5>No maintenance schedules</h5>
                <p>Click <strong>New Schedule</strong> to plan preventive maintenance.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- RECORDS TAB -->
<div id="paneRecords" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-clipboard-check me-2"></i>Maintenance Records</h6>
            <div>
                <span class="badge badge-secondary" id="recCount"><?=count($records);?> records</span>
                <button class="btn-toolbar btn-toolbar-primary ms-2" onclick="__MT.__openRecordModal()">
                    <i class="bi bi-plus-circle"></i> New Record
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="recordTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="120">Code</th>
                            <th width="100">Date</th>
                            <th>Truck</th>
                            <th width="90">Type</th>
                            <th width="100">Odometer</th>
                            <th>Work Performed</th>
                            <th width="120">Technician</th>
                            <th width="100">Total Cost</th>
                            <th width="90">Downtime</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($records) > 0): ?>
                            <?php foreach($records as $row): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['record_code'];?></span></td>
                                <td><?=$row['maintenance_date'] ? date('M d, Y', strtotime($row['maintenance_date'])) : '—';?></td>
                                <td>
                                    <strong><?=$row['plate_number'] ?? $row['truck_plate'];?></strong><br>
                                    <small class="text-muted"><?=($row['make'] ?? '') . ' ' . ($row['model'] ?? '');?></small>
                                </td>
                                <td>
                                    <?php
                                    $mtClass = 'badge-info';
                                    if($row['maintenance_type'] == 'PREVENTIVE') $mtClass = 'badge-info';
                                    elseif($row['maintenance_type'] == 'CORRECTIVE') $mtClass = 'badge-warning';
                                    elseif($row['maintenance_type'] == 'EMERGENCY') $mtClass = 'badge-danger';
                                    ?>
                                    <span class="badge <?=$mtClass;?>"><?=$row['maintenance_type'];?></span>
                                </td>
                                <td><?=number_format($row['odometer'], 0);?> km</td>
                                <td><?=substr($row['work_performed'] ?? '—', 0, 50);?></td>
                                <td><?=$row['technician'] ?: '—';?></td>
                                <td><strong>₱<?=number_format($row['total_cost'], 2);?></strong></td>
                                <td><?=$row['downtime_hours'] ? number_format($row['downtime_hours'], 1) . ' hrs' : '—';?></td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button class="btn-icon btn-icon-view" onclick="__MT.__openRecordModal(<?=$row['record_id'];?>)" title="View/Edit Record">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-delete" onclick="__MT.__deleteRecord(<?=$row['record_id'];?>)" title="Delete Record">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(count($records) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-clipboard-check"></i>
                <h5>No maintenance records</h5>
                <p>Click <strong>New Record</strong> to log completed work.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- SCHEDULE MODAL -->
<div class="modal fade" id="scheduleModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalTitle">
                    <i class="bi bi-calendar-check me-2"></i>New Maintenance Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sched_id">

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Truck <span class="required">*</span></label>
                        <select class="form-control" id="sched_truck_id" onchange="__MT.__onTruckChange(this)">
                            <option value="">— Select Truck —</option>
                            <?php foreach($trucks as $t): ?>
                            <option value="<?=$t['truck_id'];?>" 
                                    data-plate="<?=$t['plate_number'];?>" 
                                    data-odo="<?=$t['current_odometer'];?>">
                                <?=$t['plate_number'];?> (<?=$t['make'] . ' ' . $t['model'];?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="sched_truck_plate">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Maintenance Type <span class="required">*</span></label>
                        <select class="form-control" id="sched_maintenance_type">
                            <option value="PREVENTIVE">Preventive</option>
                            <option value="CORRECTIVE">Corrective</option>
                            <option value="EMERGENCY">Emergency</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Service Type</label>
                        <input type="text" class="form-control" id="sched_service_type" placeholder="PMS, Oil Change, etc.">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="form-label">Scheduled Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="sched_date">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Current Odometer</label>
                        <input type="number" class="form-control" id="sched_current_odo" step="0.01">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Service Interval (km)</label>
                        <input type="number" class="form-control" id="sched_interval" step="0.01" placeholder="10000">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Next Service (km)</label>
                        <input type="number" class="form-control" id="sched_next_odo" step="0.01" readonly>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Technician</label>
                        <input type="text" class="form-control" id="sched_technician" placeholder="Technician name">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Priority</label>
                        <select class="form-control" id="sched_priority">
                            <option value="LOW">Low</option>
                            <option value="NORMAL" selected>Normal</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="sched_status">
                            <option value="SCHEDULED">Scheduled</option>
                            <option value="IN_PROGRESS">In Progress</option>
                            <option value="COMPLETED">Completed</option>
                            <option value="CANCELLED">Cancelled</option>
                            <option value="OVERDUE">Overdue</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="sched_remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="schedSubmitBtn" onclick="__MT.__saveSchedule()">
                    <i class="bi bi-save"></i> <span id="schedBtnText">Save Schedule</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- RECORD MODAL -->
<div class="modal fade" id="recordModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordModalTitle">
                    <i class="bi bi-clipboard-check me-2"></i>Maintenance Record
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rec_id">
                <input type="hidden" id="rec_schedule_id">

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Maintenance Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Truck <span class="required">*</span></label>
                                <select class="form-control" id="rec_truck_id" onchange="__MT.__onRecordTruckChange(this)">
                                    <option value="">— Select Truck —</option>
                                    <?php foreach($trucks as $t): ?>
                                    <option value="<?=$t['truck_id'];?>" 
                                            data-plate="<?=$t['plate_number'];?>" 
                                            data-odo="<?=$t['current_odometer'];?>">
                                        <?=$t['plate_number'];?> (<?=$t['make'] . ' ' . $t['model'];?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="rec_truck_plate">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Maintenance Date <span class="required">*</span></label>
                                <input type="date" class="form-control" id="rec_date">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Odometer (km)</label>
                                <input type="number" class="form-control" id="rec_odometer" step="0.01">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label">Maintenance Type</label>
                                <select class="form-control" id="rec_maintenance_type">
                                    <option value="PREVENTIVE">Preventive</option>
                                    <option value="CORRECTIVE">Corrective</option>
                                    <option value="EMERGENCY">Emergency</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Service Category</label>
                                <input type="text" class="form-control" id="rec_service_category" placeholder="PMS, Engine, Brakes...">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Technician</label>
                                <input type="text" class="form-control" id="rec_technician">
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Problem / Reason</label>
                                <textarea class="form-control" id="rec_problem_reason" rows="2"></textarea>
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Work Performed</label>
                                <textarea class="form-control" id="rec_work_performed" rows="2"></textarea>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label">Downtime (hours)</label>
                                <input type="number" class="form-control" id="rec_downtime" step="0.1">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="rec_status">
                                    <option value="COMPLETED">Completed</option>
                                    <option value="ONGOING">Ongoing</option>
                                    <option value="CANCELLED">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Remarks</label>
                                <input type="text" class="form-control" id="rec_remarks">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Cost Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Labor Cost (₱)</label>
                                <input type="number" class="form-control" id="rec_labor_cost" step="0.01" value="0" oninput="__MT.__updateCostSummary()">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Parts Cost (₱)</label>
                                <input type="number" class="form-control" id="rec_parts_cost" step="0.01" value="0" readonly>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Other Cost (₱)</label>
                                <input type="number" class="form-control" id="rec_other_cost" step="0.01" value="0" oninput="__MT.__updateCostSummary()">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Total Cost (₱)</label>
                                <input type="number" class="form-control" id="rec_total_cost" step="0.01" value="0" readonly style="font-weight:700;color:var(--primary);">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PARTS REPLACEMENT -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Parts Replacement</h6>
                    </div>
                    <div class="card-body">
                        <div class="item-form">
                            <input type="hidden" id="part_editing_id">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label" style="font-size:9px;">Part Name *</label>
                                    <input type="text" class="form-control" id="part_name">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label" style="font-size:9px;">Qty</label>
                                    <input type="number" class="form-control" id="part_qty" value="1" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;">Unit Cost</label>
                                    <input type="number" class="form-control" id="part_unit_cost" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size:9px;">Supplier</label>
                                    <input type="text" class="form-control" id="part_supplier">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;">Warranty</label>
                                    <input type="text" class="form-control" id="part_warranty" placeholder="6 Months">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;">&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" id="partActionBtn" onclick="__MT.__savePart()" style="font-size:12px;padding:6px 8px;">
                                        <i class="bi bi-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Part Name</th>
                                        <th width="60">Qty</th>
                                        <th width="90">Unit Cost</th>
                                        <th width="100">Total Cost</th>
                                        <th>Supplier</th>
                                        <th width="90">Warranty</th>
                                        <th width="80" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="partsBody">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Save record first to add parts</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="recSubmitBtn" onclick="__MT.__saveRecord()">
                    <i class="bi bi-save"></i> <span id="recBtnText">Save Record</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
var scheduleTable;
var recordTable;
var currentMTFilter = 'all';

$(document).ready(function () {
    scheduleTable = $('#scheduleTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'desc']],
        language: { search: "Search:", emptyTable: "No schedules found" },
        columnDefs: [{ orderable: false, targets: [10] }]
    });

    recordTable = $('#recordTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'desc']],
        language: { search: "Search:", emptyTable: "No records found" },
        columnDefs: [{ orderable: false, targets: [9] }]
    });
});
</script>

<script src="<?=base_url('assets/js/apps/fms/maintenance/maintenance.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>