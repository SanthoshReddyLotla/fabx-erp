<?php /** FabX ERP - Purchase Requisitions List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-clipboard-check text-primary"></i> Purchase Requisitions</h1>
    <div class="page-actions">
        <a href="<?= base_url('purchase/requisitions/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Requisition
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="submitted" <?= (input('status') === 'submitted') ? 'selected' : '' ?>>Submitted</option>
            <option value="approved" <?= (input('status') === 'approved') ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= (input('status') === 'rejected') ? 'selected' : '' ?>>Rejected</option>
            <option value="po_raised" <?= (input('status') === 'po_raised') ? 'selected' : '' ?>>PO Raised</option>
        </select>
        <?php if (input('status')): ?>
            <a href="<?= base_url('purchase/requisitions') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Requisitions Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-check"></i> Purchase Requisition Register</h5>
        <?php
        $pendingPRs = array_filter($requisitions ?? [], fn($r) => ($r['status'] ?? '') === 'submitted');
        if (count($pendingPRs)): ?>
            <span class="badge-fx badge-fx-warning"><?= count($pendingPRs) ?> Awaiting Approval</span>
        <?php endif; ?>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>PR Number</th>
                        <th>Date</th>
                        <th>Department</th>
                        <th>Requested By</th>
                        <th>Project Ref</th>
                        <th>Required By</th>
                        <th>Justification</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requisitions)): ?>
                        <?php foreach ($requisitions as $pr): ?>
                            <?php $sc = match($pr['status'] ?? '') {
                                'approved' => 'badge-fx-success',
                                'rejected' => 'badge-fx-danger',
                                'submitted' => 'badge-fx-warning',
                                'po_raised' => 'badge-fx-info',
                                default => 'badge-fx-secondary'
                            }; ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary font-monospace"><?= e($pr['pr_no']) ?></div>
                                </td>
                                <td><?= format_date($pr['pr_date']) ?></td>
                                <td>
                                    <span class="badge bg-dark border border-secondary text-muted small"><?= e($pr['department_name'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center text-primary fw-bold flex-shrink-0" style="width:28px;height:28px;font-size:0.7rem;">
                                            <?= strtoupper(substr($pr['requested_by_name'] ?? 'U', 0, 2)) ?>
                                        </div>
                                        <small><?= e($pr['requested_by_name'] ?? '-') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($pr['project_id'] ?? null): ?>
                                        <a href="<?= base_url('projects/view/' . $pr['project_id']) ?>" class="text-info small text-decoration-none">
                                            <i class="bi bi-diagram-3"></i> Linked Project
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $due = $pr['required_by_date'] ?? null; ?>
                                    <?php if ($due && strtotime($due) < time() && !in_array($pr['status'] ?? '', ['approved', 'po_raised'])): ?>
                                        <span class="text-danger fw-bold small"><i class="bi bi-exclamation-circle"></i> <?= format_date($due) ?></span>
                                    <?php else: ?>
                                        <span class="small"><?= format_date($due) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small text-muted text-truncate" style="max-width:180px;" title="<?= e($pr['justification'] ?? '') ?>">
                                        <?= e(substr($pr['justification'] ?? '-', 0, 60)) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $pr['status'] ?? '')) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-clipboard-check"></i>
                                <h5>No purchase requisitions found</h5>
                                <a href="<?= base_url('purchase/requisitions/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Create First PR</a>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> – <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> requisitions</small>
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
