<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Larga Fleet · Login</title>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
  <!-- Google Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

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
      align-items: center;
      justify-content: center;
      background: #eef5fb;
      padding: 1.5rem;
    }

    /* ============================================================
       MAIN WRAPPER — single page, all-in-one
       ============================================================ */
    .main-wrapper {
      max-width: 1200px;
      width: 100%;
      background: #ffffff;
      border-radius: 40px;
      box-shadow: 0 30px 80px -20px rgba(0, 40, 80, 0.08);
      overflow: hidden;
      display: grid;
      grid-template-columns: 1fr 1fr;
      min-height: 600px;
      border: 1px solid rgba(0, 80, 200, 0.02);
      transition: all 0.3s ease;
    }

    .main-wrapper:hover {
      box-shadow: 0 40px 100px -24px rgba(0, 40, 80, 0.10);
    }

    /* ============================================================
       LEFT PANEL — brand + fleet info
       ============================================================ */
    .left-panel {
      padding: 3rem 3rem 3rem 3.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: linear-gradient(165deg, #f5faff 0%, #eaf3fc 100%);
      position: relative;
      overflow: hidden;
    }

    .left-panel::before {
      content: '';
      position: absolute;
      top: -20%;
      right: -20%;
      width: 70%;
      height: 70%;
      background: radial-gradient(circle at 70% 30%, rgba(0, 110, 230, 0.03) 0%, transparent 60%);
      pointer-events: none;
    }

    .left-panel::after {
      content: '';
      position: absolute;
      bottom: -20%;
      left: -20%;
      width: 60%;
      height: 60%;
      background: radial-gradient(circle at 30% 70%, rgba(70, 180, 255, 0.03) 0%, transparent 60%);
      pointer-events: none;
    }

    .left-content {
      position: relative;
      z-index: 2;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 24px;
    }

    .brand-icon {
      width: 52px;
      height: 52px;
      background: #ffffff;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(0, 80, 200, 0.06);
      box-shadow: 0 8px 20px -8px rgba(0, 70, 160, 0.04);
    }

    .brand-icon i {
      font-size: 28px;
      color: #1a6bb0;
    }

    .brand h1 {
      font-size: 26px;
      font-weight: 800;
      color: #0b2a47;
      letter-spacing: -0.5px;
    }

    .brand h1 span {
      color: #1a6bb0;
    }

    .brand .tag {
      font-size: 10px;
      font-weight: 600;
      color: #1a6bb0;
      background: rgba(26, 107, 176, 0.04);
      padding: 2px 14px;
      border-radius: 30px;
      border: 1px solid rgba(26, 107, 176, 0.04);
      margin-left: 4px;
      letter-spacing: 0.3px;
    }

    .tagline {
      font-size: 14px;
      color: #1e4a6e;
      margin-bottom: 32px;
      padding-left: 4px;
      border-left: 3px solid rgba(26, 107, 176, 0.15);
      padding-left: 16px;
    }

    .tagline i {
      color: #1a6bb0;
      margin-right: 4px;
    }

    /* fleet stats */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 32px;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(2px);
      border-radius: 16px;
      padding: 16px 12px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.5);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      background: #ffffff;
      transform: translateY(-2px);
      border-color: rgba(26, 107, 176, 0.04);
      box-shadow: 0 8px 20px -8px rgba(0, 70, 150, 0.02);
    }

    .stat-number {
      font-size: 24px;
      font-weight: 800;
      color: #0b2a47;
      font-family: 'Inter', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .stat-number i {
      font-size: 18px;
      color: #1a6bb0;
      opacity: 0.5;
    }

    .stat-label {
      font-size: 10px;
      font-weight: 600;
      color: #3f6b8f;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      margin-top: 4px;
    }

    /* feature chips */
    .feature-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .chip {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 500;
      color: #1e4a6e;
      padding: 4px 14px 4px 10px;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 30px;
      border: 1px solid rgba(0, 80, 200, 0.02);
      transition: all 0.2s ease;
    }

    .chip:hover {
      background: #ffffff;
      border-color: rgba(26, 107, 176, 0.04);
      transform: translateY(-1px);
    }

    .chip i {
      font-size: 14px;
      color: #1a6bb0;
      opacity: 0.6;
    }

    .chip .badge {
      font-size: 7px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #1a6bb0;
      background: rgba(26, 107, 176, 0.04);
      padding: 1px 10px;
      border-radius: 30px;
      opacity: 0.5;
    }

    /* ============================================================
       RIGHT PANEL — login form
       ============================================================ */
    .right-panel {
      padding: 3rem 3rem 3rem 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #ffffff;
    }

    .login-header {
      margin-bottom: 28px;
    }

    .login-header .lock-icon {
      width: 48px;
      height: 48px;
      background: #f2f9ff;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(0, 80, 200, 0.04);
      margin-bottom: 14px;
    }

    .login-header .lock-icon i {
      font-size: 24px;
      color: #1a6bb0;
    }

    .login-header h2 {
      font-size: 22px;
      font-weight: 700;
      color: #0b2a47;
      margin-bottom: 2px;
    }

    .login-header p {
      font-size: 14px;
      color: #3f6b8f;
    }

    .login-header p i {
      color: #1a6bb0;
      font-size: 12px;
    }

    /* form */
    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      font-size: 10px;
      font-weight: 700;
      color: #1a4b6e;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      display: block;
      margin-bottom: 6px;
    }

    .form-group label i {
      color: #1a6bb0;
      margin-right: 4px;
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #6f8fa8;
      font-size: 16px;
      transition: color 0.2s;
    }

    .form-control {
      width: 100%;
      padding: 13px 16px 13px 46px;
      border: 1.5px solid #e2ecf5;
      border-radius: 14px;
      font-size: 14px;
      background: #fafcff;
      outline: none;
      transition: all 0.2s;
      font-weight: 500;
      color: #0b2a47;
    }

    .form-control::placeholder {
      color: #8aa3b9;
      font-weight: 400;
    }

    .form-control:focus {
      border-color: #1a6bb0;
      box-shadow: 0 0 0 4px rgba(26, 107, 176, 0.04);
      background: #ffffff;
    }

    .form-control:focus ~ i,
    .form-control:focus + .input-wrap i {
      color: #1a6bb0;
    }

    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 16px 0 24px 0;
      font-size: 13px;
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      color: #1a4b6e;
      font-weight: 500;
    }

    .checkbox-label input {
      width: 16px;
      height: 16px;
      accent-color: #1a6bb0;
      cursor: pointer;
    }

    .forgot-link {
      color: #1a6bb0;
      text-decoration: none;
      font-weight: 600;
      font-size: 13px;
      transition: 0.2s;
      border-bottom: 1px dashed transparent;
    }

    .forgot-link:hover {
      color: #0b4a7a;
      border-bottom-color: #1a6bb0;
    }

    .btn-login {
      width: 100%;
      padding: 15px;
      background: #1a6bb0;
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
      box-shadow: 0 4px 16px rgba(26, 107, 176, 0.08);
    }

    .btn-login:hover {
      background: #0f5a99;
      transform: translateY(-2px);
      box-shadow: 0 8px 28px -4px rgba(26, 107, 176, 0.14);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .btn-login i {
      font-size: 18px;
    }

    .login-footer {
      text-align: center;
      margin-top: 28px;
      padding-top: 18px;
      border-top: 1px solid #e8f0f8;
      font-size: 11px;
      color: #4b6f92;
    }

    .login-footer .version {
      background: rgba(26, 107, 176, 0.02);
      padding: 2px 16px;
      border-radius: 30px;
      font-weight: 600;
      color: #1a6bb0;
      display: inline-block;
      border: 1px solid rgba(26, 107, 176, 0.02);
    }

    .login-footer p {
      margin-top: 6px;
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 920px) {
      .main-wrapper {
        grid-template-columns: 1fr;
        min-height: auto;
        border-radius: 28px;
      }
      .left-panel {
        padding: 2rem 2rem 1.5rem 2rem;
        border-bottom: 1px solid rgba(0, 80, 200, 0.04);
      }
      .right-panel {
        padding: 2rem 2rem 2.5rem 2rem;
      }
      .stats-grid {
        gap: 8px;
      }
      .stat-card {
        padding: 12px 8px;
      }
      .stat-number {
        font-size: 20px;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 0.8rem;
      }
      .main-wrapper {
        border-radius: 20px;
      }
      .left-panel {
        padding: 1.5rem 1.2rem 1rem 1.2rem;
      }
      .right-panel {
        padding: 1.5rem 1.2rem 2rem 1.2rem;
      }
      .brand h1 {
        font-size: 20px;
      }
      .brand .tag {
        font-size: 8px;
        padding: 1px 10px;
      }
      .tagline {
        font-size: 13px;
        margin-bottom: 20px;
        padding-left: 12px;
      }
      .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
      }
      .stat-card {
        padding: 10px 6px;
      }
      .stat-number {
        font-size: 17px;
      }
      .stat-number i {
        font-size: 14px;
      }
      .stat-label {
        font-size: 8px;
        letter-spacing: 0.4px;
      }
      .feature-chips {
        gap: 4px;
      }
      .chip {
        font-size: 10px;
        padding: 3px 10px 3px 8px;
      }
      .chip .badge {
        font-size: 6px;
        padding: 0px 8px;
      }
      .login-header h2 {
        font-size: 19px;
      }
      .form-options {
        flex-wrap: wrap;
        gap: 8px;
      }
    }
  </style>
