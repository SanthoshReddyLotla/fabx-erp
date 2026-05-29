<?php /** FabX ERP - Vendor Payments List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bank"></i> Vendor Payments</h1>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Vendor</th><th>Amount</th><th>Mode</th>
                        <th>Reference</th><th>Payment Date</th><th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td><?= e($p['vendor_name'] ?? '-') ?></td>
                                <td><?= format_currency($p['amount'] ?? 0) ?></td>
                                <td><?= e(ucfirst($p['payment_mode'] ?? '-')) ?></td>
                                <td><?= e($p['reference_no'] ?? '-') ?></td>
                                <td><?= format_date($p['payment_date']) ?></td>
                                <td><?= e($p['remarks'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-bank"></i><h5>No vendor payments found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
