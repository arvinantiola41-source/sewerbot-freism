<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error = 'Email cannot be empty';
    } else {
        $success = 'Reset instructions sent to your email!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewerBot — Forgot Password</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Arial,sans-serif; background:#0d1b2a; color:#fff; min-height:100vh; display:flex; flex-direction:column; }
        .page-header { padding:20px 24px 0; display:flex; align-items:center; }
        .back-btn { background:none; border:none; color:#94a3b8; font-size:1.5rem; cursor:pointer; padding:8px; border-radius:50%; line-height:1; text-decoration:none; }
        .back-btn:hover { background:rgba(255,255,255,0.08); }
        .content { flex:1; display:flex; flex-direction:column; justify-content:center; padding:20px 32px 60px; max-width:420px; width:100%; margin:0 auto; }
        h1 { font-size:2rem; font-weight:800; text-align:center; margin-bottom:32px; }
        .input-group { margin-bottom:16px; }
        .input-group input { width:100%; padding:18px; background:#152030; border:1px solid #1e3a4a; border-radius:12px; color:#fff; font-size:1rem; outline:none; transition:border-color 0.2s; }
        .input-group input::placeholder { color:#4a6378; }
        .input-group input:focus { border-color:#3b82f6; }
        .error-msg { background:rgba(239,68,68,0.15); border:1px solid #ef4444; color:#f87171; padding:12px 16px; border-radius:10px; font-size:0.9rem; margin-bottom:16px; }
        .success-msg { background:rgba(34,197,94,0.15); border:1px solid #22c55e; color:#4ade80; padding:12px 16px; border-radius:10px; font-size:0.9rem; margin-bottom:16px; text-align:center; }
        .btn-reset { width:100%; padding:18px; background:#2563eb; color:#fff; border:none; border-radius:50px; font-size:1rem; font-weight:700; cursor:pointer; margin-top:10px; transition:background 0.2s; }
        .btn-reset:hover { background:#1d4ed8; }
    </style>
</head>
<body>
    <div class="page-header">
        <a href="login.php" class="back-btn">&#8630;</a>
    </div>
    <div class="content">
        <h1>Forgot Password</h1>
        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-msg"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <button type="submit" class="btn-reset">Reset Password</button>
        </form>
    </div>
</body>
</html>
