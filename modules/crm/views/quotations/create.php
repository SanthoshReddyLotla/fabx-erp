<?php
/**
 * CRM - Create Quotation View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-file-text text-primary"></i> Create Quotation</h1>
    <a href="<?= base_url('crm/quotations') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="<?= base_url('crm/quotations/create') ?>" id="quotationForm">
            <?= csrf_field() ?>
            
            <div class="fx-card mb-3">
                <div class="fx-card-header"><h5 class="fx-card-title">Quotation Details</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label">Quotation No</label>
                            <input type="text" class="form-control" value="<?= $quotation_no ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label">Quotation Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="quotation_date" value="<?= date('d-m-Y') ?>" data-datepicker required>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="client_id" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>"><?= e($client['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label">Contact Person</label>
                            <input type="text" class="form-control" name="contact_person">
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" placeholder="Quotation subject line">
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fx-card mb-3">
                <div class="fx-card-header d-flex justify-content-between align-items-center">
                    <h5 class="fx-card-title">Line Items</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addItemRow()"><i class="bi bi-plus-lg"></i> Add Item</button>
                </div>
                <div class="fx-card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Description <span class="text-danger">*</span></th>
                                    <th style="width:120px">Qty <span class="text-danger">*</span></th>
                                    <th style="width:100px">UOM</th>
                                    <th style="width:150px">Unit Rate (₹) <span class="text-danger">*</span></th>
                                    <th style="width:150px">Amount (₹)</th>
                                    <th style="width:40px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td class="row-num">1</td>
                                    <td><input type="text" class="form-control form-control-sm" name="items[0][description]" required></td>
                                    <td><input type="number" class="form-control form-control-sm qty" name="items[0][quantity]" value="1" min="0.01" step="0.01" onchange="calculateTotals()"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="items[0][uom]" value="Nos"></td>
                                    <td><input type="number" class="form-control form-control-sm rate" name="items[0][unit_rate]" value="0" min="0" step="0.01" onchange="calculateTotals()"></td>
                                    <td><input type="text" class="form-control form-control-sm amount" readonly value="0.00"></td>
                                    <td><button type="button" class="btn btn-sm btn-link text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="fx-card mb-3">
                <div class="fx-card-header"><h5 class="fx-card-title">Terms & Conditions</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fx-form-label">Delivery Terms</label>
                            <input type="text" class="form-control" name="delivery_terms" placeholder="e.g., 8-10 weeks ex-works">
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label">Payment Terms</label>
                            <input type="text" class="form-control" name="payment_terms" placeholder="e.g., 30% advance, 70% against dispatch">
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label">Terms & Conditions</label>
                            <textarea class="form-control" name="terms_conditions" rows="4" placeholder="1. Validity: 30 days
2. Taxes: GST extra @ 18%
3. Packing: Standard seaworthy"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-check-lg"></i> Save Quotation</button>
                <button type="submit" name="save_send" value="1" class="btn btn-success"><i class="bi bi-send"></i> Save & Send</button>
                <a href="<?= base_url('crm/quotations') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="fx-card">
            <div class="fx-card-header"><h5 class="fx-card-title">Summary</h5></div>
            <div class="fx-card-body">
                <div class="d-flex justify-content-between mb-2"><span>Subtotal:</span><strong id="subtotalDisplay">₹ 0.00</strong></div>
                <div class="d-flex justify-content-between mb-2">
                    <span>GST (%):</span>
                    <input type="number" class="form-control form-control-sm" style="width:80px" name="gst_rate" value="<?= DEFAULT_GST_RATE ?>" onchange="calculateTotals()">
                </div>
                <div class="d-flex justify-content-between mb-2"><span>GST Amount:</span><strong id="gstDisplay">₹ 0.00</strong></div>
                <hr>
                <div class="d-flex justify-content-between"><span class="h5">Total:</span><strong class="h5 text-primary" id="totalDisplay">₹ 0.00</strong></div>
            </div>
        </div>
    </div>
</div>

<script>
let rowCount = 1;

function addItemRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td class="row-num">${++rowCount}</td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowCount-1}][description]" required></td>
        <td><input type="number" class="form-control form-control-sm qty" name="items[${rowCount-1}][quantity]" value="1" min="0.01" step="0.01" onchange="calculateTotals()"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowCount-1}][uom]" value="Nos"></td>
        <td><input type="number" class="form-control form-control-sm rate" name="items[${rowCount-1}][unit_rate]" value="0" min="0" step="0.01" onchange="calculateTotals()"></td>
        <td><input type="text" class="form-control form-control-sm amount" readonly value="0.00"></td>
        <td><button type="button" class="btn btn-sm btn-link text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    renumberRows();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) { alert('At least one item is required'); return; }
    btn.closest('tr').remove();
    calculateTotals();
    renumberRows();
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
    
    const gstRate = parseFloat(document.querySelector('[name="gst_rate"]').value) || 0;
    const gstAmount = (subtotal * gstRate) / 100;
    const total = subtotal + gstAmount;
    
    document.getElementById('subtotalDisplay').textContent = '₹ ' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('gstDisplay').textContent = '₹ ' + gstAmount.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('totalDisplay').textContent = '₹ ' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
}
</script>
