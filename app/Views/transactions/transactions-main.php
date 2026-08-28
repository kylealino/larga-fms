<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("
    SELECT t.*, 
           ra.full_name as assistant_name,
           ra.badge_number as assistant_badge,
           bs.status as bay_status
    FROM tbl_transactions t
    LEFT JOIN tbl_range_assistants ra ON t.range_assistant_id = ra.assistant_id
    LEFT JOIN tbl_bay_status bs ON t.stage_id = bs.stage_id
    ORDER BY t.transaction_id DESC
");
$transactions = $query->getResultArray();

// Get range assistants for dropdown
$assistants_query = $this->db->query("SELECT * FROM tbl_range_assistants WHERE status = 'ACTIVE' ORDER BY full_name");
$range_assistants = $assistants_query->getResultArray();

// Get bay status for display
$bay_status_query = $this->db->query("SELECT * FROM tbl_bay_status ORDER BY stage_id");
$bay_statuses = $bay_status_query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_transactions = $this->db->query("SELECT COUNT(*) as total FROM tbl_transactions")->getRow()->total;
$total_active = $this->db->query("SELECT COUNT(*) as total FROM tbl_transactions WHERE status = 'ACTIVE'")->getRow()->total;
$total_completed = $this->db->query("SELECT COUNT(*) as total FROM tbl_transactions WHERE status = 'COMPLETED'")->getRow()->total;
$total_revenue = $this->db->query("SELECT SUM(total_amount) as total FROM tbl_transactions WHERE status != 'CANCELLED'")->getRow()->total;
$available_bays = $this->db->query("SELECT COUNT(*) as total FROM tbl_bay_status WHERE status = 'AVAILABLE'")->getRow()->total;

// ==============================
// FETCH OCCUPANTS FOR BAYS
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

    /* ============================================ */
    /* TRANSACTION MODULE - UNIFIED DASHBOARD DESIGN */
    /* ============================================ */
    :root {
        --bg-primary: #f0f4f8;
        --bg-card: #ffffff;
        --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --text-muted: #718096;
        --border-color: #e2e8f0;
        --accent: #2b6cb0;
        --accent-light: #ebf4ff;
        --success: #38a169;
        --success-light: #f0fff4;
        --warning: #d69e2e;
        --warning-light: #fffbeb;
        --danger: #e53e3e;
        --danger-light: #fff5f5;
        --disabled: #e2e8f0;
        --disabled-text: #a0aec0;
        --mono: 'SF Mono', 'Menlo', 'Monaco', monospace;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.10);
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* ============================================ */
    /* STATUS BADGES - UNIFIED */
    /* ============================================ */
    .status-badge {
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        padding: 3px 12px;
        border-radius: 4px;
        letter-spacing: 0.3px;
    }

    .status-badge.active,
    .status-badge.on-duty {
        background: var(--success-light);
        color: var(--success);
        border: 1px solid #c6f6d5;
    }

    .status-badge.scheduled,
    .status-badge.break {
        background: var(--warning-light);
        color: var(--warning);
        border: 1px solid #fef3c7;
    }

    .status-badge.completed,
    .status-badge.off-duty {
        background: var(--bg-primary);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }

    .status-badge.cancelled {
        background: var(--danger-light);
        color: var(--danger);
        border: 1px solid #fed7d7;
    }

    /* ============================================ */
    /* STAT CARDS - UNIFIED */
    /* ============================================ */
    .stat-card {
        background: var(--bg-card);
        border-radius: 8px;
        padding: 20px 24px;
        border: 1px solid var(--border-color);
        height: 100%;
        position: relative;
        transition: all 0.2s;
        box-shadow: var(--shadow-sm);
    }

    .stat-card:hover {
        border-color: var(--accent);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-label i {
        color: var(--accent);
        font-size: 12px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-primary);
        font-family: var(--mono);
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.5px;
    }

    .stat-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .stat-sub .up { color: var(--success); }
    .stat-sub .down { color: var(--danger); }

    /* ============================================ */
    /* SECTION TITLES - UNIFIED */
    /* ============================================ */
    .section-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex-wrap: wrap;
    }

    .section-title i {
        color: var(--accent);
        font-size: 14px;
    }

    .section-title .badge-count {
        background: var(--bg-primary);
        color: var(--text-secondary);
        font-size: 9px;
        padding: 2px 10px;
        border-radius: 20px;
        margin-left: 4px;
        font-weight: 600;
        border: 1px solid var(--border-color);
    }

    .section-title .badge-count.current-time {
        background: var(--accent-light);
        color: var(--accent);
        border-color: var(--accent);
    }

    .card-container {
        background: var(--bg-card);
        border-radius: 8px;
        padding: 20px 24px;
        border: 1px solid var(--border-color);
        transition: all 0.2s;
        box-shadow: var(--shadow-sm);
        margin-bottom: 20px;
    }

    .card-container:hover {
        border-color: var(--accent);
        box-shadow: var(--shadow-md);
    }

    /* ============================================ */
    /* BAY STATUS GRID - UNIFIED */
    /* ============================================ */
    .bay-status-grid {
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        gap: 6px;
    }

    .bay-item {
        padding: 10px 6px;
        border-radius: 8px;
        text-align: center;
        font-size: 10px;
        font-weight: 600;
        border: 2px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        min-height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--bg-card);
    }

    .bay-item:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-lg);
        z-index: 10;
    }

    /* Available */
    .bay-item.available {
        border-color: var(--success);
        background: var(--success-light);
        color: #065f46;
    }

    /* Occupied */
    .bay-item.occupied {
        border-color: var(--danger);
        background: var(--danger-light);
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

    @keyframes pulse-occupied {
        0%, 100% { 
            transform: scale(1); 
            box-shadow: 0 0 0 0 rgba(229, 62, 62, 0.1);
        }
        50% { 
            transform: scale(1.02); 
            box-shadow: 0 0 20px rgba(229, 62, 62, 0.15);
        }
    }

    /* Reserved */
    .bay-item.reserved {
        border-color: var(--warning);
        background: var(--warning-light);
        color: #92400e;
    }

    /* Maintenance */
    .bay-item.maintenance {
        border-color: var(--text-muted);
        background: var(--bg-primary);
        color: var(--text-muted);
    }

    .bay-item .bay-number {
        display: block;
        font-size: 18px;
        font-weight: 700;
        font-family: var(--mono);
    }

    .bay-item .bay-status-label {
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
        display: block;
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
        color: var(--text-primary);
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
        color: var(--text-muted);
    }

    .bay-item .bay-occupant .occupant-type .badge {
        font-size: 7px;
        padding: 1px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .bay-item .bay-occupant .occupant-time {
        font-size: 8px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .bay-item .bay-occupant .occupant-time i {
        font-size: 8px;
    }

    /* ============================================ */
    /* FORM ELEMENTS - UNIFIED */
    /* ============================================ */
    .form-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 4px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .form-control, select.form-control {
        border: 1.5px solid var(--border-color);
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 13px;
        color: var(--text-primary);
        background: #ffffff;
        transition: all 0.2s;
        width: 100%;
        height: 38px;
        font-family: inherit;
    }

    .form-control:focus, select.form-control:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.08);
    }

    .form-control[readonly] {
        background: var(--bg-primary);
        cursor: not-allowed;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 60px;
        height: auto;
    }

    /* ============================================ */
    /* BUTTONS - UNIFIED */
    /* ============================================ */
    .btn-primary {
        background: var(--accent);
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #2b6cb0;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(43, 108, 176, 0.3);
    }

    .btn-danger {
        background: var(--danger);
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
        cursor: pointer;
    }

    .btn-danger:hover {
        background: #c53030;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
    }

    .btn-success {
        background: var(--success);
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
        color: #ffffff;
        cursor: pointer;
    }

    .btn-success:hover {
        background: #2f855a;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
    }

    .btn-secondary {
        background: #ffffff;
        border: 1.5px solid var(--border-color);
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-secondary:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: #ffffff;
    }

    /* ============================================ */
    /* ACTION BUTTONS - UNIFIED */
    /* ============================================ */
    .action-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        flex-wrap: wrap;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 5px 12px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 11px;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
        font-family: inherit;
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

    .btn-view {
        color: var(--accent);
    }

    .btn-view:hover {
        background: var(--accent-light);
        border-color: #bee3f8;
    }

    .btn-checkout {
        color: var(--success);
    }

    .btn-checkout:hover {
        background: var(--success-light);
        border-color: #c6f6d5;
    }

    .btn-docs {
        color: #3182ce;
    }

    .btn-docs:hover {
        background: var(--accent-light);
        border-color: #bee3f8;
    }

    /* ============================================ */
    /* TABLES - UNIFIED */
    /* ============================================ */
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .table thead th {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        background: var(--bg-primary);
        border-bottom: 1.5px solid var(--border-color);
        padding: 10px 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: center;
    }

    .table tbody td {
        font-size: 12px;
        color: var(--text-secondary);
        padding: 10px 10px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        text-align: center;
    }

    .table-hover tbody tr:hover {
        background: var(--bg-primary);
    }

    /* ============================================ */
    /* BREADCRUMB - UNIFIED */
    /* ============================================ */
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 1rem;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: var(--text-muted);
        font-size: 12px;
        transition: all 0.2s;
    }

    .breadcrumb-item a:hover {
        color: var(--accent);
    }

    .breadcrumb-item.active {
        color: var(--accent);
        font-weight: 600;
    }

    /* ============================================ */
    /* BADGE - UNIFIED */
    /* ============================================ */
    .badge {
        font-size: 10px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 4px;
        letter-spacing: 0.3px;
    }

    .badge.bg-success { background: var(--success) !important; color: #ffffff; }
    .badge.bg-danger { background: var(--danger) !important; color: #ffffff; }
    .badge.bg-warning { background: var(--warning) !important; color: #ffffff; }
    .badge.bg-primary { background: var(--accent) !important; color: #ffffff; }
    .badge.bg-secondary { background: var(--text-muted) !important; color: #ffffff; }
    .badge.bg-light { background: var(--bg-primary) !important; color: var(--text-secondary); }

    /* ============================================ */
    /* DOCUMENT MANAGEMENT - UNIFIED */
    /* ============================================ */
    .doc-upload-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 20px;
        border-radius: 6px;
        border: 2px dashed var(--accent);
        background: var(--accent-light);
        color: var(--accent);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }

    .doc-upload-btn:hover {
        background: var(--accent);
        color: #ffffff;
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 108, 176, 0.2);
    }

    .doc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
        margin-top: 10px;
    }

    .doc-card {
        background: var(--bg-primary);
        border-radius: 8px;
        padding: 16px 12px;
        border: 1px solid var(--border-color);
        text-align: center;
        transition: all 0.3s;
        position: relative;
    }

    .doc-card:hover {
        border-color: var(--accent);
        box-shadow: var(--shadow-md);
        transform: translateY(-3px);
    }

    .doc-card .doc-icon-large {
        font-size: 36px;
        color: var(--accent);
        display: block;
        margin-bottom: 6px;
    }

    .doc-card .doc-name {
        font-size: 11px;
        font-weight: 500;
        color: var(--text-secondary);
        word-break: break-all;
        line-height: 1.3;
    }

    .doc-card .doc-type-badge {
        display: inline-block;
        font-size: 8px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 4px;
        background: var(--border-color);
        color: var(--text-muted);
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .doc-card .doc-actions-overlay {
        position: absolute;
        top: 6px;
        right: 6px;
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: all 0.3s;
    }

    .doc-card:hover .doc-actions-overlay {
        opacity: 1;
    }

    .doc-card .doc-actions-overlay .btn-doc-sm {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        border: none;
        background: #ffffff;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
    }

    .doc-card .doc-actions-overlay .btn-doc-sm:hover {
        transform: scale(1.1);
    }

    .doc-card .doc-actions-overlay .btn-doc-sm.btn-view-sm {
        color: var(--accent);
    }

    .doc-card .doc-actions-overlay .btn-doc-sm.btn-view-sm:hover {
        background: var(--accent-light);
    }

    .doc-card .doc-actions-overlay .btn-doc-sm.btn-delete-sm {
        color: var(--danger);
    }

    .doc-card .doc-actions-overlay .btn-doc-sm.btn-delete-sm:hover {
        background: var(--danger-light);
    }

    .doc-card .doc-actions-overlay .btn-doc-sm.btn-download-sm {
        color: var(--success);
    }

    .doc-card .doc-actions-overlay .btn-doc-sm.btn-download-sm:hover {
        background: var(--success-light);
    }

    .doc-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }

    .doc-empty .doc-empty-icon {
        font-size: 48px;
        display: block;
        margin-bottom: 10px;
        opacity: 0.3;
        color: var(--border-color);
    }

    .doc-empty p {
        font-size: 14px;
        margin-bottom: 4px;
        color: var(--text-secondary);
    }

    .doc-empty small {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* Quick Upload Area */
    .quick-upload-area {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: var(--bg-primary);
    }

    .quick-upload-area:hover {
        border-color: var(--accent);
        background: var(--accent-light);
    }

    .quick-upload-area .upload-icon-lg {
        font-size: 48px;
        color: var(--text-muted);
        display: block;
        margin-bottom: 10px;
    }

    .quick-upload-area:hover .upload-icon-lg {
        color: var(--accent);
    }

    .quick-upload-area p {
        font-size: 14px;
        color: var(--text-secondary);
        margin-bottom: 4px;
    }

    .quick-upload-area small {
        font-size: 12px;
        color: var(--text-muted);
    }

    .quick-upload-area .file-name {
        display: none;
        margin-top: 8px;
        padding: 6px 12px;
        background: var(--border-color);
        border-radius: 6px;
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* Upload Progress */
    .upload-progress {
        display: none;
        margin-top: 15px;
    }

    .upload-progress .progress {
        height: 8px;
        border-radius: 4px;
        background: var(--border-color);
        overflow: hidden;
    }

    .upload-progress .progress-bar {
        height: 100%;
        background: var(--accent);
        border-radius: 4px;
        transition: width 0.3s;
        width: 0%;
    }

    .upload-progress .progress-text {
        font-size: 12px;
        color: var(--text-muted);
        text-align: center;
        margin-top: 4px;
    }

    /* ============================================ */
    /* MODAL - UNIFIED */
    /* ============================================ */
    .modal-content {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-primary);
        border-radius: 8px 8px 0 0;
    }

    .modal-header .modal-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .modal-header .modal-title i {
        color: var(--accent);
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 14px 20px;
        border-top: 1px solid var(--border-color);
        background: var(--bg-primary);
        border-radius: 0 0 8px 8px;
    }

    .modal-header.bg-success .modal-title,
    .modal-header.bg-success .modal-title i {
        color: #ffffff;
    }

    .modal-header.bg-success .btn-close {
        filter: brightness(0) invert(1);
    }

    /* ============================================ */
    /* DATA TABLES - UNIFIED */
    /* ============================================ */
    .dataTables_wrapper {
        font-family: inherit;
        overflow-x: visible !important;
    }

    .dataTables_filter {
        float: right;
        margin-bottom: 16px;
    }

    .dataTables_filter label {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dataTables_filter input {
        width: 200px;
        padding: 6px 12px;
        border: 1.5px solid var(--border-color);
        border-radius: 6px;
        font-size: 12px;
        transition: all 0.2s;
        outline: none;
        font-family: inherit;
    }

    .dataTables_filter input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.08);
    }

    .dataTables_paginate {
        float: right;
        margin-top: 16px;
    }

    .dataTables_paginate .paginate_button {
        padding: 4px 10px !important;
        margin: 0 2px !important;
        border-radius: 4px !important;
        border: 1px solid var(--border-color) !important;
        background: #ffffff !important;
        color: var(--text-secondary) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        transition: all 0.2s;
        font-family: inherit !important;
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--accent) !important;
        border-color: var(--accent) !important;
        color: #ffffff !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background: var(--bg-primary) !important;
        border-color: var(--border-color) !important;
        color: var(--accent) !important;
    }

    .dataTables_info {
        float: left;
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 16px;
    }

    /* ============================================ */
    /* RESPONSIVE - UNIFIED */
    /* ============================================ */
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

        .doc-grid {
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
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
        
        .stat-value {
            font-size: 22px;
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
        
        .dataTables_filter input {
            width: 150px;
        }
        
        .btn-action .action-label {
            display: none;
        }
        
        .btn-action {
            padding: 4px 6px;
        }

        .doc-grid {
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        }

        .card-container {
            padding: 16px;
        }

        .stat-card {
            padding: 16px;
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
        
        .stat-value {
            font-size: 18px;
        }
        
        .btn-action {
            width: 28px;
            height: 28px;
            padding: 0;
            border-radius: 4px;
        }
        
        .btn-action i {
            font-size: 13px;
        }

        .doc-grid {
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 8px;
        }

        .doc-card {
            padding: 12px 8px;
        }

        .doc-card .doc-icon-large {
            font-size: 28px;
        }

        .doc-card .doc-name {
            font-size: 10px;
        }

        .quick-upload-area {
            padding: 25px 15px;
        }

        .quick-upload-area .upload-icon-lg {
            font-size: 36px;
        }

        .card-container {
            padding: 12px;
        }

        .stat-card {
            padding: 12px 16px;
        }

        .section-title {
            font-size: 11px;
        }
        
        .qcpd-module-header .header-left .module-icon {
            width: 28px;
            height: 28px;
            font-size: 14px;
        }
    }

    /* ============================================ */
    /* LEGEND - UNIFIED */
    /* ============================================ */
    .legend {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        font-size: 11px;
        color: var(--text-muted);
        padding-top: 12px;
        margin-top: 12px;
        border-top: 1px solid var(--border-color);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 4px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>

<div class="me-trans-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- QCPD UNIFORM HEADER -->
<!-- ============================================ -->
<div class="qcpd-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="ti ti-receipt"></i>
        </div>
        <div class="module-info">
            <h4>
                Transaction Management
                <span class="module-badge">Range Operations</span>
            </h4>
        </div>
    </div>
</div>


<!-- ============================================ -->
<!-- STAT CARDS - UNIFIED -->
<!-- ============================================ -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-receipt"></i> Total Transactions</div>
            <div class="stat-value"><?=$total_transactions;?></div>
            <div class="stat-sub">All transactions</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-clock"></i> Active</div>
            <div class="stat-value"><?=$total_active;?></div>
            <div class="stat-sub">On range now</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-check"></i> Completed</div>
            <div class="stat-value"><?=$total_completed;?></div>
            <div class="stat-sub">Finished sessions</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-currency-peso"></i> Revenue</div>
            <div class="stat-value">₱<?=number_format($total_revenue, 2);?></div>
            <div class="stat-sub">Total collected</div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- RANGE AVAILABILITY - UNIFIED -->
<!-- ============================================ -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card-container">
            <div class="section-title">
                <i class="ti ti-layout-grid"></i> Range Availability 
                <span class="badge-count"><?=$available_bays;?> Available</span>
                <span class="badge-count" style="background: var(--success); color: white; border-color: var(--success);">
                    Available
                </span>
                <span class="badge-count" style="background: var(--danger); color: white; border-color: var(--danger);">
                    Occupied
                </span>
                <span class="badge-count" style="background: var(--warning); color: white; border-color: var(--warning);">
                    Reserved
                </span>
                <span class="badge-count" style="background: var(--text-muted); color: white; border-color: var(--text-muted);">
                    Maintenance
                </span>
            </div>
            
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
            
            <div class="legend">
                <span class="legend-item"><span class="legend-dot" style="background: var(--success);"></span> Available</span>
                <span class="legend-item"><span class="legend-dot" style="background: var(--danger);"></span> Occupied</span>
                <span class="legend-item"><span class="legend-dot" style="background: var(--warning);"></span> Reserved</span>
                <span class="legend-item"><span class="legend-dot" style="background: var(--text-muted);"></span> Maintenance</span>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TRANSACTIONS & FORM - UNIFIED -->
<!-- ============================================ -->
<div class="row g-4">
    <!-- TRANSACTIONS TABLE -->
    <div class="col-md-8">
        <div class="card-container">
            <div class="section-title">
                <i class="ti ti-list"></i> Transactions 
                <span class="badge-count"><?=count($transactions);?> records</span>
            </div>

            <div class="table-responsive">
                <table id="transTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Shooter</th>
                            <th>Type</th>
                            <th>Stage</th>
                            <th>Check-in</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th width="300">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($transactions as $row): ?>
                        <tr>
                            <td><strong><?=$row['transaction_id'];?></strong></td>
                            <td><?=date('m/d/Y', strtotime($row['transaction_date']));?></td>
                            <td><?=htmlspecialchars($row['shooter_name']);?></td>
                            <td>
                                <span class="badge <?=$row['shooter_type'] == 'PNP' ? 'bg-primary' : 'bg-secondary';?>">
                                    <?=$row['shooter_type'];?>
                                </span>
                            </td>
                            <td>Stage <?=$row['stage_id'];?></td>
                            <td><?=date('h:i A', strtotime($row['checkin_time']));?></td>
                            <td>₱<?=number_format($row['total_amount'], 2);?></td>
                            <td>
                                <?php if($row['status'] == 'ACTIVE'): ?>
                                    <span class="status-badge active">● Active</span>
                                <?php elseif($row['status'] == 'COMPLETED'): ?>
                                    <span class="status-badge completed">● Completed</span>
                                <?php else: ?>
                                    <span class="status-badge cancelled">● Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-wrapper">
                                    <!-- View/Edit Button -->
                                    <button class="btn-action btn-view" 
                                            onclick="viewEditTransaction(<?=$row['transaction_id'];?>)" 
                                            title="View & Edit Details">
                                        <i class="ti ti-file"></i>
                                        <span class="action-label">Details</span>
                                    </button>
                                    
                                    <!-- Documents Button -->
                                    <button class="btn-action btn-docs" 
                                            onclick="viewDocuments(<?=$row['transaction_id'];?>, '<?=addslashes($row['shooter_name']);?>')" 
                                            title="Manage Documents">
                                        <i class="ti ti-files"></i>
                                        <span class="action-label">Docs</span>
                                    </button>
                                    
                                    <!-- Checkout Button -->
                                    <?php if($row['status'] == 'ACTIVE'): ?>
                                    <button class="btn-action btn-checkout" 
                                            onclick="checkoutTransaction(<?=$row['transaction_id'];?>)" 
                                            title="Checkout">
                                        <i class="ti ti-logout"></i>
                                        <span class="action-label">Checkout</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ADD TRANSACTION FORM - UNIFIED -->
    <div class="col-md-4">
        <div class="card-container">
            <div class="section-title">
                <i class="ti ti-plus"></i> New Transaction
            </div>

            <form class="trans-reg-form" id="transRegForm">
                <div class="mb-3">
                    <label class="form-label">Transaction Date</label>
                    <input type="date" id="transaction_date" class="form-control" name="transaction_date" value="<?=date('Y-m-d');?>" required>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Check-in</label>
                        <input type="time" id="checkin_time" class="form-control" name="checkin_time" value="<?=date('H:i');?>" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Check-out</label>
                        <input type="time" id="checkout_time" class="form-control" name="checkout_time">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stage (1-10)</label>
                    <select name="stage_id" id="stage_id" class="form-control" required>
                        <option value="">Select Stage</option>
                        <?php for($i = 1; $i <= 10; $i++): 
                            $is_available = false;
                            foreach($bay_statuses as $bay) {
                                if($bay['stage_id'] == $i && $bay['status'] == 'AVAILABLE') {
                                    $is_available = true;
                                    break;
                                }
                            }
                        ?>
                            <option value="<?=$i;?>" <?=!$is_available ? 'disabled style="color: #999;"' : '';?>>
                                Stage <?=$i;?> <?=!$is_available ? '(Occupied)' : '(Available)';?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <small class="text-muted" style="font-size: 10px; color: var(--text-muted);">Only available stages are selectable</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Shooter Type</label>
                    <select name="shooter_type" id="shooter_type" class="form-control" required>
                        <option value="">Select Type</option>
                        <option value="PNP">PNP</option>
                        <option value="CIVILIAN">Civilian</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Shooter Name</label>
                    <input type="text" id="shooter_name" class="form-control" name="shooter_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Range Assistant</label>
                    <select name="range_assistant_id" id="range_assistant_id" class="form-control">
                        <option value="">Select Assistant</option>
                        <?php foreach($range_assistants as $ra): ?>
                            <option value="<?=$ra['assistant_id'];?>"><?=htmlspecialchars($ra['full_name']);?> (<?=htmlspecialchars($ra['badge_number']);?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Range Fee</label>
                        <input type="number" id="rangefee_amount" class="form-control" name="rangefee_amount" step="0.01" value="300" onchange="calculateTotal()" onkeyup="calculateTotal()">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Target Board</label>
                        <input type="number" id="targetboard_amount" class="form-control" name="targetboard_amount" step="0.01" value="0.00" onchange="calculateTotal()" onkeyup="calculateTotal()">
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Ammunition</label>
                        <input type="number" id="ammunition_amount" class="form-control" name="ammunition_amount" step="0.01" value="0.00" onchange="calculateTotal()" onkeyup="calculateTotal()">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Total Amount</label>
                        <input type="text" id="total_amount" class="form-control" name="total_amount" readonly style="background: var(--bg-primary); font-weight: 700; color: var(--accent); font-family: var(--mono);">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                </div>

                <button type="submit" class="btn btn-danger w-100">
                    <i class="ti ti-device-floppy me-1"></i>
                    Save Transaction
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- VIEW/EDIT TRANSACTION MODAL - UNIFIED -->
<!-- ============================================ -->
<div class="modal fade" id="viewEditModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-file me-2"></i> Transaction Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewEditModalBody">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- DOCUMENT MANAGEMENT MODAL - UNIFIED -->
<!-- ============================================ -->
<div class="modal fade" id="documentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-files me-2"></i> Document Management
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="documentModalBody">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- QUICK DOCUMENT UPLOAD MODAL - UNIFIED -->
<!-- ============================================ -->
<div class="modal fade" id="quickUploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-upload me-2"></i> Upload Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="quick_upload_transaction_id">
                
                <div class="mb-3">
                    <label class="form-label">Document Type</label>
                    <select id="quick_document_type" class="form-control">
                        <option value="VALID_ID">Valid ID</option>
                        <option value="LTOPF">LTOPF</option>
                        <option value="FIREARM_REG">Firearm Registration</option>
                        <option value="PTCFOR">PTCFOR</option>
                        <option value="SHOOTER_PHOTO">Shooter Photo</option>
                    </select>
                </div>

                <div class="quick-upload-area" onclick="document.getElementById('quickFileInput').click()">
                    <i class="ti ti-cloud-upload upload-icon-lg"></i>
                    <p>Click to select a file</p>
                    <small>Supported: JPG, PNG, GIF, PDF (Max 5MB)</small>
                    <div class="file-name" id="selectedFileName"></div>
                    <input type="file" id="quickFileInput" style="display: none;" accept=".jpg,.jpeg,.png,.gif,.pdf" onchange="handleFileSelect()">
                </div>

                <div class="upload-progress" id="uploadProgress">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="uploadProgressBar" style="width: 0%;"></div>
                    </div>
                    <div class="progress-text" id="uploadProgressText">Uploading...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmUploadBtn" onclick="quickUploadDocument()" style="display: none;">
                    <i class="ti ti-upload me-1"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- CHECKOUT CONFIRMATION MODAL - UNIFIED -->
<!-- ============================================ -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">
                    <i class="ti ti-logout me-2"></i> Confirm Checkout
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <i class="ti ti-clock" style="font-size: 48px; color: var(--success);"></i>
                    <h4 class="mt-3" style="color: var(--text-primary);">Complete Session?</h4>
                    <p class="text-muted" style="color: var(--text-muted);">You are about to checkout shooter: <br>
                        <strong id="checkout_shooter_name" class="text-success" style="color: var(--success);"></strong>
                    </p>
                    <p class="text-muted" style="color: var(--text-muted);">Stage will become available for next shooter.</p>
                    <div class="mt-3">
                        <label class="form-label">Checkout Time</label>
                        <input type="time" id="checkout_time_modal" class="form-control" value="<?=date('H:i');?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmCheckoutBtn">
                    <i class="ti ti-logout"></i> Checkout
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
    $('#transTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']],
        language: {
            search: "Search Transaction:"
        }
    });

    $('#confirmCheckoutBtn').on('click', function() {
        confirmCheckout();
        var modal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
        modal.hide();
    });
});

