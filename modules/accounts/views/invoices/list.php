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
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td><a href="<?= base_url('accounts/invoices/view/' . $inv['id']) ?>" class="text-info fw-bold"><?= e($inv['invoice_no']) ?></a></td>
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
                                <td class="actions text-end pe-3">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="<?= base_url('accounts/invoices/view/' . $inv['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                                        <?php if (($inv['status'] ?? '') === 'draft'): ?>
                                            <a href="<?= base_url('accounts/invoices/edit/' . $inv['id']) ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <?php endif; ?>
                                        <?php if (($inv['status'] ?? '') !== 'paid'): ?>
                                            <a href="<?= base_url('accounts/invoices/markAsPaid/' . $inv['id']) ?>" class="btn btn-sm btn-light text-success" title="Mark Paid" data-confirm="Are you sure you want to mark this invoice as fully paid?" onclick="return confirm('Are you sure you want to mark this invoice as fully paid?');"><i class="bi bi-check-circle-fill"></i></a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('accounts/invoices/delete/' . $inv['id']) ?>" class="btn btn-sm btn-light text-danger" title="Delete" data-swal-confirm="Are you sure you want to delete this invoice? This action will completely remove all associated line items." onclick="return confirm('Are you sure you want to delete this invoice? This action will completely remove all associated line items.');"><i class="bi bi-trash-fill"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
 ̑                            <div class="empty-state">
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
