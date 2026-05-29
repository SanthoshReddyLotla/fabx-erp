<?php /** FabX ERP - Admin Roles List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-shield-check"></i> Roles & Permissions</h1>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Role</th><th>Description</th>
                        <?php foreach ($permissions as $perm): ?>
                            <th class="text-center" style="font-size:0.75rem;"><?= ucfirst($perm) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><strong><?= e($role['name']) ?></strong></td>
                                <td><?= e($role['description'] ?? '-') ?></td>
                                <?php foreach ($permissions as $perm): ?>
                                    <td class="text-center">
                                        <?php $perms = json_decode($role['permissions'] ?? '[]', true); ?>
                                        <?php if (in_array($perm, $perms ?? [])): ?>
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        <?php else: ?>
                                            <i class="bi bi-dash-circle text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="<?= count($permissions) + 2 ?>"><div class="empty-state"><i class="bi bi-shield-check"></i><h5>No roles found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