</head>
<body>

  <!-- ============================================================
       MAIN WRAPPER — single page, all-in-one
       ============================================================ -->
  <div class="main-wrapper">

    <!-- ============================================================
       LEFT PANEL — brand + fleet info
       ============================================================ -->
    <div class="left-panel">
      <div class="left-content">

        <div class="brand">
          <div class="brand-icon">
            <i class="bi bi-truck-front-fill"></i>
          </div>
          <h1>Larga<span>.</span> <span style="font-weight:400; color:#4b6f92;">Fleet</span></h1>
          <span class="tag"><i class="bi bi-dot" style="font-size:18px;"></i> Enterprise</span>
        </div>

        <div class="tagline">
          <i class="bi bi-shield-check"></i> Secure Fleet Management · Command with confidence
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-number"><i class="bi bi-truck"></i> 24</div>
            <div class="stat-label">Vehicles</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><i class="bi bi-person-badge"></i> 14</div>
            <div class="stat-label">Drivers</div>
          </div>
          <div class="stat-card">
            <div class="stat-number" style="font-size: 18px; gap: 4px;">
              <i class="bi bi-clock"></i>
              <span style="background:rgba(26,107,176,0.04); padding:2px 10px; border-radius:6px; font-size:17px;">06:00</span>
              <span style="color:#1a6bb0; opacity:0.2;">—</span>
              <span style="background:rgba(26,107,176,0.04); padding:2px 10px; border-radius:6px; font-size:17px;">22:00</span>
            </div>
            <div class="stat-label">Ops Hours</div>
          </div>
        </div>

        <!-- Feature chips -->
        <div class="feature-chips">
          <span class="chip"><i class="bi bi-diagram-3"></i> Dispatch <span class="badge">Ops</span></span>
          <span class="chip"><i class="bi bi-tools"></i> Maintenance <span class="badge">Workshop</span></span>
          <span class="chip"><i class="bi bi-fuel-pump"></i> Fuel Logs <span class="badge">Inventory</span></span>
          <span class="chip"><i class="bi bi-activity"></i> Telematics <span class="badge">Analytics</span></span>
          <span class="chip"><i class="bi bi-people"></i> Drivers <span class="badge">Personnel</span></span>
          <span class="chip"><i class="bi bi-file-earmark-bar-graph"></i> Reports <span class="badge">BI</span></span>
        </div>
      </div>
    </div>

    <!-- ============================================================
       RIGHT PANEL — login form
       ============================================================ -->
    <div class="right-panel">

      <div class="login-header">
        <div class="lock-icon">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h2>Welcome back</h2>
        <p><i class="bi bi-key-fill"></i> Sign in to manage your fleet operations</p>
      </div>

      <!-- 
        ============================================================
        FORM — EXACT SAME IDs AND ACTION AS ORIGINAL
        MyUsername, MyPassword, mylogin-auth — DO NOT CHANGE
        ============================================================
      -->
      <form action="<?= site_url(); ?>mylogin-auth" method="post" novalidate>
        <div class="form-group">
          <label><i class="bi bi-person-circle"></i> Username / Employee ID</label>
          <div class="input-wrap">
            <i class="bi bi-person"></i>
            <input type="text" id="MyUsername" name="MyUsername" class="form-control" placeholder="Enter your username or ID" autocomplete="off" />
          </div>
        </div>

        <div class="form-group">
          <label><i class="bi bi-lock"></i> Password</label>
          <div class="input-wrap">
            <i class="bi bi-lock-fill"></i>
            <input type="password" id="MyPassword" name="MyPassword" class="form-control" placeholder="Enter your secure password" />
          </div>
        </div>

        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="remember" /> <span>Stay signed in</span>
          </label>
          <a href="#" class="forgot-link">Reset credentials</a>
        </div>

        <button type="submit" class="btn-login">
          <i class="bi bi-box-arrow-in-right"></i> Sign In
        </button>

        <div class="login-footer">
          <span class="version"><i class="bi bi-dash-circle"></i> v4.2 · secure</span>
          <p>© 2026 Larga Fleet · all rights reserved</p>
        </div>
      </form>

    </div>
  </div>

  <!-- Toastr + jQuery (for flash feedback) — EXACT SAME AS ORIGINAL -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

  <script>
    toastr.options = {
      closeButton: true,
      progressBar: true,
      positionClass: "toast-top-right",
      timeOut: 4500,
      showDuration: 300,
      hideDuration: 1000
    };

    // Client-side validation — EXACT SAME AS ORIGINAL (uses MyUsername, MyPassword)
    $('form').on('submit', function(e) {
      const username = $('#MyUsername').val().trim();
      const password = $('#MyPassword').val();

      if (!username) {
        e.preventDefault();
        toastr.warning('Please enter your username or employee ID', 'Missing field');
        $('#MyUsername').focus();
      } else if (!password) {
        e.preventDefault();
        toastr.warning('Please enter your password', 'Missing field');
        $('#MyPassword').focus();
      }
    });

    // Flashdata error (from CI) — EXACT SAME AS ORIGINAL
    <?php if(session()->getFlashdata('login_error')): ?>
      toastr.error('<?= session()->getFlashdata('login_error') ?>', 'Authentication failed');
    <?php endif; ?>
  </script>

</body>
</html>