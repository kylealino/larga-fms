<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// Fetch assigned trips
$trips = $this->db->query("
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
           d.dispatch_status,
           d.container_return_required,
           d.container_return_status,
           CASE WHEN d.dispatch_id IS NOT NULL THEN 1 ELSE 0 END as has_dispatch
    FROM tbl_trips t
    LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
    LEFT JOIN tbl_trip_assignments a ON t.trip_id = a.trip_id
    LEFT JOIN tbl_dispatch d ON t.trip_id = d.trip_id
    WHERE t.trip_status IN ('ASSIGNED', 'DISPATCHED', 'IN_TRANSIT', 'DELIVERED', 'COMPLETED')
    ORDER BY t.scheduled_date DESC
")->getResultArray();

$total_assigned = 0;
$total_pending = 0;
$total_dispatched = 0;
$total_in_transit = 0;
$total_delivered = 0;
$total_container_return = 0;
$total_dispatch_completed = 0;

foreach($trips as $row) {
    $total_assigned++; // Count all returned rows for "Total Assigned Trips" card
    if(empty($row['dispatch_id'])) $total_pending++; // Assigned but not yet dispatched
    if($row['dispatch_status'] == 'DISPATCHED') $total_dispatched++;
    elseif($row['dispatch_status'] == 'IN_TRANSIT') $total_in_transit++;
    elseif($row['dispatch_status'] == 'DELIVERED') $total_delivered++;
    elseif($row['dispatch_status'] == 'COMPLETED') $total_dispatch_completed++;

    if((int) ($row['container_return_required'] ?? 0) === 1 && $row['container_return_status'] !== 'RETURNED') {
        $total_container_return++;
    }
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
        grid-template-columns: repeat(3, 1fr);
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

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 8px;
        cursor: pointer;
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

    .btn-warning {
        background: var(--warning);
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

    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.3);
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

    .btn-icon-dispatch { color: var(--success); }
    .btn-icon-dispatch:hover { background: #d1fae5; border-color: #6ee7b7; }

    /* Waypoint journey timeline */
    .journey-timeline { position: relative; padding-left: 48px; margin-top: 4px; }
    .journey-timeline::before {
        content: ''; position: absolute; left: 19px; top: 4px; bottom: 4px;
        width: 2px; background: var(--gray-200);
    }
    .journey-item {
        position: relative; padding: 14px 16px; background: #ffffff;
        border: 1px solid var(--gray-200); border-radius: 10px;
        margin-bottom: 14px; box-shadow: var(--shadow);
    }
    .journey-item .wp-dot {
        position: absolute; left: -48px; top: 12px;
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #ffffff; border: 3px solid var(--gray-300);
        font-weight: 700; font-size: 13px; color: var(--gray-500);
    }
    .journey-item.wp-arrived .wp-dot { border-color: var(--info); color: var(--info); }
    .journey-item.wp-departed .wp-dot { border-color: var(--warning); color: var(--warning); }
    .journey-item.wp-completed .wp-dot { border-color: var(--success); background: var(--success); color: #ffffff; }
    .journey-item .j-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
    .journey-item .j-type { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-500); }
    .journey-item .j-description { font-size: 14px; font-weight: 600; color: var(--gray-800); margin-bottom: 6px; }
    .journey-item .j-details { font-size: 11px; color: var(--gray-500); line-height: 1.7; }
    .journey-item .wp-actions { margin-top: 10px; display: flex; gap: 6px; }

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

    .btn-toolbar-filter {
        background: var(--gray-50);
        border-color: var(--gray-200);
        color: var(--gray-600);
        font-size: 11px;
        padding: 3px 10px;
    }

    .btn-toolbar-filter.active {
        background: var(--primary);
        border-color: var(--primary);
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

    .dispatch-badge {
        font-size: 9px;
        padding: 2px 10px;
        border-radius: 12px;
        font-weight: 500;
    }
    .dispatch-badge.has { background: #d1fae5; color: #065f46; }
    .dispatch-badge.none { background: #f3f4f6; color: #6b7280; }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }
    .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state h5 { font-size: 16px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
    .empty-state p { font-size: 13px; color: var(--gray-400); }

    .trip-info-box {
        background: var(--gray-50);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        border-left: 4px solid var(--primary);
    }

    .container-fields, .container-return-fields {
        background: var(--gray-50);
        border-radius: 8px;
        padding: 16px;
        margin-top: 10px;
        border-left: 3px solid var(--info);
    }

    .total-amount {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .section-header h6 {
        margin: 0;
        font-weight: 600;
    }

    .checklist-form, .expense-form {
        background: var(--gray-50);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
        border: 1px solid var(--gray-200);
    }

    .vehicle-display {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        background: var(--gray-100);
        display: inline-block;
    }
    .vehicle-display .vehicle-type {
        font-weight: 600;
        color: var(--primary);
    }
    .vehicle-display .vehicle-plate {
        color: var(--gray-700);
    }

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

<div class="me-dsp-msg"></div>
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
                Dispatch Monitoring
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

<!-- ============================================ -->
<!-- STATS CARDS - CLICKABLE -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card active" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Assigned Trips</div>
            <div class="stat-value"><?=$total_assigned;?></div>
            <div class="stat-sub">Ready for dispatch</div>
        </div>
        <div class="stat-right"><i class="bi bi-truck"></i></div>
    </div>
    <div class="stat-card" data-filter="PENDING" onclick="filterTable('PENDING')">
        <div class="stat-left">
            <div class="stat-label">Pending</div>
            <div class="stat-value"><?=$total_pending;?></div>
            <div class="stat-sub">Awaiting dispatch</div>
        </div>
        <div class="stat-right"><i class="bi bi-hourglass-split"></i></div>
    </div>
    <div class="stat-card" data-filter="DISPATCHED" onclick="filterTable('DISPATCHED')">
        <div class="stat-left">
            <div class="stat-label">Dispatched</div>
            <div class="stat-value"><?=$total_dispatched;?></div>
            <div class="stat-sub">On the way</div>
        </div>
        <div class="stat-right"><i class="bi bi-arrow-right"></i></div>
    </div>
    <div class="stat-card" data-filter="IN_TRANSIT" onclick="filterTable('IN_TRANSIT')">
        <div class="stat-left">
            <div class="stat-label">In Transit</div>
            <div class="stat-value"><?=$total_in_transit;?></div>
            <div class="stat-sub">En route</div>
        </div>
        <div class="stat-right"><i class="bi bi-geo-alt"></i></div>
    </div>
    <div class="stat-card" data-filter="DELIVERED" onclick="filterTable('DELIVERED')">
        <div class="stat-left">
            <div class="stat-label">Delivered</div>
            <div class="stat-value"><?=$total_delivered;?></div>
            <div class="stat-sub">Cargo dropped off</div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
    </div>
    <div class="stat-card" data-filter="CONTAINER_RETURN" onclick="filterTable('CONTAINER_RETURN')">
        <div class="stat-left">
            <div class="stat-label">Container Return</div>
            <div class="stat-value"><?=$total_container_return;?></div>
            <div class="stat-sub">Pending return to port</div>
        </div>
        <div class="stat-right"><i class="bi bi-box-seam"></i></div>
    </div>
    <div class="stat-card" data-filter="COMPLETED" onclick="filterTable('COMPLETED')">
        <div class="stat-left">
            <div class="stat-label">Completed</div>
            <div class="stat-value"><?=$total_dispatch_completed;?></div>
            <div class="stat-sub">Crew back, dispatch closed</div>
        </div>
        <div class="stat-right"><i class="bi bi-check2-all"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Assigned Trips for Dispatch</h6>
                <div>
                    <span class="badge badge-secondary" id="recordCount"><?=count($trips);?> records</span>
                    <button class="btn-toolbar btn-toolbar-filter btn-sm ms-2" onclick="filterTable('all')" id="clearFilterBtn" style="display:none;">
                        <i class="bi bi-x"></i> Clear Filter
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <button class="btn-toolbar" onclick="window.location.reload();">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <span class="text-muted" style="font-size:12px;">
                            <i class="bi bi-info-circle"></i> Click <strong>Dispatch</strong> to process trip
                        </span>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="dispatchTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="120">Trip #</th>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th width="100">Status</th>
                                <th width="100">Dispatch</th>
                                <th width="250" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($trips) > 0): ?>
                                <?php foreach($trips as $row): 
                                    $hasDispatch = $row['has_dispatch'] ?? 0;
                                    $dispatchLabel = $hasDispatch ? 'Dispatched' : 'Pending';
                                    $dispatchClass = $hasDispatch ? 'has' : 'none';
                                    $statusLabel = $row['trip_status'] ?: 'Assigned';
                                    
                                    $vehicleDisplay = '—';
                                    $vehicleType = $row['vehicle_type'] ?? '';
                                    $vehicleTypeLabel = $vehicleType ? str_replace('_', ' ', $vehicleType) : '—';
                                    $truckPlate = $row['truck_plate'] ?? '';
                                    $tractorPlate = $row['tractor_plate'] ?? '';
                                    $chassisPlate = $row['chassis_plate'] ?? '';
                                    $vendorName = $row['vendor_name'] ?? '';
                                    
                                    if($vehicleType == 'RIGID') {
                                        $vehicleDisplay = $truckPlate ?: '—';
                                    } elseif($vehicleType == 'TRACTOR_CHASSIS') {
                                        if($tractorPlate && $chassisPlate) {
                                            $vehicleDisplay = $tractorPlate . ' + ' . $chassisPlate;
                                        } elseif($tractorPlate) {
                                            $vehicleDisplay = $tractorPlate . ' + (Chassis)';
                                        } elseif($chassisPlate) {
                                            $vehicleDisplay = '(Tractor) + ' . $chassisPlate;
                                        } else {
                                            $vehicleDisplay = 'Tractor + Chassis';
                                        }
                                    } elseif($vehicleType == 'TRACTOR_RENTED_CHASSIS') {
                                        if($tractorPlate && $chassisPlate) {
                                            $vehicleDisplay = $tractorPlate . ' + ' . $chassisPlate . ' (Rented)';
                                        } elseif($tractorPlate) {
                                            $vehicleDisplay = $tractorPlate . ' + (Rented Chassis)';
                                        } elseif($chassisPlate) {
                                            $vehicleDisplay = '(Tractor) + ' . $chassisPlate . ' (Rented)';
                                        } else {
                                            $vehicleDisplay = 'Tractor + Rented Chassis';
                                        }
                                    } elseif($vehicleType == 'CHASSIS_ONLY') {
                                        $vehicleDisplay = $chassisPlate ?: 'Chassis Only';
                                    } elseif($vehicleType == 'RENTED_ALL') {
                                        $vehicleDisplay = 'Package: ' . ($vendorName ?: 'Vendor');
                                    } else {
                                        $vehicleDisplay = '—';
                                    }
                                ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['trip_code'];?></span></td>
                                    <td><?=$row['customer_name'] ?? '—';?></td>
                                    <td>
                                        <span class="vehicle-display">
                                            <span class="vehicle-type"><?=$vehicleTypeLabel;?></span>
                                            <span class="vehicle-plate"><?=$vehicleDisplay;?></span>
                                        </span>
                                    </td>
                                    <td><?=$row['driver_name'] ?? '—';?></td>
                                    <td><?=substr($row['origin'] ?? '—', 0, 20);?></td>
                                    <td><?=substr($row['destination'] ?? '—', 0, 20);?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-info';
                                        if($row['trip_status'] == 'ASSIGNED') { $statusClass = 'badge-primary'; $statusLabel = 'Assigned'; }
                                        elseif($row['trip_status'] == 'DISPATCHED') { $statusClass = 'badge-warning'; $statusLabel = 'Dispatched'; }
                                        elseif($row['trip_status'] == 'IN_TRANSIT') { $statusClass = 'badge-info'; $statusLabel = 'In Transit'; }
                                        elseif($row['trip_status'] == 'DELIVERED') { $statusClass = 'badge-success'; $statusLabel = 'Delivered'; }
                                        elseif($row['trip_status'] == 'COMPLETED') { $statusClass = 'badge-success'; $statusLabel = 'Completed'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td>
                                        <span class="dispatch-badge <?=$dispatchClass;?>">
                                            <?=$dispatchLabel;?>
                                        </span>
                                        <?php if((int) ($row['container_return_required'] ?? 0) === 1 && $row['container_return_status'] !== 'RETURNED'): ?>
                                        <br><span class="badge badge-warning" style="font-size:9px;margin-top:4px;">Container Return</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-dispatch" 
                                                    onclick="__Dispatch.__openDispatchModal(<?=$row['trip_id'];?>)" 
                                                    title="<?=$hasDispatch ? 'Edit Dispatch' : 'Create Dispatch';?>">
                                                <i class="bi bi-<?=$hasDispatch ? 'pencil' : 'plus-circle';?>"></i>
                                            </button>
                                            <?php if($hasDispatch): ?>
                                            <button class="btn-icon btn-icon-print" 
                                                    onclick="__Dispatch.__showPdfInModal('<?= base_url('fms-dispatch?meaction=PRINT-DR-PREVIEW&trip_id='.$row['trip_id']) ?>')" 
                                                    title="Print DR (Pre-Print)">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if($hasDispatch): ?>
                                            <button class="btn-icon btn-icon-view"
                                                    onclick="__Dispatch.__openWaypointTracking(<?=$row['trip_id'];?>, '<?=addslashes($row['trip_code']);?>')"
                                                    title="Track Waypoints">
                                                <i class="bi bi-geo-alt"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if($hasDispatch && isset($row['dispatch_id']) && $row['dispatch_id']): ?>
                                            <button class="btn-icon btn-icon-delete" 
                                                    onclick="__Dispatch.__deleteDispatch(<?=$row['dispatch_id'];?>, <?=$row['trip_id'];?>)" 
                                                    title="Delete Dispatch">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
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
                    <h5>No assigned trips</h5>
                    <p>Please assign resources to trips first before dispatching.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN DISPATCH MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="dispatchModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dispatchModalTitle">
                    <i class="bi bi-plus-circle me-2"></i>New Dispatch
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="dispatch_id">
                <input type="hidden" id="dispatch_trip_id">
                
                <!-- Trip Info -->
                <div class="trip-info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-truck" style="font-size:24px;color:var(--primary);"></i>
                        <div>
                            <div class="text-muted small">Trip Assignment</div>
                            <div class="fw-semibold" id="dispatch_trip_code" style="font-size:15px;"></div>
                        </div>
                        <div class="ms-auto">
                            <span class="badge badge-primary">Dispatch #: <span id="dispatch_code_display"></span></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2"><strong>Vehicle Type:</strong> <span id="dispatch_vehicle_type_display">—</span></div>
                        <div class="col-md-2"><strong>Vehicle:</strong> <span id="dispatch_truck_display">—</span></div>
                        <div class="col-md-2"><strong>Driver:</strong> <span id="dispatch_driver_display">—</span></div>
                        <div class="col-md-2"><strong>Helper:</strong> <span id="dispatch_helper_display">—</span></div>
                        <div class="col-md-2"><strong>Origin:</strong> <span id="dispatch_origin_display">—</span></div>
                        <div class="col-md-2"><strong>Destination:</strong> <span id="dispatch_destination_display">—</span></div>
                    </div>
                </div>
                
                <!-- Dispatch Form -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Dispatch Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Dispatch Date <span class="required">*</span></label>
                                <input type="date" class="form-control" id="dispatch_date">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Dispatch Time</label>
                                <input type="time" class="form-control" id="dispatch_time" value="06:00">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Dispatcher Name</label>
                                <input type="text" class="form-control" id="dispatcher_name" placeholder="Dispatcher name">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Dispatch Status</label>
                                <select class="form-control" id="dispatch_status">
                                    <option value="DISPATCHED">Dispatched</option>
                                    <option value="IN_TRANSIT">In Transit</option>
                                    <option value="DELIVERED">Delivered</option>
                                    <option value="COMPLETED">Completed</option>
                                    <option value="CANCELLED">Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Truck/Vehicle</label>
                                <input type="text" class="form-control" id="dispatch_truck" placeholder="Vehicle details">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Driver</label>
                                <input type="text" class="form-control" id="dispatch_driver" placeholder="Driver name">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Helper</label>
                                <input type="text" class="form-control" id="dispatch_helper" placeholder="Helper name">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Origin</label>
                                <input type="text" class="form-control" id="dispatch_origin" placeholder="Origin">
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Destination</label>
                                <input type="text" class="form-control" id="dispatch_destination" placeholder="Destination">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Odometer Out (km)</label>
                                <input type="number" class="form-control" id="odometer_out" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Fuel Level Out (%)</label>
                                <input type="number" class="form-control" id="fuel_level_out" max="100" placeholder="100">
                            </div>
                        </div>
                        
                        <!-- Container Section -->
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="container_required">
                                    <label class="form-check-label" for="container_required">
                                        <strong>Container Required</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div id="container_fields" style="display:none;" class="container-fields">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Container Number</label>
                                    <input type="text" class="form-control" id="container_number" placeholder="ABCU1234567">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Container Type</label>
                                    <input type="text" class="form-control" id="container_type" placeholder="40ft Dry Container">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Container Description</label>
                                    <input type="text" class="form-control" id="container_description" placeholder="Description">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Container Markings</label>
                                    <input type="text" class="form-control" id="container_markings" placeholder="ABC / 123456 / LARSA">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Container Reference</label>
                                    <input type="text" class="form-control" id="container_reference" placeholder="CONT-2026-00125">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Release Port</label>
                                    <input type="text" class="form-control" id="container_release_port" placeholder="Manila Port">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Release Date</label>
                                    <input type="date" class="form-control" id="container_release_date">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Release Time</label>
                                    <input type="time" class="form-control" id="container_release_time">
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="container_return_required">
                                        <label class="form-check-label" for="container_return_required">
                                            <strong>Container Return Required</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="container_return_fields" style="display:none;" class="container-return-fields">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Return Date</label>
                                        <input type="date" class="form-control" id="container_return_date">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Return Time</label>
                                        <input type="time" class="form-control" id="container_return_time">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Return Port</label>
                                        <input type="text" class="form-control" id="container_return_port" placeholder="Return port">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Return Odometer (km)</label>
                                        <input type="number" class="form-control" id="container_return_odometer" step="0.01" placeholder="0.00">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Return Status</label>
                                        <select class="form-control" id="container_return_status">
                                            <option value="NOT_APPLICABLE">Not Applicable</option>
                                            <option value="FOR_RETURN">For Return</option>
                                            <option value="RETURNED">Returned</option>
                                            <option value="OVERDUE">Overdue</option>
                                            <option value="DAMAGED">Damaged</option>
                                            <option value="LOST">Lost</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Return Proof</label>
                                        <input type="text" class="form-control" id="container_return_proof" placeholder="Proof reference">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Delivery & Monitoring -->
                        <div class="row mt-3">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Actual Delivery Date</label>
                                <input type="date" class="form-control" id="actual_delivery_date">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Actual Delivery Time</label>
                                <input type="time" class="form-control" id="actual_delivery_time">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Odometer In (km)</label>
                                <input type="number" class="form-control" id="odometer_in" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Fuel Level In (%)</label>
                                <input type="number" class="form-control" id="fuel_level_in" max="100" placeholder="100">
                            </div>
                            
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Total Distance (km)</label>
                                <input type="number" class="form-control" id="total_distance" step="0.01" placeholder="0.00" readonly>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Fuel Consumed (%)</label>
                                <input type="number" class="form-control" id="fuel_consumed" step="0.01" placeholder="0.00" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Delay Reason</label>
                                <input type="text" class="form-control" id="delay_reason" placeholder="If any delay occurred">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" id="dispatch_remarks" rows="2" placeholder="Additional notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- CHECKLIST SECTION -->
                <!-- ============================================ -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Tools & Equipment Checklist</h6>
                    </div>
                    <div class="card-body">
                        <div class="checklist-form">
                            <input type="hidden" id="cl_editing_id">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Item Name *</label>
                                    <input type="text" class="form-control" id="cl_item_name" placeholder="e.g., Jack">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Avail Qty</label>
                                    <input type="number" class="form-control" id="cl_available_qty" value="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Req Qty</label>
                                    <input type="number" class="form-control" id="cl_required_qty" value="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Condition</label>
                                    <select class="form-control" id="cl_condition">
                                        <option value="GOOD">Good</option>
                                        <option value="FAIR">Fair</option>
                                        <option value="POOR">Poor</option>
                                        <option value="DAMAGED">Damaged</option>
                                        <option value="MISSING">Missing</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Checked</label>
                                    <input type="checkbox" class="form-check-input" id="cl_checked" checked>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Accountable Person</label>
                                    <input type="text" class="form-control" id="cl_accountable" placeholder="Driver/Helper name">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Status</label>
                                    <select class="form-control" id="cl_status">
                                        <option value="COMPLETE">Complete</option>
                                        <option value="INCOMPLETE">Incomplete</option>
                                        <option value="MISSING">Missing</option>
                                        <option value="DAMAGED">Damaged</option>
                                        <option value="NOT_APPLICABLE">N/A</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Remarks</label>
                                    <input type="text" class="form-control" id="cl_remarks" placeholder="Notes">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">&nbsp;</label>
                                    <button class="btn btn-primary w-100" id="clActionBtn" onclick="__Dispatch.__saveChecklistItem()" style="font-size:12px;padding:6px 8px;white-space:nowrap;">
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
                                        <th>Item Name</th>
                                        <th width="80">Avail</th>
                                        <th width="80">Req</th>
                                        <th width="100">Condition</th>
                                        <th width="60">Checked</th>
                                        <th>Accountable</th>
                                        <th width="100">Status</th>
                                        <th width="80" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="checklistBody">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No checklist items</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- EXPENSES SECTION -->
                <!-- ============================================ -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <div class="section-header">
                            <h6 class="mb-0"><i class="bi bi-wallet me-2"></i>Dispatch Expenses</h6>
                            <span class="total-amount" id="total_expenses">₱0.00</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="expense-form">
                            <input type="hidden" id="exp_editing_id">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Date</label>
                                    <input type="date" class="form-control" id="exp_date">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Type *</label>
                                    <select class="form-control" id="exp_type">
                                        <option value="">— Select —</option>
                                        <option value="TOLL">Toll</option>
                                        <option value="PARKING">Parking</option>
                                        <option value="FUEL">Fuel</option>
                                        <option value="MEALS">Meals</option>
                                        <option value="LOADING_UNLOADING">Loading/Unloading</option>
                                        <option value="CONTAINER_RENTAL">Container Rental</option>
                                        <option value="EMPTY_RETURN_FEE">Empty Return Fee</option>
                                        <option value="CONTAINER_RETURN_FEE">Container Return Fee</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Amount *</label>
                                    <input type="number" class="form-control" id="exp_amount" step="0.01" placeholder="0.00">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Description</label>
                                    <input type="text" class="form-control" id="exp_description" placeholder="Description">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Paid By</label>
                                    <input type="text" class="form-control" id="exp_paid_by" placeholder="Driver">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Reference No.</label>
                                    <input type="text" class="form-control" id="exp_reference" placeholder="Ref #">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">Remarks</label>
                                    <input type="text" class="form-control" id="exp_remarks" placeholder="Notes">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;color:var(--gray-500);">&nbsp;</label>
                                    <button class="btn btn-primary w-100" id="expActionBtn" onclick="__Dispatch.__saveExpenseItem()" style="font-size:12px;padding:6px 8px;white-space:nowrap;">
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
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th width="100" class="text-end">Amount</th>
                                        <th>Paid By</th>
                                        <th>Reference</th>
                                        <th width="80" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="expensesBody">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No expenses recorded</td>
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
                <button type="button" class="btn btn-primary" id="dispatchSubmitBtn" onclick="__Dispatch.__saveDispatch()">
                    <i class="bi bi-save"></i> <span id="dispatchBtnText">Save Dispatch</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- WAYPOINT TRACKING MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="waypointTrackingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-geo-alt me-2"></i>Waypoint Tracking - <span id="tracking_trip_code"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tracking_trip_id">
                <input type="hidden" id="tracking_waypoint_id">

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-list me-2"></i>Route Waypoints</h6>
                    </div>
                    <div class="card-body">
                        <div id="trackingWaypointsBody">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
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
<!-- TRACKING UPDATE MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="trackingUpdateModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trackingUpdateTitle">
                    <i class="bi bi-clock me-2"></i>Record Arrival
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tracking_update_waypoint_id">
                <input type="hidden" id="tracking_update_type">
                <input type="hidden" id="tracking_update_trip_id">
                
                <div class="mb-3">
                    <label class="form-label">Waypoint</label>
                    <div class="fw-semibold" id="tracking_update_waypoint_name"></div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Date/Time <span class="required">*</span></label>
                    <input type="datetime-local" class="form-control" id="tracking_update_datetime">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" id="tracking_update_remarks" rows="2" placeholder="Additional notes"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="trackingUpdateBtn" onclick="__Dispatch.__saveWaypointTracking()">
                    <i class="bi bi-check"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- PDF PREVIEW MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delivery Receipt Preview (Pre-Print)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfFrame" src="" style="width: 100%; height: 80vh;" frameborder="0"></iframe>
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
var dispatchTable;
var currentFilter = 'all';

$(document).ready(function () {
    dispatchTable = $('#dispatchTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']],
        language: {
            search: "Search:",
            emptyTable: "No assigned trips found"
        },
        columnDefs: [
            { orderable: false, targets: [8] }
        ]
    });
});

// =============================================
// FILTER TABLE BY TRIP STATUS
// =============================================
function filterTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    currentFilter = status;

    // Column 6 = Status column (Assigned / Dispatched / In Transit / Delivered / Completed)
    var columnIndex = 6;

    if (status === 'all') {
        dispatchTable.column(columnIndex).search('', true, false).draw();
        dispatchTable.column(7).search('', true, false).draw();
        $('#clearFilterBtn').hide();
        $('#recordCount').text('<?=count($trips);?> records');
    } else if (status === 'PENDING') {
        // "Pending" lives in the Dispatch column (assigned but no dispatch record yet) —
        // keep it separate from the Status column filters below so it doesn't get
        // merged into Assigned/Dispatched/Completed trip_status rows.
        dispatchTable.column(columnIndex).search('', true, false).draw();
        dispatchTable.column(7).search('Pending', true, false).draw();
        $('#clearFilterBtn').show();

        var info = dispatchTable.page.info();
        $('#recordCount').text(info.recordsDisplay + ' records');
    } else if (status === 'CONTAINER_RETURN') {
        // No dedicated status column for this — filter on the "Container Return" marker
        // badge rendered in the Dispatch column instead.
        dispatchTable.column(columnIndex).search('', true, false).draw();
        dispatchTable.column(7).search('Container Return', true, false).draw();
        $('#clearFilterBtn').show();

        var info = dispatchTable.page.info();
        $('#recordCount').text(info.recordsDisplay + ' records');
    } else {
        dispatchTable.column(7).search('', true, false);

        var labelMap = {
            'DISPATCHED' : 'Dispatched',
            'IN_TRANSIT' : 'In Transit',
            'DELIVERED'  : '^Delivered$',
            'COMPLETED'  : '^Completed$'
        };
        var searchTerm = labelMap[status] || status;

        dispatchTable.column(columnIndex).search(searchTerm, true, false).draw();
        $('#clearFilterBtn').show();

        var info = dispatchTable.page.info();
        $('#recordCount').text(info.recordsDisplay + ' records');
    }
}

$(document).on('keyup', '.dataTables_filter input', function() {
    if($(this).val() !== '') {
        $('.stat-card').removeClass('active');
        $('#clearFilterBtn').hide();
        currentFilter = 'all';
    }
});

$('#clearFilterBtn').on('click', function() {
    filterTable('all');
});
</script>

<script src="<?=base_url('assets/js/apps/fms/dispatch/dispatch.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>