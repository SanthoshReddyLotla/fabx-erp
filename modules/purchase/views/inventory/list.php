<?php /** FabX ERP - Inventory List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-boxes"></i> Inventory</h1>
</div>
<?php if (($low_stock_count ?? 0) > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong><?= $low_stock_count ?> items</strong> are at or below reorder level.
</div>
<?php endif; ?>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="inventoryTable">
                <thead>
                    <tr>
                        <th>Item Code</th><th>Name</th><th>Category</th>
                        <th>UOM</th><th>Current Stock</th><th>Reorder Level</th>
                        <th>Last Price</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <?php $isLow = ($item['current_stock'] ?? 0) <= ($item['reorder_level'] ?? 0); ?>
                            <tr class="<?= $isLow ? 'table-warning' : '' ?>">
                                <td><strong><?= e($item['item_code']) ?></strong></td>
                                <td><?= e($item['name']) ?></td>
                                <td><?= e($item['category_name'] ?? '-') ?></td>
                                <td><?= e($item['uom'] ?? '-') ?></td>
                                <td>
                                    <?php if ($isLow): ?>
                                        <span class="text-danger fw-bold"><?= e($item['current_stock'] ?? 0) ?></span>
                                        <i class="bi bi-exclamation-circle-fill text-danger ms-1"></i>
                                    <?php else: ?>
                                        <?= e($item['current_stock'] ?? 0) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($item['reorder_level'] ?? 0) ?></td>
                                <td><?= format_currency($item['last_purchase_price'] ?? 0) ?></td>
                                <td>
                                    <span class="badge-fx <?= $item['status'] === 'active' ? 'badge-fx-success' : 'badge-fx-secondary' ?>">
                                        <?= ucfirst($item['status'] ?? '') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8"><div class="empty-state"><i class="bi bi-boxes"></i><h5>No inventory items found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
