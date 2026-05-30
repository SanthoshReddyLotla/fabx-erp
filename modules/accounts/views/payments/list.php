<?php
/**
 * FabX ERP - Payments Received List View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-cash-stack"></i> Payments Received</h1>
    <div class="page-actions">
        <button type="button" class="btn btn-fx btn-fx-primary" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
            <i class="bi bi-plus-lg"></i> Record Client Payment
        </button>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-lock"></i> Financial Collection & Double-Entry Ledger</h5>
        <span class="badge bg-dark border border-secondary text-muted">Page <?= $pagination['page'] ?? 1 ?></span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Client Name</th>
                        <th>Allocated Invoice No</th>
                        <th>Amount Received</th>
                        <th>TDS Amount</th>
                        <th>Net Amount</th>
                        <th>Payment Mode</th>
                        <th>Txn Ref / Date</th>
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
                                        <?= e($p['receipt_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($p['client_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <span class="text-info fw-bold"><i class="bi bi-receipt me-1"></i><?= e($p['invoice_no'] ?? 'General Advance') ?></span>
                                </td>
                                <td class="fw-bold text-success">
                                    <?= format_currency($p['amount'] ?? 0) ?>
                                </td>
                                <td class="text-danger small">
                                    <?= format_currency($p['tds_amount'] ?? 0) ?>
                                </td>
                                <td class="fw-bold text-success">
                                    <?= format_currency($p['net_amount'] ?? ($p['amount'] ?? 0)) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $modeBadge ?> text-uppercase px-2 py-1" style="font-size:0.65rem; letter-spacing: 0.3px;">
                                        <?= e(str_replace('_', ' ', $p['payment_mode'] ?? '-')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-light-heading"><?= e($p['transaction_ref'] ?? 'Direct Entry') ?></div>
                                    <small class="text-muted d-block" style="font-size:0.7rem;"><i class="bi bi-calendar-event me-1"></i><?= format_date($p['receipt_date']) ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state py-5">
                                    <i class="bi bi-cash-stack display-4 mb-3 d-block text-muted"></i>
                                    <h5>No Client Payments Recorded</h5>
                                    <p>Start recording payments received from clients to balance outstanding invoices and keep dynamic cash positions in real time.</p>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> payments</small>
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

<!-- Record Client Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="recordPaymentModalLabel"><i class="bi bi-cash-stack text-success"></i> Record Client Payment Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('accounts/payments') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <div class="modal-body text-white">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Receipt No (Optional)</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="receipt_no" placeholder="Auto-generated if left blank">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Client / Customer <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="client_id" id="modalClientSelector" onchange="filterModalInvoices()" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>"><?= e($client['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a client.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Outstanding Invoice <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="invoice_id" id="modalInvoiceSelector" required>
                                <option value="">Select Invoice</option>
                                <?php foreach ($invoices as $inv): ?>
                                    <option value="<?= $inv['id'] ?>" data-client="<?= $inv['client_id'] ?>" data-outstanding="<?= $inv['grand_total'] - $inv['paid_amount'] ?>">
                                        <?= e($inv['invoice_no']) ?> (Outstanding: ₹<?= number_format($inv['grand_total'] - $inv['paid_amount'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select an invoice to balance.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Payment Received (₹) <span class="text-danger">*</span></label>
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
                                <option value="upi">UPI / GPay</option>
                                <option value="cheque">Cheque Deposit</option>
                                <option value="cash">Cash Received</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Transaction Ref / Cheque No</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="transaction_ref" placeholder="Txn Ref No">
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="payment_date" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Payment Memo / Remarks</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="remarks" rows="2" placeholder="Brief payment comments..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Save Payment Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterModalInvoices() {
    const clientVal = document.getElementById('modalClientSelector').value;
    const invSelector = document.getElementById('modalInvoiceSelector');
    
    // Reset selection
    invSelector.value = '';
    
    // Filter options
    for (let i = 0; i < invSelector.options.length; i++) {
        const option = invSelector.options[i];
        if (option.value === '') continue;
        
        const optClient = option.getAttribute('data-client');
        if (clientVal === '' || optClient === clientVal) {
            option.classList.remove('d-none');
            option.disabled = false;
        } else {
            option.classList.add('d-none');
            option.disabled = true;
        }
    }
}
</script>