function calculateTotal() {
    var range_fee = parseFloat($('#rangefee_amount').val()) || 0;
    var target_board = parseFloat($('#targetboard_amount').val()) || 0;
    var ammunition = parseFloat($('#ammunition_amount').val()) || 0;
    var total = range_fee + target_board + ammunition;
    $('#total_amount').val(total.toFixed(2));
}

// ============================================ //
// VIEW/EDIT TRANSACTION                        //
// ============================================ //
function viewEditTransaction(id) {
    var mparam = {
        transaction_id: id,
        meaction: 'GET_TRANSACTION'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>transactions',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data) {
                var html = generateViewEditHTML(data);
                $('#viewEditModalBody').html(html);
                var viewEditModal = new bootstrap.Modal(document.getElementById('viewEditModal'));
                viewEditModal.show();
            } else {
                toastr.error('Transaction not found');
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error: " + error);
        }
    });
}

// ============================================ //
// GENERATE VIEW/EDIT HTML                      //
// ============================================ //
function generateViewEditHTML(data) {
    var statusBadge = data.status == 'ACTIVE' ? 'bg-success' : (data.status == 'COMPLETED' ? 'bg-secondary' : 'bg-danger');
    var bayStatus = data.bay_status || 'N/A';
    var bayStatusBadge = bayStatus == 'AVAILABLE' ? 'bg-success' : (bayStatus == 'OCCUPIED' ? 'bg-danger' : (bayStatus == 'RESERVED' ? 'bg-warning' : 'bg-secondary'));
    var isEditable = (data.status == 'ACTIVE' || data.status == 'COMPLETED');
    
    return `
        <div class="row">
            <!-- LEFT COLUMN - Transaction Info -->
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3" style="color: var(--text-primary);"><i class="ti ti-info-circle me-2" style="color: var(--accent);"></i>Transaction Information</h6>
                <table class="table table-sm">
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Transaction ID:</td><td style="color: var(--text-primary);">#${data.transaction_id}</td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Date:</td><td style="color: var(--text-primary);">${data.transaction_date}</td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Check-in:</td><td style="color: var(--text-primary);">${data.checkin_time}</td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Check-out:</td><td style="color: var(--text-primary);">${data.checkout_time || '—'}</td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Stage:</td><td style="color: var(--text-primary);">Stage ${data.stage_id}</td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Bay Status:</td><td><span class="badge ${bayStatusBadge}">${bayStatus}</span></td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Shooter:</td><td style="color: var(--text-primary);">${data.shooter_name}</td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Shooter Type:</td><td><span class="badge ${data.shooter_type == 'PNP' ? 'bg-primary' : 'bg-secondary'}">${data.shooter_type}</span></td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Assistant:</td><td style="color: var(--text-primary);">${data.assistant_name || '—'}</td></tr>
                    <tr><td style="font-weight: 600; color: var(--text-secondary);">Status:</td><td><span class="badge ${statusBadge}">${data.status}</span></td></tr>
                </table>
            </div>
            
            <!-- RIGHT COLUMN - Payment Info & Edit -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0" style="color: var(--text-primary);"><i class="ti ti-currency-peso me-2" style="color: var(--accent);"></i>Payment Details</h6>
                    ${isEditable ? '<span class="badge bg-warning">Editable</span>' : '<span class="badge bg-secondary">Read Only</span>'}
                </div>
                
                <form id="editPaymentForm">
                    <input type="hidden" id="edit_transaction_id" value="${data.transaction_id}">
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Range Fee</label>
                            <input type="number" id="edit_rangefee_amount" class="form-control" step="0.01" 
                                   value="${data.rangefee_amount}" ${!isEditable ? 'readonly' : ''}
                                   onchange="calculateEditTotal()" onkeyup="calculateEditTotal()">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Target Board</label>
                            <input type="number" id="edit_targetboard_amount" class="form-control" step="0.01" 
                                   value="${data.targetboard_amount}" ${!isEditable ? 'readonly' : ''}
                                   onchange="calculateEditTotal()" onkeyup="calculateEditTotal()">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Ammunition</label>
                            <input type="number" id="edit_ammunition_amount" class="form-control" step="0.01" 
                                   value="${data.ammunition_amount}" ${!isEditable ? 'readonly' : ''}
                                   onchange="calculateEditTotal()" onkeyup="calculateEditTotal()">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Total Amount</label>
                            <input type="text" id="edit_total_amount" class="form-control" readonly 
                                   style="background: var(--bg-primary); font-weight: 700; color: var(--accent); font-family: var(--mono);"
                                   value="${parseFloat(data.total_amount).toFixed(2)}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea id="edit_notes" class="form-control" rows="2" ${!isEditable ? 'readonly' : ''}>${data.notes || ''}</textarea>
                    </div>

                    ${isEditable ? `
                    <button type="button" class="btn btn-primary w-100" onclick="updatePayment()">
                        <i class="ti ti-device-floppy me-1"></i> Update Payment
                    </button>
                    ` : ''}
                </form>
            </div>
        </div>
    `;
}

