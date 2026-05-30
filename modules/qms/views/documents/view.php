<?php /** FabX ERP - View Document */
$history = json_decode($document['change_history'] ?? '[]', true) ?: [];
$statusClass = match($document['status']) {
  'approved' => 'badge-fx-success', 'under_review' => 'badge-fx-warning',
  'draft' => 'badge-fx-secondary', 'obsolete' => 'badge-fx-danger', default => 'badge-fx-secondary'
};
?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-file-earmark-text"></i> <?= e($document['doc_code']) ?></h1>
  <div class="page-actions">
    <?php if ($document['file_path']): ?>
      <a href="<?= base_url($document['file_path']) ?>" class="btn btn-outline-primary" target="_blank"><i class="bi bi-download"></i> Download</a>
    <?php endif; ?>
    <a href="<?= base_url('qms/documents/edit/'.$document['id']) ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-pencil"></i> Edit</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card mb-4">
      <div class="fx-card-header d-flex justify-content-between align-items-center">
        <h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Document Information</h5>
        <span class="badge-fx <?= $statusClass ?>"><?= ucfirst(str_replace('_',' ',$document['status'])) ?></span>
      </div>
      <div class="fx-card-body">
        <h4 class="mb-1"><?= e($document['title']) ?></h4>
        <p class="text-muted mb-3"><?= e($document['description'] ?: '—') ?></p>
        <div class="row g-3 text-sm">
          <div class="col-6 col-md-3"><div class="text-muted small">Version</div><strong><?= e($document['version']) ?></strong></div>
          <div class="col-6 col-md-3"><div class="text-muted small">Revision</div><strong><?= $document['revision_no'] ?></strong></div>
          <div class="col-6 col-md-3"><div class="text-muted small">Category</div><strong><?= e($document['category_name'] ?? '—') ?></strong></div>
          <div class="col-6 col-md-3"><div class="text-muted small">Department</div><strong><?= e($document['department_name'] ?? '—') ?></strong></div>
          <div class="col-6 col-md-3"><div class="text-muted small">Effective Date</div><strong><?= format_date($document['effective_date']) ?></strong></div>
          <div class="col-6 col-md-3"><div class="text-muted small">Expiry Date</div><strong><?= format_date($document['expiry_date']) ?></strong></div>
          <div class="col-6 col-md-3"><div class="text-muted small">Prepared By</div><strong><?= e($document['prepared_by_name'] ?? '—') ?></strong></div>
          <div class="col-6 col-md-3"><div class="text-muted small">Approved By</div><strong><?= e($document['approved_by_name'] ?? '—') ?></strong></div>
        </div>
      </div>
    </div>
    <?php if (!empty($history)): ?>
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-clock-history"></i> Revision History</h5></div>
      <div class="fx-card-body p-0">
        <table class="fx-table mb-0">
          <thead><tr><th>Date</th><th>Status Change</th><th>Revision</th><th>Notes</th></tr></thead>
          <tbody>
            <?php foreach (array_reverse($history) as $h): ?>
            <tr>
              <td><?= format_datetime($h['date']) ?></td>
              <td><span class="badge bg-secondary"><?= ucfirst(str_replace('_',' ',$h['status'])) ?></span></td>
              <td>Rev <?= (int)$h['revision'] ?></td>
              <td><?= e($h['notes'] ?: '—') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <div class="col-lg-4">
    <div class="fx-card mb-3">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-arrow-left-right"></i> Change Status</h5></div>
      <div class="fx-card-body">
        <form method="POST" action="<?= base_url('qms/documents/status/'.$document['id']) ?>">
          <?= csrf_field() ?>
          <select name="status" class="form-select mb-2">
            <?php foreach (['draft'=>'Draft','under_review'=>'Under Review','approved'=>'Approved','obsolete'=>'Obsolete'] as $v => $l): ?>
              <option value="<?= $v ?>" <?= $document['status']===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
          <textarea name="change_notes" class="form-control mb-2" rows="2" placeholder="Reason / change notes..."></textarea>
          <button type="submit" class="btn btn-fx btn-fx-primary w-100"><i class="bi bi-check2-circle"></i> Update Status</button>
        </form>
      </div>
    </div>
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-shield-check"></i> Approval Chain</h5></div>
      <div class="fx-card-body">
        <div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-person-fill text-primary"></i><div><div class="small text-muted">Prepared By</div><strong><?= e($document['prepared_by_name'] ?? '—') ?></strong></div></div>
        <div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-search text-warning"></i><div><div class="small text-muted">Reviewed By</div><strong><?= e($document['reviewed_by_name'] ?? 'Pending') ?></strong></div></div>
        <div class="d-flex align-items-center gap-2"><i class="bi bi-patch-check-fill text-success"></i><div><div class="small text-muted">Approved By</div><strong><?= e($document['approved_by_name'] ?? 'Pending') ?></strong></div></div>
      </div>
    </div>
  </div>
</div>
