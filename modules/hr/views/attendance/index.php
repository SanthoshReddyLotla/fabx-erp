<?php /** FabX ERP - Attendance Management */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-calendar-check text-success"></i> Daily Attendance Register</h1>
</div>

<!-- Date Selector -->
<div class="fx-card mb-4">
    <div class="fx-card-body p-3">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <label class="form-label mb-0 fw-semibold text-muted small">Select Date:</label>
            <input type="date" name="date" value="<?= e($date) ?>" class="form-control w-auto bg-dark border-secondary text-white">
            <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-search"></i> View</button>
            <?php if ($date !== date('Y-m-d')): ?>
                <a href="<?= base_url('hr/attendance') ?>" class="btn btn-outline-secondary btn-sm">Today</a>
            <?php endif; ?>
            <span class="ms-auto text-muted small"><i class="bi bi-calendar3"></i> <?= date('l, d F Y', strtotime($date)) ?></span>
        </form>
    </div>
</div>

<?php
// Compute stats from records
$present = 0; $absent = 0; $halfDay = 0; $onLeave = 0;
foreach ($attendance as $a) {
    match($a['status'] ?? '') {
        'present' => $present++,
        'absent' => $absent++,
        'half_day' => $halfDay++,
        'leave' => $onLeave++,
        default => null
    };
}
$total = count($attendance);
?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-success"><?= $present ?></div>
            <div class="text-muted small"><i class="bi bi-check-circle text-success"></i> Present</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-danger"><?= $absent ?></div>
            <div class="text-muted small"><i class="bi bi-x-circle text-danger"></i> Absent</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-warning"><?= $halfDay ?></div>
            <div class="text-muted small"><i class="bi bi-circle-half text-warning"></i> Half Day</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-info"><?= $onLeave ?></div>
            <div class="text-muted small"><i class="bi bi-calendar-x text-info"></i> On Leave</div>
        </div>
    </div>
</div>

<!-- Attendance Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person-check"></i> Attendance Log for <?= date('d M Y', strtotime($date)) ?></h5>
        <?php if ($total > 0): ?>
            <span class="badge bg-dark border border-secondary text-muted">
                <?= round(($present + $halfDay * 0.5) / max(1, $total) * 100) ?>% Attendance Rate
            </span>
        <?php endif; ?>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Code</th>
                        <th class="text-center">Check In</th>
                        <th class="text-center">Check Out</th>
                        <th class="text-center">Working Hours</th>
                        <th>Shift</th>
                        <th class="text-center">Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendance)): ?>
                        <?php foreach ($attendance as $a): ?>
                            <?php
                            $sc = match($a['status'] ?? '') {
                                'present' => 'badge-fx-success',
                                'absent' => 'badge-fx-danger',
                                'half_day' => 'badge-fx-warning',
                                'leave' => 'badge-fx-info',
                                'holiday' => 'badge-fx-secondary',
                                default => 'badge-fx-secondary'
                            };
                            $hours = (float)($a['working_hours'] ?? 0);
                            $hoursClass = $hours < 4 && $hours > 0 ? 'text-warning' : ($hours >= 8 ? 'text-success' : 'text-light-heading');
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($a['employee_name'] ?? '-') ?></div>
                                </td>
                                <td><span class="badge bg-dark border border-secondary text-muted font-monospace small"><?= e($a['employee_code'] ?? '-') ?></span></td>
                                <td class="text-center">
                                    <?php if ($a['check_in']): ?>
                                        <span class="small fw-bold text-success"><i class="bi bi-box-arrow-in-right"></i> <?= e($a['check_in']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($a['check_out']): ?>
                                        <span class="small fw-bold text-danger"><i class="bi bi-box-arrow-right"></i> <?= e($a['check_out']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold <?= $hoursClass ?>">
                                        <?= $hours > 0 ? number_format($hours, 1) . ' hrs' : '—' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $shiftIcon = match($a['shift'] ?? '') {
                                        'day' => '<i class="bi bi-brightness-high text-warning" title="Day"></i>',
                                        'night' => '<i class="bi bi-moon-stars text-info" title="Night"></i>',
                                        default => '<i class="bi bi-clock text-secondary" title="General"></i>'
                                    }; ?>
                                    <span class="small"><?= $shiftIcon ?> <?= ucfirst($a['shift'] ?? 'General') ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $a['status'] ?? '')) ?></span>
                                </td>
                                <td><small class="text-muted"><?= e(substr($a['remarks'] ?? '', 0, 50)) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-calendar-check"></i>
                                <h5>No attendance records for <?= e(date('d M Y', strtotime($date))) ?></h5>
                                <p>Records are populated automatically via biometric or manual entry.</p>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
