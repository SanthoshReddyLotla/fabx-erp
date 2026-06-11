<?php
/**
 * FabX ERP - Vendor Payments List View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bank text-primary"></i> Vendor Payments</h1>
    <div class="page-actions">
        <button type="button" class="btn btn-fx btn-fx-primary" data-bs-toggle="modal" data-bs-target="#recordVendorPaymentModal">
            <i class="bi bi-plus-lg"></i> Record Vendor Payment
        </button>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-receipt-cutoff"></i> Supplier Procurement Outflows & Vendor Payments Ledger</h5>
        <span class="badge bg-dark border border-secondary text-muted">Page <?= $pagination['page'] ?? 1 ?></span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Payment Ref</th>
                        <th>Vendor Name</th>
                        <th>Allocated PO Ref</th>
                        <th class="text-end">Base Amount</th>
                        <th class="text-end">TDS Deducted</th>
                        <th class="text-end">Net Paid Out</th>
                        <th>Payment Mode</th>
                        <th>Txn Ref / Date</th>
                        <th>Memo Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $p): 
                            $modeBadge = match($p['payment_mode'] ?? '') {
                                'cash' => 'bg-success text-white',
                                'cheque' => 'bg-warning text-dark',
                                'upi' => 'bg-info text-dark',
                                'neft', 'rtgs' => 'bg-primary text-white',
                                default => 'bg-secondary text-light'
                            };
                        ?>
                            <tr>
                                <td>
                                    <span class="badge bg-dark border border-secondary font-monospace py-2 px-3">
                                        <?= e($p['payment_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($p['vendor_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <?php if ($p['po_no']): ?>
                                        <span class="text-info fw-semibold"><i class="bi bi-file-earmark-zip me-1"></i><?= e($p['po_no']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">Direct Payment</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-muted small">
                                    <?= format_currency($p['amount'] ?? 0) ?>
                                </td>
                                <td class="text-end text-danger small">
                                    <?= format_currency($p['tds_amount'] ?? 0) ?>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    <?= format_currency($p['net_amount'] ?? ($p['amount'] ?? 0)) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $modeBadge ?> text-uppercase px-2 py-1" style="font-size:0.65rem; letter-spacing: 0.3px;">
                                        <?= e($p['payment_mode'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-light-heading"><?= e($p['transaction_ref'] ?? 'Direct Outflow') ?></div>
                                    <small class="text-muted d-block" style="font-size:0.7rem;"><i class="bi bi-calendar-event me-1"></i><?= format_date($p['payment_date']) ?></small>
                                </td>
                                <td class="small" style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= e($p['remarks'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state py-5">
                                    <i class="bi bi-bank display-4 mb-3 d-block text-muted"></i>
                                    <h5>No Vendor Payments Logged</h5>
                                    <p>Start recording payments made to vendors / subcontractors against purchase orders and procurement bills.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
        <div class="fx-card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> supplier payments</small>
                <nav><div class="pagination-fx">
                    <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['page'] - 1 ?>">&laquo;</a><?php endif; ?>
                    <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['page'] + 1 ?>">&raquo;</a><?php endif; ?>
                </div></nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Record Vendor Payment Modal -->
<div class="modal fade" id="recordVendorPaymentModal" tabindex="-1" aria-labelledby="recordVendorPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="recordVendorPaymentModalLabel"><i class="bi bi-bank2 text-primary"></i> Log Supplier Payment Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('accounts/vendor-payments') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <div class="modal-body text-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Vendor / Supplier <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="vendor_id" required>
                                <option value="">Select Vendor</option>
                                <?php foreach ($vendors as $v): ?>
                                    <option value="<?= $v['id'] ?>"><?= e($v['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select the vendor.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Allocated Purchase Order</label>
                            <select class="form-select bg-dark border-secondary text-white" name="po_id">
                                <option value="">Direct Payment (No PO)</option>
                                <?php foreach ($purchase_orders as $po): ?>
                                    <option value="<?= $po['id'] ?>"><?= e($po['po_no']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Payment Issued (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="amount" min="0.01" step="0.01" required>
                            <div class="invalid-feedback">Amount must be greater than zero.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">TDS Deducted (₹)</label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="tds_amount" value="0.00" min="0" step="0.01">
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Payment Mode <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="payment_mode" required>
                                <option value="neft">NEFT Transfer</option>
                                <option value="rtgs">RTGS Transfer</option>
                                <option value="upi">UPI Transfer</option>
                                <option value="cheque">Cheque Disbursement</option>
                                <option value="cash">Cash Account</option>
                                <option value="other">Other Mode</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Transaction Ref / Cheque No</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="transaction_ref" placeholder="Txn Ref No">
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="payment_date" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Remarks / Payment Memo</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="remarks" rows="3" placeholder="Brief payment notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-bank"></i> Save Payment Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
