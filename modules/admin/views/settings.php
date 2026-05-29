<?php /** FabX ERP - Admin Settings View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-gear"></i> System Settings</h1>
</div>

<form method="POST">
    <input type="hidden" name="fabx_csrf_token" value="<?= $csrf_token ?>">
    
    <?php if (!empty($settings)): ?>
        <?php foreach ($settings as $group => $items): ?>
            <div class="fx-card mb-4">
                <div class="fx-card-header">
                    <strong><?= ucwords(str_replace('_', ' ', $group)) ?></strong>
                </div>
                <div class="fx-card-body">
                    <?php foreach ($items as $setting): ?>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-semibold">
                                <?= ucwords(str_replace('_', ' ', $setting['setting_key'])) ?>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="settings[<?= e($setting['setting_key']) ?>]"
                                    value="<?= e($setting['setting_value'] ?? '') ?>"
                                    class="form-control">
                                <?php if (!empty($setting['description'])): ?>
                                    <small class="text-muted"><?= e($setting['description']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save Settings</button>
        </div>
    <?php else: ?>
        <div class="fx-card">
            <div class="fx-card-body p-5 text-center">
                <i class="bi bi-gear display-1 text-muted"></i>
                <h5 class="mt-3">No settings configured</h5>
                <p class="text-muted">System settings will appear here once populated.</p>
            </div>
        </div>
    <?php endif; ?>
</form>
