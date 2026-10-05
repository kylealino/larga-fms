<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Larga Fleet · Fleet Management System</title>
  <link rel="shortcut icon" type="image/png" href="<?=base_url('assets/images/logos/gym-logo.png')?>" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --primary: #1a6bb0;
      --primary-dark: #0f5a99;
      --navy-1: #071a2e;
      --navy-2: #0d2c4a;
      --navy-3: #123f66;
      --ink: #0b2a47;
      --ink-mid: #24486a;
      --ink-muted: #5c7690;
      --bg: #f4f7fa;
      --border: #e2ecf5;
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', sans-serif;
      color: var(--ink);
      background: #ffffff;
      overflow-x: hidden;
    }

    a { text-decoration: none; }
    ul { list-style: none; }
    img { max-width: 100%; display: block; }

    .container { max-width: 1160px; margin: 0 auto; padding: 0 24px; }

    /* ============================================================
       NAVBAR
       ============================================================ */
    .navbar {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border);
    }

    .navbar .inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 68px;
    }

    .navbar .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 800;
      font-size: 16px;
      color: var(--ink);
    }

    .navbar .brand .icon {
      width: 34px;
      height: 34px;
      background: var(--primary);
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 16px;
    }

    .navbar .brand span { color: var(--primary); }

    .navbar nav {
      display: flex;
      align-items: center;
      gap: 30px;
    }

    .navbar nav a.nav-link {
      font-size: 13.5px;
      font-weight: 600;
      color: var(--ink-mid);
      transition: color 0.2s;
    }

    .navbar nav a.nav-link:hover { color: var(--primary); }

    .btn-nav-signin {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      background: var(--primary);
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      padding: 9px 20px;
      border-radius: 9px;
      box-shadow: 0 8px 18px -8px rgba(26, 107, 176, 0.5);
      transition: all 0.2s;
    }

    .btn-nav-signin:hover { background: var(--primary-dark); transform: translateY(-1px); color: #fff; }

    /* ============================================================
       HERO
       ============================================================ */
    .hero {
      position: relative;
      background: linear-gradient(160deg, var(--navy-1) 0%, var(--navy-2) 55%, var(--navy-3) 100%);
      color: #eaf2fb;
      padding: 90px 0 0;
      overflow: hidden;
    }

    .hero .route-motif {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      opacity: 0.45;
      pointer-events: none;
    }

    .hero .route-motif path {
      fill: none;
      stroke: rgba(120, 185, 255, 0.3);
      stroke-width: 1.4;
      stroke-dasharray: 7 9;
      animation: dash-flow 30s linear infinite;
    }

    .hero .route-motif circle { fill: #5fb2ff; opacity: 0.85; }

    @keyframes dash-flow { to { stroke-dashoffset: -800; } }

    .hero .inner { position: relative; z-index: 2; text-align: center; max-width: 760px; margin: 0 auto; }

    .hero .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #7fc0ff;
      background: rgba(127, 192, 255, 0.1);
      border: 1px solid rgba(127, 192, 255, 0.2);
      padding: 6px 16px;
      border-radius: 30px;
      margin-bottom: 26px;
    }

    .hero h1 {
      font-size: 44px;
      font-weight: 800;
      line-height: 1.2;
      letter-spacing: -0.8px;
      color: #ffffff;
      margin-bottom: 18px;
    }

    .hero h1 .accent { color: #7fc0ff; }

    .hero .sub {
      font-size: 15.5px;
      line-height: 1.7;
      color: #a9c3dc;
      max-width: 600px;
      margin: 0 auto 32px;
    }

    .hero .cta-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .btn-primary-lg {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      background: var(--primary);
      color: #fff;
      font-size: 14.5px;
      font-weight: 700;
      padding: 15px 28px;
      border-radius: 11px;
      box-shadow: 0 14px 30px -8px rgba(26, 107, 176, 0.55);
      transition: all 0.2s;
    }

    .btn-primary-lg:hover { background: var(--primary-dark); transform: translateY(-2px); color: #fff; }

    .btn-ghost-lg {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.18);
      color: #eaf2fb;
      font-size: 14.5px;
      font-weight: 600;
      padding: 15px 26px;
      border-radius: 11px;
      transition: all 0.2s;
    }

    .btn-ghost-lg:hover { background: rgba(255, 255, 255, 0.1); color: #fff; }

    .hero .fine-print {
      font-size: 12px;
      color: #7fa8cc;
      margin-bottom: 50px;
    }

    /* hero showcase visual — real dashboard screenshot in a browser frame */
    .hero-showcase {
      position: relative;
      z-index: 2;
      max-width: 980px;
      margin: 0 auto;
      padding: 0 24px;
    }

    /* ============================================================
       BROWSER FRAME — wraps every real screenshot
       ============================================================ */
    .browser-frame {
      background: #ffffff;
      border-radius: 14px 14px 8px 8px;
      overflow: hidden;
      box-shadow: 0 40px 90px -30px rgba(0, 10, 30, 0.5);
      border: 1px solid rgba(255,255,255,0.08);
    }

    .browser-frame .chrome-bar {
      background: #eef2f6;
      padding: 10px 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      border-bottom: 1px solid #dfe7ee;
    }

    .browser-frame .chrome-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #d3dae2;
    }

    .browser-frame .chrome-url {
      flex: 1;
      background: #ffffff;
      border: 1px solid #dfe7ee;
      border-radius: 6px;
      padding: 4px 12px;
      font-size: 11px;
      color: #7086a0;
      font-family: 'SF Mono', Menlo, monospace;
      margin-left: 6px;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    .browser-frame img {
      width: 100%;
      display: block;
      cursor: zoom-in;
    }

    .hero-showcase .browser-frame { transform: translateY(0); }

    /* ============================================================
       LOGO / MODULE STRIP
       ============================================================ */
    .module-strip {
      position: relative;
      z-index: 2;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
      max-width: 800px;
      margin: 46px auto 0;
      padding: 0 24px 70px;
    }

    .module-strip span {
      font-size: 11.5px;
      font-weight: 600;
      color: #cfe1f2;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 6px 14px;
      border-radius: 30px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .module-strip i { color: #5fb2ff; font-size: 12px; }

    /* ============================================================
       SECTION HEADERS
       ============================================================ */
    .section-head {
      text-align: center;
      max-width: 640px;
      margin: 0 auto 56px;
    }

    .section-head .eyebrow-sm {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--primary);
      margin-bottom: 12px;
    }

    .section-head h2 {
      font-size: 30px;
      font-weight: 800;
      letter-spacing: -0.5px;
      color: var(--ink);
      margin-bottom: 12px;
    }

    .section-head p {
      font-size: 14.5px;
      color: var(--ink-muted);
      line-height: 1.6;
    }

    /* ============================================================
       ZIG-ZAG PRODUCT SHOWCASE
       ============================================================ */
    .showcase { padding: 100px 0 40px; }

    .showcase-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
      margin-bottom: 100px;
    }

    .showcase-row.reverse .showcase-copy { order: 2; }
    .showcase-row.reverse .showcase-visual { order: 1; }

    .showcase-copy .tag {
      display: inline-block;
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--primary);
      background: rgba(26, 107, 176, 0.08);
      padding: 5px 14px;
      border-radius: 20px;
      margin-bottom: 16px;
    }

    .showcase-copy h3 {
      font-size: 25px;
      font-weight: 800;
      color: var(--ink);
      letter-spacing: -0.4px;
      margin-bottom: 14px;
      line-height: 1.3;
    }

    .showcase-copy p {
      font-size: 14px;
      line-height: 1.7;
      color: var(--ink-muted);
      margin-bottom: 18px;
    }

    .showcase-copy ul li {
      font-size: 13px;
      color: var(--ink-mid);
      padding: 5px 0;
      display: flex;
      align-items: flex-start;
      gap: 9px;
    }

    .showcase-copy ul li i { color: var(--primary); font-size: 13px; margin-top: 2px; }

    .showcase-visual .browser-frame { box-shadow: 0 26px 60px -24px rgba(15, 60, 110, 0.28); }

    /* ============================================================
       SCREEN GALLERY (secondary screenshots)
       ============================================================ */
    .gallery { background: var(--bg); padding: 90px 0; }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 26px;
    }

    .gallery-item .browser-frame { box-shadow: 0 20px 44px -20px rgba(15, 60, 110, 0.2); }

    .gallery-item .caption {
      margin-top: 14px;
      text-align: center;
    }

    .gallery-item .caption h4 {
      font-size: 14.5px;
      font-weight: 700;
      color: var(--ink);
      margin-bottom: 3px;
    }

    .gallery-item .caption p {
      font-size: 12px;
      color: var(--ink-muted);
    }

    /* ============================================================
       QUOTE / MISSION
       ============================================================ */
    .quote-section {
      background: #ffffff;
      padding: 90px 0;
      text-align: center;
    }

    .quote-section blockquote {
      max-width: 760px;
      margin: 0 auto;
      font-size: 26px;
      font-weight: 700;
      line-height: 1.5;
      color: var(--ink);
      letter-spacing: -0.4px;
    }

    .quote-section blockquote i.bi-quote {
      font-size: 30px;
      color: var(--primary);
      opacity: 0.35;
      display: block;
      margin-bottom: 12px;
    }

    .quote-section .cite {
      display: block;
      margin-top: 20px;
      font-size: 12.5px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--primary);
    }

    /* ============================================================
       PRICING
       ============================================================ */
    .pricing { background: var(--bg); padding: 100px 0; }

    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
      align-items: stretch;
    }

    .price-card {
      background: #ffffff;
      border: 1.5px solid var(--border);
      border-radius: 18px;
      padding: 32px 28px;
      display: flex;
      flex-direction: column;
      transition: all 0.25s;
    }

    .price-card:hover { transform: translateY(-4px); box-shadow: 0 24px 50px -24px rgba(15, 60, 110, 0.2); }

    .price-card.featured {
      border-color: var(--primary);
      background: linear-gradient(180deg, #ffffff 0%, #f5faff 100%);
      position: relative;
    }

    .price-card.featured .ribbon {
      position: absolute;
      top: -12px;
      left: 50%;
      transform: translateX(-50%);
      background: var(--primary);
      color: #fff;
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      padding: 5px 16px;
      border-radius: 20px;
      box-shadow: 0 8px 18px -6px rgba(26, 107, 176, 0.5);
    }

    .price-card .plan-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: rgba(26, 107, 176, 0.08);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 18px;
    }

    .price-card .plan-icon i { font-size: 20px; color: var(--primary); }

    .price-card h3 {
      font-size: 18px;
      font-weight: 800;
      color: var(--ink);
      margin-bottom: 6px;
    }

    .price-card .plan-sub {
      font-size: 12.5px;
      color: var(--ink-muted);
      margin-bottom: 20px;
      line-height: 1.5;
      min-height: 38px;
    }

    .price-card .plan-price {
      font-size: 13px;
      font-weight: 700;
      color: var(--primary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border);
    }

    .price-card ul { flex: 1; margin-bottom: 24px; }

    .price-card ul li {
      font-size: 13px;
      color: var(--ink-mid);
      padding: 6px 0;
      display: flex;
      align-items: flex-start;
      gap: 9px;
    }

    .price-card ul li i { color: var(--primary); font-size: 13px; margin-top: 2px; }

    .price-card .btn-plan {
      display: block;
      text-align: center;
      padding: 12px;
      border-radius: 10px;
      font-size: 13.5px;
      font-weight: 700;
      transition: all 0.2s;
    }

    .price-card .btn-plan.solid {
      background: var(--primary);
      color: #fff;
      box-shadow: 0 10px 22px -8px rgba(26, 107, 176, 0.5);
    }

    .price-card .btn-plan.solid:hover { background: var(--primary-dark); }

    .price-card .btn-plan.outline {
      border: 1.5px solid var(--border);
      color: var(--ink);
    }

    .price-card .btn-plan.outline:hover { border-color: var(--primary); color: var(--primary); }

    .pricing-footnote {
      text-align: center;
      font-size: 12px;
      color: var(--ink-muted);
      margin-top: 30px;
    }

    /* ============================================================
       FINAL CTA BAND
       ============================================================ */
    .cta-band {
      background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
      padding: 70px 0;
      text-align: center;
      color: #fff;
    }

    .cta-band h2 {
      font-size: 28px;
      font-weight: 800;
      letter-spacing: -0.4px;
      margin-bottom: 12px;
    }

    .cta-band p {
      font-size: 14.5px;
      color: rgba(255,255,255,0.85);
      margin-bottom: 30px;
    }

    .btn-cta-white {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      background: #ffffff;
      color: var(--primary-dark);
      font-size: 14.5px;
      font-weight: 700;
      padding: 15px 30px;
      border-radius: 11px;
      box-shadow: 0 14px 30px -8px rgba(0,0,0,0.25);
      transition: all 0.2s;
    }

    .btn-cta-white:hover { transform: translateY(-2px); color: var(--primary-dark); }

    .cta-band .footnote {
      margin-top: 20px;
      font-size: 12px;
      color: rgba(255,255,255,0.7);
    }

    /* ============================================================
       FOOTER
       ============================================================ */
    footer {
      background: var(--navy-1);
      color: #7fa8cc;
      padding: 34px 0;
      text-align: center;
    }

    footer .brand-mini {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-weight: 700;
      color: #eaf2fb;
      font-size: 13px;
      margin-bottom: 8px;
    }

    footer p { font-size: 11.5px; }

    /* ============================================================
       LIGHTBOX (click a screenshot to enlarge)
       ============================================================ */
    .lightbox {
      position: fixed;
      inset: 0;
      background: rgba(5, 15, 30, 0.88);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999;
      padding: 30px;
      cursor: zoom-out;
    }

    .lightbox.active { display: flex; }

    .lightbox img {
      max-width: 100%;
      max-height: 100%;
      border-radius: 10px;
      box-shadow: 0 30px 80px rgba(0,0,0,0.5);
    }

    .lightbox .close-hint {
      position: absolute;
      top: 24px;
      right: 30px;
      color: #fff;
      font-size: 13px;
      opacity: 0.7;
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 900px) {
      .navbar nav a.nav-link { display: none; }
      .showcase-row { grid-template-columns: 1fr; gap: 30px; margin-bottom: 70px; }
      .showcase-row.reverse .showcase-copy { order: 1; }
      .showcase-row.reverse .showcase-visual { order: 2; }
      .gallery-grid { grid-template-columns: 1fr; }
      .pricing-grid { grid-template-columns: 1fr; }
      .hero h1 { font-size: 32px; }
    }

    @media (max-width: 560px) {
      .hero { padding: 60px 0 0; }
      .hero h1 { font-size: 26px; }
      .hero .sub { font-size: 14px; }
      .quote-section blockquote { font-size: 20px; }
      .section-head h2 { font-size: 24px; }
      .cta-band h2 { font-size: 22px; }
      .showcase-copy h3 { font-size: 21px; }
    }
  </style>
