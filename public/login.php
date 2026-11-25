<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (empty($email) || empty($pass)) {
        $error = 'Email and password cannot be empty';
    } elseif (!str_ends_with(strtolower($email), '@gmail.com') || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email must be a valid @gmail.com address';
    } else {
        $_SESSION['uid']   = md5($email);
        $_SESSION['email'] = $email;
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewerBot — Login</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Arial,sans-serif; background:#0d1b2a; color:#fff; min-height:100vh; display:flex; flex-direction:column; }
        .page-header { padding:20px 24px 0; display:flex; align-items:center; }
        .back-btn { background:none; border:none; color:#94a3b8; font-size:1.5rem; cursor:pointer; padding:8px; border-radius:50%; line-height:1; text-decoration:none; }
        .back-btn:hover { background:rgba(255,255,255,0.08); }
        .content { flex:1; display:flex; flex-direction:column; justify-content:center; padding:40px 32px 60px; max-width:420px; width:100%; margin:0 auto; }
        h1 { font-size:2rem; font-weight:800; text-align:center; margin-bottom:6px; }
        .subtitle { text-align:center; color:#64748b; font-size:0.95rem; margin-bottom:40px; }
        .input-group { position:relative; margin-bottom:16px; }
        .input-group input { width:100%; padding:18px 50px 18px 18px; background:#152030; border:1px solid #1e3a4a; border-radius:12px; color:#fff; font-size:1rem; outline:none; transition:border-color 0.2s; }
        .input-group input::placeholder { color:#4a6378; }
        .input-group input:focus { border-color:#3b82f6; }
        .eye-btn { position:absolute; right:16px; top:50%; transform:translateY(-50%); background:none; border:none; color:#4a6378; cursor:pointer; font-size:1.2rem; padding:4px; }
        .error-msg { background:rgba(239,68,68,0.15); border:1px solid #ef4444; color:#f87171; padding:12px 16px; border-radius:10px; font-size:0.9rem; margin-bottom:16px; }
        .btn-login { width:100%; padding:18px; background:#2563eb; color:#fff; border:none; border-radius:50px; font-size:1rem; font-weight:700; cursor:pointer; margin-top:10px; transition:background 0.2s; }
        .btn-login:hover { background:#1d4ed8; }
        .links { text-align:center; margin-top:24px; display:flex; flex-direction:column; gap:12px; }
        .links a { color:#3b82f6; text-decoration:none; font-size:0.95rem; }
        .links a:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="page-header">
        <a href="index.php" class="back-btn">&#8630;</a>
    </div>
    <div class="content">
        <h1>SewerBot</h1>
        <p class="subtitle">Sign in to manage your IoT system</p>
        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="input-group">
                <input type="password" name="password" id="p1" placeholder="Password" required>
                <button type="button" class="eye-btn" onclick="var i=document.getElementById('p1');i.type=i.type==='password'?'text':'password';">👁</button>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
        <div class="links">
            <a href="forgot.php">Forgot Password?</a>
            <a href="register.php">Don't have an account? Register</a>
        </div>
    </div>
</body>
</html>
