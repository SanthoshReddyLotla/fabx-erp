<?php /** FabX ERP - Purchase Orders List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bag-check-fill text-primary"></i> Purchase Orders</h1>
    <div class="page-actions">
        <a href="<?= base_url('purchase/orders/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Purchase Order
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="draft" <?= (input('status') === 'draft') ? 'selected' : '' ?>>Draft</option>
            <option value="approved" <?= (input('status') === 'approved') ? 'selected' : '' ?>>Approved</option>
            <option value="sent" <?= (input('status') === 'sent') ? 'selected' : '' ?>>Sent to Vendor</option>
            <option value="partial" <?= (input('status') === 'partial') ? 'selected' : '' ?>>Partially Received</option>
            <option value="received" <?= (input('status') === 'received') ? 'selected' : '' ?>>Fully Received</option>
            <option value="cancelled" <?= (input('status') === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <?php if (input('status')): ?>
            <a href="<?= base_url('purchase/orders') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Orders Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-ruled"></i> PO Register</h5>
        <span class="badge bg-dark border border-secondary text-muted"><?= count($orders) ?> records</span>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Date</th>
                        <th>Vendor / Supplier</th>
                        <th>PR Reference</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">GST</th>
                        <th class="text-end">Total</th>
                        <th>Delivery Date</th>
                        <th>Prepared By</th>
                        <th class="text-center">Status</th>
                        <th class="actions text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $po): ?>
                            <?php
                            $sc = match($po['status'] ?? '') {
                                'received' => 'badge-fx-success',
                                'partial' => 'badge-fx-warning',
                                'approved' => 'badge-fx-info',
                                'sent' => 'badge-fx-primary',
                                'draft' => 'badge-fx-secondary',
                                'cancelled' => 'badge-fx-danger',
                                default => 'badge-fx-secondary'
                            };
                            $isOverdue = ($po['delivery_date'] ?? null) && strtotime($po['delivery_date']) < time() && !in_array($po['status'] ?? '', ['received', 'cancelled']);
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary font-monospace"><?= e($po['po_no']) ?></div>
                                </td>
                                <td><small><?= format_date($po['po_date']) ?></small></td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($po['vendor_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <?php if ($po['pr_id'] ?? null): ?>
                                        <span class="badge bg-dark border border-secondary text-muted font-monospace small">PR-Linked</span>
                                    <?php else: ?>
                                        <span class="text-muted small">Direct</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-muted small"><?= format_currency($po['subtotal'] ?? 0) ?></td>
                                <td class="text-end text-muted small"><?= format_currency($po['gst_amount'] ?? 0) ?></td>
                                <td class="text-end fw-bold text-success"><?= format_currency($po['total_amount'] ?? 0) ?></td>
                                <td>
                                    <?php if ($isOverdue): ?>
                                        <span class="text-danger fw-bold small"><i class="bi bi-exclamation-circle"></i> <?= format_date($po['delivery_date']) ?></span>
                                    <?php else: ?>
                                        <small><?= format_date($po['delivery_date'] ?? null) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><small><?= e($po['prepared_by_name'] ?? '-') ?></small></td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($po['status'] ?? '') ?></span>
                                </td>
                                <td class="actions text-end">
                                    <a href="<?= base_url('purchase/orders/print/' . $po['id']) ?>" target="_blank" class="btn btn-sm btn-light" title="Print / PDF">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="11">
                            <div class="empty-state">
                                <i class="bi bi-bag-check"></i>
                                <h5>No purchase orders found</h5>
                                <a href="<?= base_url('purchase/orders/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Create PO</a>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> – <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> orders</small>
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
