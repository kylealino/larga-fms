<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH TIRES
// ==============================
$tires = $this->db->query("
    SELECT * FROM tbl_tires
    ORDER BY tire_id DESC
")->getResultArray();

// ==============================
// FETCH TRANSACTIONS
// ==============================
$transactions = $this->db->query("
    SELECT * FROM tbl_tire_transactions
    ORDER BY transaction_date DESC, transaction_id DESC
")->getResultArray();

// ==============================
// FETCH INSTALLATIONS
// ==============================
$installations = $this->db->query("
    SELECT i.*,
           t.plate_number AS truck_plate_live,
           t.make,
           t.model
    FROM tbl_tire_installations i
    LEFT JOIN tbl_trucks t ON i.truck_id = t.truck_id
    ORDER BY i.installation_date DESC, i.installation_id DESC
")->getResultArray();

// ==============================
// FETCH DISPOSALS
// ==============================
$disposals = $this->db->query("
    SELECT * FROM tbl_tire_disposals
    ORDER BY disposal_date DESC, disposal_id DESC
")->getResultArray();

// ==============================
// FETCH TRUCKS
// ==============================
$trucks = $this->db->query("
    SELECT truck_id, truck_code, plate_number, make, model, current_odometer
    FROM tbl_trucks
    WHERE truck_status NOT IN ('RETIRED','OUT OF SERVICE')
    ORDER BY plate_number
")->getResultArray();

// ==============================
// FETCH AVAILABLE TIRES (for install / dispose)
// ==============================
$available_tires = $this->db->query("
    SELECT tire_id, tire_code, serial_number, model, brand, size, tread_depth_mm, total_distance_km
    FROM tbl_tires
    WHERE tire_status IN ('IN_STOCK','REMOVED')
    ORDER BY tire_code
")->getResultArray();

// ==============================
// STATS
// ==============================
$total_tires = count($tires);
$stat_in_stock = 0;
$stat_installed = 0;
$stat_removed = 0;
$stat_for_disposal = 0;
$stat_disposed = 0;

foreach ($tires as $t) {
    if ($t['tire_status'] == 'IN_STOCK') $stat_in_stock++;
    elseif ($t['tire_status'] == 'INSTALLED') $stat_installed++;
    elseif ($t['tire_status'] == 'REMOVED') $stat_removed++;
    elseif ($t['tire_status'] == 'FOR_DISPOSAL') $stat_for_disposal++;
    elseif ($t['tire_status'] == 'DISPOSED') $stat_disposed++;
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
    .btn-icon-print { color: var(--primary); } .btn-icon-print:hover { background: var(--primary-light); border-color: var(--primary); }
    .btn-icon-time { color: #8b5cf6; } .btn-icon-time:hover { background: #ede9fe; border-color: #c4b5fd; }
    .btn-icon-dispose { color: var(--danger); } .btn-icon-dispose:hover { background: #fee2e2; border-color: #fca5a5; }

    .btn-toolbar {
        padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;
        border: 1px solid var(--gray-200); background: #ffffff; color: var(--gray-600);
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
    }
    .btn-toolbar:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-toolbar-primary { background: var(--primary); border-color: var(--primary); color: #ffffff; }
    .btn-toolbar-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); color: #ffffff; }
    .btn-toolbar-filter {
        background: var(--gray-50); border-color: var(--gray-200);
        color: var(--gray-600); font-size: 11px; padding: 3px 10px;
    }

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

    /* Journey timeline */
    .journey-timeline {
        position: relative;
        padding-left: 40px;
        margin-top: 12px;
    }
    .journey-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 8px;
        bottom: 8px;
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
        left: -32px;
        top: 20px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid var(--primary);
    }
    .journey-item.j-purchase::before { border-color: #8b5cf6; }
    .journey-item.j-inventory::before { border-color: var(--info); }
    .journey-item.j-install::before { border-color: var(--success); }
    .journey-item.j-remove::before { border-color: var(--warning); }
    .journey-item.j-dispose::before { border-color: var(--danger); }
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

<div class="me-tire-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="bi bi-circle-square"></i>
        </div>
        <div class="module-info">
            <h4>
                Tire Management
                <span class="module-badge">Operations</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="__Tire.__openTireModal()">
            <i class="bi bi-plus-circle"></i> New Tire
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card active" data-filter="all" onclick="filterTireTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Tires</div>
            <div class="stat-value"><?=$total_tires;?></div>
            <div class="stat-sub">All tires</div>
        </div>
        <div class="stat-right"><i class="bi bi-circle-square"></i></div>
    </div>
    <div class="stat-card" data-filter="IN_STOCK" onclick="filterTireTable('IN_STOCK')">
        <div class="stat-left">
            <div class="stat-label">In Stock</div>
            <div class="stat-value"><?=$stat_in_stock;?></div>
            <div class="stat-sub">Available inventory</div>
        </div>
        <div class="stat-right"><i class="bi bi-box-seam"></i></div>
    </div>
    <div class="stat-card" data-filter="INSTALLED" onclick="filterTireTable('INSTALLED')">
        <div class="stat-left">
            <div class="stat-label">Installed</div>
            <div class="stat-value"><?=$stat_installed;?></div>
            <div class="stat-sub">On vehicles</div>
        </div>
        <div class="stat-right"><i class="bi bi-truck"></i></div>
    </div>
    <div class="stat-card" data-filter="REMOVED" onclick="filterTireTable('REMOVED')">
        <div class="stat-left">
            <div class="stat-label">Removed</div>
            <div class="stat-value"><?=$stat_removed;?></div>
            <div class="stat-sub">Pulled from vehicles</div>
        </div>
        <div class="stat-right"><i class="bi bi-tools"></i></div>
    </div>
    <div class="stat-card" data-filter="DISPOSED" onclick="filterTireTable('DISPOSED')">
        <div class="stat-left">
            <div class="stat-label">Disposed</div>
            <div class="stat-value"><?=$stat_disposed;?></div>
            <div class="stat-sub">End of life</div>
        </div>
        <div class="stat-right"><i class="bi bi-trash"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- TABS -->
<!-- ============================================ -->
<div class="nav-tabs-modern">
    <button class="nav-tab-modern active" id="tabTires" onclick="switchTireTab('tires')">
        <i class="bi bi-circle-square"></i> Tire Master
    </button>
    <button class="nav-tab-modern" id="tabTransactions" onclick="switchTireTab('transactions')">
        <i class="bi bi-arrow-left-right"></i> Inventory In/Out
    </button>
    <button class="nav-tab-modern" id="tabInstallations" onclick="switchTireTab('installations')">
        <i class="bi bi-truck"></i> Installations
    </button>
    <button class="nav-tab-modern" id="tabDisposals" onclick="switchTireTab('disposals')">
        <i class="bi bi-trash"></i> Disposals
    </button>
</div>

<!-- ============================================ -->
<!-- TAB: TIRE MASTER -->
<!-- ============================================ -->
<div id="paneTires">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-circle-square me-2"></i>Tire Master</h6>
            <div>
                <span class="badge badge-secondary"><?=count($tires);?> records</span>
                <button class="btn-toolbar btn-toolbar-primary ms-2" onclick="__Tire.__openTireModal()">
                    <i class="bi bi-plus-circle"></i> New Tire
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="tireTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="130">Tire Code</th>
                            <th>Serial #</th>
                            <th>Brand / Model</th>
                            <th width="100">Size</th>
                            <th width="90">Tread</th>
                            <th width="110">Life (km)</th>
                            <th width="100">Price</th>
                            <th width="110">Total Km</th>
                            <th width="110">Status</th>
                            <th width="170" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($tires) > 0): ?>
                            <?php foreach($tires as $row): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['tire_code'];?></span></td>
                                <td><?=$row['serial_number'];?></td>
                                <td>
                                    <strong><?=$row['brand'] ?: '—';?></strong><br>
                                    <small class="text-muted"><?=$row['model'] ?: '—';?></small>
                                </td>
                                <td><?=$row['size'] ?: '—';?></td>
                                <td><?=number_format($row['tread_depth_mm'], 1);?> mm</td>
                                <td><?=number_format($row['life_expectancy_km'], 0);?></td>
                                <td>₱<?=number_format($row['price'], 2);?></td>
                                <td><?=number_format($row['total_distance_km'], 0);?> km</td>
                                <td>
                                    <?php
                                    $stClass = 'badge-secondary';
                                    $stLabel = $row['tire_status'];
                                    if($row['tire_status'] == 'IN_STOCK') { $stClass = 'badge-info'; $stLabel = 'In Stock'; }
                                    elseif($row['tire_status'] == 'INSTALLED') { $stClass = 'badge-success'; $stLabel = 'Installed'; }
                                    elseif($row['tire_status'] == 'REMOVED') { $stClass = 'badge-warning'; $stLabel = 'Removed'; }
                                    elseif($row['tire_status'] == 'FOR_DISPOSAL') { $stClass = 'badge-warning'; $stLabel = 'For Disposal'; }
                                    elseif($row['tire_status'] == 'DISPOSED') { $stClass = 'badge-danger'; $stLabel = 'Disposed'; }
                                    ?>
                                    <span class="badge <?=$stClass;?>"><?=$stLabel;?></span>
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button class="btn-icon btn-icon-time" 
                                                onclick="__Tire.__openJourneyModal(<?=$row['tire_id'];?>, '<?=addslashes($row['tire_code']);?>')" 
                                                title="Tire Journey">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                        <?php if($row['tire_status'] == 'IN_STOCK' || $row['tire_status'] == 'REMOVED'): ?>
                                        <button class="btn-icon btn-icon-dispatch" 
                                                onclick="__Tire.__openInstallModal(<?=$row['tire_id'];?>)" 
                                                title="Install on Truck">
                                            <i class="bi bi-truck"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if($row['tire_status'] != 'DISPOSED'): ?>
                                        <button class="btn-icon btn-icon-dispose" 
                                                onclick="__Tire.__openDisposeModal(<?=$row['tire_id'];?>)" 
                                                title="Dispose Tire">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn-icon btn-icon-edit" 
                                                onclick="__Tire.__openTireModal(<?=$row['tire_id'];?>)" 
                                                title="Edit Tire">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-delete" 
                                                onclick="__Tire.__deleteTire(<?=$row['tire_id'];?>)" 
                                                title="Delete Tire">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(count($tires) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-circle-square"></i>
                <h5>No tires registered</h5>
                <p>Click <strong>New Tire</strong> to register your first tire.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TAB: TRANSACTIONS -->
<!-- ============================================ -->
<div id="paneTransactions" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-arrow-left-right me-2"></i>Tire Inventory In/Out</h6>
            <div>
                <span class="badge badge-secondary"><?=count($transactions);?> records</span>
                <button class="btn-toolbar ms-2" style="background:#10b981;border-color:#10b981;color:#fff;" onclick="__Tire.__openTransactionModal('IN')">
                    <i class="bi bi-arrow-down-circle"></i> Inventory In
                </button>
                <button class="btn-toolbar ms-2" style="background:#f59e0b;border-color:#f59e0b;color:#fff;" onclick="__Tire.__openTransactionModal('OUT')">
                    <i class="bi bi-arrow-up-circle"></i> Inventory Out
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="transactionTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="130">Code</th>
                            <th width="90">Type</th>
                            <th width="100">Date</th>
                            <th width="120">Tire</th>
                            <th width="60">Qty</th>
                            <th width="100">Price</th>
                            <th>Vendor / Purpose</th>
                            <th width="100">Reference</th>
                            <th width="80" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($transactions) > 0): ?>
                            <?php foreach($transactions as $row): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['transaction_code'];?></span></td>
                                <td>
                                    <?php if($row['transaction_type'] == 'IN'): ?>
                                        <span class="badge badge-success">IN</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">OUT</span>
                                    <?php endif; ?>
                                </td>
                                <td><?=$row['transaction_date'] ? date('M d, Y', strtotime($row['transaction_date'])) : '—';?></td>
                                <td><strong><?=$row['tire_code'];?></strong></td>
                                <td><?=number_format($row['quantity'], 0);?></td>
                                <td>₱<?=number_format($row['price'], 2);?></td>
                                <td>
                                    <?php
                                    if($row['transaction_type'] == 'IN') {
                                        echo $row['supplier_vendor'] ?: '—';
                                    } else {
                                        echo $row['purpose'] ?: '—';
                                        if($row['truck_plate']) echo '<br><small class="text-muted">→ ' . $row['truck_plate'] . '</small>';
                                    }
                                    ?>
                                </td>
                                <td><?=$row['reference_number'] ?: '—';?></td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button class="btn-icon btn-icon-delete" 
                                                onclick="__Tire.__deleteTransaction(<?=$row['transaction_id'];?>)" 
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

            <?php if(count($transactions) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-arrow-left-right"></i>
                <h5>No transactions yet</h5>
                <p>Use <strong>Inventory In</strong> to add tires or <strong>Inventory Out</strong> to release them.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TAB: INSTALLATIONS -->
<!-- ============================================ -->
<div id="paneInstallations" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-truck me-2"></i>Tire Installations</h6>
            <div>
                <span class="badge badge-secondary"><?=count($installations);?> records</span>
                <button class="btn-toolbar btn-toolbar-primary ms-2" onclick="__Tire.__openInstallModal()">
                    <i class="bi bi-plus-circle"></i> Install Tire
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="installationTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="130">Code</th>
                            <th width="120">Tire</th>
                            <th>Truck</th>
                            <th width="100">Position</th>
                            <th width="100">Install Date</th>
                            <th width="100">Odometer</th>
                            <th width="100">Status</th>
                            <th width="100">Removed</th>
                            <th width="100">Distance</th>
                            <th width="130" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($installations) > 0): ?>
                            <?php foreach($installations as $row): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['installation_code'];?></span></td>
                                <td><strong><?=$row['tire_code'];?></strong></td>
                                <td>
                                    <strong><?=$row['truck_plate_live'] ?? $row['truck_plate'];?></strong><br>
                                    <small class="text-muted"><?=($row['make'] ?? '') . ' ' . ($row['model'] ?? '');?></small>
                                </td>
                                <td><?=$row['position'] ?: '—';?></td>
                                <td><?=$row['installation_date'] ? date('M d, Y', strtotime($row['installation_date'])) : '—';?></td>
                                <td><?=number_format($row['installation_odometer'], 0);?> km</td>
                                <td>
                                    <?php if($row['status'] == 'ACTIVE'): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Removed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?=$row['removed_date'] ? date('M d, Y', strtotime($row['removed_date'])) : '—';?></td>
                                <td><?=number_format($row['distance_used_km'], 0);?> km</td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <?php if($row['status'] == 'ACTIVE'): ?>
                                        <button class="btn-icon btn-icon-dispatch" 
                                                onclick="__Tire.__openRemoveModal(<?=$row['installation_id'];?>, <?=$row['installation_odometer'];?>)" 
                                                title="Remove Tire">
                                            <i class="bi bi-arrow-up-circle"></i>
                                        </button>
                                        <?php else: ?>
                                        <button class="btn-icon btn-icon-view" 
                                                onclick="__Tire.__viewRemoval(<?=$row['installation_id'];?>)" 
                                                title="View Removal Details">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn-icon btn-icon-delete" 
                                                onclick="__Tire.__deleteInstallation(<?=$row['installation_id'];?>)" 
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

            <?php if(count($installations) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-truck"></i>
                <h5>No installations yet</h5>
                <p>Click <strong>Install Tire</strong> to mount a tire on a truck.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TAB: DISPOSALS -->
<!-- ============================================ -->
<div id="paneDisposals" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-trash me-2"></i>Tire Disposals</h6>
            <div>
                <span class="badge badge-secondary"><?=count($disposals);?> records</span>
                <button class="btn-toolbar btn-toolbar-primary ms-2" onclick="__Tire.__openDisposeModal()">
                    <i class="bi bi-plus-circle"></i> Dispose Tire
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="disposalTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="130">Code</th>
                            <th width="120">Tire</th>
                            <th width="100">Date</th>
                            <th>Reason</th>
                            <th width="100">Final Km</th>
                            <th width="120">Final Status</th>
                            <th width="80" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($disposals) > 0): ?>
                            <?php foreach($disposals as $row): ?>
                            <tr>
                                <td><span class="badge badge-danger"><?=$row['disposal_code'];?></span></td>
                                <td><strong><?=$row['tire_code'];?></strong></td>
                                <td><?=$row['disposal_date'] ? date('M d, Y', strtotime($row['disposal_date'])) : '—';?></td>
                                <td><?=$row['disposal_reason'] ?: '—';?></td>
                                <td><?=number_format($row['final_mileage'], 0);?> km</td>
                                <td><span class="badge badge-danger"><?=str_replace('_', ' ', $row['final_status']);?></span></td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button class="btn-icon btn-icon-delete" 
                                                onclick="__Tire.__deleteDisposal(<?=$row['disposal_id'];?>)" 
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

            <?php if(count($disposals) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-trash"></i>
                <h5>No disposals yet</h5>
                <p>Click <strong>Dispose Tire</strong> to record an end-of-life tire.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TIRE MODAL (Create/Edit)
<!-- ============================================ -->
<div class="modal fade" id="tireModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tireModalTitle">
                    <i class="bi bi-plus-circle me-2"></i>New Tire
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tire_id">

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Serial Number <span class="required">*</span></label>
                        <input type="text" class="form-control" id="tire_serial">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" id="tire_brand">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Model</label>
                        <input type="text" class="form-control" id="tire_model">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Size</label>
                        <input type="text" class="form-control" id="tire_size" placeholder="e.g., 11R22.5">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="form-label">Tread Depth (mm)</label>
                        <input type="number" class="form-control" id="tire_tread" step="0.01">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Life Expectancy (km)</label>
                        <input type="number" class="form-control" id="tire_life" step="0.01">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" id="tire_price" step="0.01">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Manufacture Date</label>
                        <input type="date" class="form-control" id="tire_mfg_date">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="tire_supplier">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="tire_status">
                            <option value="IN_STOCK">In Stock</option>
                            <option value="INSTALLED">Installed</option>
                            <option value="REMOVED">Removed</option>
                            <option value="FOR_DISPOSAL">For Disposal</option>
                            <option value="DISPOSED">Disposed</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="tire_remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="tireSubmitBtn" onclick="__Tire.__saveTire()">
                    <i class="bi bi-save"></i> <span id="tireBtnText">Save Tire</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TRANSACTION MODAL (IN / OUT)
<!-- ============================================ -->
<div class="modal fade" id="transactionModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalTitle">
                    <i class="bi bi-arrow-down-circle me-2"></i>Inventory In
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txn_type">

                <div class="trip-info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-info-circle" style="font-size:24px;color:var(--primary);"></i>
                        <div id="txn_info_text" style="font-size:13px;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Transaction Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="txn_date">
                    </div>
                    <div class="col-md-8 mb-2">
                        <label class="form-label">Tire <span class="required">*</span></label>
                        <select class="form-control" id="txn_tire_id" onchange="__Tire.__onTxnTireChange(this)">
                            <option value="">— Select Tire —</option>
                            <?php foreach($tires as $t): ?>
                            <option value="<?=$t['tire_id'];?>" 
                                    data-code="<?=$t['tire_code'];?>" 
                                    data-status="<?=$t['tire_status'];?>"
                                    data-price="<?=$t['price'];?>">
                                <?=$t['tire_code'];?> — <?=$t['serial_number'];?> (<?=$t['brand'] ?: '—';?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="txn_tire_code">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Quantity <span class="required">*</span></label>
                        <input type="number" class="form-control" id="txn_quantity" value="1" step="0.01">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" id="txn_price" step="0.01">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Reference No.</label>
                        <input type="text" class="form-control" id="txn_ref" placeholder="Invoice/PO #">
                    </div>

                    <!-- IN-specific -->
                    <div class="col-md-6 mb-2 txn-in-field" style="display:none;">
                        <label class="form-label">Supplier / Vendor</label>
                        <input type="text" class="form-control" id="txn_vendor">
                    </div>

                    <!-- OUT-specific -->
                    <div class="col-md-6 mb-2 txn-out-field" style="display:none;">
                        <label class="form-label">Purpose</label>
                        <input type="text" class="form-control" id="txn_purpose" placeholder="Installation, Replacement, etc.">
                    </div>
                    <div class="col-md-6 mb-2 txn-out-field" style="display:none;">
                        <label class="form-label">Truck / Vehicle</label>
                        <select class="form-control" id="txn_truck_id" onchange="__Tire.__onTxnTruckChange(this)">
                            <option value="">— Select Truck —</option>
                            <?php foreach($trucks as $t): ?>
                            <option value="<?=$t['truck_id'];?>" data-plate="<?=$t['plate_number'];?>">
                                <?=$t['plate_number'];?> (<?=$t['make'] . ' ' . $t['model'];?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="txn_truck_plate">
                    </div>
                    <div class="col-md-6 mb-2 txn-out-field" style="display:none;">
                        <label class="form-label">Released By</label>
                        <input type="text" class="form-control" id="txn_released_by">
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="txn_remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="txnSubmitBtn" onclick="__Tire.__saveTransaction()">
                    <i class="bi bi-save"></i> <span id="txnBtnText">Save Transaction</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- INSTALL TIRE MODAL
<!-- ============================================ -->
<div class="modal fade" id="installModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-truck me-2"></i>Install Tire on Truck
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Tire <span class="required">*</span></label>
                        <select class="form-control" id="install_tire_id" onchange="__Tire.__onInstallTireChange(this)">
                            <option value="">— Select Tire —</option>
                            <?php foreach($available_tires as $t): ?>
                            <option value="<?=$t['tire_id'];?>" data-code="<?=$t['tire_code'];?>">
                                <?=$t['tire_code'];?> — <?=$t['serial_number'];?> (<?=$t['brand'] ?: '—';?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="install_tire_code">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Truck <span class="required">*</span></label>
                        <select class="form-control" id="install_truck_id" onchange="__Tire.__onInstallTruckChange(this)">
                            <option value="">— Select Truck —</option>
                            <?php foreach($trucks as $t): ?>
                            <option value="<?=$t['truck_id'];?>" 
                                    data-plate="<?=$t['plate_number'];?>"
                                    data-odo="<?=$t['current_odometer'];?>">
                                <?=$t['plate_number'];?> (<?=$t['make'] . ' ' . $t['model'];?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="install_truck_plate">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" id="install_position" placeholder="e.g., Front Left, Rear Right">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Install Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="install_date">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Odometer (km) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="install_odometer" step="0.01">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Installed By</label>
                        <input type="text" class="form-control" id="install_by" placeholder="Technician name">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Remarks</label>
                        <input type="text" class="form-control" id="install_remarks">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="installSubmitBtn" onclick="__Tire.__saveInstallation()">
                    <i class="bi bi-check-circle"></i> Install Tire
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- REMOVE TIRE MODAL
<!-- ============================================ -->
<div class="modal fade" id="removeModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-up-circle me-2"></i>Remove Tire from Truck
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="remove_installation_id">
                <input type="hidden" id="remove_install_odometer">

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Removal Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="remove_date">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Removal Odometer (km) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="remove_odometer" step="0.01" oninput="__Tire.__calcDistance()">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Distance Used (km)</label>
                        <input type="number" class="form-control" id="remove_distance" readonly>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Reason for Removal</label>
                        <input type="text" class="form-control" id="remove_reason" placeholder="Worn out, Damaged, etc.">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Tire Condition</label>
                        <input type="text" class="form-control" id="remove_condition" placeholder="Good, Fair, Poor">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Removed By</label>
                        <input type="text" class="form-control" id="remove_by">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Remarks</label>
                        <input type="text" class="form-control" id="remove_remarks">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-warning" id="removeSubmitBtn" onclick="__Tire.__saveRemoval()">
                    <i class="bi bi-check-circle"></i> Confirm Removal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- DISPOSE TIRE MODAL
<!-- ============================================ -->
<div class="modal fade" id="disposeModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-trash me-2"></i>Dispose Tire
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Tire <span class="required">*</span></label>
                        <select class="form-control" id="dispose_tire_id" onchange="__Tire.__onDisposeTireChange(this)">
                            <option value="">— Select Tire —</option>
                            <?php foreach($available_tires as $t): ?>
                            <option value="<?=$t['tire_id'];?>" 
                                    data-code="<?=$t['tire_code'];?>" 
                                    data-km="<?=$t['total_distance_km'];?>">
                                <?=$t['tire_code'];?> — <?=$t['serial_number'];?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="dispose_tire_code">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Disposal Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="dispose_date">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Disposal Reason</label>
                        <input type="text" class="form-control" id="dispose_reason" placeholder="Worn out, Beyond repair">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Final Mileage (km)</label>
                        <input type="number" class="form-control" id="dispose_final_km" step="0.01">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Final Status</label>
                        <select class="form-control" id="dispose_final_status">
                            <option value="DISPOSED">Disposed</option>
                            <option value="SOLD_SCRAP">Sold as Scrap</option>
                            <option value="RETURNED_VENDOR">Returned to Vendor</option>
                            <option value="WRITTEN_OFF">Written Off</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="alert alert-warning mb-0" style="padding:8px 12px;font-size:12px;">
                            <i class="bi bi-exclamation-triangle"></i> This action will mark the tire as <strong>Disposed</strong>.
                        </div>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Disposal Details</label>
                        <textarea class="form-control" id="dispose_details" rows="2"></textarea>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Remarks</label>
                        <input type="text" class="form-control" id="dispose_remarks">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-danger" id="disposeSubmitBtn" onclick="__Tire.__saveDisposal()">
                    <i class="bi bi-trash"></i> Dispose Tire
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TIRE JOURNEY MODAL
<!-- ============================================ -->
<div class="modal fade" id="journeyModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>Tire Journey — <span id="journey_tire_code"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="journey_tire_id">
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
<!-- SCRIPTS
<!-- ============================================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
var tireTable, transactionTable, installationTable, disposalTable;

$(document).ready(function () {
    tireTable = $('#tireTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'desc']],
        language: { search: "Search:", emptyTable: "No tires found" },
        columnDefs: [{ orderable: false, targets: [9] }]
    });

    transactionTable = $('#transactionTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'desc']],
        language: { search: "Search:", emptyTable: "No transactions found" },
        columnDefs: [{ orderable: false, targets: [8] }]
    });

    installationTable = $('#installationTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'desc']],
        language: { search: "Search:", emptyTable: "No installations found" },
        columnDefs: [{ orderable: false, targets: [9] }]
    });

    disposalTable = $('#disposalTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'desc']],
        language: { search: "Search:", emptyTable: "No disposals found" },
        columnDefs: [{ orderable: false, targets: [6] }]
    });
});

function switchTireTab(tab) {
    $('.nav-tab-modern').removeClass('active');
    $('#paneTires, #paneTransactions, #paneInstallations, #paneDisposals').hide();

    if (tab === 'tires') {
        $('#tabTires').addClass('active');
        $('#paneTires').show();
    } else if (tab === 'transactions') {
        $('#tabTransactions').addClass('active');
        $('#paneTransactions').show();
    } else if (tab === 'installations') {
        $('#tabInstallations').addClass('active');
        $('#paneInstallations').show();
    } else if (tab === 'disposals') {
        $('#tabDisposals').addClass('active');
        $('#paneDisposals').show();
    }
}

function filterTireTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    switchTireTab('tires');

    var columnIndex = 8;
    if (status === 'all') {
        tireTable.column(columnIndex).search('', true, false).draw();
    } else {
        var labelMap = {
            'IN_STOCK'  : 'In Stock',
            'INSTALLED' : 'Installed',
            'REMOVED'   : 'Removed',
            'DISPOSED'  : 'Disposed'
        };
        var searchTerm = labelMap[status] || status;
        tireTable.column(columnIndex).search(searchTerm, true, false).draw();
    }
}
</script>

<script src="<?=base_url('assets/js/apps/fms/tire/tire.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>