<?php /** FabX ERP - Attendance View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-calendar-check"></i> Attendance</h1>
</div>

<div class="fx-card mb-3">
    <div class="fx-card-body p-3">
        <form method="GET" class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0 fw-bold">Date:</label>
            <input type="date" name="date" value="<?= e($date) ?>" class="form-control w-auto">
            <button type="submit" class="btn btn-fx btn-fx-primary">View</button>
        </form>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Employee Code</th><th>Employee Name</th><th>Check In</th>
                        <th>Check Out</th><th>Hours</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendance)): ?>
                        <?php foreach ($attendance as $a): ?>
                            <tr>
                                <td><?= e($a['employee_code'] ?? '-') ?></td>
                                <td><?= e($a['employee_name'] ?? '-') ?></td>
                                <td><?= e($a['check_in'] ?? '-') ?></td>
                                <td><?= e($a['check_out'] ?? '-') ?></td>
                                <td><?= e($a['working_hours'] ?? '-') ?></td>
                                <td>
                                    <?php $sc = match($a['status'] ?? '') {
                                        'present' => 'badge-fx-success', 'absent' => 'badge-fx-danger',
                                        'half_day' => 'badge-fx-warning', 'leave' => 'badge-fx-info',
                                        default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $a['status'] ?? '')) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-calendar-check"></i>
                                <h5>No attendance records for <?= e($date) ?></h5>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