// ============================================ //
// VIEW DOCUMENTS - Separate Modal              //
// ============================================ //
function viewDocuments(transaction_id, shooter_name) {
    var mparam = {
        transaction_id: transaction_id,
        meaction: 'GET_TRANSACTION'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>transactions',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data) {
                var html = generateDocumentManagementHTML(data);
                $('#documentModalBody').html(html);
                var docModal = new bootstrap.Modal(document.getElementById('documentModal'));
                docModal.show();
                
                // Load documents
                loadDocumentsGridSeparate(transaction_id);
            } else {
                toastr.error('Transaction not found');
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error: " + error);
        }
    });
}

// ============================================ //
// GENERATE DOCUMENT MANAGEMENT HTML            //
// ============================================ //
function generateDocumentManagementHTML(data) {
    return `
        <div class="row mb-3">
            <div class="col-md-6">
                <p style="color: var(--text-secondary);"><strong style="color: var(--text-primary);">Transaction:</strong> #${data.transaction_id}</p>
                <p style="color: var(--text-secondary);"><strong style="color: var(--text-primary);">Shooter:</strong> ${data.shooter_name}</p>
                <p style="color: var(--text-secondary);"><strong style="color: var(--text-primary);">Stage:</strong> Stage ${data.stage_id}</p>
            </div>
            <div class="col-md-6 text-end">
                <button class="doc-upload-btn" onclick="openQuickUploadSeparate(${data.transaction_id})">
                    <i class="ti ti-upload"></i> Upload Document
                </button>
            </div>
        </div>
        <hr style="border-color: var(--border-color);">
        <div id="documentGridSeparate">
            <div class="doc-empty">
                <i class="ti ti-file doc-empty-icon"></i>
                <p>Loading documents...</p>
            </div>
        </div>
    `;
}

