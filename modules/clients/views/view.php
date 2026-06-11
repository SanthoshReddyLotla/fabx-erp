<?php
/**
 * FabX ERP - Client Profile View
 */

$outstanding = 0.00;
foreach ($invoices as $inv) {
    if (in_array($inv['status'] ?? '', ['sent', 'partial', 'overdue'])) {
        $outstanding += ((float)($inv['grand_total'] ?? 0) - (float)($inv['paid_amount'] ?? 0));
    }
}
?>

<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded bg-primary bg-opacity-10 border border-primary border-opacity-25 d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 50px; height: 50px; font-size: 1.5rem;">
            <?= strtoupper(substr($client['company_name'] ?? 'C', 0, 1)) ?>
        </div>
        <div>
            <h1 class="page-title mb-0"><?= e($client['company_name']) ?></h1>
            <span class="badge bg-dark border border-secondary text-muted font-monospace small px-2 py-1 mt-1">
                REF: <?= e($client['client_code']) ?>
            </span>
        </div>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Directory</a>
    </div>
</div>

<!-- Vital Financial & Credit Metrics Deck -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="fx-card text-center p-3">
            <div class="text-muted small mb-1">Dynamic Total Outstanding Receivable</div>
            <h3 class="fw-bold <?= $outstanding > 0 ? 'text-danger' : 'text-success' ?> mb-0">
                <?= format_currency($outstanding) ?>
            </h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="fx-card text-center p-3">
            <div class="text-muted small mb-1">Approved Credit Limit</div>
            <h3 class="fw-bold text-white mb-0">
                <?= format_currency($client['credit_limit'] ?? 0) ?>
            </h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="fx-card text-center p-3">
            <div class="text-muted small mb-1">Authorized Credit Days</div>
            <h3 class="fw-bold text-warning mb-0">
                <?= (int)($client['credit_days'] ?? 30) ?> Days
            </h3>
        </div>
    </div>
</div>

