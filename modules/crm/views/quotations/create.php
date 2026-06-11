<?php
/**
 * CRM - Create Quotation View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-file-text text-primary"></i> Create Quotation</h1>
    <a href="<?= base_url('crm/quotations') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="POST" action="<?= base_url('crm/quotations/create') ?>" id="quotationForm" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-sliders"></i> Quotation Specifications</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Quotation No</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" value="<?= $quotation_no ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Quotation Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="quotation_date" value="<?= date('Y-m-d') ?>" required>
                            <div class="invalid-feedback">Please select the quotation date.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Client / Customer <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="client_id" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>"><?= e($client['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a client.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Contact Person</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="contact_person" placeholder="e.g. John Doe">
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Subject</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="subject" placeholder="Quotation subject line, e.g. Supply of Fabrication Steel Elements">
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Description / Cover Letter Intro</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="description" rows="2" placeholder="Brief introduction or reference to the customer inquiry..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fx-card mb-4">
                <div class="fx-card-header d-flex justify-content-between align-items-center">
                    <h5 class="fx-card-title"><i class="bi bi-list-ol"></i> Line Items Breakdown</h5>
                    <button type="button" class="btn btn-sm btn-fx btn-fx-primary" onclick="addItemRow()"><i class="bi bi-plus-lg"></i> Add Item Row</button>
                </div>
                <div class="fx-card-body p-0">
                    <div class="table-responsive-fx">
                        <table class="fx-table align-middle mb-0" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Description <span class="text-danger">*</span></th>
                                    <th>Specification / Detailed Notes</th>
                                    <th style="width:100px">Qty <span class="text-danger">*</span></th>
                                    <th style="width:90px">UOM</th>
                                    <th style="width:140px">Unit Rate (₹) <span class="text-danger">*</span></th>
                                    <th style="width:140px" class="text-end">Amount (₹)</th>
                                    <th style="width:45px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td class="row-num text-muted small fw-bold">1</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][description]" placeholder="e.g. M.S. Flange Plates" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][specification]" placeholder="e.g. IS 2062 Grade">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-file-earmark-text"></i> Commercial Terms & Legal Clauses</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Delivery Terms</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="delivery_terms" placeholder="e.g. 8-10 weeks ex-works">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Payment Terms</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="payment_terms" placeholder="e.g. 30% advance, 70% against dispatch">
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Terms & Conditions / Remarks</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="terms_conditions" rows="4" placeholder="1. Validity: 30 days from quotation date&#10;2. Freight: Extra at actuals&#10;3. Warranty: 12 months standard warranty"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-check-circle"></i> Save Draft Quotation</button>
                <a href="<?= base_url('crm/quotations') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="fx-card position-sticky" style="top: 20px;">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-calculator"></i> Quotation Financial Summary</h5></div>
                <div class="fx-card-body p-4">
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Items Subtotal:</span>
                        <strong id="subtotalDisplay" class="text-white">₹ 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">GST Rate (%):</span>
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <input type="number" class="form-control bg-dark border-secondary text-white text-center" name="gst_rate" value="<?= DEFAULT_GST_RATE ?>" min="0" max="100" step="0.1" oninput="calculateTotals()" required>
                            <span class="input-group-text bg-dark border-secondary text-white">%</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted small border-bottom border-secondary pb-3">
                        <span>GST Amount:</span>
                        <strong id="gstDisplay" class="text-white">₹ 0.00</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-1 pt-2">
                        <span class="h5 mb-0 text-light-heading">Grand Total:</span>
                        <strong class="h4 text-success mb-0" id="totalDisplay">₹ 0.00</strong>
                    </div>
                    <small class="text-muted d-block text-end" style="font-size:0.75rem;">Prices in INR</small>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let rowCount = 1;

function addItemRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td class="row-num text-muted small fw-bold">${++rowCount}</td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][description]" placeholder="e.g. M.S. Flange Plates" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][specification]" placeholder="e.g. IS 2062 Grade">
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
        alert('At least one item line is required to build a valid quotation.'); 
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
    
    const gstRateInput = document.querySelector('[name="gst_rate"]');
    const gstRate = gstRateInput ? parseFloat(gstRateInput.value) || 0 : 18;
    const gstAmount = (subtotal * gstRate) / 100;
    const total = subtotal + gstAmount;
    
    document.getElementById('subtotalDisplay').textContent = '₹ ' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('gstDisplay').textContent = '₹ ' + gstAmount.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('totalDisplay').textContent = '₹ ' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
}

window.addEventListener('DOMContentLoaded', () => {
    calculateTotals();
});
</script>
