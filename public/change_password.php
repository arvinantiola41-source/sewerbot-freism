<?php
session_start();
if (!isset($_SESSION['uid'])) { header('Location: login.php'); exit; }
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['new_password'] ?? '';
    if (!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=])(?=\S+$).{8,}$/', $newPass)) {
        $error = 'Password must be at least 8 chars, 1 uppercase, 1 digit, 1 special char (@#$%^&+=)';
    } else {
        $success = 'Password updated successfully!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewerBot — Change Password</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Arial,sans-serif; background:#0d1b2a; color:#fff; min-height:100vh; display:flex; flex-direction:column; }
        .page-header { padding:20px 24px 0; display:flex; align-items:center; }
        .back-btn { background:none; border:none; color:#94a3b8; font-size:1.5rem; cursor:pointer; padding:8px; border-radius:50%; line-height:1; text-decoration:none; }
        .back-btn:hover { background:rgba(255,255,255,0.08); }
        .content { flex:1; display:flex; flex-direction:column; justify-content:center; padding:20px 32px 60px; max-width:420px; width:100%; margin:0 auto; }
        h1 { font-size:2rem; font-weight:800; text-align:center; margin-bottom:32px; }
        .input-group { position:relative; margin-bottom:16px; }
        .input-group input { width:100%; padding:18px 50px 18px 18px; background:#152030; border:1px solid #1e3a4a; border-radius:12px; color:#fff; font-size:1rem; outline:none; transition:border-color 0.2s; }
        .input-group input::placeholder { color:#4a6378; }
        .input-group input:focus { border-color:#3b82f6; }
        .eye-btn { position:absolute; right:16px; top:50%; transform:translateY(-50%); background:none; border:none; color:#4a6378; cursor:pointer; font-size:1.2rem; padding:4px; }
        .error-msg { background:rgba(239,68,68,0.15); border:1px solid #ef4444; color:#f87171; padding:12px 16px; border-radius:10px; font-size:0.9rem; margin-bottom:16px; }
        .success-msg { background:rgba(34,197,94,0.15); border:1px solid #22c55e; color:#4ade80; padding:12px 16px; border-radius:10px; font-size:0.9rem; margin-bottom:16px; text-align:center; }
        .btn-update { width:100%; padding:18px; background:#2563eb; color:#fff; border:none; border-radius:50px; font-size:1rem; font-weight:700; cursor:pointer; margin-top:10px; transition:background 0.2s; }
        .btn-update:hover { background:#1d4ed8; }
    </style>
</head>
<body>
    <div class="page-header">
        <a href="dashboard.php" class="back-btn">&#8630;</a>
    </div>
    <div class="content">
        <h1>Change Password</h1>
        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-msg"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <input type="password" name="new_password" id="p1" placeholder="Password" required>
                <button type="button" class="eye-btn" onclick="var i=document.getElementById('p1');i.type=i.type==='password'?'text':'password';">👁</button>
            </div>
            <button type="submit" class="btn-update">Update Password</button>
        </form>
    </div>
</body>
</html>
