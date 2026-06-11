<?php
/**
 * Purchase - Create Purchase Requisition View
 */
$db = \Core\Database::getInstance();
$itemsList = $db->fetchAll("SELECT id, item_code, name, uom FROM " . $db->table("items") . " WHERE status = 'active'");
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-clipboard-plus text-primary"></i> Create Purchase Requisition</h1>
    <a href="<?= base_url('purchase/requisitions') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to PRs</a>
</div>

<form method="POST" action="<?= base_url('purchase/requisitions/create') ?>" id="prForm" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- PR Header Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-sliders"></i> Requisition Specifications</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Department <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="department_id" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['id'] ?>" <?= (isset($_SESSION['user_department']) && $_SESSION['user_department'] == $dept['name']) ? 'selected' : '' ?>>
                                        <?= e($dept['name']) ?> (<?= e($dept['code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a department.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Associated Project (Optional)</label>
                            <select class="form-select bg-dark border-secondary text-white" name="project_id">
                                <option value="">Select Project (General Stock)</option>
                                <?php foreach ($projects as $proj): ?>
                                    <option value="<?= $proj['id'] ?>"><?= e($proj['project_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Required By Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="required_by_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                            <div class="invalid-feedback">Please specify the date materials are required.</div>
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Requisition Justification / Purpose <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="justification" rows="3" placeholder="Explain why these items are required (e.g. Fabrication works for Reliance structure, replenishment of general store hardware...)" required></textarea>
                            <div class="invalid-feedback">Please enter a justification.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header d-flex justify-content-between align-items-center">
                    <h5 class="fx-card-title"><i class="bi bi-list-ol"></i> Requisition Line Items</h5>
                    <button type="button" class="btn btn-sm btn-fx btn-fx-primary" onclick="addItemRow()"><i class="bi bi-plus-lg"></i> Add Item Row</button>
                </div>
                <div class="fx-card-body p-0">
                    <div class="table-responsive-fx">
                        <table class="fx-table align-middle mb-0" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Material / Item <span class="text-danger">*</span></th>
                                    <th>Description / Specification</th>
                                    <th style="width:100px">Qty <span class="text-danger">*</span></th>
                                    <th style="width:90px">UOM</th>
                                    <th style="width:130px">Required Date</th>
                                    <th>Purpose / Location</th>
                                    <th style="width:45px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td class="row-num text-muted small fw-bold">1</td>
                                    <td>
                                        <select class="form-select form-select-sm bg-dark border-secondary text-white item-select" name="items[0][item_id]" onchange="handleItemSelect(this)" required>
                                            <option value="">-- Select Catalog Item --</option>
                                            <?php foreach ($itemsList as $i): ?>
                                                <option value="<?= $i['id'] ?>" data-uom="<?= e($i['uom']) ?>" data-name="<?= e($i['name']) ?>">
                                                    [<?= e($i['item_code']) ?>] <?= e($i['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <option value="custom">-- Custom Item (Not in Catalog) --</option>
                                        </select>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white mt-1 d-none custom-desc" name="items[0][description]" placeholder="Enter item description..." required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][specification]" placeholder="e.g. Size, Grade, Material specifications">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white qty" name="items[0][quantity]" value="1" min="0.001" step="0.001" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white uom" name="items[0][uom]" value="Nos" required>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][required_date]" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][purpose]" placeholder="e.g. Workshop use">
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

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-check-circle"></i> Submit Requisition</button>
                <a href="<?= base_url('purchase/requisitions') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>

        <!-- Onboarding/Information Column -->
        <div class="col-lg-4">
            <div class="fx-card">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> PR Guidelines</h5></div>
                <div class="fx-card-body small text-muted">
                    <p class="mb-2"><strong class="text-white">Workflow Cycle:</strong> Submitted requisitions are routed to the department head or Purchase Manager for authorization. Once approved, they can be directly converted into a Purchase Order (PO).</p>
                    <p class="mb-2"><strong class="text-white">Catalog vs Custom:</strong> Prefer selecting catalog items to maintain correct inventory tracking and transaction history. Use "Custom Item" only for one-off specialty items or custom fabrication details.</p>
                    <p class="mb-0"><strong class="text-white">Lead Times:</strong> Take vendor lead times into consideration when defining the "Required By Date" to prevent project delays.</p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let rowCount = 1;
const itemsList = <?= json_encode($itemsList) ?>;

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
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][specification]" placeholder="e.g. Size, Grade, Material specifications">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm bg-dark border-secondary text-white qty" name="items[${rowCount-1}][quantity]" value="1" min="0.001" step="0.001" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white uom" name="items[${rowCount-1}][uom]" value="Nos" required>
        </td>
        <td>
            <input type="date" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][required_date]" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${rowCount-1}][purpose]" placeholder="e.g. Workshop use">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)"><i class="bi bi-trash fs-5"></i></button>
        </td>
    `;
    tbody.appendChild(row);
    renumberRows();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) { 
        alert('At least one line item is required to submit a requisition.'); 
        return; 
    }
    btn.closest('tr').remove();
    renumberRows();
}

function renumberRows() {
    document.querySelectorAll('.item-row').forEach((row, i) => {
        row.querySelector('.row-num').textContent = i + 1;
    });
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
</script>
