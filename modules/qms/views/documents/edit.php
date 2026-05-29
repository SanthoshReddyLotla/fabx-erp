<?php /** FabX ERP - Edit Document */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-pencil-square"></i> Edit Document</h1>
  <a href="<?= base_url('qms/documents/view/'.$document['id']) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/documents/edit/'.$document['id']) ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-file-earmark-text"></i> <?= e($document['doc_code']) ?></h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Document Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" value="<?= e($document['title']) ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Version</label>
              <input type="text" class="form-control bg-light" value="<?= e($document['version']) ?> (Rev <?= $document['revision_no'] ?>)" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Category</label>
              <select name="category_id" class="form-select">
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= $document['category_id']==$cat['id']?'selected':'' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Department</label>
              <select name="department_id" class="form-select">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                  <option value="<?= $dept['id'] ?>" <?= $document['department_id']==$dept['id']?'selected':'' ?>><?= e($dept['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control" rows="4"><?= e($document['description']) ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Change Notes (for revision history)</label>
              <textarea name="change_notes" class="form-control" rows="2" placeholder="Describe what changed in this revision..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="fx-card mb-3">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-arrow-left-right"></i> Status Workflow</h5></div>
        <div class="fx-card-body">
          <?php
          $currentStatus = $document['status'];
          $transitions = ['draft'=>'under_review','under_review'=>'approved','approved'=>'obsolete'];
          $statusLabels = ['draft'=>'Draft','under_review'=>'Under Review','approved'=>'Approved','obsolete'=>'Obsolete'];
          ?>
          <p class="mb-2">Current: <strong><?= $statusLabels[$currentStatus] ?? ucfirst($currentStatus) ?></strong></p>
          <select name="status" class="form-select mb-3">
            <?php foreach ($statusLabels as $val => $label): ?>
              <option value="<?= $val ?>" <?= $currentStatus===$val?'selected':'' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($transitions[$currentStatus])): ?>
            <div class="alert alert-info small py-2">
              <i class="bi bi-info-circle"></i> Next workflow step: <strong><?= $statusLabels[$transitions[$currentStatus]] ?></strong>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="fx-card mb-3">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-upload"></i> Replace File</h5></div>
        <div class="fx-card-body">
          <?php if ($document['file_path']): ?>
            <p class="small text-muted mb-2"><i class="bi bi-paperclip"></i> Current file attached</p>
          <?php endif; ?>
          <input type="file" name="document_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
          <div class="form-text">Upload to replace existing file.</div>
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save Changes</button>
        <a href="<?= base_url('qms/documents/view/'.$document['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
