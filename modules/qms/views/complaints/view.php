<?php /** FabX ERP - Complaint Detail + Investigation */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-chat-left-text"></i> <?= e($complaint['complaint_no']) ?></h1>
  <div class="page-actions">
    <?= priority_badge($complaint['severity']) ?>
    <?= status_badge($complaint['status']) ?>
    <a href="<?= base_url('qms/complaints') ?>" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card mb-4">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Complaint Information</h5></div>
      <div class="fx-card-body">
        <div class="row g-3">
          <div class="col-md-4"><div class="small text-muted">Date</div><strong><?= format_date($complaint['complaint_date']) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Client</div><strong><?= e($complaint['client_name']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Project</div><strong><?= e($complaint['project_name']??'None') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Source</div><strong><?= ucfirst($complaint['source']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Received By</div><strong><?= e($complaint['received_by_name']??'—') ?></strong></div>
          <div class="col-12"><div class="small text-muted mb-1">Description</div><div class="bg-light rounded p-3"><?= nl2br(e($complaint['description'])) ?></div></div>
          <?php if ($complaint['immediate_action']): ?><div class="col-12"><div class="small text-muted mb-1">Immediate Action</div><div class="bg-light rounded p-3"><?= nl2br(e($complaint['immediate_action'])) ?></div></div><?php endif; ?>
        </div>
      </div>
    </div>
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-search"></i> Investigation & Resolution</h5></div>
      <div class="fx-card-body">
        <form method="POST" action="<?= base_url('qms/complaints/status/'.$complaint['id']) ?>">
          <?= csrf_field() ?>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Investigation Findings</label>
              <textarea name="investigation_findings" class="form-control" rows="3" placeholder="Document investigation findings..."><?= e($complaint['investigation_findings']??'') ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Root Cause</label>
              <textarea name="root_cause" class="form-control" rows="2" placeholder="Identified root cause..."><?= e($complaint['root_cause']??'') ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Corrective Action</label>
              <textarea name="corrective_action" class="form-control" rows="2" placeholder="Actions taken to resolve..."><?= e($complaint['corrective_action']??'') ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Customer Satisfaction</label>
              <select name="customer_satisfaction" class="form-select">
                <option value="pending" <?= ($complaint['customer_satisfaction']??'')==='pending'?'selected':'' ?>>⏳ Pending</option>
                <option value="satisfied" <?= ($complaint['customer_satisfaction']??'')==='satisfied'?'selected':'' ?>>😊 Satisfied</option>
                <option value="not_satisfied" <?= ($complaint['customer_satisfaction']??'')==='not_satisfied'?'selected':'' ?>>😞 Not Satisfied</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Update Status</label>
              <select name="status" class="form-select">
                <?php foreach(['open'=>'Open','under_investigation'=>'Under Investigation','action_taken'=>'Action Taken','verified'=>'Verified','closed'=>'Closed'] as $v=>$l): ?>
                  <option value="<?= $v ?>" <?= ($complaint['status']??'')===$v?'selected':'' ?>><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 d-flex gap-2">
              <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Save Updates</button>
              <a href="<?= base_url('qms/capa/create?from_complaint='.$complaint['id']) ?>" class="btn btn-outline-warning"><i class="bi bi-arrow-repeat"></i> Create CAPA</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-diagram-3"></i> Resolution Pipeline</h5></div>
      <div class="fx-card-body">
        <?php $steps=['open'=>'Open','under_investigation'=>'Investigating','action_taken'=>'Action Taken','verified'=>'Verified','closed'=>'Closed'];
        $order=array_keys($steps); $curIdx=array_search($complaint['status'],$order); ?>
        <?php foreach($steps as $step=>$label): $idx=array_search($step,$order); $done=$idx<$curIdx; $cur=$step===$complaint['status']; ?>
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
