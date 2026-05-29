<?php /** FabX ERP - Admin Users List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-people-fill"></i> User Management</h1>
</div>

<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="active" <?= (input('status', 'active') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (input('status', 'active') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
        </select>
    </form>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="usersTable">
                <thead>
                    <tr>
                        <th>Employee Code</th><th>Name</th><th>Email</th>
                        <th>Role</th><th>Department</th><th>Status</th><th>Last Login</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><strong><?= e($u['employee_code']) ?></strong></td>
                                <td><?= e($u['first_name'] . ' ' . $u['last_name']) ?></td>
                                <td><?= e($u['email']) ?></td>
                                <td><?= e($u['role_name'] ?? '-') ?></td>
                                <td><?= e($u['department_name'] ?? '-') ?></td>
                                <td>
                                    <span class="badge-fx <?= $u['status'] === 'active' ? 'badge-fx-success' : 'badge-fx-secondary' ?>">
                                        <?= ucfirst($u['status']) ?>
                                    </span>
                                </td>
                                <td><?= format_date($u['last_login'] ?? null) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people-fill"></i><h5>No users found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
