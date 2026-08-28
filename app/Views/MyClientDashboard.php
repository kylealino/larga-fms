<?php
$showDashboard = $showDashboard ?? false;
$rfid_uid = $rfid_uid ?? '';
$currentWeight = 75;
$goalWeight = 80;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<title><?= $showDashboard ? 'My Dashboard' : 'Client Check-In' ?> | MJESHTER FITNESS GYM</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    background: #f8f9fa;
  }

  /* Check-in Page Styles */
  .checkin-wrapper {
    display: flex;
    min-height: 100vh;
  }

  .left {
    flex: 1;
    background: linear-gradient(135deg, #0f0f10, #1a1a1c);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px;
    color: #fff;
  }

  .left-content { max-width: 500px; }

  .brand { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }

  .brand h1 { font-size: 28px; font-weight: 700; }

  .brand span { color: #c41e3a; }

  .desc { color: #9ca3af; font-size: 14px; margin-bottom: 40px; }

  .stats { display: flex; gap: 40px; }

  .stats strong { font-size: 20px; }

  .stats small { font-size: 11px; color: #6b7280; }

  .right {
    width: 420px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: -5px 0 30px rgba(0,0,0,0.1);
  }

  .login-wrap { width: 100%; max-width: 320px; text-align: center; }

  .logo-area { text-align: center; margin-bottom: 32px; }

  .logo-icon {
    width: 70px; height: 70px;
    background: linear-gradient(135deg,#dc2626,#b91c1c);
    border-radius: 20px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 16px;
  }

  .logo-icon i { font-size: 32px; color:#fff; }

  .header { text-align:center; margin-bottom:20px; }

  #rfid_input {
    opacity:0;
    position:absolute;
    height:1px;
    width:1px;
  }

  .scan-card-panel {
    background:#f9fafb;
    border-radius:24px;
    padding:28px 20px;
    border:2px solid #eef2f6;
    cursor:pointer;
    transition:0.3s;
  }

  .scan-icon { font-size:48px; color:#dc2626; margin-bottom:10px; }

  .scan-text { font-size:15px; font-weight:600; }

  .scan-status { font-size:12px; color:#6b7280; margin-top:5px; }

  .footer { font-size:11px; color:#9ca3af; margin-top:20px; }

  /* Dashboard Styles - Enhanced */
  .dashboard-navbar {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 12px 24px;
    width: 100%;
  }
  
  .dashboard-container {
    padding: 24px;
    max-width: 1400px;
    margin: 0 auto;
  }
  
  .stat-card {
    background: #fff;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    height: 100%;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -8px rgba(0,0,0,0.1);
  }
  
  .stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(220, 38, 38, 0.1);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .stat-icon i { font-size: 24px; color: #dc2626; }
  .stat-value { font-size: 28px; font-weight: 700; color: #1a1a1a; }
  .stat-label { font-size: 12px; color: #6c757d; }
  
  .section-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #dc2626;
    display: inline-block;
  }
  
  .timer-alert {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #fee2e2;
    border-radius: 12px;
    padding: 12px 20px;
    color: #dc2626;
    z-index: 999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    font-size: 14px;
  }
  
  .btn-checkout {
    background: #dc2626;
    color: #fff;
    border: none;
    padding: 14px;
    border-radius: 12px;
    font-weight: 600;
    width: 100%;
    transition: all 0.3s;
  }
  
  .btn-checkout:hover {
    background: #b91c1c;
  }
  
  .info-row {
    display: flex;
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .info-label {
    width: 140px;
    font-weight: 500;
    color: #6c757d;
  }
  
  .info-value {
    flex: 1;
    color: #1a1a1a;
  }
  
  .membership-card {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 24px;
    padding: 24px;
    color: #fff;
    margin-bottom: 24px;
  }
  
  /* Progress Photos - 2 columns with emphasis */
  .progress-grid-2cols {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }
  
  .progress-item {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
  }
  
  .progress-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 30px -12px rgba(0,0,0,0.15);
  }
  
  .progress-img-container {
    position: relative;
    overflow: hidden;
    background: #f5f5f5;
    border-radius: 20px 20px 0 0;
    cursor: pointer;
  }
  
  .progress-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    transition: transform 0.4s ease;
    display: block;
  }
  
  .progress-img-container:hover .progress-img {
    transform: scale(1.05);
  }
  
  .progress-badge {
    position: absolute;
    bottom: 12px;
    left: 12px;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    z-index: 10;
  }
  
  .progress-info {
    padding: 16px;
  }
  
  .progress-date {
    font-size: 12px;
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
  }
  
  .progress-notes {
    font-size: 13px;
    color: #4b5563;
    margin-bottom: 0;
    line-height: 1.5;
  }
  
  .empty-progress {
    text-align: center;
    padding: 60px 20px;
    background: #f9fafb;
    border-radius: 24px;
    color: #6c757d;
  }
  
  /* KG Analytics Cards */
  .kg-card {
    background: linear-gradient(135deg, #fef3c7, #fffbeb);
    border-left: 4px solid #f59e0b;
  }
  
  .weight-goal {
    background: #eef2ff;
    border-radius: 16px;
    padding: 16px;
  }
  
  .motivation-tip {
    background: #fff3e0;
    border-left: 4px solid #f59e0b;
    padding: 16px;
    border-radius: 12px;
    margin-top: 16px;
  }
  
  .membership-upgrade {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    border-radius: 20px;
    padding: 20px;
    color: white;
    text-align: center;
  }
  
  .btn-upgrade {
    background: white;
    color: #dc2626;
    border: none;
    padding: 10px 24px;
    border-radius: 40px;
    font-weight: 600;
    transition: all 0.3s;
  }
  
  .btn-upgrade:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

  /* Skeleton Loading Styles */
  .skeleton-wrapper {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }

  .skeleton-item {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
  }

  .skeleton-img {
    width: 100%;
    height: 220px;
    background: #e8e8e8;
    position: relative;
    overflow: hidden;
  }

  .skeleton-img::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, #e8e8e8 25%, #f0f0f0 50%, #e8e8e8 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
  }

  @keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
  }

  .skeleton-text {
    padding: 16px;
  }

  .skeleton-line {
    height: 14px;
    background: #e8e8e8;
    border-radius: 8px;
    margin-bottom: 10px;
    position: relative;
    overflow: hidden;
  }

  .skeleton-line::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, #e8e8e8 25%, #f0f0f0 50%, #e8e8e8 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
  }

  .skeleton-line.short { width: 60%; }
  .skeleton-line.medium { width: 80%; }
  
  @media (max-width: 768px) {
    .checkin-wrapper { flex-direction: column; }
    .right { width: 100%; padding: 40px; }
    .left { display: none; }
    .dashboard-container { padding: 16px; }
    .info-row { flex-direction: column; }
    .info-label { width: 100%; margin-bottom: 4px; }
    .progress-grid-2cols {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .progress-img {
      height: 200px;
    }
    .skeleton-wrapper {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .skeleton-img {
      height: 200px;
    }
  }
</style>
</head>
<body>

<?php if(!$showDashboard): ?>
<!-- CHECK-IN PAGE (unchanged) -->
<div class="checkin-wrapper">
  <div class="left">
    <div class="left-content">
      <div class="brand">
        <h1>MJESHTER <span>FITNESS GYM</span></h1>
      </div>
      <div class="desc">RFID Check-in system for members.</div>
      <div class="stats">
        <div><strong>1200+</strong><small>MEMBERS</small></div>
        <div><strong>98%</strong><small>ACTIVE</small></div>
        <div><strong>OPEN</strong><small>DAILY</small></div>
      </div>
    </div>
  </div>

  <div class="right">
    <div class="login-wrap">
      <div class="logo-area">
        <div class="logo-icon"><i class="bi bi-upc-scan"></i></div>
        <h2>MEMBER CHECK-IN</h2>
        <p>Tap RFID card</p>
      </div>
      <div class="header"><h3>Scan Card</h3></div>

      <form action="<?=site_url();?>myclientdashboard" method="post" id="checkinForm">
        <input type="hidden" name="rfid_uid" id="rfid_uid_hidden">
        <input type="hidden" name="meaction" value="PROCESS-CHECKIN">
        <input type="text" id="rfid_input" autocomplete="off">

        <div class="scan-card-panel" id="scanPanel">
          <div class="scan-icon"><i class="bi bi-credit-card-2-front"></i></div>
          <div class="scan-text" id="scanText">Ready</div>
          <div class="scan-status" id="scanStatus">Waiting RFID...</div>
        </div>
      </form>
      <div class="footer">MJESHTER FITNESS GYM © 2026</div>
    </div>
  </div>
</div>

<audio id="beepSound">
  <source src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg" type="audio/ogg">
</audio>

<script>
toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: 3000 };

(function(){
  const form = document.getElementById('checkinForm');
  const input = document.getElementById('rfid_input');
  const hidden = document.getElementById('rfid_uid_hidden');
  const status = document.getElementById('scanStatus');
  const text = document.getElementById('scanText');
  const beep = document.getElementById('beepSound');
  const scanPanel = document.getElementById('scanPanel');

  let processing = false;

  function playBeep(){
    try { beep.currentTime = 0; beep.play(); } catch(e){}
  }

  function validateRFID(uid){
    if(processing) return;
    processing = true;

    playBeep();
    text.innerText = "Validating...";
    status.innerText = "Checking RFID card...";

    $.ajax({
      type: "POST",
      url: "<?=site_url();?>myclientdashboard",
      data: { rfid_uid: uid, meaction: 'VALIDATE-RFID' },
      dataType: "json",
      success: function(response){
        if(response.status === 'allowed') {
          text.innerText = "Access Granted";
          status.innerText = "Welcome " + response.member_name + "!";
          scanPanel.style.borderColor = "#28a745";
          hidden.value = uid;
          setTimeout(() => { form.submit(); }, 1000);
        } else {
          toastr.error(response.message, 'Access Denied');
          text.innerText = "Access Denied";
          status.innerText = response.message;
          scanPanel.style.borderColor = "#dc3545";
          processing = false;
          setTimeout(() => {
            text.innerText = "Ready";
            status.innerText = "Waiting RFID...";
            scanPanel.style.borderColor = "#eef2f6";
          }, 2000);
        }
      },
      error: function(){ toastr.error('Server error', 'Error'); processing = false; }
    });
  }

  input.addEventListener('keypress', function(e){
    if(e.key === 'Enter'){
      e.preventDefault();
      const uid = this.value.trim();
      this.value = '';
      if(uid !== '') validateRFID(uid);
    }
  });

  setInterval(() => { if(document.activeElement !== input && !processing) input.focus(); }, 500);
})();
</script>

<?php else: ?>
<!-- DASHBOARD PAGE - MONTHLY CHECK-IN ANALYTICS -->
<nav class="dashboard-navbar">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mb-0" style="font-weight: 700;">MJESHTER <span style="color: #dc2626;">FITNESS GYM</span></h4>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="text-muted"><?= $member['first_name'] . ' ' . $member['last_name']; ?></span>
      <a href="<?=site_url();?>myclientdashboard?meaction=LOGOUT" class="btn btn-sm btn-outline-danger">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>
</nav>

<div class="timer-alert" id="timerAlert">
  <i class="bi bi-clock-history me-2"></i> Auto logout in <strong id="timerCount">15</strong> seconds
</div>

<div class="dashboard-container">
  <!-- Welcome Section with Personalized Message -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div>
      <h4 class="fw-bold mb-1">Welcome back, <?= $member['first_name']; ?>! 💪</h4>
      <p class="text-muted mb-0">Member since <?= date('F d, Y', strtotime($member['membership_start_date'])); ?></p>
    </div>
    <span class="badge bg-success px-3 py-2"><i class="bi bi-shield-check"></i> <?= $member['membership_status']; ?> Member</span>
  </div>

    <!-- Membership Card -->
  <div class="membership-card">
    <div class="row align-items-center">
      <div class="col-md-7">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon" style="background: rgba(255,255,255,0.1);">
            <i class="bi bi-star-fill text-white"></i>
          </div>
          <div>
            <h5 class="text-white mb-0"><?= $member['membership_plan']; ?> Plan</h5>
            <p class="text-white-50 mb-0">Valid until <?= date('F d, Y', strtotime($member['membership_end_date'])); ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <div class="small text-white-50">Today's Check-in</div>
        <span class="h3 text-white fw-bold"><?= $checkin_time; ?></span>
      </div>
    </div>
  </div>

  <!-- ⭐ PROGRESS PHOTOS SECTION - MOVED TO TOP FOR EMPHASIS -->
  <div class="stat-card mb-4">
    <h6 class="section-title"><i class="bi bi-images me-2 text-danger"></i> Your Fitness Progress Photos</h6>
    <div id="progressImagesContainer">
      <!-- Skeleton loading will appear here -->
    </div>
  </div>

    <!-- Recent Check-ins (still showing individual logs) -->
  <div class="stat-card mb-4">
    <h6 class="section-title"><i class="bi bi-clock-history me-2 text-danger"></i> Recent Check-ins</h6>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="table-light">
          <tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Duration</th></tr>
        </thead>
        <tbody>
          <?php foreach($recentCheckins as $check): ?>
          <tr>
            <td><?= date('M d, Y', strtotime($check['date'])); ?></td>
            <td><?= $check['time_in']; ?></td>
            <td><?= $check['time_out']; ?></td>
            <td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2"><?= $check['duration']; ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($recentCheckins)): ?>
          <tr><td colspan="4" class="text-center py-4">No check-ins yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>



  <!-- Enhanced Analytics Row (including KG metrics) -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="stat-card text-center">
        <div class="stat-icon mx-auto mb-3"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-value"><?= $yearlyCheckins; ?></div>
        <div class="stat-label">Check-ins in <?= $selectedYear; ?></div>
        <small class="text-muted"><?= round(($yearlyCheckins / max(1, 52)) * 100, 1); ?>% attendance</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card text-center">
        <div class="stat-icon mx-auto mb-3"><i class="bi bi-fire"></i></div>
        <div class="stat-value"><?= $currentStreak ?? 5; ?></div>
        <div class="stat-label">Current Streak 🔥</div>
        <small class="text-muted">Keep it up!</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card text-center kg-card">
        <div class="stat-icon mx-auto mb-3"><i class="bi bi-activity"></i></div>
        <div class="stat-value"><?= $currentWeight ?? 68; ?> kg</div>
        <div class="stat-label">Current Weight</div>
        <small class="text-muted">Goal: <?= $goalWeight ?? 65; ?> kg</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card text-center">
        <div class="stat-icon mx-auto mb-3"><i class="bi bi-arrow-up-short"></i></div>
        <div class="stat-value"><?= $monthlyIncrease ?? '+15'; ?>%</div>
        <div class="stat-label">Monthly Activity</div>
        <small class="text-muted">vs last month</small>
      </div>
    </div>
  </div>

  <!-- KG Weight Trend Chart & Goal -->
  <div class="row g-4 mb-4">
    <div class="col-md-8">
      <div class="stat-card">
        <h6 class="section-title"><i class="bi bi-graph-up me-2 text-danger"></i> Weight Progress (kg) - Last 6 Months</h6>
        <canvas id="weightChart" height="200" style="max-height: 250px; width: 100%;"></canvas>
        <div class="mt-3 text-center">
          <small class="text-muted">📉 Steady progress toward your goal – keep pushing!</small>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <h6 class="section-title"><i class="bi bi-trophy me-2 text-danger"></i> Weight Goal</h6>
        <div class="weight-goal">
          <div class="d-flex justify-content-between mb-2">
            <span>Current: <strong><?= $currentWeight ?? 68; ?> kg</strong></span>
            <span>Target: <strong><?= $goalWeight ?? 65; ?> kg</strong></span>
          </div>
          <div class="progress mb-2" style="height: 12px;">
            <?php $progressPercent = (($currentWeight ?? 68) <= ($goalWeight ?? 65)) ? 100 : max(0, round((1 - (($currentWeight ?? 68) - ($goalWeight ?? 65)) / ($currentWeight ?? 68)) * 100)); ?>
            <div class="progress-bar bg-danger" style="width: <?= $progressPercent; ?>%"></div>
          </div>
          <small class="text-muted"><?= ($currentWeight ?? 68) > ($goalWeight ?? 65) ? ($currentWeight - $goalWeight) . ' kg to go!' : 'Goal achieved! 🎉'; ?></small>
        </div>
        <div class="motivation-tip mt-3">
          <i class="bi bi-quote fs-5 text-warning"></i>
          <p class="mb-0 mt-1 fst-italic">"<?= $motivationQuote ?? 'The only bad workout is the one that didn\'t happen.'; ?>"</p>
        </div>
      </div>
    </div>
  </div>

  <!-- MONTHLY CHECK-IN TREND CHART (replaces weekly) -->
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="stat-card">
        <h6 class="section-title"><i class="bi bi-calendar-month me-2 text-danger"></i> Monthly Check-in Trend (<?= date('Y'); ?>)</h6>
        <canvas id="monthlyCheckinChart" height="200" style="max-height: 250px; width: 100%;"></canvas>
        <div class="mt-3 text-center">
          <small class="text-muted">📊 See your monthly commitment – every visit counts!</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Fitness Achievements / Badges -->
  <div class="stat-card mb-4">
    <h6 class="section-title"><i class="bi bi-award me-2 text-danger"></i> Your Achievements</h6>
    <div class="d-flex gap-4 flex-wrap justify-content-center">
      <div class="text-center">
        <i class="bi bi-calendar-check fs-1 text-warning"></i>
        <div class="small fw-bold">5 Days</div>
        <small class="text-muted">First Week</small>
      </div>
      <div class="text-center">
        <i class="bi bi-fire fs-1 text-danger"></i>
        <div class="small fw-bold">10 Streak</div>
        <small class="text-muted">On Fire!</small>
      </div>
      <div class="text-center">
        <i class="bi bi-trophy fs-1 text-success"></i>
        <div class="small fw-bold">50 Workouts</div>
        <small class="text-muted">Dedication</small>
      </div>
      <div class="text-center">
        <i class="bi bi-star fs-1 text-info"></i>
        <div class="small fw-bold">3 Months</div>
        <small class="text-muted">Loyal Member</small>
      </div>
    </div>
  </div>

  <!-- Membership Upgrade Section -->
  <div class="membership-upgrade mb-4">
    <div class="row align-items-center">
      <div class="col-md-8 text-md-start text-center">
        <h5 class="mb-2"><i class="bi bi-gem me-2"></i> Upgrade Your Membership</h5>
        <p class="mb-0 small opacity-75">Unlock premium features, personal training sessions, and exclusive discounts.</p>
      </div>
      <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
        <button class="btn-upgrade" onclick="alert('Contact front desk for membership upgrades!')">
          <i class="bi bi-arrow-right"></i> Upgrade Now
        </button>
      </div>
    </div>
  </div>



  <!-- Personal Information -->
  <div class="stat-card mb-4">
    <h6 class="section-title"><i class="bi bi-person-badge me-2 text-danger"></i> Personal Information</h6>
    <div class="info-row"><div class="info-label">Full Name</div><div class="info-value"><?= $member['first_name'] . ' ' . $member['last_name']; ?></div></div>
    <div class="info-row"><div class="info-label">Email Address</div><div class="info-value"><?= $member['email']; ?></div></div>
    <div class="info-row"><div class="info-label">Mobile Number</div><div class="info-value"><?= $member['mobile_number']; ?></div></div>
    <div class="info-row"><div class="info-label">Address</div><div class="info-value"><?= $member['address'] ?? 'Not provided'; ?></div></div>
  </div>

  <!-- Checkout Button -->
  <?php if($hasActiveSession ?? false): ?>
  <form action="<?=site_url();?>myclientdashboard" method="post">
    <input type="hidden" name="rfid_uid" value="<?= $rfid_uid; ?>">
    <input type="hidden" name="meaction" value="CHECKOUT">
    <button type="submit" class="btn-checkout"><i class="bi bi-box-arrow-right me-2"></i> Checkout Now</button>
  </form>
  <?php endif; ?>
</div>

<!-- View Image Modal -->
<div class="modal fade view-image-modal" id="viewImageModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-image me-2"></i> Progress Photo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-4">
        <img id="viewImageSrc" src="" class="img-fluid rounded" style="max-height: 70vh;">
        <p id="viewImageNotes" class="mt-3 text-muted"></p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<form id="autoLogoutForm" action="<?=site_url();?>myclientdashboard" method="post" style="display: none;">
  <input type="hidden" name="rfid_uid" value="<?= $rfid_uid; ?>">
  <input type="hidden" name="meaction" value="AUTO-LOGOUT">
</form>

<script>
let currentMemberId = '<?= $member['member_id'] ?? ''; ?>';

// Weight Chart (kg analytics)
const weightCtx = document.getElementById('weightChart').getContext('2d');
new Chart(weightCtx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'Weight (kg)',
      data: [72, 71, 70, 68.5, 67, 68],
      borderColor: '#dc2626',
      backgroundColor: 'rgba(220, 38, 38, 0.1)',
      tension: 0.3,
      fill: true,
      pointBackgroundColor: '#dc2626',
      pointBorderColor: '#fff',
      pointRadius: 5,
      pointHoverRadius: 7
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { position: 'top', labels: { font: { size: 11 } } }
    },
    scales: {
      y: { beginAtZero: false, title: { display: true, text: 'Kilograms (kg)' } }
    }
  }
});

// MONTHLY CHECK-IN CHART (dynamic data from PHP)
const monthlyCtx = document.getElementById('monthlyCheckinChart').getContext('2d');
new Chart(monthlyCtx, {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
      label: 'Check-ins',
      data: <?= json_encode($monthlyCheckins ?? [8, 10, 12, 14, 16, 18, 15, 12, 10, 9, 11, 13]); ?>,
      backgroundColor: '#dc2626',
      borderRadius: 10,
      barPercentage: 0.7
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { position: 'top', labels: { font: { size: 11 } } },
      tooltip: { callbacks: { label: (ctx) => `${ctx.raw} visits` } }
    },
    scales: {
      y: { beginAtZero: true, title: { display: true, text: 'Number of check-ins' }, grid: { color: '#eef2f8' } },
      x: { ticks: { maxRotation: 45, minRotation: 45 } }
    }
  }
});

function viewImage(imageUrl, notes) {
  $('#viewImageSrc').attr('src', imageUrl);
  $('#viewImageNotes').text(notes || 'No notes provided');
  $('#viewImageModal').modal('show');
}

// Show skeleton loading while images are being fetched
function showSkeletonLoading() {
  let skeletonHtml = '<div class="skeleton-wrapper">';
  for (let i = 0; i < 4; i++) {
    skeletonHtml += `
      <div class="skeleton-item">
        <div class="skeleton-img"></div>
        <div class="skeleton-text">
          <div class="skeleton-line short"></div>
          <div class="skeleton-line medium"></div>
        </div>
      </div>
    `;
  }
  skeletonHtml += '</div>';
  $('#progressImagesContainer').html(skeletonHtml);
}

function loadProgressImages() {
    if (!currentMemberId) {
        $('#progressImagesContainer').html(`<div class="empty-progress"><i class="bi bi-camera"></i><p class="mb-0">No progress photos uploaded yet</p><small class="text-muted">Upload photos to track your transformation!</small></div>`);
        return;
    }
    
    // Show skeleton loading immediately
    showSkeletonLoading();

    $.ajax({
        type: "POST",
        url: "<?= site_url(); ?>myclientdashboard/getProgressImages",
        data: { 
            member_id: currentMemberId, 
            meaction: 'GET-PROGRESS-IMAGES' 
        },
        dataType: 'json',
        success: function(response) {
            if(response.status == 'success' && response.data && response.data.length > 0){
                renderProgressImages(response.data);
            } else {
                $('#progressImagesContainer').html(`<div class="empty-progress"><i class="bi bi-camera"></i><p class="mb-0">No progress photos uploaded yet</p><small class="text-muted">Upload photos to track your transformation!</small></div>`);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading images:', error);
            $('#progressImagesContainer').html(`<div class="empty-progress"><i class="bi bi-bug"></i><p class="mb-0">Unable to load progress photos</p><small class="text-muted">Please try again later</small></div>`);
        }
    });
}

function renderProgressImages(images) {
    let html = '<div class="progress-grid-2cols">';
    images.forEach(function(img) {
        // Construct the full image URL - adjust base_url as needed
        let imageUrl = '<?= base_url(); ?>' + '/' + img.image_path;
        let quarterDisplay = img.quarter + ' ' + img.year;
        let uploadedDate = img.uploaded_date || 'Unknown date';
        let notesText = img.notes || '';
        
        html += `
            <div class="progress-item">
                <div class="progress-img-container" onclick="viewImage('${imageUrl}', '${escapeHtml(notesText)}')">
                    <img src="${imageUrl}" class="progress-img" alt="Progress ${quarterDisplay}" onerror="this.src='<?= base_url(); ?>/assets/images/placeholder.jpg'">
                    <div class="progress-badge">
                        <i class="bi bi-calendar3 me-1"></i> ${quarterDisplay}
                    </div>
                </div>
                <div class="progress-info">
                    <div class="progress-date">
                        <i class="bi bi-clock"></i> Uploaded: ${uploadedDate}
                    </div>
                    ${notesText ? `<p class="progress-notes"><i class="bi bi-chat-dots me-1"></i> ${escapeHtml(notesText)}</p>` : ''}
                </div>
            </div>
        `;
    });
    html += '</div>';
    $('#progressImagesContainer').html(html);
}

function escapeHtml(text) {
  if (!text) return '';
  return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

// Auto logout timer
let timer = 15, autoLogoutTriggered = false;
const timerDisplay = document.getElementById('timerCount'), timerAlert = document.getElementById('timerAlert');
const countdown = setInterval(function() {
  timer--; timerDisplay.textContent = timer;
  if(timer <= 5 && timer > 0) { timerAlert.style.background = '#ffcccc'; timerAlert.style.color = '#990000'; }
  if(timer <= 0 && !autoLogoutTriggered) {
    clearInterval(countdown); autoLogoutTriggered = true;
    toastr.info('Auto logging out due to inactivity...', 'Session Expired');
    setTimeout(() => { document.getElementById('autoLogoutForm').submit(); }, 1000);
  }
}, 1000);

function resetTimer() { if(!autoLogoutTriggered){ timer = 15; timerDisplay.textContent = timer; timerAlert.style.background = '#fee2e2'; timerAlert.style.color = '#dc2626'; } }
document.onmousemove = resetTimer; document.onkeypress = resetTimer; document.onclick = resetTimer; document.onscroll = resetTimer;

$(document).ready(function() { 
  if (currentMemberId) {
    loadProgressImages();
  } else {
    $('#progressImagesContainer').html(`<div class="empty-progress"><i class="bi bi-camera"></i><p class="mb-0">No progress photos uploaded yet</p><small class="text-muted">Upload photos to track your transformation!</small></div>`);
  }
});
</script>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>