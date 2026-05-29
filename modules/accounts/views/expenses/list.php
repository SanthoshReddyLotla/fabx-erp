<?php /** FabX ERP - Expenses List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-credit-card"></i> Expenses</h1>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Category</th><th>Description</th><th>Amount</th>
                        <th>Date</th><th>Submitted By</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expenses)): ?>
                        <?php foreach ($expenses as $e): ?>
                            <tr>
                                <td><?= e(ucfirst($e['category'] ?? '-')) ?></td>
                                <td><?= e(substr($e['description'] ?? '', 0, 60)) ?></td>
                                <td><?= format_currency($e['amount'] ?? 0) ?></td>
                                <td><?= format_date($e['expense_date']) ?></td>
                                <td><?= e($e['created_name'] ?? '-') ?></td>
                                <td>
                                    <?php $sc = match($e['status'] ?? '') {
                                        'approved' => 'badge-fx-success', 'rejected' => 'badge-fx-danger',
                                        'pending' => 'badge-fx-warning', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($e['status'] ?? '') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-credit-card"></i><h5>No expenses found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
