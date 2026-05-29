<?php /** FabX ERP - Create CAPA */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-arrow-repeat text-warning"></i> Create Corrective & Preventive Action</h1>
  <a href="<?= base_url('qms/capa') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/capa/create') ?>">
  <?= csrf_field() ?>
  <?php $fromNcrId = (int)($_GET['from_ncr'] ?? 0); ?>
  <?php if ($fromNcrId): ?><input type="hidden" name="source_type" value="ncr"><input type="hidden" name="source_id" value="<?= $fromNcrId ?>"><?php endif; ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card mb-4">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> CAPA Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <?php if (!$fromNcrId): ?>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Source Type <span class="text-danger">*</span></label>
              <select name="source_type" class="form-select" required>
                <option value="">Select Source</option>
                <?php foreach(['ncr'=>'NCR','audit'=>'Audit','complaint'=>'Customer Complaint','risk'=>'Risk','management_review'=>'Management Review','other'=>'Other'] as $v=>$l): ?>
                  <option value="<?= $v ?>"><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Source Reference ID</label>
              <input type="text" name="source_id" class="form-control" placeholder="Reference record ID">
            </div>
            <?php endif; ?>
            <div class="col-12">
              <label class="form-label fw-semibold">Problem / Non-Conformance Description <span class="text-danger">*</span></label>
              <textarea name="description" class="form-control" rows="3" required placeholder="Describe the problem that triggered this CAPA..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Root Cause Analysis <span class="text-danger">*</span></label>
              <textarea name="root_cause_analysis" class="form-control" rows="4" required placeholder="Detail the root cause investigation findings..."></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Root Cause Method</label>
              <select name="root_cause_method" class="form-select">
                <option value="5_why">5-Why Analysis</option>
                <option value="fishbone">Fishbone (Ishikawa)</option>
                <option value="pareto">Pareto Analysis</option>
                <option value="fmea">FMEA</option>
                <option value="other">Other</option>
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
            <div class="col-12">
              <label class="form-label fw-semibold">Corrective Action Plan</label>
              <textarea name="corrective_action" class="form-control" rows="3" placeholder="Specific actions to correct the identified non-conformance..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Preventive Action Plan</label>
              <textarea name="preventive_action" class="form-control" rows="3" placeholder="Actions to prevent recurrence of similar issues..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="fx-card mb-3">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-people"></i> Assignment</h5></div>
        <div class="fx-card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Responsible Person <span class="text-danger">*</span></label>
            <select name="responsible_person" class="form-select" required>
              <option value="">Select Person</option>
              <?php foreach($users as $u): ?>
                <option value="<?= $u['id'] ?>"><?= e($u['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Target Completion Date</label>
            <input type="date" name="target_date" class="form-control">
          </div>
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-send"></i> Submit CAPA</button>
        <a href="<?= base_url('qms/capa') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
