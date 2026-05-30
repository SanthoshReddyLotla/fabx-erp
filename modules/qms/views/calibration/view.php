<?php /** FabX ERP - Calibration Record View */
$today = new DateTime();
$nextDate = $calibration['next_calibration_date'] ? new DateTime($calibration['next_calibration_date']) : null;
$daysUntil = $nextDate ? (int)$today->diff($nextDate)->days * ($nextDate > $today ? 1 : -1) : null;
$isOverdue = $daysUntil !== null && $daysUntil < 0;
$isDueSoon = !$isOverdue && $daysUntil !== null && $daysUntil <= 30;
?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-thermometer"></i> <?= e($calibration['equipment_id']) ?> — <?= e($calibration['equipment_name']) ?></h1>
  <a href="<?= base_url('qms/calibration') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<?php if ($isOverdue): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>OVERDUE:</strong> This equipment's calibration is <?= abs($daysUntil) ?> days past due. Remove from service until recalibrated.</div>
<?php elseif ($isDueSoon): ?>
<div class="alert alert-warning"><i class="bi bi-clock me-2"></i> <strong>DUE SOON:</strong> Calibration due in <?= $daysUntil ?> days. Schedule recalibration now.</div>
<?php endif; ?>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Equipment Details</h5></div>
      <div class="fx-card-body">
        <div class="row g-3">
          <div class="col-md-4"><div class="small text-muted">Equipment ID</div><strong><?= e($calibration['equipment_id']) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Manufacturer</div><strong><?= e($calibration['manufacturer']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Model</div><strong><?= e($calibration['model_no']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Serial No</div><strong><?= e($calibration['serial_no']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Range</div><strong><?= e($calibration['range_value']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Accuracy</div><strong><?= e($calibration['accuracy']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Location</div><strong><?= e($calibration['location']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Department</div><strong><?= e($calibration['department_name']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Frequency</div><strong><?= ucfirst(str_replace('_',' ',$calibration['frequency']??'—')) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Last Calibrated</div><strong><?= format_date($calibration['last_calibration_date']) ?></strong></div>
          <div class="col-md-4">
            <div class="small text-muted">Next Due</div>
            <strong class="<?= $isOverdue?'text-danger':($isDueSoon?'text-warning':'') ?>">
              <?= format_date($calibration['next_calibration_date']) ?>
            </strong>
          </div>
          <div class="col-md-4"><div class="small text-muted">Certificate No</div><strong><?= e($calibration['calibration_certificate_no']??'—') ?></strong></div>
          <div class="col-12"><div class="small text-muted">Calibrated By</div><strong><?= e($calibration['calibrated_by']??'—') ?></strong></div>
          <?php if ($calibration['remarks']): ?><div class="col-12"><div class="small text-muted mb-1">Remarks</div><div class="bg-light rounded p-2"><?= nl2br(e($calibration['remarks'])) ?></div></div><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-activity"></i> Calibration Status</h5></div>
      <div class="fx-card-body text-center">
        <div class="display-4 mb-2">
          <?php if ($isOverdue): ?><i class="bi bi-exclamation-triangle text-danger"></i>
          <?php elseif ($isDueSoon): ?><i class="bi bi-clock-history text-warning"></i>
          <?php else: ?><i class="bi bi-check-circle text-success"></i><?php endif; ?>
        </div>
        <h5 class="<?= $isOverdue?'text-danger':($isDueSoon?'text-warning':'text-success') ?>">
          <?= $isOverdue?'OVERDUE':($isDueSoon?'DUE SOON':'IN CALIBRATION') ?>
        </h5>
        <?php if ($daysUntil !== null): ?>
          <p class="text-muted"><?= $isOverdue?abs($daysUntil).' days past due':$daysUntil.' days remaining' ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
