<?php /** FabX ERP - Create Calibration Record */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-plus-circle"></i> Add Calibration Record</h1>
  <a href="<?= base_url('qms/calibration') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/calibration/create') ?>">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-thermometer"></i> Equipment Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Equipment ID <span class="text-danger">*</span></label>
              <input type="text" name="equipment_id" class="form-control" required placeholder="e.g. CAL-001">
            </div>
            <div class="col-md-8">
              <label class="form-label fw-semibold">Equipment Name <span class="text-danger">*</span></label>
              <input type="text" name="equipment_name" class="form-control" required placeholder="e.g. Vernier Caliper 300mm">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Manufacturer</label>
              <input type="text" name="manufacturer" class="form-control" placeholder="Brand name">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Model No</label>
              <input type="text" name="model_no" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Serial No</label>
              <input type="text" name="serial_no" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Range Value</label>
              <input type="text" name="range_value" class="form-control" placeholder="e.g. 0–300mm">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Accuracy</label>
              <input type="text" name="accuracy" class="form-control" placeholder="e.g. ±0.02mm">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Calibration Frequency</label>
              <select name="frequency" class="form-select">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="half_yearly">Half-Yearly</option>
                <option value="yearly" selected>Yearly</option>
                <option value="bi_yearly">Bi-Yearly</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Location</label>
              <input type="text" name="location" class="form-control" placeholder="Store / Workshop / Lab">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Department</label>
              <select name="department_id" class="form-select">
                <option value="">Select Department</option>
                <?php foreach ($departments as $d): ?>
                  <option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Last Calibration Date</label>
              <input type="date" name="last_calibration_date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Next Calibration Date</label>
              <input type="date" name="next_calibration_date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Certificate No</label>
              <input type="text" name="calibration_certificate_no" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Calibrated By (Lab/Agency)</label>
              <input type="text" name="calibrated_by" class="form-control" placeholder="Calibrating authority name">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Remarks</label>
              <textarea name="remarks" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="fx-card mb-3">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Alert Thresholds</h5></div>
        <div class="fx-card-body">
          <div class="mb-2 p-2 rounded" style="background:rgba(243,156,18,.1)"><span class="badge bg-warning text-dark me-1">Yellow</span><small>Due within 30 days</small></div>
          <div class="p-2 rounded" style="background:rgba(231,76,60,.1)"><span class="badge bg-danger me-1">Red</span><small>Overdue / past next due date</small></div>
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save Record</button>
        <a href="<?= base_url('qms/calibration') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
