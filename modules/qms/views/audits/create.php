<?php /** FabX ERP - Create Audit with Dynamic Checklist */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-clipboard-plus"></i> Schedule Internal Audit</h1>
  <a href="<?= base_url('qms/audits') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/audits/create') ?>" id="auditForm">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card mb-4">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Audit Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Audit Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" required placeholder="e.g. Welding Process Audit Q1-2025">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Audit Type <span class="text-danger">*</span></label>
              <select name="audit_type" class="form-select" required>
                <option value="internal">Internal</option>
                <option value="external">External</option>
                <option value="supplier">Supplier</option>
                <option value="product">Product</option>
                <option value="process">Process</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Department</label>
              <select name="department_id" class="form-select">
                <option value="">Select Department</option>
                <?php foreach($departments as $d): ?>
                  <option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Lead Auditor <span class="text-danger">*</span></label>
              <select name="auditor_id" class="form-select" required>
                <option value="">Select Auditor</option>
                <?php foreach($users as $u): ?>
                  <option value="<?= $u['id'] ?>"><?= e($u['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Planned Start Date</label>
              <input type="date" name="planned_start_date" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Planned End Date</label>
              <input type="date" name="planned_end_date" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Audit Scope</label>
              <textarea name="scope" class="form-control" rows="2" placeholder="Define the scope boundaries of this audit..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Audit Criteria / Standards</label>
              <textarea name="criteria" class="form-control" rows="2" placeholder="ISO 9001:2015 clauses, internal procedures..."></textarea>
            </div>
          </div>
        </div>
      </div>
      <!-- Dynamic Checklist Engine -->
      <div class="fx-card">
        <div class="fx-card-header d-flex justify-content-between align-items-center">
          <h5 class="fx-card-title"><i class="bi bi-list-check"></i> Audit Checklist</h5>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="addChecklistItem()"><i class="bi bi-plus-circle"></i> Add Item</button>
        </div>
        <div class="fx-card-body">
          <div id="checklistContainer"></div>
          <div class="text-center text-muted py-3" id="checklistEmpty">
            <i class="bi bi-list-check fs-3 d-block mb-2 opacity-50"></i>
            <small>Click "Add Item" to build your audit checklist.</small>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="fx-card mb-3">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-lightbulb"></i> Checklist Guide</h5></div>
        <div class="fx-card-body">
          <p class="small text-muted mb-2">Build your audit checklist by adding evaluation questions. Each item should reference an ISO 9001:2015 clause.</p>
          <p class="small text-muted mb-0"><strong>Common clauses:</strong> 4.1 Context, 6.1 Risks, 7.1 Resources, 8.1 Operations, 9.1 Monitoring, 10.2 NCR</p>
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-calendar-check"></i> Schedule Audit</button>
        <a href="<?= base_url('qms/audits') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
<script>
var checklistCount = 0;
function addChecklistItem() {
  checklistCount++;
  document.getElementById('checklistEmpty').style.display = 'none';
  var container = document.getElementById('checklistContainer');
  var row = document.createElement('div');
  row.className = 'border rounded p-3 mb-3 position-relative';
  row.id = 'checklistItem' + checklistCount;
  row.innerHTML = `
    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="removeChecklistItem(${checklistCount})"><i class="bi bi-x-lg"></i></button>
    <div class="row g-2">
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Clause Ref</label>
        <input type="text" name="checklist[${checklistCount}][clause]" class="form-control form-control-sm" placeholder="e.g. 8.5.1">
      </div>
      <div class="col-md-9">
        <label class="form-label small fw-semibold">Evaluation Question <span class="text-danger">*</span></label>
        <input type="text" name="checklist[${checklistCount}][question]" class="form-control form-control-sm" placeholder="What will you evaluate?" required>
      </div>
      <div class="col-12">
        <label class="form-label small fw-semibold">Acceptance Criteria</label>
        <input type="text" name="checklist[${checklistCount}][criteria]" class="form-control form-control-sm" placeholder="Define what 'conforming' looks like...">
      </div>
    </div>`;
  container.appendChild(row);
}
function removeChecklistItem(n) {
  var el = document.getElementById('checklistItem' + n);
  if (el) el.remove();
  if (document.getElementById('checklistContainer').children.length === 0) {
    document.getElementById('checklistEmpty').style.display = '';
  }
}
</script>
