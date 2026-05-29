<?php /** FabX ERP - Accounts Invoices List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-receipt"></i> Invoices</h1>
    <div class="page-actions">
        <a href="<?= base_url('accounts/invoices/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Invoice
        </a>
    </div>
</div>

<div class="fx-card mb-4">
    <div class="fx-card-body p-3 text-center">
        <div class="text-muted small mb-1">Total Outstanding</div>
        <div class="display-6 fw-bold text-danger"><?= format_currency($total_outstanding ?? 0) ?></div>
    </div>
</div>

<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="draft" <?= (input('status') === 'draft') ? 'selected' : '' ?>>Draft</option>
            <option value="sent" <?= (input('status') === 'sent') ? 'selected' : '' ?>>Sent</option>
            <option value="partial" <?= (input('status') === 'partial') ? 'selected' : '' ?>>Partial</option>
            <option value="paid" <?= (input('status') === 'paid') ? 'selected' : '' ?>>Paid</option>
            <option value="overdue" <?= (input('status') === 'overdue') ? 'selected' : '' ?>>Overdue</option>
        </select>
    </form>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="invoicesTable">
                <thead>
                    <tr>
                        <th>Invoice No</th><th>Client</th><th>Date</th><th>Due Date</th>
                        <th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td><strong><?= e($inv['invoice_no']) ?></strong></td>
                                <td><?= e($inv['client_name'] ?? '-') ?></td>
                                <td><?= format_date($inv['invoice_date']) ?></td>
                                <td><?= format_date($inv['due_date']) ?></td>
                                <td><?= format_currency($inv['grand_total'] ?? 0) ?></td>
                                <td><?= format_currency($inv['paid_amount'] ?? 0) ?></td>
                                <td><?= format_currency(($inv['grand_total'] ?? 0) - ($inv['paid_amount'] ?? 0)) ?></td>
                                <td>
                                    <?php $sc = match($inv['status'] ?? '') {
                                        'paid' => 'badge-fx-success', 'overdue' => 'badge-fx-danger',
                                        'sent' => 'badge-fx-info', 'partial' => 'badge-fx-warning',
                                        'draft' => 'badge-fx-secondary', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($inv['status'] ?? '') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-receipt"></i>
                                <h5>No invoices found</h5>
                                <a href="<?= base_url('accounts/invoices/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Create Invoice</a>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
