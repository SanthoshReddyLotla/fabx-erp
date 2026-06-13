<?php /** FabX ERP - Create Delivery Challan */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-truck text-primary"></i> Create Delivery Challan</h1>
    <a href="<?= base_url('accounts/delivery-challans') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="POST" action="<?= base_url('accounts/delivery-challans/create') ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Challan Details</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">DC No</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" value="<?= e($dc_no) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">DC Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark border-secondary text-white" name="dc_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Consignee (Client) <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="client_id" id="dcClient" onchange="fillShipTo()" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $cl): ?>
                                    <option value="<?= $cl['id'] ?>" data-address="<?= e($cl['address'] ?? '') ?>"><?= e($cl['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Select the consignee.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Reason for Transportation</label>
                            <select class="form-select bg-dark border-secondary text-white" name="reason">
                                <option value="supply">Supply</option>
                                <option value="job_work">Job Work</option>
                                <option value="sample">Sample / Display</option>
                                <option value="approval">Supply on Approval</option>
                                <option value="return">Sales Return</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Project (optional)</label>
                            <select class="form-select bg-dark border-secondary text-white" name="project_id">
                                <option value="">None</option>
                                <?php foreach ($projects as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= e($p['project_code']) ?> - <?= e($p['project_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Ship To Address</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="ship_to_address" id="dcShipTo" rows="2" placeholder="Defaults to client address"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fx-card mb-4">
                <div class="fx-card-header d-flex justify-content-between align-items-center">
                    <h5 class="fx-card-title"><i class="bi bi-box-seam"></i> Goods Dispatched</h5>
                    <button type="button" class="btn btn-sm btn-fx btn-fx-primary" onclick="addDcRow()"><i class="bi bi-plus-lg"></i> Add Item</button>
                </div>
                <div class="fx-card-body p-0">
                    <div class="table-responsive-fx">
                        <table class="fx-table align-middle mb-0" id="dcItems">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Description <span class="text-danger">*</span></th>
                                    <th style="width:110px">HSN/SAC</th>
                                    <th style="width:90px">Qty</th>
                                    <th style="width:90px">UOM</th>
                                    <th style="width:130px">Value (₹)</th>
                                    <th style="width:45px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="dc-row">
                                    <td class="row-num text-muted small fw-bold">1</td>
                                    <td><input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][description]" placeholder="e.g. Fabricated MS Bracket" required></td>
                                    <td><input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][hsn_code]" placeholder="7308"></td>
                                    <td><input type="number" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][quantity]" value="1" min="0" step="0.001"></td>
                                    <td><input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[0][uom]" value="Nos"></td>
                                    <td><input type="number" class="form-control form-control-sm bg-dark border-secondary text-white text-end" name="items[0][value]" value="0" min="0" step="0.01"></td>
                                    <td><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeDcRow(this)"><i class="bi bi-trash fs-5"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-check-circle"></i> Create &amp; Print Challan</button>
                <a href="<?= base_url('accounts/delivery-challans') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="fx-card">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-truck"></i> Transport Details</h5></div>
                <div class="fx-card-body">
                    <div class="mb-3">
                        <label class="fx-form-label text-muted small">Vehicle Number</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" name="vehicle_no" placeholder="e.g. MH12 AB 1234">
                    </div>
                    <div class="mb-3">
                        <label class="fx-form-label text-muted small">Transport Mode</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" name="transport_mode" placeholder="e.g. Road / Courier">
                    </div>
                    <div class="mb-3">
                        <label class="fx-form-label text-muted small">Transporter</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" name="transporter" placeholder="Transporter name">
                    </div>
                    <div class="mb-3">
                        <label class="fx-form-label text-muted small">E-Way Bill No</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" name="eway_bill_no" placeholder="If applicable">
                    </div>
                    <div class="mb-0">
                        <label class="fx-form-label text-muted small">Remarks</label>
                        <textarea class="form-control bg-dark border-secondary text-white" name="remarks" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let dcRow = 1;
function fillShipTo() {
    const sel = document.getElementById('dcClient');
    const opt = sel.options[sel.selectedIndex];
    const box = document.getElementById('dcShipTo');
    if (opt && !box.value.trim()) box.value = opt.getAttribute('data-address') || '';
}
function addDcRow() {
    const tbody = document.querySelector('#dcItems tbody');
    const i = dcRow++;
    const tr = document.createElement('tr');
    tr.className = 'dc-row';
    tr.innerHTML = `
        <td class="row-num text-muted small fw-bold">${i + 1}</td>
        <td><input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${i}][description]" required></td>
        <td><input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${i}][hsn_code]"></td>
        <td><input type="number" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${i}][quantity]" value="1" min="0" step="0.001"></td>
        <td><input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" name="items[${i}][uom]" value="Nos"></td>
        <td><input type="number" class="form-control form-control-sm bg-dark border-secondary text-white text-end" name="items[${i}][value]" value="0" min="0" step="0.01"></td>
        <td><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeDcRow(this)"><i class="bi bi-trash fs-5"></i></button></td>`;
    tbody.appendChild(tr);
    renumberDc();
}
function removeDcRow(btn) {
    if (document.querySelectorAll('.dc-row').length <= 1) { alert('At least one item is required.'); return; }
    btn.closest('tr').remove();
    renumberDc();
}
function renumberDc() {
    document.querySelectorAll('.dc-row .row-num').forEach((c, i) => c.textContent = i + 1);
}
</script>
