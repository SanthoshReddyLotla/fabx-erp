<?php /** FabX ERP - CAPA View */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-arrow-repeat text-warning"></i> <?= e($capa['capa_no']) ?></h1>
  <div class="page-actions">
    <?= status_badge($capa['status']) ?>
    <a href="<?= base_url('qms/capa') ?>" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card mb-4">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> CAPA Details</h5></div>
      <div class="fx-card-body">
        <div class="row g-3">
          <div class="col-md-4"><div class="small text-muted">CAPA No</div><strong><?= e($capa['capa_no']) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Source Type</div><strong><?= ucfirst(str_replace('_',' ',$capa['source_type'])) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Method</div><strong><?= ucfirst(str_replace('_',' ',$capa['root_cause_method']??'—')) ?></strong></div>
          <div class="col-md-6"><div class="small text-muted">Responsible</div><strong><?= e($capa['responsible_name']??'—') ?></strong></div>
          <div class="col-md-3"><div class="small text-muted">Target Date</div><strong><?= format_date($capa['target_date']) ?></strong></div>
          <div class="col-md-3"><div class="small text-muted">Impl. Date</div><strong><?= format_date($capa['implementation_date']) ?></strong></div>
          <div class="col-12"><div class="small text-muted mb-1">Description</div><div class="bg-light rounded p-3"><?= nl2br(e($capa['description'])) ?></div></div>
          <div class="col-12"><div class="small text-muted mb-1">Root Cause Analysis</div><div class="bg-light rounded p-3"><?= nl2br(e($capa['root_cause_analysis']??'—')) ?></div></div>
          <div class="col-12"><div class="small text-muted mb-1">Corrective Action</div><div class="bg-light rounded p-3"><?= nl2br(e($capa['corrective_action']??'—')) ?></div></div>
          <div class="col-12"><div class="small text-muted mb-1">Preventive Action</div><div class="bg-light rounded p-3"><?= nl2br(e($capa['preventive_action']??'—')) ?></div></div>
        </div>
      </div>
    </div>
    <!-- Effectiveness Audit Panel -->
    <div class="fx-card">
      <div class="fx-card-header d-flex justify-content-between align-items-center">
        <h5 class="fx-card-title"><i class="bi bi-patch-check"></i> Effectiveness Verification</h5>
        <?php if ($capa['effectiveness_result']): ?>
          <?php $effClass = match($capa['effectiveness_result']) {'effective'=>'badge-fx-success','partially_effective'=>'badge-fx-warning',default=>'badge-fx-danger'}; ?>
          <span class="badge-fx <?= $effClass ?>"><?= ucfirst(str_replace('_',' ',$capa['effectiveness_result'])) ?></span>
        <?php endif; ?>
      </div>
      <div class="fx-card-body">
        <?php if ($capa['effectiveness_result']): ?>
          <div class="row g-3 mb-3">
            <div class="col-md-4"><div class="small text-muted">Verified By</div><strong><?= e($capa['verified_by_name']??'—') ?></strong></div>
            <div class="col-md-4"><div class="small text-muted">Verification Date</div><strong><?= format_date($capa['effectiveness_date']) ?></strong></div>
            <div class="col-12"><div class="small text-muted mb-1">Effectiveness Check Notes</div><div class="bg-light rounded p-3"><?= nl2br(e($capa['effectiveness_check']??'—')) ?></div></div>
          </div>
        <?php endif; ?>
        <?php if ($capa['status'] !== 'closed'): ?>
        <form method="POST" action="<?= base_url('qms/capa/effectiveness/'.$capa['id']) ?>">
          <?= csrf_field() ?>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Effectiveness Check Notes <span class="text-danger">*</span></label>
              <textarea name="effectiveness_check" class="form-control" rows="3" required placeholder="Describe what checks were performed to verify effectiveness..."><?= e($capa['effectiveness_check']??'') ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Effectiveness Result <span class="text-danger">*</span></label>
              <select name="effectiveness_result" class="form-select" required>
                <option value="">Select Result</option>
                <option value="effective" <?= ($capa['effectiveness_result']??'')==='effective'?'selected':'' ?>>✅ Effective</option>
                <option value="partially_effective" <?= ($capa['effectiveness_result']??'')==='partially_effective'?'selected':'' ?>>⚠️ Partially Effective</option>
                <option value="not_effective" <?= ($capa['effectiveness_result']??'')==='not_effective'?'selected':'' ?>>❌ Not Effective</option>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="submit" class="btn btn-fx btn-fx-primary w-100"><i class="bi bi-patch-check"></i> Save Verification</button>
            </div>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-diagram-3"></i> CAPA Progress</h5></div>
      <div class="fx-card-body">
        <?php $steps=['open'=>'Open','in_progress'=>'In Progress','implemented'=>'Implemented','verified'=>'Verified','closed'=>'Closed'];
        $order=array_keys($steps); $curIdx=array_search($capa['status'],$order); ?>
        <?php foreach ($steps as $step=>$label): $idx=array_search($step,$order); $done=$idx<$curIdx; $cur=$step===$capa['status']; ?>
        <div class="d-flex align-items-center gap-2 mb-2">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:<?= $cur?'var(--fx-accent)':($done?'#27ae60':'#dee2e6') ?>;flex-shrink:0">
            <i class="bi bi-<?= $cur?'circle-fill':($done?'check-lg':'circle') ?> text-white small"></i>
          </div>
          <span class="<?= $cur?'fw-bold':'' ?>"><?= $label ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
