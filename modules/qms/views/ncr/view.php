<?php /** FabX ERP - NCR Details */
$severityClass = match($ncr['severity']) {
  'critical' => 'danger', 'major' => 'warning', default => 'success'
};
$statusFlow = ['open','in_progress','pending_verification','closed'];
?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-exclamation-octagon text-danger"></i> <?= e($ncr['ncr_no']) ?></h1>
  <div class="page-actions">
    <span class="badge bg-<?= $severityClass ?> me-2"><?= ucfirst($ncr['severity']) ?></span>
    <?= status_badge($ncr['status']) ?>
    <a href="<?= base_url('qms/ncr') ?>" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card mb-4">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> NCR Information</h5></div>
      <div class="fx-card-body">
        <div class="row g-3">
          <div class="col-md-3"><div class="small text-muted">NCR No</div><strong><?= e($ncr['ncr_no']) ?></strong></div>
          <div class="col-md-3"><div class="small text-muted">Date</div><strong><?= format_date($ncr['ncr_date']) ?></strong></div>
          <div class="col-md-3"><div class="small text-muted">Source</div><strong><?= ucfirst(str_replace('_',' ',$ncr['source'])) ?></strong></div>
          <div class="col-md-3"><div class="small text-muted">Category</div><strong><?= ucfirst($ncr['category']) ?></strong></div>
          <div class="col-md-6"><div class="small text-muted">Department</div><strong><?= e($ncr['department_name'] ?? '—') ?></strong></div>
          <div class="col-md-6"><div class="small text-muted">Reported By</div><strong><?= e($ncr['reported_by_name'] ?? '—') ?></strong></div>
          <div class="col-12"><div class="small text-muted mb-1">Description</div><p class="bg-light rounded p-3 mb-0"><?= nl2br(e($ncr['description'])) ?></p></div>
          <?php if ($ncr['immediate_action']): ?>
          <div class="col-12"><div class="small text-muted mb-1">Immediate Action Taken</div><p class="bg-light rounded p-3 mb-0"><?= nl2br(e($ncr['immediate_action'])) ?></p></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-wrench-adjustable"></i> Corrective Action Loop</h5></div>
      <div class="fx-card-body">
        <form method="POST" action="<?= base_url('qms/ncr/update/'.$ncr['id']) ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="status" value="<?= in_array($ncr['status'],['open','in_progress'])?'in_progress':$ncr['status'] ?>">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Root Cause Analysis <span class="text-danger">*</span></label>
              <textarea name="root_cause" class="form-control" rows="3" placeholder="Analyse why this non-conformance occurred..."><?= e($ncr['root_cause']) ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Corrective Action</label>
              <textarea name="corrective_action" class="form-control" rows="3" placeholder="What corrective action will be taken?"><?= e($ncr['corrective_action']) ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Preventive Action</label>
              <textarea name="preventive_action" class="form-control" rows="2" placeholder="How will recurrence be prevented?"><?= e($ncr['preventive_action'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Target Closure Date</label>
              <input type="date" name="target_date" class="form-control" value="<?= $ncr['target_date'] ?? '' ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select name="status" class="form-select">
                <?php foreach (['open'=>'Open','in_progress'=>'In Progress','pending_verification'=>'Pending Verification','closed'=>'Closed'] as $v=>$l): ?>
                  <option value="<?= $v ?>" <?= $ncr['status']===$v?'selected':'' ?>><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save Updates</button>
            <button type="submit" name="create_capa" value="1" class="btn btn-outline-warning"><i class="bi bi-arrow-repeat"></i> Save & Create CAPA</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="fx-card mb-3">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-diagram-3"></i> Status Progress</h5></div>
      <div class="fx-card-body">
        <?php $flowSteps = ['open'=>'Open','in_progress'=>'In Progress','pending_verification'=>'Pending Verify','closed'=>'Closed'];
        $active = false; ?>
        <?php foreach ($flowSteps as $step => $label): ?>
          <?php $isDone = (array_search($step, array_keys($flowSteps)) < array_search($ncr['status'], array_keys($flowSteps))); $isCurrent = ($step === $ncr['status']); ?>
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:<?= $isCurrent?'var(--fx-accent)':($isDone?'#27ae60':'#dee2e6') ?>;flex-shrink:0">
              <i class="bi bi-<?= $isCurrent?'circle-fill':($isDone?'check-lg':'circle') ?> text-white small"></i>
            </div>
            <span class="<?= $isCurrent?'fw-bold':'' ?>"><?= $label ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-people"></i> Assignment</h5></div>
      <div class="fx-card-body">
        <div class="mb-2"><span class="small text-muted">Project</span><br><strong><?= e($ncr['project_name'] ?? 'None') ?></strong></div>
        <div class="mb-2"><span class="small text-muted">Target Date</span><br><strong><?= format_date($ncr['target_date']) ?></strong></div>
        <div><span class="small text-muted">Completion Date</span><br><strong><?= format_date($ncr['completion_date']) ?></strong></div>
      </div>
    </div>
  </div>
</div>
