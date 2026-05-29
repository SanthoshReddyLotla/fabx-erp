<?php /** FabX ERP - Vendors List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-truck"></i> Vendors</h1>
    <div class="page-actions">
        <a href="<?= base_url('vendors/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Vendor
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-primary"><?= $stats['total'] ?? 0 ?></div>
            <div class="text-muted small">Active Vendors</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-warning"><?= $stats['pending'] ?? 0 ?></div>
            <div class="text-muted small">Pending Approval</div>
        </div>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="vendorsTable">
                <thead>
                    <tr>
                        <th>Code</th><th>Company Name</th><th>Contact Person</th>
                        <th>Email</th><th>Phone</th><th>Category</th>
                        <th>Approval</th><th>Status</th><th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vendors)): ?>
                        <?php foreach ($vendors as $v): ?>
                            <tr>
                                <td><strong><?= e($v['vendor_code']) ?></strong></td>
                                <td><?= e($v['company_name']) ?></td>
                                <td><?= e($v['contact_person'] ?? '-') ?></td>
                                <td><?= e($v['email'] ?? '-') ?></td>
                                <td><?= e($v['phone'] ?? '-') ?></td>
                                <td><?= e($v['category'] ?? '-') ?></td>
                                <td>
                                    <?php $asc = match($v['approval_status'] ?? '') {
                                        'approved' => 'badge-fx-success', 'pending' => 'badge-fx-warning',
                                        'rejected' => 'badge-fx-danger', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $asc ?>"><?= ucfirst($v['approval_status'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <span class="badge-fx <?= $v['status'] === 'active' ? 'badge-fx-success' : 'badge-fx-secondary' ?>">
                                        <?= ucfirst($v['status'] ?? '') ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="<?= base_url('vendors/view/' . $v['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-truck"></i>
                                <h5>No vendors found</h5>
                                <a href="<?= base_url('vendors/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Vendor</a>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
