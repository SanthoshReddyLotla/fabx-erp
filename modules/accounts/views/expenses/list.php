<?php
/**
 * FabX ERP - Expenses List View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-wallet2 text-primary"></i> Expense Operations</h1>
    <div class="page-actions">
        <button type="button" class="btn btn-fx btn-fx-primary" data-bs-toggle="modal" data-bs-target="#logExpenseModal">
            <i class="bi bi-plus-lg"></i> Log Operational Expense
        </button>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calculator"></i> Cost Center Allocation Mapping & Operational Expense Ledger</h5>
        <span class="badge bg-dark border border-secondary text-muted">Page <?= $pagination['page'] ?? 1 ?></span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Expense Ref</th>
                        <th>Cost Center Category</th>
                        <th>Project / Allocation Reference</th>
                        <th>Recipient Vendor</th>
                        <th class="text-end">Base Amount</th>
                        <th class="text-end">GST Amount</th>
                        <th class="text-end">Total Expenditures</th>
                        <th>Txn / Mode</th>
                        <th>Logged By</th>
                        <th>Logged On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expenses)): ?>
                        <?php foreach ($expenses as $e): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-dark border border-secondary font-monospace py-2 px-3">
                                        <?= e($e['expense_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e(ucfirst(str_replace('_', ' ', $e['category'] ?? '-'))) ?></div>
                                    <small class="text-muted d-block" style="font-size:0.75rem;"><?= e(substr($e['description'] ?? '', 0, 45)) ?><?= strlen($e['description'] ?? '') > 45 ? '...' : '' ?></small>
                                </td>
                                <td>
                                    <?php if ($e['project_id']): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1">
                                            <i class="bi bi-gear-wide-connected me-1"></i><?= e($e['project_name'] ?? 'Project Allocation') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1">
                                            <i class="bi bi-building me-1"></i> Corporate Overhead
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-light-heading"><?= e($e['vendor'] ?? 'General Outflow') ?></div>
                                </td>
                                <td class="text-end text-muted small">
                                    <?= format_currency($e['amount'] ?? 0) ?>
                                </td>
                                <td class="text-end text-danger small">
                                    <?= format_currency($e['gst_amount'] ?? 0) ?>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    <?= format_currency($e['total_amount'] ?? ($e['amount'] ?? 0)) ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-light-heading text-uppercase" style="font-size:0.75rem;"><?= e($e['payment_mode'] ?? '-') ?></div>
                                    <small class="text-muted" style="font-size:0.7rem;"><?= e($e['reference_no'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="small"><?= e($e['created_name'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <div class="small text-light-heading"><?= format_date($e['expense_date']) ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">
                                <div class="empty-state py-5">
                                    <i class="bi bi-wallet2 display-4 mb-3 d-block text-muted"></i>
                                    <h5>No Expenses Registered</h5>
                                    <p>Log corporate overheads, manufacturing costs, sub-contractor procurements, or consumables here to compute precise financial reporting statements.</p>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> overhead logs</small>
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

<!-- Log Expense Modal -->
<div class="modal fade" id="logExpenseModal" tabindex="-1" aria-labelledby="logExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="logExpenseModalLabel"><i class="bi bi-wallet-fill text-primary"></i> Log Operational Expenditure</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('accounts/expenses') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <div class="modal-body text-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Cost Center Category <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="category" required>
                                <option value="raw_materials">Raw Materials Procurement</option>
                                <option value="consumables">Workshop Consumables</option>
                                <option value="logistics">Logistics & Freight</option>
                                <option value="machinery_maintenance">Machinery Maintenance</option>
                                <option value="subcontractor_charges">Subcontractor Charges</option>
                                <option value="power_utility">Electricity & Power Utility</option>
                                <option value="salaries_wages">Salaries & Wages</option>
                                <option value="office_admin">Office Administration</option>
                                <option value="other">Other Miscellaneous</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Cost Center Allocation (Project)</label>
                            <select class="form-select bg-dark border-secondary text-white" name="project_id">
                                <option value="">Corporate Overhead (Unallocated)</option>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?= $project['id'] ?>"><?= e($project['project_code']) ?> - <?= e($project['project_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Recipient Vendor / Service Provider <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="vendor" placeholder="e.g. Acme Steels Ltd" required>
                            <div class="invalid-feedback">Please enter the vendor name.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Base Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="amount" min="0.01" step="0.01" required>
                            <div class="invalid-feedback">Base amount must be greater than zero.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">GST Paid (₹)</label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="gst_amount" value="0.00" min="0" step="0.01">
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Payment Mode <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="payment_mode" required>
                                <option value="neft">NEFT Bank Transfer</option>
                                <option value="rtgs">RTGS Bank Transfer</option>
                                <option value="upi">UPI / QR Payment</option>
                                <option value="cheque">Cheque Deposit</option>
                                <option value="cash">Cash Account</option>
                                <option value="other">Other Method</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Txn Reference / Cheque No</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="reference_no" placeholder="Txn Ref No">
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="expense_date" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Expense Narrative / Description</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="description" rows="3" placeholder="Describe the utility of this expenditure..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-wallet2"></i> Save Expense Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
