<?php /** FabX ERP - Register Risk with Live Score Calculator */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-diagram-3"></i> Register Risk</h1>
  <a href="<?= base_url('qms/risks') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/risks/create') ?>">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card mb-4">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Risk Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Risk Category <span class="text-danger">*</span></label>
              <select name="category" class="form-select" required>
                <option value="">Select Category</option>
                <?php foreach(['strategic'=>'Strategic','operational'=>'Operational','financial'=>'Financial','compliance'=>'Compliance','safety'=>'Safety','quality'=>'Quality','environmental'=>'Environmental'] as $v=>$l): ?>
                  <option value="<?= $v ?>"><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Department</label>
              <select name="department_id" class="form-select">
                <option value="">All Departments</option>
                <?php foreach($departments as $d): ?><option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Risk Description <span class="text-danger">*</span></label>
              <textarea name="description" class="form-control" rows="3" required placeholder="Describe the risk scenario in detail..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Mitigation Plan</label>
              <textarea name="mitigation_plan" class="form-control" rows="3" placeholder="Actions to reduce probability or impact..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Contingency Plan</label>
              <textarea name="contingency_plan" class="form-control" rows="2" placeholder="Response plan if risk materializes..."></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Risk Owner</label>
              <select name="risk_owner" class="form-select">
                <option value="">Unassigned</option>
                <?php foreach($users as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Review Date</label>
              <input type="date" name="review_date" class="form-control">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <!-- Live Risk Matrix Calculator -->
      <div class="fx-card mb-3" id="riskMatrixCard">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-calculator"></i> Risk Score Calculator</h5></div>
        <div class="fx-card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Probability (1–5)</label>
            <select name="probability" id="probSelect" class="form-select" required>
              <option value="">Select</option>
              <?php for($i=1;$i<=5;$i++): $labels=['','Very Low','Low','Moderate','High','Very High']; ?>
              <option value="<?= $i ?>"><?= $i ?> — <?= $labels[$i] ?></option>
              <?php endfor; ?>
            </select>
            <div class="form-text">Likelihood of occurrence</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Impact (1–5)</label>
            <select name="impact" id="impactSelect" class="form-select" required>
              <option value="">Select</option>
              <?php for($i=1;$i<=5;$i++): $impLabels=['','Negligible','Minor','Moderate','Major','Catastrophic']; ?>
              <option value="<?= $i ?>"><?= $i ?> — <?= $impLabels[$i] ?></option>
              <?php endfor; ?>
            </select>
            <div class="form-text">Consequence severity</div>
          </div>
          <div class="text-center p-3 rounded" id="scoreDisplay" style="background:#f8f9fa;border:2px solid #dee2e6;transition:all 0.3s">
            <div class="small text-muted mb-1">Risk Score (P × I)</div>
            <div class="display-5 fw-bold mb-1" id="scoreValue">—</div>
            <div class="badge fs-6" id="levelBadge">Select values</div>
          </div>
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Register Risk</button>
        <a href="<?= base_url('qms/risks') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
<script>
(function(){
  var probSel=document.getElementById('probSelect');
  var impSel=document.getElementById('impactSelect');
  var scoreVal=document.getElementById('scoreValue');
  var levelBadge=document.getElementById('levelBadge');
  var scoreDisplay=document.getElementById('scoreDisplay');
  function updateScore(){
    var p=parseInt(probSel.value)||0;
    var i=parseInt(impSel.value)||0;
    if(!p||!i){scoreVal.textContent='—';levelBadge.textContent='Select values';levelBadge.className='badge fs-6 bg-secondary';scoreDisplay.style.borderColor='#dee2e6';scoreDisplay.style.background='#f8f9fa';return;}
    var score=p*i;
    scoreVal.textContent=score;
    var level,color,bg;
    if(score<=4){level='Low Risk';color='#27ae60';bg='rgba(39,174,96,.1)';}
    else if(score<=9){level='Medium Risk';color='#f39c12';bg='rgba(243,156,18,.1)';}
    else if(score<=16){level='High Risk';color='#e67e22';bg='rgba(230,126,34,.1)';}
    else{level='EXTREME Risk';color='#e74c3c';bg='rgba(231,76,60,.1)';}
    levelBadge.textContent=level;
    levelBadge.style.background=color;
    levelBadge.style.color='#fff';
    levelBadge.className='badge fs-6';
    scoreDisplay.style.borderColor=color;
    scoreDisplay.style.background=bg;
    scoreVal.style.color=color;
  }
  probSel.addEventListener('change',updateScore);
  impSel.addEventListener('change',updateScore);
})();
</script>
