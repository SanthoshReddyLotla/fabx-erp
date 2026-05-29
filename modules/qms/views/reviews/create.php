<?php /** FabX ERP - Management Review Create with Dynamic JSON Builder */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-people-fill"></i> Record Management Review</h1>
  <a href="<?= base_url('qms/reviews') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= base_url('qms/reviews/create') ?>" id="reviewForm">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="fx-card mb-4">
        <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Meeting Details</h5></div>
        <div class="fx-card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Review Date <span class="text-danger">*</span></label>
              <input type="date" name="review_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Chaired By <span class="text-danger">*</span></label>
              <select name="chaired_by" class="form-select" required>
                <option value="">Select</option>
                <?php foreach($users as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Next Review Date</label>
              <input type="date" name="next_review_date" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Attendees</label>
              <select name="attendees[]" class="form-select" multiple size="5">
                <?php foreach($users as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['name']) ?></option><?php endforeach; ?>
              </select>
              <div class="form-text">Hold Ctrl/Cmd to select multiple attendees.</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Agenda</label>
              <textarea name="agenda" class="form-control" rows="4" placeholder="List of agenda items discussed..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Minutes of Meeting</label>
              <textarea name="minutes" class="form-control" rows="5" placeholder="Detailed minutes of the review meeting..."></textarea>
            </div>
          </div>
        </div>
      </div>
      <!-- Dynamic Decisions Builder -->
      <div class="fx-card mb-4">
        <div class="fx-card-header d-flex justify-content-between align-items-center">
          <h5 class="fx-card-title"><i class="bi bi-check2-square"></i> Decisions Made</h5>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDecision()"><i class="bi bi-plus-circle"></i> Add Decision</button>
        </div>
        <div class="fx-card-body" id="decisionsContainer">
          <div class="text-muted text-center py-2" id="decisionsEmpty"><small>Click "Add Decision" to record meeting decisions.</small></div>
        </div>
      </div>
      <!-- Dynamic Action Items Builder -->
      <div class="fx-card">
        <div class="fx-card-header d-flex justify-content-between align-items-center">
          <h5 class="fx-card-title"><i class="bi bi-list-task"></i> Action Items</h5>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="addActionItem()"><i class="bi bi-plus-circle"></i> Add Action</button>
        </div>
        <div class="fx-card-body" id="actionItemsContainer">
          <div class="text-muted text-center py-2" id="actionItemsEmpty"><small>Click "Add Action" to record follow-up items.</small></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save Review</button>
        <a href="<?= base_url('qms/reviews') ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
<script>
var dCount=0, aCount=0;
function addDecision(){
  dCount++;
  document.getElementById('decisionsEmpty').style.display='none';
  var c=document.getElementById('decisionsContainer');
  var d=document.createElement('div');
  d.className='d-flex gap-2 mb-2 align-items-center';
  d.id='decision'+dCount;
  d.innerHTML='<input type="text" name="decisions[]" class="form-control" placeholder="Decision '+(dCount)+'..." required><button type="button" class="btn btn-sm btn-outline-danger" onclick="document.getElementById(\'decision'+dCount+'\').remove();checkEmpty(\'decisionsContainer\',\'decisionsEmpty\')"><i class="bi bi-x-lg"></i></button>';
  c.appendChild(d);
}
function addActionItem(){
  aCount++;
  document.getElementById('actionItemsEmpty').style.display='none';
  var c=document.getElementById('actionItemsContainer');
  var d=document.createElement('div');
  d.className='border rounded p-3 mb-3 position-relative';
  d.id='action'+aCount;
  d.innerHTML='<button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="document.getElementById(\'action'+aCount+'\').remove();checkEmpty(\'actionItemsContainer\',\'actionItemsEmpty\')"><i class="bi bi-x-lg"></i></button><div class="row g-2"><div class="col-12"><label class="form-label small fw-semibold">Action Description <span class="text-danger">*</span></label><input type="text" name="ai_description[]" class="form-control form-control-sm" placeholder="What needs to be done?" required></div><div class="col-md-6"><label class="form-label small fw-semibold">Responsible Person</label><input type="text" name="ai_responsible[]" class="form-control form-control-sm" placeholder="Name / department"></div><div class="col-md-6"><label class="form-label small fw-semibold">Due Date</label><input type="date" name="ai_due_date[]" class="form-control form-control-sm"></div></div>';
  c.appendChild(d);
}
function checkEmpty(containerId, emptyId){
  var container=document.getElementById(containerId);
  var items=container.querySelectorAll('[id^="decision"],[id^="action"]');
  if(items.length===0) document.getElementById(emptyId).style.display='';
}
</script>
