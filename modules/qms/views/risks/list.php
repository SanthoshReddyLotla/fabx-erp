<?php /** FabX ERP - Risk Assessment Register */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-diagram-3 text-danger"></i> Risk Assessment Register</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/risks/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> Register Risk</a>
  </div>
</div>
<!-- Risk Level Summary -->
<div class="row g-3 mb-4">
  <?php $levels=['low'=>['Low','success','≤4'],'medium'=>['Medium','warning','≤9'],'high'=>['High','orange','≤16'],'extreme'=>['Extreme','danger','≥20']];
  foreach($levels as $lvl=>[$label,$color,$range]): $cnt=count(array_filter($risks,fn($r)=>$r['risk_level']===$lvl)); ?>
  <div class="col-md-3"><div class="fx-card p-3 text-center border-<?= $color ?>" style="border-top:3px solid"><div class="badge bg-<?= $color ?> mb-1"><?= $label ?> Risk <?= $range ?></div><div class="fs-4 fw-bold"><?= $cnt ?></div></div></div>
  <?php endforeach; ?>
</div>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive-fx">
      <table class="fx-table mb-0" id="risksTable">
        <thead><tr><th>Risk No</th><th>Category</th><th>Description</th><th>Prob</th><th>Impact</th><th>Score</th><th>Level</th><th>Owner</th><th>Status</th><th class="actions">Actions</th></tr></thead>
        <tbody>
          <?php if (!empty($risks)): foreach ($risks as $r):
            $rScore = (int)$r['risk_score'];
            $rLevel = $r['risk_level']??'low';
            $scoreStyle = match($rLevel){'extreme'=>'background:#e74c3c;color:#fff','high'=>'background:#e67e22;color:#fff','medium'=>'background:#f39c12;color:#fff',default=>'background:#27ae60;color:#fff'};
          ?>
          <tr>
            <td><strong><?= e($r['risk_no']) ?></strong></td>
            <td><span class="badge bg-secondary"><?= ucfirst($r['category']) ?></span></td>
            <td><?= e(truncate($r['description'],50)) ?></td>
            <td class="text-center"><?= $r['probability'] ?></td>
            <td class="text-center"><?= $r['impact'] ?></td>
            <td class="text-center"><span class="badge" style="<?= $scoreStyle ?>"><?= $rScore ?></span></td>
            <td><span class="badge" style="<?= $scoreStyle ?>"><?= ucfirst($rLevel) ?></span></td>
            <td><?= e($r['risk_owner_name']??'—') ?></td>
            <td><?= status_badge($r['status']) ?></td>
            <td class="actions">
              <a href="<?= base_url('qms/risks/view/'.$r['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="10"><div class="empty-state"><i class="bi bi-diagram-3"></i><h5>No risks registered</h5><p class="mb-3">Register and track operational risks.</p><a href="<?= base_url('qms/risks/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Register Risk</a></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
