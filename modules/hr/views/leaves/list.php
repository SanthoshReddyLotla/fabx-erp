<?php /** FabX ERP - Leaves List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-calendar-x"></i> Leave Management</h1>
</div>

<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="pending" <?= (input('status') === 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= (input('status') === 'approved') ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= (input('status') === 'rejected') ? 'selected' : '' ?>>Rejected</option>
        </select>
    </form>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Employee</th><th>Leave Type</th><th>From</th><th>To</th>
                        <th>Days</th><th>Reason</th><th>Status</th><th>Applied On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leaves)): ?>
                        <?php foreach ($leaves as $l): ?>
                            <tr>
                                <td><?= e($l['employee_name'] ?? '-') ?></td>
                                <td><?= e(strtoupper($l['leave_type'] ?? '-')) ?></td>
                                <td><?= format_date($l['from_date']) ?></td>
                                <td><?= format_date($l['to_date']) ?></td>
                                <td><?= e($l['days'] ?? '-') ?></td>
                                <td><?= e(substr($l['reason'] ?? '', 0, 60)) ?></td>
                                <td>
                                    <?php $sc = match($l['status'] ?? '') {
                                        'approved' => 'badge-fx-success', 'rejected' => 'badge-fx-danger',
                                        'pending' => 'badge-fx-warning', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($l['status'] ?? '') ?></span>
                                </td>
                                <td><?= format_date($l['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-calendar-x"></i>
                                <h5>No leave requests found</h5>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
