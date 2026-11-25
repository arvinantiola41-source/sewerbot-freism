<?php
session_start();
if (!isset($_SESSION['uid'])) { header('Location: login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cmd = $_POST['action'] ?? '';
    if (in_array($cmd, ['FORWARD','BACKWARD','LEFT','RIGHT','STOP'])) {
        $_SESSION['last_move'] = $cmd;
        header('Location: controller.php'); exit;
    }
}

$lastMove = $_SESSION['last_move'] ?? 'STOP';
$cmdLabels = ['FORWARD'=>'FORWARD (▲)','BACKWARD'=>'BACKWARD (▼)','LEFT'=>'TURNING LEFT (◄)','RIGHT'=>'TURNING RIGHT (►)','STOP'=>'STOPPED (⏹)'];
$displayMove = $cmdLabels[$lastMove] ?? $lastMove;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewerBot — Command Console</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Arial,sans-serif; background:#0d1b2a; color:#fff; min-height:100vh; }
        .page-header { display:flex; align-items:center; gap:14px; padding:20px 20px 0; }
        .back-btn { background:#152030; border:none; color:#94a3b8; font-size:1.3rem; padding:10px 12px; border-radius:12px; cursor:pointer; text-decoration:none; line-height:1; }
        .back-btn:hover { background:#1e3a4a; }
        .header-title { font-size:1.1rem; font-weight:800; letter-spacing:1px; }
        .header-sub { font-size:0.75rem; color:#64748b; margin-top:2px; }
        .main-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; padding:20px; }
        @media (max-width:600px) { .main-grid { grid-template-columns:1fr; } }
        .panel { background:#152030; border-radius:20px; padding:20px; }
        .info-row { margin-bottom:16px; }
        .info-label { font-size:0.75rem; color:#64748b; margin-bottom:4px; }
        .info-val { font-size:0.9rem; font-weight:600; }
        .badge { display:inline-block; padding:3px 10px; border-radius:6px; font-size:0.7rem; font-weight:700; letter-spacing:1px; }
        .badge-offline { background:#7f1d1d; color:#fca5a5; }
        .move-display { padding:6px 12px; background:#1e3a4a; border-radius:8px; font-size:0.85rem; font-weight:600; color:#38bdf8; display:inline-block; }
        .ir-danger { color:#f87171; }
        .ir-clear { color:#4ade80; }
        .water-dry { color:#94a3b8; }
        .btn-radar { width:100%; padding:12px; background:linear-gradient(135deg,#0ea5e9,#0284c7); color:#fff; border:none; border-radius:50px; font-size:0.85rem; font-weight:700; cursor:pointer; margin-top:4px; }
        .dpad-label { text-align:center; font-size:0.75rem; font-weight:700; color:#38bdf8; letter-spacing:1.5px; margin-bottom:20px; }
        .dpad { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
        .dpad-btn { padding:16px 8px; border:none; border-radius:14px; background:#2563eb; color:#fff; font-size:0.85rem; font-weight:700; cursor:pointer; text-align:center; transition:background 0.15s,transform 0.1s; }
        .dpad-btn:hover { background:#1d4ed8; }
        .dpad-btn:active { transform:scale(0.95); }
        .dpad-btn.stop { background:#dc2626; }
        .dpad-btn.stop:hover { background:#b91c1c; }
        .dpad-empty { visibility:hidden; }
        /* Radar modal */
        .radar-modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.75); z-index:1000; align-items:center; justify-content:center; }
        .radar-modal.open { display:flex; }
        .radar-box { background:#101828; border-radius:16px; padding:24px 28px; max-width:400px; width:90%; }
        .radar-title { font-size:1rem; font-weight:700; letter-spacing:1px; margin-bottom:16px; border-bottom:1px solid #1e3a4a; padding-bottom:12px; }
        .radar-body { font-size:0.85rem; line-height:1.8; white-space:pre-line; color:#e2e8f0; }
        .radar-close { width:100%; margin-top:20px; padding:14px; background:none; color:#3b82f6; border:none; font-size:0.9rem; font-weight:700; cursor:pointer; letter-spacing:1px; }
    </style>
    <script>
        function toggleRadar() {
            var m = document.getElementById('radarModal');
            m.className = m.className === 'radar-modal' ? 'radar-modal open' : 'radar-modal';
        }
    </script>
</head>
<body>
    <div class="page-header">
        <a href="dashboard.php" class="back-btn">&#8630;</a>
        <div>
            <div class="header-title">COMMAND CONSOLE</div>
            <div class="header-sub">Landscape Steering Mode</div>
        </div>
    </div>

    <div class="main-grid">
        <!-- Left: Sensor info -->
        <div class="panel">
            <div class="info-row" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="info-label" style="margin:0;">ESP32 Signal</span>
                <span class="badge badge-offline">OFFLINE</span>
            </div>
            <div class="info-row">
                <div class="info-label">Movement</div>
                <span class="move-display"><?= htmlspecialchars($displayMove) ?></span>
            </div>
            <div class="info-row">
                <div class="info-label">Sharp IR Distance</div>
                <div class="info-val ir-danger">🚨 DANGER: COLLISION IMMINENT! (8 cm)</div>
            </div>
            <button class="btn-radar" onclick="toggleRadar()">📡 IR RADAR SCANNER</button>
            <div class="info-row" style="margin-top:16px;">
                <div class="info-label">Water Ingress Sensor</div>
                <div class="info-val water-dry">0 ADC (DRY)</div>
            </div>
        </div>

        <!-- Right: D-pad -->
        <div class="panel">
            <div class="dpad-label">PRO DUAL-AXIS STEERING CONSOLE</div>
            <form method="POST">
                <div class="dpad">
                    <div class="dpad-empty"></div>
                    <button class="dpad-btn" name="action" value="FORWARD">FORWARD ▲</button>
                    <div class="dpad-empty"></div>
                    <button class="dpad-btn" name="action" value="LEFT">◄ TURN LEFT</button>
                    <button class="dpad-btn stop" name="action" value="STOP">STOP ■</button>
                    <button class="dpad-btn" name="action" value="RIGHT">TURN RIGHT ►</button>
                    <div class="dpad-empty"></div>
                    <button class="dpad-btn" name="action" value="BACKWARD">BACKWARD ▼</button>
                    <div class="dpad-empty"></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Radar Modal -->
    <div id="radarModal" class="radar-modal">
        <div class="radar-box">
            <div class="radar-title">📡 SHARP IR RADAR &amp; OBSTACLE SCANNER</div>
            <pre class="radar-body">📡 SHARP IR RADAR SCANNER
───────────────────────────────────
🔍 DETECTED OBJECT:
🧱 SOLID PIPE WALL / CLOSE OBSTACLE

📏 DIGITAL DISTANCE:
8 cm

📊 PROXIMITY METER:
[ █ █ █ █ █ █ █ █ █ █ ] 100%

⚠️ RADAR SAFETY STATUS:
🚨 DANGER: COLLISION IMMINENT!

⚡ RAW SENSOR SIGNAL:
4095 ADC (Sharp IR Sensor D34)
───────────────────────────────────</pre>
            <button class="radar-close" onclick="toggleRadar()">TAP AGAIN TO EXIT</button>
        </div>
    </div>
</body>
</html>
