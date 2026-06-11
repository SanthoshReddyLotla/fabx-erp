<?php
/**
 * Purchase - Create Purchase Order View
 */
$db = \Core\Database::getInstance();
$prs = $db->fetchAll("SELECT id, pr_no FROM " . $db->table("purchase_requisitions") . " WHERE status = 'approved' ORDER BY created_at DESC");
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bag-plus text-primary"></i> Create Purchase Order</h1>
    <a href="<?= base_url('purchase/orders') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to POs</a>
</div>

<form method="POST" action="<?= base_url('purchase/orders/create') ?>" id="poForm" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- PO Header Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-sliders"></i> Purchase Order Specifications</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Vendor Name <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="vendor_id" required>
                                <option value="">Select Vendor</option>
                                <?php foreach ($vendors as $v): ?>
                                    <option value="<?= $v['id'] ?>"><?= e($v['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a vendor.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Linked Requisition (PR) (Optional)</label>
                            <select class="form-select bg-dark border-secondary text-white" name="pr_id">
                                <option value="">None (Direct PO)</option>
                                <?php foreach ($prs as $pr): ?>
                                    <option value="<?= $pr['id'] ?>"><?= e($pr['pr_no']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Delivery Target Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="delivery_date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
                            <div class="invalid-feedback">Please select a target delivery date.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Delivery Destination Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="delivery_location" value="<?= e(COMPANY_ADDRESS) ?>" required>
                            <div class="invalid-feedback">Please enter the delivery destination address.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Payment Terms / Details <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="payment_terms" placeholder="e.g. 30 days post-receipt, 50% advance..." required>
                            <div class="invalid-feedback">Please enter payment terms.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items Card -->
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
                                    <th>Material / Item <span class="text-danger">*</span></th>
                                    <th>Specification</th>
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
                                        <select class="form-select form-select-sm bg-dark border-secondary text-white item-select" name="items[0][item_id]" onchange="handleItemSelect(this)" required>
                                            <option value="">-- Select Catalog Item --</option>
                                            <?php foreach ($items as $item): ?>
                                                <option value="<?= $item['id'] ?>" data-uom="<?= e($item['uom']) ?>" data-name="<?= e($item['name']) ?>">
                                                    [<?= e($item['item_code']) ?>] <?= e($item['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <option value="custom">-- Custom Item (Not in Catalog) --</option>
                                        </select>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white mt-1 d-none custom-desc" name="items[0][description]" placeholder="Enter item description..." required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][specification]" placeholder="e.g. Dimensions, Grade, standards">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white qty" name="items[0][quantity]" value="1" min="0.001" step="0.001" oninput="calculateTotals()" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white uom" name="items[0][uom]" value="Nos" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white rate text-end" name="items[0][unit_rate]" value="0.00" min="0" step="0.01" oninput="calculateTotals()" required>
                                    </td>
                                    <td class="text-end">
                                        <input type="text" class="form-control form-control-sm bg-dark border-0 text-white text-end amount font-monospace" readonly value="0.00">
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

            <!-- Terms & Conditions Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-file-earmark-text"></i> Terms, Conditions & Remarks</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Standard Contractual Conditions</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="terms_conditions" rows="4" placeholder="e.g.&#10;1. Standard warranty: 12 months from delivery.&#10;2. Goods must match the technical specifications attached.&#10;3. Damaged goods must be replaced by vendor."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-check-circle"></i> Save Draft PO</button>
                <a href="<?= base_url('purchase/orders') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>

        <!-- Summary & Calculation Column -->
        <div class="col-lg-4">
            <div class="fx-card position-sticky" style="top: 20px;">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-calculator"></i> PO Financial Summary</h5></div>
                <div class="fx-card-body p-4">
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Items Subtotal:</span>
                        <strong id="subtotalDisplay" class="text-white">₹ 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-3">
                        <span class="text-muted small">GST Rate (%):</span>
                        <strong class="text-white"><?= DEFAULT_GST_RATE ?>% (Standard)</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted small border-bottom border-secondary pb-3">
                        <span>Calculated GST Amount:</span>
                        <strong id="gstDisplay" class="text-white">₹ 0.00</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-1 pt-2">
                        <span class="h5 mb-0 text-light-heading">Grand Total:</span>
                        <strong class="h4 text-success mb-0" id="totalDisplay">₹ 0.00</strong>
                    </div>
                    <small class="text-muted d-block text-end mt-1" style="font-size:0.75rem;">Prices in INR</small>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let rowCount = 1;
const itemsList = <?= json_encode($items) ?>;
const gstRate = <?= DEFAULT_GST_RATE ?>;

function handleItemSelect(select) {
    const parentRow = select.closest('tr');
    const customInput = parentRow.querySelector('.custom-desc');
    const uomInput = parentRow.querySelector('.uom');
    
    if (select.value === 'custom') {
        customInput.classList.remove('d-none');
        customInput.value = '';
        customInput.setAttribute('required', 'required');
    } else {
        customInput.classList.add('d-none');
        customInput.removeAttribute('required');
        
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value) {
            customInput.value = selectedOption.getAttribute('data-name');
            uomInput.value = selectedOption.getAttribute('data-uom') || 'Nos';
        }
    }
    calculateTotals();
}

function addItemRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td class="row-num text-muted small fw-bold">${++rowCount}</td>
        <td>
            <select class="form-select form-select-sm bg-dark border-secondary text-white item-select" name="items[${rowCount-1}][item_id]" onchange="handleItemSelect(this)" required>
                <option value="">-- Select Catalog Item --</option>
                ${itemsList.map(i => `<option value="${i.id}" data-uom="${escapeHtml(i.uom)}" data-name="${escapeHtml(i.name)}">[${escapeHtml(i.item_code)}] ${escapeHtml(i.name)}</option>`).join('')}
                <option value="custom">-- Custom Item (Not in Catalog) --</option>
            </select>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white mt-1 d-none custom-desc" name="items[${rowCount-1}][description]" placeholder="Enter item description..." required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][specification]" placeholder="e.g. Dimensions, Grade, standards">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white qty" name="items[${rowCount-1}][quantity]" value="1" min="0.001" step="0.001" oninput="calculateTotals()" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white uom" name="items[${rowCount-1}][uom]" value="Nos" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white rate text-end" name="items[${rowCount-1}][unit_rate]" value="0.00" min="0" step="0.01" oninput="calculateTotals()" required>
        </td>
        <td class="text-end">
            <input type="text" class="form-control form-control-sm bg-dark border-0 text-white text-end amount font-monospace" readonly value="0.00">
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
        alert('At least one line item is required to create a purchase order.'); 
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
    
    const gstAmount = (subtotal * gstRate) / 100;
    const total = subtotal + gstAmount;
    
    document.getElementById('subtotalDisplay').textContent = '₹ ' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('gstDisplay').textContent = '₹ ' + gstAmount.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('totalDisplay').textContent = '₹ ' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
}

function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

window.addEventListener('DOMContentLoaded', () => {
    calculateTotals();
});
</script>
