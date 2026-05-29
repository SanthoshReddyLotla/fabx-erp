<?php /** FabX ERP - Reset Password Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FabX ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/fabx-theme.css') ?>" rel="stylesheet">
</head>
<body class="login-page">
<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card shadow-lg" style="width:400px;max-width:100%;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">Reset Password</h2>
                <p class="text-muted">Enter your new password below.</p>
            </div>
            <?php if ($flash = get_flash()): ?>
                <div class="alert alert-<?= $flash['type'] ?> mb-3"><?= e($flash['message']) ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= base_url('auth/update-password') ?>">
                <input type="hidden" name="fabx_csrf_token" value="<?= generate_csrf() ?>">
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8" placeholder="Minimum 8 characters">
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Re-enter new password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                <div class="text-center mt-3">
                    <a href="<?= base_url('auth/login') ?>" class="text-muted small">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
