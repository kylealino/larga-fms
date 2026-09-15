<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("SELECT * FROM tbl_helpers ORDER BY helper_id DESC");
$helpers = $query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_helpers = count($helpers);
$total_available = $this->db->query("SELECT COUNT(*) as total FROM tbl_helpers WHERE helper_status = 'AVAILABLE'")->getRow()->total;
$total_on_trip = $this->db->query("SELECT COUNT(*) as total FROM tbl_helpers WHERE helper_status IN ('ASSIGNED', 'ON TRIP')")->getRow()->total;
$total_inactive = $this->db->query("SELECT COUNT(*) as total FROM tbl_helpers WHERE helper_status IN ('INACTIVE', 'ON LEAVE')")->getRow()->total;

echo view('templates/myheader.php');
?>
<style>
    /* ============================================ */
    /* HELPER MODULE - PROFESSIONAL UX APPROACH */
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

    .form-control-file {
        border: 1.5px dashed var(--gray-300);
        border-radius: 8px;
        padding: 10px;
        background: #ffffff;
        width: 100%;
        transition: all 0.2s;
        font-size: 13px;
    }

    .form-control-file:hover {
        border-color: var(--primary);
        background: var(--gray-50);
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

    /* ============================================ */
    /* PROFILE PICTURE */
    /* ============================================ */
    .profile-pic-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--gray-200);
        background: var(--gray-100);
    }

    .profile-pic-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid var(--gray-200);
        background: var(--gray-100);
    }

    .profile-pic-preview {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--gray-200);
        background: var(--gray-100);
    }

    /* ============================================ */
    /* LICENSE ATTACHMENT PREVIEW */
    /* ============================================ */
    .license-preview-container {
        position: relative;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        overflow: hidden;
        max-width: 400px;
        margin-top: 5px;
        background: var(--gray-50);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .license-preview-container:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .license-preview-container .license-preview-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.6);
        color: #ffffff;
        text-align: center;
        padding: 6px 0;
        font-size: 11px;
        font-weight: 500;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .license-preview-container:hover .license-preview-overlay {
        opacity: 1;
    }

    .license-preview-container .license-preview-overlay i {
        margin-right: 4px;
    }

    .license-preview-img {
        width: 100%;
        max-height: 250px;
        object-fit: contain;
        display: block;
    }

    .license-preview-pdf {
        width: 100%;
        height: 250px;
        display: block;
        border: none;
    }

    .license-preview-file {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        gap: 8px;
    }

    .license-preview-file .file-name {
        font-size: 13px;
        color: var(--gray-700);
        word-break: break-all;
        text-align: center;
    }

    .license-preview-file .file-size {
        font-size: 11px;
        color: var(--gray-400);
    }

    /* ============================================ */
    /* LICENSE ATTACHMENT PREVIEW IN MODAL */
    /* ============================================ */
    .license-preview-modal {
        max-height: 80vh;
        overflow: auto;
    }

    .license-preview-modal img {
        max-width: 100%;
        height: auto;
    }

    .license-preview-modal iframe {
        width: 100%;
        height: 80vh;
        border: none;
    }

    /* ============================================ */
    /* DATA TABLES */
    /* ============================================ */
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

    .license-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 1px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 500;
    }

    .license-badge.has-license { background: #d1fae5; color: #065f46; }
    .license-badge.no-license { background: #fef3c7; color: #92400e; }

    .file-upload-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .file-upload-wrapper .file-name {
        font-size: 12px;
        color: var(--gray-500);
    }

    /* ============================================ */
    /* LICENSE VIEW MODAL ZOOM */
    /* ============================================ */
    .license-zoom-modal .modal-dialog {
        max-width: 90%;
        max-height: 90vh;
    }

    .license-zoom-modal .modal-body {
        padding: 0;
        overflow: auto;
        max-height: 85vh;
        background: #1a1a2e;
        border-radius: 0 0 16px 16px;
    }

    .license-zoom-modal .modal-body img,
    .license-zoom-modal .modal-body embed,
    .license-zoom-modal .modal-body iframe {
        width: 100%;
        height: auto;
        max-height: 85vh;
        object-fit: contain;
    }

    .license-zoom-modal .modal-body iframe {
        height: 85vh;
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
        .license-preview-container {
            max-width: 100%;
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
        .profile-pic-circle {
            width: 32px;
            height: 32px;
        }
        .license-preview-pdf {
            height: 150px;
        }
    }
</style>

<div class="me-hlp-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon">
            <i class="bi bi-person-plus"></i>
        </div>
        <div class="module-info">
            <h4>
                Helper Management
                <span class="module-badge">Personnel</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="openAddHelper()">
            <i class="bi bi-plus-circle"></i> New Helper
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS CARDS - CLICKABLE -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Helpers</div>
            <div class="stat-value"><?=$total_helpers;?></div>
            <div class="stat-sub">All helpers <span class="active-filter-badge" id="filterBadgeAll" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-people"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
    <div class="stat-card" data-filter="AVAILABLE" onclick="filterTable('AVAILABLE')">
        <div class="stat-left">
            <div class="stat-label">Available</div>
            <div class="stat-value"><?=$total_available;?></div>
            <div class="stat-sub">Ready for assignment <span class="active-filter-badge" id="filterBadgeAvailable" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-person-check"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
    <div class="stat-card" data-filter="ON TRIP" onclick="filterTable('ON TRIP')">
        <div class="stat-left">
            <div class="stat-label">On Trip</div>
            <div class="stat-value"><?=$total_on_trip;?></div>
            <div class="stat-sub">Currently assigned <span class="active-filter-badge" id="filterBadgeOnTrip" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-truck"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
    <div class="stat-card" data-filter="INACTIVE" onclick="filterTable('INACTIVE')">
        <div class="stat-left">
            <div class="stat-label">Inactive</div>
            <div class="stat-value"><?=$total_inactive;?></div>
            <div class="stat-sub">Leave / Inactive <span class="active-filter-badge" id="filterBadgeInactive" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-person-x"></i></div>
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
                <h6><i class="bi bi-table me-2"></i>Helper Directory 
                    <span id="filterStatusDisplay" style="font-size:11px;font-weight:400;color:var(--gray-500);margin-left:8px;"></span>
                </h6>
                <div>
                    <span class="badge badge-secondary"><?=count($helpers);?> records</span>
                    <button class="btn-toolbar btn-toolbar-filter btn-sm ms-2" onclick="filterTable('all')" id="clearFilterBtn" style="display:none;">
                        <i class="bi bi-x"></i> Clear Filter
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <button class="btn-toolbar btn-toolbar-primary" onclick="openAddHelper()">
                            <i class="bi bi-plus-circle"></i> Add New
                        </button>
                        <button class="btn-toolbar" onclick="window.location.reload();">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <span class="text-muted" style="font-size:12px;">
                            <i class="bi bi-info-circle"></i> Click <strong>Edit</strong> to manage profile picture & license
                        </span>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="helperTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="60">Photo</th>
                                <th width="100">Code</th>
                                <th>Helper Name</th>
                                <th width="100">Contact</th>
                                <th width="120">Status</th>
                                <th width="100">License</th>
                                <th width="200" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($helpers) > 0): ?>
                                <?php foreach($helpers as $row): 
                                    $profile_pic = !empty($row['profile_picture']) ? base_url($row['profile_picture']) : base_url('assets/images/profile/user-1.jpg');
                                ?>
                                <tr>
                                    <td>
                                        <img src="<?=$profile_pic;?>" class="profile-pic-circle-sm" alt="Profile">
                                    </td>
                                    <td><span class="badge badge-primary"><?=$row['helper_code'];?></span></td>
                                    <td><strong><?=$row['helper_name'];?></strong></td>
                                    <td><?=$row['contact_number'] ?: '—';?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-secondary';
                                        $statusLabel = 'Inactive';
                                        if($row['helper_status'] == 'AVAILABLE') { $statusClass = 'badge-success'; $statusLabel = 'Available'; }
                                        elseif($row['helper_status'] == 'ASSIGNED') { $statusClass = 'badge-warning'; $statusLabel = 'Assigned'; }
                                        elseif($row['helper_status'] == 'ON TRIP') { $statusClass = 'badge-info'; $statusLabel = 'On Trip'; }
                                        elseif($row['helper_status'] == 'ON LEAVE') { $statusClass = 'badge-secondary'; $statusLabel = 'On Leave'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td>
                                        <?php if($row['has_license'] == 'YES'): ?>
                                            <span class="license-badge has-license"><i class="bi bi-check-circle"></i> Has License</span>
                                        <?php else: ?>
                                            <span class="license-badge no-license"><i class="bi bi-x-circle"></i> No License</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-view" 
                                                    onclick="viewHelper(<?=$row['helper_id'];?>)" 
                                                    title="View Helper">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-edit" 
                                                    onclick="editHelper(<?=$row['helper_id'];?>)" 
                                                    title="Edit Helper">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-delete" 
                                                    onclick="__Helpers.__showDeleteModal(<?=$row['helper_id'];?>, '<?=addslashes($row['helper_name']);?>')" 
                                                    title="Delete Helper">
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

                <?php if(count($helpers) == 0): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No helpers yet</h5>
                    <p>Click <strong>New Helper</strong> to add your first helper.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ADD / EDIT HELPER MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="helperModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helperModalTitle">
                    <i class="bi bi-person-plus me-2"></i>New Helper
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="helperForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="form_helper_id">
                    <input type="hidden" id="form_helper_code">
                    <input type="hidden" id="form_existing_profile" value="">
                    <input type="hidden" id="form_existing_license" value="">

                    <!-- Profile Picture -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center gap-4">
                                <div>
                                    <img id="profilePreview" src="<?=base_url('assets/images/profile/user-1.jpg');?>" class="profile-pic-preview" alt="Profile Preview">
                                </div>
                                <div>
                                    <label class="form-label">Profile Picture</label>
                                    <input type="file" id="form_profile_picture" class="form-control-file" accept=".jpg,.jpeg,.png,.gif" onchange="previewProfilePicture(this)">
                                    <small class="text-muted">Upload a photo (JPG, PNG, GIF). Leave blank to keep default.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Helper Code</label>
                            <input type="text" id="form_helper_code_display" class="form-control" readonly>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Helper Name <span class="required">*</span></label>
                            <input type="text" id="form_helper_name" class="form-control" placeholder="Enter helper name" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Contact Number</label>
                            <input type="text" id="form_contact_number" class="form-control" placeholder="Phone number">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Address</label>
                            <input type="text" id="form_address" class="form-control" placeholder="Address">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Employment Type</label>
                            <select id="form_employment_type" class="form-control">
                                <option value="">— Select —</option>
                                <option value="Regular">Regular</option>
                                <option value="Probationary">Probationary</option>
                                <option value="Contractual">Contractual</option>
                                <option value="Casual">Casual</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date Hired</label>
                            <input type="date" id="form_date_hired" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Helper Status</label>
                            <select id="form_helper_status" class="form-control">
                                <option value="AVAILABLE">Available</option>
                                <option value="ASSIGNED">Assigned</option>
                                <option value="ON TRIP">On Trip</option>
                                <option value="ON LEAVE">On Leave</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Emergency Contact</label>
                            <input type="text" id="form_emergency_contact" class="form-control" placeholder="Emergency contact name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Emergency Contact Number</label>
                            <input type="text" id="form_emergency_contact_number" class="form-control" placeholder="Emergency phone number">
                        </div>
                    </div>

                    <!-- Optional Driver's License Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-credit-card"></i> Optional Driver's License
                            <span class="badge badge-secondary" style="font-size:8px;">Optional</span>
                        </div>
                        <p class="text-muted" style="font-size:12px;">Only fill this out if the helper is also authorized to drive.</p>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">License Number</label>
                                <input type="text" id="form_license_number" class="form-control" placeholder="License #">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">License Type</label>
                                <select id="form_license_type" class="form-control">
                                    <option value="">— Select —</option>
                                    <option value="Professional">Professional</option>
                                    <option value="Non-Professional">Non-Professional</option>
                                    <option value="Student">Student</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Restriction Code</label>
                                <input type="text" id="form_restriction_code" class="form-control" placeholder="e.g., 1,2,3">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Expiration Date</label>
                                <input type="date" id="form_expiration_date" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-12">
                                <label class="form-label">License Attachment</label>
                                <input type="file" id="form_license_attachment" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Upload license image or PDF (optional)</small>
                                <div id="licenseAttachmentPreview" style="display:none;margin-top:6px;">
                                    <span class="badge badge-success"><i class="bi bi-file-earmark-check"></i> <span id="licenseFileName"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">
                        <i class="bi bi-save"></i> <span id="formBtnText">Save Helper</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- VIEW HELPER MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i>Helper Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewHelperContent">
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
<!-- LICENSE ZOOM MODAL -->
<!-- ============================================ -->
<div class="modal fade license-zoom-modal" id="licenseZoomModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark me-2"></i>License Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="licenseZoomContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="licenseDownloadLink" class="btn btn-primary" download>
                    <i class="bi bi-download"></i> Download
                </a>
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
                <h5 class="mt-3">Delete Helper</h5>
                <p class="text-muted">You are about to delete:<br>
                    <strong id="delete_helper_name" class="text-danger"></strong>
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
var helperTable;

$(document).ready(function () {
    helperTable = $('#helperTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[1, 'desc']],
        language: {
            search: "Search Helper:",
            emptyTable: "No helpers found"
        },
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ]
    });

    $('#confirmDeleteBtn').on('click', function() {
        __Helpers.__deleteHelper();
        var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
    });

    $('#helperForm').on('submit', function(e) {
        e.preventDefault();
        var helper_id = $('#form_helper_id').val();
        if(helper_id) {
            __Helpers.__updateHelper();
        } else {
            __Helpers.__saveHelper();
        }
    });
});

// =============================================
// FILTER TABLE BY STATUS - FIXED
// =============================================
function filterTable(status) {
    $('.stat-card').removeClass('active');
    $('.active-filter-badge').hide();
    $('#clearFilterBtn').hide();
    $('#filterStatusDisplay').text('');
    
    // Column index for status is column 4 (0-based index)
    // Columns: Photo(0), Code(1), Name(2), Contact(3), Status(4), License(5), Actions(6)
    var columnIndex = 4;
    
    if(status === 'all') {
        helperTable.column(columnIndex).search('').draw();
        $('.stat-card[data-filter="all"]').addClass('active');
        $('#filterBadgeAll').show();
        $('#filterStatusDisplay').text('(Showing all helpers)');
        $('#clearFilterBtn').show();
    } else if(status === 'AVAILABLE') {
        helperTable.column(columnIndex).search('Available').draw();
        $('.stat-card[data-filter="AVAILABLE"]').addClass('active');
        $('#filterBadgeAvailable').show();
        $('#filterStatusDisplay').text('(Filtered: Available)');
        $('#clearFilterBtn').show();
    } else if(status === 'ON TRIP') {
        // Search for both "Assigned" and "On Trip" statuses
        helperTable.column(columnIndex).search('Assigned|On Trip', true, false).draw();
        $('.stat-card[data-filter="ON TRIP"]').addClass('active');
        $('#filterBadgeOnTrip').show();
        $('#filterStatusDisplay').text('(Filtered: On Trip / Assigned)');
        $('#clearFilterBtn').show();
    } else if(status === 'INACTIVE') {
        helperTable.column(columnIndex).search('On Leave|Inactive', true, false).draw();
        $('.stat-card[data-filter="INACTIVE"]').addClass('active');
        $('#filterBadgeInactive').show();
        $('#filterStatusDisplay').text('(Filtered: Inactive / On Leave)');
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
// PROFILE PICTURE PREVIEW
// =============================================
function previewProfilePicture(input) {
    var preview = document.getElementById('profilePreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// =============================================
// OPEN ZOOM MODAL FOR LICENSE
// =============================================
function openLicenseZoom(fileUrl) {
    var fileExt = fileUrl.split('.').pop().toLowerCase();
    var content = '';
    
    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(fileExt)) {
        content = '<img src="' + fileUrl + '" alt="License Document" style="width:100%;height:auto;max-height:85vh;object-fit:contain;">';
    } else if (['pdf'].includes(fileExt)) {
        content = '<embed src="' + fileUrl + '#toolbar=1" style="width:100%;height:85vh;" type="application/pdf">';
    } else {
        content = '<div class="text-center py-5"><i class="bi bi-file-earmark" style="font-size:64px;color:var(--gray-400);"></i><p class="mt-3">Preview not available for this file type.</p></div>';
    }
    
    $('#licenseZoomContent').html(content);
    $('#licenseDownloadLink').attr('href', fileUrl);
    
    var modal = new bootstrap.Modal(document.getElementById('licenseZoomModal'));
    modal.show();
}

// =============================================
// OPEN ADD HELPER
// =============================================
function openAddHelper() {
    $('#helperModalTitle').html('<i class="bi bi-person-plus me-2"></i>New Helper');
    $('#formBtnText').text('Save Helper');
    $('#form_helper_id').val('');
    $('#form_helper_code').val('');
    $('#form_helper_code_display').val('Auto-generated');
    $('#form_helper_name').val('');
    $('#form_contact_number').val('');
    $('#form_address').val('');
    $('#form_employment_type').val('');
    $('#form_date_hired').val('');
    $('#form_helper_status').val('AVAILABLE');
    $('#form_emergency_contact').val('');
    $('#form_emergency_contact_number').val('');
    $('#form_license_number').val('');
    $('#form_license_type').val('');
    $('#form_restriction_code').val('');
    $('#form_expiration_date').val('');
    $('#form_existing_profile').val('');
    $('#form_existing_license').val('');
    $('#profilePreview').attr('src', '<?=base_url('assets/images/profile/user-1.jpg');?>');
    $('#form_profile_picture').val('');
    $('#form_license_attachment').val('');
    $('#licenseAttachmentPreview').hide();
    $('#helperForm').removeClass('was-validated');
    var modal = new bootstrap.Modal(document.getElementById('helperModal'));
    modal.show();
}

// =============================================
// EDIT HELPER
// =============================================
function editHelper(helper_id) {
    var mparam = {
        helper_id: helper_id,
        meaction: 'GET_HELPER'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>fms-helpers',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data) {
                var profile_pic = data.profile_picture ? '<?=base_url();?>' + data.profile_picture : '<?=base_url('assets/images/profile/user-1.jpg');?>';
                
                $('#helperModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Helper');
                $('#formBtnText').text('Update Helper');
                $('#form_helper_id').val(data.helper_id);
                $('#form_helper_code').val(data.helper_code);
                $('#form_helper_code_display').val(data.helper_code);
                $('#form_helper_name').val(data.helper_name);
                $('#form_contact_number').val(data.contact_number);
                $('#form_address').val(data.address);
                $('#form_employment_type').val(data.employment_type);
                $('#form_date_hired').val(data.date_hired);
                $('#form_helper_status').val(data.helper_status);
                $('#form_emergency_contact').val(data.emergency_contact);
                $('#form_emergency_contact_number').val(data.emergency_contact_number);
                $('#form_license_number').val(data.license_number);
                $('#form_license_type').val(data.license_type);
                $('#form_restriction_code').val(data.restriction_code);
                $('#form_expiration_date').val(data.expiration_date);
                $('#form_existing_profile').val(data.profile_picture);
                $('#form_existing_license').val(data.license_attachment);
                $('#profilePreview').attr('src', profile_pic);
                $('#form_profile_picture').val('');
                $('#form_license_attachment').val('');
                $('#licenseAttachmentPreview').hide();
                $('#helperForm').removeClass('was-validated');
                
                var modal = new bootstrap.Modal(document.getElementById('helperModal'));
                modal.show();
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error loading helper: " + error);
        }
    });
}

// =============================================
// VIEW HELPER (with license preview)
// =============================================
function viewHelper(helper_id) {
    var mparam = {
        helper_id: helper_id,
        meaction: 'GET_HELPER'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>fms-helpers',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data) {
                var profile_pic = data.profile_picture ? '<?=base_url();?>' + data.profile_picture : '<?=base_url('assets/images/profile/user-1.jpg');?>';
                var license_status = data.license_status || 'NONE';
                var statusBadge = '';
                if(license_status == 'VALID') { statusBadge = '<span class="badge badge-success">Valid</span>'; }
                else if(license_status == 'EXPIRING') { statusBadge = '<span class="badge badge-warning">Expiring</span>'; }
                else if(license_status == 'EXPIRED') { statusBadge = '<span class="badge badge-danger">Expired</span>'; }
                else { statusBadge = '<span class="badge badge-secondary">No License</span>'; }
                
                var statusLabel = '';
                if(data.helper_status == 'AVAILABLE') { statusLabel = 'Available'; }
                else if(data.helper_status == 'ASSIGNED') { statusLabel = 'Assigned'; }
                else if(data.helper_status == 'ON TRIP') { statusLabel = 'On Trip'; }
                else if(data.helper_status == 'ON LEAVE') { statusLabel = 'On Leave'; }
                else { statusLabel = 'Inactive'; }

                // License attachment preview
                var licensePreview = '';
                if(data.license_attachment) {
                    var fileExt = data.license_attachment.split('.').pop().toLowerCase();
                    var fileUrl = '<?=base_url();?>' + data.license_attachment;
                    var fileName = data.license_attachment.split('/').pop();
                    
                    if(['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(fileExt)) {
                        licensePreview = `
                            <div class="mt-2">
                                <small class="text-muted">License Attachment</small>
                                <div class="license-preview-container" onclick="openLicenseZoom('${fileUrl}')">
                                    <img src="${fileUrl}" class="license-preview-img" alt="License Attachment">
                                    <div class="license-preview-overlay">
                                        <i class="bi bi-zoom-in"></i> Click to zoom
                                    </div>
                                </div>
                            </div>
                        `;
                    } else if(['pdf'].includes(fileExt)) {
                        licensePreview = `
                            <div class="mt-2">
                                <small class="text-muted">License Attachment</small>
                                <div class="license-preview-container" onclick="openLicenseZoom('${fileUrl}')">
                                    <embed src="${fileUrl}#toolbar=0" class="license-preview-pdf" type="application/pdf">
                                    <div class="license-preview-overlay">
                                        <i class="bi bi-zoom-in"></i> Click to zoom
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        licensePreview = `
                            <div class="mt-2">
                                <small class="text-muted">License Attachment</small>
                                <div class="license-preview-container" onclick="window.open('${fileUrl}', '_blank')">
                                    <div class="license-preview-file">
                                        <i class="bi bi-file-earmark" style="font-size:48px;color:var(--gray-400);"></i>
                                        <span class="file-name">${fileName}</span>
                                        <span class="file-size">Click to download</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }

                var html = `
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="${profile_pic}" class="profile-pic-preview" style="width:120px;height:120px;" alt="Profile">
                            <h5 class="mt-2">${data.helper_name}</h5>
                            <span class="badge badge-primary">${data.helper_code}</span>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Contact Number</small>
                                    <div><strong>${data.contact_number || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Status</small>
                                    <div><strong>${statusLabel}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Employment Type</small>
                                    <div><strong>${data.employment_type || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Date Hired</small>
                                    <div><strong>${data.date_hired || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Emergency Contact</small>
                                    <div><strong>${data.emergency_contact || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Emergency Contact Number</small>
                                    <div><strong>${data.emergency_contact_number || '—'}</strong></div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <small class="text-muted">Address</small>
                                    <div><strong>${data.address || '—'}</strong></div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <hr>
                                    <small class="text-muted">Driver's License</small>
                                    <div class="mt-1">
                                        ${statusBadge}
                                        ${data.license_number ? '<span class="ms-2"><strong>#</strong> ' + data.license_number + '</span>' : ''}
                                        ${data.license_type ? '<span class="ms-2"><strong>Type:</strong> ' + data.license_type + '</span>' : ''}
                                        ${data.restriction_code ? '<span class="ms-2"><strong>Restriction:</strong> ' + data.restriction_code + '</span>' : ''}
                                        ${data.expiration_date ? '<span class="ms-2"><strong>Expiry:</strong> ' + data.expiration_date + '</span>' : ''}
                                    </div>
                                    ${licensePreview}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#viewHelperContent').html(html);
                var modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error loading helper: " + error);
        }
    });
}
</script>

<script src="<?=base_url('assets/js/apps/fms/helper/helper.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>