// ============================================ //
// LOAD DOCUMENTS GRID - Separate              //
// ============================================ //
function loadDocumentsGridSeparate(transaction_id) {
    var mparam = {
        transaction_id: transaction_id,
        meaction: 'GET_DOCUMENTS'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>transactions',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            var html = '';
            if(data.length > 0) {
                var docTypeLabels = {
                    'VALID_ID': 'Valid ID',
                    'LTOPF': 'LTOPF',
                    'FIREARM_REG': 'Firearm Registration',
                    'PTCFOR': 'PTCFOR',
                    'SHOOTER_PHOTO': 'Shooter Photo'
                };
                
                var docIcons = {
                    'VALID_ID': 'ti ti-id',
                    'LTOPF': 'ti ti-file',
                    'FIREARM_REG': 'ti ti-shield',
                    'PTCFOR': 'ti ti-certificate',
                    'SHOOTER_PHOTO': 'ti ti-user'
                };
                
                html = '<div class="doc-grid">';
                data.forEach(function(doc) {
                    var icon = docIcons[doc.document_type] || 'ti ti-file';
                    var label = docTypeLabels[doc.document_type] || doc.document_type;
                    
                    html += `
                        <div class="doc-card">
                            <i class="${icon} doc-icon-large"></i>
                            <span class="doc-name">${doc.document_name}</span>
                            <br>
                            <span class="doc-type-badge">${label}</span>
                            <div class="doc-actions-overlay">
                                <a href="${doc.document_path}" target="_blank" class="btn-doc-sm btn-view-sm" title="View">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="${doc.document_path}" download class="btn-doc-sm btn-download-sm" title="Download">
                                    <i class="ti ti-download"></i>
                                </a>
                                <button class="btn-doc-sm btn-delete-sm" onclick="deleteDocumentFromGridSeparate(${doc.document_id}, ${transaction_id})" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html = `
                    <div class="doc-empty">
                        <i class="ti ti-file doc-empty-icon"></i>
                        <p>No documents uploaded yet</p>
                        <small>Click "Upload Document" to add files</small>
                    </div>
                `;
            }
            $('#documentGridSeparate').html(html);
        },
        error: function(xhr, status, error) {
            toastr.error("Error loading documents: " + error);
        }
    });
}

// ============================================ //
// CALCULATE EDIT TOTAL                         //
// ============================================ //
function calculateEditTotal() {
    var range_fee = parseFloat($('#edit_rangefee_amount').val()) || 0;
    var target_board = parseFloat($('#edit_targetboard_amount').val()) || 0;
    var ammunition = parseFloat($('#edit_ammunition_amount').val()) || 0;
    var total = range_fee + target_board + ammunition;
    $('#edit_total_amount').val(total.toFixed(2));
}

// ============================================ //
// UPDATE PAYMENT                               //
// ============================================ //
function updatePayment() {
    var transaction_id = $('#edit_transaction_id').val();
    var rangefee_amount = $('#edit_rangefee_amount').val() || 0;
    var targetboard_amount = $('#edit_targetboard_amount').val() || 0;
    var ammunition_amount = $('#edit_ammunition_amount').val() || 0;
    var total_amount = $('#edit_total_amount').val() || 0;
    var notes = $('#edit_notes').val();

    var mparam = {
        transaction_id: transaction_id,
        rangefee_amount: rangefee_amount,
        targetboard_amount: targetboard_amount,
        ammunition_amount: ammunition_amount,
        total_amount: total_amount,
        notes: notes,
        meaction: 'EDIT_PAYMENT'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>transactions',
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

// ============================================ //
// QUICK DOCUMENT UPLOAD - Separate            //
// ============================================ //
function openQuickUploadSeparate(transaction_id) {
    resetUploadModal();
    $('#quick_upload_transaction_id').val(transaction_id);
    var modal = new bootstrap.Modal(document.getElementById('quickUploadModal'));
    modal.show();
}

function handleFileSelect() {
    var file = document.getElementById('quickFileInput').files[0];
    if(file) {
        $('#selectedFileName').text('📄 ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)').show();
        $('#confirmUploadBtn').show();
    }
}

function resetUploadModal() {
    document.getElementById('quickFileInput').value = '';
    $('#selectedFileName').text('').hide();
    $('#uploadProgress').hide();
    $('#uploadProgressBar').css('width', '0%');
    $('#uploadProgressText').text('Uploading...');
    $('#confirmUploadBtn').prop('disabled', false).hide();
}

function quickUploadDocument() {
    var file = document.getElementById('quickFileInput').files[0];
    if(!file) {
        toastr.warning('Please select a file.');
        return;
    }

    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        toastr.error('File size exceeds 5MB limit.');
        resetUploadModal();
        return;
    }

    // Validate file type
    var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    var ext = file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(ext)) {
        toastr.error('Only JPG, PNG, GIF, and PDF files are allowed.');
        resetUploadModal();
        return;
    }

    var transaction_id = $('#quick_upload_transaction_id').val();
    var document_type = $('#quick_document_type').val();
    
    var formData = new FormData();
    formData.append('transaction_id', transaction_id);
    formData.append('document_type', document_type);
    formData.append('document_file', file);
    formData.append('meaction', 'UPLOAD_DOCUMENT');

    // Show progress
    $('#uploadProgress').show();
    $('#uploadProgressBar').css('width', '50%');
    $('#uploadProgressText').text('Uploading...');
    $('#confirmUploadBtn').prop('disabled', true).hide();

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>transactions',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        timeout: 30000,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percent = Math.round((e.loaded / e.total) * 100);
                    $('#uploadProgressBar').css('width', percent + '%');
                    $('#uploadProgressText').text('Uploading... ' + percent + '%');
                }
            });
            return xhr;
        },
        success: function(data) {
            $('#uploadProgressBar').css('width', '100%');
            $('#uploadProgressText').text('Complete!');
            
            setTimeout(function() {
                $('#uploadProgress').hide();
                if(data.status == 'success'){
                    toastr.success(data.message);
                    // Reload documents
                    loadDocumentsGridSeparate(transaction_id);
                    // Reset the upload modal completely for next upload
                    resetUploadModal();
                } else {
                    toastr.error(data.message || 'Upload failed. Please try again.');
                }
            }, 500);
        },
        error: function(xhr, status, error) {
            $('#uploadProgress').hide();
            var errorMsg = 'Upload failed: ' + (error || status);
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg);
            console.error('Upload error:', xhr, status, error);
            $('#confirmUploadBtn').prop('disabled', false).show();
        }
    });
}

// ============================================ //
// DELETE DOCUMENT FROM GRID - Separate        //
// ============================================ //
function deleteDocumentFromGridSeparate(document_id, transaction_id) {
    if(!confirm('Are you sure you want to delete this document?')) return;

    var mparam = {
        document_id: document_id,
        meaction: 'DELETE_DOCUMENT'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>transactions',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data.status == 'success'){
                toastr.success(data.message);
                loadDocumentsGridSeparate(transaction_id);
            } else {
                toastr.error(data.message);
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error: " + error);
        }
    });
}

// ============================================ //
// CHECKOUT FUNCTIONS                           //
// ============================================ //
var checkoutId = null;
var checkoutName = '';

function checkoutTransaction(id) {
    var row = $(event.target).closest('tr');
    var name = row.find('td:eq(2)').text().trim();
    
    checkoutId = id;
    checkoutName = name;
    document.getElementById('checkout_shooter_name').innerHTML = name;
    document.getElementById('checkout_time_modal').value = new Date().toTimeString().slice(0, 5);
    
    var checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    checkoutModal.show();
}

function confirmCheckout() {
    if(checkoutId) {
        var checkout_time = document.getElementById('checkout_time_modal').value;
        
        var mparam = {
            transaction_id: checkoutId,
            checkout_time: checkout_time,
            meaction: 'CHECKOUT'
        };

        jQuery.ajax({
            type: "POST",
            url: '<?=site_url();?>transactions',
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

<script src="<?=base_url('assets/js/transactions/trans.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>