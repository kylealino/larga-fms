<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Larga Fleet · Sign In</title>
  <link rel="shortcut icon" type="image/png" href="<?=base_url('assets/images/logos/gym-logo.png')?>" />

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
      background: #f4f7fa;
      padding: 1.5rem;
    }

    /* ============================================================
       MAIN WRAPPER
       ============================================================ */
    .main-wrapper {
      max-width: 1080px;
      width: 100%;
      background: #ffffff;
      border-radius: 24px;
      box-shadow: 0 40px 90px -30px rgba(6, 20, 40, 0.35);
      overflow: hidden;
      display: grid;
      grid-template-columns: 1.05fr 1fr;
      min-height: 580px;
    }

    /* ============================================================
       LEFT PANEL — dark brand panel with route motif
       ============================================================ */
    .left-panel {
      position: relative;
      padding: 3.2rem 3rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: linear-gradient(160deg, #071a2e 0%, #0d2c4a 55%, #123f66 100%);
      overflow: hidden;
      color: #eaf2fb;
    }

    /* decorative route line, pure SVG/CSS — subtle, low-key */
    .route-motif {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      opacity: 0.55;
      pointer-events: none;
    }

    .route-motif path {
      fill: none;
      stroke: rgba(120, 185, 255, 0.35);
      stroke-width: 1.4;
      stroke-dasharray: 7 9;
      animation: dash-flow 26s linear infinite;
    }

    .route-motif circle {
      fill: #5fb2ff;
      opacity: 0.9;
    }

    @keyframes dash-flow {
      to { stroke-dashoffset: -600; }
    }

    .left-top, .left-bottom {
      position: relative;
      z-index: 2;
    }

    .brand-mark {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 44px;
    }

    .brand-mark .icon {
      width: 44px;
      height: 44px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .brand-mark .icon i {
      font-size: 22px;
      color: #7fc0ff;
    }

    .brand-mark .name {
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 3px;
      color: #ffffff;
      text-transform: uppercase;
    }

    .brand-mark .name small {
      display: block;
      font-size: 9px;
      font-weight: 500;
      letter-spacing: 2px;
      color: #7fa8cc;
      margin-top: 2px;
    }

    .headline {
      font-size: 32px;
      font-weight: 700;
      line-height: 1.28;
      letter-spacing: -0.5px;
      color: #ffffff;
      max-width: 380px;
      margin-bottom: 16px;
    }

    .headline .accent {
      color: #7fc0ff;
    }

    .sub-copy {
      font-size: 14px;
      line-height: 1.6;
      color: #a9c3dc;
      max-width: 360px;
    }

    .capability-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
      margin-top: 40px;
    }

    .capability-list .cap-item {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 12.5px;
      font-weight: 500;
      color: #cfe1f2;
      padding-bottom: 14px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .capability-list .cap-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .capability-list .cap-item i {
      font-size: 15px;
      color: #5fb2ff;
      width: 20px;
      text-align: center;
      flex-shrink: 0;
    }

    .left-bottom {
      font-size: 11px;
      color: #6f93b5;
      padding-top: 24px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* ============================================================
       RIGHT PANEL — login form
       ============================================================ */
    .right-panel {
      padding: 3.4rem 3.4rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #ffffff;
    }

    .form-shell {
      width: 100%;
      max-width: 340px;
      margin: 0 auto;
    }

    .eyebrow {
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #1a6bb0;
      margin-bottom: 10px;
    }

    .right-panel h2 {
      font-size: 25px;
      font-weight: 700;
      color: #0b2a47;
      letter-spacing: -0.4px;
      margin-bottom: 6px;
    }

    .right-panel .lede {
      font-size: 13.5px;
      color: #5c7690;
      margin-bottom: 30px;
      line-height: 1.5;
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      font-size: 11px;
      font-weight: 700;
      color: #24486a;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: block;
      margin-bottom: 7px;
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap > i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #8aa3b9;
      font-size: 15px;
      pointer-events: none;
      transition: color 0.2s;
    }

    .form-control {
      width: 100%;
      padding: 12px 16px 12px 44px;
      border: 1.5px solid #dde8f2;
      border-radius: 10px;
      font-size: 14px;
      background: #fbfdff;
      outline: none;
      transition: all 0.2s;
      font-weight: 500;
      color: #0b2a47;
    }

    .form-control::placeholder {
      color: #a4b8c9;
      font-weight: 400;
    }

    .form-control:focus {
      border-color: #1a6bb0;
      box-shadow: 0 0 0 3.5px rgba(26, 107, 176, 0.1);
      background: #ffffff;
    }

    .input-wrap:focus-within > i {
      color: #1a6bb0;
    }

    .toggle-visibility {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #8aa3b9;
      cursor: pointer;
      font-size: 15px;
      padding: 4px;
      display: flex;
    }

    .toggle-visibility:hover {
      color: #1a6bb0;
    }

    .btn-login {
      width: 100%;
      padding: 13px;
      margin-top: 6px;
      background: #1a6bb0;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 14.5px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      letter-spacing: 0.2px;
      box-shadow: 0 10px 24px -8px rgba(26, 107, 176, 0.45);
    }

    .btn-login:hover {
      background: #145a95;
      box-shadow: 0 14px 30px -8px rgba(26, 107, 176, 0.5);
    }

    .btn-login:active {
      transform: translateY(1px);
    }

    .btn-login i {
      font-size: 16px;
    }

    .form-footnote {
      text-align: center;
      margin-top: 22px;
      font-size: 12px;
      color: #7086a0;
    }

    .form-footnote i {
      color: #1a6bb0;
      margin-right: 3px;
    }

    .right-panel .copyright {
      text-align: center;
      margin-top: 34px;
      font-size: 10.5px;
      color: #a7b7c6;
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 900px) {
      .main-wrapper {
        grid-template-columns: 1fr;
        min-height: auto;
        border-radius: 20px;
      }
      .left-panel {
        padding: 2.2rem 2rem;
      }
      .headline {
        font-size: 24px;
        max-width: 100%;
      }
      .sub-copy {
        max-width: 100%;
      }
      .capability-list {
        display: none;
      }
      .right-panel {
        padding: 2.4rem 1.8rem;
      }
    }

    @media (max-width: 460px) {
      body { padding: 0.75rem; }
      .main-wrapper { border-radius: 16px; }
      .left-panel { padding: 1.8rem 1.5rem; }
      .brand-mark { margin-bottom: 28px; }
      .headline { font-size: 21px; }
      .right-panel { padding: 2rem 1.4rem; }
    }
  </style>
</head>
<body>

  <div class="main-wrapper">

    <!-- ============================================================
       LEFT PANEL — brand + operations narrative
       ============================================================ -->
    <div class="left-panel">
      <svg class="route-motif" viewBox="0 0 400 580" preserveAspectRatio="none">
        <path d="M -20 480 C 80 420, 60 320, 160 300 S 300 200, 260 100 S 380 20, 420 -20" />
        <circle cx="-20" cy="480" r="3.5" />
        <circle cx="260" cy="100" r="3.5" />
      </svg>

      <div class="left-top">
        <div class="brand-mark">
          <div class="icon"><i class="bi bi-truck-front-fill"></i></div>
          <div class="name">Larga Fleet<small>Fleet Management System</small></div>
        </div>

        <div class="headline">Every route accounted for. <span class="accent">Every asset in view.</span></div>
        <div class="sub-copy">Sign in to coordinate dispatch, maintenance, and billing across your fleet from a single command center.</div>

        <div class="capability-list">
          <div class="cap-item"><i class="bi bi-signpost-split"></i> Trip scheduling & dispatch monitoring</div>
          <div class="cap-item"><i class="bi bi-tools"></i> Preventive maintenance & tire tracking</div>
          <div class="cap-item"><i class="bi bi-receipt"></i> Billing, invoicing & accounts receivable</div>
          <div class="cap-item"><i class="bi bi-shield-lock"></i> Role-based access across every module</div>
        </div>
      </div>

      <div class="left-bottom">
        Trusted by fleet operations teams to keep every truck, trip, and transaction accounted for.
      </div>
    </div>

    <!-- ============================================================
       RIGHT PANEL — login form
       ============================================================ -->
    <div class="right-panel">
      <div class="form-shell">

        <div class="eyebrow">Secure Sign In</div>
        <h2>Welcome back</h2>
        <p class="lede">Enter your credentials to access the operations dashboard.</p>

        <!--
          ============================================================
          FORM — EXACT SAME IDs AND ACTION AS ORIGINAL
          MyUsername, MyPassword, mylogin-auth — DO NOT CHANGE
          ============================================================
        -->
        <form action="<?= site_url(); ?>mylogin-auth" method="post" novalidate>
          <div class="form-group">
            <label>Username / Employee ID</label>
            <div class="input-wrap">
              <i class="bi bi-person"></i>
              <input type="text" id="MyUsername" name="MyUsername" class="form-control" placeholder="Enter your username or ID" autocomplete="off" />
            </div>
          </div>

          <div class="form-group">
            <label>Password</label>
            <div class="input-wrap">
              <i class="bi bi-lock"></i>
              <input type="password" id="MyPassword" name="MyPassword" class="form-control" placeholder="Enter your password" style="padding-right:42px;" />
              <button type="button" class="toggle-visibility" id="togglePassword" tabindex="-1" aria-label="Show password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right"></i> Sign In
          </button>

          <div class="form-footnote">
            <i class="bi bi-info-circle"></i> Access issues? Contact your system administrator.
          </div>
        </form>

        <div class="copyright">© <?= date('Y') ?> Larga Fleet Management System</div>
      </div>
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

    // Show/hide password — cosmetic only, no functional change to auth
    $('#togglePassword').on('click', function() {
      const field = $('#MyPassword');
      const icon = $(this).find('i');
      const isHidden = field.attr('type') === 'password';
      field.attr('type', isHidden ? 'text' : 'password');
      icon.toggleClass('bi-eye bi-eye-slash');
    });

    // Flashdata error (from CI) — key matches MyLogIn::auth()'s setFlashdata()
    <?php if(session()->getFlashdata('mesyszicas_memsg_login')): ?>
      toastr.error('<?= session()->getFlashdata('mesyszicas_memsg_login') ?>', 'Authentication failed');
    <?php endif; ?>
  </script>

</body>
</html>
