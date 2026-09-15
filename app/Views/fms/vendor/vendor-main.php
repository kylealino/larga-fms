<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("SELECT * FROM tbl_vendors ORDER BY vendor_id DESC");
$vendors = $query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_vendors = count($vendors);
$total_active = $this->db->query("SELECT COUNT(*) as total FROM tbl_vendors WHERE vendor_status = 'ACTIVE'")->getRow()->total;
$total_inactive = $this->db->query("SELECT COUNT(*) as total FROM tbl_vendors WHERE vendor_status IN ('INACTIVE', 'SUSPENDED', 'TERMINATED')")->getRow()->total;

echo view('templates/myheader.php');
?>
<style>
    /* ============================================ */
    /* VENDOR MODULE - PROFESSIONAL UX APPROACH */
    /* ============================================ */
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
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
        --shadow: 0 1px 3px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.08);
    }

    body {
        background: var(--gray-50);
    }

    .lrg-module-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 14px 24px;
        margin: -24px -24px 24px -24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
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
        background: rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #90cdf4;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .lrg-module-header .header-left .module-info h4 {
        font-size: 17px;
        font-weight: 600;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.3px;
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
        background: rgba(255, 255, 255, 0.12);
        color: #bee3f8;
        border: 1px solid rgba(255, 255, 255, 0.08);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-header {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
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
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-header-primary {
        background: #ffffff;
        border-color: #ffffff;
        color: var(--primary);
    }

    .btn-header-primary:hover {
        background: rgba(255, 255, 255, 0.9);
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

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-card.active {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(26,107,176,0.15), var(--shadow-md);
    }

    .stat-card.active::before {
        opacity: 1;
    }

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
        font-family: 'Inter', sans-serif;
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

    .stat-card .filter-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 8px;
        background: var(--gray-100);
        color: var(--gray-500);
        padding: 1px 8px;
        border-radius: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card:hover .filter-badge {
        opacity: 1;
    }

    .active-filter-badge {
        font-size: 9px;
        background: var(--primary-light);
        color: var(--primary);
        padding: 1px 10px;
        border-radius: 12px;
        font-weight: 500;
        display: inline-block;
        margin-left: 8px;
    }

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

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

    .card-body {
        padding: 20px;
    }

    .form-section {
        background: var(--gray-50);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        border: 1px solid var(--gray-200);
    }

    .form-section-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-title i {
        font-size: 14px;
    }

    .form-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 4px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .form-label .required {
        color: var(--danger);
        margin-left: 2px;
    }

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

    .btn-warning-custom {
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

    .btn-warning-custom:hover {
        background: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
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
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
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

    .btn-sm {
        padding: 5px 14px;
        font-size: 11px;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    .table-wrap {
        overflow-x: auto;
    }

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

    .table-hover tbody tr:hover {
        background: var(--gray-50);
    }

    .table .text-center {
        text-align: center;
    }

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

    .btn-icon:hover {
        transform: scale(1.05);
    }

    .btn-icon-view {
        color: var(--info);
    }
    .btn-icon-view:hover { background: #dbeafe; border-color: #93c5fd; }

    .btn-icon-edit {
        color: var(--warning);
    }
    .btn-icon-edit:hover { background: #fef3c7; border-color: #fcd34d; }

    .btn-icon-delete {
        color: var(--danger);
    }
    .btn-icon-delete:hover { background: #fee2e2; border-color: #fca5a5; }

    .dataTables_wrapper {
        font-family: 'Inter', sans-serif;
    }

    .dataTables_filter {
        float: right;
        margin-bottom: 16px;
    }

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

    .dataTables_paginate {
        float: right;
        margin-top: 16px;
    }

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

    .dataTables_info {
        float: left;
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 16px;
    }

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

    .modal-header .modal-title i {
        color: var(--primary);
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        border-top: 1px solid var(--gray-200);
        padding: 16px 24px;
        gap: 10px;
        background: var(--gray-50);
        border-radius: 0 0 16px 16px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-state i {
        font-size: 48px;
        color: var(--gray-300);
        margin-bottom: 16px;
    }

    .empty-state h5 {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 4px;
    }

    .empty-state p {
        font-size: 13px;
        color: var(--gray-400);
    }

    @media (max-width: 992px) {
        .lrg-module-header {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 20px;
        }
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
    }

    @media (max-width: 768px) {
        .stat-grid {
            grid-template-columns: 1fr 1fr;
        }
        .stat-card {
            padding: 14px 16px;
        }
        .stat-left .stat-value {
            font-size: 20px;
        }
        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .toolbar-left, .toolbar-right {
            flex-wrap: wrap;
        }
        .dataTables_filter input {
            width: 150px;
        }
    }

    @media (max-width: 480px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }
        .lrg-module-header {
            padding: 12px 16px;
        }
        .lrg-module-header .header-left .module-info h4 {
            font-size: 14px;
        }
        .lrg-module-header .header-left .module-info h4 .module-badge {
            font-size: 8px;
            padding: 1px 8px;
        }
        .stat-card {
            padding: 12px 14px;
        }
        .stat-left .stat-value {
            font-size: 18px;
        }
        .stat-right {
            font-size: 24px;
        }
        .btn-icon {
            width: 28px;
            height: 28px;
            font-size: 13px;
        }
        .modal-body {
            padding: 16px;
        }
    }
</style>

<div class="me-vnd-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="bi bi-building"></i>
        </div>
        <div class="module-info">
            <h4>
                Vendor Management
                <span class="module-badge">Operations</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="openAddVendor()">
            <i class="bi bi-plus-circle"></i> New Vendor
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS CARDS - CLICKABLE -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Vendors</div>
            <div class="stat-value"><?=$total_vendors;?></div>
            <div class="stat-sub">All vendors <span class="active-filter-badge" id="filterBadgeAll" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-building"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
    <div class="stat-card" data-filter="ACTIVE" onclick="filterTable('ACTIVE')">
        <div class="stat-left">
            <div class="stat-label">Active</div>
            <div class="stat-value"><?=$total_active;?></div>
            <div class="stat-sub">Active vendors <span class="active-filter-badge" id="filterBadgeActive" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
    <div class="stat-card" data-filter="INACTIVE" onclick="filterTable('INACTIVE')">
        <div class="stat-left">
            <div class="stat-label">Inactive</div>
            <div class="stat-value"><?=$total_inactive;?></div>
            <div class="stat-sub">Inactive/Suspended <span class="active-filter-badge" id="filterBadgeInactive" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-x-circle"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-table me-2"></i>Vendor Directory 
                    <span id="filterStatusDisplay" style="font-size:11px;font-weight:400;color:var(--gray-500);margin-left:8px;"></span>
                </h6>
                <div>
                    <span class="badge badge-secondary"><?=count($vendors);?> records</span>
                    <button class="btn-toolbar btn-toolbar-filter btn-sm ms-2" onclick="filterTable('all')" id="clearFilterBtn" style="display:none;">
                        <i class="bi bi-x"></i> Clear Filter
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <button class="btn-toolbar btn-toolbar-primary" onclick="openAddVendor()">
                            <i class="bi bi-plus-circle"></i> Add New
                        </button>
                        <button class="btn-toolbar" onclick="window.location.reload();">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <span class="text-muted" style="font-size:12px;">
                            <i class="bi bi-info-circle"></i> Click <strong>Services</strong> to manage vendor services
                        </span>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="vendorTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="100">Code</th>
                                <th>Vendor Name</th>
                                <th width="100">Type</th>
                                <th>Contact Person</th>
                                <th width="100">Status</th>
                                <th width="220" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($vendors) > 0): ?>
                                <?php foreach($vendors as $row): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['vendor_code'];?></span></td>
                                    <td><strong><?=$row['vendor_name'];?></strong></td>
                                    <td><?=$row['vendor_type'] ?: '—';?></td>
                                    <td><?=$row['contact_person'] ?: '—';?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-secondary';
                                        $statusLabel = 'Inactive';
                                        if($row['vendor_status'] == 'ACTIVE') { $statusClass = 'badge-success'; $statusLabel = 'Active'; }
                                        elseif($row['vendor_status'] == 'INACTIVE') { $statusClass = 'badge-secondary'; $statusLabel = 'Inactive'; }
                                        elseif($row['vendor_status'] == 'SUSPENDED') { $statusClass = 'badge-warning'; $statusLabel = 'Suspended'; }
                                        elseif($row['vendor_status'] == 'TERMINATED') { $statusClass = 'badge-danger'; $statusLabel = 'Terminated'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-view" 
                                                    onclick="viewVendor(<?=$row['vendor_id'];?>)" 
                                                    title="View Vendor">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-view" 
                                                    onclick="__Vendors.__viewServices(<?=$row['vendor_id'];?>, '<?=addslashes($row['vendor_name']);?>')" 
                                                    title="Manage Services">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-edit" 
                                                    onclick="editVendor(<?=$row['vendor_id'];?>, '<?=addslashes($row['vendor_code']);?>', '<?=addslashes($row['vendor_name']);?>', '<?=addslashes($row['vendor_type']);?>', '<?=addslashes($row['contact_person']);?>', '<?=$row['contact_number'];?>', '<?=$row['email_address'];?>', '<?=addslashes($row['address']);?>', '<?=$row['payment_terms'];?>', '<?=$row['vendor_status'];?>', '<?=addslashes($row['remarks']);?>')" 
                                                    title="Edit Vendor">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-delete" 
                                                    onclick="__Vendors.__showDeleteModal(<?=$row['vendor_id'];?>, '<?=addslashes($row['vendor_name']);?>')" 
                                                    title="Delete Vendor">
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

                <?php if(count($vendors) == 0): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No vendors yet</h5>
                    <p>Click <strong>New Vendor</strong> to add your first vendor.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ADD / EDIT VENDOR MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="vendorModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vendorModalTitle">
                    <i class="bi bi-building me-2"></i>New Vendor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="vendorForm">
                <div class="modal-body">
                    <input type="hidden" id="form_vendor_id">
                    <input type="hidden" id="form_vendor_code">

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Vendor Code</label>
                            <input type="text" id="form_vendor_code_display" class="form-control" readonly>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Vendor Name <span class="required">*</span></label>
                            <input type="text" id="form_vendor_name" class="form-control" placeholder="Enter vendor name" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Vendor Type</label>
                            <select id="form_vendor_type" class="form-control">
                                <option value="">— Select —</option>
                                <option value="Trucking Service Provider">Trucking Service Provider</option>
                                <option value="Manpower Provider">Manpower Provider</option>
                                <option value="Equipment Rental">Equipment Rental</option>
                                <option value="Transportation Service">Transportation Service</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Contact Person</label>
                            <input type="text" id="form_contact_person" class="form-control" placeholder="Full name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Contact Number</label>
                            <input type="text" id="form_contact_number" class="form-control" placeholder="Phone number">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" id="form_email_address" class="form-control" placeholder="email@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Terms</label>
                            <select id="form_payment_terms" class="form-control">
                                <option value="">— Select —</option>
                                <option value="COD">Cash on Delivery</option>
                                <option value="15 Days">15 Days</option>
                                <option value="30 Days">30 Days</option>
                                <option value="45 Days">45 Days</option>
                                <option value="60 Days">60 Days</option>
                                <option value="Net 30">Net 30</option>
                                <option value="Net 45">Net 45</option>
                                <option value="Net 60">Net 60</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Address</label>
                            <input type="text" id="form_address" class="form-control" placeholder="Address">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select id="form_vendor_status" class="form-control">
                                <option value="ACTIVE">Active</option>
                                <option value="INACTIVE">Inactive</option>
                                <option value="SUSPENDED">Suspended</option>
                                <option value="TERMINATED">Terminated</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Remarks</label>
                        <textarea id="form_remarks" class="form-control" rows="2" placeholder="Additional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">
                        <i class="bi bi-save"></i> <span id="formBtnText">Save Vendor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- VIEW VENDOR MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Vendor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewVendorContent">
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
<!-- SERVICES MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="servicesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-gear me-2"></i>Vendor Services</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="svc_vendor_id">
                <div class="d-flex align-items-center gap-3 mb-3 p-3" style="background:var(--gray-50);border-radius:8px;">
                    <i class="bi bi-building" style="font-size:24px;color:var(--primary);"></i>
                    <div>
                        <div class="fw-semibold" id="svc_vendor_name" style="font-size:15px;"></div>
                        <div class="text-muted" style="font-size:12px;">Manage services offered by this vendor</div>
                    </div>
                </div>
                
                <!-- Add Service Form -->
                <div class="row g-2 mb-3 p-3" style="background:#ffffff;border:1px solid var(--gray-200);border-radius:8px;">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Service Type *</label>
                        <select id="svc_service_type" class="form-control">
                            <option value="">— Select —</option>
                            <option value="TRUCK RENTAL">Truck Rental</option>
                            <option value="TRACTOR RENTAL">Tractor Rental</option>
                            <option value="CHASSIS RENTAL">Chassis Rental</option>
                            <option value="DRIVER SERVICE">Driver Service</option>
                            <option value="HELPER SERVICE">Helper Service</option>
                            <option value="TRUCK + DRIVER">Truck + Driver</option>
                            <option value="TRUCK + DRIVER + HELPER">Truck + Driver + Helper</option>
                            <option value="TRACTOR + CHASSIS">Tractor + Chassis</option>
                            <option value="TRACTOR + CHASSIS + DRIVER">Tractor + Chassis + Driver</option>
                            <option value="TRACTOR + CHASSIS + DRIVER + HELPER">Tractor + Chassis + Driver + Helper</option>
                            <option value="OTHER TRANSPORTATION SERVICE">Other Transportation Service</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Rate Type</label>
                        <select id="svc_rate_type" class="form-control">
                            <option value="">— Select —</option>
                            <option value="PER TRIP">Per Trip</option>
                            <option value="PER DAY">Per Day</option>
                            <option value="PER KILOMETER">Per Kilometer</option>
                            <option value="PER HOUR">Per Hour</option>
                            <option value="MONTHLY">Monthly</option>
                            <option value="FIXED RATE">Fixed Rate</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Rate Amount (₱)</label>
                        <input type="number" id="svc_rate_amount" class="form-control" placeholder="0.00" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Effective Date</label>
                        <input type="date" id="svc_effective_date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Status</label>
                        <select id="svc_service_status" class="form-control">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">&nbsp;</label>
                        <button class="btn btn-primary w-100" id="svcActionBtn" onclick="__Vendors.__saveService()" style="font-size:12px;padding:6px 8px;white-space:nowrap;">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                    <div class="col-12 mt-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Remarks</label>
                        <textarea id="svc_remarks" class="form-control form-control-sm" rows="1" placeholder="Additional notes"></textarea>
                    </div>
                </div>

                <!-- Services Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="servicesTable">
                        <thead>
                            <tr>
                                <th>Service Type</th>
                                <th>Rate Type</th>
                                <th>Rate Amount</th>
                                <th>Effective Date</th>
                                <th>Status</th>
                                <th width="80" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="servicesBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted">No services found</td>
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
<!-- DELETE CONFIRMATION MODAL -->
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
                <h5 class="mt-3">Delete Vendor</h5>
                <p class="text-muted">You are about to delete:<br>
                    <strong id="delete_vendor_name" class="text-danger"></strong>
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
var vendorTable;

$(document).ready(function () {
    vendorTable = $('#vendorTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']],
        language: {
            search: "Search Vendor:",
            emptyTable: "No vendors found"
        },
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });

    $('#confirmDeleteBtn').on('click', function() {
        __Vendors.__deleteVendor();
        var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
    });

    $('#vendorForm').on('submit', function(e) {
        e.preventDefault();
        var vendor_id = $('#form_vendor_id').val();
        if(vendor_id) {
            __Vendors.__updateVendor();
        } else {
            __Vendors.__saveVendor();
        }
    });
});

// =============================================
// FILTER TABLE BY STATUS
// =============================================
function filterTable(status) {
    $('.stat-card').removeClass('active');
    $('.active-filter-badge').hide();
    $('#clearFilterBtn').hide();
    $('#filterStatusDisplay').text('');
    
    if(status === 'all') {
        vendorTable.column(4).search('').draw();
        $('.stat-card[data-filter="all"]').addClass('active');
        $('#filterBadgeAll').show();
        $('#filterStatusDisplay').text('(Showing all vendors)');
        $('#clearFilterBtn').show();
    } else if(status === 'ACTIVE') {
        vendorTable.column(4).search('^Active$', true, false).draw();
        $('.stat-card[data-filter="ACTIVE"]').addClass('active');
        $('#filterBadgeActive').show();
        $('#filterStatusDisplay').text('(Filtered: Active)');
        $('#clearFilterBtn').show();
    } else if(status === 'INACTIVE') {
        vendorTable.column(4).search('Inactive|Suspended|Terminated', true, false).draw();
        $('.stat-card[data-filter="INACTIVE"]').addClass('active');
        $('#filterBadgeInactive').show();
        $('#filterStatusDisplay').text('(Filtered: Inactive)');
        $('#clearFilterBtn').show();
    }
}

$(document).on('keyup', '.dataTables_filter input', function() {
    $('.stat-card').removeClass('active');
    $('.active-filter-badge').hide();
    $('#clearFilterBtn').hide();
    $('#filterStatusDisplay').text('');
});

// =============================================
// OPEN ADD VENDOR
// =============================================
function openAddVendor() {
    $('#vendorModalTitle').html('<i class="bi bi-building me-2"></i>New Vendor');
    $('#formBtnText').text('Save Vendor');
    $('#form_vendor_id').val('');
    $('#form_vendor_code').val('');
    $('#form_vendor_code_display').val('Auto-generated');
    $('#form_vendor_name').val('');
    $('#form_vendor_type').val('');
    $('#form_contact_person').val('');
    $('#form_contact_number').val('');
    $('#form_email_address').val('');
    $('#form_address').val('');
    $('#form_payment_terms').val('');
    $('#form_vendor_status').val('ACTIVE');
    $('#form_remarks').val('');
    $('#vendorForm').removeClass('was-validated');
    var modal = new bootstrap.Modal(document.getElementById('vendorModal'));
    modal.show();
}

// =============================================
// EDIT VENDOR
// =============================================
function editVendor(id, code, name, type, contact_person, contact_number, email, address, payment_terms, status, remarks) {
    $('#vendorModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Vendor');
    $('#formBtnText').text('Update Vendor');
    $('#form_vendor_id').val(id);
    $('#form_vendor_code').val(code);
    $('#form_vendor_code_display').val(code);
    $('#form_vendor_name').val(name);
    $('#form_vendor_type').val(type);
    $('#form_contact_person').val(contact_person);
    $('#form_contact_number').val(contact_number);
    $('#form_email_address').val(email);
    $('#form_address').val(address);
    $('#form_payment_terms').val(payment_terms);
    $('#form_vendor_status').val(status);
    $('#form_remarks').val(remarks);
    $('#vendorForm').removeClass('was-validated');
    var modal = new bootstrap.Modal(document.getElementById('vendorModal'));
    modal.show();
}

// =============================================
// VIEW VENDOR
// =============================================
function viewVendor(vendor_id) {
    var mparam = {
        vendor_id: vendor_id,
        meaction: 'GET_VENDOR'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>fms-vendors',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data) {
                var statusLabel = '';
                var statusClass = '';
                if(data.vendor_status == 'ACTIVE') { statusLabel = 'Active'; statusClass = 'badge-success'; }
                else if(data.vendor_status == 'INACTIVE') { statusLabel = 'Inactive'; statusClass = 'badge-secondary'; }
                else if(data.vendor_status == 'SUSPENDED') { statusLabel = 'Suspended'; statusClass = 'badge-warning'; }
                else if(data.vendor_status == 'TERMINATED') { statusLabel = 'Terminated'; statusClass = 'badge-danger'; }

                var html = `
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Vendor Code</small>
                                    <div><strong>${data.vendor_code}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Vendor Name</small>
                                    <div><strong>${data.vendor_name}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Vendor Type</small>
                                    <div><strong>${data.vendor_type || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Status</small>
                                    <div><span class="badge ${statusClass}">${statusLabel}</span></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Contact Person</small>
                                    <div><strong>${data.contact_person || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Contact Number</small>
                                    <div><strong>${data.contact_number || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Email Address</small>
                                    <div><strong>${data.email_address || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Payment Terms</small>
                                    <div><strong>${data.payment_terms || '—'}</strong></div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <small class="text-muted">Address</small>
                                    <div><strong>${data.address || '—'}</strong></div>
                                </div>
                                ${data.remarks ? `
                                <div class="col-md-12 mt-2">
                                    <hr>
                                    <small class="text-muted">Remarks</small>
                                    <div><strong>${data.remarks}</strong></div>
                                </div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
                $('#viewVendorContent').html(html);
                var modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error loading vendor: " + error);
        }
    });
}
</script>

<script src="<?=base_url('assets/js/apps/fms/vendor/vendor.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>