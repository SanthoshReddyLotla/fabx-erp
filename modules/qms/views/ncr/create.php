<?php /** FabX ERP - Create NCR */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-exclamation-octagon text-danger"></i> Raise Non-Conformance Report</h1>
  <a href="<?= base_url('qms/ncr') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/ncr/create') ?>">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-file-earmark-x"></i> NCR Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">NCR Date <span class="text-danger">*</span></label>
              <input type="date" name="ncr_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Source <span class="text-danger">*</span></label>
              <select name="source" class="form-select" required>
                <option value="">Select Source</option>
                <option value="internal">Internal</option>
                <option value="external">External</option>
                <option value="customer_complaint">Customer Complaint</option>
                <option value="audit">Audit</option>
                <option value="incoming_inspection">Incoming Inspection</option>
                <option value="inprocess_inspection">In-Process Inspection</option>
                <option value="final_inspection">Final Inspection</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Severity <span class="text-danger">*</span></label>
              <select name="severity" class="form-select" required>
                <option value="minor">Minor</option>
                <option value="major">Major</option>
                <option value="critical">Critical</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
              <select name="category" class="form-select" required>
                <option value="">Select Category</option>
                <option value="material">Material</option>
                <option value="process">Process</option>
                <option value="documentation">Documentation</option>
                <option value="equipment">Equipment</option>
                <option value="personnel">Personnel</option>
                <option value="system">System</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Department</label>
              <select name="department_id" class="form-select">
                <option value="">Select Department</option>
                <?php foreach ($departments as $dept): ?>
                  <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Related Project</label>
              <select name="project_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($projects as $proj): ?>
                  <option value="<?= $proj['id'] ?>"><?= e($proj['project_code']) ?> — <?= e($proj['project_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Non-Conformance Description <span class="text-danger">*</span></label>
              <textarea name="description" class="form-control" rows="4" required placeholder="Describe the non-conformance in detail..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Immediate Action Taken</label>
              <textarea name="immediate_action" class="form-control" rows="3" placeholder="What immediate containment action was taken?"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="fx-card mb-3">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-exclamation-triangle"></i> Severity Guide</h5></div>
        <div class="fx-card-body">
          <div class="mb-2 p-2 rounded" style="background:rgba(39,174,96,.08)"><span class="badge bg-success me-1">Minor</span><small>No impact on product function/safety.</small></div>
          <div class="mb-2 p-2 rounded" style="background:rgba(243,156,18,.08)"><span class="badge bg-warning me-1">Major</span><small>Affects quality/process effectiveness.</small></div>
          <div class="p-2 rounded" style="background:rgba(231,76,60,.08)"><span class="badge bg-danger me-1">Critical</span><small>Safety/regulatory/customer impact.</small></div>
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-send"></i> Submit NCR</button>
        <a href="<?= base_url('qms/ncr') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
