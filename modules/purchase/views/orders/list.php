<?php /** FabX ERP - Purchase Orders List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bag-check"></i> Purchase Orders</h1>
    <div class="page-actions">
        <a href="<?= base_url('purchase/orders/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New PO
        </a>
    </div>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>PO No</th><th>Date</th><th>Vendor</th>
                        <th>Total Amount</th><th>Delivery Date</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $po): ?>
                            <tr>
                                <td><strong><?= e($po['po_no']) ?></strong></td>
                                <td><?= format_date($po['po_date']) ?></td>
                                <td><?= e($po['vendor_name'] ?? '-') ?></td>
                                <td><?= format_currency($po['total_amount'] ?? 0) ?></td>
                                <td><?= format_date($po['delivery_date'] ?? null) ?></td>
                                <td>
                                    <?php $sc = match($po['status'] ?? '') {
                                        'received' => 'badge-fx-success', 'partial' => 'badge-fx-warning',
                                        'approved' => 'badge-fx-info', 'draft' => 'badge-fx-secondary',
                                        'cancelled' => 'badge-fx-danger', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($po['status'] ?? '') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-bag-check"></i><h5>No purchase orders found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
