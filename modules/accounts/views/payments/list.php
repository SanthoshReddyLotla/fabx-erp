<?php /** FabX ERP - Payments List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-cash-stack"></i> Payments Received</h1>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Receipt No</th><th>Client</th><th>Invoice No</th>
                        <th>Amount</th><th>Mode</th><th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td><?= e($p['receipt_no'] ?? '-') ?></td>
                                <td><?= e($p['client_name'] ?? '-') ?></td>
                                <td><?= e($p['invoice_no'] ?? '-') ?></td>
                                <td><?= format_currency($p['amount'] ?? 0) ?></td>
                                <td><?= e(ucfirst($p['payment_mode'] ?? '-')) ?></td>
                                <td><?= format_date($p['payment_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-cash-stack"></i><h5>No payments recorded</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
