<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH TOOLS
// ==============================
$tools = $this->db->query("
    SELECT * FROM tbl_tools
    ORDER BY tool_name ASC
")->getResultArray();

// ==============================
// FETCH ISSUANCES
// ==============================
$issuances = $this->db->query("
    SELECT * FROM tbl_tool_issuances
    ORDER BY issue_date DESC, issuance_id DESC
")->getResultArray();

// ==============================
// FETCH TRUCKS
// ==============================
$trucks = $this->db->query("
    SELECT truck_id, truck_code, plate_number, make, model
    FROM tbl_trucks
    WHERE truck_status NOT IN ('RETIRED','OUT OF SERVICE')
    ORDER BY plate_number
")->getResultArray();

// ==============================
// FETCH AVAILABLE TOOLS (for issuance)
// ==============================
$available_tools = $this->db->query("
    SELECT tool_id, tool_code, tool_name, category, brand, model, serial_number, 
           tool_condition, quantity, quantity_on_hand
    FROM tbl_tools
    WHERE availability IN ('AVAILABLE','ASSIGNED')
      AND quantity_on_hand > 0
    ORDER BY tool_name
")->getResultArray();

// ==============================
// FETCH OPEN ASSIGNEES (grouped by tool_id)
// ==============================
$open_assignees_raw = $this->db->query("
    SELECT ti.tool_id, ti.issued_to, ti.quantity_pending, ti.truck_plate
    FROM tbl_tool_issuances ti
    WHERE ti.quantity_pending > 0
    ORDER BY ti.issued_to ASC
")->getResultArray();

$open_assignees = [];
foreach ($open_assignees_raw as $r) {
    $tid = $r['tool_id'];
    if (!isset($open_assignees[$tid])) $open_assignees[$tid] = [];
    $open_assignees[$tid][] = [
        'name'     => $r['issued_to'],
        'quantity' => intval($r['quantity_pending']),
        'truck'    => $r['truck_plate'],
    ];
}

// ==============================
// STATS
// ==============================
$total_tools = 0;
$total_units = 0;
$stat_available = 0;
$stat_assigned = 0;
$stat_under_repair = 0;
$stat_damaged = 0;
$stat_lost = 0;
$stat_retired = 0;
$total_value = 0;

foreach ($tools as $t) {
    $total_tools++;
    $total_units += intval($t['quantity'] ?? 1);
    $total_value += floatval($t['purchase_cost']) * intval($t['quantity'] ?? 1);

    if ($t['availability'] == 'AVAILABLE') $stat_available++;
    elseif ($t['availability'] == 'ASSIGNED') $stat_assigned++;
    elseif ($t['availability'] == 'UNDER_REPAIR') $stat_under_repair++;
    elseif ($t['availability'] == 'DAMAGED') $stat_damaged++;
    elseif ($t['availability'] == 'LOST') $stat_lost++;
    elseif ($t['availability'] == 'RETIRED') $stat_retired++;
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
    .btn-danger {
        background: var(--danger); border: none; border-radius: 8px;
        padding: 9px 20px; font-size: 13px; font-weight: 600;
        transition: all 0.2s; color: #ffffff;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-danger:hover { background: var(--danger-dark); transform: translateY(-1px); color: #ffffff; }
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
    .btn-icon-dispatch { color: var(--success); } .btn-icon-dispatch:hover { background: #d1fae5; border-color: #6ee7b7; }
    .btn-icon-time { color: #8b5cf6; } .btn-icon-time:hover { background: #ede9fe; border-color: #c4b5fd; }
    .btn-icon-return { color: var(--info); } .btn-icon-return:hover { background: #dbeafe; border-color: #93c5fd; }

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
        border-bottom: 1px solid var(--gray-200); padding-bottom: 2px; flex-wrap: wrap;
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

    .trip-info-box {
        background: var(--gray-50); border-radius: 8px; padding: 16px;
        margin-bottom: 16px; border-left: 4px solid var(--primary);
    }

    .qty-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 12px;
        font-size: 11px; font-weight: 600;
        background: var(--primary-light); color: var(--primary-dark);
    }
    .qty-pill.zero { background: #fee2e2; color: var(--danger); }
    .qty-pill.low { background: #fef3c7; color: #92400e; }

    /* Assignee list inside cell */
    .assignee-list { display: flex; flex-direction: column; gap: 3px; }
    .assignee-item {
        display: flex; align-items: center; gap: 4px;
        font-size: 11px; line-height: 1.2;
    }
    .assignee-item .person-icon {
        color: var(--primary); font-size: 10px;
    }
    .assignee-item .person-name {
        font-weight: 600; color: var(--gray-800);
    }
    .assignee-item .qty-badge {
        background: var(--warning); color: #ffffff;
        font-size: 9px; font-weight: 700;
        padding: 1px 6px; border-radius: 8px;
    }
    .assignee-item .truck-tag {
        color: var(--gray-500); font-size: 9px;
        margin-left: 14px;
    }

    /* Journey timeline */
    .journey-timeline {
        position: relative;
        padding-left: 40px;
        margin-top: 12px;
    }
    .journey-timeline::before {
        content: '';
        position: absolute;
        left: 15px; top: 8px; bottom: 8px;
        width: 2px;
        background: var(--gray-200);
    }
    .journey-item {
        position: relative;
        padding: 12px 16px;
        background: #ffffff;
        border: 1px solid var(--gray-200);
        border-radius: 10px;
        margin-bottom: 12px;
        box-shadow: var(--shadow);
    }
    .journey-item::before {
        content: '';
        position: absolute;
        left: -32px; top: 20px;
        width: 14px; height: 14px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid var(--primary);
    }
    .journey-item.j-registered::before { border-color: #8b5cf6; }
    .journey-item.j-issued::before { border-color: var(--warning); }
    .journey-item.j-returned::before { border-color: var(--success); }
    .journey-item.j-overdue::before { border-color: var(--danger); }
    .journey-item .j-header {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;
    }
    .journey-item .j-type {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: var(--gray-500);
    }
    .journey-item .j-date { font-size: 11px; color: var(--gray-400); }
    .journey-item .j-description { font-size: 13px; font-weight: 600; color: var(--gray-800); }
    .journey-item .j-details { font-size: 11px; color: var(--gray-500); margin-top: 4px; }

    @media (max-width: 992px) {
        .lrg-module-header { flex-direction: column; align-items: stretch; padding: 16px 20px; }
        .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    }
    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .nav-tabs-modern { flex-wrap: wrap; }
    }
</style>

<div class="me-tool-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="bi bi-wrench"></i>
        </div>
        <div class="module-info">
            <h4>
                Tools Management
                <span class="module-badge">Operations</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="__Tool.__openToolModal()">
            <i class="bi bi-plus-circle"></i> New Tool
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card active" data-filter="all" onclick="filterToolTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Tools</div>
            <div class="stat-value"><?=$total_tools;?></div>
            <div class="stat-sub"><?=$total_units;?> total units</div>
        </div>
        <div class="stat-right"><i class="bi bi-wrench"></i></div>
    </div>
    <div class="stat-card" data-filter="AVAILABLE" onclick="filterToolTable('AVAILABLE')">
        <div class="stat-left">
            <div class="stat-label">Available</div>
            <div class="stat-value"><?=$stat_available;?></div>
            <div class="stat-sub">Ready for issue</div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
    </div>
    <div class="stat-card" data-filter="ASSIGNED" onclick="filterToolTable('ASSIGNED')">
        <div class="stat-left">
            <div class="stat-label">Assigned</div>
            <div class="stat-value"><?=$stat_assigned;?></div>
            <div class="stat-sub">Currently issued</div>
        </div>
        <div class="stat-right"><i class="bi bi-person-check"></i></div>
    </div>
    <div class="stat-card" data-filter="DAMAGED" onclick="filterToolTable('DAMAGED')">
        <div class="stat-left">
            <div class="stat-label">Damaged</div>
            <div class="stat-value"><?=$stat_damaged;?></div>
            <div class="stat-sub">Needs attention</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-triangle"></i></div>
    </div>
    <div class="stat-card" data-filter="RETIRED" onclick="filterToolTable('RETIRED')">
        <div class="stat-left">
            <div class="stat-label">Retired / Lost</div>
            <div class="stat-value"><?=($stat_retired + $stat_lost);?></div>
            <div class="stat-sub">Not in circulation</div>
        </div>
        <div class="stat-right"><i class="bi bi-archive"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- TABS -->
<!-- ============================================ -->
<div class="nav-tabs-modern">
    <button class="nav-tab-modern active" id="tabTools" onclick="switchToolTab('tools')">
        <i class="bi bi-wrench"></i> Tools Master
    </button>
    <button class="nav-tab-modern" id="tabIssuances" onclick="switchToolTab('issuances')">
        <i class="bi bi-arrow-left-right"></i> Tool Issuances
    </button>
</div>

<!-- ============================================ -->
<!-- TAB: TOOLS MASTER -->
<!-- ============================================ -->
<div id="paneTools">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-wrench me-2"></i>Tools Master</h6>
            <div>
                <span class="badge badge-secondary"><?=count($tools);?> records</span>
                <button class="btn-toolbar btn-toolbar-primary ms-2" onclick="__Tool.__openToolModal()">
                    <i class="bi bi-plus-circle"></i> New Tool
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="toolTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="120">Code</th>
                            <th>Tool Name</th>
                            <th width="110">Category</th>
                            <th width="120">Brand / Model</th>
                            <th width="120">Serial #</th>
                            <th width="110">On Hand / Total</th>
                            <th width="90">Condition</th>
                            <th width="110">Availability</th>
                            <th width="180">Assigned To</th>
                            <th width="180" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($tools) > 0): ?>
                            <?php foreach($tools as $row): 
                                $onHand = intval($row['quantity_on_hand'] ?? 0);
                                $totalQty = intval($row['quantity'] ?? 1);
                                $qtyClass = '';
                                if ($onHand <= 0) $qtyClass = 'zero';
                                elseif ($onHand <= 2) $qtyClass = 'low';

                                $assignees = $open_assignees[$row['tool_id']] ?? [];
                            ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['tool_code'];?></span></td>
                                <td>
                                    <strong><?=$row['tool_name'];?></strong>
                                    <?php if($row['purchase_cost'] > 0): ?>
                                    <br><small class="text-muted">₱<?=number_format($row['purchase_cost'], 2);?> / unit</small>
                                    <?php endif; ?>
                                </td>
                                <td><?=$row['category'] ?: '—';?></td>
                                <td>
                                    <strong><?=$row['brand'] ?: '—';?></strong><br>
                                    <small class="text-muted"><?=$row['model'] ?: '—';?></small>
                                </td>
                                <td><?=$row['serial_number'] ?: '—';?></td>
                                <td>
                                    <span class="qty-pill <?=$qtyClass;?>">
                                        <i class="bi bi-box-seam"></i> <?=$onHand;?> / <?=$totalQty;?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $condClass = 'badge-info';
                                    if($row['tool_condition'] == 'NEW') $condClass = 'badge-success';
                                    elseif($row['tool_condition'] == 'GOOD') $condClass = 'badge-info';
                                    elseif($row['tool_condition'] == 'FAIR') $condClass = 'badge-warning';
                                    elseif($row['tool_condition'] == 'POOR') $condClass = 'badge-warning';
                                    elseif($row['tool_condition'] == 'DAMAGED') $condClass = 'badge-danger';
                                    ?>
                                    <span class="badge <?=$condClass;?>"><?=$row['tool_condition'];?></span>
                                </td>
                                <td>
                                    <?php
                                    $avClass = 'badge-secondary';
                                    $avLabel = $row['availability'];
                                    if($row['availability'] == 'AVAILABLE') { $avClass = 'badge-success'; $avLabel = 'Available'; }
                                    elseif($row['availability'] == 'ASSIGNED') { $avClass = 'badge-warning'; $avLabel = 'Assigned'; }
                                    elseif($row['availability'] == 'UNDER_REPAIR') { $avClass = 'badge-info'; $avLabel = 'Under Repair'; }
                                    elseif($row['availability'] == 'DAMAGED') { $avClass = 'badge-danger'; $avLabel = 'Damaged'; }
                                    elseif($row['availability'] == 'LOST') { $avClass = 'badge-danger'; $avLabel = 'Lost'; }
                                    elseif($row['availability'] == 'RETIRED') { $avClass = 'badge-secondary'; $avLabel = 'Retired'; }
                                    ?>
                                    <span class="badge <?=$avClass;?>"><?=$avLabel;?></span>
                                </td>
                                <td>
                                    <?php if (count($assignees) > 0): ?>
                                        <div class="assignee-list">
                                            <?php foreach($assignees as $a): ?>
                                            <div class="assignee-item">
                                                <i class="bi bi-person-fill person-icon"></i>
                                                <span class="person-name"><?=htmlspecialchars($a['name']);?></span>
                                                <span class="qty-badge"><?=$a['quantity'];?></span>
                                            </div>
                                            <?php if($a['truck']): ?>
                                            <div class="truck-tag"><i class="bi bi-truck"></i> <?=htmlspecialchars($a['truck']);?></div>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button class="btn-icon btn-icon-time" 
                                                onclick="__Tool.__openJourneyModal(<?=$row['tool_id'];?>, '<?=addslashes($row['tool_name']);?>')" 
                                                title="Tool Journey">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                        <?php if($onHand > 0 && $row['availability'] != 'RETIRED' && $row['availability'] != 'LOST'): ?>
                                        <button class="btn-icon btn-icon-dispatch" 
                                                onclick="__Tool.__openIssueModal(<?=$row['tool_id'];?>)" 
                                                title="Issue Tool">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn-icon btn-icon-edit" 
                                                onclick="__Tool.__openToolModal(<?=$row['tool_id'];?>)" 
                                                title="Edit Tool">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-delete" 
                                                onclick="__Tool.__deleteTool(<?=$row['tool_id'];?>)" 
                                                title="Delete Tool">
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

            <?php if(count($tools) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-wrench"></i>
                <h5>No tools registered</h5>
                <p>Click <strong>New Tool</strong> to register your first tool.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TAB: ISSUANCES -->
<!-- ============================================ -->
<div id="paneIssuances" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-arrow-left-right me-2"></i>Tool Issuances</h6>
            <div>
                <span class="badge badge-secondary"><?=count($issuances);?> records</span>
                <button class="btn-toolbar btn-toolbar-primary ms-2" onclick="__Tool.__openIssueModal()">
                    <i class="bi bi-box-arrow-right"></i> Issue Tool
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="issuanceTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="130">Code</th>
                            <th width="180">Tool</th>
                            <th>Issued To</th>
                            <th width="100">Issue Date</th>
                            <th width="100">Expected</th>
                            <th width="100">Returned</th>
                            <th width="90">Condition</th>
                            <th width="110">Status</th>
                            <th width="140" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($issuances) > 0): ?>
                            <?php foreach($issuances as $row): 
                                $pending = intval($row['quantity_pending'] ?? 0);
                                $isOverdue = false;
                                if ($pending > 0 && $row['expected_return'] && strtotime($row['expected_return']) < time()) {
                                    $isOverdue = true;
                                }
                            ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['issuance_code'];?></span></td>
                                <td>
                                    <strong><?=$row['tool_name'];?></strong><br>
                                    <small class="text-muted"><?=$row['tool_code'];?></small><br>
                                    <small style="font-size:10px;color:var(--primary);font-weight:600;">
                                        <?=intval($row['quantity_issued']);?> issued
                                        <?php if(intval($row['quantity_returned']) > 0): ?>
                                        · <?=intval($row['quantity_returned']);?> returned
                                        <?php endif; ?>
                                        <?php if($pending > 0): ?>
                                        · <?=$pending;?> pending
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?=$row['issued_to'];?></strong>
                                    <?php if($row['truck_plate']): ?>
                                    <br><small class="text-muted"><i class="bi bi-truck"></i> <?=$row['truck_plate'];?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?=$row['issue_date'] ? date('M d, Y', strtotime($row['issue_date'])) : '—';?></td>
                                <td><?=$row['expected_return'] ? date('M d, Y', strtotime($row['expected_return'])) : '—';?></td>
                                <td><?=$row['actual_return'] ? date('M d, Y', strtotime($row['actual_return'])) : '—';?></td>
                                <td>
                                    <?php
                                    $condClass = 'badge-info';
                                    $cond = $row['condition_after'] ?: $row['condition_before'];
                                    if($cond == 'NEW') $condClass = 'badge-success';
                                    elseif($cond == 'GOOD') $condClass = 'badge-info';
                                    elseif($cond == 'FAIR') $condClass = 'badge-warning';
                                    elseif($cond == 'POOR') $condClass = 'badge-warning';
                                    elseif($cond == 'DAMAGED') $condClass = 'badge-danger';
                                    ?>
                                    <span class="badge <?=$condClass;?>"><?=$cond;?></span>
                                </td>
                                <td>
                                    <?php if($isOverdue): ?>
                                        <span class="badge badge-danger">Overdue</span>
                                    <?php elseif($pending > 0): ?>
                                        <span class="badge badge-warning"><?=$pending;?> Out</span>
                                    <?php elseif($row['issuance_status'] == 'RETURNED'): ?>
                                        <span class="badge badge-success">Returned</span>
                                    <?php elseif($row['issuance_status'] == 'LOST'): ?>
                                        <span class="badge badge-danger">Lost</span>
                                    <?php elseif($row['issuance_status'] == 'DAMAGED'): ?>
                                        <span class="badge badge-danger">Damaged</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?=$row['issuance_status'];?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <?php if($pending > 0): ?>
                                        <button class="btn-icon btn-icon-return" 
                                                onclick="__Tool.__openReturnModal(<?=$row['issuance_id'];?>)" 
                                                title="Return Tool">
                                            <i class="bi bi-box-arrow-in-left"></i>
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn-icon btn-icon-delete" 
                                                onclick="__Tool.__deleteIssuance(<?=$row['issuance_id'];?>)" 
                                                title="Delete">
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

            <?php if(count($issuances) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-arrow-left-right"></i>
                <h5>No issuances yet</h5>
                <p>Click <strong>Issue Tool</strong> to start tracking tool loans.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TOOL MODAL (Create/Edit) -->
<!-- ============================================ -->
<div class="modal fade" id="toolModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toolModalTitle">
                    <i class="bi bi-plus-circle me-2"></i>New Tool
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tool_id">

                <div class="row">
                    <div class="col-md-8 mb-2">
                        <label class="form-label">Tool Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="tool_name" placeholder="e.g., Hydraulic Jack">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" id="tool_category" placeholder="e.g., Jacks, Power Tools">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" id="tool_brand">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Model</label>
                        <input type="text" class="form-control" id="tool_model">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="tool_serial">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" class="form-control" id="tool_purchase_date">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Purchase Cost (₱)</label>
                        <input type="number" class="form-control" id="tool_purchase_cost" step="0.01">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Total Quantity <span class="required">*</span></label>
                        <input type="number" class="form-control" id="tool_quantity" value="1" min="1">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">On Hand</label>
                        <input type="number" class="form-control" id="tool_quantity_display" readonly>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Current Location</label>
                        <input type="text" class="form-control" id="tool_location" placeholder="e.g., Warehouse A - Bay 1">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Condition</label>
                        <select class="form-control" id="tool_condition">
                            <option value="NEW">New</option>
                            <option value="GOOD" selected>Good</option>
                            <option value="FAIR">Fair</option>
                            <option value="POOR">Poor</option>
                            <option value="DAMAGED">Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Availability</label>
                        <select class="form-control" id="tool_availability">
                            <option value="AVAILABLE" selected>Available</option>
                            <option value="ASSIGNED">Assigned</option>
                            <option value="UNDER_REPAIR">Under Repair</option>
                            <option value="DAMAGED">Damaged</option>
                            <option value="LOST">Lost</option>
                            <option value="RETIRED">Retired</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Assigned To (optional)</label>
                        <input type="text" class="form-control" id="tool_assigned_to" placeholder="Person name">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Truck / Vehicle (optional)</label>
                        <select class="form-control" id="tool_truck_id" onchange="__Tool.__onToolTruckChange(this)">
                            <option value="">— Select Truck —</option>
                            <?php foreach($trucks as $t): ?>
                            <option value="<?=$t['truck_id'];?>" data-plate="<?=$t['plate_number'];?>">
                                <?=$t['plate_number'];?> (<?=$t['make'] . ' ' . $t['model'];?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="tool_truck_plate">
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="tool_remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="toolSubmitBtn" onclick="__Tool.__saveTool()">
                    <i class="bi bi-save"></i> <span id="toolBtnText">Save Tool</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ISSUE TOOL MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="issueModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-box-arrow-right me-2"></i>Issue Tool
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Tool <span class="required">*</span></label>
                        <select class="form-control" id="issue_tool_id" onchange="__Tool.__onIssueToolChange(this)">
                            <option value="">— Select Tool —</option>
                            <?php foreach($available_tools as $t): ?>
                            <option value="<?=$t['tool_id'];?>" 
                                    data-code="<?=$t['tool_code'];?>" 
                                    data-name="<?=$t['tool_name'];?>"
                                    data-condition="<?=$t['tool_condition'];?>"
                                    data-onhand="<?=$t['quantity_on_hand'];?>">
                                <?=$t['tool_code'];?> — <?=$t['tool_name'];?> (<?=intval($t['quantity_on_hand']);?> on hand)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="issue_tool_code">
                        <input type="hidden" id="issue_tool_name">
                        <input type="hidden" id="issue_max_qty">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Issued To <span class="required">*</span></label>
                        <input type="text" class="form-control" id="issue_issued_to" placeholder="Person name">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Qty <span class="required">*</span></label>
                        <input type="number" class="form-control" id="issue_quantity" value="1" min="1">
                    </div>

                    <div class="col-md-12 mb-2">
                        <div id="issue_stock_display" style="display:none;padding:8px 14px;background:var(--primary-light);border-radius:8px;font-size:12px;">
                            <i class="bi bi-box-seam"></i> Available on hand: <strong id="issue_onhand_text"></strong>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Issued To Type</label>
                        <select class="form-control" id="issue_issued_to_type">
                            <option value="">— Select —</option>
                            <option value="STAFF">Staff</option>
                            <option value="DRIVER">Driver</option>
                            <option value="MECHANIC">Mechanic</option>
                            <option value="CONTRACTOR">Contractor</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Issue Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="issue_date">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Issue Time</label>
                        <input type="time" class="form-control" id="issue_time">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Expected Return</label>
                        <input type="date" class="form-control" id="issue_expected_return">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Condition Before</label>
                        <select class="form-control" id="issue_condition_before">
                            <option value="NEW">New</option>
                            <option value="GOOD" selected>Good</option>
                            <option value="FAIR">Fair</option>
                            <option value="POOR">Poor</option>
                            <option value="DAMAGED">Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Reference No.</label>
                        <input type="text" class="form-control" id="issue_reference" placeholder="WO # / Ticket #">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Purpose</label>
                        <input type="text" class="form-control" id="issue_purpose" placeholder="e.g., Tire replacement on ABC-1234">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Truck / Vehicle (optional)</label>
                        <select class="form-control" id="issue_truck_id" onchange="__Tool.__onIssueTruckChange(this)">
                            <option value="">— Select Truck —</option>
                            <?php foreach($trucks as $t): ?>
                            <option value="<?=$t['truck_id'];?>" data-plate="<?=$t['plate_number'];?>">
                                <?=$t['plate_number'];?> (<?=$t['make'] . ' ' . $t['model'];?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="issue_truck_plate">
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="issue_remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="issueSubmitBtn" onclick="__Tool.__saveIssuance()">
                    <i class="bi bi-box-arrow-right"></i> Issue Tool
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- RETURN TOOL MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="returnModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-box-arrow-in-left me-2"></i>Return Tool
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="return_issuance_id">

                <div class="trip-info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-info-circle" style="font-size:24px;color:var(--primary);"></i>
                        <div id="return_info_text" style="font-size:13px;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Qty to Return <span class="required">*</span></label>
                        <input type="number" class="form-control" id="return_quantity" value="1" min="1">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Return Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="return_date">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Return Time</label>
                        <input type="time" class="form-control" id="return_time">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Condition After <span class="required">*</span></label>
                        <select class="form-control" id="return_condition">
                            <option value="NEW">New</option>
                            <option value="GOOD" selected>Good</option>
                            <option value="FAIR">Fair</option>
                            <option value="POOR">Poor</option>
                            <option value="DAMAGED">Damaged</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Returned By</label>
                        <input type="text" class="form-control" id="return_returned_by">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Received By</label>
                        <input type="text" class="form-control" id="return_received_by">
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Return Remarks</label>
                        <textarea class="form-control" id="return_remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="returnSubmitBtn" onclick="__Tool.__saveReturn()">
                    <i class="bi bi-check-circle"></i> Confirm Return
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TOOL JOURNEY MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="journeyModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>Tool Journey — <span id="journey_tool_name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="journey_tool_id">
                <div id="journeyContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
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
<!-- SCRIPTS -->
<!-- ============================================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
var toolTable, issuanceTable;

$(document).ready(function () {
    toolTable = $('#toolTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'asc']],
        language: { search: "Search:", emptyTable: "No tools found" },
        columnDefs: [{ orderable: false, targets: [9] }]
    });

    issuanceTable = $('#issuanceTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'desc']],
        language: { search: "Search:", emptyTable: "No issuances found" },
        columnDefs: [{ orderable: false, targets: [8] }]
    });
});

function switchToolTab(tab) {
    $('.nav-tab-modern').removeClass('active');
    $('#paneTools, #paneIssuances').hide();

    if (tab === 'tools') {
        $('#tabTools').addClass('active');
        $('#paneTools').show();
    } else if (tab === 'issuances') {
        $('#tabIssuances').addClass('active');
        $('#paneIssuances').show();
    }
}

function filterToolTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    switchToolTab('tools');

    var columnIndex = 7;
    if (status === 'all') {
        toolTable.column(columnIndex).search('', true, false).draw();
    } else if (status === 'RETIRED') {
        toolTable.column(columnIndex).search('Retired|Lost', true, false).draw();
    } else {
        var labelMap = {
            'AVAILABLE'    : 'Available',
            'ASSIGNED'     : 'Assigned',
            'DAMAGED'      : 'Damaged'
        };
        var searchTerm = labelMap[status] || status;
        toolTable.column(columnIndex).search(searchTerm, true, false).draw();
    }
}
</script>

<script src="<?=base_url('assets/js/apps/fms/tool/tool.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>