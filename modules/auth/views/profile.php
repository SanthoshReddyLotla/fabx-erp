<?php /** FabX ERP - User Profile View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-person-circle"></i> My Profile</h1>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="fx-card text-center p-4">
            <div class="mb-3">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= base_url('assets/uploads/' . $user['avatar']) ?>" class="rounded-circle" width="100" height="100" alt="Avatar">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width:100px;height:100px;font-size:2.5rem;">
                        <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <h4 class="mb-1"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></h4>
            <p class="text-muted mb-1"><?= e($user['role_name'] ?? '-') ?></p>
            <p class="text-muted small"><?= e($user['department_name'] ?? '-') ?></p>
            <p class="text-muted small"><?= e($user['employee_code'] ?? '') ?></p>
        </div>
    </div>
    <div class="col-md-8">
        <div class="fx-card">
            <div class="fx-card-header"><strong>Edit Profile</strong></div>
            <div class="fx-card-body">
                <form method="POST" action="<?= base_url('auth/update-profile') ?>" enctype="multipart/form-data" id="profileForm">
                    <input type="hidden" name="fabx_csrf_token" value="<?= $csrf_token ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" value="<?= e($user['first_name'] ?? '') ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" value="<?= e($user['last_name'] ?? '') ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" value="<?= e($user['email'] ?? '') ?>" class="form-control" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Profile Photo</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12"><hr></div>
                        <div class="col-12"><h6 class="text-muted">Change Password (optional)</h6></div>
                        <div class="col-md-4">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Leave blank to keep">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" minlength="8" placeholder="Min 8 characters">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-fx btn-fx-primary">
                                <i class="bi bi-save"></i> Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
