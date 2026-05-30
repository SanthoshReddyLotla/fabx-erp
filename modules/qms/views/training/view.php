<?php /** FabX ERP - Training View + Participant Roster */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-mortarboard"></i> <?= e($training['training_code']) ?></h1>
  <div class="page-actions">
    <?= status_badge($training['status']) ?>
    <a href="<?= base_url('qms/training') ?>" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card mb-4">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Training Details</h5></div>
      <div class="fx-card-body">
        <h5 class="mb-3"><?= e($training['title']) ?></h5>
        <div class="row g-3">
          <div class="col-md-4"><div class="small text-muted">Type</div><strong><?= ucfirst(str_replace('_',' ',$training['training_type'])) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Mode</div><strong><?= ucfirst($training['training_mode']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Duration</div><strong><?= $training['duration_hours']?$training['duration_hours'].' hrs':'—' ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Trainer</div><strong><?= e($training['trainer_name']??$training['external_trainer']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Start</div><strong><?= format_date($training['start_date']) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">End</div><strong><?= format_date($training['end_date']) ?></strong></div>
          <?php if ($training['description']): ?><div class="col-12"><div class="small text-muted mb-1">Objectives</div><div class="bg-light rounded p-2"><?= nl2br(e($training['description'])) ?></div></div><?php endif; ?>
        </div>
      </div>
    </div>
    <!-- Participant Roster -->
    <div class="fx-card">
      <div class="fx-card-header d-flex justify-content-between align-items-center">
        <h5 class="fx-card-title"><i class="bi bi-people"></i> Participant Roster (<?= count($participants) ?>)</h5>
      </div>
      <form method="POST" action="<?= base_url('qms/training/participants/'.$training['id']) ?>">
        <?= csrf_field() ?>
        <?php $enrolled = array_column($participants, null, 'employee_id'); ?>
        <div class="fx-card-body p-0">
          <div class="table-responsive-fx">
            <table class="fx-table mb-0">
              <thead><tr><th><input type="checkbox" id="selectAll" onchange="document.querySelectorAll('.empCheck').forEach(c=>c.checked=this.checked)"></th><th>Employee</th><th>Attendance</th><th>Score</th><th>Result</th><th>Certificate</th><th>Cert No</th></tr></thead>
              <tbody>
                <?php foreach ($all_employees as $emp):
                  $p = $enrolled[$emp['id']] ?? null;
                  $checked = $p !== null ? 'checked' : '';
                ?>
                <tr>
                  <td><input type="checkbox" class="empCheck" name="participants[<?= $emp['id'] ?>][include]" value="1" <?= $checked ?>></td>
                  <td><?= e($emp['name']) ?> <small class="text-muted"><?= e($emp['employee_code']??'') ?></small></td>
                  <td>
                    <select name="participants[<?= $emp['id'] ?>][attendance]" class="form-select form-select-sm" style="width:100px">
                      <?php foreach(['present'=>'Present','absent'=>'Absent','partial'=>'Partial'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($p['attendance']??'present')===$v?'selected':'' ?>><?= $l ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td><input type="number" name="participants[<?= $emp['id'] ?>][score]" class="form-control form-control-sm" style="width:80px" step="0.01" min="0" max="100" value="<?= $p['score']??'' ?>" placeholder="0-100"></td>
                  <td>
                    <select name="participants[<?= $emp['id'] ?>][result]" class="form-select form-select-sm" style="width:90px">
                      <?php foreach(['pass'=>'Pass','fail'=>'Fail','pending'=>'Pending'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($p['result']??'pending')===$v?'selected':'' ?>><?= $l ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td><input type="checkbox" name="participants[<?= $emp['id'] ?>][certificate_issued]" value="1" <?= ($p['certificate_issued']??0)?'checked':'' ?>></td>
                  <td><input type="text" name="participants[<?= $emp['id'] ?>][certificate_no]" class="form-control form-control-sm" style="width:120px" value="<?= e($p['certificate_no']??'') ?>" placeholder="Cert No"></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="fx-card-footer">
          <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save Attendance & Scores</button>
        </div>
      </form>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-bar-chart"></i> Roster Summary</h5></div>
      <div class="fx-card-body">
        <?php
        $present=count(array_filter($participants,fn($p)=>$p['attendance']==='present'));
        $passed=count(array_filter($participants,fn($p)=>$p['result']==='pass'));
        $certs=count(array_filter($participants,fn($p)=>$p['certificate_issued']));
        ?>
        <div class="d-flex justify-content-between mb-2"><span>Total Enrolled</span><strong><?= count($participants) ?></strong></div>
        <div class="d-flex justify-content-between mb-2"><span class="text-success">Present</span><strong><?= $present ?></strong></div>
        <div class="d-flex justify-content-between mb-2"><span class="text-primary">Passed</span><strong><?= $passed ?></strong></div>
        <div class="d-flex justify-content-between"><span>Certs Issued</span><strong><?= $certs ?></strong></div>
      </div>
    </div>
  </div>
</div>
