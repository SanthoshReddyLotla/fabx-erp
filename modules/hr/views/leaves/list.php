<?php /** FabX ERP - Leave Management List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-calendar-x text-warning"></i> Leave Management</h1>
    <div class="page-actions">
        <a href="<?= base_url('hr/leaves/apply') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> Apply Leave
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="pending" <?= (input('status') === 'pending') ? 'selected' : '' ?>>Pending Approval</option>
            <option value="approved" <?= (input('status') === 'approved') ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= (input('status') === 'rejected') ? 'selected' : '' ?>>Rejected</option>
            <option value="cancelled" <?= (input('status') === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <select name="leave_type" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="casual" <?= (input('leave_type') === 'casual') ? 'selected' : '' ?>>Casual Leave</option>
            <option value="sick" <?= (input('leave_type') === 'sick') ? 'selected' : '' ?>>Sick Leave</option>
            <option value="earned" <?= (input('leave_type') === 'earned') ? 'selected' : '' ?>>Earned Leave</option>
            <option value="maternity" <?= (input('leave_type') === 'maternity') ? 'selected' : '' ?>>Maternity</option>
            <option value="unpaid" <?= (input('leave_type') === 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
        </select>
        <?php if (input('status') || input('leave_type')): ?>
            <a href="<?= base_url('hr/leaves') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Leaves Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-journal-check"></i> Leave Requests Register</h5>
        <?php
        $pendingCount = array_filter($leaves ?? [], fn($l) => ($l['status'] ?? '') === 'pending');
        if (count($pendingCount)): ?>
            <span class="badge-fx badge-fx-warning"><?= count($pendingCount) ?> Pending Approval</span>
        <?php endif; ?>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th class="text-center">Days</th>
                        <th>Reason</th>
                        <th class="text-center">Status</th>
                        <th>Applied On</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leaves)): ?>
                        <?php foreach ($leaves as $l): ?>
                            <?php
                            $typeClass = match($l['leave_type'] ?? '') {
                                'sick' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                'earned' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                'casual' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                'maternity' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                default => 'bg-secondary bg-opacity-10 text-secondary border border-secondary'
                            };
                            $sc = match($l['status'] ?? '') {
                                'approved' => 'badge-fx-success',
                                'rejected' => 'badge-fx-danger',
                                'pending' => 'badge-fx-warning',
                                'cancelled' => 'badge-fx-secondary',
                                default => 'badge-fx-secondary'
                            };
                            $days = (int)($l['days'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($l['employee_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <span class="badge text-uppercase <?= $typeClass ?>" style="font-size:0.7rem;">
                                        <?= e(ucfirst($l['leave_type'] ?? '-')) ?>
                                    </span>
                                </td>
                                <td><?= format_date($l['from_date']) ?></td>
                                <td><?= format_date($l['to_date']) ?></td>
                                <td class="text-center fw-bold <?= $days > 5 ? 'text-danger' : 'text-light-heading' ?>">
                                    <?= $days ?> day<?= $days !== 1 ? 's' : '' ?>
                                </td>
                                <td>
                                    <div class="small text-muted text-truncate" style="max-width:200px;" title="<?= e($l['reason'] ?? '') ?>">
                                        <?= e(substr($l['reason'] ?? '-', 0, 60)) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($l['status'] ?? '') ?></span>
                                </td>
                                <td><small><?= format_date($l['created_at']) ?></small></td>
                                <td class="text-end pe-3">
                                    <?php if (($l['status'] ?? '') === 'pending'): ?>
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="<?= base_url('hr/leaves/approve/' . $l['id']) ?>"
                                               class="btn btn-sm btn-light text-success" title="Approve"
                                               onclick="return confirm('Approve this leave request?');">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </a>
                                            <a href="<?= base_url('hr/leaves/reject/' . $l['id']) ?>"
                                               class="btn btn-sm btn-light text-danger" title="Reject"
                                               onclick="return confirm('Reject this leave request?');">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-calendar-x"></i>
                                <h5>No leave requests found</h5>
                                <a href="<?= base_url('hr/leaves/apply') ?>" class="btn btn-primary btn-sm">Apply Leave</a>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> – <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> requests</small>
                <nav><div class="pagination-fx">
                    <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['page'] - 1 ?>">&laquo;</a><?php endif; ?>
                    <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['page'] + 1 ?>">&raquo;</a><?php endif; ?>
                </div></nav>
            </div>
        </div>
    <?php endif; ?>
</div>
