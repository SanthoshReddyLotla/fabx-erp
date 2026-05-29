<?php
/**
 * QMS - NCR List View
 */
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-exclamation-triangle text-danger"></i>
        Non-Conformance Reports (NCR)
    </h1>
    <div class="page-actions">
        <a href="<?= base_url('qms/ncr/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> Raise NCR
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar">
    <div class="flex-grow-1">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search NCRs...">
        </div>
    </div>
    <div>
        <select class="form-select" name="status">
            <option value="">All Status</option>
            <option value="open">Open</option>
            <option value="in_progress">In Progress</option>
            <option value="pending_verification">Pending Verification</option>
            <option value="closed">Closed</option>
        </select>
    </div>
    <div>
        <select class="form-select" name="severity">
            <option value="">All Severity</option>
            <option value="minor">Minor</option>
            <option value="major">Major</option>
            <option value="critical">Critical</option>
        </select>
    </div>
    <button class="btn btn-outline-secondary" onclick="exportTableToCSV('ncrTable', 'ncr_report_<?= date('Y-m-d') ?>.csv')">
        <i class="bi bi-download"></i> Export
    </button>
</div>

<!-- Risk Matrix Reference -->
<div class="fx-card mb-4">
    <div class="fx-card-header">
        <h5 class="fx-card-title"><i class="bi bi-grid-3x3"></i> Severity Classification</h5>
    </div>
    <div class="fx-card-body">
        <div class="row text-center g-2">
            <div class="col-md-4">
                <div class="p-3 rounded" style="background:rgba(39,174,96,0.08)">
                    <span class="badge bg-success mb-2">Minor</span>
                    <p class="small text-muted mb-0">Non-conformance that does not affect product function or safety. Can be corrected immediately.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background:rgba(243,156,18,0.08)">
                    <span class="badge bg-warning mb-2">Major</span>
                    <p class="small text-muted mb-0">Significant non-conformance affecting product quality or process effectiveness. Requires corrective action.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background:rgba(231,76,60,0.08)">
                    <span class="badge bg-danger mb-2">Critical</span>
                    <p class="small text-muted mb-0">Severe non-conformance affecting safety, regulatory compliance, or customer satisfaction. Immediate action required.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NCR Table -->
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="ncrTable">
                <thead>
                    <tr>
                        <th>NCR No</th>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Description</th>
                        <th>Severity</th>
                        <th>Category</th>
                        <th>Department</th>
                        <th>Reported By</th>
                        <th>Target Date</th>
                        <th>Status</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ncrs)): ?>
                        <?php foreach ($ncrs as $ncr): ?>
                            <tr>
                                <td><strong><?= e($ncr['ncr_no']) ?></strong></td>
                                <td><?= format_date($ncr['ncr_date']) ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $ncr['source'])) ?></td>
                                <td><?= e(truncate($ncr['description'], 50)) ?></td>
                                <td><?= priority_badge($ncr['severity']) ?></td>
                                <td><?= ucfirst($ncr['category']) ?></td>
                                <td><?= e($ncr['department_name'] ?? '-') ?></td>
                                <td><?= e($ncr['reported_by_name'] ?? '-') ?></td>
                                <td><?= format_date($ncr['target_date']) ?></td>
                                <td><?= status_badge($ncr['status']) ?></td>
                                <td class="actions">
                                    <a href="<?= base_url('qms/ncr/view/' . $ncr['id']) ?>" class="btn btn-sm btn-light" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-light" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-light" title="CAPA" data-bs-toggle="tooltip">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-light" title="Print" onclick="window.print()">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">
                                <div class="empty-state">
                                    <i class="bi bi-shield-check"></i>
                                    <h5>No open NCRs</h5>
                                    <p class="mb-3">All non-conformances are closed. Great job!</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="fx-card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> NCRs</small>
                <div class="pagination-fx">
                    <?php if ($pagination['has_prev']): ?>
                        <a href="?page=<?= $pagination['page'] - 1 ?>">&laquo;</a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($pagination['has_next']): ?>
                        <a href="?page=<?= $pagination['page'] + 1 ?>">&raquo;</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ISO Template Download Section -->
<div class="fx-card mt-4">
    <div class="fx-card-header">
        <h5 class="fx-card-title"><i class="bi bi-file-earmark-arrow-down"></i> ISO 9001 Templates</h5>
    </div>
    <div class="fx-card-body">
        <div class="row g-3">
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="d-flex align-items-center gap-2 p-2 rounded hover-bg" style="text-decoration:none">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="small">NCR Form Template</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="d-flex align-items-center gap-2 p-2 rounded hover-bg" style="text-decoration:none">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="small">CAPA Form</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="d-flex align-items-center gap-2 p-2 rounded hover-bg" style="text-decoration:none">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="small">Audit Checklist</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="d-flex align-items-center gap-2 p-2 rounded hover-bg" style="text-decoration:none">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="small">Calibration Record</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="d-flex align-items-center gap-2 p-2 rounded hover-bg" style="text-decoration:none">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="small">Inspection Report</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="d-flex align-items-center gap-2 p-2 rounded hover-bg" style="text-decoration:none">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="small">Welding Inspection</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="d-flex align-items-center gap-2 p-2 rounded hover-bg" style="text-decoration:none">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="small">Vendor Evaluation</span>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="d-flex align-items-center gap-2 p-2 rounded hover-bg" style="text-decoration:none">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    <span class="small">Risk Assessment Matrix</span>
                </a>
            </div>
        </div>
    </div>
</div>
