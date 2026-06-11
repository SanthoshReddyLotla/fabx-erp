<?php
/**
 * FabX ERP - Support Tickets List View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-ticket-detailed text-primary"></i> Support & Triage Tickets</h1>
    <div class="page-actions">
        <button type="button" class="btn btn-fx btn-fx-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
            <i class="bi bi-plus-lg"></i> Log Service Request
        </button>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-header py-3"><h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Support Ticket Lifecycle Registry</h5></div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Ticket No</th>
                        <th>Client Name</th>
                        <th>Project / Item Reference</th>
                        <th>Subject & Narrative</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Logged On</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $t): 
                            $prioBadge = match($t['priority'] ?? 'medium') {
                                'critical' => 'bg-danger text-white',
                                'high' => 'bg-warning text-dark',
                                'low' => 'bg-secondary text-white',
                                default => 'bg-info text-dark'
                            };
                            $statusColor = match($t['status'] ?? 'open') {
                                'resolved', 'closed' => 'badge-fx-success',
                                'in_progress' => 'badge-fx-warning',
                                default => 'badge-fx-primary'
                            };
                        ?>
                            <tr>
                                <td>
                                    <span class="badge bg-dark border border-secondary font-monospace py-2 px-3">
                                        <?= e($t['ticket_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($t['client_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <?php if ($t['project_name']): ?>
                                        <span class="text-info fw-semibold small"><i class="bi bi-gear-wide-connected me-1"></i><?= e($t['project_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">General Operational</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-light-heading"><?= e($t['subject']) ?></div>
                                    <small class="text-muted d-block" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= e($t['description'] ?? '-') ?></small>
                                    <?php if ($t['resolution']): ?>
                                        <div class="mt-1.5 p-2 bg-dark rounded border border-success border-opacity-10 small text-success">
                                            <strong><i class="bi bi-check-circle-fill me-1"></i>Resolution:</strong> <?= e($t['resolution']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-dark border border-secondary text-uppercase px-2 py-1" style="font-size:0.65rem; letter-spacing: 0.3px;">
                                        <?= e($t['category'] ?? 'general') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $prioBadge ?> text-uppercase" style="font-size:0.6rem; letter-spacing: 0.3px; padding: 3px 6px;">
                                        <?= e($t['priority'] ?? 'medium') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-fx <?= $statusColor ?> text-uppercase" style="font-size:0.65rem;">
                                        <?= e(str_replace('_', ' ', $t['status'] ?? '')) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><i class="bi bi-calendar-event me-1"></i><?= format_date($t['created_at']) ?></small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-fx btn-fx-primary" onclick="openUpdateTicketModal(<?= $t['id'] ?>, '<?= e($t['status']) ?>', '<?= e($t['resolution'] ?? '') ?>')">
                                        <i class="bi bi-pencil-square"></i> Triage
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state py-5">
                                    <i class="bi bi-ticket-detailed display-4 mb-3 d-block text-muted"></i>
                                    <h5>No Support Tickets Registered</h5>
                                    <p>Log customer service requests, workshop operational issues, quality complaints, or logistics discrepancies to track dynamic resolutions.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Log Service Request Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="createTicketModalLabel"><i class="bi bi-ticket-detailed text-primary"></i> Log Customer Support Ticket</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('clients/tickets') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create">
                <div class="modal-body text-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Select Client <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="client_id" required>
                                <option value="">Select Client Account</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= e($c['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a client account.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Associated Project</label>
                            <select class="form-select bg-dark border-secondary text-white" name="project_id">
                                <option value="">General Support (No Project)</option>
                                <?php foreach ($projects as $proj): ?>
                                    <option value="<?= $proj['id'] ?>"><?= e($proj['project_code']) ?> - <?= e($proj['project_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Service Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="subject" placeholder="e.g. Quality check discrepancy on structural beam" required>
                            <div class="invalid-feedback">Please enter the ticket subject.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Service Classification Category</label>
                            <select class="form-select bg-dark border-secondary text-white" name="category">
                                <option value="general">General Query</option>
                                <option value="technical">Technical Support</option>
                                <option value="billing">Billing & Finance</option>
                                <option value="quality">Quality & Inspection</option>
                                <option value="delivery">Logistics & Delivery</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Service Urgency Priority</label>
                            <select class="form-select bg-dark border-secondary text-white" name="priority">
                                <option value="low">Low Urgency</option>
                                <option value="medium" selected>Medium Urgency</option>
                                <option value="high">High Urgency</option>
                                <option value="critical">Critical Outage</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Detailed Issue Narrative</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="description" rows="4" placeholder="Detail the technical or logistical issue encountered..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Log Support Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ticket Triage Resolution Modal -->
<div class="modal fade" id="updateTicketModal" tabindex="-1" aria-labelledby="updateTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="updateTicketModalLabel"><i class="bi bi-pencil-square text-primary"></i> Support Ticket Triage</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('clients/tickets') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="ticket_id" id="triageTicketId">
                <div class="modal-body text-white">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Lifecycle State Status <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="status" id="triageStatus" required>
                                <option value="open">Open / Unresolved</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved / Mapped</option>
                                <option value="closed">Closed / Finalized</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Engineering Resolution Narrative</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="resolution" id="triageResolution" rows="4" placeholder="Detail the resolution narrative or troubleshooting steps performed..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Save Triage Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUpdateTicketModal(id, status, resolution) {
    document.getElementById('triageTicketId').value = id;
    document.getElementById('triageStatus').value = status;
    document.getElementById('triageResolution').value = resolution;
    
    const modal = new bootstrap.Modal(document.getElementById('updateTicketModal'));
    modal.show();
}
</script>
