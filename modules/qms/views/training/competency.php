<?php /** FabX ERP - Competency Matrix Grid */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-grid-3x3-gap"></i> Competency Matrix</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/training') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="filters-bar">
  <form class="d-flex gap-2">
    <select name="department_id" class="form-select" style="width:200px" onchange="this.form.submit()">
      <option value="">All Departments</option>
      <?php foreach ($departments as $d): ?>
        <option value="<?= $d['id'] ?>" <?= ($dept_filter==$d['id'])?'selected':'' ?>><?= e($d['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>
<?php
$gapColors = ['none'=>'#27ae60','minor'=>'#f39c12','significant'=>'#e67e22','critical'=>'#e74c3c'];
$gapLabels = ['none'=>'Meets/Exceeds','minor'=>'Minor Gap (-1)','significant'=>'Significant Gap (-2)','critical'=>'Critical Gap (>-2)'];
?>
<!-- Legend -->
<div class="fx-card mb-3">
  <div class="fx-card-body py-2">
    <div class="d-flex gap-3 align-items-center flex-wrap">
      <span class="small text-muted fw-semibold">Gap Legend:</span>
      <?php foreach ($gapColors as $gap => $color): ?>
        <div class="d-flex align-items-center gap-1">
          <div style="width:14px;height:14px;border-radius:3px;background:<?= $color ?>;opacity:.8"></div>
          <span class="small"><?= $gapLabels[$gap] ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php if (empty($employees)): ?>
<div class="fx-card"><div class="fx-card-body p-5 text-center text-muted"><i class="bi bi-grid display-3 mb-3"></i><h5>No competency data found</h5><p>Add competency records via HR module.</p></div></div>
<?php else: ?>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive" style="max-height:70vh;overflow:auto">
      <table class="table table-bordered table-sm mb-0" style="min-width:600px">
        <thead class="table-dark sticky-top">
          <tr>
            <th style="min-width:180px;position:sticky;left:0;background:#212529;z-index:5">Employee</th>
            <th style="min-width:100px;position:sticky;left:180px;background:#212529;z-index:5">Department</th>
            <?php foreach ($skills as $skill): ?>
              <th style="min-width:120px;text-align:center;white-space:nowrap"><?= e($skill) ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($employees as $empId => $emp): ?>
          <tr>
            <td style="position:sticky;left:0;background:#fff;z-index:2;font-weight:600">
              <?= e($emp['name']) ?><br><small class="text-muted"><?= e($emp['code']??'') ?></small>
            </td>
            <td style="position:sticky;left:180px;background:#fff;z-index:2"><small><?= e($emp['dept']??'—') ?></small></td>
            <?php foreach ($skills as $skill):
              $skillData = $emp['skills'][$skill] ?? null;
              $gap = $skillData['gap'] ?? null;
              $bgColor = $gap ? ($gapColors[$gap].'22') : '#f8f9fa';
              $borderColor = $gap ? $gapColors[$gap] : '#dee2e6';
              $req = $skillData['required_level'] ?? '—';
              $act = $skillData['actual_level'] ?? '—';
            ?>
            <td style="text-align:center;background:<?= $bgColor ?>;border-color:<?= $borderColor ?>;border-width:2px" 
                title="<?= e($skill) ?> — Required: <?= $req ?>, Actual: <?= $act ?>, Gap: <?= ucfirst($gap??'N/A') ?>">
              <?php if ($skillData): ?>
                <div style="color:<?= $gapColors[$gap]??'#666' ?>;font-weight:700"><?= $act ?>/<?= $req ?></div>
                <small style="color:<?= $gapColors[$gap]??'#666' ?>"><?= ucfirst($gap) ?></small>
              <?php else: ?>
                <span class="text-muted small">N/A</span>
              <?php endif; ?>
            </td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>
