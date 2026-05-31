<?php /** FabX ERP - HR Employees List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-people-fill text-primary"></i> Employees Directory</h1>
    <div class="page-actions">
        <a href="<?= base_url('hr/employees/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-person-plus-fill"></i> Add Employee
        </a>
    </div>
</div>

<!-- KPI Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-primary"><?= $stats['total'] ?? 0 ?></div>
            <div class="text-muted small">Active Employees</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-success"><?= $stats['new_this_month'] ?? 0 ?></div>
            <div class="text-muted small">Joined This Month</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-info"><?= $stats['on_leave'] ?? 0 ?></div>
            <div class="text-muted small">On Leave Today</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-warning"><?= $stats['inactive'] ?? 0 ?></div>
            <div class="text-muted small">Inactive / Resigned</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <input type="text" name="q" value="<?= e(input('q')) ?>" class="form-control w-auto" placeholder="Search name, code, email…">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="active" <?= (input('status', 'active') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (input('status', 'active') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
            <option value="resigned" <?= (input('status', 'active') === 'resigned') ? 'selected' : '' ?>>Resigned</option>
        </select>
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-search"></i> Search</button>
        <a href="<?= base_url('hr/employees') ?>" class="btn btn-outline-secondary">Clear</a>
    </form>
</div>

<!-- Employees Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-table"></i> Employee Registry</h5>
        <span class="badge bg-dark border border-secondary text-muted"><?= count($employees) ?> records</span>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="employeesTable">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Code</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th class="text-center">Status</th>
                        <th>Joined</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($employees)): ?>
                        <?php foreach ($employees as $emp): ?>
                            <?php
                            $initials = strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'] ?? '', 0, 1));
                            $avatarColors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                            $avatarColor = $avatarColors[crc32($emp['id']) % count($avatarColors)];
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 <?= $avatarColor ?>" style="width:36px; height:36px; font-size:0.8rem;">
                                            <?= $initials ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-light-heading"><?= e($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                                            <small class="text-muted"><?= e($emp['phone'] ?? '') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-dark border border-secondary text-muted font-monospace"><?= e($emp['employee_code']) ?></span></td>
                                <td><a href="mailto:<?= e($emp['email']) ?>" class="text-info text-decoration-none small"><?= e($emp['email']) ?></a></td>
                                <td><?= e($emp['role_name'] ?? '-') ?></td>
                                <td><?= e($emp['department_name'] ?? '-') ?></td>
                                <td><span class="small"><?= e($emp['designation'] ?? '-') ?></span></td>
                                <td class="text-center">
                                    <?php $sc = match($emp['status'] ?? '') {
                                        'active' => 'badge-fx-success',
                                        'inactive' => 'badge-fx-secondary',
                                        'resigned' => 'badge-fx-danger',
                                        default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($emp['status']) ?></span>
                                </td>
                                <td><small><?= format_date($emp['created_at']) ?></small></td>
                                <td class="text-end pe-3">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="<?= base_url('hr/employees/view/' . $emp['id']) ?>" class="btn btn-sm btn-light" title="View Profile"><i class="bi bi-eye"></i></a>
                                        <a href="<?= base_url('hr/employees/edit/' . $emp['id']) ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                <h5>No employees found</h5>
                                <a href="<?= base_url('hr/employees/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-person-plus-fill"></i> Add First Employee</a>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
        <div class="fx-card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> – <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> employees</small>
                <nav><div class="pagination-fx">
                    <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['page'] - 1 ?>&status=<?= input('status', 'active') ?>">&laquo;</a><?php endif; ?>
                    <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
                        <a href="?page=<?= $i ?>&status=<?= input('status', 'active') ?>" class="<?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['page'] + 1 ?>&status=<?= input('status', 'active') ?>">&raquo;</a><?php endif; ?>
                </div></nav>
            </div>
        </div>
    <?php endif; ?>
</div>
