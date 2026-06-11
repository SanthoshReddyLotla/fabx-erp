<?php /** FabX ERP - GRN List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-box-seam-fill text-success"></i> Goods Receipt Notes (GRN)</h1>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Inspection Status</option>
            <option value="pending" <?= (input('status') === 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="passed" <?= (input('status') === 'passed') ? 'selected' : '' ?>>QC Passed</option>
            <option value="failed" <?= (input('status') === 'failed') ? 'selected' : '' ?>>QC Failed</option>
            <option value="partial" <?= (input('status') === 'partial') ? 'selected' : '' ?>>Partial Accepted</option>
        </select>
        <?php if (input('status')): ?>
            <a href="<?= base_url('purchase/grn') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- GRN Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-inboxes-fill"></i> Goods Receipt Register</h5>
        <span class="badge bg-dark border border-secondary text-muted"><?= count($grns) ?> records</span>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>GRN Number</th>
                        <th>PO Reference</th>
                        <th>Vendor / Supplier</th>
                        <th>Receipt Date</th>
                        <th>Vendor Invoice No</th>
                        <th class="text-end">Received Qty</th>
                        <th class="text-end">Accepted Qty</th>
                        <th class="text-end">Rejected Qty</th>
                        <th class="text-center">QC Status</th>
                        <th>Received By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($grns)): ?>
                        <?php foreach ($grns as $grn): ?>
                            <?php $sc = match($grn['inspection_status'] ?? '') {
                                'passed' => 'badge-fx-success',
                                'failed' => 'badge-fx-danger',
                                'partial' => 'badge-fx-warning',
                                'pending' => 'badge-fx-secondary',
                                default => 'badge-fx-secondary'
                            }; ?>
                            <tr>
                                <td><strong class="text-primary font-monospace"><?= e($grn['grn_no'] ?? '-') ?></strong></td>
                                <td><span class="badge bg-dark border border-secondary text-muted font-monospace small"><?= e($grn['po_no'] ?? '-') ?></span></td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($grn['vendor_name'] ?? '-') ?></div>
                                </td>
                                <td><?= format_date($grn['receipt_date'] ?? null) ?></td>
                                <td><small><?= e($grn['vendor_invoice_no'] ?? '-') ?></small></td>
                                <td class="text-end fw-semibold"><?= number_format($grn['received_quantity'] ?? 0, 3) ?></td>
                                <td class="text-end text-success fw-bold"><?= number_format($grn['accepted_quantity'] ?? 0, 3) ?></td>
                                <td class="text-end <?= ($grn['rejected_quantity'] ?? 0) > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">
                                    <?= number_format($grn['rejected_quantity'] ?? 0, 3) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($grn['inspection_status'] ?? 'pending') ?></span>
                                </td>
                                <td>
                                    <small><?= e($grn['received_by_name'] ?? '-') ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10">
                            <div class="empty-state">
                                <i class="bi bi-box-seam"></i>
                                <h5>No GRN records found</h5>
                                <p class="text-muted small">Goods Receipt Notes are created when materials arrive against a Purchase Order.</p>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> – <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> GRNs</small>
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
