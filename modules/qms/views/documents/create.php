<?php /** FabX ERP - Create Document */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-file-earmark-plus"></i> Create Document</h1>
  <a href="<?= base_url('qms/documents') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/documents/create') ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Document Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Doc Code <span class="text-muted small">(auto-generated if blank)</span></label>
              <input type="text" name="doc_code" class="form-control" placeholder="e.g. SOP-QMS-001">
            </div>
            <div class="col-md-8">
              <label class="form-label fw-semibold">Document Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" required placeholder="Enter document title">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
              <select name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Department</label>
              <select name="department_id" class="form-select">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                  <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Effective Date</label>
              <input type="date" name="effective_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Expiry / Review Date</label>
              <input type="date" name="expiry_date" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description / Scope</label>
              <textarea name="description" class="form-control" rows="4" placeholder="Describe the document scope and purpose..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="fx-card">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-upload"></i> File Upload</h5></div>
        <div class="fx-card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Attach Document File</label>
            <input type="file" name="document_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
            <div class="form-text">PDF, Word, Excel, PPT accepted. Max 10MB.</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Keywords</label>
            <input type="text" name="keywords" class="form-control" placeholder="Comma-separated tags">
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_confidential" value="1" id="isConfidential">
            <label class="form-check-label" for="isConfidential">Mark as Confidential</label>
          </div>
        </div>
      </div>
      <div class="mt-3 d-grid gap-2">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save as Draft</button>
        <a href="<?= base_url('qms/documents') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
