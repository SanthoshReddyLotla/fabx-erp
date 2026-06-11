<?php
/**
 * FabX ERP - Vendor Details View
 */

$overallRating = (float)($vendor['overall_rating'] ?? 0.00);
$ratingClass = match(true) {
    $overallRating >= 4.0 => 'text-success',
    $overallRating >= 3.0 => 'text-info',
    $overallRating >= 2.0 => 'text-warning',
    default => 'text-danger'
};
?>

<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded bg-primary bg-opacity-10 border border-primary border-opacity-25 d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 50px; height: 50px; font-size: 1.5rem;">
            <?= strtoupper(substr($vendor['company_name'] ?? 'V', 0, 1)) ?>
        </div>
        <div>
            <h1 class="page-title mb-0"><?= e($vendor['company_name']) ?></h1>
            <span class="badge bg-dark border border-secondary text-muted font-monospace small px-2 py-1 mt-1">
                REF: <?= e($vendor['vendor_code']) ?>
            </span>
        </div>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('vendors') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Directory</a>
    </div>
</div>

<!-- Performance Metrics Deck -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="text-muted small mb-1">Quality Assessment</div>
            <h3 class="fw-bold text-success mb-0"><?= number_format($vendor['quality_score'] ?? 0, 2) ?> / 5.0</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="text-muted small mb-1">Delivery Reliability</div>
            <h3 class="fw-bold text-info mb-0"><?= number_format($vendor['delivery_score'] ?? 0, 2) ?> / 5.0</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="text-muted small mb-1">Service & Support</div>
            <h3 class="fw-bold text-warning mb-0"><?= number_format($vendor['service_score'] ?? 0, 2) ?> / 5.0</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="text-muted small mb-1">Overall QMS Rating</div>
            <h3 class="fw-bold <?= $ratingClass ?> mb-0"><?= number_format($overallRating, 2) ?> / 5.0</h3>
        </div>
    </div>
</div>

