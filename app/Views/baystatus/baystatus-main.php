<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("SELECT * FROM tbl_bay_status ORDER BY stage_id");
$bay_statuses = $query->getResultArray();

// ==============================
// FETCH OCCUPANTS FOR BAYS (Like Transaction Module)
// ==============================
$occupants_query = $this->db->query("
    SELECT t.stage_id, t.shooter_name, t.shooter_type, t.checkin_time, t.transaction_id
    FROM tbl_transactions t
    WHERE t.status = 'ACTIVE'
");
$occupants = [];
foreach($occupants_query->getResultArray() as $occ) {
    $occupants[$occ['stage_id']] = $occ;
}

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_bays = count($bay_statuses);
$available = $this->db->query("SELECT COUNT(*) as total FROM tbl_bay_status WHERE status = 'AVAILABLE'")->getRow()->total;
$occupied = $this->db->query("SELECT COUNT(*) as total FROM tbl_bay_status WHERE status = 'OCCUPIED'")->getRow()->total;
$reserved = $this->db->query("SELECT COUNT(*) as total FROM tbl_bay_status WHERE status = 'RESERVED'")->getRow()->total;
$maintenance = $this->db->query("SELECT COUNT(*) as total FROM tbl_bay_status WHERE status = 'MAINTENANCE'")->getRow()->total;

// Get the highest stage ID
$max_stage = $this->db->query("SELECT MAX(stage_id) as max FROM tbl_bay_status")->getRow()->max;
$next_stage = $max_stage ? $max_stage + 1 : 1;

echo view('templates/myheader.php');
?>
<style>
    /* ============================================ */
    /* QCPD HEADER - UNIFIED WITH DASHBOARD */
    /* ============================================ */
    .qcpd-module-header {
        background: #1a365d;
        background: linear-gradient(135deg, #1a365d 0%, #2b6cb0 100%);
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

    .qcpd-module-header .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
        min-width: 200px;
    }

    .qcpd-module-header .header-left .module-icon {
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

    .qcpd-module-header .header-left .module-info h4 {
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

    .qcpd-module-header .header-left .module-info h4 .module-badge {
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

    .qcpd-module-header .header-left .module-info .module-meta {
        color: #bee3f8;
        font-size: 12px;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .qcpd-module-header .header-left .module-info .module-meta .meta-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: #e2e8f0;
    }

    .qcpd-module-header .header-left .module-info .module-meta .meta-item i {
        color: #90cdf4;
        font-size: 11px;
    }

    .qcpd-module-header .header-left .module-info .module-meta .divider {
        color: rgba(255, 255, 255, 0.15);
    }

    .qcpd-module-header .header-left .module-info .module-meta .live-clock {
        color: #ffffff;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.12);
        padding: 2px 10px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .qcpd-module-header .header-left .module-info .module-meta .live-clock i {
        color: #90cdf4;
    }

    :root {
        --primary: #1e3a5f;
        --primary-dark: #0f2b44;
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
    }

    body {
        background: var(--gray-50);
    }

    .attendance-card {
        border-radius: 12px;
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
        background: #ffffff;
        height: 100%;
    }

    .attendance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -12px rgba(0,0,0,0.1);
        border-color: var(--gray-300);
    }

    .attendance-card .card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
    }

    .attendance-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
        color: var(--gray-800);
        font-family: 'Inter', sans-serif;
    }

    .attendance-icon {
        font-size: 36px;
        opacity: 0.1;
        color: var(--primary);
    }

    .attendance-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .attendance-sub {
        font-size: 11px;
        color: var(--gray-400);
        margin-top: 4px;
    }

    .card {
        border-radius: 12px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        background: #ffffff;
        margin-bottom: 20px;
    }

    .card-header {
        background: #ffffff;
        border-bottom: 1px solid var(--gray-200);
        padding: 14px 20px;
        border-radius: 12px 12px 0 0;
    }

    .card-header h6 {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 0;
        color: var(--gray-700);
    }

    .card-body {
        padding: 16px 20px;
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

    .form-control, select.form-control {
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        color: var(--gray-700);
        background: #ffffff;
        transition: all 0.2s;
        width: 100%;
        height: 38px;
    }

    .form-control:focus, select.form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
    }

    .btn-danger {
        background: var(--danger);
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
    }

    .btn-danger:hover {
        background: var(--danger-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-secondary {
        background: #ffffff;
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-600);
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: #ffffff;
    }

    .badge {
        font-size: 10px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }

    .bg-success { background: var(--success) !important; color: #ffffff; }
    .bg-danger { background: var(--danger) !important; color: #ffffff; }
    .bg-warning { background: var(--warning) !important; color: #ffffff; }
    .bg-primary { background: var(--primary) !important; color: #ffffff; }
    .bg-info { background: var(--info) !important; color: #ffffff; }
    .bg-secondary { background: var(--gray-500) !important; color: #ffffff; }
    .bg-light { background: var(--gray-100) !important; color: var(--gray-700); }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 1rem;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: var(--gray-500);
        font-size: 12px;
        transition: all 0.2s;
    }

    .breadcrumb-item a:hover {
        color: var(--primary);
    }

    .breadcrumb-item.active {
        color: var(--primary);
        font-weight: 600;
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
        padding: 10px 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: center;
    }

    .table tbody td {
        font-size: 12px;
        color: var(--gray-700);
        padding: 10px 10px;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
        text-align: center;
    }

    .table-hover tbody tr:hover {
        background: var(--gray-50);
    }

    .dataTables_wrapper {
        font-family: 'Inter', sans-serif;
        overflow-x: visible !important;
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
        box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
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
        background: var(--danger) !important;
        border-color: var(--danger) !important;
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

    /* ============================================ */
    /* BAY STATUS CARDS - FULL WIDTH */
    /* ============================================ */
    .bay-status-grid {
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        gap: 6px;
        width: 100%;
    }

    .bay-item {
        padding: 12px 6px;
        border-radius: 8px;
        text-align: center;
        font-size: 10px;
        font-weight: 600;
        border: 2px solid var(--gray-200);
        transition: all 0.3s;
        position: relative;
        min-height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .bay-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 10;
    }

    /* Available */
    .bay-item.available {
        background: #d1fae5;
        border-color: var(--success);
        color: #065f46;
    }

    .bay-item.available .bay-number {
        color: #065f46;
    }

    /* Occupied - with more detail */
    .bay-item.occupied {
        background: #fee2e2;
        border-color: var(--danger);
        color: #991b1b;
        animation: pulse-occupied 2s ease-in-out infinite;
        padding: 8px 4px;
        min-height: 100px;
    }

    .bay-item.occupied .bay-number {
        color: var(--danger-dark);
        font-size: 16px;
    }

    .bay-item.occupied .bay-status-label {
        color: var(--danger);
        font-size: 7px;
    }

    /* Occupant Info */
    .bay-item .bay-occupant {
        margin-top: 4px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }

    .bay-item .bay-occupant .occupant-name {
        font-size: 9px;
        font-weight: 700;
        color: var(--gray-800);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .bay-item .bay-occupant .occupant-name i {
        font-size: 9px;
        color: var(--gray-600);
    }

    .bay-item .bay-occupant .occupant-type {
        font-size: 8px;
    }

    .bay-item .bay-occupant .occupant-type .badge {
        font-size: 7px;
        padding: 1px 8px;
        border-radius: 10px;
    }

    .bay-item .bay-occupant .occupant-time {
        font-size: 8px;
        color: var(--gray-600);
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .bay-item .bay-occupant .occupant-time i {
        font-size: 8px;
    }

    /* Reserved */
    .bay-item.reserved {
        background: #fef3c7;
        border-color: var(--warning);
        color: #92400e;
        animation: pulse-reserved 2s ease-in-out infinite;
    }

    .bay-item.reserved .bay-number {
        color: #92400e;
    }

    /* Maintenance */
    .bay-item.maintenance {
        background: var(--gray-200);
        border-color: var(--gray-400);
        color: var(--gray-600);
    }

    .bay-item.maintenance .bay-number {
        color: var(--gray-600);
    }

    @keyframes pulse-occupied {
        0%, 100% { 
            transform: scale(1); 
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.1);
        }
        50% { 
            transform: scale(1.02); 
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.15);
        }
    }

    @keyframes pulse-reserved {
        0%, 100% { 
            transform: scale(1); 
            box-shadow: 0 0 0 0 rgba(183, 121, 31, 0.1);
        }
        50% { 
            transform: scale(1.02); 
            box-shadow: 0 0 20px rgba(183, 121, 31, 0.15);
        }
    }

    .bay-item .bay-number {
        display: block;
        font-size: 18px;
        font-weight: 700;
        font-family: 'Inter', monospace;
    }

    .bay-item .bay-status-label {
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
        display: block;
    }

    /* ============================================ */
    /* ACTION BUTTONS */
    /* ============================================ */
    .action-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2px;
        flex-wrap: wrap;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 11px;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-action i {
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .btn-action .action-label {
        font-size: 10px;
        font-weight: 500;
    }

    .btn-action:hover {
        transform: translateY(-1px);
    }

    .btn-edit {
        color: var(--warning);
    }

    .btn-edit:hover {
        background: #fef3c7;
        border-color: #fcd34d;
    }

    .btn-delete {
        color: var(--danger);
    }

    .btn-delete:hover {
        background: #fee2e2;
        border-color: #fca5a5;
    }


    /* Responsive */
    @media (max-width: 992px) {
        .qcpd-module-header {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 20px;
            gap: 12px;
        }
        
        .bay-status-grid {
            grid-template-columns: repeat(5, 1fr);
        }
        
        .dataTables_filter,
        .dataTables_paginate,
        .dataTables_info {
            float: none;
            text-align: center;
        }
        
        .dataTables_filter {
            margin-bottom: 12px;
        }
        
        .dataTables_filter label {
            justify-content: center;
        }
        
        .dataTables_paginate {
            margin-top: 12px;
        }
        
        .dataTables_info {
            margin-top: 12px;
            margin-bottom: 8px;
        }
        
        .bay-item.occupied {
            min-height: 90px;
        }
    }

    @media (max-width: 768px) {
        .qcpd-module-header .header-left .module-info h4 {
            font-size: 16px;
        }
        
        .qcpd-module-header .header-left .module-info .module-meta {
            font-size: 11px;
            gap: 6px;
        }
        
        .attendance-value {
            font-size: 22px;
        }
        
        .attendance-icon {
            font-size: 30px;
        }
        
        .bay-status-grid {
            grid-template-columns: repeat(5, 1fr);
            gap: 4px;
        }
        
        .bay-item {
            padding: 6px 4px;
            min-height: 70px;
        }
        
        .bay-item .bay-number {
            font-size: 14px;
        }
        
        .bay-item .bay-status-label {
            font-size: 6px;
        }
        
        .bay-item .bay-occupant .occupant-name {
            font-size: 8px;
        }
        
        .bay-item .bay-occupant .occupant-type .badge {
            font-size: 6px;
            padding: 1px 6px;
        }
        
        .bay-item .bay-occupant .occupant-time {
            font-size: 7px;
        }
        
        .bay-item.occupied {
            min-height: 80px;
            padding: 6px 3px;
        }
        
        .card-header {
            flex-direction: column;
            gap: 8px;
            align-items: flex-start !important;
        }
        
        .dataTables_filter input {
            width: 150px;
        }
        
        .btn-action .action-label {
            display: none;
        }
        
        .btn-action {
            padding: 4px 6px;
        }

        .col-md-8, .col-md-4 {
            padding: 0 !important;
        }
        
        .qcpd-module-header .header-left .module-icon {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .qcpd-module-header {
            padding: 12px 16px;
        }
        
        .qcpd-module-header .header-left .module-info h4 {
            font-size: 14px;
        }
        
        .qcpd-module-header .header-left .module-info h4 .module-badge {
            font-size: 8px;
            padding: 1px 8px;
        }
        
        .qcpd-module-header .header-left .module-info .module-meta {
            font-size: 10px;
            gap: 4px;
        }
        
        .qcpd-module-header .header-left .module-info .module-meta .divider {
            display: none;
        }
        
        .bay-status-grid {
            grid-template-columns: repeat(5, 1fr);
            gap: 3px;
        }
        
        .bay-item {
            padding: 4px 2px;
            min-height: 60px;
        }
        
        .bay-item .bay-number {
            font-size: 12px;
        }
        
        .bay-item .bay-status-label {
            font-size: 5px;
        }
        
        .bay-item .bay-occupant .occupant-name {
            font-size: 7px;
        }
        
        .bay-item .bay-occupant .occupant-type .badge {
            font-size: 5px;
            padding: 0 4px;
        }
        
        .bay-item .bay-occupant .occupant-time {
            font-size: 6px;
        }
        
        .bay-item.occupied {
            min-height: 65px;
            padding: 4px 2px;
        }
        
        .attendance-card .card-body {
            padding: 12px 16px;
        }
        
        .attendance-value {
            font-size: 18px;
        }
        
        .attendance-icon {
            font-size: 24px;
        }
        
        .btn-action {
            width: 28px;
            height: 28px;
            padding: 0;
            border-radius: 6px;
        }
        
        .btn-action i {
            font-size: 13px;
        }
        
        .qcpd-module-header .header-left .module-icon {
            width: 28px;
            height: 28px;
            font-size: 14px;
        }
    }
</style>

<div class="me-bs-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- QCPD UNIFORM HEADER -->
<!-- ============================================ -->
<div class="qcpd-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="ti ti-layout-grid"></i>
        </div>
        <div class="module-info">
            <h4>
                Bay Status Management
                <span class="module-badge">Facility Management</span>
            </h4>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TOTALS - FULL WIDTH -->
<!-- ============================================ -->
<div class="row g-1 mb-4">
    <div class="col-md-3">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="attendance-label">Total Bays</div>
                    <div class="attendance-value"><?=$total_bays;?></div>
                    <div class="attendance-sub">All bays</div>
                </div>
                <i class="ti ti-layout-grid attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="attendance-label">Available</div>
                    <div class="attendance-value"><?=$available;?></div>
                    <div class="attendance-sub">Ready for use</div>
                </div>
                <i class="ti ti-check attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="attendance-label">Occupied</div>
                    <div class="attendance-value"><?=$occupied;?></div>
                    <div class="attendance-sub">In use</div>
                </div>
                <i class="ti ti-x attendance-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card attendance-card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="attendance-label">Reserved / Maint</div>
                    <div class="attendance-value"><?=$reserved + $maintenance;?></div>
                    <div class="attendance-sub">Not available</div>
                </div>
                <i class="ti ti-clock attendance-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- RANGE AVAILABILITY - FULL WIDTH -->
<!-- ============================================ -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">
                    <i class="ti ti-layout-grid"></i> Range Availability
                    <span class="badge bg-light text-dark border ms-2"><?=$available;?> Available</span>
                </h6>
                <div>
                    <span class="badge bg-success me-1">Available</span>
                    <span class="badge bg-danger me-1">Occupied</span>
                    <span class="badge bg-warning me-1">Reserved</span>
                    <span class="badge bg-secondary">Maintenance</span>
                </div>
            </div>
            <div class="card-body">
                <div class="bay-status-grid">
                    <?php foreach($bay_statuses as $bay): 
                        $status_class = strtolower($bay['status']);
                        $status_label = $bay['status'];
                        $is_occupied = ($bay['status'] == 'OCCUPIED');
                        $occupant = isset($occupants[$bay['stage_id']]) ? $occupants[$bay['stage_id']] : null;
                    ?>
                    <div class="bay-item <?=$status_class;?>">
                        <span class="bay-number"><?=$bay['stage_id'];?></span>
                        <span class="bay-status-label"><?=$status_label;?></span>
                        
                        <?php if($is_occupied && $occupant): ?>
                        <div class="bay-occupant">
                            <div class="occupant-name">
                                <i class="ti ti-user"></i>
                                <?=htmlspecialchars($occupant['shooter_name']);?>
                            </div>
                            <div class="occupant-type">
                                <span class="badge <?=$occupant['shooter_type'] == 'PNP' ? 'bg-primary' : 'bg-secondary';?>">
                                    <?=$occupant['shooter_type'];?>
                                </span>
                            </div>
                            <div class="occupant-time">
                                <i class="ti ti-clock"></i> <?=date('h:i A', strtotime($occupant['checkin_time']));?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- BAY STATUS TABLE & FORM - FULL WIDTH -->
<!-- ============================================ -->
<div class="row">
    <!-- BAY STATUS TABLE -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Bay Status List</h6>
                <span class="badge bg-light text-dark border"><?=count($bay_statuses);?> records</span>
            </div>

            <div class="card-body">
                <table id="bsTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th width="250">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($bay_statuses as $row): ?>
                        <tr>
                            <td><strong><?=$row['bay_id'];?></strong></td>
                            <td>Stage <?=$row['stage_id'];?></td>
                            <td>
                                <?php 
                                    $status_class = '';
                                    if($row['status'] == 'AVAILABLE') $status_class = 'bg-success';
                                    elseif($row['status'] == 'OCCUPIED') $status_class = 'bg-danger';
                                    elseif($row['status'] == 'RESERVED') $status_class = 'bg-warning';
                                    else $status_class = 'bg-secondary';
                                ?>
                                <span class="badge <?=$status_class;?>"><?=$row['status'];?></span>
                            </td>
                            <td><?=date('m/d/Y h:i A', strtotime($row['updated_at']));?></td>
                            <td>
                                <div class="action-wrapper">
                                    <!-- Edit Button -->
                                    <button class="btn-action btn-edit" 
                                            onclick="editBayStatus(<?=$row['bay_id'];?>, <?=$row['stage_id'];?>, '<?=$row['status'];?>')" 
                                            title="Edit">
                                        <i class="ti ti-edit"></i>
                                        <span class="action-label">Edit</span>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <button class="btn-action btn-delete" 
                                            onclick="showDeleteModal(<?=$row['bay_id'];?>, <?=$row['stage_id'];?>)" 
                                            title="Delete">
                                        <i class="ti ti-trash"></i>
                                        <span class="action-label">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ADD BAY STATUS FORM -->
    <div class="col-md-4">
        <div class="card">
            <form class="bs-reg-form" id="bsRegForm">
                <div class="card-header">
                    <h6 class="fw-semibold mb-0">Add Bay / Status</h6>
                    <small class="text-muted">Add a new bay or update status</small>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Stage Number</label>
                        <input type="number" id="stage_id" class="form-control" name="stage_id" 
                               placeholder="Enter stage number (e.g., 1, 2, 3...)" 
                               min="1" step="1" value="<?=$next_stage;?>" required>
                        <small class="text-muted">Next available: <strong>Stage <?=$next_stage;?></strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="AVAILABLE">AVAILABLE</option>
                            <option value="OCCUPIED">OCCUPIED</option>
                            <option value="RESERVED">RESERVED</option>
                            <option value="MAINTENANCE">MAINTENANCE</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 mt-2">
                        <i class="ti ti-device-floppy me-1"></i>
                        Save Bay Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bay Status Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Bay Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_bay_id">
                
                <div class="mb-3">
                    <label class="form-label">Stage Number</label>
                    <input type="number" id="edit_stage_id" class="form-control" min="1" step="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select id="edit_status" class="form-control" required>
                        <option value="AVAILABLE">AVAILABLE</option>
                        <option value="OCCUPIED">OCCUPIED</option>
                        <option value="RESERVED">RESERVED</option>
                        <option value="MAINTENANCE">MAINTENANCE</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="updateBayStatus()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="ti ti-trash me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <i class="ti ti-alert-triangle" style="font-size: 48px; color: var(--danger);"></i>
                    <h4 class="mt-3">Are you sure?</h4>
                    <p class="text-muted">You are about to delete bay status for: <br>
                        <strong id="delete_bay_stage" class="text-danger"></strong>
                    </p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="ti ti-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $('#bsTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']],
        language: {
            search: "Search Bay Status:"
        }
    });

    $('#confirmDeleteBtn').on('click', function() {
        deleteBayStatus();
        var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
    });

    // Pre-fill next available stage
    $('#stage_id').val(<?=$next_stage;?>);
});

function editBayStatus(id, stage, status) {
    document.getElementById('edit_bay_id').value = id;
    document.getElementById('edit_stage_id').value = stage;
    document.getElementById('edit_status').value = status;
    
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

var deleteId = null;
var deleteStage = '';

function showDeleteModal(id, stage) {
    deleteId = id;
    deleteStage = stage;
    document.getElementById('delete_bay_stage').innerHTML = 'Stage ' + stage;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function deleteBayStatus() {
    if(deleteId) {
        var mparam = {
            bay_id: deleteId,
            meaction: 'DELETE'
        };

        jQuery.ajax({
            type: "POST",
            url: '<?=site_url();?>baystatus',
            data: mparam,
            dataType: 'json',
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + error);
            }
        });
    }
}

function updateBayStatus() {
    var bay_id = document.getElementById('edit_bay_id').value;
    var stage_id = document.getElementById('edit_stage_id').value;
    var status = document.getElementById('edit_status').value;

    var mparam = {
        bay_id: bay_id,
        stage_id: stage_id,
        status: status,
        meaction: 'EDIT'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>baystatus',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data.status == 'success'){
                toastr.success(data.message);
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                toastr.error(data.message);
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error: " + error);
        }
    });
}

// Live Clock Update
function updateClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    const clockElement = document.getElementById('liveClock');
    if (clockElement) {
        clockElement.textContent = timeStr;
    }
}
updateClock();
setInterval(updateClock, 30000);
</script>

<script src="<?=base_url('assets/js/baystatus/bs.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>