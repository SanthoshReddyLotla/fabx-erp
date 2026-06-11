<?php /** FabX ERP - CRM Quotations List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-file-text text-primary"></i> Quotations</h1>
    <div class="page-actions">
        <a href="<?= base_url('crm/quotations/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Quotation
        </a>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="quotationsTable">
                <thead>
                    <tr>
                        <th>Quotation No</th>
                        <th>Client Name</th>
                        <th>Subject</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th class="actions text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($quotations)): ?>
                        <?php foreach ($quotations as $q): ?>
                            <tr>
                                <td><strong>
                                        <?= e($q['quotation_no']) ?>
                                    </strong></td>
                                <td>
                                    <?= e($q['client_name'] ?? '-') ?>
                                </td>
                                <td>
                                    <?= e($q['subject'] ?? '-') ?>
                                </td>
                                <td>
                                    <?= format_currency($q['total_amount'] ?? 0) ?>
                                </td>
                                <td>
                                    <?php
                                    $sc = match ($q['status'] ?? '') {
                                        'approved', 'accepted' => 'badge-fx-success',
                                        'rejected', 'expired' => 'badge-fx-danger',
                                        'sent', 'under_review' => 'badge-fx-warning',
                                        default => 'badge-fx-secondary'
                                    };
                                    ?>
                                    <span class="badge-fx <?= $sc ?>">
                                        <?= ucfirst(str_replace('_', ' ', $q['status'] ?? '')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= format_date($q['created_at']) ?>
                                </td>
                                <td class="actions text-end">
                                    <a href="<?= base_url('crm/quotations/print/' . $q['id']) ?>" target="_blank" class="btn btn-sm btn-light" title="Print / PDF">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-file-text"></i>
                                    <h5>No quotations found</h5>
                                    <p>Start by creating your first client quotation.</p>
                                    <a href="<?= base_url('crm/quotations/create') ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg"></i> Create Quotation
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>