<!-- Tabbed Panel Interface -->
<div class="fx-card">
    <div class="fx-card-header p-0 border-bottom border-secondary border-opacity-25">
        <ul class="nav nav-tabs border-0 px-3" id="vendorTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-white py-3 px-4 active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profileTabContent" type="button" role="tab" aria-controls="profileTabContent" aria-selected="true">
                    <i class="bi bi-building me-1"></i> Profile Credentials
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-white py-3 px-4" id="orders-tab" data-bs-toggle="tab" data-bs-target="#ordersTabContent" type="button" role="tab" aria-controls="ordersTabContent" aria-selected="false">
                    <i class="bi bi-cart me-1"></i> Purchase Orders (<?= count($purchase_orders) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-white py-3 px-4" id="evaluations-tab" data-bs-toggle="tab" data-bs-target="#evaluationsTabContent" type="button" role="tab" aria-controls="evaluationsTabContent" aria-selected="false">
                    <i class="bi bi-shield-check me-1"></i> QMS Evaluations (<?= count($evaluations) ?>)
                </button>
            </li>
        </ul>
    </div>
    
    <div class="fx-card-body p-4">
        <div class="tab-content" id="vendorTabsContent">
            <!-- Tab 1: Profile Credentials -->
            <div class="tab-pane fade show active" id="profileTabContent" role="tabpanel" aria-labelledby="profile-tab">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-white border-bottom border-secondary border-opacity-10 pb-2 mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Demographics</h6>
                        <table class="table table-borderless text-white small mb-0">
                            <tr><td class="text-muted ps-0" style="width: 140px;">Contact Person:</td><td><strong><?= e($vendor['contact_person'] ?? '-') ?></strong></td></tr>
                            <tr><td class="text-muted ps-0">Email Address:</td><td><?= e($vendor['email'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Phone Coordinates:</td><td><?= e($vendor['phone'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Alternative Contact:</td><td><?= e($vendor['alt_phone'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Supplier Type:</td><td class="text-capitalize"><?= e($vendor['vendor_type'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Material category:</td><td><?= e($vendor['category'] ?? '-') ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-white border-bottom border-secondary border-opacity-10 pb-2 mb-3"><i class="bi bi-shield-check text-primary me-2"></i>Compliance & Banking</h6>
                        <table class="table table-borderless text-white small mb-0">
                            <tr><td class="text-muted ps-0" style="width: 140px;">GSTIN Code:</td><td class="font-monospace text-uppercase"><?= e($vendor['gstin'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">PAN Reference:</td><td class="font-monospace text-uppercase"><?= e($vendor['pan'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Bank Name:</td><td><?= e($vendor['bank_name'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Account Number:</td><td class="font-monospace"><?= e($vendor['bank_account_no'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Bank IFSC:</td><td class="font-monospace text-uppercase"><?= e($vendor['bank_ifsc'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted ps-0">Approval Status:</td><td><?= status_badge($vendor['approval_status'] ?? 'pending') ?></td></tr>
                        </table>
                    </div>
                    <div class="col-12">
                        <h6 class="text-white border-bottom border-secondary border-opacity-10 pb-2 mb-3"><i class="bi bi-geo-alt text-primary me-2"></i>Address Coordinates</h6>
                        <div class="p-3 bg-dark border border-secondary border-opacity-10 rounded text-light-heading small" style="line-height: 1.6;">
                            <?= nl2br(e($vendor['address'] ?? '')) ?><br>
                            <strong><?= e($vendor['city'] ?? '') ?>, <?= e($vendor['state'] ?? '') ?> - <?= e($vendor['pincode'] ?? '') ?></strong><br>
                            <span class="text-muted"><?= e($vendor['country'] ?? 'India') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab 2: Purchase Orders -->
            <div class="tab-pane fade" id="ordersTabContent" role="tabpanel" aria-labelledby="orders-tab">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Order Date</th>
                                <th>Delivery Date</th>
                                <th class="text-end">Base Subtotal</th>
                                <th class="text-end">GST Amount</th>
                                <th class="text-end">Grand Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($purchase_orders)): ?>
                                <?php foreach ($purchase_orders as $po): ?>
                                    <tr>
                                        <td><strong><?= e($po['po_no']) ?></strong></td>
                                        <td><?= format_date($po['po_date']) ?></td>
                                        <td><?= format_date($po['delivery_date']) ?></td>
                                        <td class="text-end text-muted small"><?= format_currency($po['subtotal'] ?? 0) ?></td>
                                        <td class="text-end text-muted small"><?= format_currency($po['gst_amount'] ?? 0) ?></td>
                                        <td class="text-end fw-bold text-light-heading"><?= format_currency($po['total_amount'] ?? 0) ?></td>
                                        <td><?= status_badge($po['status'] ?? 'draft') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state py-4 text-center text-muted">
                                            <i class="bi bi-cart display-6 mb-2 d-block opacity-25"></i>
                                            <span>No historical Purchase Orders logged for this vendor supplier.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tab 3: QMS Evaluations -->
            <div class="tab-pane fade" id="evaluationsTabContent" role="tabpanel" aria-labelledby="evaluations-tab">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Evaluation Date</th>
                                <th>Period</th>
                                <th>Quality Score</th>
                                <th>Delivery Score</th>
                                <th>Cost Score</th>
                                <th>Service Score</th>
                                <th>Overall Score</th>
                                <th>Evaluated By</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($evaluations)): ?>
                                <?php foreach ($evaluations as $eval): ?>
                                    <tr>
                                        <td><strong><?= format_date($eval['evaluation_date']) ?></strong></td>
                                        <td><?= e($eval['evaluation_period'] ?? '-') ?></td>
                                        <td class="text-success fw-bold"><?= number_format($eval['quality_score'] ?? 0, 1) ?></td>
                                        <td class="text-info fw-bold"><?= number_format($eval['delivery_score'] ?? 0, 1) ?></td>
                                        <td class="text-white fw-bold"><?= number_format($eval['cost_score'] ?? 0, 1) ?></td>
                                        <td class="text-warning fw-bold"><?= number_format($eval['service_score'] ?? 0, 1) ?></td>
                                        <td><span class="badge bg-secondary"><?= number_format($eval['overall_score'] ?? 0, 1) ?></span></td>
                                        <td><?= e($eval['evaluated_by_name'] ?? '-') ?></td>
                                        <td class="small text-muted"><?= e($eval['remarks'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state py-4 text-center text-muted">
                                            <i class="bi bi-shield-check display-6 mb-2 d-block opacity-25"></i>
                                            <span>No QMS performance audits or scoring records found for this vendor.</span>
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
