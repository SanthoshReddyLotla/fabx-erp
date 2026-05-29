<?php /** FabX ERP - HR Employees List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-people"></i> Employees</h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-primary"><?= $stats['total'] ?? 0 ?></div>
            <div class="text-muted small">Active Employees</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-success"><?= $stats['new_this_month'] ?? 0 ?></div>
            <div class="text-muted small">Joined This Month</div>
        </div>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="employeesTable">
                <thead>
                    <tr>
                        <th>Employee Code</th><th>Name</th><th>Email</th>
                        <th>Role</th><th>Department</th><th>Designation</th>
                        <th>Status</th><th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($employees)): ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td><strong><?= e($emp['employee_code']) ?></strong></td>
                                <td><?= e($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
                                <td><?= e($emp['email']) ?></td>
                                <td><?= e($emp['role_name'] ?? '-') ?></td>
                                <td><?= e($emp['department_name'] ?? '-') ?></td>
                                <td><?= e($emp['designation'] ?? '-') ?></td>
                                <td>
                                    <span class="badge-fx <?= $emp['status'] === 'active' ? 'badge-fx-success' : 'badge-fx-secondary' ?>">
                                        <?= ucfirst($emp['status']) ?>
                                    </span>
                                </td>
                                <td><?= format_date($emp['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                <h5>No employees found</h5>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
