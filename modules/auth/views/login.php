<?php
/**
 * FabX ERP - Login Page View
 */
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/fabx-theme.css') ?>" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0c1222 100%);
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 15% 85%, rgba(230, 126, 34, 0.06) 0%, transparent 40%),
                radial-gradient(circle at 85% 15%, rgba(52, 152, 219, 0.06) 0%, transparent 40%);
        }

        /* Geometric pattern overlay */
        body::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #e67e22, #f39c12);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 10px 30px rgba(230, 126, 34, 0.3);
        }

        .logo-icon i { font-size: 2rem; color: #fff; }

        .login-logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .login-logo p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0.25rem 0 0;
        }

        .form-floating > .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255,255,255,0.1);
            color: #e2e8f0;
        }

        .form-floating > .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.15);
            color: #e2e8f0;
        }

        .form-floating > label { color: #64748b; }
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label { color: #94a3b8; }

        .btn-login {
            background: linear-gradient(135deg, #e67e22, #d35400);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(230, 126, 34, 0.3);
            color: #fff;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            color: #475569;
            font-size: 0.75rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #475569;
            font-size: 0.75rem;
        }

        .login-footer a { color: #94a3b8; text-decoration: none; }
        .login-footer a:hover { color: #e67e22; }

        .version-info {
            text-align: center;
            margin-top: 2rem;
            color: #334155;
            font-size: 0.7rem;
        }

        .version-info span { display: inline-flex; align-items: center; gap: 0.35rem; }

        .iso-badge { color: #27ae60 !important; }

        .floating-shapes {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.03;
            background: #fff;
        }

        .shape-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .shape-2 { width: 200px; height: 200px; bottom: 10%; right: -50px; }
        .shape-3 { width: 150px; height: 150px; top: 40%; left: 10%; }

        .form-check-input:checked { background-color: #e67e22; border-color: #e67e22; }
        
        .forgot-link {
            font-size: 0.8rem;
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #e67e22; }

        .timeout-alert {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.2);
            color: #ef4444;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

<div class="floating-shapes">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
</div>

<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <div class="logo-icon">
                <i class="bi bi-hexagon-fill"></i>
            </div>
            <h1>FabX Engineering</h1>
            <p>Precision Mechanical Fabrication Solutions</p>
        </div>

        <?php if ($timeout ?? false): ?>
            <div class="timeout-alert">
                <i class="bi bi-clock-history"></i>
                Your session has expired. Please login again.
            </div>
        <?php endif; ?>

        <?php if ($error ?? false): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="font-size: 0.85rem;">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?= e($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php foreach (get_flash() as $flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" style="font-size: 0.85rem;">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'x-circle' : ($flash['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle')) ?> me-2"></i>
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="<?= base_url('auth/authenticate') ?>" id="loginForm">
            <?= csrf_field() ?>
            
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required autofocus>
                <label for="email"><i class="bi bi-envelope me-1"></i>Email address</label>
            </div>
            
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size: 0.8rem; color: #94a3b8;">
                        Remember me
                    </label>
                </div>
                <a href="<?= base_url('auth/forgot-password') ?>" class="forgot-link">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn btn-login w-100" id="loginBtn">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <div class="divider">ISO 9001:2015 Certified</div>

        <div class="text-center" style="font-size: 0.75rem; color: #475569;">
            <p class="mb-1">This system is restricted to authorized users.</p>
            <p class="mb-0">All activities are monitored and logged.</p>
        </div>
    </div>

    <div class="version-info">
        <span><i class="bi bi-shield-check iso-badge"></i> ISO 9001:2015</span>
        <span style="margin: 0 0.5rem;">|</span>
        <span><?= APP_NAME ?> v<?= APP_VERSION ?></span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
});
</script>
</body>
</html>
