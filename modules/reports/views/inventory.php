<?php /** FabX ERP - Inventory Reports View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-clipboard-data"></i> Inventory Reports</h1>
    <div class="page-actions">
        <button onclick="exportTableToCSV('stockTable','inventory_report.csv')" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export</button>
    </div>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="stockTable">
                <thead>
                    <tr>
                        <th>Item Code</th><th>Name</th><th>Category</th>
                        <th>UOM</th><th>Stock</th><th>Reorder Level</th><th>Stock Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stock)): ?>
                        <?php foreach ($stock as $item): ?>
                            <tr class="<?= $item['stock_status'] === 'low' ? 'table-warning' : '' ?>">
                                <td><?= e($item['item_code']) ?></td>
                                <td><?= e($item['name']) ?></td>
                                <td><?= e($item['category_name'] ?? '-') ?></td>
                                <td><?= e($item['uom'] ?? '-') ?></td>
                                <td><?= e($item['current_stock'] ?? 0) ?></td>
                                <td><?= e($item['reorder_level'] ?? 0) ?></td>
                                <td>
                                    <?php if ($item['stock_status'] === 'low'): ?>
                                        <span class="badge-fx badge-fx-danger"><i class="bi bi-exclamation-triangle-fill"></i> Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge-fx badge-fx-success">OK</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-clipboard-data"></i><h5>No inventory data found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
