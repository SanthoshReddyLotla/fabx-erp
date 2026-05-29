<?php /** FabX ERP - Appraisals List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-star-half"></i> Performance Appraisals</h1>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Employee</th><th>Period</th><th>Reviewer</th>
                        <th>Overall Score</th><th>Grade</th><th>Status</th><th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($appraisals)): ?>
                        <?php foreach ($appraisals as $a): ?>
                            <tr>
                                <td><?= e($a['employee_name'] ?? '-') ?></td>
                                <td><?= e($a['review_period'] ?? '-') ?></td>
                                <td><?= e($a['reviewer_name'] ?? '-') ?></td>
                                <td><?= e($a['overall_score'] ?? '-') ?>%</td>
                                <td><strong><?= e($a['grade'] ?? '-') ?></strong></td>
                                <td>
                                    <?php $sc = match($a['status'] ?? '') {
                                        'completed' => 'badge-fx-success', 'in_progress' => 'badge-fx-warning',
                                        'draft' => 'badge-fx-secondary', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $a['status'] ?? '')) ?></span>
                                </td>
                                <td><?= format_date($a['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-star-half"></i>
                                <h5>No appraisal records found</h5>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
