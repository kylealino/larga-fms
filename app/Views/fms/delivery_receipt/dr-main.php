<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// 1. EXISTING DELIVERY RECEIPTS
// Latest POD only (prevents duplicates when a DR has multiple POD rows)
// ==============================
$drs = $this->db->query("
    SELECT d.*,
           t.trip_code,
           c.customer_name,
           dp.dispatch_code,
           pod.received_by,
           pod.date_received,
           pod.delivery_condition
    FROM tbl_delivery_receipts d
    LEFT JOIN tbl_trips t ON d.trip_id = t.trip_id
    LEFT JOIN tbl_customers c ON d.customer_id = c.customer_id
    LEFT JOIN tbl_dispatch dp ON d.dispatch_id = dp.dispatch_id
    LEFT JOIN tbl_delivery_receipt_pod pod ON pod.pod_id = (
        SELECT MAX(p2.pod_id) FROM tbl_delivery_receipt_pod p2 WHERE p2.dr_id = d.dr_id
    )
    ORDER BY d.dr_date DESC, d.dr_id DESC
")->getResultArray();

// ==============================
// 2. PENDING TRIPS (dispatched, no DR yet)
// ==============================
$pending_trips = $this->db->query("
    SELECT t.trip_id,
           t.trip_code,
           t.customer_id,
           t.destination,
           t.scheduled_date,
           t.trip_status,
           c.customer_name,
           d.dispatch_id,
           d.dispatch_date,
           d.dispatch_status,
           d.truck,
           d.driver,
           d.helper,
           d.origin AS dispatch_origin,
           d.destination AS dispatch_destination
    FROM tbl_trips t
    LEFT JOIN tbl_customers c ON t.customer_id = c.customer_id
    LEFT JOIN tbl_dispatch d ON t.trip_id = d.trip_id
    WHERE d.dispatch_id IS NOT NULL
      AND t.trip_status IN ('DISPATCHED','IN_TRANSIT','DELIVERED','COMPLETED')
      AND NOT EXISTS (
          SELECT 1 FROM tbl_delivery_receipts dr WHERE dr.dispatch_id = d.dispatch_id
      )
    ORDER BY d.dispatch_date DESC, t.trip_id DESC
")->getResultArray();

// ==============================
// 3. STATS
// ==============================
$total_drs = 0;
$total_delivered = 0;
$total_partial = 0;

foreach($drs as $row) {
    $total_drs++;
    if($row['dr_status'] == 'DELIVERED') $total_delivered++;
    elseif($row['dr_status'] == 'PARTIALLY_DELIVERED') $total_partial++;
}

$total_pending_dr = count($pending_trips);

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
        display: flex; align-items: center; gap: 16px; flex: 1; min-width: 200px;
    }
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

    .stat-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 16px; margin-bottom: 24px;
    }
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
    .form-check-input { width: 18px; height: 18px; margin-top: 8px; cursor: pointer; }

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
    .btn-warning:hover { background: #d97706; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(245,158,11,0.3); color: #ffffff; }
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
    .btn-icon-pod { color: var(--success); } .btn-icon-pod:hover { background: #d1fae5; border-color: #6ee7b7; }
    .btn-icon-dispatch { color: var(--success); } .btn-icon-dispatch:hover { background: #d1fae5; border-color: #6ee7b7; }
    .btn-icon-print { color: var(--primary); } .btn-icon-print:hover { background: var(--primary-light); border-color: var(--primary); }

    .btn-toolbar {
        padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;
        border: 1px solid var(--gray-200); background: #ffffff; color: var(--gray-600);
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
    }
    .btn-toolbar:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-toolbar-filter {
        background: var(--gray-50); border-color: var(--gray-200);
        color: var(--gray-600); font-size: 11px; padding: 3px 10px;
    }
    .btn-toolbar-filter.active { background: var(--primary); border-color: var(--primary); color: #ffffff; }

    .toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .toolbar-left { display: flex; align-items: center; gap: 8px; }
    .toolbar-right { display: flex; align-items: center; gap: 10px; }

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
        transition: all 0.2s;
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

    .trip-info-box {
        background: var(--gray-50); border-radius: 8px; padding: 16px;
        margin-bottom: 16px; border-left: 4px solid var(--primary);
    }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .section-header h6 { margin: 0; font-weight: 600; }
    .item-form { background: var(--gray-50); border-radius: 8px; padding: 12px; margin-bottom: 12px; border: 1px solid var(--gray-200); }

    /* Signature Pad */
    .sig-pad-wrapper {
        background: #ffffff;
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 8px;
        position: relative;
    }
    .sig-pad-wrapper.active { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,107,176,0.08); }
    #signaturePad {
        width: 100%;
        height: 180px;
        display: block;
        border-radius: 6px;
        background: #fdfdfd;
        cursor: crosshair;
        touch-action: none;
    }
    .sig-pad-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        font-size: 11px;
        color: var(--gray-500);
    }
    .sig-pad-clear {
        color: var(--danger);
        cursor: pointer;
        text-decoration: none;
    }
    .sig-pad-clear:hover { text-decoration: underline; }
    .sig-tabs {
        display: inline-flex;
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .sig-tab {
        padding: 6px 16px;
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-600);
        cursor: pointer;
        background: #ffffff;
        border: none;
    }
    .sig-tab.active {
        background: var(--primary);
        color: #ffffff;
    }

    /* Pending DR row styling */
    tr.pending-dr-row { background: #fff9ec !important; }
    tr.pending-dr-row:hover { background: #fff3d6 !important; }

    @media (max-width: 992px) {
        .lrg-module-header { flex-direction: column; align-items: stretch; padding: 16px 20px; }
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

<div class="me-dr-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="bi bi-receipt"></i>
        </div>
        <div class="module-info">
            <h4>
                Delivery Receipt
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
<!-- STATS CARDS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card active" data-filter="all" onclick="filterDRTable('all')">
        <div class="stat-left">
            <div class="stat-label">All DRs</div>
            <div class="stat-value"><?=$total_drs;?></div>
            <div class="stat-sub">All delivery receipts</div>
        </div>
        <div class="stat-right"><i class="bi bi-receipt"></i></div>
    </div>
    <div class="stat-card" data-filter="PENDING_DR" onclick="filterDRTable('PENDING_DR')">
        <div class="stat-left">
            <div class="stat-label">Pending DR</div>
            <div class="stat-value"><?=$total_pending_dr;?></div>
            <div class="stat-sub">Awaiting creation</div>
        </div>
        <div class="stat-right"><i class="bi bi-hourglass-split"></i></div>
    </div>
    <div class="stat-card" data-filter="DELIVERED" onclick="filterDRTable('DELIVERED')">
        <div class="stat-left">
            <div class="stat-label">Delivered</div>
            <div class="stat-value"><?=$total_delivered;?></div>
            <div class="stat-sub">Complete delivery</div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
    </div>
    <div class="stat-card" data-filter="PARTIALLY_DELIVERED" onclick="filterDRTable('PARTIALLY_DELIVERED')">
        <div class="stat-left">
            <div class="stat-label">Partial</div>
            <div class="stat-value"><?=$total_partial;?></div>
            <div class="stat-sub">Partial delivery</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-circle"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Delivery Receipts</h6>
                <div>
                    <span class="badge badge-secondary" id="drRecordCount"><?=count($drs);?> records</span>
                    <button class="btn-toolbar btn-toolbar-filter btn-sm ms-2" onclick="filterDRTable('all')" id="drClearFilterBtn" style="display:none;">
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
                            <i class="bi bi-info-circle"></i> Click <strong>+</strong> on a pending row to create its DR
                        </span>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="drTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="120">DR #</th>
                                <th width="120">Trip #</th>
                                <th>Customer</th>
                                <th>Truck</th>
                                <th>Driver</th>
                                <th width="100">Delivery Date</th>
                                <th width="120">Received By</th>
                                <th width="130">DR Status</th>
                                <th width="200" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($pending_trips) > 0): ?>
                                <?php foreach($pending_trips as $row): ?>
                                <tr class="pending-dr-row">
                                    <td><span class="badge badge-warning">No DR Yet</span></td>
                                    <td><span class="badge badge-primary"><?=$row['trip_code'];?></span></td>
                                    <td><?=$row['customer_name'] ?? '—';?></td>
                                    <td><?=$row['truck'] ?? '—';?></td>
                                    <td><?=$row['driver'] ?? '—';?></td>
                                    <td>—</td>
                                    <td>—</td>
                                    <td><span class="badge badge-warning">Pending DR</span></td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-dispatch" 
                                                    onclick="__DR.__openDRModalFromTrip(<?=$row['trip_id'];?>, <?=$row['dispatch_id'];?>)" 
                                                    title="Create Delivery Receipt">
                                                <i class="bi bi-plus-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if(count($drs) > 0): ?>
                                <?php foreach($drs as $row): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['dr_code'];?></span></td>
                                    <td><?=$row['trip_code'] ?? '—';?></td>
                                    <td><?=$row['customer_name'] ?? '—';?></td>
                                    <td><?=$row['truck'] ?? '—';?></td>
                                    <td><?=$row['driver'] ?? '—';?></td>
                                    <td><?=$row['dr_date'] ? date('M d, Y', strtotime($row['dr_date'])) : '—';?></td>
                                    <td><?=$row['received_by'] ?? '—';?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-secondary';
                                        $statusLabel = 'Pending';
                                        if($row['dr_status'] == 'PENDING') { $statusClass = 'badge-secondary'; $statusLabel = 'Pending'; }
                                        elseif($row['dr_status'] == 'IN_TRANSIT') { $statusClass = 'badge-info'; $statusLabel = 'In Transit'; }
                                        elseif($row['dr_status'] == 'ARRIVED') { $statusClass = 'badge-primary'; $statusLabel = 'Arrived'; }
                                        elseif($row['dr_status'] == 'DELIVERED') { $statusClass = 'badge-success'; $statusLabel = 'Delivered'; }
                                        elseif($row['dr_status'] == 'PARTIALLY_DELIVERED') { $statusClass = 'badge-warning'; $statusLabel = 'Partial'; }
                                        elseif($row['dr_status'] == 'FAILED_DELIVERY') { $statusClass = 'badge-danger'; $statusLabel = 'Failed'; }
                                        elseif($row['dr_status'] == 'CANCELLED') { $statusClass = 'badge-danger'; $statusLabel = 'Cancelled'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-pod" 
                                                    onclick="__DR.__openDRModal(<?=$row['dr_id'];?>)" 
                                                    title="Edit DR">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-view" 
                                                    onclick="__DR.__openPODModal(<?=$row['dr_id'];?>)" 
                                                    title="Proof of Delivery">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-print" 
                                                    onclick="__DR.__showPdfInModal('<?= base_url('fms-delivery-receipt?meaction=PRINT-DR&dr_id='.$row['dr_id']) ?>')" 
                                                    title="Print DR">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-delete" 
                                                    onclick="__DR.__deleteDR(<?=$row['dr_id'];?>)" 
                                                    title="Delete DR">
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

                <?php if(count($drs) == 0 && count($pending_trips) == 0): ?>
                <div class="empty-state">
                    <i class="bi bi-receipt"></i>
                    <h5>No delivery receipts</h5>
                    <p>Dispatch a trip first to enable DR creation.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- DR MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="drModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="drModalTitle">
                    <i class="bi bi-receipt me-2"></i>Delivery Receipt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="dr_id">
                <input type="hidden" id="dr_trip_id">
                <input type="hidden" id="dr_customer_id">
                <input type="hidden" id="dr_dispatch_id">

                <div class="trip-info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-receipt" style="font-size:24px;color:var(--primary);"></i>
                        <div>
                            <div class="text-muted small">Delivery Receipt</div>
                            <div class="fw-semibold" id="dr_code_display" style="font-size:15px;"></div>
                        </div>
                        <div class="ms-auto">
                            <span class="badge badge-primary">Trip #: <span id="dr_trip_code_display"></span></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3"><strong>Customer:</strong> <span id="dr_customer_display">—</span></div>
                        <div class="col-md-3"><strong>Truck:</strong> <span id="dr_truck_display">—</span></div>
                        <div class="col-md-3"><strong>Driver:</strong> <span id="dr_driver_display">—</span></div>
                        <div class="col-md-3"><strong>Helper:</strong> <span id="dr_helper_display">—</span></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>DR Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Delivery Date <span class="required">*</span></label>
                                <input type="date" class="form-control" id="dr_date">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Delivery Time</label>
                                <input type="time" class="form-control" id="dr_time">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">DR Status</label>
                                <select class="form-control" id="dr_status">
                                    <option value="PENDING">Pending</option>
                                    <option value="IN_TRANSIT">In Transit</option>
                                    <option value="ARRIVED">Arrived</option>
                                    <option value="DELIVERED">Delivered</option>
                                    <option value="PARTIALLY_DELIVERED">Partially Delivered</option>
                                    <option value="FAILED_DELIVERY">Failed Delivery</option>
                                    <option value="CANCELLED">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Truck</label>
                                <input type="text" class="form-control" id="dr_truck">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Driver</label>
                                <input type="text" class="form-control" id="dr_driver">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Helper</label>
                                <input type="text" class="form-control" id="dr_helper">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Origin</label>
                                <input type="text" class="form-control" id="dr_origin">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Destination</label>
                                <input type="text" class="form-control" id="dr_destination">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" id="dr_remarks" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Delivery Items</h6>
                    </div>
                    <div class="card-body">
                        <div class="item-form">
                            <input type="hidden" id="item_editing_id">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size:9px;">Item Description *</label>
                                    <input type="text" class="form-control" id="item_description">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label" style="font-size:9px;">Qty Disp</label>
                                    <input type="number" class="form-control" id="item_qty_dispatched" step="0.01">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label" style="font-size:9px;">Qty Del</label>
                                    <input type="number" class="form-control" id="item_qty_delivered" step="0.01">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label" style="font-size:9px;">Short</label>
                                    <input type="number" class="form-control" id="item_qty_shortage" step="0.01">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label" style="font-size:9px;">Damaged</label>
                                    <input type="number" class="form-control" id="item_qty_damaged" step="0.01">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label" style="font-size:9px;">Unit</label>
                                    <input type="text" class="form-control" id="item_unit">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label" style="font-size:9px;">Weight</label>
                                    <input type="number" class="form-control" id="item_weight" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;">Condition</label>
                                    <select class="form-control" id="item_condition">
                                        <option value="GOOD">Good</option>
                                        <option value="FAIR">Fair</option>
                                        <option value="POOR">Poor</option>
                                        <option value="DAMAGED">Damaged</option>
                                        <option value="SHORT">Short</option>
                                        <option value="REJECTED">Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;">Remarks</label>
                                    <input type="text" class="form-control" id="item_remarks">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" style="font-size:9px;">&nbsp;</label>
                                    <button class="btn btn-primary w-100" id="itemActionBtn" onclick="__DR.__saveDRItem()" style="font-size:12px;padding:6px 8px;white-space:nowrap;">
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
                                        <th>Item</th>
                                        <th width="80">Dispatched</th>
                                        <th width="80">Delivered</th>
                                        <th width="80">Shortage</th>
                                        <th width="80">Damaged</th>
                                        <th width="60">Unit</th>
                                        <th width="80">Weight</th>
                                        <th width="100">Condition</th>
                                        <th width="80" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="drItemsBody">
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">No items</td>
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
                <button type="button" class="btn btn-primary" id="drSubmitBtn" onclick="__DR.__saveDR()">
                    <i class="bi bi-save"></i> <span id="drBtnText">Save DR</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- POD MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="podModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-check me-2"></i>Proof of Delivery
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pod_dr_id">
                <input type="hidden" id="pod_id">

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Received By</label>
                        <input type="text" class="form-control" id="pod_received_by">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" id="pod_position">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Date Received</label>
                        <input type="date" class="form-control" id="pod_date">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Time Received</label>
                        <input type="time" class="form-control" id="pod_time">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Quantity Received</label>
                        <input type="text" class="form-control" id="pod_quantity">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Delivery Condition</label>
                        <select class="form-control" id="pod_condition">
                            <option value="GOOD">Good</option>
                            <option value="PARTIAL">Partial</option>
                            <option value="DAMAGED">Damaged</option>
                            <option value="REJECTED">Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Customer Signature</label>
                        <input type="hidden" id="pod_signature">

                        <div class="sig-tabs">
                            <button type="button" class="sig-tab active" id="sigTabDraw" onclick="__DR.__switchSigTab('draw')">
                                <i class="bi bi-pencil"></i> Draw Signature
                            </button>
                            <button type="button" class="sig-tab" id="sigTabUpload" onclick="__DR.__switchSigTab('upload')">
                                <i class="bi bi-upload"></i> Upload File
                            </button>
                        </div>

                        <div id="sigPaneDraw">
                            <div class="sig-pad-wrapper" id="sigPadWrapper">
                                <canvas id="signaturePad"></canvas>
                            </div>
                            <div class="sig-pad-actions">
                                <span><i class="bi bi-info-circle"></i> Sign above using mouse or touch</span>
                                <a href="javascript:void(0);" class="sig-pad-clear" onclick="__DR.__clearSignaturePad()">
                                    <i class="bi bi-eraser"></i> Clear
                                </a>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-primary btn-sm" onclick="__DR.__saveSignature()">
                                    <i class="bi bi-check"></i> Save Signature
                                </button>
                            </div>
                        </div>

                        <div id="sigPaneUpload" style="display:none;">
                            <input type="file" class="form-control" id="pod_signature_file" 
                                   accept="image/*,.pdf"
                                   onchange="__DR.__uploadPODFile('customer_signature')">
                        </div>

                        <div id="pod_signature_preview" class="mt-2" style="display:none;">
                            <img id="pod_signature_img" src="" style="max-height:80px;border-radius:6px;border:1px solid var(--gray-200);">
                            <div class="mt-1" style="font-size:11px;color:var(--gray-500);">
                                <i class="bi bi-check-circle" style="color:var(--success);"></i>
                                <span id="pod_signature_name"></span>
                                <a href="javascript:void(0);" onclick="__DR.__clearPODFile('customer_signature')" 
                                   style="color:var(--danger);margin-left:8px;">Remove</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Delivery Photo</label>
                        <input type="hidden" id="pod_photo">
                        <input type="file" class="form-control" id="pod_photo_file" 
                               accept="image/*"
                               onchange="__DR.__uploadPODFile('delivery_photo')">
                        <div id="pod_photo_preview" class="mt-2" style="display:none;">
                            <img id="pod_photo_img" src="" style="max-height:80px;border-radius:6px;border:1px solid var(--gray-200);">
                            <div class="mt-1" style="font-size:11px;color:var(--gray-500);">
                                <i class="bi bi-check-circle" style="color:var(--success);"></i>
                                <span id="pod_photo_name"></span>
                                <a href="javascript:void(0);" onclick="__DR.__clearPODFile('delivery_photo')" 
                                   style="color:var(--danger);margin-left:8px;">Remove</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Signed DR</label>
                        <input type="hidden" id="pod_signed_dr">
                        <input type="file" class="form-control" id="pod_signed_dr_file" 
                               accept="image/*,.pdf"
                               onchange="__DR.__uploadPODFile('signed_dr')">
                        <div id="pod_signed_dr_preview" class="mt-2" style="display:none;">
                            <img id="pod_signed_dr_img" src="" style="max-height:80px;border-radius:6px;border:1px solid var(--gray-200);">
                            <div class="mt-1" style="font-size:11px;color:var(--gray-500);">
                                <i class="bi bi-check-circle" style="color:var(--success);"></i>
                                <span id="pod_signed_dr_name"></span>
                                <a href="javascript:void(0);" onclick="__DR.__clearPODFile('signed_dr')" 
                                   style="color:var(--danger);margin-left:8px;">Remove</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Supporting Documents</label>
                        <input type="hidden" id="pod_supporting">
                        <input type="file" class="form-control" id="pod_supporting_file" 
                               accept="image/*,.pdf"
                               onchange="__DR.__uploadPODFile('supporting_documents')">
                        <div id="pod_supporting_preview" class="mt-2" style="display:none;">
                            <img id="pod_supporting_img" src="" style="max-height:80px;border-radius:6px;border:1px solid var(--gray-200);">
                            <div class="mt-1" style="font-size:11px;color:var(--gray-500);">
                                <i class="bi bi-check-circle" style="color:var(--success);"></i>
                                <span id="pod_supporting_name"></span>
                                <a href="javascript:void(0);" onclick="__DR.__clearPODFile('supporting_documents')" 
                                   style="color:var(--danger);margin-left:8px;">Remove</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">&nbsp;</label>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="pod_remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="podSubmitBtn" onclick="__DR.__savePOD()">
                    <i class="bi bi-save"></i> <span id="podBtnText">Save POD</span>
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
                <h5 class="modal-title">Delivery Receipt Preview</h5>
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
var drTable;
var currentDRFilter = 'all';

$(document).ready(function () {
    drTable = $('#drTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [],
        language: {
            search: "Search:",
            emptyTable: "No delivery receipts found"
        },
        columnDefs: [
            { orderable: false, targets: [8] }
        ]
    });
});

function filterDRTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    currentDRFilter = status;
    var columnIndex = 7;

    if (status === 'all') {
        drTable.column(columnIndex).search('', true, false).draw();
        $('#drClearFilterBtn').hide();
        $('#drRecordCount').text('<?=count($drs) + count($pending_trips);?> records');
    } else {
        var labelMap = {
            'PENDING_DR'             : 'Pending DR',
            'DELIVERED'              : '^Delivered$',
            'PARTIALLY_DELIVERED'    : 'Partial'
        };
        var searchTerm = labelMap[status] || status;

        drTable.column(columnIndex).search(searchTerm, true, false).draw();
        $('#drClearFilterBtn').show();

        var info = drTable.page.info();
        $('#drRecordCount').text(info.recordsDisplay + ' records');
    }
}

$(document).on('keyup', '.dataTables_filter input', function() {
    if($(this).val() !== '') {
        $('.stat-card').removeClass('active');
        $('#drClearFilterBtn').hide();
        currentDRFilter = 'all';
    }
});

$('#drClearFilterBtn').on('click', function() {
    filterDRTable('all');
});
</script>

<script src="<?=base_url('assets/js/apps/fms/delivery_receipt/dr.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>