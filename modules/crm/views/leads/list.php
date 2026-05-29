<?php /** FabX ERP - CRM Leads List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-funnel"></i> Leads</h1>
    <div class="page-actions">
        <a href="<?= base_url('crm/pipeline') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-columns-gap"></i> Pipeline View
        </a>
    </div>
</div>

<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="new" <?= (input('status') === 'new') ? 'selected' : '' ?>>New</option>
            <option value="contacted" <?= (input('status') === 'contacted') ? 'selected' : '' ?>>Contacted</option>
            <option value="qualified" <?= (input('status') === 'qualified') ? 'selected' : '' ?>>Qualified</option>
            <option value="proposal_sent" <?= (input('status') === 'proposal_sent') ? 'selected' : '' ?>>Proposal Sent</option>
            <option value="negotiation" <?= (input('status') === 'negotiation') ? 'selected' : '' ?>>Negotiation</option>
            <option value="won" <?= (input('status') === 'won') ? 'selected' : '' ?>>Won</option>
            <option value="lost" <?= (input('status') === 'lost') ? 'selected' : '' ?>>Lost</option>
        </select>
    </form>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="leadsTable">
                <thead>
                    <tr>
                        <th>Company</th><th>Contact Person</th><th>Email</th>
                        <th>Phone</th><th>Source</th><th>Assigned To</th>
                        <th>Value</th><th>Status</th><th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leads)): ?>
                        <?php foreach ($leads as $lead): ?>
                            <tr>
                                <td><strong><?= e($lead['company_name'] ?? '-') ?></strong></td>
                                <td><?= e($lead['contact_person'] ?? '-') ?></td>
                                <td><?= e($lead['email'] ?? '-') ?></td>
                                <td><?= e($lead['phone'] ?? '-') ?></td>
                                <td><?= e(ucfirst($lead['source'] ?? '-')) ?></td>
                                <td><?= e($lead['assigned_name'] ?? 'Unassigned') ?></td>
                                <td><?= format_currency($lead['estimated_value'] ?? 0) ?></td>
                                <td>
                                    <?php $sc = match($lead['status'] ?? '') {
                                        'won' => 'badge-fx-success', 'lost' => 'badge-fx-danger',
                                        'new' => 'badge-fx-info', 'negotiation' => 'badge-fx-warning',
                                        default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $lead['status'] ?? '')) ?></span>
                                </td>
                                <td><?= format_date($lead['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-funnel"></i>
                                <h5>No leads found</h5>
                                <p>Start tracking your sales leads here.</p>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
