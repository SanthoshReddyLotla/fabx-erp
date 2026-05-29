<?php
/**
 * FabX ERP - Forgot Password View
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0c1222 100%); position: relative; overflow: hidden; }
        body::before { content: ''; position: absolute; inset: 0;
            background: radial-gradient(circle at 15% 85%, rgba(230,126,34,0.06) 0%, transparent 40%),
                radial-gradient(circle at 85% 15%, rgba(52,152,219,0.06) 0%, transparent 40%); }
        .login-card { background: rgba(30,41,59,0.85); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px; padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4); max-width: 420px; width: 100%; }
        .logo-icon { width: 64px; height: 64px; background: linear-gradient(135deg, #e67e22, #f39c12);
            border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;
            box-shadow: 0 10px 30px rgba(230,126,34,0.3); }
        .logo-icon i { font-size: 2rem; color: #fff; }
        .form-floating > .form-control { background: rgba(15,23,42,0.6); border: 1px solid rgba(255,255,255,0.1); color: #e2e8f0; }
        .form-floating > .form-control:focus { background: rgba(15,23,42,0.8); border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230,126,34,0.15); color: #e2e8f0; }
        .btn-login { background: linear-gradient(135deg, #e67e22, #d35400); border: none; color: #fff; font-weight: 600;
            padding: 0.75rem; border-radius: 10px; transition: all 0.2s ease; }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 10px 25px rgba(230,126,34,0.3); color: #fff; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center mb-4">
        <div class="logo-icon"><i class="bi bi-hexagon-fill"></i></div>
        <h1 class="text-white h4">Reset Password</h1>
        <p class="text-muted small">Enter your email to receive reset instructions</p>
    </div>

    <?php foreach (get_flash() as $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" style="font-size: 0.85rem;">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <form method="POST" action="<?= base_url('auth/send-reset') ?>">
        <?= csrf_field() ?>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required autofocus>
            <label for="email"><i class="bi bi-envelope me-1"></i>Email address</label>
        </div>
        <button type="submit" class="btn btn-login w-100 mb-3">
            <i class="bi bi-send me-2"></i>Send Reset Link
        </button>
        <div class="text-center">
            <a href="<?= base_url('auth/login') ?>" class="text-decoration-none" style="color: #94a3b8; font-size: 0.85rem;">
                <i class="bi bi-arrow-left me-1"></i>Back to Login
            </a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