<!-- Tabbed Panel Interface Workspace -->
<div class="fx-card">
    <div class="fx-card-header p-0 border-bottom border-secondary border-opacity-25">
        <ul class="nav nav-tabs border-0 px-3" id="clientTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-white py-3 px-4 active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profileTabContent" type="button" role="tab" aria-controls="profileTabContent" aria-selected="true">
                    <i class="bi bi-building me-1"></i> Profile Credentials
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-white py-3 px-4" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contactsTabContent" type="button" role="tab" aria-controls="contactsTabContent" aria-selected="false">
                    <i class="bi bi-people me-1"></i> Contacts List (<?= count($contacts) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-white py-3 px-4" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projectsTabContent" type="button" role="tab" aria-controls="projectsTabContent" aria-selected="false">
                    <i class="bi bi-gear me-1"></i> Projects History (<?= count($projects) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-white py-3 px-4" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoicesTabContent" type="button" role="tab" aria-controls="invoicesTabContent" aria-selected="false">
                    <i class="bi bi-receipt me-1"></i> Invoices Log (<?= count($invoices) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-white py-3 px-4" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#ticketsTabContent" type="button" role="tab" aria-controls="ticketsTabContent" aria-selected="false">
                    <i class="bi bi-ticket-perforated me-1"></i> Operations Tickets (<?= count($tickets) ?>)
                </button>
            </li>
        </ul>
    </div>
    
    <div class="fx-card-body p-4">
        <div class="tab-content" id="clientTabsContent">
            <!-- 1. Profile Credentials Tab -->
            <div class="tab-pane fade show active" id="profileTabContent" role="tabpanel" aria-labelledby="profile-tab">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-white border-bottom border-secondary border-opacity-10 pb-2 mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Demographics</h6>
                        <table class="table table-borderless text-white small mb-0">
                            <tr><td class="text-muted ps-0" style="width: 140px;">Contact Person:</td><td><strong><?= e($client['contact_person'] ?? '-') ?></strong></td></tr>
                            <tr><td class="text-muted ps-0">Email Address:</td><td><?= e($client['email'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Phone Coordinates:</td><td><?= e($client['phone'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Alternate Contact:</td><td><?= e($client['alt_phone'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Website URL:</td><td><?= $client['website'] ? '<a href="' . e($client['website']) . '" target="_blank" class="text-info">' . e($client['website']) . ' <i class="bi bi-box-arrow-up-right small"></i></a>' : '-' ?></td></tr>
                            <tr><td class="text-muted ps-0">Industrial Domain:</td><td><?= e(ucfirst($client['industry'] ?? '-')) ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-white border-bottom border-secondary border-opacity-10 pb-2 mb-3"><i class="bi bi-shield-check text-primary me-2"></i>Statutory & Credit Compliance</h6>
                        <table class="table table-borderless text-white small mb-0">
                            <tr><td class="text-muted ps-0" style="width: 140px;">GSTIN Code:</td><td class="font-monospace text-uppercase"><?= e($client['gstin'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">PAN Reference:</td><td class="font-monospace text-uppercase"><?= e($client['pan'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Client Class:</td><td class="text-capitalize"><?= e($client['client_type'] ?? 'direct') ?> Account</td></tr>
                            <tr><td class="text-muted ps-0">Payment Protocol:</td><td><?= e($client['payment_terms'] ?? 'Standard terms') ?></td></tr>
                            <tr><td class="text-muted ps-0">Profile Status:</td><td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 text-uppercase" style="font-size:0.65rem;">
                                    <?= e($client['status'] ?? 'active') ?>
                                </span>
                            </td></tr>
                        </table>
                    </div>
                    <div class="col-12">
                        <h6 class="text-white border-bottom border-secondary border-opacity-10 pb-2 mb-3"><i class="bi bi-geo-alt text-primary me-2"></i>Address Coordinates</h6>
                        <div class="p-3 bg-dark border border-secondary border-opacity-10 rounded text-light-heading small" style="line-height: 1.6;">
                            <?= nl2br(e($client['address'] ?? '')) ?><br>
                            <strong><?= e($client['city'] ?? '') ?>, <?= e($client['state'] ?? '') ?> - <?= e($client['pincode'] ?? '') ?></strong><br>
                            <span class="text-muted"><?= e($client['country'] ?? 'India') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 2. Contacts List Tab -->
            <div class="tab-pane fade" id="contactsTabContent" role="tabpanel" aria-labelledby="contacts-tab">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Contact Name</th>
                                <th>Designation</th>
                                <th>Email ID</th>
                                <th>Phone Number</th>
                                <th>Alternative Coordinates</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($contacts)): ?>
                                <?php foreach ($contacts as $c): ?>
                                    <tr>
                                        <td><div class="fw-bold text-light-heading"><?= e($c['contact_name']) ?></div></td>
                                        <td><?= e($c['designation'] ?? '-') ?></td>
                                        <td><?= e($c['email'] ?? '-') ?></td>
                                        <td><?= e($c['phone'] ?? '-') ?></td>
                                        <td><?= e($c['mobile'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state py-4 text-center text-muted">
                                            <i class="bi bi-people display-6 mb-2 d-block opacity-25"></i>
                                            <span>No Primary or Secondary contacts mapped for this client corporate profile.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- 3. Projects History Tab -->
            <div class="tab-pane fade" id="projectsTabContent" role="tabpanel" aria-labelledby="projects-tab">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Project Ref</th>
                                <th>Project Name</th>
                                <th>Stage Category</th>
                                <th>Progress / Metrics</th>
                                <th>Planned Value</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $proj): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-dark border border-secondary font-monospace py-2 px-3 small">
                                                <?= e($proj['project_code']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-light-heading"><?= e($proj['project_name']) ?></div>
                                        </td>
                                        <td>
                                            <span class="text-uppercase text-info small fw-semibold"><?= e(str_replace('_', ' ', $proj['stage'] ?? 'design')) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-100 bg-dark" style="height: 6px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= (int)($proj['progress'] ?? 0) ?>%"></div>
                                                </div>
                                                <span class="small fw-bold font-monospace"><?= (int)($proj['progress'] ?? 0) ?>%</span>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-light-heading">
                                            <?= format_currency($proj['planned_value'] ?? 0) ?>
                                        </td>
                                        <td>
                                            <div class="small"><?= format_date($proj['end_date'] ?? null) ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state py-4 text-center text-muted">
                                            <i class="bi bi-gear display-6 mb-2 d-block opacity-25"></i>
                                            <span>No historical projects registered for this client account.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- 4. Invoices Log Tab -->
            <div class="tab-pane fade" id="invoicesTabContent" role="tabpanel" aria-labelledby="invoices-tab">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Base Subtotal</th>
                                <th class="text-end">Grand Total</th>
                                <th class="text-end">Paid Amount</th>
                                <th class="text-end">Balance Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($invoices)): ?>
                                <?php foreach ($invoices as $inv): 
                                    $statusColor = match($inv['status'] ?? '') {
                                        'paid' => 'badge-fx-success',
                                        'partial' => 'badge-fx-warning',
                                        'sent' => 'badge-fx-info',
                                        'overdue' => 'badge-fx-danger',
                                        default => 'badge-fx-secondary'
                                    };
                                ?>
                                    <tr>
                                        <td><strong><?= e($inv['invoice_no']) ?></strong></td>
                                        <td><?= format_date($inv['invoice_date']) ?></td>
                                        <td><?= format_date($inv['due_date']) ?></td>
                                        <td class="text-end text-muted small"><?= format_currency($inv['subtotal'] ?? 0) ?></td>
                                        <td class="text-end fw-bold text-light-heading"><?= format_currency($inv['grand_total'] ?? 0) ?></td>
                                        <td class="text-end text-success"><?= format_currency($inv['paid_amount'] ?? 0) ?></td>
                                        <td class="text-end fw-bold text-danger"><?= format_currency(((float)($inv['grand_total'] ?? 0) - (float)($inv['paid_amount'] ?? 0))) ?></td>
                                        <td>
                                            <span class="badge-fx <?= $statusColor ?>">
                                                <?= ucfirst(str_replace('_', ' ', $inv['status'] ?? '')) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state py-4 text-center text-muted">
                                            <i class="bi bi-receipt display-6 mb-2 d-block opacity-25"></i>
                                            <span>No historical invoices logged for this client account.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- 5. Support Tickets Tab -->
            <div class="tab-pane fade" id="ticketsTabContent" role="tabpanel" aria-labelledby="tickets-tab">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ticket No</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Logged On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tickets)): ?>
                                <?php foreach ($tickets as $t): 
                                    $prioClass = match($t['priority'] ?? 'medium') {
                                        'critical' => 'bg-danger text-white',
                                        'high' => 'bg-warning text-dark',
                                        'low' => 'bg-secondary text-white',
                                        default => 'bg-info text-dark'
                                    };
                                    $tStatusColor = match($t['status'] ?? 'open') {
                                        'resolved', 'closed' => 'badge-fx-success',
                                        'in_progress' => 'badge-fx-warning',
                                        default => 'badge-fx-primary'
                                    };
                                ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-dark border border-secondary font-monospace py-2 px-3 small">
                                                <?= e($t['ticket_no']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-light-heading"><?= e($t['subject']) ?></div>
                                        </td>
                                        <td class="text-capitalize small"><?= e($t['category'] ?? 'general') ?></td>
                                        <td>
                                            <span class="badge <?= $prioClass ?> text-uppercase" style="font-size:0.6rem; padding: 2px 6px;">
                                                <?= e($t['priority'] ?? 'medium') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge-fx <?= $tStatusColor ?>">
                                                <?= ucfirst(str_replace('_', ' ', $t['status'] ?? '')) ?>
                                            </span>
                                        </td>
                                        <td class="small"><?= format_date($t['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state py-4 text-center text-muted">
                                            <i class="bi bi-ticket-perforated display-6 mb-2 d-block opacity-25"></i>
                                            <span>No operational issues or active support tickets logged for this client.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    background: transparent;
    border-radius: 0;
    border-bottom: 2px solid transparent !important;
    transition: all 0.2s ease-in-out;
}
.nav-tabs .nav-link:hover {
    border-bottom-color: rgba(255,255,255,0.1) !important;
}
.nav-tabs .nav-link.active {
    background: rgba(255,255,255,0.03) !important;
    border-bottom: 2px solid var(--primary-color) !important;
    font-weight: bold;
}
</style>
