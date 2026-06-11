<?php /** FabX ERP - Material Issues */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-arrow-bar-right text-warning"></i> Material Issues Register</h1>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <label class="text-muted small fw-semibold me-1">Filter by Project:</label>
        <select name="project_id" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">-- All Projects --</option>
            <?php foreach ($projects ?? [] as $proj): ?>
                <option value="<?= $proj['id'] ?>" <?= (input('project_id') == $proj['id']) ? 'selected' : '' ?>><?= e($proj['project_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (input('project_id')): ?>
            <a href="<?= base_url('purchase/issues') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Issues Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-box-arrow-up-right"></i> Material Issuance Log</h5>
        <span class="badge bg-dark border border-secondary text-muted"><?= count($issues) ?> records</span>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Issue No</th>
                        <th>Issue Date</th>
                        <th>Project</th>
                        <th>Issued To</th>
                        <th>Item / Material</th>
                        <th class="text-end">Qty Issued</th>
                        <th>UOM</th>
                        <th>Purpose / Work Order</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($issues)): ?>
                        <?php foreach ($issues as $iss): ?>
                            <?php $sc = match($iss['status'] ?? 'issued') {
                                'issued' => 'badge-fx-success',
                                'returned' => 'badge-fx-info',
                                'partial_return' => 'badge-fx-warning',
                                default => 'badge-fx-secondary'
                            }; ?>
                            <tr>
                                <td><strong class="text-primary font-monospace"><?= e($iss['issue_no'] ?? '-') ?></strong></td>
                                <td><?= format_date($iss['issue_date'] ?? null) ?></td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($iss['project_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center text-warning fw-bold flex-shrink-0" style="width:28px;height:28px;font-size:0.7rem;">
                                            <?= strtoupper(substr($iss['issued_to_name'] ?? 'U', 0, 2)) ?>
                                        </div>
                                        <small><?= e($iss['issued_to_name'] ?? '-') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-light-heading"><?= e($iss['item_name'] ?? '-') ?></div>
                                    <small class="text-muted"><?= e($iss['item_code'] ?? '') ?></small>
                                </td>
                                <td class="text-end fw-bold text-light-heading"><?= number_format($iss['quantity'] ?? 0, 3) ?></td>
                                <td><span class="badge bg-secondary opacity-75"><?= e($iss['uom'] ?? 'Nos') ?></span></td>
                                <td>
                                    <div class="small text-muted text-truncate" style="max-width:180px;" title="<?= e($iss['purpose'] ?? '') ?>">
                                        <?= e(substr($iss['purpose'] ?? '-', 0, 60)) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $iss['status'] ?? 'issued')) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-arrow-bar-right"></i>
                                <h5>No material issues found</h5>
                                <?php if (input('project_id')): ?>
                                    <p>No material issuances recorded for this project yet.</p>
                                <?php endif; ?>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> – <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> issues</small>
                <nav><div class="pagination-fx">
                    <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['page'] - 1 ?><?= input('project_id') ? '&project_id='.input('project_id') : '' ?>">&laquo;</a><?php endif; ?>
                    <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
                        <a href="?page=<?= $i ?><?= input('project_id') ? '&project_id='.input('project_id') : '' ?>" class="<?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['page'] + 1 ?><?= input('project_id') ? '&project_id='.input('project_id') : '' ?>">&raquo;</a><?php endif; ?>
                </div></nav>
            </div>
        </div>
    <?php endif; ?>
</div>
