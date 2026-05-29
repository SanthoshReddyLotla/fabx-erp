<?php /** FabX ERP - Clients List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-building"></i> Clients</h1>
    <div class="page-actions">
        <a href="<?= base_url('clients/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Client
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-primary"><?= $stats['total'] ?? 0 ?></div>
            <div class="text-muted small">Active Clients</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-warning"><?= format_currency($stats['total_receivable'] ?? 0) ?></div>
            <div class="text-muted small">Total Outstanding</div>
        </div>
    </div>
</div>

<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" <?= (input('status') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (input('status') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
        </select>
    </form>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="clientsTable">
                <thead>
                    <tr>
                        <th>Code</th><th>Company Name</th><th>Contact Person</th>
                        <th>Email</th><th>Phone</th><th>City</th><th>GSTIN</th>
                        <th>Status</th><th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $c): ?>
                            <tr>
                                <td><strong><?= e($c['client_code']) ?></strong></td>
                                <td><?= e($c['company_name']) ?></td>
                                <td><?= e($c['contact_person'] ?? '-') ?></td>
                                <td><?= e($c['email'] ?? '-') ?></td>
                                <td><?= e($c['phone'] ?? '-') ?></td>
                                <td><?= e($c['city'] ?? '-') ?></td>
                                <td><?= e($c['gstin'] ?? '-') ?></td>
                                <td>
                                    <span class="badge-fx <?= $c['status'] === 'active' ? 'badge-fx-success' : 'badge-fx-secondary' ?>">
                                        <?= ucfirst($c['status']) ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="<?= base_url('clients/view/' . $c['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-building"></i>
                                <h5>No clients found</h5>
                                <p>Add your first client to begin managing relationships.</p>
                                <a href="<?= base_url('clients/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Client</a>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
