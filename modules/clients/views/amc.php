<?php
/**
 * FabX ERP - Annual Maintenance Contracts (AMC) List View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-shield-check text-primary"></i> AMC SLA Operations</h1>
    <div class="page-actions">
        <button type="button" class="btn btn-fx btn-fx-primary" data-bs-toggle="modal" data-bs-target="#createAmcModal">
            <i class="bi bi-plus-lg"></i> Register AMC Contract
        </button>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-header py-3"><h5 class="mb-0"><i class="bi bi-shield-exclamation"></i> Annual Maintenance Service & SLA Tracker</h5></div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Contract No</th>
                        <th>Client / Partner Name</th>
                        <th>Allocated Project</th>
                        <th class="text-end">Contract Value (₹)</th>
                        <th>Visit Schedule</th>
                        <th>Performance Progress</th>
                        <th>Duration Scope</th>
                        <th>Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($contracts)): ?>
                        <?php foreach ($contracts as $c): 
                            $statusBadge = match($c['status'] ?? 'active') {
                                'active' => 'badge-fx-success',
                                'renewed' => 'badge-fx-info',
                                'expired' => 'badge-fx-secondary',
                                'terminated' => 'badge-fx-danger',
                                default => 'badge-fx-secondary'
                            };
                            
                            $totalVisits = (int)($c['total_visits'] ?? 0);
                            $completedVisits = (int)($c['completed_visits'] ?? 0);
                            $progressPct = $totalVisits > 0 ? min(100, round(($completedVisits / $totalVisits) * 100)) : 0;
                        ?>
                            <tr>
                                <td>
                                    <span class="badge bg-dark border border-secondary font-monospace py-2 px-3">
                                        <?= e($c['amc_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($c['client_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <?php if ($c['project_name']): ?>
                                        <span class="text-info fw-semibold small"><i class="bi bi-gear-wide-connected me-1"></i><?= e($c['project_code']) ?> - <?= e($c['project_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">Standalone Service</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-light-heading">
                                    <?= format_currency($c['value'] ?? 0) ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-light-heading text-capitalize"><?= e($c['visit_frequency']) ?></div>
                                    <small class="text-muted" style="font-size:0.7rem;">visits scheduled</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2" style="min-width: 120px;">
                                        <div class="progress w-100 bg-dark" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progressPct ?>%"></div>
                                        </div>
                                        <span class="small fw-bold font-monospace"><?= $completedVisits ?>/<?= $totalVisits ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-light-heading"><i class="bi bi-calendar-check me-1"></i><?= format_date($c['start_date']) ?></div>
                                    <small class="text-muted d-block" style="font-size:0.7rem;"><i class="bi bi-calendar-x me-1"></i>Expiry: <?= format_date($c['end_date']) ?></small>
                                </td>
                                <td>
                                    <span class="badge-fx <?= $statusBadge ?> text-uppercase" style="font-size:0.65rem;">
                                        <?= e($c['status'] ?? 'active') ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-fx btn-fx-primary" onclick="openUpdateAmcModal(<?= $c['id'] ?>, <?= $completedVisits ?>, '<?= e($c['status']) ?>')">
                                        <i class="bi bi-gear-wide"></i> Audit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state py-5">
                                    <i class="bi bi-shield-check display-4 mb-3 d-block text-muted"></i>
                                    <h5>No AMC Contracts Logged</h5>
                                    <p>Register Annual Maintenance Contracts here to track visit schedules, SLA durations, values, and milestone executions dynamically.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Register AMC Contract Modal -->
<div class="modal fade" id="createAmcModal" tabindex="-1" aria-labelledby="createAmcModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="createAmcModalLabel"><i class="bi bi-shield-plus text-primary"></i> Register Annual Maintenance Contract</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('clients/amc') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create">
                <div class="modal-body text-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Contract No (Optional)</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="amc_no" placeholder="Auto-generated if blank">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Select Client <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="client_id" required>
                                <option value="">Select Client Account</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= e($c['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a client.</div>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Associated Project</label>
                            <select class="form-select bg-dark border-secondary text-white" name="project_id">
                                <option value="">Standalone Equipment / General Service</option>
                                <?php foreach ($projects as $proj): ?>
                                    <option value="<?= $proj['id'] ?>"><?= e($proj['project_code']) ?> - <?= e($proj['project_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Contract Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="start_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Contract End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="end_date" value="<?= date('Y-m-d', strtotime('+1 year')) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">SLA Value (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="value" min="0" step="0.01" value="0.00" required>
                            <div class="invalid-feedback">SLA Value is required.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Service Visit Schedule Frequency</label>
                            <select class="form-select bg-dark border-secondary text-white" name="visit_frequency">
                                <option value="monthly">Monthly Cycle</option>
                                <option value="quarterly" selected>Quarterly Cycle</option>
                                <option value="half_yearly">Half Yearly Cycle</option>
                                <option value="yearly">Yearly Cycle</option>
                                <option value="on_call">On Call Support</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Contractual Scheduled Visits <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="total_visits" min="1" value="4" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Completed Visits <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="completed_visits" min="0" value="0" required>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">SLA Scope Details & Terms</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="description" rows="3" placeholder="Detail scheduled items, structural maintenance items, checklist items..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-shield-check"></i> Register AMC Contract</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- AMC Audit Performance Modal -->
<div class="modal fade" id="updateAmcModal" tabindex="-1" aria-labelledby="updateAmcModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="updateAmcModalLabel"><i class="bi bi-shield-check text-primary"></i> Audit AMC Contract Execution</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('clients/amc') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_visits">
                <input type="hidden" name="amc_id" id="auditAmcId">
                <div class="modal-body text-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Completed Service Visits <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="completed_visits" id="auditCompletedVisits" min="0" required>
                            <div class="invalid-feedback">Completed visits are required.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Contract Status <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="status" id="auditStatus" required>
                                <option value="active">Active SLA</option>
                                <option value="renewed">Renewed SLA</option>
                                <option value="expired">Expired Contract</option>
                                <option value="terminated">Terminated SLA</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Save Audit Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUpdateAmcModal(id, completed, status) {
    document.getElementById('auditAmcId').value = id;
    document.getElementById('auditCompletedVisits').value = completed;
    document.getElementById('auditStatus').value = status;
    
    const modal = new bootstrap.Modal(document.getElementById('updateAmcModal'));
    modal.show();
}
</script>
