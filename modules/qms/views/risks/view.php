<?php /** FabX ERP - Risk Detail View */
$rScore = (int)($risk['risk_score'] ?? 0);
$rLevel = $risk['risk_level'] ?? 'low';
$scoreStyle = match($rLevel){'extreme'=>'danger','high'=>'warning','medium'=>'info',default=>'success'};
?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-diagram-3"></i> <?= e($risk['risk_no']) ?></h1>
  <div class="page-actions">
    <span class="badge bg-<?= $scoreStyle ?> fs-6 me-2">Score: <?= $rScore ?> — <?= ucfirst($rLevel) ?></span>
    <?= status_badge($risk['status']) ?>
    <a href="<?= base_url('qms/risks') ?>" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Risk Details</h5></div>
      <div class="fx-card-body">
        <div class="row g-3">
          <div class="col-md-4"><div class="small text-muted">Category</div><strong><?= ucfirst($risk['category']) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Owner</div><strong><?= e($risk['risk_owner_name']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Department</div><strong><?= e($risk['department_name']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Probability</div><strong><?= $risk['probability'] ?>/5</strong></div>
          <div class="col-md-4"><div class="small text-muted">Impact</div><strong><?= $risk['impact'] ?>/5</strong></div>
          <div class="col-md-4"><div class="small text-muted">Review Date</div><strong><?= format_date($risk['review_date']) ?></strong></div>
          <div class="col-12"><div class="small text-muted mb-1">Risk Description</div><div class="bg-light rounded p-3"><?= nl2br(e($risk['description'])) ?></div></div>
          <?php if ($risk['mitigation_plan']): ?><div class="col-12"><div class="small text-muted mb-1">Mitigation Plan</div><div class="bg-light rounded p-3"><?= nl2br(e($risk['mitigation_plan'])) ?></div></div><?php endif; ?>
          <?php if ($risk['contingency_plan']): ?><div class="col-12"><div class="small text-muted mb-1">Contingency Plan</div><div class="bg-light rounded p-3"><?= nl2br(e($risk['contingency_plan'])) ?></div></div><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="fx-card text-center p-4">
      <div class="small text-muted mb-2">Risk Score</div>
      <div class="display-2 fw-bold text-<?= $scoreStyle ?>"><?= $rScore ?></div>
      <div class="badge bg-<?= $scoreStyle ?> fs-6 mt-2"><?= strtoupper($rLevel) ?> RISK</div>
      <div class="mt-3">
        <div class="row g-2 text-start">
          <div class="col-6"><small class="text-muted">Probability</small><div class="fw-bold"><?= $risk['probability'] ?>/5</div></div>
          <div class="col-6"><small class="text-muted">Impact</small><div class="fw-bold"><?= $risk['impact'] ?>/5</div></div>
        </div>
      </div>
    </div>
  </div>
</div>
