<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("SELECT * FROM tbl_trips ORDER BY trip_id DESC");
$trips = $query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_trips = count($trips);
$total_scheduled = $this->db->query("SELECT COUNT(*) as total FROM tbl_trips WHERE trip_status = 'SCHEDULED'")->getRow()->total;
$total_assigned = $this->db->query("SELECT COUNT(*) as total FROM tbl_trips WHERE trip_status = 'ASSIGNED'")->getRow()->total;
$total_in_transit = $this->db->query("SELECT COUNT(*) as total FROM tbl_trips WHERE trip_status IN ('DISPATCHED', 'IN_TRANSIT')")->getRow()->total;
$total_completed = $this->db->query("SELECT COUNT(*) as total FROM tbl_trips WHERE trip_status IN ('DELIVERED', 'COMPLETED')")->getRow()->total;

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

    .lrg-module-header .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
        min-width: 200px;
    }

    .lrg-module-header .header-left .module-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.12);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #90cdf4;
        border: 1px solid rgba(255,255,255,0.08);
    }

    .lrg-module-header .header-left .module-info h4 {
        font-size: 17px;
        font-weight: 600;
        color: #ffffff;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .lrg-module-header .header-left .module-info h4 .module-badge {
        font-size: 9px;
        font-weight: 600;
        padding: 2px 12px;
        border-radius: 4px;
        background: rgba(255,255,255,0.12);
        color: #bee3f8;
        border: 1px solid rgba(255,255,255,0.08);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-header {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15);
        color: #ffffff;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .btn-header:hover {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.25);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-header-primary {
        background: #ffffff;
        border-color: #ffffff;
        color: var(--primary);
    }

    .btn-header-primary:hover {
        background: rgba(255,255,255,0.9);
        color: var(--primary-dark);
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--gray-200);
        padding: 16px 20px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--gray-300);
    }

    .stat-card:hover::before { opacity: 1; }

    .stat-card.active {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(26,107,176,0.15), var(--shadow-md);
    }

    .stat-card.active::before { opacity: 1; }

    .stat-left .stat-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .stat-left .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--gray-800);
        line-height: 1.2;
    }

    .stat-left .stat-sub {
        font-size: 11px;
        color: var(--gray-400);
        margin-top: 2px;
    }

    .stat-right {
        font-size: 32px;
        color: var(--primary);
        opacity: 0.08;
        line-height: 1;
    }

    .card {
        border-radius: 12px;
        border: 1px solid var(--gray-200);
        background: #ffffff;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
    }

    .card-header {
        background: #ffffff;
        border-bottom: 1px solid var(--gray-200);
        padding: 14px 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header h6 {
        font-size: 13px;
        font-weight: 600;
        margin: 0;
        color: var(--gray-700);
    }

    .card-body { padding: 20px; }

    .form-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 4px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .form-label .required { color: var(--danger); margin-left: 2px; }

    .form-control, select.form-control {
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 13px;
        color: var(--gray-700);
        background: #ffffff;
        transition: all 0.2s;
        width: 100%;
        height: 40px;
    }

    .form-control:focus, select.form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(26,107,176,0.08);
    }

    textarea.form-control {
        height: auto;
        min-height: 60px;
        resize: vertical;
    }

    .form-control[readonly] {
        background: var(--gray-50);
        cursor: not-allowed;
    }

    .btn-primary {
        background: var(--primary);
        border: none;
        border-radius: 8px;
        padding: 9px 20px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(26,107,176,0.3);
        color: #ffffff;
    }

    .btn-danger {
        background: var(--danger);
        border: none;
        border-radius: 8px;
        padding: 9px 20px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-danger:hover {
        background: var(--danger-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220,38,38,0.3);
        color: #ffffff;
    }

    .btn-secondary {
        background: #ffffff;
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 9px 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-600);
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .btn-success {
        background: var(--success);
        border: none;
        border-radius: 8px;
        padding: 9px 20px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        color: #ffffff;
    }

    .btn-sm {
        padding: 5px 14px;
        font-size: 11px;
    }

    .table-wrap { overflow-x: auto; }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .table thead th {
        font-size: 10px;
        font-weight: 600;
        color: var(--gray-500);
        background: var(--gray-50);
        border-bottom: 1.5px solid var(--gray-200);
        padding: 10px 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
        white-space: nowrap;
    }

    .table tbody td {
        font-size: 12px;
        color: var(--gray-700);
        padding: 10px 12px;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
    }

    .table-hover tbody tr:hover { background: var(--gray-50); }

    .badge {
        font-size: 10px;
        font-weight: 600;
        padding: 3px 12px;
        border-radius: 20px;
        letter-spacing: 0.3px;
        display: inline-block;
    }

    .badge-success { background: var(--success); color: #ffffff; }
    .badge-danger { background: var(--danger); color: #ffffff; }
    .badge-warning { background: var(--warning); color: #ffffff; }
    .badge-primary { background: var(--primary); color: #ffffff; }
    .badge-secondary { background: var(--gray-500); color: #ffffff; }
    .badge-info { background: var(--info); color: #ffffff; }

    .action-group {
        display: flex;
        align-items: center;
        gap: 4px;
        justify-content: center;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--gray-500);
        font-size: 14px;
    }

    .btn-icon:hover { transform: scale(1.05); }

    .btn-icon-view { color: var(--info); }
    .btn-icon-view:hover { background: #dbeafe; border-color: #93c5fd; }

    .btn-icon-edit { color: var(--warning); }
    .btn-icon-edit:hover { background: #fef3c7; border-color: #fcd34d; }

    .btn-icon-delete { color: var(--danger); }
    .btn-icon-delete:hover { background: #fee2e2; border-color: #fca5a5; }

    .btn-icon-assign { color: var(--success); }
    .btn-icon-assign:hover { background: #d1fae5; border-color: #6ee7b7; }

    .btn-toolbar {
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid var(--gray-200);
        background: #ffffff;
        color: var(--gray-600);
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }

    .btn-toolbar:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .btn-toolbar-primary {
        background: var(--primary);
        border-color: var(--primary);
        color: #ffffff;
    }

    .btn-toolbar-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        color: #ffffff;
    }

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .toolbar-left { display: flex; align-items: center; gap: 8px; }
    .toolbar-right { display: flex; align-items: center; gap: 10px; }

    .dataTables_wrapper { font-family: 'Inter', sans-serif; }
    .dataTables_filter { float: right; margin-bottom: 16px; }
    .dataTables_filter label {
        font-size: 12px;
        font-weight: 500;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dataTables_filter input {
        width: 200px;
        padding: 6px 12px;
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        font-size: 12px;
        transition: all 0.2s;
        outline: none;
    }
    .dataTables_filter input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(26,107,176,0.08);
    }
    .dataTables_paginate { float: right; margin-top: 16px; }
    .dataTables_paginate .paginate_button {
        padding: 4px 10px !important;
        margin: 0 2px !important;
        border-radius: 6px !important;
        border: 1px solid var(--gray-200) !important;
        background: #ffffff !important;
        color: var(--gray-600) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        transition: all 0.2s;
    }
    .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #ffffff !important;
    }
    .dataTables_paginate .paginate_button:hover {
        background: var(--gray-50) !important;
        border-color: var(--gray-300) !important;
        color: var(--primary) !important;
    }
    .dataTables_info { float: left; font-size: 12px; color: var(--gray-500); margin-top: 16px; }

    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        border-bottom: 1px solid var(--gray-200);
        padding: 18px 24px;
        background: var(--gray-50);
        border-radius: 16px 16px 0 0;
    }

    .modal-header .modal-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-800);
    }

    .modal-header .modal-title i { color: var(--primary); }

    .modal-body { padding: 24px; }

    .modal-footer {
        border-top: 1px solid var(--gray-200);
        padding: 16px 24px;
        gap: 10px;
        background: var(--gray-50);
        border-radius: 0 0 16px 16px;
    }

    .assignment-badge {
        font-size: 9px;
        padding: 2px 10px;
        border-radius: 12px;
        font-weight: 500;
    }
    .assignment-badge.has { background: #d1fae5; color: #065f46; }
    .assignment-badge.none { background: #f3f4f6; color: #6b7280; }

    .toggle-field { display: none; }

    .assignment-info-box {
        background: var(--gray-50);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        border-left: 4px solid var(--primary);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }
    .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state h5 { font-size: 16px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
    .empty-state p { font-size: 13px; color: var(--gray-400); }

    @media (max-width: 992px) {
        .lrg-module-header {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 20px;
        }
        .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .stat-card { padding: 14px 16px; }
        .stat-left .stat-value { font-size: 20px; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-left, .toolbar-right { flex-wrap: wrap; }
        .dataTables_filter input { width: 150px; }
    }

    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
        .lrg-module-header { padding: 12px 16px; }
        .lrg-module-header .header-left .module-info h4 { font-size: 14px; }
        .stat-card { padding: 12px 14px; }
        .stat-left .stat-value { font-size: 18px; }
        .stat-right { font-size: 24px; }
        .btn-icon { width: 28px; height: 28px; font-size: 13px; }
        .modal-body { padding: 16px; }
    }
</style>

<div class="me-trp-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="bi bi-truck"></i>
        </div>
        <div class="module-info">
            <h4>
                Trip Scheduling
                <span class="module-badge">Operations</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="__Trips.__openAddTrip()">
            <i class="bi bi-plus-circle"></i> New Trip
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS CARDS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Trips</div>
            <div class="stat-value"><?=$total_trips;?></div>
            <div class="stat-sub">All trips</div>
        </div>
        <div class="stat-right"><i class="bi bi-truck"></i></div>
    </div>
    <div class="stat-card" data-filter="SCHEDULED" onclick="filterTable('SCHEDULED')">
        <div class="stat-left">
            <div class="stat-label">Scheduled</div>
            <div class="stat-value"><?=$total_scheduled;?></div>
            <div class="stat-sub">Ready for assignment</div>
        </div>
        <div class="stat-right"><i class="bi bi-calendar-check"></i></div>
    </div>
    <div class="stat-card" data-filter="ASSIGNED" onclick="filterTable('ASSIGNED')">
        <div class="stat-left">
            <div class="stat-label">Assigned</div>
            <div class="stat-value"><?=$total_assigned;?></div>
            <div class="stat-sub">Resources assigned</div>
        </div>
        <div class="stat-right"><i class="bi bi-person-check"></i></div>
    </div>
    <div class="stat-card" data-filter="IN_TRANSIT" onclick="filterTable('IN_TRANSIT')">
        <div class="stat-left">
            <div class="stat-label">In Transit</div>
            <div class="stat-value"><?=$total_in_transit;?></div>
            <div class="stat-sub">On the road</div>
        </div>
        <div class="stat-right"><i class="bi bi-arrow-right"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Trip Directory</h6>
                <div>
                    <span class="badge badge-secondary"><?=count($trips);?> records</span>
                </div>
            </div>
            <div class="card-body">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <button class="btn-toolbar btn-toolbar-primary" onclick="__Trips.__openAddTrip()">
                            <i class="bi bi-plus-circle"></i> Add New
                        </button>
                        <button class="btn-toolbar" onclick="window.location.reload();">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <span class="text-muted" style="font-size:12px;">
                            <i class="bi bi-info-circle"></i> Click <strong>Assign</strong> to manage trip resources
                        </span>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="tripTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="120">Trip #</th>
                                <th>Customer</th>
                                <th width="120">Type</th>
                                <th width="100">Priority</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th width="100">Status</th>
                                <th width="100">Assignment</th>
                                <th width="280" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($trips) > 0): ?>
                                <?php foreach($trips as $row): 
                                    $hasAssignment = $row['has_assignment'] ?? 0;
                                    $assignmentLabel = $hasAssignment ? 'Assigned' : 'Pending';
                                    $assignmentClass = $hasAssignment ? 'has' : 'none';
                                ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['trip_code'];?></span></td>
                                    <td>
                                        <?php
                                        $customer = $this->db->query("SELECT customer_name FROM tbl_customers WHERE customer_id = ?", [$row['customer_id']])->getRow();
                                        echo $customer ? $customer->customer_name : '—';
                                        ?>
                                    </td>
                                    <td><?=$row['trip_type'] ?: '—';?></td>
                                    <td>
                                        <?php
                                        $priorityClass = 'badge-secondary';
                                        $priorityLabel = $row['priority'] ?: 'Normal';
                                        if($row['priority'] == 'LOW') { $priorityClass = 'badge-secondary'; }
                                        elseif($row['priority'] == 'NORMAL') { $priorityClass = 'badge-info'; }
                                        elseif($row['priority'] == 'HIGH') { $priorityClass = 'badge-warning'; }
                                        elseif($row['priority'] == 'URGENT') { $priorityClass = 'badge-danger'; }
                                        ?>
                                        <span class="badge <?=$priorityClass;?>"><?=$priorityLabel;?></span>
                                    </td>
                                    <td><?=substr($row['origin'] ?? '—', 0, 30);?></td>
                                    <td><?=substr($row['destination'] ?? '—', 0, 30);?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-secondary';
                                        $statusLabel = $row['trip_status'] ?: 'Draft';
                                        if($row['trip_status'] == 'DRAFT') { $statusClass = 'badge-secondary'; $statusLabel = 'Draft'; }
                                        elseif($row['trip_status'] == 'SCHEDULED') { $statusClass = 'badge-info'; $statusLabel = 'Scheduled'; }
                                        elseif($row['trip_status'] == 'ASSIGNED') { $statusClass = 'badge-primary'; $statusLabel = 'Assigned'; }
                                        elseif($row['trip_status'] == 'DISPATCHED') { $statusClass = 'badge-warning'; $statusLabel = 'Dispatched'; }
                                        elseif($row['trip_status'] == 'IN_TRANSIT') { $statusClass = 'badge-info'; $statusLabel = 'In Transit'; }
                                        elseif($row['trip_status'] == 'DELIVERED') { $statusClass = 'badge-success'; $statusLabel = 'Delivered'; }
                                        elseif($row['trip_status'] == 'COMPLETED') { $statusClass = 'badge-success'; $statusLabel = 'Completed'; }
                                        elseif($row['trip_status'] == 'CANCELLED') { $statusClass = 'badge-danger'; $statusLabel = 'Cancelled'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td>
                                        <span class="assignment-badge <?=$assignmentClass;?>">
                                            <?=$assignmentLabel;?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-view" 
                                                    onclick="__Trips.__viewTrip(<?=$row['trip_id'];?>)" 
                                                    title="View Trip">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-assign" 
                                                    onclick="__Trips.__openAssignmentModal(<?=$row['trip_id'];?>)" 
                                                    title="Manage Assignment">
                                                <i class="bi bi-person-gear"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-view" 
                                                    onclick="__Trips.__viewRoute(<?=$row['trip_id'];?>, '<?=addslashes($row['trip_code']);?>')" 
                                                    title="Manage Route">
                                                <i class="bi bi-map"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-edit" 
                                                    onclick="__Trips.__editTrip(<?=$row['trip_id'];?>)" 
                                                    title="Edit Trip">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-delete" 
                                                    onclick="__Trips.__showDeleteModal(<?=$row['trip_id'];?>, '<?=addslashes($row['trip_code']);?>')" 
                                                    title="Delete Trip">
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

                <?php if(count($trips) == 0): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No trips yet</h5>
                    <p>Click <strong>New Trip</strong> to schedule your first trip.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TRIP MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="tripModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tripModalTitle">
                    <i class="bi bi-plus-circle me-2"></i>New Trip
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tripForm">
                <div class="modal-body">
                    <input type="hidden" id="form_trip_id">
                    <input type="hidden" id="form_trip_code">

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Trip Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Trip Number</label>
                                    <input type="text" id="form_trip_code_display" class="form-control" readonly>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Customer <span class="required">*</span></label>
                                    <select id="form_customer_id" class="form-control" required>
                                        <option value="">— Select Customer —</option>
                                        <?php
                                        $customers = $this->db->query("SELECT customer_id, customer_name FROM tbl_customers WHERE status = 'ACTIVE' ORDER BY customer_name")->getResultArray();
                                        foreach($customers as $c):
                                        ?>
                                        <option value="<?=$c['customer_id'];?>"><?=$c['customer_name'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Booking Reference</label>
                                    <input type="text" id="form_booking_reference" class="form-control" placeholder="BK-2026-00125">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Service Type</label>
                                    <select id="form_service_type" class="form-control">
                                        <option value="">— Select —</option>
                                        <option value="Trucking Service">Trucking Service</option>
                                        <option value="Hauling">Hauling</option>
                                        <option value="Delivery">Delivery</option>
                                        <option value="Distribution">Distribution</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Trip Type</label>
                                    <select id="form_trip_type" class="form-control">
                                        <option value="ONE_WAY">One-Way</option>
                                        <option value="ROUND_TRIP">Round Trip</option>
                                        <option value="MULTI_DROP">Multi-Drop</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Priority</label>
                                    <select id="form_priority" class="form-control">
                                        <option value="LOW">Low</option>
                                        <option value="NORMAL" selected>Normal</option>
                                        <option value="HIGH">High</option>
                                        <option value="URGENT">Urgent</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Scheduled Date <span class="required">*</span></label>
                                    <input type="date" id="form_scheduled_date" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pickup Date</label>
                                    <input type="date" id="form_pickup_date" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Expected Delivery Date</label>
                                    <input type="date" id="form_expected_delivery_date" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Origin</label>
                                    <input type="text" id="form_origin" class="form-control" placeholder="Pickup location">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Destination</label>
                                    <input type="text" id="form_destination" class="form-control" placeholder="Delivery location">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Cargo Description</label>
                                    <textarea id="form_cargo_description" class="form-control" rows="2" placeholder="Describe the cargo"></textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" id="form_quantity" class="form-control" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Unit</label>
                                    <input type="text" id="form_unit" class="form-control" placeholder="Cartons, Pcs, Kg">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Estimated Weight (kg)</label>
                                    <input type="number" id="form_estimated_weight" class="form-control" value="0" step="0.01">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Special Instructions</label>
                                <textarea id="form_special_instructions" class="form-control" rows="2" placeholder="Handle with care, Temperature controlled, etc."></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Trip Status</label>
                                    <select id="form_trip_status" class="form-control">
                                        <option value="DRAFT">Draft</option>
                                        <option value="SCHEDULED" selected>Scheduled</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Remarks</label>
                                    <textarea id="form_remarks" class="form-control" rows="2" placeholder="Additional notes"></textarea>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <small>You can assign resources (driver, truck, etc.) after creating the trip using the <strong>Assign</strong> button.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">
                        <i class="bi bi-save"></i> <span id="formBtnText">Save Trip</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ASSIGNMENT MODAL - UPDATED -->
<!-- ============================================ -->
<div class="modal fade" id="assignmentModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-gear me-2"></i>Edit Assignment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assignment_id">
                <input type="hidden" id="assignment_trip_id">
                
                <!-- Trip Info -->
                <div class="assignment-info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-truck" style="font-size:24px;color:var(--primary);"></i>
                        <div>
                            <div class="text-muted small">Trip Assignment</div>
                            <div class="fw-semibold" id="assignment_trip_code" style="font-size:15px;"></div>
                        </div>
                        <div class="ms-auto">
                            <span id="assignment_status_badge" class="badge badge-secondary">No Assignment</span>
                        </div>
                    </div>
                </div>
                
                <!-- Assignment Form -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Assignment Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Vehicle Type -->
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Vehicle Type <span class="required">*</span></label>
                                <select class="form-control" id="assignment_vehicle_type" onchange="__Trips.__toggleVehicleFields()">
                                    <option value="">— Select —</option>
                                    <option value="RIGID">Rigid Truck</option>
                                    <option value="TRACTOR_CHASSIS">Tractor + Owned Chassis</option>
                                    <option value="TRACTOR_RENTED_CHASSIS">Tractor + Rented Chassis</option>
                                    <option value="RENTED_ALL">Rented All (Package)</option>
                                </select>
                            </div>
                            
                            <!-- Truck (RIGID) -->
                            <div class="col-md-4 mb-2 toggle-field" id="assignment_truck_field" style="display:none;">
                                <label class="form-label">Truck <span class="required">*</span></label>
                                <select class="form-control" id="assignment_truck_plate">
                                    <option value="">— Select —</option>
                                </select>
                            </div>
                            
                            <!-- Tractor -->
                            <div class="col-md-4 mb-2 toggle-field" id="assignment_tractor_field" style="display:none;">
                                <label class="form-label">Tractor <span class="required">*</span></label>
                                <select class="form-control" id="assignment_tractor_plate">
                                    <option value="">— Select —</option>
                                </select>
                            </div>
                            
                            <!-- Chassis -->
                            <div class="col-md-4 mb-2 toggle-field" id="assignment_chassis_field" style="display:none;">
                                <label class="form-label">Chassis <span class="required">*</span></label>
                                <select class="form-control" id="assignment_chassis_plate">
                                    <option value="">— Select —</option>
                                </select>
                            </div>
                            
                            <!-- Chassis Type -->
                            <div class="col-md-3 mb-2 toggle-field" id="assignment_chassis_type_field" style="display:none;">
                                <label class="form-label">Chassis Type</label>
                                <select class="form-control" id="assignment_chassis_type" onchange="__Trips.__toggleRentalFields()">
                                    <option value="OWNED">Owned</option>
                                    <option value="RENTED">Rented</option>
                                </select>
                            </div>
                            
                            <!-- Rented All Section -->
                            <div class="col-md-12 mb-2 toggle-field" id="assignment_rented_section" style="display:none;">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Rented All Package:</strong> This includes Tractor, Chassis, Driver, and Helper as a complete package from the vendor.
                                </div>
                            </div>
                            
                            <!-- Vendor -->
                            <div class="col-md-3 mb-2 toggle-field" id="assignment_vendor_field" style="display:none;">
                                <label class="form-label">Vendor <span class="required">*</span></label>
                                <select class="form-control" id="assignment_vendor_name">
                                    <option value="">— Select —</option>
                                </select>
                            </div>
                            
                            <!-- Driver -->
                            <div class="col-md-3 mb-2" id="assignment_driver_field">
                                <label class="form-label">Driver <span class="required">*</span></label>
                                <select class="form-control" id="assignment_driver_name">
                                    <option value="">— Select —</option>
                                </select>
                            </div>
                            
                            <!-- Helper -->
                            <div class="col-md-3 mb-2" id="assignment_helper_field">
                                <label class="form-label">Helper</label>
                                <select class="form-control" id="assignment_helper_name">
                                    <option value="">— Select —</option>
                                </select>
                            </div>
                            
                            <!-- Rental Fields -->
                            <div id="assignment_rental_fields" style="display:none;" class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Rental Rate</label>
                                    <input type="number" class="form-control" id="assignment_rental_rate" step="0.01" placeholder="0.00">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Rental Start</label>
                                    <input type="date" class="form-control" id="assignment_rental_start_date">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Rental End</label>
                                    <input type="date" class="form-control" id="assignment_rental_end_date">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Agreement No.</label>
                                    <input type="text" class="form-control" id="assignment_rental_agreement_no" placeholder="RA-2026-001">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Contact Person</label>
                                    <input type="text" class="form-control" id="assignment_vendor_contact_person" placeholder="Contact person">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="assignment_vendor_contact_number" placeholder="0917-555-1234">
                                </div>
                            </div>
                            
                            <!-- Assignment Date -->
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Assignment Date <span class="required">*</span></label>
                                <input type="date" class="form-control" id="assignment_assignment_date">
                            </div>
                            
                            <!-- Dispatch Time -->
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Dispatch Time</label>
                                <input type="time" class="form-control" id="assignment_dispatch_time" value="06:00">
                            </div>
                            
                            <!-- Dispatch Location -->
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Dispatch Location</label>
                                <input type="text" class="form-control" id="assignment_dispatch_location" placeholder="Main Yard">
                            </div>
                            
                            <!-- Odometer -->
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Odometer (km)</label>
                                <input type="number" class="form-control" id="assignment_odometer_before_trip" step="0.01" placeholder="0.00">
                            </div>
                            
                            <!-- Fuel Level -->
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Fuel Level %</label>
                                <input type="number" class="form-control" id="assignment_fuel_level" max="100" placeholder="100">
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="assignment_status">
                                    <option value="ASSIGNED">Assigned</option>
                                    <option value="DISPATCHED">Dispatched</option>
                                    <option value="IN_TRANSIT">In Transit</option>
                                    <option value="COMPLETED">Completed</option>
                                    <option value="CANCELLED">Cancelled</option>
                                </select>
                            </div>
                            
                            <!-- Remarks -->
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" id="assignment_remarks" rows="2" placeholder="Additional notes"></textarea>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="col-12 text-end mt-2">
                                <button type="button" class="btn btn-secondary me-2" onclick="__Trips.__resetAssignmentForm()">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                                <button type="button" class="btn btn-success" id="assignmentActionBtn" onclick="__Trips.__saveAssignment()">
                                    <i class="bi bi-check"></i> <span id="assignmentBtnText">Save Assignment</span>
                                </button>
                                <button type="button" class="btn btn-danger" id="assignment_remove_btn" onclick="__Trips.__removeAssignment()" style="display:none;">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================ -->
<!-- VIEW TRIP MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-truck me-2"></i>Trip Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewTripContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ROUTE MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="routeModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-route me-2"></i>Trip Route Planning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="route_trip_id">
                <input type="hidden" id="route_editing_id" value="">
                
                <div class="d-flex align-items-center gap-3 mb-3 p-3" style="background:var(--gray-50);border-radius:8px;">
                    <i class="bi bi-truck" style="font-size:24px;color:var(--primary);"></i>
                    <div>
                        <div class="fw-semibold" id="route_trip_code" style="font-size:15px;"></div>
                        <div class="text-muted" style="font-size:12px;">Plan the route waypoints for this trip</div>
                    </div>
                </div>
                
                <div class="row g-2 mb-3 p-3" style="background:#ffffff;border:1px solid var(--gray-200);border-radius:8px;">
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Sequence #</label>
                        <input type="number" id="wp_sequence" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Waypoint Type *</label>
                        <select id="wp_type" class="form-control">
                            <option value="GARAGE">Garage</option>
                            <option value="EMPTY_CONTAINER_PICKUP">Empty Container Pickup</option>
                            <option value="CLIENT_WAREHOUSE">Client Warehouse</option>
                            <option value="DELIVERY_DESTINATION">Delivery Destination</option>
                            <option value="PORT_TERMINAL">Port / Terminal</option>
                            <option value="RETURN_POINT">Return Point</option>
                            <option value="PICKUP_LOCATION">Pickup Location</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Waypoint Name *</label>
                        <input type="text" id="wp_name" class="form-control" placeholder="e.g., Manila Port">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Address</label>
                        <input type="text" id="wp_address" class="form-control" placeholder="Address">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">&nbsp;</label>
                        <button class="btn btn-primary w-100" id="wpActionBtn" onclick="__Trips.__saveWaypoint()" style="font-size:12px;padding:6px 8px;white-space:nowrap;">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                    
                    <div class="col-12 mt-2">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Expected Arrival</label>
                                <input type="datetime-local" id="wp_expected_arrival" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Expected Departure</label>
                                <input type="datetime-local" id="wp_expected_departure" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Remarks</label>
                                <input type="text" id="wp_remarks" class="form-control" placeholder="Additional notes">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Waypoint</th>
                                <th>Type</th>
                                <th>Address</th>
                                <th>Expected Arrival</th>
                                <th>Expected Departure</th>
                                <th width="80" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="waypointsBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted">No waypoints found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- DELETE MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-trash me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle" style="font-size:56px;color:var(--danger);opacity:0.6;"></i>
                <h5 class="mt-3">Delete Trip</h5>
                <p class="text-muted">You are about to delete:<br>
                    <strong id="delete_trip_name" class="text-danger"></strong>
                </p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
var tripTable;

$(document).ready(function () {
    tripTable = $('#tripTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']],
        language: {
            search: "Search Trip:",
            emptyTable: "No trips found"
        },
        columnDefs: [
            { orderable: false, targets: [8] }
        ]
    });

    $('#confirmDeleteBtn').on('click', function() {
        __Trips.__deleteTrip();
        var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
    });

    $('#tripForm').on('submit', function(e) {
        e.preventDefault();
        var trip_id = $('#form_trip_id').val();
        if(trip_id) {
            __Trips.__updateTrip();
        } else {
            __Trips.__saveTrip();
        }
    });
});

// =============================================
// FILTER TABLE BY STATUS
// =============================================
function filterTable(status) {
    $('.stat-card').removeClass('active');
    
    var columnIndex = 6;
    
    if(status === 'all') {
        tripTable.column(columnIndex).search('').draw();
        $('.stat-card[data-filter="all"]').addClass('active');
    } else if(status === 'SCHEDULED') {
        tripTable.column(columnIndex).search('Scheduled').draw();
        $('.stat-card[data-filter="SCHEDULED"]').addClass('active');
    } else if(status === 'ASSIGNED') {
        tripTable.column(columnIndex).search('Assigned').draw();
        $('.stat-card[data-filter="ASSIGNED"]').addClass('active');
    } else if(status === 'IN_TRANSIT') {
        tripTable.column(columnIndex).search('Dispatched|In Transit', true, false).draw();
        $('.stat-card[data-filter="IN_TRANSIT"]').addClass('active');
    }
}
</script>

<script src="<?=base_url('assets/js/apps/fms/trip/trip.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>