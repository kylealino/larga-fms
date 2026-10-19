<?php 
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');
helper(['permission', 'theme']);

  $query = $this->db->query("
  SELECT
      `full_name`,
      `division`,
      `section`,
      `position`,
      `username`,
      `hash_password`,
      `hash_value`
  FROM
      `myua_user`
  WHERE
      `username` = ?", [$this->cuser]
  );

  $data = $query->getRowArray();
  $full_name = $data['full_name'];
  $position = $data['position'];
  $section = $data['section'];
  $division = $data['division'];
  $theme = role_theme($this->session->get('__xsys_myuserrole__'));
  $role_name = $this->session->get('__xsys_myuserrolename__');
  
  // Get current URL for active menu highlighting
  $current_url = current_url();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <link rel="shortcut icon" type="image/png" href="<?=base_url('assets/images/logos/gym-logo.png')?>" />
  <title>Larga Fleet | Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.30.0/tabler-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    /* ============================================ */
    /* LARGA FLEET - LIGHT BLUE / WHITE THEME */
    /* ============================================ */
    :root {
      --lrg-bg: #f0f7fe;
      --lrg-bg-dark: #e3effa;
      --lrg-sidebar: #ffffff;
      --lrg-sidebar-border: rgba(0, 80, 200, 0.06);
      --lrg-blue: #1a6bb0;
      --lrg-blue-light: #3c9eff;
      --lrg-blue-dark: #0f5a99;
      --lrg-blue-pale: #e8f2fa;
      --lrg-blue-rgb: 26, 107, 176;
      --lrg-text-dark: #0b2a47;
      --lrg-text-mid: #1e4a6e;
      --lrg-text-light: #3f6b8f;
      --lrg-text-muted: #4b6f92;
      --lrg-white: #ffffff;
      --lrg-border: #dde8f2;
      --lrg-border-light: rgba(0, 80, 200, 0.04);
      --lrg-hover: rgba(var(--lrg-blue-rgb), 0.04);
      --lrg-active: rgba(var(--lrg-blue-rgb), 0.06);
      --lrg-shadow: rgba(0, 50, 100, 0.04);
      --lrg-shadow-hover: rgba(0, 70, 150, 0.06);

      /* Sidebar — defaults are the LIGHT sidebar style; theme_helper.php overrides for TINTED / SOLID */
      --lrg-side-bg: var(--lrg-sidebar);
      --lrg-side-text: var(--lrg-text-light);
      --lrg-side-text-strong: var(--lrg-text-dark);
      --lrg-side-muted: var(--lrg-text-muted);
      --lrg-side-icon: var(--lrg-blue);
      --lrg-side-accent: var(--lrg-blue);
      --lrg-side-hover: var(--lrg-hover);
      --lrg-side-active: var(--lrg-active);
      --lrg-side-border: var(--lrg-sidebar-border);
      --lrg-side-item-border: var(--lrg-border-light);
      --lrg-side-chip: var(--lrg-blue-pale);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--lrg-bg);
      overflow-x: hidden;
    }

    /* ============================================ */
    /* SIDEBAR - Clean white with light blue accents */
    /* ============================================ */
    .left-sidebar {
      background: var(--lrg-side-bg);
      box-shadow: 4px 0 30px var(--lrg-shadow);
      border-right: 1px solid var(--lrg-side-border);
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 280px;
      z-index: 1000;
      transition: transform 0.3s ease, width 0.3s ease;
      overflow-y: auto;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
    }

    .left-sidebar.collapsed {
      width: 80px;
    }

    .left-sidebar.collapsed .brand-text,
    .left-sidebar.collapsed .sidebar-link span:not(.ti),
    .left-sidebar.collapsed .nav-small-cap span,
    .left-sidebar.collapsed .logout-link span {
      display: none;
    }

    .left-sidebar.collapsed .sidebar-link {
      justify-content: center;
      padding: 10px;
    }

    .left-sidebar.collapsed .sidebar-link i,
    .left-sidebar.collapsed .sidebar-link .bi {
      margin: 0;
    }

    .left-sidebar.collapsed .brand-logo a {
      justify-content: center;
    }

    .left-sidebar::-webkit-scrollbar {
      width: 3px;
    }
    .left-sidebar::-webkit-scrollbar-track {
      background: transparent;
    }
    .left-sidebar::-webkit-scrollbar-thumb {
      background: var(--lrg-side-icon);
      border-radius: 3px;
    }

    @media (max-width: 768px) {
      .left-sidebar {
        transform: translateX(-100%);
        width: 260px;
      }
      .left-sidebar.open {
        transform: translateX(0);
      }
      .left-sidebar.collapsed {
        width: 260px;
      }
      .left-sidebar.collapsed .brand-text,
      .left-sidebar.collapsed .sidebar-link span:not(.ti),
      .left-sidebar.collapsed .nav-small-cap span,
      .left-sidebar.collapsed .logout-link span {
        display: inline;
      }
    }

    .brand-logo {
      padding: 20px 24px;
      border-bottom: 1px solid var(--lrg-side-border);
      background: var(--lrg-side-bg);
    }

    .brand-logo a {
      color: var(--lrg-side-text-strong);
      font-weight: 700;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .brand-logo .shield-icon {
      width: 40px;
      height: 40px;
      background: var(--lrg-side-chip);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--lrg-side-item-border);
      box-shadow: 0 4px 12px var(--lrg-shadow);
      flex-shrink: 0;
    }

    .brand-logo .shield-icon i {
      font-size: 20px;
      color: var(--lrg-side-icon);
    }

    .brand-text {
      font-size: 0.9rem;
      letter-spacing: 0.5px;
      font-weight: 700;
      color: var(--lrg-side-text-strong);
    }

    .brand-text span {
      color: var(--lrg-side-accent);
      font-weight: 800;
    }

    /* Sidebar Navigation */
    .sidebar-nav {
      padding: 16px 0 0 0;
      flex: 1;
    }

    .nav-small-cap {
      padding: 8px 24px 4px 24px;
    }

    .nav-small-cap span {
      color: var(--lrg-side-muted);
      font-size: 0.6rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      font-weight: 600;
    }

    .sidebar-item {
      list-style: none;
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 20px;
      margin: 2px 12px;
      color: var(--lrg-side-text);
      border-radius: 10px;
      transition: all 0.2s ease;
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .sidebar-link:hover {
      background: var(--lrg-side-hover);
      color: var(--lrg-side-text-strong);
    }

    .sidebar-item.active .sidebar-link {
      background: var(--lrg-side-active);
      color: var(--lrg-side-accent);
      border: 1px solid var(--lrg-side-item-border);
    }

    .sidebar-link i, .sidebar-link .bi {
      font-size: 1.2rem;
      width: 24px;
      color: var(--lrg-side-icon);
      opacity: 0.6;
    }

    .sidebar-item.active .sidebar-link i,
    .sidebar-item.active .sidebar-link .bi {
      opacity: 1;
    }

    .sidebar-link:hover i,
    .sidebar-link:hover .bi {
      opacity: 1;
    }

    /* Sidebar Footer - Logout */
    .sidebar-footer {
      padding: 20px 20px 30px 20px;
      border-top: 1px solid var(--lrg-side-border);
      margin-top: auto;
    }

    .logout-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 16px;
      color: var(--lrg-side-text);
      border-radius: 10px;
      transition: all 0.2s ease;
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 500;
      background: var(--lrg-side-hover);
      border: 1px solid var(--lrg-side-item-border);
      cursor: pointer;
      width: 100%;
      text-align: left;
    }

    .logout-link:hover {
      background: var(--lrg-side-active);
      color: var(--lrg-side-accent);
      border-color: var(--lrg-side-border);
    }

    .logout-link i {
      font-size: 1.2rem;
      width: 24px;
      color: var(--lrg-side-icon);
      opacity: 0.6;
    }

    .logout-link:hover i {
      opacity: 1;
    }

    /* ============================================ */
    /* LOGOUT CONFIRMATION MODAL */
    /* ============================================ */
    .logout-modal .modal-content {
      background: var(--lrg-white);
      border-radius: 16px;
      border: 1px solid var(--lrg-border);
      box-shadow: 0 20px 60px rgba(0,0,0,0.08);
    }

    .logout-modal .modal-header {
      border-bottom: 1px solid var(--lrg-border);
      padding: 18px 24px;
    }

    .logout-modal .modal-header .modal-title {
      font-weight: 600;
      color: var(--lrg-text-dark);
    }

    .logout-modal .modal-header .modal-title i {
      color: var(--lrg-blue);
      font-size: 1.2rem;
    }

    .logout-modal .modal-body {
      padding: 30px 24px;
      text-align: center;
    }

    .logout-modal .modal-body .logout-icon {
      font-size: 56px;
      color: var(--lrg-blue);
      opacity: 0.4;
      margin-bottom: 16px;
    }

    .logout-modal .modal-body h4 {
      font-weight: 600;
      color: var(--lrg-text-dark);
      margin-bottom: 8px;
    }

    .logout-modal .modal-body p {
      color: var(--lrg-text-light);
      font-size: 0.95rem;
      margin-bottom: 0;
    }

    .logout-modal .modal-footer {
      border-top: 1px solid var(--lrg-border);
      padding: 16px 24px;
      gap: 10px;
    }

    .logout-modal .btn-cancel {
      background: var(--lrg-bg);
      border: 1px solid var(--lrg-border);
      border-radius: 8px;
      padding: 8px 24px;
      font-weight: 600;
      font-size: 0.85rem;
      color: var(--lrg-text-light);
      transition: 0.2s;
    }

    .logout-modal .btn-cancel:hover {
      background: var(--lrg-border);
      border-color: rgba(var(--lrg-blue-rgb), 0.25);
    }

    .logout-modal .btn-logout-confirm {
      background: var(--lrg-blue);
      border: none;
      border-radius: 8px;
      padding: 8px 24px;
      font-weight: 600;
      font-size: 0.85rem;
      color: #ffffff;
      transition: 0.2s;
    }

    .logout-modal .btn-logout-confirm:hover {
      background: var(--lrg-blue-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(var(--lrg-blue-rgb), 0.2);
    }

    /* ============================================ */
    /* PAGE WRAPPER */
    /* ============================================ */
    .page-wrapper {
      margin-left: 280px;
      transition: margin-left 0.3s ease;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .page-wrapper.expanded {
      margin-left: 80px;
    }

    @media (max-width: 768px) {
      .page-wrapper {
        margin-left: 0;
      }
      .page-wrapper.expanded {
        margin-left: 0;
      }
    }

    /* ============================================ */
    /* TOPBAR - Clean white */
    /* ============================================ */
    .topbar {
      background: var(--lrg-white);
      border-bottom: 1px solid var(--lrg-border);
      box-shadow: 0 1px 3px var(--lrg-shadow);
      position: sticky;
      top: 0;
      z-index: 999;
      width: 100%;
    }

    .navbar {
      padding: 10px 24px;
    }

    .navbar-nav {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .nav-item {
      list-style: none;
    }

    #headerCollapse, .mobile-menu-toggle {
      background: transparent;
      border: none;
      cursor: pointer;
      padding: 8px;
      border-radius: 8px;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    #headerCollapse i, .mobile-menu-toggle i {
      font-size: 1.4rem;
      color: var(--lrg-text-dark);
    }

    #headerCollapse:hover, .mobile-menu-toggle:hover {
      background: var(--lrg-hover);
    }

    #headerCollapse:hover i, .mobile-menu-toggle:hover i {
      color: var(--lrg-blue);
    }

    .mobile-menu-toggle {
      display: none;
    }

    @media (max-width: 768px) {
      .mobile-menu-toggle {
        display: flex;
      }
    }

    /* User Profile */
    .user-profile-img img {
      border: 2px solid var(--lrg-border);
      transition: 0.2s;
      border-radius: 50%;
      width: 38px;
      height: 38px;
      object-fit: cover;
    }

    .user-profile-img img:hover {
      border-color: var(--lrg-blue);
      transform: scale(1.05);
    }

    .dropdown-menu {
      border-radius: 12px;
      border: 1px solid var(--lrg-border);
      box-shadow: 0 10px 30px rgba(0,0,0,0.06);
      min-width: 260px;
      padding: 0;
    }

    .profile-dropdown {
      background: var(--lrg-white);
      border-radius: 12px;
      overflow: hidden;
    }

    .profile-dropdown .dropdown-header {
      background: var(--lrg-blue-pale);
      color: var(--lrg-text-dark);
      padding: 14px 18px;
      border-bottom: 1px solid var(--lrg-border-light);
    }

    .profile-dropdown .dropdown-header h5 {
      margin: 0;
      font-size: 0.9rem;
      font-weight: 600;
    }

    .profile-info {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 18px;
      border-bottom: 1px solid var(--lrg-border);
    }

    .profile-info h6 {
      margin: 0;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--lrg-text-dark);
    }

    .profile-info span {
      font-size: 0.65rem;
      color: var(--lrg-text-light);
    }

    .btn-outline-primary {
      border-radius: 8px;
      border: 1px solid var(--lrg-blue);
      color: var(--lrg-blue);
      background: transparent;
      padding: 8px 16px;
      font-weight: 600;
      font-size: 0.75rem;
      transition: 0.2s;
      width: 100%;
    }

    .btn-outline-primary:hover {
      background: var(--lrg-blue);
      color: var(--lrg-white);
    }

    /* Sidebar Overlay */
    .sidebar-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.3);
      z-index: 998;
      display: none;
    }

    .sidebar-overlay.active {
      display: block;
    }

    /* Body Wrapper */
    .body-wrapper {
      background: var(--lrg-bg);
      flex: 1;
      padding: 24px;
    }

    @media (max-width: 768px) {
      .body-wrapper {
        padding: 16px;
      }
    }

    /* Footer */
    .footer {
      background: var(--lrg-white);
      border-top: 1px solid var(--lrg-border);
      color: var(--lrg-text-muted);
      font-size: 0.7rem;
      padding: 12px 24px;
      text-align: center;
    }

    /* ============================================ */
    /* UI LAYOUTS - body.lrg-sb-* / body.lrg-hd-*   */
    /* (role setting, app/Config/Themes.php)        */
    /* ============================================ */

    /* Name & role chip in the topbar — only the Branded / Floating headers show it */
    .topbar-user {
      display: none;
      flex-direction: column;
      align-items: flex-end;
      line-height: 1.2;
      margin-right: 10px;
    }

    .topbar-user .tu-name {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--lrg-text-dark);
    }

    .topbar-user .tu-role {
      font-size: 0.65rem;
      color: var(--lrg-text-muted);
    }

    body.lrg-hd-branded .topbar-user,
    body.lrg-hd-floating .topbar-user {
      display: flex;
    }

    /* Sidebar: COMPACT — icon rail (starts collapsed), expands over the page on hover */
    @media (min-width: 769px) {
      body.lrg-sb-compact .left-sidebar.collapsed:hover {
        width: 280px;
        box-shadow: 8px 0 30px rgba(var(--lrg-blue-rgb), 0.12);
      }

      body.lrg-sb-compact .left-sidebar.collapsed:hover .brand-text,
      body.lrg-sb-compact .left-sidebar.collapsed:hover .sidebar-link span:not(.ti),
      body.lrg-sb-compact .left-sidebar.collapsed:hover .nav-small-cap span,
      body.lrg-sb-compact .left-sidebar.collapsed:hover .logout-link span {
        display: inline;
      }

      body.lrg-sb-compact .left-sidebar.collapsed:hover .sidebar-link {
        justify-content: flex-start;
        padding: 10px 20px;
      }

      body.lrg-sb-compact .left-sidebar.collapsed:hover .brand-logo a {
        justify-content: flex-start;
      }
    }

    /* Sidebar: FLOATING — detached rounded panel */
    @media (min-width: 769px) {
      body.lrg-sb-floating .left-sidebar {
        top: 12px;
        left: 12px;
        height: calc(100vh - 24px);
        border-radius: 16px;
        border: 1px solid var(--lrg-side-border);
        box-shadow: 0 12px 32px rgba(var(--lrg-blue-rgb), 0.12);
      }

      body.lrg-sb-floating .brand-logo {
        border-radius: 16px 16px 0 0;
      }

      body.lrg-sb-floating .page-wrapper {
        margin-left: 304px;
      }

      body.lrg-sb-floating .page-wrapper.expanded {
        margin-left: 104px;
      }
    }

    /* Sidebar: LINE — flat full-width items, accent bar on the active one */
    body.lrg-sb-line .sidebar-link {
      margin: 1px 0;
      border-radius: 0;
      padding: 10px 24px 10px 21px;
      border-left: 3px solid transparent;
    }

    body.lrg-sb-line .sidebar-item.active .sidebar-link {
      border: 0;
      border-left: 3px solid var(--lrg-side-accent);
    }

    body.lrg-sb-line .sidebar-link:hover {
      border-left-color: var(--lrg-side-border);
    }

    /* Header: BRANDED — palette color bar */
    body.lrg-hd-branded .topbar {
      background: linear-gradient(135deg, var(--lrg-blue-dark) 0%, var(--lrg-blue) 100%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 2px 10px rgba(var(--lrg-blue-rgb), 0.25);
    }

    body.lrg-hd-branded .topbar .sidebartoggler i,
    body.lrg-hd-branded .topbar .mobile-menu-toggle i,
    body.lrg-hd-branded .topbar-user .tu-name {
      color: #ffffff;
    }

    body.lrg-hd-branded .topbar-user .tu-role {
      color: rgba(255, 255, 255, 0.75);
    }

    body.lrg-hd-branded .user-profile-img img {
      border-color: rgba(255, 255, 255, 0.6);
    }

    body.lrg-hd-branded .topbar .sidebartoggler:hover,
    body.lrg-hd-branded .topbar .mobile-menu-toggle:hover {
      background: rgba(255, 255, 255, 0.12);
    }

    /* Header: FLOATING — detached rounded card */
    @media (min-width: 769px) {
      body.lrg-hd-floating .topbar {
        top: 12px;
        margin: 12px 24px 0 24px;
        width: auto;
        border: 1px solid var(--lrg-border);
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(var(--lrg-blue-rgb), 0.08);
      }

      body.lrg-hd-floating .body-wrapper {
        padding-top: 36px;
      }
    }

    /* Header: MINIMAL — blends into the page background */
    body.lrg-hd-minimal .topbar {
      background: var(--lrg-bg);
      border-bottom-color: transparent;
      box-shadow: none;
    }

    @media (max-width: 768px) {
      .topbar-user {
        display: none !important;
      }
    }

    /* Bootstrap pagination (DataTables bootstrap5 pager) follows the role palette */
    .pagination {
      --bs-pagination-color: var(--lrg-blue);
      --bs-pagination-hover-color: var(--lrg-blue-dark);
      --bs-pagination-focus-color: var(--lrg-blue-dark);
      --bs-pagination-focus-box-shadow: 0 0 0 0.2rem rgba(var(--lrg-blue-rgb), 0.15);
      --bs-pagination-active-bg: var(--lrg-blue);
      --bs-pagination-active-border-color: var(--lrg-blue);
    }
  </style>

  <style id="lrg-theme">
    /* ============================================ */
    /* ROLE COLOR PALETTE - <?=$theme['palette'];?> / SIDEBAR <?=$theme['sidebar'];?> (app/Config/Themes.php) */
    /* ============================================ */
    <?=theme_css_vars($theme['palette'], $theme['sidebar']);?>

  </style>
</head>
<body class="<?=theme_body_classes($theme);?>">
  <!-- Mobile Sidebar Overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div id="main-wrapper">
    <!-- Sidebar Start -->
    <?php $sb_compact = $theme['sidebar_layout'] === 'COMPACT'; // icon rail starts collapsed (server-side, no flash) ?>
    <aside class="left-sidebar<?=$sb_compact ? ' collapsed' : '';?>" id="sidebar">
      <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="<?=site_url();?>myadmindashboard" class="text-nowrap logo-img">
          <div class="d-flex align-items-center gap-2">
            <div class="shield-icon">
              <i class="bi bi-truck-front"></i>
            </div>
            <span class="brand-text">Larga <span>Fleet</span></span>
          </div>
        </a> 
        <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none d-block d-xl-none" id="closeSidebar">
          <i class="bi bi-x" style="color: var(--lrg-side-muted); font-size: 1.2rem;"></i>
        </a>
      </div>

      <nav class="sidebar-nav">
        <ul id="sidebarnav" style="list-style: none; padding-left: 0;">
          
          <!-- DASHBOARD -->
          <li class="sidebar-item <?= strpos($current_url, 'myadmindashboard') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>myadmindashboard">
              <i class="bi bi-speedometer2"></i>
              <span>Dashboard</span>
            </a>
          </li>

          <!-- ============================================ -->
          <!-- OPERATIONS MANAGEMENT -->
          <!-- ============================================ -->
          <li class="nav-small-cap">
            <span>Operations</span>
          </li>

          <?php if (user_can('fms-customers')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-customers') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-customers">
              <i class="bi bi-people"></i>
              <span>Customer Management</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('fms-vendors')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-vendors') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-vendors">
              <i class="bi bi-building"></i>
              <span>Vendor Management</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('fms-trucks')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-trucks') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-trucks">
              <i class="bi bi-truck"></i>
              <span>Truck Management</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- <li class="sidebar-item <?= strpos($current_url, 'chassis') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>chassis">
              <i class="bi bi-layers"></i>
              <span>Chassis Management</span>
            </a>
          </li> -->

          <?php if (user_can('fms-drivers')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-drivers') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-drivers">
              <i class="bi bi-person-badge"></i>
              <span>Driver Management</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('fms-helpers')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-helpers') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-helpers">
              <i class="bi bi-person-plus"></i>
              <span>Helper Management</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- ============================================ -->
          <!-- TRIP & DISPATCH -->
          <!-- ============================================ -->
          <li class="nav-small-cap">
            <span>Trip & Dispatch</span>
          </li>

          <?php if (user_can('fms-trips')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-trips') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-trips">
              <i class="bi bi-calendar-event"></i>
              <span>Trip Scheduling</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('fms-dispatch')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-dispatch') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-dispatch">
              <i class="bi bi-send"></i>
              <span>Dispatch Monitoring</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('fms-delivery-receipt')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-delivery-receipt') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-delivery-receipt">
              <i class="bi bi-receipt"></i>
              <span>Delivery Receipt</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- ============================================ -->
          <!-- BILLING & ACCOUNTS RECEIVABLE -->
          <!-- ============================================ -->
          <li class="nav-small-cap">
            <span>Billing & AR</span>
          </li>

          <?php if (user_can('billing')): ?>
          <li class="sidebar-item <?= (strpos($current_url, 'billing') !== false && strpos($current_url, 'billingreports') === false) ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>billing">
              <i class="bi bi-receipt"></i>
              <span>Billing Generation</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('invoice')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'invoice') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>invoice">
              <i class="bi bi-file-earmark-text"></i>
              <span>Invoice</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('payment')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'payment') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>payment">
              <i class="bi bi-credit-card"></i>
              <span>Payment Recording</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('accountsreceivable')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'accountsreceivable') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>accountsreceivable">
              <i class="bi bi-pie-chart"></i>
              <span>Accounts Receivable</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('statementofaccount')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'statementofaccount') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>statementofaccount">
              <i class="bi bi-file-earmark-bar-graph"></i>
              <span>Statement of Account</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- ============================================ -->
          <!-- MAINTENANCE MANAGEMENT -->
          <!-- ============================================ -->
          <li class="nav-small-cap">
            <span>Maintenance</span>
          </li>

          <?php if (user_can('fms-maintenance')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-maintenance') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-maintenance">
              <i class="bi bi-tools"></i>
              <span>Preventive Maintenance</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('fms-tire')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-tire') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-tire">
              <i class="bi bi-circle-square"></i>
              <span>Tire Management</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('fms-supply')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-supply') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-supply">
              <i class="bi bi-box"></i>
              <span>Supplies Management</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('fms-tool')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'fms-tool') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>fms-tool">
              <i class="bi bi-wrench"></i>
              <span>Tools Management</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- ============================================ -->
          <!-- REPORTS -->
          <!-- ============================================ -->
          <li class="nav-small-cap">
            <span>Reports</span>
          </li>

          <?php if (user_can('operationsreports')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'operationsreports') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>operationsreports">
              <i class="bi bi-file-earmark-ruled"></i>
              <span>Operations Reports</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('deliveryreports')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'deliveryreports') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>deliveryreports">
              <i class="bi bi-file-earmark-check"></i>
              <span>Delivery Reports</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('billingreports')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'billingreports') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>billingreports">
              <i class="bi bi-bar-chart"></i>
              <span>Billing Reports</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if (user_can('maintenancereports')): ?>
          <li class="sidebar-item <?= strpos($current_url, 'maintenancereports') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>maintenancereports">
              <i class="bi bi-graph-up"></i>
              <span>Maintenance Reports</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- ============================================ -->
          <!-- ADMINISTRATION -->
          <!-- ============================================ -->
          <?php if (user_can('usermanagement')): ?>
          <li class="nav-small-cap">
            <span>Administration</span>
          </li>

          <li class="sidebar-item <?= strpos($current_url, 'usermanagement') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>usermanagement">
              <i class="bi bi-person-gear"></i>
              <span>User Management</span>
            </a>
          </li>
          <?php endif; ?>

        </ul>
      </nav>

      <!-- Logout Section at Bottom -->
      <div class="sidebar-footer">
        <button class="logout-link" id="logoutBtn">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </button>
      </div>
    </aside>
    <!-- Sidebar End -->

    <div class="page-wrapper<?=$sb_compact ? ' expanded' : '';?>" id="pageWrapper">
      <!-- Header Start -->
      <header class="topbar">
        <div class="with-vertical">
          <nav class="navbar navbar-expand-lg p-0 d-flex justify-content-between">
            <ul class="navbar-nav">
              <li class="nav-item d-block d-xl-none">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                  <i class="bi bi-list"></i>
                </button>
              </li>
              <li class="nav-item d-none d-xl-block">
                <button class="nav-link sidebartoggler" id="sidebarToggle">
                  <i class="bi bi-list"></i>
                </button>
              </li>
            </ul>

            <ul class="navbar-nav flex-row align-items-center pe-4">
              <li class="nav-item dropdown">
                <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                  <div class="d-flex align-items-center">
                    <!-- name & role chip — shown by the Branded / Floating header layouts -->
                    <div class="topbar-user">
                      <span class="tu-name"><?=esc($full_name);?></span>
                      <span class="tu-role"><?=esc($role_name ?: $position);?></span>
                    </div>
                    <div class="user-profile-img">
                      <img src="<?=base_url('assets/images/profile/user-1.jpg')?>" class="rounded-circle" alt="profile" />
                    </div>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="drop1">
                  <div class="profile-dropdown">
                    <div class="dropdown-header">
                      <h5>User Profile</h5>
                    </div>
                    <div class="profile-info">
                      <img src="<?=base_url('assets/images/profile/user-1.jpg')?>" class="rounded-circle" width="45" height="45" alt="profile" />
                      <div>
                        <h6><?=$full_name;?></h6>
                        <span><?=$position;?></span>
                        <span class="d-block"><?=$division . ' - ' . $section;?></span>
                        <?php if ($role_name): ?><span class="d-block"><strong><?=$role_name;?></strong></span><?php endif; ?>
                      </div>
                    </div>
                    <div class="d-grid py-3 px-4">
                      <button class="btn-outline-primary" id="profileLogoutBtn">Log Out</button>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </nav>
        </div>
      </header>
      <!-- Header End -->

      <div class="body-wrapper">