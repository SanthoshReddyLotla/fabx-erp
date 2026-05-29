<?php /** FabX ERP - Schedule Training */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-calendar-plus"></i> Schedule Training Programme</h1>
  <a href="<?= base_url('qms/training') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/training/create') ?>">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-mortarboard"></i> Training Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Training Code</label>
              <input type="text" name="training_code" class="form-control" placeholder="Auto-generated if blank">
            </div>
            <div class="col-md-8">
              <label class="form-label fw-semibold">Training Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" required placeholder="e.g. Welding Safety & Procedures">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Training Type <span class="text-danger">*</span></label>
              <select name="training_type" class="form-select" required>
                <option value="induction">Induction</option>
                <option value="on_job">On-the-Job</option>
                <option value="safety">Safety</option>
                <option value="quality">Quality</option>
                <option value="technical">Technical</option>
                <option value="soft_skill">Soft Skills</option>
                <option value="compliance">Compliance</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Training Mode</label>
              <select name="training_mode" class="form-select">
                <option value="classroom">Classroom</option>
                <option value="online">Online</option>
                <option value="on_job">On-the-Job</option>
                <option value="workshop">Workshop</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Internal Trainer</label>
              <select name="trainer_id" class="form-select">
                <option value="">None / External</option>
                <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">External Trainer / Agency</label>
              <input type="text" name="external_trainer" class="form-control" placeholder="If using external trainer">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Start Date</label>
              <input type="date" name="start_date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">End Date</label>
              <input type="date" name="end_date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Duration (Hours)</label>
              <input type="number" name="duration_hours" class="form-control" step="0.5" placeholder="e.g. 8">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Venue</label>
              <input type="text" name="venue" class="form-control" placeholder="Training venue / location">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Department</label>
              <select name="department_id" class="form-select">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?><option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description / Objectives</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Training objectives and content overview..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Schedule Training</button>
        <a href="<?= base_url('qms/training') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
