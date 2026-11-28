<?php
/**
 * Landing / Index page — matches the original app's blue splash screen.
 * Shows the SewerBot logo, system features, and Get Started / Create Account buttons.
 * Also displays FREISM course info and group members as required by the final project.
 */
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewerBot — IoT Pipeline Inspection</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #1565C0;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ===== FREISM Banner (required by project) ===== */
        .freism-banner {
            width: 100%;
            background: rgba(0,0,0,0.25);
            text-align: center;
            padding: 14px 20px;
        }
        .freism-banner .course-code {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #fff;
        }
        .freism-banner .freism-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.8);
            margin-top: 2px;
        }

        /* ===== Main content ===== */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 24px 20px;
            width: 100%;
            max-width: 420px;
        }

        /* Logo */
        .logo-box {
            background: #0d1b2a;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 32px;
            text-align: center;
            width: 220px;
        }
        .logo-icon {
            font-size: 4rem;
            line-height: 1;
            margin-bottom: 8px;
        }
        .logo-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #fff;
        }
        .logo-title span {
            color: #FFA726;
        }
        .logo-subtitle {
            font-size: 0.65rem;
            color: #64b5f6;
            letter-spacing: 1.5px;
            margin-top: 4px;
        }

        /* Features card */
        .features-card {
            background: rgba(0,0,0,0.3);
            border-radius: 16px;
            padding: 20px 24px;
            width: 100%;
            margin-bottom: 40px;
        }
        .features-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: rgba(255,255,255,0.6);
            letter-spacing: 1.5px;
            margin-bottom: 14px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            font-size: 0.95rem;
            color: #fff;
        }
        .feature-item + .feature-item {
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .feature-icon { font-size: 1.2rem; width: 24px; text-align: center; }

        /* Buttons */
        .btn-group {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            letter-spacing: 0.5px;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.9; }
        .btn-dark { background: #0d1b2a; color: #fff; }
        .btn-outline { background: transparent; color: #fff; border: 2px solid #fff; }

        /* ===== Members section ===== */
        .members-section {
            width: 100%;
            max-width: 420px;
            padding: 0 24px 40px;
        }
        .members-heading {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.7);
            margin-bottom: 16px;
        }
        .members-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .member-card {
            background: rgba(0,0,0,0.3);
            border-radius: 14px;
            padding: 16px 12px;
            text-align: center;
        }
        .member-photo {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            margin: 0 auto 10px;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
        }
        .member-photo img { width: 100%; height: 100%; object-fit: cover; }
        .member-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.3;
        }
    </style>
</head>
<body>

    <!-- FREISM required banner -->
    <div class="freism-banner">
        <div class="course-code">82235 — FREE ELECTIVE 4 | FREISM — 4B</div>
        <div class="freism-label">GROUP MEMBERS SHOWN BELOW</div>
    </div>

    <!-- Main app content -->
    <div class="main">

        <!-- Logo -->
        <div class="logo-box">
            <div class="logo-icon">🤖</div>
            <div class="logo-title">Sewer<span>Bot</span></div>
            <div class="logo-subtitle">IOT PIPELINE INSPECTION SYSTEM</div>
        </div>

        <!-- Features -->
        <div class="features-card">
            <div class="features-label">SYSTEM FEATURES</div>
            <div class="feature-item">
                <span class="feature-icon">📷</span>
                Live M5 Camera Feed Inspection
            </div>
            <div class="feature-item">
                <span class="feature-icon">📡</span>
                Sharp IR Distance Obstacle Sensor
            </div>
            <div class="feature-item">
                <span class="feature-icon">💧</span>
                Water Ingress Emergency Sensor
            </div>
        </div>

        <!-- Buttons -->
        <div class="btn-group">
            <a href="login.php" class="btn btn-dark">GET STARTED / SIGN IN</a>
            <a href="register.php" class="btn btn-outline">CREATE AN ACCOUNT</a>
        </div>
    </div>

    <!-- Group Members -->
    <div class="members-section">
        <div class="members-heading">GROUP MEMBERS</div>
        <div class="members-grid" style="grid-template-columns:1fr; max-width:220px; margin:0 auto;">

            <!-- Member 1 -->
            <div class="member-card">
                <div class="member-photo">
                    <img src="members/member1.jpg" alt="Arvin M. Antiola">
                </div>
                <div class="member-name">Arvin M. Antiola</div>
            </div>

        </div>
    </div>

</body>
</html>
