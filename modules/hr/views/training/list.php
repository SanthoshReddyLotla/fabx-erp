<?php /** FabX ERP - Training List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-mortarboard"></i> Training Records</h1>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Training Title</th><th>Type</th><th>Trainer</th>
                        <th>Department</th><th>Scheduled Date</th><th>Duration (hrs)</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($trainings)): ?>
                        <?php foreach ($trainings as $t): ?>
                            <tr>
                                <td><strong><?= e($t['title'] ?? '-') ?></strong></td>
                                <td><?= e(ucfirst($t['type'] ?? '-')) ?></td>
                                <td><?= e($t['trainer_name'] ?? '-') ?></td>
                                <td><?= e($t['department_name'] ?? '-') ?></td>
                                <td><?= format_date($t['scheduled_date'] ?? null) ?></td>
                                <td><?= e($t['duration_hours'] ?? '-') ?></td>
                                <td>
                                    <?php $sc = match($t['status'] ?? '') {
                                        'completed' => 'badge-fx-success', 'scheduled' => 'badge-fx-info',
                                        'cancelled' => 'badge-fx-danger', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($t['status'] ?? '') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-mortarboard"></i>
                                <h5>No training records found</h5>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
