<?php
session_start();
if (!isset($_SESSION['uid'])) { header('Location: login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'logout') { session_destroy(); header('Location: login.php'); exit; }
    if ($action === 'led_on' || $action === 'led_off') {
        $_SESSION['led_command'] = strtoupper(str_replace('led_', '', $action));
        header('Location: dashboard.php'); exit;
    }
}

$ledCommand = $_SESSION['led_command'] ?? 'OFF';

// Sample reports for display
$reports = [
    ['location' => 'Sharp IR Distance Sensor (D34)', 'status' => '⚠️ Obstacle Detected! Distance: 9 cm (1363 ADC)', 'lastChecked' => '2026-11-24 16:56:19'],
    ['location' => 'Sharp IR Distance Sensor (D34)', 'status' => '⚠️ Obstacle Detected! Distance: 9 cm (1367 ADC)', 'lastChecked' => '2026-11-24 16:54:39'],
    ['location' => 'Sharp IR Distance Sensor (D34)', 'status' => '⚠️ Obstacle Detected! Distance: 8 cm (4095 ADC)', 'lastChecked' => '2026-11-24 16:53:15'],
    ['location' => 'Sharp IR Distance Sensor (D34)', 'status' => '⚠️ Obstacle Detected! Distance: 9 cm (1350 ADC)', 'lastChecked' => '2026-11-24 16:53:05'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewerBot — Dashboard</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Arial,sans-serif; background:#0d1b2a; color:#fff; min-height:100vh; padding-bottom:30px; }
        .app-header { text-align:center; padding:32px 24px 24px; }
        .logo-box { display:inline-flex; align-items:center; gap:10px; background:#152030; border-radius:16px; padding:10px 20px; margin-bottom:10px; }
        .logo-icon { font-size:2rem; }
        .logo-title { font-size:1.3rem; font-weight:800; }
        .logo-title span { color:#FFA726; }
        .logo-sub { font-size:0.6rem; color:#64748b; letter-spacing:1.5px; }
        .app-title { font-size:1.6rem; font-weight:800; }
        .app-subtitle { font-size:0.85rem; color:#64748b; margin-top:4px; }
        .card { margin:0 16px 16px; background:#152030; border-radius:20px; padding:20px; }
        .card-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; }
        .card-title { font-size:1rem; font-weight:700; }
        .card-subtitle { font-size:0.75rem; color:#64748b; margin-top:2px; }
        .badge { font-size:0.7rem; font-weight:700; padding:4px 10px; border-radius:6px; letter-spacing:1px; }
        .badge-offline { background:#7f1d1d; color:#fca5a5; }
        .badge-online { background:#14532d; color:#86efac; }
        .status-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #1e3a4a; font-size:0.9rem; }
        .status-row:last-of-type { border-bottom:none; }
        .status-label { color:#64748b; }
        .val-on { color:#4ade80; font-weight:600; }
        .val-off { color:#f87171; font-weight:600; }
        .btn-row { display:flex; gap:12px; margin-top:16px; }
        .btn-led-on { flex:1; padding:14px; border:none; border-radius:50px; background:#16a34a; color:#fff; font-size:0.95rem; font-weight:700; cursor:pointer; }
        .btn-led-on:hover { background:#15803d; }
        .btn-led-off { flex:1; padding:14px; border:none; border-radius:50px; background:#dc2626; color:#fff; font-size:0.95rem; font-weight:700; cursor:pointer; }
        .btn-led-off:hover { background:#b91c1c; }
        .btn-controller { display:block; width:100%; padding:16px; margin-top:12px; background:#0ea5e9; color:#fff; border:none; border-radius:50px; font-size:0.95rem; font-weight:700; cursor:pointer; text-align:center; text-decoration:none; }
        .btn-controller:hover { background:#0284c7; }
        .section-title { font-size:1rem; font-weight:700; margin-bottom:4px; }
        .section-sub { font-size:0.75rem; color:#64748b; margin-bottom:16px; }
        .report-item { background:#1e3a4a; border-radius:14px; padding:14px 16px; margin-bottom:10px; }
        .report-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; }
        .report-location { font-size:0.9rem; font-weight:600; }
        .report-time { font-size:0.75rem; color:#64748b; }
        .status-warning { color:#fbbf24; font-size:0.85rem; }
        .status-critical { color:#f87171; font-size:0.85rem; }
        .status-normal { color:#60a5fa; font-size:0.85rem; }
        .bottom-actions { margin:0 16px; display:flex; justify-content:space-around; border-top:1px solid #1e3a4a; padding-top:20px; }
        .link-change { color:#3b82f6; text-decoration:none; font-size:0.95rem; font-weight:600; padding:8px 16px; }
        .link-logout { background:none; border:none; color:#ef4444; font-size:0.95rem; font-weight:600; cursor:pointer; padding:8px 16px; }
        .link-change:hover, .link-logout:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="app-header">
        <div class="logo-box">
            <div class="logo-icon">🤖</div>
            <div>
                <div class="logo-title">Sewer<span>Bot</span></div>
                <div class="logo-sub">IOT PIPELINE INSPECTION SYSTEM</div>
            </div>
        </div>
        <div class="app-title">SewerBot</div>
        <div class="app-subtitle">Smart Sewer Monitoring &amp; Control</div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">ESP32 CONTROL</div>
                <div class="card-subtitle">LED Illumination &amp; Movement</div>
            </div>
            <span class="badge badge-offline">OFFLINE</span>
        </div>
        <div class="status-row">
            <span class="status-label">Requested Command</span>
            <span class="<?= $ledCommand === 'ON' ? 'val-on' : 'val-off' ?>"><?= $ledCommand ?></span>
        </div>
        <div class="status-row">
            <span class="status-label">Actual LED Status</span>
            <span class="<?= $ledCommand === 'ON' ? 'val-on' : 'val-off' ?>"><?= $ledCommand ?></span>
        </div>
        <form method="POST" style="display:contents;">
            <div class="btn-row">
                <button class="btn-led-on"  name="action" value="led_on">LED ON</button>
                <button class="btn-led-off" name="action" value="led_off">LED OFF</button>
            </div>
        </form>
        <a href="controller.php" class="btn-controller">🕹 OPEN SEWERBOT CONTROLLER</a>
    </div>

    <div class="card">
        <div class="section-title">SEWER REPORTS</div>
        <div class="section-sub">Recent monitoring activity</div>
        <?php foreach ($reports as $r): ?>
            <?php
                $cls = 'status-normal';
                if (str_contains($r['status'], '🚨')) $cls = 'status-critical';
                elseif (str_contains($r['status'], '⚠️')) $cls = 'status-warning';
            ?>
            <div class="report-item">
                <div class="report-top">
                    <span class="report-location"><?= htmlspecialchars($r['location']) ?></span>
                    <span class="report-time"><?= htmlspecialchars($r['lastChecked']) ?></span>
                </div>
                <div class="<?= $cls ?>"><?= htmlspecialchars($r['status']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bottom-actions">
        <a href="change_password.php" class="link-change">Change Password</a>
        <form method="POST" style="display:inline;">
            <button class="link-logout" name="action" value="logout">Logout</button>
        </form>
    </div>
</body>
</html>
