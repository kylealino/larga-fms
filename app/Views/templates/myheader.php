<?php 
$this->request = \Config\Services::request();
$this->db = \Config\Database::connect();
$this->session = session();
$this->cuser = $this->session->get('__xsys_myuserzicas__');

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
      `username` = '$this->cuser'"
  );

  $data = $query->getRowArray();
  $full_name = $data['full_name'];
  $position = $data['position'];
  $section = $data['section'];
  $division = $data['division'];
  
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
  <title>QCPD Shooting Range | Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.30.0/tabler-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    /* ============================================ */
    /* QCPD SHOOTING RANGE - TACTICAL DARK/BLUE THEME */
    /* MATCHES LOGIN PAGE EXACTLY */
    /* ============================================ */
    :root {
      --qcpd-bg-dark: #0b0d10;
      --qcpd-bg-card: #0f1217;
      --qcpd-blue: #4fc3ff;
      --qcpd-blue-dark: #0091ea;
      --qcpd-blue-deep: #0a2a44;
      --qcpd-blue-mid: #0b2a44;
      --qcpd-text-white: #f0f4fa;
      --qcpd-text-light: #a0b8cc;
      --qcpd-text-muted: #7a93aa;
      --qcpd-border: rgba(255, 255, 255, 0.06);
      --qcpd-border-light: rgba(0, 150, 255, 0.15);
      --qcpd-hover: rgba(79, 195, 255, 0.08);
      --qcpd-active: rgba(79, 195, 255, 0.12);
      --qcpd-white: #f5f9ff;
      --qcpd-gray: #9ca3af;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: #f0f4f8;
      overflow-x: hidden;
    }

    /* ============================================ */
    /* SIDEBAR - Dark tactical (matches login left side) */
    /* ============================================ */
    .left-sidebar {
      background: var(--qcpd-bg-dark);
      box-shadow: 4px 0 30px rgba(0,0,0,0.4);
      border-right: 1px solid rgba(79, 195, 255, 0.06);
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
    .left-sidebar.collapsed .sidebar-link .ti {
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
      background: var(--qcpd-blue);
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
      border-bottom: 1px solid rgba(79, 195, 255, 0.06);
      background: var(--qcpd-bg-dark);
    }

    .brand-logo a {
      color: var(--qcpd-text-white);
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
      background: linear-gradient(145deg, #0f2840, #0a1a2a);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(79, 195, 255, 0.2);
      box-shadow: 0 6px 16px -4px rgba(0,0,0,0.6);
      flex-shrink: 0;
    }

    .brand-logo .shield-icon i {
      font-size: 20px;
      color: var(--qcpd-blue);
      filter: drop-shadow(0 0 6px rgba(0,150,255,0.2));
    }

    .brand-text {
      font-size: 0.9rem;
      letter-spacing: 0.5px;
      font-weight: 700;
      color: var(--qcpd-text-white);
    }

    .brand-text span {
      color: var(--qcpd-blue);
      font-weight: 800;
      background: linear-gradient(135deg, #4fc3ff, #0091ea);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
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
      color: var(--qcpd-text-muted);
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
      color: var(--qcpd-text-muted);
      border-radius: 10px;
      transition: all 0.2s ease;
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .sidebar-link:hover {
      background: var(--qcpd-hover);
      color: var(--qcpd-text-white);
    }

    .sidebar-item.active .sidebar-link {
      background: var(--qcpd-active);
      color: var(--qcpd-blue);
      border: 1px solid rgba(79, 195, 255, 0.1);
    }

    .sidebar-link i, .sidebar-link .ti {
      font-size: 1.2rem;
      width: 24px;
    }

    /* Sidebar Footer - Logout */
    .sidebar-footer {
      padding: 20px 20px 30px 20px;
      border-top: 1px solid rgba(79, 195, 255, 0.06);
      margin-top: auto;
    }

    .logout-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 16px;
      color: var(--qcpd-text-muted);
      border-radius: 10px;
      transition: all 0.2s ease;
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 500;
      background: rgba(79, 195, 255, 0.04);
      border: 1px solid rgba(79, 195, 255, 0.06);
      cursor: pointer;
      width: 100%;
      text-align: left;
    }

    .logout-link:hover {
      background: rgba(79, 195, 255, 0.12);
      color: var(--qcpd-blue);
      border-color: rgba(79, 195, 255, 0.15);
    }

    .logout-link i {
      font-size: 1.2rem;
      width: 24px;
    }

    /* ============================================ */
    /* LOGOUT CONFIRMATION MODAL */
    /* ============================================ */
    .logout-modal .modal-content {
      background: var(--qcpd-white);
      border-radius: 16px;
      border: 1px solid #d7e2ec;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .logout-modal .modal-header {
      border-bottom: 1px solid #d7e2ec;
      padding: 18px 24px;
    }

    .logout-modal .modal-header .modal-title {
      font-weight: 600;
      color: #0f1f2e;
    }

    .logout-modal .modal-header .modal-title i {
      color: var(--qcpd-blue-dark);
      font-size: 1.2rem;
    }

    .logout-modal .modal-body {
      padding: 30px 24px;
      text-align: center;
    }

    .logout-modal .modal-body .logout-icon {
      font-size: 56px;
      color: var(--qcpd-blue);
      opacity: 0.6;
      margin-bottom: 16px;
    }

    .logout-modal .modal-body h4 {
      font-weight: 600;
      color: #0f1f2e;
      margin-bottom: 8px;
    }

    .logout-modal .modal-body p {
      color: #5d7b93;
      font-size: 0.95rem;
      margin-bottom: 0;
    }

    .logout-modal .modal-footer {
      border-top: 1px solid #d7e2ec;
      padding: 16px 24px;
      gap: 10px;
    }

    .logout-modal .btn-cancel {
      background: #f1f5f9;
      border: 1px solid #d7e2ec;
      border-radius: 8px;
      padding: 8px 24px;
      font-weight: 600;
      font-size: 0.85rem;
      color: #5d7b93;
      transition: 0.2s;
    }

    .logout-modal .btn-cancel:hover {
      background: #e5edf5;
      border-color: #bccfdf;
    }

    .logout-modal .btn-logout-confirm {
      background: var(--qcpd-blue-dark);
      border: none;
      border-radius: 8px;
      padding: 8px 24px;
      font-weight: 600;
      font-size: 0.85rem;
      color: #ffffff;
      transition: 0.2s;
    }

    .logout-modal .btn-logout-confirm:hover {
      background: #0073c4;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 150, 255, 0.3);
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
    /* TOPBAR - Clean white (matches login right side) */
    /* ============================================ */
    .topbar {
      background: var(--qcpd-white);
      border-bottom: 1px solid #d7e2ec;
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
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
      color: #0f1f2e;
    }

    #headerCollapse:hover, .mobile-menu-toggle:hover {
      background: rgba(79, 195, 255, 0.08);
    }

    #headerCollapse:hover i, .mobile-menu-toggle:hover i {
      color: var(--qcpd-blue-dark);
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
      border: 2px solid #d7e2ec;
      transition: 0.2s;
      border-radius: 50%;
      width: 38px;
      height: 38px;
      object-fit: cover;
    }

    .user-profile-img img:hover {
      border-color: var(--qcpd-blue);
      transform: scale(1.05);
    }

    .dropdown-menu {
      border-radius: 12px;
      border: 1px solid #d7e2ec;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      min-width: 260px;
      padding: 0;
    }

    .profile-dropdown {
      background: var(--qcpd-white);
      border-radius: 12px;
      overflow: hidden;
    }

    .profile-dropdown .dropdown-header {
      background: linear-gradient(145deg, #0f2840, #0a1a2a);
      color: var(--qcpd-white);
      padding: 14px 18px;
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
      border-bottom: 1px solid #d7e2ec;
    }

    .profile-info h6 {
      margin: 0;
      font-size: 0.85rem;
      font-weight: 600;
      color: #0f1f2e;
    }

    .profile-info span {
      font-size: 0.65rem;
      color: #3b5b77;
    }

    .btn-outline-primary {
      border-radius: 8px;
      border: 1px solid var(--qcpd-blue-dark);
      color: var(--qcpd-blue-dark);
      background: transparent;
      padding: 8px 16px;
      font-weight: 600;
      font-size: 0.75rem;
      transition: 0.2s;
      width: 100%;
    }

    .btn-outline-primary:hover {
      background: var(--qcpd-blue-dark);
      color: var(--qcpd-white);
    }

    /* Sidebar Overlay */
    .sidebar-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 998;
      display: none;
    }

    .sidebar-overlay.active {
      display: block;
    }

    /* Body Wrapper */
    .body-wrapper {
      background: #f0f4f8;
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
      background: var(--qcpd-white);
      border-top: 1px solid #d7e2ec;
      color: #5d7b93;
      font-size: 0.7rem;
      padding: 12px 24px;
      text-align: center;
    }
  </style>
</head>
<body>
  <!-- Mobile Sidebar Overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div id="main-wrapper">
    <!-- Sidebar Start -->
    <aside class="left-sidebar" id="sidebar">
      <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="<?=site_url();?>myadmindashboard" class="text-nowrap logo-img">
          <div class="d-flex align-items-center gap-2">
            <div class="shield-icon">
              <i class="ti ti-shield-check"></i>
            </div>
            <span class="brand-text">QCPD <span>RANGE</span></span>
          </div>
        </a> 
        <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none d-block d-xl-none" id="closeSidebar">
          <i class="ti ti-x" style="color: var(--qcpd-text-muted); font-size: 1.2rem;"></i>
        </a>
      </div>

      <nav class="sidebar-nav">
        <ul id="sidebarnav" style="list-style: none; padding-left: 0;">
          <!-- DASHBOARD -->
          <li class="sidebar-item <?= strpos($current_url, 'myadmindashboard') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>myadmindashboard">
              <i class="ti ti-dashboard"></i>
              <span>Dashboard</span>
            </a>
          </li>

          <!-- RANGE OPERATIONS -->
          <li class="nav-small-cap">
            <span>RANGE OPERATIONS</span>
          </li>

          <li class="sidebar-item <?= strpos($current_url, 'transactions') !== false ? 'active' : ''; ?>">
              <a class="sidebar-link" href="<?=site_url();?>transactions">
                  <i class="ti ti-receipt"></i>
                  <span>Transactions</span>
              </a>
          </li>

          <!-- PERSONNEL -->
          <li class="nav-small-cap">
            <span>PERSONNEL</span>
          </li>

          <li class="sidebar-item <?= strpos($current_url, 'rangeassistants') !== false ? 'active' : ''; ?>">
              <a class="sidebar-link" href="<?=site_url();?>rangeassistants">
                  <i class="ti ti-users"></i>
                  <span>Range Assistants</span>
              </a>
          </li>

          <!-- FACILITY -->
        <li class="nav-small-cap">
            <span>FACILITY</span>
        </li>

          <li class="sidebar-item <?= strpos($current_url, 'baystatus') !== false ? 'active' : ''; ?>">
              <a class="sidebar-link" href="<?=site_url();?>baystatus">
                  <i class="ti ti-layout-grid"></i>
                  <span>Bay Status</span>
              </a>
          </li>

                    <!-- INVENTORY -->
          <li class="nav-small-cap">
            <span>INVENTORY</span>
          </li>

          <li class="sidebar-item <?= strpos($current_url, 'firearmsinventory') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>firearmsinventory?meaction=MAIN">
              <i class="ti ti-package"></i>
              <span>Firearms &amp; Ammo</span>
            </a>
          </li>

          <li class="sidebar-item <?= strpos($current_url, 'equipmentinventory') !== false ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?=site_url();?>equipmentinventory?meaction=MAIN">
              <i class="ti ti-box"></i>
              <span>Equipment</span>
            </a>
          </li>

          <!-- REPORTS -->
          <li class="nav-small-cap">
              <span>REPORTS</span>
          </li>

          <li class="sidebar-item <?= strpos($current_url, 'rangereports') !== false ? 'active' : ''; ?>">
              <a class="sidebar-link" href="<?=site_url();?>rangereports">
                  <i class="ti ti-file-report"></i>
                  <span>Range Reports</span>
              </a>
          </li>
          
        </ul>
      </nav>

      <!-- Logout Section at Bottom -->
      <div class="sidebar-footer">
        <button class="logout-link" id="logoutBtn">
          <i class="ti ti-logout"></i>
          <span>Logout</span>
        </button>
      </div>
    </aside>
    <!-- Sidebar End -->

    <div class="page-wrapper" id="pageWrapper">
      <!-- Header Start -->
      <header class="topbar">
        <div class="with-vertical">
          <nav class="navbar navbar-expand-lg p-0 d-flex justify-content-between">
            <ul class="navbar-nav">
              <li class="nav-item d-block d-xl-none">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                  <i class="ti ti-menu-2"></i>
                </button>
              </li>
              <li class="nav-item d-none d-xl-block">
                <button class="nav-link sidebartoggler" id="sidebarToggle">
                  <i class="ti ti-menu-2"></i>
                </button>
              </li>
            </ul>

            <ul class="navbar-nav flex-row align-items-center pe-4">
              <li class="nav-item dropdown">
                <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                  <div class="d-flex align-items-center">
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