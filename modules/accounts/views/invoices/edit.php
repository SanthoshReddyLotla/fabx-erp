<?php
/**
 * FabX ERP - Edit Invoice View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-receipt text-primary"></i> Edit GST Tax Invoice</h1>
    <a href="<?= base_url('accounts/invoices') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="POST" action="<?= base_url('accounts/invoices/edit/' . $invoice['id']) ?>" id="invoiceForm" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    
    <div class="row">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- Invoice Meta Information Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-file-earmark-spreadsheet"></i> Invoice Credentials</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Invoice No</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" value="<?= e($invoice['invoice_no']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Client / Buyer <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="client_id" id="clientSelector" onchange="updateBillingAddress()" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" data-gstin="<?= e($client['gstin'] ?? '') ?>" data-address="<?= e($client['address'] ?? '') ?>" <?= ((int)$invoice['client_id'] === (int)$client['id']) ? 'selected' : '' ?>>
                                        <?= e($client['company_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a buyer client.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="invoice_date" value="<?= e($invoice['invoice_date']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="due_date" value="<?= e($invoice['due_date']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">PO Reference No</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="po_reference" value="<?= e($invoice['po_reference']) ?>" placeholder="e.g. PO-998877">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">GSTIN Reference</label>
                            <?php
                            $selectedClientGstin = '';
                            foreach ($clients as $c) {
                                if ((int)$c['id'] === (int)$invoice['client_id']) {
                                    $selectedClientGstin = $c['gstin'];
                                    break;
                                }
                            }
                            ?>
                            <input type="text" class="form-control bg-dark border-secondary text-white text-muted" id="gstinDisplay" value="<?= e($selectedClientGstin) ?>" placeholder="Client GSTIN" readonly>
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Billing / Supply Address <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="billing_address" id="billingAddress" rows="3" required><?= e($invoice['billing_address']) ?></textarea>
                            <div class="invalid-feedback">Billing address is required.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header d-flex justify-content-between align-items-center">
                    <h5 class="fx-card-title"><i class="bi bi-list-stars"></i> Invoice Line Details</h5>
                    <button type="button" class="btn btn-sm btn-fx btn-fx-primary" onclick="addInvoiceItemRow()"><i class="bi bi-plus-lg"></i> Add Item Line</button>
                </div>
                <div class="fx-card-body p-0">
                    <div class="table-responsive-fx">
                        <table class="fx-table align-middle mb-0" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Description <span class="text-danger">*</span></th>
                                    <th style="width:110px">HSN / SAC</th>
                                    <th style="width:90px">Qty <span class="text-danger">*</span></th>
                                    <th style="width:90px">UOM</th>
                                    <th style="width:130px">Unit Rate (₹) <span class="text-danger">*</span></th>
                                    <th style="width:140px" class="text-end">Amount (₹)</th>
                                    <th style="width:45px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr class="item-row">
                                            <td class="row-num text-muted small fw-bold"><?= $index + 1 ?></td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[<?= $index ?>][description]" value="<?= e($item['description']) ?>" placeholder="e.g. M.S. Welded Structure" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[<?= $index ?>][hsn_code]" value="<?= e($item['hsn_code']) ?>" placeholder="e.g. 7308">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white qty" name="items[<?= $index ?>][quantity]" value="<?= (float)$item['quantity'] ?>" min="0.001" step="0.001" oninput="calculateTotals()" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[<?= $index ?>][uom]" value="<?= e($item['uom'] ?: 'Nos') ?>" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white rate" name="items[<?= $index ?>][unit_rate]" value="<?= (float)$item['unit_rate'] ?>" min="0" step="0.01" oninput="calculateTotals()" required>
                                            </td>
                                            <td class="text-end">
                                                <input type="text" class="form-control form-control-sm bg-dark border-0 text-white text-end amount" readonly value="<?= number_format($item['amount'], 2, '.', '') ?>">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)"><i class="bi bi-trash fs-5"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="item-row">
                                        <td class="row-num text-muted small fw-bold">1</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][description]" placeholder="e.g. M.S. Welded Structure" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][hsn_code]" placeholder="e.g. 7308">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white qty" name="items[0][quantity]" value="1" min="0.001" step="0.001" oninput="calculateTotals()" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][uom]" value="Nos" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white rate" name="items[0][unit_rate]" value="0" min="0" step="0.01" oninput="calculateTotals()" required>
                                        </td>
                                        <td class="text-end">
                                            <input type="text" class="form-control form-control-sm bg-dark border-0 text-white text-end amount" readonly value="0.00">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)"><i class="bi bi-trash fs-5"></i></button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-shield-lock"></i> Declarations, Bank & Terms</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Bank Details</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="bank_details" rows="4" placeholder="Bank: State Bank of India&#10;A/C: 12345678901&#10;IFSC: SBIN0001234&#10;Branch: Industrial Area"><?= e($invoice['bank_details']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Terms & Conditions</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="terms_conditions" rows="4" placeholder="1. Interest @ 18% p.a. charged after due date.&#10;2. Goods once sold will not be taken back.&#10;3. Disputes subject to local jurisdiction."><?= e($invoice['terms_conditions']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-receipt-cutoff"></i> Save Changes</button>
                <a href="<?= base_url('accounts/invoices') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>

        <!-- Summary & Taxation Column -->
        <div class="col-lg-4">
            <div class="fx-card position-sticky" style="top: 20px;">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-calculator-fill"></i> Financial Summary</h5></div>
                <div class="fx-card-body p-4">
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Items Subtotal:</span>
                        <strong id="subtotalDisplay" class="text-white">₹ <?= number_format($invoice['subtotal'], 2) ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fx-form-label text-muted small d-block mb-1">Discount Amount (₹):</label>
                        <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="discount_amount" id="discountAmount" value="<?= (float)$invoice['discount_amount'] ?>" min="0" step="0.01" oninput="calculateTotals()">
                    </div>

                    <div class="d-flex justify-content-between mb-3 text-muted small border-bottom border-secondary pb-3">
                        <span>Taxable Value:</span>
                        <strong id="taxableDisplay" class="text-white">₹ <?= number_format($invoice['taxable_amount'], 2) ?></strong>
                    </div>

                    <?php
                    $isInterstate = ((float)$invoice['igst_amount'] > 0);
                    ?>
                    <div class="mb-3">
                        <label class="fx-form-label text-muted small d-block mb-1">GST Classification:</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gst_type" id="intrastateGst" value="intrastate" <?= !$isInterstate ? 'checked' : '' ?> onchange="calculateTotals()">
                                <label class="form-check-label text-white small" for="intrastateGst">CGST + SGST (Intrastate)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gst_type" id="interstateGst" value="interstate" <?= $isInterstate ? 'checked' : '' ?> onchange="calculateTotals()">
                                <label class="form-check-label text-white small" for="interstateGst">IGST (Interstate)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Split Tax Displays -->
                    <div id="splitGstSection" class="<?= $isInterstate ? 'd-none' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">CGST Rate (%):</span>
                            <div class="input-group input-group-sm" style="width: 80px;">
                                <input type="number" class="form-control bg-dark border-secondary text-white text-center" name="cgst_rate" value="<?= (float)$invoice['cgst_rate'] ?: 9 ?>" min="0" step="0.1" oninput="calculateTotals()">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted small">
                            <span>CGST Amount:</span>
                            <strong id="cgstDisplay" class="text-white">₹ <?= number_format($invoice['cgst_amount'], 2) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">SGST Rate (%):</span>
                            <div class="input-group input-group-sm" style="width: 80px;">
                                <input type="number" class="form-control bg-dark border-secondary text-white text-center" name="sgst_rate" value="<?= (float)$invoice['sgst_rate'] ?: 9 ?>" min="0" step="0.1" oninput="calculateTotals()">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted small border-bottom border-secondary pb-3">
                            <span>SGST Amount:</span>
                            <strong id="sgstDisplay" class="text-white">₹ <?= number_format($invoice['sgst_amount'], 2) ?></strong>
                        </div>
                    </div>

                    <div id="integratedGstSection" class="<?= !$isInterstate ? 'd-none' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">IGST Rate (%):</span>
                            <div class="input-group input-group-sm" style="width: 80px;">
                                <input type="number" class="form-control bg-dark border-secondary text-white text-center" name="igst_rate" value="<?= (float)$invoice['igst_rate'] ?: 18 ?>" min="0" step="0.1" oninput="calculateTotals()">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted small border-bottom border-secondary pb-3">
                            <span>IGST Amount:</span>
                            <strong id="igstDisplay" class="text-white">₹ <?= number_format($invoice['igst_amount'], 2) ?></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fx-form-label text-muted small d-block mb-1">Adjustment / Round Off (₹):</label>
                        <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="round_off" id="roundOff" value="<?= number_format($invoice['round_off'], 2, '.', '') ?>" step="0.01" oninput="calculateTotals()">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1 pt-2">
                        <span class="h5 mb-0 text-light-heading">Grand Total:</span>
                        <strong class="h4 text-success mb-0" id="grandTotalDisplay">₹ <?= number_format($invoice['grand_total'], 2) ?></strong>
                    </div>
                    <small class="text-muted d-block text-end" style="font-size:0.75rem;">INR Currency</small>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let rowCount = <?= count($items) ?: 1 ?>;

function updateBillingAddress() {
    const selector = document.getElementById('clientSelector');
    const selected = selector.options[selector.selectedIndex];
    if (selected) {
        document.getElementById('gstinDisplay').value = selected.getAttribute('data-gstin') || '';
        document.getElementById('billingAddress').value = selected.getAttribute('data-address') || '';
    }
}

function addInvoiceItemRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td class="row-num text-muted small fw-bold">${++rowCount}</td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][description]" placeholder="e.g. M.S. Welded Structure" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][hsn_code]" placeholder="e.g. 7308">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white qty" name="items[${rowCount-1}][quantity]" value="1" min="0.001" step="0.001" oninput="calculateTotals()" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][uom]" value="Nos" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white rate" name="items[${rowCount-1}][unit_rate]" value="0" min="0" step="0.01" oninput="calculateTotals()" required>
        </td>
        <td class="text-end">
            <input type="text" class="form-control form-control-sm bg-dark border-0 text-white text-end amount" readonly value="0.00">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)"><i class="bi bi-trash fs-5"></i></button>
        </td>
    `;
    tbody.appendChild(row);
    renumberRows();
    calculateTotals();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) { 
        alert('At least one invoice line item is required.'); 
        return; 
    }
    btn.closest('tr').remove();
    renumberRows();
    calculateTotals();
}

function renumberRows() {
    document.querySelectorAll('.item-row').forEach((row, i) => {
        row.querySelector('.row-num').textContent = i + 1;
    });
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const rate = parseFloat(row.querySelector('.rate').value) || 0;
        const amount = qty * rate;
        row.querySelector('.amount').value = amount.toFixed(2);
        subtotal += amount;
    });
    
    document.getElementById('subtotalDisplay').textContent = '₹ ' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    
    const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
    const taxable = Math.max(0, subtotal - discount);
    document.getElementById('taxableDisplay').textContent = '₹ ' + taxable.toLocaleString('en-IN', {minimumFractionDigits: 2});
    
    const isInterstate = document.getElementById('interstateGst').checked;
    
    let cgstAmt = 0;
    let sgstAmt = 0;
    let igstAmt = 0;
    
    if (isInterstate) {
        document.getElementById('splitGstSection').classList.add('d-none');
        document.getElementById('integratedGstSection').classList.remove('d-none');
        
        const igstRate = parseFloat(document.getElementsByName('igst_rate')[0].value) || 0;
        igstAmt = (taxable * igstRate) / 100;
        document.getElementById('igstDisplay').textContent = '₹ ' + igstAmt.toLocaleString('en-IN', {minimumFractionDigits: 2});
    } else {
        document.getElementById('splitGstSection').classList.remove('d-none');
        document.getElementById('integratedGstSection').classList.add('d-none');
        
        const cgstRate = parseFloat(document.getElementsByName('cgst_rate')[0].value) || 0;
        const sgstRate = parseFloat(document.getElementsByName('sgst_rate')[0].value) || 0;
        
        cgstAmt = (taxable * cgstRate) / 100;
        sgstAmt = (taxable * sgstRate) / 100;
        
        document.getElementById('cgstDisplay').textContent = '₹ ' + cgstAmt.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('sgstDisplay').textContent = '₹ ' + sgstAmt.toLocaleString('en-IN', {minimumFractionDigits: 2});
    }
    
    const roundOff = parseFloat(document.getElementById('roundOff').value) || 0;
    const grandTotal = taxable + cgstAmt + sgstAmt + igstAmt + roundOff;
    document.getElementById('grandTotalDisplay').textContent = '₹ ' + grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
}

window.addEventListener('DOMContentLoaded', () => {
    calculateTotals();
});
</script>
