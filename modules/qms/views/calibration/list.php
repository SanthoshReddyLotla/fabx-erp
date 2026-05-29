<?php /** FabX ERP - Calibration Tracking */
$today = new DateTime();
?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-thermometer" style="color:#e67e22"></i> Calibration Tracking</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/calibration/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> Add Equipment</a>
  </div>
</div>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="fx-card p-3 d-flex align-items-center gap-3"><div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(39,174,96,.1)"><i class="bi bi-check-circle text-success fs-5"></i></div><div><div class="small text-muted">Active Equipment</div><strong class="fs-5"><?= count(array_filter($calibrations,fn($c)=>$c['status']==='active')) ?></strong></div></div></div>
  <div class="col-md-4"><div class="fx-card p-3 d-flex align-items-center gap-3"><div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(243,156,18,.1)"><i class="bi bi-clock-history text-warning fs-5"></i></div><div><div class="small text-muted">Due Within 30 Days</div><strong class="fs-5"><?= count(array_filter($calibrations,fn($c)=>$c['next_calibration_date']&&(new DateTime($c['next_calibration_date']))->diff(new DateTime())->days<=30&&strtotime($c['next_calibration_date'])>=time())) ?></strong></div></div></div>
  <div class="col-md-4"><div class="fx-card p-3 d-flex align-items-center gap-3"><div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(231,76,60,.1)"><i class="bi bi-exclamation-triangle text-danger fs-5"></i></div><div><div class="small text-muted">Overdue</div><strong class="fs-5"><?= count(array_filter($calibrations,fn($c)=>$c['next_calibration_date']&&strtotime($c['next_calibration_date'])<time())) ?></strong></div></div></div>
</div>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive-fx">
      <table class="fx-table mb-0" id="calibrationTable">
        <thead><tr><th>Equipment ID</th><th>Equipment Name</th><th>Range</th><th>Accuracy</th><th>Frequency</th><th>Last Calibrated</th><th>Next Due</th><th>Calibrated By</th><th>Status</th><th class="actions">Actions</th></tr></thead>
        <tbody>
          <?php if (!empty($calibrations)): foreach ($calibrations as $c):
            $nextDate = $c['next_calibration_date'] ? new DateTime($c['next_calibration_date']) : null;
            $daysUntil = $nextDate ? (int)$today->diff($nextDate)->days * ($nextDate > $today ? 1 : -1) : null;
            $isOverdue = $daysUntil !== null && $daysUntil < 0;
            $isDueSoon = $daysUntil !== null && $daysUntil >= 0 && $daysUntil <= 30;
            $rowClass = $isOverdue ? 'table-danger' : ($isDueSoon ? 'table-warning' : '');
          ?>
          <tr class="<?= $rowClass ?>">
            <td><strong><?= e($c['equipment_id']) ?></strong></td>
            <td><?= e($c['equipment_name']) ?></td>
            <td><?= e($c['range_value']??'—') ?></td>
            <td><?= e($c['accuracy']??'—') ?></td>
            <td><?= ucfirst(str_replace('_',' ',$c['frequency']??'—')) ?></td>
            <td><?= format_date($c['last_calibration_date']) ?></td>
            <td>
              <?php if ($nextDate): ?>
                <span class="badge <?= $isOverdue?'bg-danger':($isDueSoon?'bg-warning text-dark':'bg-success') ?>">
                  <?= format_date($c['next_calibration_date']) ?>
                  <?php if ($isOverdue): ?> (<?= abs($daysUntil) ?>d overdue)<?php elseif ($isDueSoon): ?> (<?= $daysUntil ?>d)<?php endif; ?>
                </span>
              <?php else: ?><span class="text-muted">—</span><?php endif; ?>
            </td>
            <td><?= e($c['calibrated_by']??'—') ?></td>
            <td>
              <?php $sClass=match($c['status']??'active'){'active'=>'badge-fx-success','due'=>'badge-fx-warning','overdue'=>'badge-fx-danger',default=>'badge-fx-secondary'}; ?>
              <span class="badge-fx <?= $sClass ?>"><?= ucfirst(str_replace('_',' ',$c['status']??'active')) ?></span>
            </td>
            <td class="actions">
              <a href="<?= base_url('qms/calibration/view/'.$c['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="10"><div class="empty-state"><i class="bi bi-thermometer"></i><h5>No equipment records</h5><p class="mb-3">Add your first calibration record.</p><a href="<?= base_url('qms/calibration/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Equipment</a></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
