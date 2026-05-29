<?php /** FabX ERP - Purchase Requisitions List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-clipboard-check"></i> Purchase Requisitions</h1>
    <div class="page-actions">
        <a href="<?= base_url('purchase/requisitions/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New PR
        </a>
    </div>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>PR No</th><th>Date</th><th>Department</th>
                        <th>Requested By</th><th>Required By</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requisitions)): ?>
                        <?php foreach ($requisitions as $pr): ?>
                            <tr>
                                <td><strong><?= e($pr['pr_no']) ?></strong></td>
                                <td><?= format_date($pr['pr_date']) ?></td>
                                <td><?= e($pr['department_name'] ?? '-') ?></td>
                                <td><?= e($pr['requested_by_name'] ?? '-') ?></td>
                                <td><?= format_date($pr['required_by_date'] ?? null) ?></td>
                                <td>
                                    <?php $sc = match($pr['status'] ?? '') {
                                        'approved' => 'badge-fx-success', 'rejected' => 'badge-fx-danger',
                                        'submitted' => 'badge-fx-warning', 'pending' => 'badge-fx-info',
                                        default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($pr['status'] ?? '') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-clipboard-check"></i><h5>No purchase requisitions found</h5><a href="<?= base_url('purchase/requisitions/create') ?>" class="btn btn-primary btn-sm">Create PR</a></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
