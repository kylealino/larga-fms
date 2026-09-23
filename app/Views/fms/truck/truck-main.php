<?php
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$query = $this->db->query("SELECT * FROM tbl_trucks ORDER BY truck_id DESC");
$trucks = $query->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_trucks = count($trucks);
$total_available = $this->db->query("SELECT COUNT(*) as total FROM tbl_trucks WHERE truck_status = 'AVAILABLE'")->getRow()->total;
$total_in_transit = $this->db->query("SELECT COUNT(*) as total FROM tbl_trucks WHERE truck_status IN ('ASSIGNED', 'DISPATCHED', 'IN TRANSIT', 'RETURNING')")->getRow()->total;
$total_maintenance = $this->db->query("SELECT COUNT(*) as total FROM tbl_trucks WHERE truck_status = 'UNDER MAINTENANCE'")->getRow()->total;
$total_inactive = $this->db->query("SELECT COUNT(*) as total FROM tbl_trucks WHERE truck_status IN ('OUT OF SERVICE', 'RETIRED')")->getRow()->total;

echo view('templates/myheader.php');
?>
<style>
    /* ============================================ */
    /* TRUCK MODULE - PROFESSIONAL UX APPROACH */
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

    .btn-icon-time { color: #8b5cf6; } .btn-icon-time:hover { background: #ede9fe; border-color: #c4b5fd; }

    /* Delivery history timeline */
    .journey-timeline { position: relative; padding-left: 40px; margin-top: 12px; }
    .journey-timeline::before {
        content: ''; position: absolute; left: 15px; top: 8px; bottom: 8px;
        width: 2px; background: var(--gray-200);
    }
    .journey-item {
        position: relative; padding: 12px 16px; background: #ffffff;
        border: 1px solid var(--gray-200); border-radius: 10px;
        margin-bottom: 12px; box-shadow: var(--shadow);
    }
    .journey-item::before {
        content: ''; position: absolute; left: -32px; top: 20px;
        width: 14px; height: 14px; border-radius: 50%;
        background: #ffffff; border: 3px solid var(--primary);
    }
    .journey-item.j-delivered::before { border-color: var(--success); }
    .journey-item.j-partial::before { border-color: var(--warning); }
    .journey-item.j-failed::before { border-color: var(--danger); }
    .journey-item .j-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
    .journey-item .j-type { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-500); }
    .journey-item .j-date { font-size: 11px; color: var(--gray-400); }
    .journey-item .j-description { font-size: 13px; font-weight: 600; color: var(--gray-800); }
    .journey-item .j-details { font-size: 11px; color: var(--gray-500); margin-top: 4px; }

    /* ============================================ */
    /* TRUCK IMAGE */
    /* ============================================ */
    .truck-image-sm {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--gray-200);
        background: var(--gray-100);
    }

    .truck-image-preview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--gray-200);
        background: var(--gray-100);
    }

    .config-badge {
        font-size: 9px;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .config-badge-rigid { background: #dbeafe; color: #1e40af; }
    .config-badge-tractor { background: #fef3c7; color: #92400e; }
    .config-badge-trailer { background: #d1fae5; color: #065f46; }

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

    /* ============================================ */
    /* DOCUMENT ATTACHMENT PREVIEW */
    /* ============================================ */
    .doc-preview-container {
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

    .doc-preview-container:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .doc-preview-container .doc-preview-overlay {
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

    .doc-preview-container:hover .doc-preview-overlay {
        opacity: 1;
    }

    .doc-preview-container .doc-preview-overlay i {
        margin-right: 4px;
    }

    .doc-preview-img {
        width: 100%;
        max-height: 200px;
        object-fit: contain;
        display: block;
    }

    .doc-preview-pdf {
        width: 100%;
        height: 200px;
        display: block;
        border: none;
    }

    .doc-preview-file {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        gap: 8px;
    }

    .doc-preview-file .file-name {
        font-size: 13px;
        color: var(--gray-700);
        word-break: break-all;
        text-align: center;
    }

    .doc-preview-file .file-size {
        font-size: 11px;
        color: var(--gray-400);
    }

    .doc-zoom-modal .modal-dialog {
        max-width: 90%;
        max-height: 90vh;
    }

    .doc-zoom-modal .modal-body {
        padding: 0;
        overflow: auto;
        max-height: 85vh;
        background: #1a1a2e;
        border-radius: 0 0 16px 16px;
    }

    .doc-zoom-modal .modal-body img {
        width: 100%;
        height: auto;
        max-height: 85vh;
        object-fit: contain;
    }

    .doc-zoom-modal .modal-body embed,
    .doc-zoom-modal .modal-body iframe {
        width: 100%;
        height: 85vh;
        border: none;
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

<div class="me-trk-msg"></div>
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
                Truck & Fleet Management
                <span class="module-badge">Fleet</span>
            </h4>
        </div>
    </div>
    <div class="header-actions">
        <button class="btn-header" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="btn-header btn-header-primary" onclick="openAddTruck()">
            <i class="bi bi-plus-circle"></i> New Truck
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- STATS CARDS - CLICKABLE -->
<!-- ============================================ -->
<div class="stat-grid">
    <div class="stat-card" data-filter="all" onclick="filterTable('all')">
        <div class="stat-left">
            <div class="stat-label">Total Fleet</div>
            <div class="stat-value"><?=$total_trucks;?></div>
            <div class="stat-sub">All assets <span class="active-filter-badge" id="filterBadgeAll" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-truck"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
    <div class="stat-card" data-filter="AVAILABLE" onclick="filterTable('AVAILABLE')">
        <div class="stat-left">
            <div class="stat-label">Available</div>
            <div class="stat-value"><?=$total_available;?></div>
            <div class="stat-sub">Ready for assignment <span class="active-filter-badge" id="filterBadgeAvailable" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
    <div class="stat-card" data-filter="IN TRANSIT" onclick="filterTable('IN TRANSIT')">
        <div class="stat-left">
            <div class="stat-label">In Transit</div>
            <div class="stat-value"><?=$total_in_transit;?></div>
            <div class="stat-sub">On the road <span class="active-filter-badge" id="filterBadgeInTransit" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-arrow-right"></i></div>
        <span class="filter-badge">Click to filter</span>
    </div>
    <div class="stat-card" data-filter="MAINTENANCE" onclick="filterTable('MAINTENANCE')">
        <div class="stat-left">
            <div class="stat-label">Maintenance</div>
            <div class="stat-value"><?=$total_maintenance;?></div>
            <div class="stat-sub">Under maintenance <span class="active-filter-badge" id="filterBadgeMaintenance" style="display:none;">Active Filter</span></div>
        </div>
        <div class="stat-right"><i class="bi bi-tools"></i></div>
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
                <h6><i class="bi bi-table me-2"></i>Fleet Directory 
                    <span id="filterStatusDisplay" style="font-size:11px;font-weight:400;color:var(--gray-500);margin-left:8px;"></span>
                </h6>
                <div>
                    <span class="badge badge-secondary"><?=count($trucks);?> records</span>
                    <button class="btn-toolbar btn-toolbar-filter btn-sm ms-2" onclick="filterTable('all')" id="clearFilterBtn" style="display:none;">
                        <i class="bi bi-x"></i> Clear Filter
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <button class="btn-toolbar btn-toolbar-primary" onclick="openAddTruck()">
                            <i class="bi bi-plus-circle"></i> Add New
                        </button>
                        <button class="btn-toolbar" onclick="window.location.reload();">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="toolbar-right">
                        <span class="text-muted" style="font-size:12px;">
                            <i class="bi bi-info-circle"></i> Click <strong>View</strong> or <strong>Edit</strong> to manage
                        </span>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="truckTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80">Code</th>
                                <th>Plate #</th>
                                <th width="100">Config</th>
                                <th>Make / Model</th>
                                <th width="110">Status</th>
                                <th width="220" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($trucks) > 0): ?>
                                <?php foreach($trucks as $row): 
                                    $configLabel = '';
                                    $configClass = '';
                                    if($row['vehicle_config'] == 'RIGID') { $configLabel = 'Rigid'; $configClass = 'config-badge-rigid'; }
                                    elseif($row['vehicle_config'] == 'TRACTOR') { $configLabel = 'Tractor'; $configClass = 'config-badge-tractor'; }
                                    elseif($row['vehicle_config'] == 'TRAILER') { $configLabel = 'Trailer'; $configClass = 'config-badge-trailer'; }
                                ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?=$row['truck_code'];?></span></td>
                                    <td><strong><?=$row['plate_number'];?></strong></td>
                                    <td><span class="config-badge <?=$configClass;?>"><?=$configLabel;?></span></td>
                                    <td><?=$row['make'];?> <?=$row['model'];?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-secondary';
                                        $statusLabel = 'Inactive';
                                        if($row['truck_status'] == 'AVAILABLE') { $statusClass = 'badge-success'; $statusLabel = 'Available'; }
                                        elseif($row['truck_status'] == 'ASSIGNED') { $statusClass = 'badge-warning'; $statusLabel = 'Assigned'; }
                                        elseif($row['truck_status'] == 'DISPATCHED') { $statusClass = 'badge-info'; $statusLabel = 'Dispatched'; }
                                        elseif($row['truck_status'] == 'IN TRANSIT') { $statusClass = 'badge-primary'; $statusLabel = 'In Transit'; }
                                        elseif($row['truck_status'] == 'RETURNING') { $statusClass = 'badge-info'; $statusLabel = 'Returning'; }
                                        elseif($row['truck_status'] == 'UNDER MAINTENANCE') { $statusClass = 'badge-warning'; $statusLabel = 'Under Maintenance'; }
                                        elseif($row['truck_status'] == 'OUT OF SERVICE') { $statusClass = 'badge-danger'; $statusLabel = 'Out of Service'; }
                                        elseif($row['truck_status'] == 'RETIRED') { $statusClass = 'badge-secondary'; $statusLabel = 'Retired'; }
                                        ?>
                                        <span class="badge <?=$statusClass;?>"><?=$statusLabel;?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <button class="btn-icon btn-icon-view" 
                                                    onclick="viewTruck(<?=$row['truck_id'];?>)" 
                                                    title="View Truck">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-view"
                                                    onclick="__Trucks.__viewDocuments(<?=$row['truck_id'];?>, '<?=addslashes($row['plate_number']);?>')"
                                                    title="Manage Documents">
                                                <i class="bi bi-files"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-time"
                                                    onclick="__Trucks.__openHistoryModal(<?=$row['truck_id'];?>, '<?=addslashes($row['plate_number']);?>')"
                                                    title="Delivery History">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-edit"
                                                    onclick="editTruck(<?=$row['truck_id'];?>)" 
                                                    title="Edit Truck">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-delete" 
                                                    onclick="__Trucks.__showDeleteModal(<?=$row['truck_id'];?>, '<?=addslashes($row['plate_number']);?>')" 
                                                    title="Delete Truck">
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

                <?php if(count($trucks) == 0): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No trucks yet</h5>
                    <p>Click <strong>New Truck</strong> to add your first fleet asset.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ADD / EDIT TRUCK MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="truckModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="truckModalTitle">
                    <i class="bi bi-truck me-2"></i>New Truck
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="truckForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="form_truck_id">
                    <input type="hidden" id="form_truck_code">
                    <input type="hidden" id="form_existing_image" value="">

                    <!-- Truck Image -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center gap-4">
                                <div>
                                    <img id="imagePreview" src="<?=base_url('assets/images/profile/truck-default.jpg');?>" class="truck-image-preview" alt="Truck Image">
                                </div>
                                <div>
                                    <label class="form-label">Truck Image</label>
                                    <input type="file" id="form_truck_image" class="form-control-file" accept=".jpg,.jpeg,.png,.gif" onchange="previewTruckImage(this)">
                                    <small class="text-muted">Upload a photo of the truck (JPG, PNG, GIF). Leave blank to keep default.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Truck Code + Configuration -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Truck Code</label>
                            <input type="text" id="form_truck_code_display" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vehicle Configuration <span class="required">*</span></label>
                            <select id="form_vehicle_config" class="form-control" required>
                                <option value="RIGID">Rigid Truck / Complete Truck</option>
                                <option value="TRACTOR">Tractor Head</option>
                                <option value="TRAILER">Trailer / Cargo Chassis</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Plate Number <span class="required">*</span></label>
                            <input type="text" id="form_plate_number" class="form-control" placeholder="ABC-1234" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">MV File Number</label>
                            <input type="text" id="form_mv_file_number" class="form-control" placeholder="MV-123456789">
                        </div>
                    </div>

                    <!-- Vehicle Details -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Vehicle Type</label>
                            <input type="text" id="form_vehicle_type" class="form-control" placeholder="10-Wheeler">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Body Type</label>
                            <input type="text" id="form_body_type" class="form-control" placeholder="Closed Van">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Make</label>
                            <input type="text" id="form_make" class="form-control" placeholder="Isuzu">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Model</label>
                            <input type="text" id="form_model" class="form-control" placeholder="F-Series">
                        </div>
                    </div>

                    <!-- Chassis + Engine -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Model Year</label>
                            <input type="number" id="form_model_year" class="form-control" placeholder="2022">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Chassis Number</label>
                            <input type="text" id="form_chassis_number" class="form-control" placeholder="CHS123456789">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Engine Number</label>
                            <input type="text" id="form_engine_number" class="form-control" placeholder="ENG123456">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fuel Type</label>
                            <select id="form_fuel_type" class="form-control">
                                <option value="">— Select —</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Gasoline">Gasoline</option>
                                <option value="Electric">Electric</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Capacities -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Fuel Tank Capacity (L)</label>
                            <input type="number" id="form_fuel_tank_capacity" class="form-control" value="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Load Capacity (kg)</label>
                            <input type="number" id="form_load_capacity" class="form-control" value="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current Odometer (km)</label>
                            <input type="number" id="form_current_odometer" class="form-control" value="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Truck Status</label>
                            <select id="form_truck_status" class="form-control">
                                <option value="AVAILABLE">Available</option>
                                <option value="ASSIGNED">Assigned</option>
                                <option value="DISPATCHED">Dispatched</option>
                                <option value="IN TRANSIT">In Transit</option>
                                <option value="RETURNING">Returning</option>
                                <option value="UNDER MAINTENANCE">Under Maintenance</option>
                                <option value="OUT OF SERVICE">Out of Service</option>
                                <option value="RETIRED">Retired</option>
                            </select>
                        </div>
                    </div>

                    <!-- Acquisition -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-building"></i> Acquisition & Ownership</div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Acquisition Date</label>
                                <input type="date" id="form_acquisition_date" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Acquired From</label>
                                <input type="text" id="form_acquired_from" class="form-control" placeholder="ABC Motors">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Acquired From Branch</label>
                                <input type="text" id="form_acquired_from_branch" class="form-control" placeholder="Manila Branch">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Manager</label>
                                <input type="text" id="form_account_manager" class="form-control" placeholder="Juan Dela Cruz">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6">
                                <label class="form-label">Ownership</label>
                                <select id="form_ownership" class="form-control">
                                    <option value="COMPANY-OWNED">Company-Owned</option>
                                    <option value="LEASED">Leased</option>
                                    <option value="RENTED">Rented</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks</label>
                                <textarea id="form_remarks" class="form-control" rows="2" placeholder="Additional notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">
                        <i class="bi bi-save"></i> <span id="formBtnText">Save Truck</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- VIEW TRUCK MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-truck me-2"></i>Truck Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewTruckContent">
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
<!-- DOCUMENTS MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="documentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-files me-2"></i>Truck Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="doc_truck_id">
                <input type="hidden" id="doc_existing_attachment" value="">
                <div class="d-flex align-items-center gap-3 mb-3 p-3" style="background:var(--gray-50);border-radius:8px;">
                    <i class="bi bi-truck" style="font-size:24px;color:var(--primary);"></i>
                    <div>
                        <div class="fw-semibold" id="doc_truck_plate" style="font-size:15px;"></div>
                        <div class="text-muted" style="font-size:12px;">Manage documents for this truck</div>
                    </div>
                </div>
                
                <!-- Add Document Form -->
                <div class="row g-2 mb-3 p-3" style="background:#ffffff;border:1px solid var(--gray-200);border-radius:8px;">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Document Type *</label>
                        <select id="doc_document_type" class="form-control">
                            <option value="">— Select —</option>
                            <option value="REGISTRATION">Registration (OR/CR)</option>
                            <option value="INSURANCE">Insurance</option>
                            <option value="SOLIDARITY STICKER">Solidarity Sticker</option>
                            <option value="EAGLE STICKER">Eagle Sticker</option>
                            <option value="GENERAL STICKER">General Sticker</option>
                            <option value="FRANCHISE">Franchise</option>
                            <option value="ACCREDITATION">Accreditation</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Document Number</label>
                        <input type="text" id="doc_document_number" class="form-control" placeholder="e.g., OR-2026-001">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Issue Date</label>
                        <input type="date" id="doc_issue_date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Expiration Date</label>
                        <input type="date" id="doc_expiration_date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">&nbsp;</label>
                        <button class="btn btn-primary btn-sm w-100" onclick="__Trucks.__saveDocument()">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                    
                    <!-- Insurance Fields (shown only when Insurance is selected) -->
                    <div class="col-12 mt-2" id="insuranceFields" style="display:none;">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Provider / Insurance Company</label>
                                <input type="text" id="doc_provider_name" class="form-control form-control-sm" placeholder="e.g., ABC Insurance">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Policy Number</label>
                                <input type="text" id="doc_policy_number" class="form-control form-control-sm" placeholder="e.g., POL-2026-001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Coverage Type</label>
                                <select id="doc_coverage_type" class="form-control form-control-sm">
                                    <option value="">— Select —</option>
                                    <option value="Comprehensive">Comprehensive</option>
                                    <option value="TPL">TPL</option>
                                    <option value="Third Party">Third Party</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Premium (₱)</label>
                                <input type="number" id="doc_premium" class="form-control form-control-sm" placeholder="0.00" step="0.01">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Coverage Amount (₱)</label>
                                <input type="number" id="doc_coverage_amount" class="form-control form-control-sm" placeholder="0.00" step="0.01">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Rate (₱)</label>
                                <input type="number" id="doc_rate" class="form-control form-control-sm" placeholder="0.00" step="0.01">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">Attachment</label>
                                <input type="file" id="doc_attachment" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="form-label" style="font-size:9px;color:var(--gray-500);">&nbsp;</label>
                                <div id="doc_attachment_preview" style="display:none;margin-top:5px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remarks for all document types -->
                    <div class="col-12 mt-2">
                        <label class="form-label" style="font-size:9px;color:var(--gray-500);">Remarks</label>
                        <textarea id="doc_remarks" class="form-control form-control-sm" rows="1" placeholder="Additional notes"></textarea>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="documentsTable">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Document #</th>
                                <th>Issue Date</th>
                                <th>Expiration Date</th>
                                <th>Status</th>
                                <th>Attachment</th>
                                <th width="80" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="documentsBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted">No documents found</td>
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
<!-- DOCUMENT ZOOM MODAL -->
<!-- ============================================ -->
<div class="modal fade doc-zoom-modal" id="docZoomModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark me-2"></i>Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="docZoomContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="docDownloadLink" class="btn btn-primary" download>
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
                <h5 class="mt-3">Delete Truck</h5>
                <p class="text-muted">You are about to delete:<br>
                    <strong id="delete_truck_name" class="text-danger"></strong>
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
<!-- DELIVERY HISTORY MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="historyModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>Delivery History — <span id="history_truck_name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="history_truck_id">
                <div id="historyContent">
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
var truckTable;

$(document).ready(function () {
    truckTable = $('#truckTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']],
        language: {
            search: "Search Fleet:",
            emptyTable: "No trucks found"
        },
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });

    $('#confirmDeleteBtn').on('click', function() {
        __Trucks.__deleteTruck();
        var modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
    });

    $('#truckForm').on('submit', function(e) {
        e.preventDefault();
        var truck_id = $('#form_truck_id').val();
        if(truck_id) {
            __Trucks.__updateTruck();
        } else {
            __Trucks.__saveTruck();
        }
    });

    // Toggle insurance fields on document type change
    $(document).on('change', '#doc_document_type', function() {
        if($(this).val() == 'INSURANCE') {
            $('#insuranceFields').show();
        } else {
            $('#insuranceFields').hide();
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
        truckTable.column(4).search('').draw();
        $('.stat-card[data-filter="all"]').addClass('active');
        $('#filterBadgeAll').show();
        $('#filterStatusDisplay').text('(Showing all fleet)');
        $('#clearFilterBtn').show();
    } else if(status === 'AVAILABLE') {
        truckTable.column(4).search('^Available$', true, false).draw();
        $('.stat-card[data-filter="AVAILABLE"]').addClass('active');
        $('#filterBadgeAvailable').show();
        $('#filterStatusDisplay').text('(Filtered: Available)');
        $('#clearFilterBtn').show();
    } else if(status === 'IN TRANSIT') {
        truckTable.column(4).search('Assigned|Dispatched|In Transit|Returning', true, false).draw();
        $('.stat-card[data-filter="IN TRANSIT"]').addClass('active');
        $('#filterBadgeInTransit').show();
        $('#filterStatusDisplay').text('(Filtered: In Transit)');
        $('#clearFilterBtn').show();
    } else if(status === 'MAINTENANCE') {
        truckTable.column(4).search('Under Maintenance', true, false).draw();
        $('.stat-card[data-filter="MAINTENANCE"]').addClass('active');
        $('#filterBadgeMaintenance').show();
        $('#filterStatusDisplay').text('(Filtered: Under Maintenance)');
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
// TRUCK IMAGE PREVIEW
// =============================================
function previewTruckImage(input) {
    var preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// =============================================
// OPEN ZOOM MODAL FOR DOCUMENT
// =============================================
function openDocZoom(fileUrl) {
    var fileExt = fileUrl.split('.').pop().toLowerCase();
    var content = '';
    
    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(fileExt)) {
        content = '<img src="' + fileUrl + '" alt="Document" style="width:100%;height:auto;max-height:85vh;object-fit:contain;">';
    } else if (['pdf'].includes(fileExt)) {
        content = '<embed src="' + fileUrl + '#toolbar=1" style="width:100%;height:85vh;" type="application/pdf">';
    } else {
        content = '<div class="text-center py-5"><i class="bi bi-file-earmark" style="font-size:64px;color:var(--gray-400);"></i><p class="mt-3">Preview not available for this file type.</p></div>';
    }
    
    $('#docZoomContent').html(content);
    $('#docDownloadLink').attr('href', fileUrl);
    
    var modal = new bootstrap.Modal(document.getElementById('docZoomModal'));
    modal.show();
}

// =============================================
// OPEN ADD TRUCK
// =============================================
function openAddTruck() {
    $('#truckModalTitle').html('<i class="bi bi-truck me-2"></i>New Truck');
    $('#formBtnText').text('Save Truck');
    $('#form_truck_id').val('');
    $('#form_truck_code').val('');
    $('#form_truck_code_display').val('Auto-generated');
    $('#form_plate_number').val('');
    $('#form_mv_file_number').val('');
    $('#form_vehicle_config').val('RIGID');
    $('#form_vehicle_type').val('');
    $('#form_body_type').val('');
    $('#form_make').val('');
    $('#form_model').val('');
    $('#form_model_year').val('');
    $('#form_chassis_number').val('');
    $('#form_engine_number').val('');
    $('#form_fuel_type').val('');
    $('#form_fuel_tank_capacity').val('0');
    $('#form_load_capacity').val('0');
    $('#form_current_odometer').val('0');
    $('#form_acquisition_date').val('');
    $('#form_acquired_from').val('');
    $('#form_acquired_from_branch').val('');
    $('#form_account_manager').val('');
    $('#form_ownership').val('COMPANY-OWNED');
    $('#form_truck_status').val('AVAILABLE');
    $('#form_remarks').val('');
    $('#form_existing_image').val('');
    $('#imagePreview').attr('src', '<?=base_url('assets/images/profile/truck-default.jpg');?>');
    $('#form_truck_image').val('');
    $('#truckForm').removeClass('was-validated');
    var modal = new bootstrap.Modal(document.getElementById('truckModal'));
    modal.show();
}

// =============================================
// EDIT TRUCK
// =============================================
function editTruck(truck_id) {
    var mparam = {
        truck_id: truck_id,
        meaction: 'GET_TRUCK'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>fms-trucks',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data) {
                var truck_image = data.truck_image ? '<?=base_url();?>' + data.truck_image : '<?=base_url('assets/images/profile/truck-default.jpg');?>';
                
                $('#truckModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Truck');
                $('#formBtnText').text('Update Truck');
                $('#form_truck_id').val(data.truck_id);
                $('#form_truck_code').val(data.truck_code);
                $('#form_truck_code_display').val(data.truck_code);
                $('#form_vehicle_config').val(data.vehicle_config);
                $('#form_plate_number').val(data.plate_number);
                $('#form_mv_file_number').val(data.mv_file_number);
                $('#form_vehicle_type').val(data.vehicle_type);
                $('#form_body_type').val(data.body_type);
                $('#form_make').val(data.make);
                $('#form_model').val(data.model);
                $('#form_model_year').val(data.model_year);
                $('#form_chassis_number').val(data.chassis_number);
                $('#form_engine_number').val(data.engine_number);
                $('#form_fuel_type').val(data.fuel_type);
                $('#form_fuel_tank_capacity').val(data.fuel_tank_capacity);
                $('#form_load_capacity').val(data.load_capacity);
                $('#form_current_odometer').val(data.current_odometer);
                $('#form_acquisition_date').val(data.acquisition_date);
                $('#form_acquired_from').val(data.acquired_from);
                $('#form_acquired_from_branch').val(data.acquired_from_branch);
                $('#form_account_manager').val(data.account_manager);
                $('#form_ownership').val(data.ownership);
                $('#form_truck_status').val(data.truck_status);
                $('#form_remarks').val(data.remarks);
                $('#form_existing_image').val(data.truck_image);
                $('#imagePreview').attr('src', truck_image);
                $('#form_truck_image').val('');
                $('#truckForm').removeClass('was-validated');
                
                var modal = new bootstrap.Modal(document.getElementById('truckModal'));
                modal.show();
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error loading truck: " + error);
        }
    });
}

// =============================================
// VIEW TRUCK
// =============================================
function viewTruck(truck_id) {
    var mparam = {
        truck_id: truck_id,
        meaction: 'GET_TRUCK'
    };

    jQuery.ajax({
        type: "POST",
        url: '<?=site_url();?>fms-trucks',
        data: mparam,
        dataType: 'json',
        success: function(data) {
            if(data) {
                var truck_image = data.truck_image ? '<?=base_url();?>' + data.truck_image : '<?=base_url('assets/images/profile/truck-default.jpg');?>';
                
                var configLabel = '';
                if(data.vehicle_config == 'RIGID') { configLabel = 'Rigid Truck'; }
                else if(data.vehicle_config == 'TRACTOR') { configLabel = 'Tractor Head'; }
                else if(data.vehicle_config == 'TRAILER') { configLabel = 'Trailer / Chassis'; }

                var statusLabel = '';
                var statusClass = '';
                if(data.truck_status == 'AVAILABLE') { statusLabel = 'Available'; statusClass = 'badge-success'; }
                else if(data.truck_status == 'ASSIGNED') { statusLabel = 'Assigned'; statusClass = 'badge-warning'; }
                else if(data.truck_status == 'DISPATCHED') { statusLabel = 'Dispatched'; statusClass = 'badge-info'; }
                else if(data.truck_status == 'IN TRANSIT') { statusLabel = 'In Transit'; statusClass = 'badge-primary'; }
                else if(data.truck_status == 'RETURNING') { statusLabel = 'Returning'; statusClass = 'badge-info'; }
                else if(data.truck_status == 'UNDER MAINTENANCE') { statusLabel = 'Under Maintenance'; statusClass = 'badge-warning'; }
                else if(data.truck_status == 'OUT OF SERVICE') { statusLabel = 'Out of Service'; statusClass = 'badge-danger'; }
                else { statusLabel = 'Retired'; statusClass = 'badge-secondary'; }

                var html = `
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="${truck_image}" style="width:150px;height:150px;object-fit:cover;border-radius:8px;border:1px solid var(--gray-200);" alt="Truck Image">
                            <h5 class="mt-2">${data.plate_number}</h5>
                            <span class="badge badge-primary">${data.truck_code}</span>
                            <div class="mt-1"><span class="badge ${statusClass}">${statusLabel}</span></div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Configuration</small>
                                    <div><strong>${configLabel}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">MV File Number</small>
                                    <div><strong>${data.mv_file_number || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Vehicle Type</small>
                                    <div><strong>${data.vehicle_type || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Body Type</small>
                                    <div><strong>${data.body_type || '—'}</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Make / Model</small>
                                    <div><strong>${data.make || '—'} ${data.model || ''} (${data.model_year || ''})</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Chassis Number</small>
                                    <div><strong>${data.chassis_number || '—'}</strong></div>
                                </div>
                                ${data.engine_number ? `
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Engine Number</small>
                                    <div><strong>${data.engine_number}</strong></div>
                                </div>` : ''}
                                ${data.fuel_type ? `
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Fuel Type</small>
                                    <div><strong>${data.fuel_type}</strong></div>
                                </div>` : ''}
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Fuel Tank Capacity</small>
                                    <div><strong>${data.fuel_tank_capacity || 0} L</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Load Capacity</small>
                                    <div><strong>${data.load_capacity || 0} kg</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Current Odometer</small>
                                    <div><strong>${data.current_odometer || 0} km</strong></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Ownership</small>
                                    <div><strong>${data.ownership || '—'}</strong></div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <small class="text-muted">Acquisition</small>
                                    <div><strong>${data.acquisition_date || '—'} ${data.acquired_from ? 'from ' + data.acquired_from : ''} ${data.acquired_from_branch ? '(' + data.acquired_from_branch + ')' : ''}</strong></div>
                                </div>
                                ${data.account_manager ? `
                                <div class="col-md-12 mb-2">
                                    <small class="text-muted">Account Manager</small>
                                    <div><strong>${data.account_manager}</strong></div>
                                </div>` : ''}
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
                $('#viewTruckContent').html(html);
                var modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();
            }
        },
        error: function(xhr, status, error) {
            toastr.error("Error loading truck: " + error);
        }
    });
}
</script>

<script src="<?=base_url('assets/js/apps/fms/truck/truck.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>