  <!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<title>Client Check-In | MJESHTER FITNESS GYM</title>

<!-- Bootstrap Icons & Google Fonts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Bootstrap Icons & Google Fonts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- ✅ Bootstrap CSS - REQUIRED FOR TOASTS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    display: flex;
    background: #0f0f10;
  }

    /* Toast positioning */
  .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
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
</style>
</head>

<body>

<div class="left">
  <div class="left-content">
    <div class="brand">
      <h1>MJESHTER <span>FITNESS GYM</span></h1>
    </div>
    <div class="desc">
      RFID Check-in system for members.
    </div>
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

    <div class="header">
      <h3>Scan Card</h3>
    </div>

    <form action="<?=site_url();?>myclientdashboard" method="post" id="checkinForm">

      <input type="hidden" name="rfid_uid" id="rfid_uid_hidden">

      <input type="text" id="rfid_input" autocomplete="off">

      <div class="scan-card-panel idle-heartbeat" id="scanPanel">
        <div class="scan-icon"><i class="bi bi-credit-card-2-front"></i></div>
        <div class="scan-text" id="scanText">Ready</div>
        <div class="scan-status" id="scanStatus">Waiting RFID...</div>
      </div>

    </form>

    <div class="footer">
      MJESHTER FITNESS GYM © 2026
    </div>

  </div>
</div>

<!-- AUDIO BEEP -->
<audio id="beepSound">
  <source src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg" type="audio/ogg">
</audio>

<script>
(function(){

  const form = document.getElementById('checkinForm');
  const input = document.getElementById('rfid_input');
  const hidden = document.getElementById('rfid_uid_hidden');
  const status = document.getElementById('scanStatus');
  const text = document.getElementById('scanText');
  const beep = document.getElementById('beepSound');

  let processing = false;
  let holdTimer = null;

  function playBeep(){
    try {
      beep.currentTime = 0;
      beep.play();
    } catch(e){}
  }

  function lockSubmit(){
    form.onsubmit = function(e){
      e.preventDefault();
      return false;
    };
  }

  function unlockSubmit(){
    form.onsubmit = null;
  }

  function process(uid){
    if(processing) return;
    processing = true;

    lockSubmit(); // 🚨 block instant submit

    playBeep();

    // SHOW UID IMMEDIATELY
    text.innerText = "Card Detected";
    status.innerText = "UID: " + uid;

    hidden.value = uid;

    // FORCE HOLD TIME (5 seconds visible)
    holdTimer = setTimeout(() => {

      status.innerText = "Preparing redirect...";

      // extra small delay for UX
      setTimeout(() => {
        unlockSubmit();
        form.submit();
      }, 1000);

    }, 2000); // ⬅️ adjust display time here
  }

  // RFID scanner input
  input.addEventListener('keypress', function(e){
    if(e.key === 'Enter'){
      e.preventDefault();

      const uid = this.value.trim();
      this.value = '';

      if(uid !== ''){
        process(uid);
      }
    }
  });

  // keep focus for RFID reader
  setInterval(() => {
    if(document.activeElement !== input && !processing){
      input.focus();
    }
  }, 500);

})();
</script>
<!-- ✅ Bootstrap JS Bundle - REQUIRED FOR TOASTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>