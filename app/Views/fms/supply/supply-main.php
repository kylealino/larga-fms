<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH SUPPLIES
// ==============================
$supplies = $this->db->query("
    SELECT * FROM tbl_supplies
    ORDER BY supply_name ASC
")->getResultArray();

// ==============================
// FETCH TRANSACTIONS
// ==============================
$transactions = $this->db->query("
    SELECT * FROM tbl_supply_transactions
    ORDER BY transaction_date DESC, transaction_id DESC
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
// STATS
// ==============================
$total_supplies = count($supplies);
$stat_in_stock = 0;
$stat_low_stock = 0;
$stat_out_of_stock = 0;
$stat_discontinued = 0;
$total_value = 0;

foreach ($supplies as $s) {
    if ($s['status'] == 'IN_STOCK') $stat_in_stock++;
    elseif ($s['status'] == 'LOW_STOCK') $stat_low_stock++;
    elseif ($s['status'] == 'OUT_OF_STOCK') $stat_out_of_stock++;
    elseif ($s['status'] == 'DISCONTINUED') $stat_discontinued++;
    $total_value += floatval($s['current_stock']) * floatval($s['unit_cost']);
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
    .btn-icon-stock { color: var(--success); } .btn-icon-stock:hover { background: #d1fae5; border-color: #6ee7b7; }
    .btn-icon-out { color: var(--warning); } .btn-icon-out:hover { background: #fef3c7; border-color: #fcd34d; }

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

    /* Stock level indicator */
    .stock-bar {
        height: 6px; background: var(--gray-200); border-radius: 3px;
        overflow: hidden; margin-top: 4px; width: 100%; max-width: 120px;
    }
    .stock-bar-inner {
        height: 100%;
        background: var(--success);
        border-radius: 3px;
        transition: width 0.3s ease;
    }
    .stock-bar-inner.warning { background: var(--warning); }
    .stock-bar-inner.danger { background: var(--danger); }

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
    .journey-item.j-in::before { border-color: var(--success); }
    .journey-item.j-out::before { border-color: var(--warning); }
    .journey-item.j-return::before { border-color: var(--info); }
    .journey-item.j-adjust::before { border-color: #6366f1; }
    .journey-item.j-damaged::before { border-color: var(--danger); }
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

<div class="me-sup-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="bi bi-box"></i>
        </div>
        <div class="module-info">
            <h4>
                Supplies Management
                <span class="module-badge">Operations</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="__Sup.__openSupplyModal()">
            <i class="bi bi-plus-circle"></i> New Supply
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card active" data-filter="all" onclick="filterSupTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Supplies</div>
            <div class="stat-value"><?=$total_supplies;?></div>
            <div class="stat-sub">All items</div>
        </div>
        <div class="stat-right"><i class="bi bi-box"></i></div>
    </div>
    <div class="stat-card" data-filter="IN_STOCK" onclick="filterSupTable('IN_STOCK')">
        <div class="stat-left">
            <div class="stat-label">In Stock</div>
            <div class="stat-value"><?=$stat_in_stock;?></div>
            <div class="stat-sub">Healthy levels</div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
    </div>
    <div class="stat-card" data-filter="LOW_STOCK" onclick="filterSupTable('LOW_STOCK')">
        <div class="stat-left">
            <div class="stat-label">Low Stock</div>
            <div class="stat-value"><?=$stat_low_stock;?></div>
            <div class="stat-sub">Needs reorder</div>
        </div>
        <div class="stat-right"><i class="bi bi-exclamation-triangle"></i></div>
    </div>
    <div class="stat-card" data-filter="OUT_OF_STOCK" onclick="filterSupTable('OUT_OF_STOCK')">
        <div class="stat-left">
            <div class="stat-label">Out of Stock</div>
            <div class="stat-value"><?=$stat_out_of_stock;?></div>
            <div class="stat-sub">Empty inventory</div>
        </div>
        <div class="stat-right"><i class="bi bi-x-circle"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-left">
            <div class="stat-label">Total Value</div>
            <div class="stat-value">₱<?=number_format($total_value, 0);?></div>
            <div class="stat-sub">Inventory worth</div>
        </div>
        <div class="stat-right"><i class="bi bi-cash-coin"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- TABS -->
<!-- ============================================ -->
<div class="nav-tabs-modern">
    <button class="nav-tab-modern active" id="tabSupplies" onclick="switchSupTab('supplies')">
        <i class="bi bi-box"></i> Supplies Master
    </button>
    <button class="nav-tab-modern" id="tabTransactions" onclick="switchSupTab('transactions')">
        <i class="bi bi-arrow-left-right"></i> Transactions
    </button>
</div>

<!-- ============================================ -->
<!-- TAB: SUPPLIES MASTER -->
<!-- ============================================ -->
<div id="paneSupplies">
    <div class="card">
        <div class="card-header">
            <h6><i class="bi bi-box me-2"></i>Supplies Master</h6>
            <div>
                <span class="badge badge-secondary"><?=count($supplies);?> records</span>
                <button class="btn-toolbar btn-toolbar-primary ms-2" onclick="__Sup.__openSupplyModal()">
                    <i class="bi bi-plus-circle"></i> New Supply
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="supplyTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="130">Code</th>
                            <th>Name</th>
                            <th width="110">Category</th>
                            <th width="70">Unit</th>
                            <th width="110">Current Stock</th>
                            <th width="90">Min Stock</th>
                            <th width="110">Unit Cost</th>
                            <th width="100">Total Value</th>
                            <th width="110">Status</th>
                            <th width="180" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($supplies) > 0): ?>
                            <?php foreach($supplies as $row): 
                                $current = floatval($row['current_stock']);
                                $minimum = floatval($row['minimum_stock']);
                                $pct = ($minimum > 0) ? min(100, ($current / ($minimum * 3)) * 100) : 100;
                                $barClass = '';
                                if ($row['status'] == 'LOW_STOCK') $barClass = 'warning';
                                elseif ($row['status'] == 'OUT_OF_STOCK') $barClass = 'danger';
                                $totalVal = $current * floatval($row['unit_cost']);
                            ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['supply_code'];?></span></td>
                                <td>
                                    <strong><?=$row['supply_name'];?></strong>
                                    <?php if($row['storage_location']): ?>
                                    <br><small class="text-muted"><i class="bi bi-geo-alt"></i> <?=$row['storage_location'];?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?=$row['category'] ?: '—';?></td>
                                <td><?=$row['unit'] ?: '—';?></td>
                                <td>
                                    <strong><?=number_format($current, 2);?></strong>
                                    <div class="stock-bar">
                                        <div class="stock-bar-inner <?=$barClass;?>" style="width:<?=$pct;?>%;"></div>
                                    </div>
                                </td>
                                <td><?=number_format($minimum, 2);?></td>
                                <td>₱<?=number_format($row['unit_cost'], 2);?></td>
                                <td><strong>₱<?=number_format($totalVal, 2);?></strong></td>
                                <td>
                                    <?php
                                    $stClass = 'badge-secondary';
                                    $stLabel = $row['status'];
                                    if($row['status'] == 'IN_STOCK') { $stClass = 'badge-success'; $stLabel = 'In Stock'; }
                                    elseif($row['status'] == 'LOW_STOCK') { $stClass = 'badge-warning'; $stLabel = 'Low Stock'; }
                                    elseif($row['status'] == 'OUT_OF_STOCK') { $stClass = 'badge-danger'; $stLabel = 'Out of Stock'; }
                                    elseif($row['status'] == 'DISCONTINUED') { $stClass = 'badge-secondary'; $stLabel = 'Discontinued'; }
                                    ?>
                                    <span class="badge <?=$stClass;?>"><?=$stLabel;?></span>
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button class="btn-icon btn-icon-time" 
                                                onclick="__Sup.__openJourneyModal(<?=$row['supply_id'];?>, '<?=addslashes($row['supply_name']);?>')" 
                                                title="Supply Journey">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-stock" 
                                                onclick="__Sup.__openTransactionModal('STOCK_IN', <?=$row['supply_id'];?>)" 
                                                title="Stock In">
                                            <i class="bi bi-arrow-down-circle"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-out" 
                                                onclick="__Sup.__openTransactionModal('STOCK_OUT', <?=$row['supply_id'];?>)" 
                                                title="Stock Out">
                                            <i class="bi bi-arrow-up-circle"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-edit" 
                                                onclick="__Sup.__openSupplyModal(<?=$row['supply_id'];?>)" 
                                                title="Edit Supply">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-icon btn-icon-delete" 
                                                onclick="__Sup.__deleteSupply(<?=$row['supply_id'];?>)" 
                                                title="Delete Supply">
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

            <?php if(count($supplies) == 0): ?>
            <div class="empty-state">
                <i class="bi bi-box"></i>
                <h5>No supplies registered</h5>
                <p>Click <strong>New Supply</strong> to add your first consumable item.</p>
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
            <h6><i class="bi bi-arrow-left-right me-2"></i>Supply Transactions</h6>
            <div>
                <span class="badge badge-secondary"><?=count($transactions);?> records</span>
                <button class="btn-toolbar ms-2" style="background:#10b981;border-color:#10b981;color:#fff;" onclick="__Sup.__openTransactionModal('STOCK_IN')">
                    <i class="bi bi-arrow-down-circle"></i> Stock In
                </button>
                <button class="btn-toolbar ms-2" style="background:#f59e0b;border-color:#f59e0b;color:#fff;" onclick="__Sup.__openTransactionModal('STOCK_OUT')">
                    <i class="bi bi-arrow-up-circle"></i> Stock Out
                </button>
                <button class="btn-toolbar ms-2" onclick="__Sup.__openTransactionModal('ADJUSTMENT')">
                    <i class="bi bi-sliders"></i> Adjust
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table id="transactionTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="130">Code</th>
                            <th width="100">Type</th>
                            <th width="100">Date</th>
                            <th width="120">Supply</th>
                            <th width="90">Qty</th>
                            <th width="90">Stock</th>
                            <th>Reference / Purpose</th>
                            <th width="120">Truck</th>
                            <th width="80" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($transactions) > 0): ?>
                            <?php foreach($transactions as $row): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?=$row['transaction_code'];?></span></td>
                                <td>
                                    <?php
                                    $typeClass = 'badge-secondary';
                                    $typeLabel = $row['transaction_type'];
                                    if($row['transaction_type'] == 'STOCK_IN') { $typeClass = 'badge-success'; $typeLabel = 'Stock In'; }
                                    elseif($row['transaction_type'] == 'STOCK_OUT') { $typeClass = 'badge-warning'; $typeLabel = 'Stock Out'; }
                                    elseif($row['transaction_type'] == 'RETURN') { $typeClass = 'badge-info'; $typeLabel = 'Return'; }
                                    elseif($row['transaction_type'] == 'ADJUSTMENT') { $typeClass = 'badge-primary'; $typeLabel = 'Adjustment'; }
                                    elseif($row['transaction_type'] == 'DAMAGED') { $typeClass = 'badge-danger'; $typeLabel = 'Damaged'; }
                                    elseif($row['transaction_type'] == 'DISPOSAL') { $typeClass = 'badge-danger'; $typeLabel = 'Disposal'; }
                                    ?>
                                    <span class="badge <?=$typeClass;?>"><?=$typeLabel;?></span>
                                </td>
                                <td><?=$row['transaction_date'] ? date('M d, Y', strtotime($row['transaction_date'])) : '—';?></td>
                                <td>
                                    <strong><?=$row['supply_name'];?></strong><br>
                                    <small class="text-muted"><?=$row['supply_code'];?></small>
                                </td>
                                <td>
                                    <?php
                                    $sign = '';
                                    if($row['transaction_type'] == 'STOCK_IN' || $row['transaction_type'] == 'RETURN') $sign = '+';
                                    elseif($row['transaction_type'] == 'STOCK_OUT' || $row['transaction_type'] == 'DAMAGED' || $row['transaction_type'] == 'DISPOSAL') $sign = '−';
                                    ?>
                                    <strong><?=$sign . number_format($row['quantity'], 2);?></strong> <?=$row['unit'];?>
                                </td>
                                <td>
                                    <small><?=number_format($row['previous_stock'], 2);?></small>
                                    <i class="bi bi-arrow-right"></i>
                                    <strong><?=number_format($row['new_stock'], 2);?></strong>
                                </td>
                                <td>
                                    <?=$row['reference_number'] ?: '—';?>
                                    <?php if($row['purpose']): ?><br><small class="text-muted"><?=$row['purpose'];?></small><?php endif; ?>
                                    <?php if($row['remarks']): ?><br><small class="text-muted"><i><?=$row['remarks'];?></i></small><?php endif; ?>
                                </td>
                                <td><?=$row['truck_plate'] ?: '—';?></td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button class="btn-icon btn-icon-delete" 
                                                onclick="__Sup.__deleteTransaction(<?=$row['transaction_id'];?>)" 
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
                <p>Click <strong>Stock In</strong> to add supplies or <strong>Stock Out</strong> to release them.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SUPPLY MODAL (Create/Edit) -->
<!-- ============================================ -->
<div class="modal fade" id="supplyModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplyModalTitle">
                    <i class="bi bi-plus-circle me-2"></i>New Supply
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sup_id">

                <div class="row">
                    <div class="col-md-8 mb-2">
                        <label class="form-label">Supply Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="sup_name" placeholder="e.g., Engine Oil 15W-40">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" id="sup_category" placeholder="e.g., Oil, Filters, Grease">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="form-label">Unit <span class="required">*</span></label>
                        <input type="text" class="form-control" id="sup_unit" placeholder="Liters, Pcs, Kg">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Unit Cost (₱)</label>
                        <input type="number" class="form-control" id="sup_cost" step="0.01">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Current Stock</label>
                        <input type="number" class="form-control" id="sup_stock" step="0.01" value="0">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Minimum Stock</label>
                        <input type="number" class="form-control" id="sup_min" step="0.01" value="0">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Reorder Level</label>
                        <input type="number" class="form-control" id="sup_reorder" step="0.01" value="0">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="sup_supplier">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="form-label">Storage Location</label>
                        <input type="text" class="form-control" id="sup_location" placeholder="e.g., Warehouse A - Shelf 3">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Status</label>
                        <input type="text" class="form-control" id="sup_status_display" readonly placeholder="Auto-computed">
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="sup_remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="supSubmitBtn" onclick="__Sup.__saveSupply()">
                    <i class="bi bi-save"></i> <span id="supBtnText">Save Supply</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TRANSACTION MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="transactionModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalTitle">
                    <i class="bi bi-arrow-down-circle me-2"></i>Stock In
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
                        <label class="form-label">Supply <span class="required">*</span></label>
                        <select class="form-control" id="txn_supply_id" onchange="__Sup.__onTxnSupplyChange(this)">
                            <option value="">— Select Supply —</option>
                            <?php foreach($supplies as $s): ?>
                            <option value="<?=$s['supply_id'];?>" 
                                    data-code="<?=$s['supply_code'];?>" 
                                    data-name="<?=$s['supply_name'];?>"
                                    data-unit="<?=$s['unit'];?>"
                                    data-cost="<?=$s['unit_cost'];?>"
                                    data-stock="<?=$s['current_stock'];?>">
                                <?=$s['supply_code'];?> — <?=$s['supply_name'];?> (<?=$s['current_stock'] . ' ' . $s['unit'];?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Stock info box -->
                    <div class="col-md-12 mb-2">
                        <div id="txn_stock_display" style="display:none;padding:10px 14px;background:var(--primary-light);border-radius:8px;font-size:12px;">
                            <strong>Current Stock:</strong> <span id="txn_stock_current"></span>
                            <span style="margin-left:16px;"><strong>Unit:</strong> <span id="txn_stock_unit"></span></span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label" id="txn_qty_label">Quantity <span class="required">*</span></label>
                        <input type="number" class="form-control" id="txn_quantity" step="0.01" value="0">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Unit Cost (₱)</label>
                        <input type="number" class="form-control" id="txn_cost" step="0.01">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Reference No.</label>
                        <input type="text" class="form-control" id="txn_ref" placeholder="Invoice/PO/MNT #">
                    </div>

                    <!-- IN-specific -->
                    <div class="col-md-6 mb-2 txn-in-field" style="display:none;">
                        <label class="form-label">Supplier / Vendor</label>
                        <input type="text" class="form-control" id="txn_vendor">
                    </div>

                    <!-- OUT-specific -->
                    <div class="col-md-6 mb-2 txn-out-field" style="display:none;">
                        <label class="form-label">Issued To</label>
                        <input type="text" class="form-control" id="txn_issued_to">
                    </div>
                    <div class="col-md-6 mb-2 txn-out-field" style="display:none;">
                        <label class="form-label">Truck / Vehicle</label>
                        <select class="form-control" id="txn_truck_id" onchange="__Sup.__onTxnTruckChange(this)">
                            <option value="">— Select Truck (optional) —</option>
                            <?php foreach($trucks as $t): ?>
                            <option value="<?=$t['truck_id'];?>" data-plate="<?=$t['plate_number'];?>">
                                <?=$t['plate_number'];?> (<?=$t['make'] . ' ' . $t['model'];?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="txn_truck_plate">
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Purpose / Reference Module</label>
                        <input type="text" class="form-control" id="txn_purpose" placeholder="e.g., Maintenance MNT-001">
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
                <button type="button" class="btn btn-primary" id="txnSubmitBtn" onclick="__Sup.__saveTransaction()">
                    <i class="bi bi-save"></i> <span id="txnBtnText">Save Transaction</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SUPPLY JOURNEY MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="journeyModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>Supply Journey — <span id="journey_supply_name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="journey_supply_id">
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
var supplyTable, transactionTable;

$(document).ready(function () {
    supplyTable = $('#supplyTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'asc']],
        language: { search: "Search:", emptyTable: "No supplies found" },
        columnDefs: [{ orderable: false, targets: [9] }]
    });

    transactionTable = $('#transactionTable').DataTable({
        pageLength: 10, lengthChange: false, order: [[0, 'desc']],
        language: { search: "Search:", emptyTable: "No transactions found" },
        columnDefs: [{ orderable: false, targets: [8] }]
    });
});

function switchSupTab(tab) {
    $('.nav-tab-modern').removeClass('active');
    $('#paneSupplies, #paneTransactions').hide();

    if (tab === 'supplies') {
        $('#tabSupplies').addClass('active');
        $('#paneSupplies').show();
    } else if (tab === 'transactions') {
        $('#tabTransactions').addClass('active');
        $('#paneTransactions').show();
    }
}

function filterSupTable(status) {
    $('.stat-card').removeClass('active');
    $('.stat-card[data-filter="' + status + '"]').addClass('active');

    switchSupTab('supplies');

    var columnIndex = 8;
    if (status === 'all') {
        supplyTable.column(columnIndex).search('', true, false).draw();
    } else {
        var labelMap = {
            'IN_STOCK'     : 'In Stock',
            'LOW_STOCK'    : 'Low Stock',
            'OUT_OF_STOCK' : 'Out of Stock'
        };
        var searchTerm = labelMap[status] || status;
        supplyTable.column(columnIndex).search(searchTerm, true, false).draw();
    }
}
</script>

<script src="<?=base_url('assets/js/apps/fms/supply/supply.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>