<?php /** FabX ERP - KPI & Objectives Scorecard */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-speedometer2" style="color:#3498db"></i> KPI &amp; Quality Objectives</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/kpi/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> Add KPI</a>
  </div>
</div>
<!-- Scorecard Summary -->
<?php
$onTrack = count(array_filter($kpis, fn($k)=>($k['status']??'')==='on_track'));
$atRisk  = count(array_filter($kpis, fn($k)=>($k['status']??'')==='at_risk'));
$offTrack= count(array_filter($kpis, fn($k)=>($k['status']??'')==='off_track'));
?>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="fx-card p-3 d-flex align-items-center gap-3"><div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(39,174,96,.1)"><i class="bi bi-check-circle text-success fs-5"></i></div><div><div class="small text-muted">On Track</div><strong class="fs-4"><?= $onTrack ?></strong></div></div></div>
  <div class="col-md-4"><div class="fx-card p-3 d-flex align-items-center gap-3"><div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(243,156,18,.1)"><i class="bi bi-exclamation-triangle text-warning fs-5"></i></div><div><div class="small text-muted">At Risk</div><strong class="fs-4"><?= $atRisk ?></strong></div></div></div>
  <div class="col-md-4"><div class="fx-card p-3 d-flex align-items-center gap-3"><div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(231,76,60,.1)"><i class="bi bi-x-circle text-danger fs-5"></i></div><div><div class="small text-muted">Off Track</div><strong class="fs-4"><?= $offTrack ?></strong></div></div></div>
</div>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive-fx">
      <table class="fx-table mb-0" id="kpiTable">
        <thead><tr><th>KPI Code</th><th>KPI Name</th><th>Objective</th><th>Target</th><th>Actual</th><th>Unit</th><th>Achievement</th><th>Period</th><th>Status</th></tr></thead>
        <tbody>
          <?php if (!empty($kpis)): foreach ($kpis as $k):
            $target = (float)($k['target_value']??0);
            $actual = (float)($k['actual_value']??0);
            $pct = ($target > 0) ? round(($actual/$target)*100,1) : 0;
            $statusKey = $k['status'] ?? 'off_track';
            $pctColor = match($statusKey){'on_track'=>'#27ae60','at_risk'=>'#f39c12','off_track'=>'#e74c3c',default=>'#95a5a6'};
            $badgeClass = match($statusKey){'on_track'=>'badge-fx-success','at_risk'=>'badge-fx-warning','off_track'=>'badge-fx-danger',default=>'badge-fx-secondary'};
          ?>
          <tr>
            <td><strong><?= e($k['kpi_code']) ?></strong></td>
            <td><?= e(truncate($k['kpi_name'],35)) ?></td>
            <td class="text-muted small"><?= e(truncate($k['objective']??'',40)) ?></td>
            <td class="text-end"><?= number_format($target,2) ?></td>
            <td class="text-end"><?= number_format($actual,2) ?></td>
            <td><?= e($k['unit']??'—') ?></td>
            <td style="min-width:160px">
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:8px;border-radius:4px">
                  <div class="progress-bar" style="width:<?= min($pct,100) ?>%;background:<?= $pctColor ?>;border-radius:4px" role="progressbar"></div>
                </div>
                <span class="small fw-bold" style="color:<?= $pctColor ?>;min-width:40px"><?= $pct ?>%</span>
              </div>
            </td>
            <td>
              <?php if ($k['year']): ?>
                <span><?= $k['year'] ?></span>
                <?php if ($k['quarter']): ?><span class="badge bg-secondary ms-1">Q<?= $k['quarter'] ?></span><?php endif; ?>
                <?php if ($k['month']): ?><span class="badge bg-secondary ms-1">M<?= $k['month'] ?></span><?php endif; ?>
              <?php else: ?>—<?php endif; ?>
            </td>
            <td><span class="badge-fx <?= $badgeClass ?>"><?= ucfirst(str_replace('_',' ',$statusKey)) ?></span></td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="9"><div class="empty-state"><i class="bi bi-speedometer2"></i><h5>No KPIs defined</h5><p class="mb-3">Define quality objectives and track performance.</p><a href="<?= base_url('qms/kpi/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add KPI</a></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
