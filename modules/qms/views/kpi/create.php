<?php /** FabX ERP - Create KPI */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-speedometer2"></i> Add KPI / Quality Objective</h1>
  <a href="<?= base_url('qms/kpi') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/kpi/create') ?>">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> KPI Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">KPI Code</label>
              <input type="text" name="kpi_code" class="form-control" placeholder="e.g. KPI-001">
            </div>
            <div class="col-md-8">
              <label class="form-label fw-semibold">KPI Name <span class="text-danger">*</span></label>
              <input type="text" name="kpi_name" class="form-control" required placeholder="e.g. Customer Complaint Rate">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Quality Objective / Description</label>
              <textarea name="objective" class="form-control" rows="2" placeholder="Describe the quality objective this KPI supports..."></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Target Value <span class="text-danger">*</span></label>
              <input type="number" name="target_value" class="form-control" step="0.01" required placeholder="e.g. 100">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Actual Value</label>
              <input type="number" name="actual_value" class="form-control" step="0.01" placeholder="Current actual">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Unit</label>
              <input type="text" name="unit" class="form-control" placeholder="e.g. %, ppm, hrs">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Measurement Frequency</label>
              <select name="frequency" class="form-select">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="half_yearly">Half-Yearly</option>
                <option value="yearly">Yearly</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Department</label>
              <select name="department_id" class="form-select">
                <option value="">All Departments</option>
                <?php foreach($departments as $d): ?><option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Responsible Person</label>
              <select name="responsible_person" class="form-select">
                <option value="">Unassigned</option>
                <?php foreach($users as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Year</label>
              <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" min="2020" max="2035">
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Quarter</label>
              <select name="quarter" class="form-select">
                <option value="0">—</option>
                <?php for($q=1;$q<=4;$q++): ?><option value="<?= $q ?>">Q<?= $q ?></option><?php endfor; ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Month</label>
              <select name="month" class="form-select">
                <option value="0">—</option>
                <?php for($m=1;$m<=12;$m++): ?><option value="<?= $m ?>"><?= date('M',mktime(0,0,0,$m,1)) ?></option><?php endfor; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Remarks</label>
              <textarea name="remarks" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="fx-card mb-3">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Status Thresholds</h5></div>
        <div class="fx-card-body">
          <div class="mb-2 p-2 rounded" style="background:rgba(39,174,96,.1)"><span class="badge bg-success me-1">On Track</span><small>Achievement ≥ 90%</small></div>
          <div class="mb-2 p-2 rounded" style="background:rgba(243,156,18,.1)"><span class="badge bg-warning text-dark me-1">At Risk</span><small>Achievement 60–89%</small></div>
          <div class="p-2 rounded" style="background:rgba(231,76,60,.1)"><span class="badge bg-danger me-1">Off Track</span><small>Achievement &lt; 60%</small></div>
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save KPI</button>
        <a href="<?= base_url('qms/kpi') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
