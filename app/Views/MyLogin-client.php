<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<title>Client Check-In | MJESHTER FITNESS GYM</title>

<!-- Bootstrap Icons & Google Fonts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Toastr CSS/JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    display: flex;
    background: #0f0f10;
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
      <input type="hidden" name="meaction" id="meaction_hidden" value="PROCESS-CHECKIN">
      <input type="text" id="rfid_input" autocomplete="off">
      <div class="scan-card-panel" id="scanPanel">
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

<audio id="beepSound">
  <source src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg" type="audio/ogg">
</audio>

<script>
toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: "toast-top-right",
  timeOut: 3000
};

$(document).ready(function() {
  toastr.info('System Ready', 'Initialized');
});

(function(){

  let processing = false;

  const form = document.getElementById('checkinForm');
  const hidden = document.getElementById('rfid_uid_hidden');

  const scanText = document.getElementById('scanText');
  const scanStatus = document.getElementById('scanStatus');
  const scanPanel = document.getElementById('scanPanel');

  function resetUI(){
    scanText.innerText = "Ready";
    scanStatus.innerText = "Waiting RFID...";
    scanPanel.style.borderColor = "#eef2f6";
    processing = false;
  }

  function showError(msg){
    toastr.clear();
    toastr.error(msg || "Access Denied");

    scanText.innerText = "Access Denied";
    scanStatus.innerText = msg || "Invalid card";
    scanPanel.style.borderColor = "#dc3545";

    setTimeout(resetUI, 2000);
  }

  function showSuccess(name){
    toastr.clear();
    toastr.success("Welcome " + name + "!");

    scanText.innerText = "Access Granted";
    scanStatus.innerText = "Welcome " + name + "!";
    scanPanel.style.borderColor = "#28a745";
  }

  // =========================
  // CHECK-IN FLOW (REDIRECT)
  // =========================
  function proceedCheckin(uid){
    hidden.value = uid;
    form.submit(); // your ORIGINAL system preserved
  }

  function validateRFID(uid){

    if(processing) return;
    processing = true;

    scanText.innerText = "Validating...";
    scanStatus.innerText = "Checking RFID...";

    fetch("<?=site_url();?>myclientdashboard", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: new URLSearchParams({
        rfid_uid: uid,
        meaction: "VALIDATE-RFID"
      })
    })
    .then(res => res.json())
    .then(res => {

      // =========================
      // 🔴 CHECKOUT (NO REDIRECT)
      // =========================
      if(res.status === "checkout"){

        toastr.info(res.message, "Checked Out");

        setTimeout(() => {
          resetUI(); // ONLY RESET UI
        }, 1500);

        return;
      }

      // =========================
      // 🟢 CHECK-IN (REDIRECT FLOW)
      // =========================
      if(res.status === "allowed"){

        showSuccess(res.member_name);

        setTimeout(() => {
          proceedCheckin(uid); // REDIRECT ONLY HERE
        }, 1200);

        return;
      }

      // =========================
      // ❌ ERROR
      // =========================
      showError(res.message);

    })
    .catch(() => {
      showError("Server error");
    })
    .finally(() => {
      setTimeout(() => processing = false, 500);
    });
  }

  // =========================
  // WEBSOCKET LISTENER
  // =========================
  const ws = new WebSocket("ws://localhost:6789");

  ws.onopen = function(){
    console.log("RFID Connected");
    toastr.success("RFID Live Connected");
  };

  ws.onmessage = function(event){

    const uid = event.data.trim();

    if(!uid || processing) return;

    console.log("RFID RECEIVED:", uid);

    setTimeout(() => {
      validateRFID(uid);
    }, 0);
  };

  ws.onerror = function(){
    toastr.error("RFID connection failed");
  };

})();
</script>
</body>
</html>