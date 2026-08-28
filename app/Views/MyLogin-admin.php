<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QCPD Shooting Range · Admin Login</title>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Google Fonts: Inter + JetBrains Mono -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      background: #0b0d10;
    }

    /* ============================================ */
    /* LEFT SIDE — QCPD BRAND */
    /* ============================================ */
    .left {
      flex: 1;
      background: #0f1217;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      position: relative;
      overflow: hidden;
    }

    /* subtle glow effects */
    .left::before {
      content: '';
      position: absolute;
      top: -30%;
      right: -20%;
      width: 80%;
      height: 80%;
      background: radial-gradient(circle, rgba(0, 120, 220, 0.05) 0%, transparent 70%);
      pointer-events: none;
    }

    .left::after {
      content: '';
      position: absolute;
      bottom: -20%;
      left: -20%;
      width: 60%;
      height: 60%;
      background: radial-gradient(circle, rgba(79, 195, 255, 0.03) 0%, transparent 70%);
      pointer-events: none;
    }

    .left-content {
      max-width: 520px;
      position: relative;
      z-index: 2;
      width: 100%;
    }

    /* Brand Header */
    .brand-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .shield-icon {
      width: 52px;
      height: 52px;
      background: linear-gradient(145deg, #0f2840, #0a1a2a);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(0, 150, 255, 0.2);
      box-shadow: 0 6px 16px -4px rgba(0,0,0,0.6);
      flex-shrink: 0;
      transition: all 0.3s ease;
    }

    .shield-icon:hover {
      border-color: rgba(79, 195, 255, 0.4);
      box-shadow: 0 6px 20px -4px rgba(79, 195, 255, 0.15);
    }

    .shield-icon i {
      font-size: 26px;
      color: #4fc3ff;
      filter: drop-shadow(0 0 6px rgba(0,150,255,0.2));
    }

    .brand-title {
      font-size: 20px;
      font-weight: 700;
      color: #f0f4fa;
      letter-spacing: -0.2px;
      line-height: 1.2;
    }

    .brand-title span {
      color: #4fc3ff;
      font-weight: 800;
      background: linear-gradient(135deg, #4fc3ff, #0091ea);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .brand-title .light {
      font-weight: 400;
      color: #a0b8cc;
      -webkit-text-fill-color: #a0b8cc;
      background: none;
    }

    /* Description */
    .desc {
      color: #a0b8cc;
      font-size: 14px;
      line-height: 1.7;
      margin: 4px 0 28px 0;
      padding-left: 18px;
      border-left: 3px solid rgba(79, 195, 255, 0.25);
    }

    .desc i {
      color: #4fc3ff;
      margin-right: 4px;
    }

    /* Stats - Fixed alignment with proper spacing */
    .stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 32px;
    }

    .stat-item {
      background: rgba(255, 255, 255, 0.02);
      border-radius: 14px;
      padding: 16px 14px;
      border: 1px solid rgba(255, 255, 255, 0.04);
      transition: all 0.3s ease;
      text-align: center;
    }

    .stat-item:hover {
      background: rgba(0, 150, 255, 0.05);
      border-color: rgba(79, 195, 255, 0.15);
      transform: translateY(-3px);
    }

    .stat-value {
      font-size: 24px;
      font-weight: 800;
      color: #eef4fa;
      font-family: 'JetBrains Mono', monospace;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .stat-value i {
      font-size: 18px;
      color: #4fc3ff;
      opacity: 0.7;
    }

    /* Fixed: Range Operations - properly centered and aligned */
    .stat-value.time-range {
      font-size: 16px;
      font-weight: 700;
      gap: 4px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .stat-value.time-range .time-block {
      background: rgba(79, 195, 255, 0.1);
      padding: 2px 10px;
      border-radius: 6px;
      border: 1px solid rgba(79, 195, 255, 0.12);
      font-weight: 700;
      letter-spacing: 0.5px;
      font-size: 15px;
    }

    .stat-value.time-range .sep {
      color: #4fc3ff;
      opacity: 0.3;
      font-weight: 300;
      font-size: 14px;
    }

    .stat-value.time-range i {
      font-size: 16px;
      opacity: 0.7;
    }

    .stat-label {
      font-size: 10px;
      font-weight: 600;
      color: #7a93aa;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-top: 4px;
    }

    /* Features / Modules List */
    .features {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px 16px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.04);
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #b0c8dd;
      font-size: 12px;
      padding: 6px 0;
      transition: all 0.2s ease;
    }

    .feature-item:hover {
      color: #d6e5f5;
      transform: translateX(4px);
    }

    .feature-item i {
      font-size: 16px;
      color: #4fc3ff;
      width: 20px;
      text-align: center;
      flex-shrink: 0;
      opacity: 0.6;
    }

    .feature-item .module-tag {
      font-size: 8px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #4fc3ff;
      background: rgba(79, 195, 255, 0.08);
      padding: 1px 8px;
      border-radius: 10px;
      margin-left: auto;
      opacity: 0.5;
    }

    .feature-item strong {
      color: #d6e5f5;
      font-weight: 600;
    }

    /* ============================================ */
    /* RIGHT SIDE — LOGIN */
    /* ============================================ */
    .right {
      width: 480px;
      background: #f5f9ff;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: -8px 0 40px rgba(0, 0, 0, 0.25);
    }

    .login-wrap {
      width: 100%;
      max-width: 380px;
      padding: 2.2rem 1.8rem;
    }

    .logo-area {
      text-align: center;
      margin-bottom: 24px;
    }

    .logo-icon {
      width: 72px;
      height: 72px;
      background: linear-gradient(145deg, #0f2840, #0a1a2a);
      border-radius: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 12px;
      border: 1px solid rgba(0, 150, 255, 0.15);
      box-shadow: 0 10px 20px -8px rgba(0, 40, 70, 0.3);
      transition: all 0.3s ease;
    }

    .logo-icon:hover {
      transform: scale(1.03);
      border-color: rgba(79, 195, 255, 0.3);
    }

    .logo-icon i {
      font-size: 34px;
      color: #4fc3ff;
      filter: drop-shadow(0 0 8px rgba(0,150,255,0.15));
    }

    .logo-area h2 {
      font-size: 18px;
      font-weight: 700;
      color: #0f1f2e;
      letter-spacing: 0.5px;
    }

    .logo-area .sub {
      font-size: 11px;
      font-weight: 500;
      color: #3b5b77;
      background: rgba(0, 80, 150, 0.05);
      padding: 3px 14px;
      border-radius: 40px;
      display: inline-block;
      margin-top: 4px;
      letter-spacing: 0.5px;
    }

    .header {
      text-align: center;
      margin-bottom: 26px;
    }

    .header h3 {
      font-size: 22px;
      font-weight: 700;
      color: #0b1f2e;
      margin-bottom: 2px;
    }

    .header p {
      font-size: 13px;
      color: #4d6a82;
    }

    /* Form */
    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      font-size: 10px;
      font-weight: 700;
      color: #1d3a50;
      margin-bottom: 6px;
      display: block;
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .form-group label i {
      color: #4fc3ff;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #5f7f99;
      font-size: 16px;
      transition: color 0.2s;
    }

    .form-control {
      width: 100%;
      padding: 12px 14px 12px 44px;
      border: 1.5px solid #d7e2ec;
      border-radius: 14px;
      font-size: 14px;
      background: white;
      outline: none;
      transition: all 0.2s;
      font-weight: 500;
      color: #0b1f2e;
    }

    .form-control:focus {
      border-color: #1a73a8;
      box-shadow: 0 0 0 4px rgba(0, 100, 200, 0.06);
    }

    .form-control:focus ~ i,
    .form-control:focus + .input-wrapper i {
      color: #1a73a8;
    }

    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      font-size: 13px;
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      color: #1d3a50;
      font-weight: 500;
    }

    .checkbox-label input {
      width: 16px;
      height: 16px;
      accent-color: #0a4b78;
      cursor: pointer;
    }

    .forgot-link {
      color: #0a4b78;
      text-decoration: none;
      font-weight: 600;
      font-size: 13px;
      transition: 0.2s;
      border-bottom: 1px dashed transparent;
    }

    .forgot-link:hover {
      color: #00315a;
      border-bottom-color: #0a4b78;
    }

    .btn-login {
      width: 100%;
      padding: 14px;
      background: #0b2a44;
      color: white;
      border: none;
      border-radius: 14px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      letter-spacing: 0.3px;
      box-shadow: 0 4px 14px rgba(0, 40, 80, 0.15);
    }

    .btn-login:hover {
      background: #0f3e62;
      transform: translateY(-2px);
      box-shadow: 0 10px 24px -6px rgba(0, 40, 80, 0.25);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .btn-login i {
      font-size: 18px;
    }

    .footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 18px;
      border-top: 1px solid #d0deec;
      font-size: 11px;
      color: #5d7b93;
    }

    .footer p {
      line-height: 1.5;
    }

    .footer .badge-version {
      background: rgba(0, 60, 120, 0.04);
      padding: 2px 12px;
      border-radius: 30px;
      font-weight: 600;
      color: #1e4b6b;
      display: inline-block;
      margin-top: 4px;
    }

    /* ============================================ */
    /* RESPONSIVE */
    /* ============================================ */
    @media (max-width: 1000px) {
      .stats { gap: 10px; }
      .stat-item { padding: 14px 10px; }
      .stat-value { font-size: 20px; }
      .stat-value.time-range { font-size: 14px; }
      .features { grid-template-columns: 1fr; }
    }

    @media (max-width: 860px) {
      .left { display: none; }
      .right { width: 100%; }
      .login-wrap { max-width: 420px; }
    }

    @media (max-width: 480px) {
      .right { padding: 1rem; }
      .login-wrap { padding: 1rem; }
      .header h3 { font-size: 20px; }
      .brand-title { font-size: 18px; }
      .stat-value.time-range { font-size: 13px; }
      .stat-value.time-range .time-block { font-size: 13px; padding: 1px 8px; }
      .stats { grid-template-columns: repeat(3, 1fr); gap: 6px; }
      .stat-item { padding: 10px 6px; }
      .stat-value { font-size: 17px; }
      .stat-value i { font-size: 14px; }
      .stat-label { font-size: 8px; letter-spacing: 0.5px; }
    }
  </style>
</head>
<body>

<!-- ========================================================= -->
<!-- LEFT SIDE — QCPD BRAND -->
<!-- ========================================================= -->
<div class="left">
  <div class="left-content">

    <!-- Brand Header -->
    <div class="brand-header">
      <div class="shield-icon">
        <i class="bi bi-shield-fill-check"></i>
      </div>
      <div class="brand-title">
        QCPD <span>Shooting Range</span> <span class="light">Management System</span>
      </div>
    </div>

    <div class="desc">
      <i class="bi bi-dot"></i> Secure range management — lane scheduling, ammunition tracking, qualification records, and personnel access in one centralized platform.
    </div>

    <!-- Stats - Fixed alignment -->
    <div class="stats">
      <div class="stat-item">
        <div class="stat-value"><i class="bi bi-layers-fill"></i> 10</div>
        <div class="stat-label">Active Lanes</div>
      </div>
      <div class="stat-item">
        <div class="stat-value"><i class="bi bi-person-badge"></i> 8+</div>
        <div class="stat-label">Range Assistants</div>
      </div>
      <div class="stat-item">
        <div class="stat-value time-range">
          <i class="bi bi-clock"></i>
          <span class="time-block">08:00</span>
          <span class="sep">—</span>
          <span class="time-block">17:00</span>
        </div>
        <div class="stat-label">Range Operations</div>
      </div>
    </div>

    <!-- Modules / Features -->
    <div class="features">
      <div class="feature-item">
        <i class="bi bi-receipt"></i>
        <span><strong>Transactions</strong></span>
        <span class="module-tag">Range Ops</span>
      </div>
      <div class="feature-item">
        <i class="bi bi-people"></i>
        <span><strong>Range Assistants</strong></span>
        <span class="module-tag">Personnel</span>
      </div>
      <div class="feature-item">
        <i class="bi bi-grid-3x3-gap-fill"></i>
        <span><strong>Bay Status</strong></span>
        <span class="module-tag">Facility</span>
      </div>
      <div class="feature-item">
        <i class="bi bi-shield"></i>
        <span><strong>Firearms &amp; Ammo</strong></span>
        <span class="module-tag">Inventory</span>
      </div>
      <div class="feature-item">
        <i class="bi bi-box"></i>
        <span><strong>Equipment</strong></span>
        <span class="module-tag">Inventory</span>
      </div>
      <div class="feature-item">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span><strong>Range Reports</strong></span>
        <span class="module-tag">Reports</span>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================= -->
<!-- RIGHT SIDE — LOGIN FORM -->
<!-- ========================================================= -->
<div class="right">
  <div class="login-wrap">

    <div class="logo-area">
      <div class="logo-icon">
        <i class="bi bi-shield-lock-fill"></i>
      </div>
      <h2>RANGE COMMAND</h2>
      <span class="sub"><i class="bi bi-key-fill" style="margin-right: 6px;"></i> authorized access only</span>
    </div>

    <div class="header">
      <h3>Welcome back, Officer</h3>
      <p>Sign in to manage range operations</p>
    </div>

    <!-- FORM — action points to your CI controller -->
    <form action="<?= site_url(); ?>mylogin-auth" method="post" novalidate>
      <div class="form-group">
        <label><i class="bi bi-person-circle" style="margin-right: 6px;"></i> Username / Badge ID</label>
        <div class="input-wrapper">
          <i class="bi bi-person"></i>
          <input type="text" id="MyUsername" name="MyUsername" class="form-control" placeholder="Enter your username or badge" autocomplete="off">
        </div>
      </div>

      <div class="form-group">
        <label><i class="bi bi-lock" style="margin-right: 6px;"></i> Password</label>
        <div class="input-wrapper">
          <i class="bi bi-lock-fill"></i>
          <input type="password" id="MyPassword" name="MyPassword" class="form-control" placeholder="Enter your secure password">
        </div>
      </div>

      <div class="options">
        <label class="checkbox-label">
          <input type="checkbox" name="remember"> <span>Stay signed in</span>
        </label>
        <a href="#" class="forgot-link">Reset credentials</a>
      </div>

      <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
      </button>

      <div class="footer">
        <p><i class="bi bi-shield-check" style="color: #0a4b78;"></i> QCPD Shooting Range · Enterprise Management</p>
        <span class="badge-version"><i class="bi bi-dash-circle"></i> v3.2 · secure</span>
        <p style="margin-top: 8px;">© 2026 QCPD · all rights reserved</p>
      </div>
    </form>

  </div>
</div>

<!-- Toastr + jQuery (for flash feedback) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script>
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 4500,
    showDuration: 300,
    hideDuration: 1000
  };

  // Client-side validation
  $('form').on('submit', function(e) {
    const username = $('#MyUsername').val().trim();
    const password = $('#MyPassword').val();

    if (!username) {
      e.preventDefault();
      toastr.warning('Please enter your username or badge ID', 'Missing field');
      $('#MyUsername').focus();
    } else if (!password) {
      e.preventDefault();
      toastr.warning('Please enter your password', 'Missing field');
      $('#MyPassword').focus();
    }
  });

  // Flashdata error (from CI)
  <?php if(session()->getFlashdata('login_error')): ?>
    toastr.error('<?= session()->getFlashdata('login_error') ?>', 'Authentication failed');
  <?php endif; ?>
</script>

</body>
</html>