</head>
<body>

  <!-- ============================================================ -->
  <!-- NAVBAR -->
  <!-- ============================================================ -->
  <header class="navbar">
    <div class="container inner">
      <a href="<?= site_url('landing-page') ?>" class="brand">
        <span class="icon"><i class="bi bi-truck-front-fill"></i></span>
        Larga <span>Fleet</span>
      </a>
      <nav>
        <a href="#showcase" class="nav-link">Product</a>
        <a href="#pricing" class="nav-link">Pricing</a>
        <a href="<?= site_url() ?>" class="btn-nav-signin"><i class="bi bi-box-arrow-in-right"></i> Sign In</a>
      </nav>
    </div>
  </header>

  <!-- ============================================================ -->
  <!-- HERO -->
  <!-- ============================================================ -->
  <section class="hero">
    <svg class="route-motif" viewBox="0 0 1200 500" preserveAspectRatio="none">
      <path d="M -40 420 C 160 340, 300 460, 460 380 S 700 220, 860 260 S 1100 120, 1260 60" />
      <circle cx="-40" cy="420" r="4" />
      <circle cx="860" cy="260" r="4" />
    </svg>

    <div class="container inner">
      <div class="eyebrow"><i class="bi bi-stars"></i> Fleet Management, Simplified</div>
      <h1>Command your entire fleet <span class="accent">from one system.</span></h1>
      <p class="sub">Larga Fleet unifies dispatch, maintenance, billing, and reporting into a single role-based platform — built for teams who treat every truck, trip, and peso as accountable.</p>

      <div class="cta-row">
        <a href="<?= site_url() ?>" class="btn-primary-lg"><i class="bi bi-rocket-takeoff"></i> Start Free Trial</a>
        <a href="#pricing" class="btn-ghost-lg"><i class="bi bi-tags"></i> View Pricing</a>
      </div>
      <div class="fine-print">No credit card required to explore · Real screenshots below, not mockups</div>
    </div>

    <div class="hero-showcase">
      <div class="browser-frame" onclick="openLightbox(this.querySelector('img').src)">
        <div class="chrome-bar">
          <span class="chrome-dot" style="background:#ff5f57;"></span>
          <span class="chrome-dot" style="background:#febc2e;"></span>
          <span class="chrome-dot" style="background:#28c840;"></span>
          <span class="chrome-url"><i class="bi bi-lock-fill" style="font-size:9px;"></i> larga-fms/public/myadmindashboard</span>
        </div>
        <img src="<?= base_url('assets/images/showcase/dashboard.png') ?>" alt="Larga Fleet live operations dashboard" />
      </div>
    </div>

    <div class="module-strip">
      <span><i class="bi bi-truck"></i> Truck & Driver Records</span>
      <span><i class="bi bi-signpost-split"></i> Trip & Dispatch</span>
      <span><i class="bi bi-receipt"></i> Billing & AR</span>
      <span><i class="bi bi-tools"></i> Preventive Maintenance</span>
      <span><i class="bi bi-bar-chart"></i> Live Reports</span>
      <span><i class="bi bi-shield-lock"></i> Role-Based Access</span>
    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ZIG-ZAG PRODUCT SHOWCASE (real screenshots) -->
  <!-- ============================================================ -->
  <section class="showcase" id="showcase">
    <div class="container">

      <div class="section-head">
        <div class="eyebrow-sm">The Actual Product</div>
        <h2>Every screen below is the real system</h2>
        <p>No mockups, no stock photography — this is Larga Fleet running live, module by module.</p>
      </div>

      <!-- Row 1: Dashboard -->
      <div class="showcase-row">
        <div class="showcase-copy">
          <span class="tag">Live Dashboard</span>
          <h3>See your entire operation the moment you log in</h3>
          <p>Fleet, personnel, trip, and billing KPIs pulled straight from your live data — filterable by month and year, not frozen mockup numbers.</p>
          <ul>
            <li><i class="bi bi-check-circle-fill"></i> Fleet, driver & helper availability at a glance</li>
            <li><i class="bi bi-check-circle-fill"></i> Fuel expense trends & route analytics</li>
            <li><i class="bi bi-check-circle-fill"></i> Customer activity & outstanding receivables</li>
          </ul>
        </div>
        <div class="showcase-visual">
          <div class="browser-frame" onclick="openLightbox(this.querySelector('img').src)">
            <div class="chrome-bar">
              <span class="chrome-dot" style="background:#ff5f57;"></span>
              <span class="chrome-dot" style="background:#febc2e;"></span>
              <span class="chrome-dot" style="background:#28c840;"></span>
              <span class="chrome-url">larga-fms/public/myadmindashboard</span>
            </div>
            <img src="<?= base_url('assets/images/showcase/dashboard.png') ?>" alt="Fleet operations dashboard" />
          </div>
        </div>
      </div>

      <!-- Row 2: Dispatch Monitoring -->
      <div class="showcase-row reverse">
        <div class="showcase-copy">
          <span class="tag">Trip & Dispatch</span>
          <h3>Dispatch trucks with total visibility</h3>
          <p>Track every assigned trip from pending to completed — vehicle, driver, origin, destination, and status, all in one board.</p>
          <ul>
            <li><i class="bi bi-check-circle-fill"></i> Real-time dispatch status counts</li>
            <li><i class="bi bi-check-circle-fill"></i> One-click dispatch processing per trip</li>
            <li><i class="bi bi-check-circle-fill"></i> Full trip-to-delivery traceability</li>
          </ul>
        </div>
        <div class="showcase-visual">
          <div class="browser-frame" onclick="openLightbox(this.querySelector('img').src)">
            <div class="chrome-bar">
              <span class="chrome-dot" style="background:#ff5f57;"></span>
              <span class="chrome-dot" style="background:#febc2e;"></span>
              <span class="chrome-dot" style="background:#28c840;"></span>
              <span class="chrome-url">larga-fms/public/fms-dispatch</span>
            </div>
            <img src="<?= base_url('assets/images/showcase/dispatch-monitoring.png') ?>" alt="Dispatch monitoring board" />
          </div>
        </div>
      </div>

      <!-- Row 3: Billing Reports -->
      <div class="showcase-row">
        <div class="showcase-copy">
          <span class="tag">Billing & Receivables</span>
          <h3>From completed trip to collected payment</h3>
          <p>Generate billing summaries, invoices, and payment reports with a live running balance — no more reconciling spreadsheets at month-end.</p>
          <ul>
            <li><i class="bi bi-check-circle-fill"></i> Billing summary, invoice & payment reports</li>
            <li><i class="bi bi-check-circle-fill"></i> Accounts receivable & AR aging</li>
            <li><i class="bi bi-check-circle-fill"></i> Printable statements of account</li>
          </ul>
        </div>
        <div class="showcase-visual">
          <div class="browser-frame" onclick="openLightbox(this.querySelector('img').src)">
            <div class="chrome-bar">
              <span class="chrome-dot" style="background:#ff5f57;"></span>
              <span class="chrome-dot" style="background:#febc2e;"></span>
              <span class="chrome-dot" style="background:#28c840;"></span>
              <span class="chrome-url">larga-fms/public/billingreports</span>
            </div>
            <img src="<?= base_url('assets/images/showcase/billing-reports.png') ?>" alt="Billing reports screen" />
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- SCREEN GALLERY -->
  <!-- ============================================================ -->
  <section class="gallery">
    <div class="container">
      <div class="section-head">
        <div class="eyebrow-sm">More of the System</div>
        <h2>Built out, not bolted on</h2>
        <p>A sample of the modules working together end-to-end.</p>
      </div>

      <div class="gallery-grid">
        <div class="gallery-item">
          <div class="browser-frame" onclick="openLightbox(this.querySelector('img').src)">
            <div class="chrome-bar">
              <span class="chrome-dot" style="background:#ff5f57;"></span>
              <span class="chrome-dot" style="background:#febc2e;"></span>
              <span class="chrome-dot" style="background:#28c840;"></span>
              <span class="chrome-url">larga-fms/public/fms-delivery-receipt</span>
            </div>
            <img src="<?= base_url('assets/images/showcase/delivery-receipt-pod.png') ?>" alt="Proof of delivery capture" />
          </div>
          <div class="caption">
            <h4>Proof of Delivery Capture</h4>
            <p>Signature, photo & document upload per delivery receipt</p>
          </div>
        </div>

        <div class="gallery-item">
          <div class="browser-frame" onclick="openLightbox(this.querySelector('img').src)">
            <div class="chrome-bar">
              <span class="chrome-dot" style="background:#ff5f57;"></span>
              <span class="chrome-dot" style="background:#febc2e;"></span>
              <span class="chrome-dot" style="background:#28c840;"></span>
              <span class="chrome-url">larga-fms/public/invoice</span>
            </div>
            <img src="<?= base_url('assets/images/showcase/invoice.png') ?>" alt="Printable sales invoice" />
          </div>
          <div class="caption">
            <h4>Print-Ready Invoices</h4>
            <p>Professional sales invoices generated straight from billing</p>
          </div>
        </div>

        <div class="gallery-item">
          <div class="browser-frame" onclick="openLightbox(this.querySelector('img').src)">
            <div class="chrome-bar">
              <span class="chrome-dot" style="background:#ff5f57;"></span>
              <span class="chrome-dot" style="background:#febc2e;"></span>
              <span class="chrome-dot" style="background:#28c840;"></span>
              <span class="chrome-url">larga-fms/public/statementofaccount</span>
            </div>
            <img src="<?= base_url('assets/images/showcase/statement-of-account.png') ?>" alt="Customer statement of account" />
          </div>
          <div class="caption">
            <h4>Statement of Account</h4>
            <p>Full running balance per customer, with manual adjustments</p>
          </div>
        </div>

        <div class="gallery-item">
          <div class="browser-frame" onclick="openLightbox(this.querySelector('img').src)">
            <div class="chrome-bar">
              <span class="chrome-dot" style="background:#ff5f57;"></span>
              <span class="chrome-dot" style="background:#febc2e;"></span>
              <span class="chrome-dot" style="background:#28c840;"></span>
              <span class="chrome-url">larga-fms/public/usermanagement</span>
            </div>
            <img src="<?= base_url('assets/images/showcase/user-management-permissions.png') ?>" alt="Role-based permissions matrix" />
          </div>
          <div class="caption">
            <h4>Role-Based Permissions</h4>
            <p>View / add / edit / delete rights, set per role, per module</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================ -->
  <!-- QUOTE / MISSION -->
  <!-- ============================================================ -->
  <section class="quote-section">
    <div class="container">
      <blockquote>
        <i class="bi bi-quote"></i>
        Every route accounted for. Every asset in view. Every peso reconciled — that's the standard Larga Fleet is built to run on.
        <span class="cite">The Larga Fleet Standard</span>
      </blockquote>
    </div>
  </section>

  <!-- ============================================================ -->
  <!-- PRICING -->
  <!-- ============================================================ -->
  <section class="pricing" id="pricing">
    <div class="container">
      <div class="section-head">
        <div class="eyebrow-sm">Get Started</div>
        <h2>Choose how you bring Larga Fleet on board</h2>
        <p>Try it risk-free, subscribe as you grow, or own the system outright — whichever fits how your operation runs.</p>
      </div>

      <div class="pricing-grid">
        <div class="price-card">
          <div class="plan-icon"><i class="bi bi-rocket-takeoff"></i></div>
          <h3>Free Trial</h3>
          <div class="plan-sub">Explore every module before you commit.</div>
          <div class="plan-price">No cost to start</div>
          <ul>
            <li><i class="bi bi-check-circle-fill"></i> Full access to all modules</li>
            <li><i class="bi bi-check-circle-fill"></i> Sample data & guided walkthrough</li>
            <li><i class="bi bi-check-circle-fill"></i> No credit card required</li>
          </ul>
          <a href="<?= site_url() ?>" class="btn-plan outline">Start Free Trial</a>
        </div>

        <div class="price-card featured">
          <span class="ribbon">Most Popular</span>
          <div class="plan-icon"><i class="bi bi-arrow-repeat"></i></div>
          <h3>Subscription</h3>
          <div class="plan-sub">Ongoing access with updates and support included.</div>
          <div class="plan-price">Billed monthly or annually</div>
          <ul>
            <li><i class="bi bi-check-circle-fill"></i> All modules, always up to date</li>
            <li><i class="bi bi-check-circle-fill"></i> Role-based seats for your whole team</li>
            <li><i class="bi bi-check-circle-fill"></i> Priority support & onboarding</li>
          </ul>
          <a href="<?= site_url() ?>" class="btn-plan solid">Subscribe Now</a>
        </div>

        <div class="price-card">
          <div class="plan-icon"><i class="bi bi-award"></i></div>
          <h3>Full System License</h3>
          <div class="plan-sub">Own it outright, deploy on your own infrastructure.</div>
          <div class="plan-price">One-time purchase</div>
          <ul>
            <li><i class="bi bi-check-circle-fill"></i> Perpetual license, self-hosted</li>
            <li><i class="bi bi-check-circle-fill"></i> Full source & customization rights</li>
            <li><i class="bi bi-check-circle-fill"></i> Dedicated setup & data migration</li>
          </ul>
          <a href="<?= site_url() ?>" class="btn-plan outline">Request a Quote</a>
        </div>
      </div>

      <div class="pricing-footnote">Pricing tailored to fleet size and deployment. Sign in or reach out to your Larga Fleet contact to discuss the right plan.</div>
    </div>
  </section>

  <!-- ============================================================ -->
  <!-- FINAL CTA -->
  <!-- ============================================================ -->
  <section class="cta-band">
    <div class="container">
      <h2>Ready to run your fleet with less friction?</h2>
      <p>Sign in and see your operations in one place — or start a free trial today.</p>
      <a href="<?= site_url() ?>" class="btn-cta-white"><i class="bi bi-box-arrow-in-right"></i> Sign In Now</a>
      <div class="footnote">Don't have an account yet? Contact your system administrator to get set up.</div>
    </div>
  </section>

  <!-- ============================================================ -->
  <!-- FOOTER -->
  <!-- ============================================================ -->
  <footer>
    <div class="container">
      <div class="brand-mini"><i class="bi bi-truck-front-fill"></i> Larga Fleet</div>
      <p>© <?= date('Y') ?> Larga Fleet Management System. All rights reserved.</p>
    </div>
  </footer>

  <!-- ============================================================ -->
  <!-- LIGHTBOX -->
  <!-- ============================================================ -->
  <div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <span class="close-hint"><i class="bi bi-x-lg"></i> Click anywhere to close</span>
    <img id="lightboxImg" src="" alt="Enlarged screenshot" />
  </div>

  <script>
    function openLightbox(src) {
      document.getElementById('lightboxImg').src = src;
      document.getElementById('lightbox').classList.add('active');
    }
    function closeLightbox() {
      document.getElementById('lightbox').classList.remove('active');
    }
  </script>

</body>
</html>
