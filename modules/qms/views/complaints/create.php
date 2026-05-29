<?php /** FabX ERP - Log Complaint */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-chat-left-text"></i> Log Customer Complaint</h1>
  <a href="<?= base_url('qms/complaints') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/complaints/create') ?>">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Complaint Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Complaint Date <span class="text-danger">*</span></label>
              <input type="date" name="complaint_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Source <span class="text-danger">*</span></label>
              <select name="source" class="form-select" required>
                <option value="">Select Source</option>
                <?php foreach(['email'=>'Email','phone'=>'Phone','letter'=>'Letter','verbal'=>'Verbal','site_visit'=>'Site Visit','audit'=>'Audit'] as $v=>$l): ?>
                  <option value="<?= $v ?>"><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Severity</label>
              <select name="severity" class="form-select">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
              <select name="client_id" class="form-select" required>
                <option value="">Select Client</option>
                <?php foreach ($clients as $cl): ?><option value="<?= $cl['id'] ?>"><?= e($cl['company_name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Related Project</label>
              <select name="project_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($projects as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['project_code']) ?> — <?= e($p['project_name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Complaint Description <span class="text-danger">*</span></label>
              <textarea name="description" class="form-control" rows="4" required placeholder="Describe the customer complaint in detail..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Immediate Action Taken</label>
              <textarea name="immediate_action" class="form-control" rows="2" placeholder="Any immediate response or containment action..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-send"></i> Log Complaint</button>
        <a href="<?= base_url('qms/complaints') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
