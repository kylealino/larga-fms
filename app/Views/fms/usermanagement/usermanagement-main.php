<?php
helper(['permission', 'theme']);
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();

// ==============================
// FETCH DATA
// ==============================
$users = $this->db->query("
    SELECT u.recid, u.username, u.full_name, u.division, u.section, u.position,
           u.role_id, r.role_name, u.is_active
    FROM myua_user u
    LEFT JOIN tbl_roles r ON u.role_id = r.role_id
    ORDER BY u.full_name ASC
")->getResultArray();

$roles = $this->db->query("
    SELECT r.role_id, r.role_code, r.role_name, r.description, r.theme_palette, r.sidebar_style, r.sidebar_layout, r.header_layout, r.role_status, r.created_at,
           (SELECT COUNT(*) FROM myua_user u WHERE u.role_id = r.role_id) AS user_count
    FROM tbl_roles r
    ORDER BY r.role_id ASC
")->getResultArray();

// ==============================
// DASHBOARD CALCULATIONS
// ==============================
$total_users = count($users);
$total_active = $this->db->query("SELECT COUNT(*) as total FROM myua_user WHERE is_active = 1")->getRow()->total;
$total_inactive = $this->db->query("SELECT COUNT(*) as total FROM myua_user WHERE is_active = 0")->getRow()->total;
$total_roles = count($roles);

$can_add = user_can('usermanagement', 'add');
$can_edit = user_can('usermanagement', 'edit');
$can_delete = user_can('usermanagement', 'delete');
$theme_palettes = theme_palettes();
$sidebar_styles = theme_sidebar_styles();
$sidebar_layouts = theme_sidebar_layouts();
$header_layouts = theme_header_layouts();

// CSS variables of every palette × sidebar style, for the Theme modal's live preview
$theme_preview = [];
foreach ($theme_palettes as $pkey => $p) {
    foreach ($sidebar_styles as $skey => $s) {
        $theme_preview[$pkey][$skey] = theme_vars($pkey, $skey);
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
    .lrg-module-header .header-left { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 200px; }
    .lrg-module-header .header-left .module-icon {
        width: 40px; height: 40px; background: rgba(255,255,255,0.12); border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-size: 20px;
        color: var(--primary-tint, #90cdf4); border: 1px solid rgba(255,255,255,0.08);
    }
    .lrg-module-header .header-left .module-info h4 {
        font-size: 17px; font-weight: 600; color: #ffffff; margin: 0;
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .lrg-module-header .header-left .module-info h4 .module-badge {
        font-size: 9px; font-weight: 600; padding: 2px 12px; border-radius: 4px;
        background: rgba(255,255,255,0.12); color: var(--primary-tint-2, #bee3f8); border: 1px solid rgba(255,255,255,0.08);
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .header-actions { display: flex; align-items: center; gap: 10px; }
    .btn-header {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: #ffffff;
        padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 500; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px; cursor: pointer;
    }
    .btn-header:hover { background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.25); color: #ffffff; transform: translateY(-1px); }
    .btn-header-primary { background: #ffffff; border-color: #ffffff; color: var(--primary); }
    .btn-header-primary:hover { background: rgba(255,255,255,0.9); color: var(--primary-dark); }

    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: #ffffff; border-radius: 12px; border: 1px solid var(--gray-200); padding: 16px 20px;
        transition: all 0.3s ease; display: flex; align-items: center; justify-content: space-between;
        box-shadow: var(--shadow); position: relative; overflow: hidden;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light)); opacity: 0; transition: opacity 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--gray-300); }
    .stat-card:hover::before { opacity: 1; }
    .stat-left .stat-label { font-size: 10px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .stat-left .stat-value { font-size: 24px; font-weight: 700; color: var(--gray-800); line-height: 1.2; }
    .stat-left .stat-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
    .stat-right { font-size: 32px; color: var(--primary); opacity: 0.08; line-height: 1; }

    .card { border-radius: 12px; border: 1px solid var(--gray-200); background: #ffffff; box-shadow: var(--shadow); margin-bottom: 20px; }
    .card-header {
        background: #ffffff; border-bottom: 1px solid var(--gray-200); padding: 14px 20px; border-radius: 12px 12px 0 0;
        display: flex; align-items: center; justify-content: space-between;
    }
    .card-header h6 { font-size: 13px; font-weight: 600; margin: 0; color: var(--gray-700); }
    .card-body { padding: 20px; }

    .form-label { font-size: 11px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; display: block; text-transform: uppercase; letter-spacing: 0.3px; }
    .form-label .required { color: var(--danger); margin-left: 2px; }
    .form-control, select.form-control {
        border: 1.5px solid var(--gray-200); border-radius: 8px; padding: 9px 12px; font-size: 13px;
        color: var(--gray-700); background: #ffffff; transition: all 0.2s; width: 100%; height: 40px;
    }
    .form-control:focus, select.form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 26, 107, 176), 0.08); }
    textarea.form-control { height: auto; min-height: 60px; resize: vertical; }

    .btn-primary {
        background: var(--primary); border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600;
        transition: all 0.2s; color: #ffffff; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(var(--primary-rgb, 26, 107, 176), 0.3); color: #ffffff; }
    .btn-danger {
        background: var(--danger); border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600;
        transition: all 0.2s; color: #ffffff; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-danger:hover { background: var(--danger-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(220,38,38,0.3); color: #ffffff; }
    .btn-secondary {
        background: #ffffff; border: 1.5px solid var(--gray-200); border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600;
        color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-secondary:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-sm { padding: 5px 14px; font-size: 11px; }

    .table-wrap { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .table thead th {
        font-size: 10px; font-weight: 600; color: var(--gray-500); background: var(--gray-50); border-bottom: 1.5px solid var(--gray-200);
        padding: 10px 12px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; white-space: nowrap;
    }
    .table tbody td { font-size: 12px; color: var(--gray-700); padding: 10px 12px; border-bottom: 1px solid var(--gray-100); vertical-align: middle; }
    .table-hover tbody tr:hover { background: var(--gray-50); }

    .badge { font-size: 10px; font-weight: 600; padding: 3px 12px; border-radius: 20px; letter-spacing: 0.3px; display: inline-block; }
    .badge-success { background: var(--success); color: #ffffff; }
    .badge-danger { background: var(--danger); color: #ffffff; }
    .badge-warning { background: var(--warning); color: #ffffff; }
    .badge-primary { background: var(--primary); color: #ffffff; }
    .badge-secondary { background: var(--gray-500); color: #ffffff; }
    .badge-info { background: var(--info); color: #ffffff; }

    .action-group { display: flex; align-items: center; gap: 4px; justify-content: center; }
    .btn-icon {
        display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px;
        border: 1px solid transparent; background: transparent; cursor: pointer; transition: all 0.2s; color: var(--gray-500); font-size: 14px;
    }
    .btn-icon:hover { transform: scale(1.05); }
    .btn-icon-view { color: var(--info); } .btn-icon-view:hover { background: #dbeafe; border-color: #93c5fd; }
    .btn-icon-edit { color: var(--warning); } .btn-icon-edit:hover { background: #fef3c7; border-color: #fcd34d; }
    .btn-icon-delete { color: var(--danger); } .btn-icon-delete:hover { background: #fee2e2; border-color: #fca5a5; }
    .btn-icon-perms { color: var(--success); } .btn-icon-perms:hover { background: #d1fae5; border-color: #6ee7b7; }

    .btn-toolbar {
        padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; border: 1px solid var(--gray-200);
        background: #ffffff; color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
    }
    .btn-toolbar:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

    .toolbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .toolbar-left { display: flex; align-items: center; gap: 8px; }
    .toolbar-right { display: flex; align-items: center; gap: 10px; }

    .dataTables_wrapper { font-family: 'Inter', sans-serif; }
    .dataTables_filter { float: right; margin-bottom: 16px; }
    .dataTables_filter label { font-size: 12px; font-weight: 500; color: var(--gray-500); display: flex; align-items: center; gap: 8px; }
    .dataTables_filter input { width: 200px; padding: 6px 12px; border: 1.5px solid var(--gray-200); border-radius: 8px; font-size: 12px; transition: all 0.2s; outline: none; }
    .dataTables_filter input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 26, 107, 176), 0.08); }
    .dataTables_paginate { float: right; margin-top: 16px; }
    .dataTables_paginate .paginate_button {
        padding: 4px 10px !important; margin: 0 2px !important; border-radius: 6px !important; border: 1px solid var(--gray-200) !important;
        background: #ffffff !important; color: var(--gray-600) !important; font-size: 11px !important; font-weight: 600 !important; transition: all 0.2s;
    }
    .dataTables_paginate .paginate_button.current { background: var(--primary) !important; border-color: var(--primary) !important; color: #ffffff !important; }
    .dataTables_paginate .paginate_button:hover { background: var(--gray-50) !important; border-color: var(--gray-300) !important; color: var(--primary) !important; }
    .dataTables_info { float: left; font-size: 12px; color: var(--gray-500); margin-top: 16px; }

    .modal-content { border-radius: 16px; border: none; box-shadow: var(--shadow-lg); }
    .modal-header { border-bottom: 1px solid var(--gray-200); padding: 18px 24px; background: var(--gray-50); border-radius: 16px 16px 0 0; }
    .modal-header .modal-title { font-size: 16px; font-weight: 600; color: var(--gray-800); }
    .modal-header .modal-title i { color: var(--primary); }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid var(--gray-200); padding: 16px 24px; gap: 10px; background: var(--gray-50); border-radius: 0 0 16px 16px; }

    .empty-state { text-align: center; padding: 40px 20px; }
    .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state h5 { font-size: 16px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
    .empty-state p { font-size: 13px; color: var(--gray-400); }

    /* Tabs */
    .lrg-tabs { display: flex; gap: 4px; border-bottom: 1.5px solid var(--gray-200); margin-bottom: 0; padding: 0 4px; }
    .lrg-tab-btn {
        border: none; background: transparent; padding: 12px 18px; font-size: 12px; font-weight: 600;
        color: var(--gray-500); cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1.5px;
        display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
    }
    .lrg-tab-btn:hover { color: var(--primary); }
    .lrg-tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
    .lrg-tab-pane { display: none; }
    .lrg-tab-pane.active { display: block; }

    /* Permission Matrix */
    .perm-matrix-table th, .perm-matrix-table td { text-align: center; }
    .perm-matrix-table td:first-child, .perm-matrix-table th:first-child { text-align: left; }
    .perm-group-row td { background: var(--gray-50); font-weight: 700; color: var(--primary); font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; }
    .perm-matrix-table input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }

    /* Dashboard Widgets List */
    .widget-group-label { font-weight: 700; color: var(--primary); font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; margin: 12px 0 6px; }
    .widget-group-label:first-child { margin-top: 0; }
    .widget-item { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border: 1px solid var(--gray-200); border-radius: 8px; margin-bottom: 6px; }
    .widget-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }
    .widget-item .widget-name { font-size: 12px; font-weight: 600; color: var(--gray-700); }
    .widget-item .widget-desc { font-size: 11px; color: var(--gray-500); }

    /* ==============================
       THEME PALETTE PICKER
       ============================== */
    .palette-swatch { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 6px; vertical-align: -1px; }
    .palette-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .palette-option {
        display: flex; align-items: center; gap: 10px; padding: 8px 10px; border: 1.5px solid var(--gray-200); border-radius: 8px;
        cursor: pointer; font-size: 12px; font-weight: 600; color: var(--gray-700); background: #ffffff; transition: all 0.2s;
    }
    .palette-option:hover { border-color: var(--gray-300); background: var(--gray-50); }
    .palette-option.active { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 26, 107, 176), 0.08); }
    .palette-option .palette-dots { display: flex; flex-shrink: 0; }
    .palette-option .palette-dots span { width: 14px; height: 14px; border-radius: 50%; border: 2px solid #ffffff; margin-left: -4px; box-shadow: 0 0 0 1px var(--gray-200); }
    .palette-option .palette-dots span:first-child { margin-left: 0; }
    .palette-option .palette-check { margin-left: auto; color: var(--primary); opacity: 0; }
    .palette-option.active .palette-check { opacity: 1; }
    .palette-option .palette-name { display: flex; flex-direction: column; line-height: 1.25; }
    .palette-option .palette-name small { font-size: 10px; font-weight: 500; color: var(--gray-400); white-space: nowrap; }
    .theme-sub { font-size: 10px; color: var(--gray-400); margin-left: 18px; }
    .btn-icon-theme { color: var(--primary); } .btn-icon-theme:hover { background: var(--primary-light); border-color: var(--primary); }

    .sidebar-style-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .sidebar-option {
        display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 10px 8px; text-align: center;
        border: 1.5px solid var(--gray-200); border-radius: 8px; cursor: pointer; background: #ffffff; transition: all 0.2s;
    }
    .sidebar-option:hover { border-color: var(--gray-300); background: var(--gray-50); }
    .sidebar-option.active { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 26, 107, 176), 0.08); }
    .sidebar-option strong { font-size: 12px; color: var(--gray-700); }
    .sidebar-option small { font-size: 10px; color: var(--gray-400); line-height: 1.3; }
    .so-thumb { width: 48px; height: 32px; border: 1px solid var(--gray-200); border-radius: 4px; background: #ffffff; display: flex; overflow: hidden; }
    .so-thumb span { width: 14px; height: 100%; border-right: 1px solid var(--gray-200); }
    .so-thumb-light span { background: #ffffff; }
    .so-thumb-tinted span { background: var(--gray-200); }
    .so-thumb-solid span { background: var(--gray-700); border-color: var(--gray-700); }

    /* ==============================
       THEME PREVIEW (vars set on #themePreview by __UserManagement.__renderThemePreview)
       ============================== */
    .theme-preview { display: flex; height: 300px; border: 1px solid var(--gray-200); border-radius: 12px; overflow: hidden; background: var(--lrg-bg); margin-bottom: 6px; font-size: 10px; }
    .tp-sidebar { width: 38%; background: var(--lrg-side-bg); border-right: 1px solid var(--lrg-side-border); padding: 10px 6px; }
    .tp-brand { display: flex; align-items: center; gap: 6px; padding: 0 4px 8px; border-bottom: 1px solid var(--lrg-side-border); margin-bottom: 6px; font-weight: 700; color: var(--lrg-side-text-strong); }
    .tp-chip { width: 20px; height: 20px; border-radius: 6px; background: var(--lrg-side-chip); color: var(--lrg-side-icon); display: flex; align-items: center; justify-content: center; font-size: 11px; }
    .tp-brand-text span { color: var(--lrg-side-accent); }
    .tp-cap { font-size: 7px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: var(--lrg-side-muted); padding: 6px 6px 2px; }
    .tp-link { display: flex; align-items: center; gap: 6px; padding: 5px 6px; border-radius: 6px; color: var(--lrg-side-text); border: 1px solid transparent; }
    .tp-link i { color: var(--lrg-side-icon); opacity: 0.6; }
    .tp-link.active { background: var(--lrg-side-active); color: var(--lrg-side-accent); border-color: var(--lrg-side-item-border); }
    .tp-link.active i { opacity: 1; }
    .tp-main { flex: 1; display: flex; flex-direction: column; }
    .tp-header { display: flex; align-items: center; gap: 6px; padding: 10px; color: #ffffff; font-weight: 600; font-size: 11px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); }
    .tp-header-icon { width: 20px; height: 20px; border-radius: 5px; background: rgba(255,255,255,0.12); color: var(--primary-tint); display: flex; align-items: center; justify-content: center; }
    .tp-header-badge { font-size: 7px; text-transform: uppercase; padding: 1px 6px; border-radius: 3px; background: rgba(255,255,255,0.12); color: var(--primary-tint-2); }
    .tp-body { padding: 10px; display: flex; flex-direction: column; gap: 8px; }
    .tp-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
    .tp-stat { background: #ffffff; border: 1px solid var(--gray-200); border-radius: 6px; padding: 6px 8px; display: flex; flex-direction: column; border-top: 2px solid transparent; }
    .tp-stat.active { border-color: var(--primary); border-top-color: var(--primary); }
    .tp-stat small { font-size: 7px; text-transform: uppercase; color: var(--gray-500); }
    .tp-stat strong { font-size: 14px; color: var(--gray-800); }
    .tp-row { display: flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid var(--gray-200); border-radius: 6px; padding: 6px; }
    .tp-pill { font-size: 7px; font-weight: 600; color: #ffffff; background: var(--primary-dark); padding: 2px 6px; border-radius: 10px; }
    .tp-line { flex: 1; height: 5px; border-radius: 3px; background: var(--gray-100); }
    .tp-btn { align-self: flex-start; font-size: 9px; font-weight: 600; color: #ffffff; background: var(--primary); padding: 5px 10px; border-radius: 6px; }
    .tp-link span { white-space: nowrap; }
    .tp-topbar { display: flex; align-items: center; gap: 6px; padding: 6px 10px; background: #ffffff; border-bottom: 1px solid var(--lrg-border); color: var(--lrg-text-dark); font-size: 12px; }
    .tp-user { display: none; flex-direction: column; align-items: flex-end; margin-left: auto; line-height: 1.1; }
    .tp-user b { font-size: 8px; } .tp-user small { font-size: 7px; opacity: 0.75; }
    .tp-avatar { width: 16px; height: 16px; border-radius: 50%; background: var(--lrg-border); border: 1.5px solid var(--lrg-border); margin-left: auto; }
    .tp-user + .tp-avatar { margin-left: 0; }

    /* preview: sidebar layouts */
    .tp-sb-compact .tp-sidebar { width: 14%; padding: 10px 4px; }
    .tp-sb-compact .tp-brand-text, .tp-sb-compact .tp-link span, .tp-sb-compact .tp-cap { display: none; }
    .tp-sb-compact .tp-link, .tp-sb-compact .tp-brand { justify-content: center; padding-left: 0; padding-right: 0; }
    .tp-sb-floating .tp-sidebar { margin: 6px; border-radius: 10px; border: 1px solid var(--lrg-side-border); box-shadow: 0 4px 12px rgba(var(--lrg-blue-rgb), 0.12); }
    .tp-sb-line .tp-sidebar { padding-left: 0; padding-right: 0; }
    .tp-sb-line .tp-link { border-radius: 0; border: 0; border-left: 2px solid transparent; padding-left: 8px; }
    .tp-sb-line .tp-link.active { border-left-color: var(--lrg-side-accent); }

    /* preview: header layouts */
    .tp-hd-branded .tp-topbar { background: linear-gradient(135deg, var(--lrg-blue-dark) 0%, var(--lrg-blue) 100%); color: #ffffff; border-bottom-color: rgba(255,255,255,0.15); }
    .tp-hd-branded .tp-avatar { border-color: rgba(255,255,255,0.6); }
    .tp-hd-branded .tp-user, .tp-hd-floating .tp-user { display: flex; }
    .tp-hd-floating .tp-topbar { margin: 6px 6px 4px; border: 1px solid var(--lrg-border); border-radius: 8px; box-shadow: 0 3px 8px rgba(var(--lrg-blue-rgb), 0.08); }
    .tp-hd-minimal .tp-topbar { background: transparent; border-bottom-color: transparent; }

    /* Theme modal tabs + layout option thumbnails */
    .theme-tabs { display: flex; gap: 6px; margin-bottom: 14px; border-bottom: 1px solid var(--gray-200); }
    .theme-tab { background: none; border: 0; border-bottom: 2px solid transparent; padding: 8px 12px; font-size: 12px; font-weight: 600; color: var(--gray-500); display: inline-flex; align-items: center; gap: 6px; margin-bottom: -1px; }
    .theme-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
    .layout-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
    .lay-thumb { position: relative; width: 56px; height: 38px; border: 1px solid var(--gray-200); border-radius: 5px; background: var(--gray-50); overflow: hidden; display: block; }
    .lay-thumb .lt-side { position: absolute; left: 0; top: 0; bottom: 0; width: 16px; background: var(--gray-300); }
    .lay-thumb .lt-top { position: absolute; left: 16px; right: 0; top: 0; height: 8px; background: #ffffff; border-bottom: 1px solid var(--gray-200); }
    .lay-sb-compact .lt-side { width: 7px; } .lay-sb-compact .lt-top { left: 7px; }
    .lay-sb-floating .lt-side { top: 3px; left: 3px; bottom: 3px; width: 13px; border-radius: 3px; } .lay-sb-floating .lt-top { left: 19px; }
    .lay-sb-line .lt-side::after { content: ''; position: absolute; left: 0; top: 12px; width: 3px; height: 6px; background: var(--primary); }
    .lay-hd-branded .lt-top { background: var(--primary); border-bottom: 0; }
    .lay-hd-floating .lt-top { top: 3px; left: 19px; right: 3px; border: 1px solid var(--gray-300); border-radius: 3px; }
    .lay-hd-minimal .lt-top { background: transparent; border-bottom-color: transparent; }
    @media (max-width: 768px) { .layout-grid { grid-template-columns: repeat(2, 1fr); } }

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
        .btn-icon { width: 28px; height: 28px; font-size: 13px; }
        .modal-body { padding: 16px; }
    }
</style>

<div class="me-trp-msg"></div>
<input type="hidden" id="__siteurl" data-mesiteurl="<?=site_url();?>" />
<input type="hidden" id="__can_add" value="<?=$can_add ? 1 : 0;?>" />
<input type="hidden" id="__can_edit" value="<?=$can_edit ? 1 : 0;?>" />
<input type="hidden" id="__can_delete" value="<?=$can_delete ? 1 : 0;?>" />

<!-- ============================================ -->
<!-- MODULE HEADER -->
<!-- ============================================ -->
<div class="lrg-module-header">
    <div class="header-left">
        <div class="module-icon"><i class="bi bi-person-gear"></i></div>
        <div class="module-info">
            <h4>
                User Management
                <span class="module-badge">Administration</span>
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
    <div class="stat-card">
        <div class="stat-left">
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?=$total_users;?></div>
            <div class="stat-sub">All accounts</div>
        </div>
        <div class="stat-right"><i class="bi bi-people"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-left">
            <div class="stat-label">Active</div>
            <div class="stat-value"><?=$total_active;?></div>
            <div class="stat-sub">Can log in</div>
        </div>
        <div class="stat-right"><i class="bi bi-check-circle"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-left">
            <div class="stat-label">Deactivated</div>
            <div class="stat-value"><?=$total_inactive;?></div>
            <div class="stat-sub">Cannot log in</div>
        </div>
        <div class="stat-right"><i class="bi bi-slash-circle"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-left">
            <div class="stat-label">Roles</div>
            <div class="stat-value"><?=$total_roles;?></div>
            <div class="stat-sub">Defined roles</div>
        </div>
        <div class="stat-right"><i class="bi bi-shield-lock"></i></div>
    </div>
</div>

<!-- ============================================ -->
<!-- TABS -->
<!-- ============================================ -->
<div class="card">
    <div class="lrg-tabs">
        <button class="lrg-tab-btn active" data-tab="usersTab" onclick="__UserManagement.__switchTab('usersTab', this)">
            <i class="bi bi-people"></i> Users
        </button>
        <button class="lrg-tab-btn" data-tab="rolesTab" onclick="__UserManagement.__switchTab('rolesTab', this)">
            <i class="bi bi-shield-lock"></i> Roles & Permissions
        </button>
    </div>

    <div class="card-body">
        <!-- ============================================ -->
        <!-- USERS TAB -->
        <!-- ============================================ -->
        <div class="lrg-tab-pane active" id="usersTab">
            <div class="toolbar">
                <div class="toolbar-left">
                    <button class="btn-toolbar" onclick="window.location.reload();">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="toolbar-right">
                    <?php if ($can_add): ?>
                    <button class="btn-primary btn-sm" onclick="__UserManagement.__openAddUser()">
                        <i class="bi bi-plus-circle"></i> New User
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-wrap">
                <table id="usersTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Division / Section</th>
                            <th>Position</th>
                            <th>Role</th>
                            <th width="100">Status</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $row): ?>
                        <tr>
                            <td><strong><?=esc($row['username']);?></strong></td>
                            <td><?=esc($row['full_name']);?></td>
                            <td><?=esc(trim(($row['division'] ?? '') . ' / ' . ($row['section'] ?? ''), ' /')) ?: '—';?></td>
                            <td><?=esc($row['position']) ?: '—';?></td>
                            <td><?php if ($row['role_name']): ?><span class="badge badge-primary"><?=esc($row['role_name']);?></span><?php else: ?><span class="badge badge-secondary">No Role</span><?php endif; ?></td>
                            <td><?php if ((int)$row['is_active'] === 1): ?><span class="badge badge-success">Active</span><?php else: ?><span class="badge badge-danger">Inactive</span><?php endif; ?></td>
                            <td class="text-center">
                                <div class="action-group">
                                    <?php if ($can_edit): ?>
                                    <button class="btn-icon btn-icon-edit" onclick="__UserManagement.__openEditUser(<?=$row['recid'];?>)" title="Edit User">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($can_delete): ?>
                                    <button class="btn-icon btn-icon-delete" onclick="__UserManagement.__deleteUser(<?=$row['recid'];?>, '<?=esc($row['username'], 'js');?>')" title="Delete User">
                                        <i class="bi bi-trash"></i>
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

        <!-- ============================================ -->
        <!-- ROLES & PERMISSIONS TAB -->
        <!-- ============================================ -->
        <div class="lrg-tab-pane" id="rolesTab">
            <div class="toolbar">
                <div class="toolbar-left">
                    <span class="text-muted" style="font-size:12px;">
                        <i class="bi bi-info-circle"></i> Click <strong>Permissions</strong> to set what a role can view / add / edit / delete per module
                    </span>
                </div>
                <div class="toolbar-right">
                    <?php if ($can_add): ?>
                    <button class="btn-primary btn-sm" onclick="__UserManagement.__openAddRole()">
                        <i class="bi bi-plus-circle"></i> New Role
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-wrap">
                <table id="rolesTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="140">Role Code</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th width="80" class="text-center">Users</th>
                            <th width="150">Theme</th>
                            <th width="100">Status</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $row): ?>
                        <tr>
                            <td><span class="badge badge-secondary"><?=esc($row['role_code']);?></span></td>
                            <td><strong><?=esc($row['role_name']);?></strong></td>
                            <td><?=esc($row['description']) ?: '—';?></td>
                            <td class="text-center"><?=$row['user_count'];?></td>
                            <?php
                            $row_palette = theme_palette_key($row['theme_palette']);
                            $row_sidebar = theme_sidebar_key($row['sidebar_style']);
                            $row_sb_layout = theme_sidebar_layout_key($row['sidebar_layout']);
                            $row_hd_layout = theme_header_layout_key($row['header_layout']);
                            $tp = $theme_palettes[$row_palette];
                            ?>
                            <td>
                                <span class="palette-swatch" style="background: <?=$tp['primary'];?>;"></span><?=esc($tp['label']);?>
                                <div class="theme-sub"><?=esc($sidebar_styles[$row_sidebar]['label']);?> · <?=esc($sidebar_layouts[$row_sb_layout]['label']);?> menu · <?=esc($header_layouts[$row_hd_layout]['label']);?> header</div>
                            </td>
                            <td><?php if ($row['role_status'] == 'ACTIVE'): ?><span class="badge badge-success">Active</span><?php else: ?><span class="badge badge-secondary">Inactive</span><?php endif; ?></td>
                            <td class="text-center">
                                <div class="action-group">
                                    <?php if ($can_edit): ?>
                                    <button class="btn-icon btn-icon-perms" onclick="__UserManagement.__openPermissions(<?=$row['role_id'];?>, '<?=esc($row['role_name'], 'js');?>')" title="Manage Permissions">
                                        <i class="bi bi-shield-check"></i>
                                    </button>
                                    <button class="btn-icon btn-icon-view" onclick="__UserManagement.__openDashboardWidgets(<?=$row['role_id'];?>, '<?=esc($row['role_name'], 'js');?>')" title="Customize Dashboard">
                                        <i class="bi bi-grid-1x2"></i>
                                    </button>
                                    <button class="btn-icon btn-icon-theme" onclick="__UserManagement.__openTheme(<?=$row['role_id'];?>, '<?=esc($row['role_name'], 'js');?>', '<?=$row_palette;?>', '<?=$row_sidebar;?>', '<?=$row_sb_layout;?>', '<?=$row_hd_layout;?>')" title="Set Theme">
                                        <i class="bi bi-palette"></i>
                                    </button>
                                    <button class="btn-icon btn-icon-edit" onclick="__UserManagement.__openEditRole(<?=$row['role_id'];?>)" title="Edit Role">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($can_delete): ?>
                                    <button class="btn-icon btn-icon-delete" onclick="__UserManagement.__deleteRole(<?=$row['role_id'];?>, '<?=esc($row['role_name'], 'js');?>')" title="Delete Role">
                                        <i class="bi bi-trash"></i>
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
</div>

<!-- ============================================ -->
<!-- USER MODAL (add / edit) -->
<!-- ============================================ -->
<div class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle"><i class="bi bi-person-plus me-2"></i>New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="user_recid">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Username <span class="required">*</span></label>
                        <input type="text" class="form-control" id="user_username" autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Password <span class="required" id="user_password_required">*</span></label>
                        <input type="password" class="form-control" id="user_password" autocomplete="new-password" placeholder="">
                        <small class="text-muted" id="user_password_hint" style="display:none;">Leave blank to keep current password</small>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="user_full_name">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Role</label>
                        <select class="form-control" id="user_role_id">
                            <option value="">— No Role —</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Division</label>
                        <input type="text" class="form-control" id="user_division">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Section</label>
                        <input type="text" class="form-control" id="user_section">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" id="user_position">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="user_is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Close</button>
                <button type="button" class="btn btn-primary" id="userSubmitBtn" onclick="__UserManagement.__saveUser()">
                    <i class="bi bi-save"></i> Save User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ROLE MODAL (add / edit) -->
<!-- ============================================ -->
<div class="modal fade" id="roleModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle"><i class="bi bi-shield-plus me-2"></i>New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="role_id">
                <div class="mb-2">
                    <label class="form-label">Role Name <span class="required">*</span></label>
                    <input type="text" class="form-control" id="role_name">
                </div>
                <div class="mb-2">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="role_description"></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Status</label>
                    <select class="form-control" id="role_status">
                        <option value="ACTIVE">Active</option>
                        <option value="INACTIVE">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Close</button>
                <button type="button" class="btn btn-primary" id="roleSubmitBtn" onclick="__UserManagement.__saveRole()">
                    <i class="bi bi-save"></i> Save Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- PERMISSION MATRIX MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="permModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-shield-check me-2"></i>Permissions — <span id="perm_role_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="perm_role_id">
                <div class="table-wrap">
                    <table class="table table-hover perm-matrix-table">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th width="70">View</th>
                                <th width="70">Add</th>
                                <th width="70">Edit</th>
                                <th width="70">Delete</th>
                            </tr>
                        </thead>
                        <tbody id="permMatrixBody">
                            <tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Close</button>
                <button type="button" class="btn btn-primary" id="permSubmitBtn" onclick="__UserManagement.__savePermissions()">
                    <i class="bi bi-save"></i> Save Permissions
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- DASHBOARD WIDGETS MODAL -->
<!-- ============================================ -->
<div class="modal fade" id="widgetsModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-grid-1x2 me-2"></i>Dashboard Widgets — <span id="widgets_role_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="widgets_role_id">
                <p class="text-muted" style="font-size:12px;">Choose which dashboard sections this role sees when they log in.</p>
                <div id="widgetsListBody">
                    <div class="text-center text-muted">Loading...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Close</button>
                <button type="button" class="btn btn-primary" id="widgetsSubmitBtn" onclick="__UserManagement.__saveDashboardWidgets()">
                    <i class="bi bi-save"></i> Save Widgets
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- THEME MODAL (colors + UI layout) -->
<!-- ============================================ -->
<div class="modal fade" id="themeModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-palette me-2"></i>Theme — <span id="theme_role_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="theme_role_id">
                <input type="hidden" id="theme_palette" data-default="<?=config('Themes')->default;?>">
                <input type="hidden" id="theme_sidebar_style" data-default="<?=config('Themes')->defaultSidebar;?>">
                <input type="hidden" id="theme_sidebar_layout" data-default="<?=config('Themes')->defaultSidebarLayout;?>">
                <input type="hidden" id="theme_header_layout" data-default="<?=config('Themes')->defaultHeaderLayout;?>">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="theme-tabs">
                            <button type="button" class="theme-tab active" data-pane="themePaneColors" onclick="__UserManagement.__themeTab('themePaneColors')"><i class="bi bi-droplet-half"></i> Colors</button>
                            <button type="button" class="theme-tab" data-pane="themePaneLayout" onclick="__UserManagement.__themeTab('themePaneLayout')"><i class="bi bi-layout-sidebar"></i> Layout</button>
                        </div>

                        <!-- COLORS -->
                        <div class="theme-pane" id="themePaneColors">
                            <label class="form-label">Color Palette</label>
                            <div class="palette-grid mb-3">
                                <?php foreach ($theme_palettes as $key => $p): ?>
                                <div class="palette-option" data-palette="<?=$key;?>" onclick="__UserManagement.__selectPalette('<?=$key;?>')">
                                    <span class="palette-dots"><span style="background: <?=$p['primary'];?>;"></span><span style="background: <?=$p['primary_dark'];?>;"></span><span style="background: <?=$p['primary_pale'];?>;"></span></span>
                                    <span class="palette-name"><?=esc($p['label']);?><small><?=esc($p['hint'] ?? '');?></small></span>
                                    <i class="bi bi-check-circle-fill palette-check"></i>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <label class="form-label">Sidebar Color</label>
                            <div class="sidebar-style-grid">
                                <?php foreach ($sidebar_styles as $key => $s): ?>
                                <div class="sidebar-option" data-sidebar="<?=$key;?>" onclick="__UserManagement.__selectSidebarStyle('<?=$key;?>')">
                                    <span class="so-thumb so-thumb-<?=strtolower($key);?>"><span></span></span>
                                    <strong><?=esc($s['label']);?></strong>
                                    <small><?=esc($s['description']);?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- LAYOUT -->
                        <div class="theme-pane" id="themePaneLayout" style="display: none;">
                            <label class="form-label">Sidebar Layout</label>
                            <div class="layout-grid mb-3">
                                <?php foreach ($sidebar_layouts as $key => $l): ?>
                                <div class="sidebar-option layout-option" data-sidebar-layout="<?=$key;?>" onclick="__UserManagement.__selectSidebarLayout('<?=$key;?>')">
                                    <span class="lay-thumb lay-sb-<?=strtolower($key);?>"><span class="lt-side"></span><span class="lt-top"></span></span>
                                    <strong><?=esc($l['label']);?></strong>
                                    <small><?=esc($l['description']);?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <label class="form-label">Header Layout</label>
                            <div class="layout-grid">
                                <?php foreach ($header_layouts as $key => $l): ?>
                                <div class="sidebar-option layout-option" data-header-layout="<?=$key;?>" onclick="__UserManagement.__selectHeaderLayout('<?=$key;?>')">
                                    <span class="lay-thumb lay-hd-<?=strtolower($key);?>"><span class="lt-side"></span><span class="lt-top"></span></span>
                                    <strong><?=esc($l['label']);?></strong>
                                    <small><?=esc($l['description']);?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <label class="form-label">Preview</label>
                        <div class="theme-preview" id="themePreview">
                            <div class="tp-sidebar">
                                <div class="tp-brand"><span class="tp-chip"><i class="bi bi-truck-front"></i></span><span class="tp-brand-text">Larga <span>Fleet</span></span></div>
                                <div class="tp-cap">Operations</div>
                                <div class="tp-link active"><i class="bi bi-speedometer2"></i> <span>Dashboard</span></div>
                                <div class="tp-link"><i class="bi bi-people"></i> <span>Customers</span></div>
                                <div class="tp-link"><i class="bi bi-truck"></i> <span>Trucks</span></div>
                                <div class="tp-cap">Billing &amp; AR</div>
                                <div class="tp-link"><i class="bi bi-receipt"></i> <span>Billing</span></div>
                                <div class="tp-link"><i class="bi bi-cash-coin"></i> <span>Payments</span></div>
                            </div>
                            <div class="tp-main">
                                <div class="tp-topbar"><i class="bi bi-list"></i><span class="tp-user"><b>Juan Dela Cruz</b><small>Administrator</small></span><span class="tp-avatar"></span></div>
                                <div class="tp-header"><span class="tp-header-icon"><i class="bi bi-receipt"></i></span> Billing <span class="tp-header-badge">Billing &amp; AR</span></div>
                                <div class="tp-body">
                                    <div class="tp-stats">
                                        <div class="tp-stat active"><small>Total</small><strong>18</strong></div>
                                        <div class="tp-stat"><small>Draft</small><strong>0</strong></div>
                                    </div>
                                    <div class="tp-row"><span class="tp-pill">BILL-2026-000019</span><span class="tp-line"></span></div>
                                    <div class="tp-row"><span class="tp-pill">BILL-2026-000018</span><span class="tp-line"></span></div>
                                    <span class="tp-btn"><i class="bi bi-plus-circle"></i> New Billing</span>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Applies to every module for users with this role, from their next page load.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Close</button>
                <button type="button" class="btn btn-primary" id="themeSubmitBtn" onclick="__UserManagement.__saveTheme()">
                    <i class="bi bi-save"></i> Save Theme
                </button>
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
                <h5 class="modal-title"><i class="bi bi-trash me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle" style="font-size:56px;color:var(--danger);opacity:0.6;"></i>
                <h5 class="mt-3" id="delete_title">Delete Record</h5>
                <p class="text-muted">You are about to delete:<br><strong id="delete_target_name" class="text-danger"></strong></p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><i class="bi bi-trash"></i> Delete</button>
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
var usersTable, rolesTable;
// palette key → sidebar style key → { '--css-var': value } (theme_helper.php theme_vars)
var __themePreviewVars = <?=json_encode($theme_preview);?>;

$(document).ready(function () {
    usersTable = $('#usersTable').DataTable({
        pageLength: 10, lengthChange: false,
        language: { search: "Search:", emptyTable: "No users found" },
        columnDefs: [{ orderable: false, targets: [6] }]
    });

    rolesTable = $('#rolesTable').DataTable({
        pageLength: 10, lengthChange: false,
        language: { search: "Search:", emptyTable: "No roles found" },
        columnDefs: [{ orderable: false, targets: [6] }]
    });
});
</script>

<script src="<?=base_url('assets/js/apps/fms/usermanagement/usermanagement.js') . '?v=' . filemtime(FCPATH . 'assets/js/apps/fms/usermanagement/usermanagement.js');?>"></script>

<?php
echo view('templates/myfooter.php');
?>
