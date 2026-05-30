<?php /** FabX ERP - Audit View + Findings Matrix */
$checklist = json_decode($audit['checklist'] ?? '[]', true) ?: [];
?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-clipboard-check"></i> <?= e($audit['audit_no']) ?></h1>
  <div class="page-actions">
    <?= status_badge($audit['status']) ?>
    <a href="<?= base_url('qms/audits') ?>" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card mb-4">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Audit Information</h5></div>
      <div class="fx-card-body">
        <h5 class="mb-2"><?= e($audit['title']) ?></h5>
        <div class="row g-3">
          <div class="col-md-4"><div class="small text-muted">Type</div><strong><?= ucfirst($audit['audit_type']) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Auditor</div><strong><?= e($audit['auditor_name']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Department</div><strong><?= e($audit['department_name']??'—') ?></strong></div>
          <div class="col-md-6"><div class="small text-muted">Planned Start</div><strong><?= format_date($audit['planned_start_date']) ?></strong></div>
          <div class="col-md-6"><div class="small text-muted">Planned End</div><strong><?= format_date($audit['planned_end_date']) ?></strong></div>
          <?php if ($audit['scope']): ?><div class="col-12"><div class="small text-muted mb-1">Scope</div><div class="bg-light rounded p-2"><?= nl2br(e($audit['scope'])) ?></div></div><?php endif; ?>
          <?php if ($audit['criteria']): ?><div class="col-12"><div class="small text-muted mb-1">Criteria</div><div class="bg-light rounded p-2"><?= nl2br(e($audit['criteria'])) ?></div></div><?php endif; ?>
        </div>
      </div>
    </div>
    <?php if (!empty($checklist)): ?>
    <div class="fx-card mb-4">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-list-check"></i> Audit Checklist (<?= count($checklist) ?> items)</h5></div>
      <div class="fx-card-body p-0">
        <table class="fx-table mb-0">
          <thead><tr><th style="width:90px">Clause</th><th>Question</th><th>Criteria</th></tr></thead>
          <tbody>
            <?php foreach($checklist as $item): ?>
            <tr>
              <td><span class="badge bg-secondary"><?= e($item['clause']??'—') ?></span></td>
              <td><?= e($item['question']??'—') ?></td>
              <td class="text-muted small"><?= e($item['criteria']??'—') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
    <!-- Findings Matrix -->
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-exclamation-diamond"></i> Audit Findings (<?= count($findings) ?>)</h5></div>
      <div class="fx-card-body p-0">
        <?php if (!empty($findings)): ?>
        <table class="fx-table mb-0">
          <thead><tr><th style="width:90px">Clause</th><th>Type</th><th>Description</th><th>Evidence</th><th>Status</th></tr></thead>
          <tbody>
            <?php foreach($findings as $f): ?>
            <?php $tClass=match($f['finding_type']){'major'=>'danger','minor'=>'warning','observation'=>'info',default=>'success'}; ?>
            <tr>
              <td><span class="badge bg-secondary"><?= e($f['clause_reference']??'—') ?></span></td>
              <td><span class="badge bg-<?= $tClass ?>"><?= ucfirst($f['finding_type']) ?></span></td>
              <td><?= e(truncate($f['description'],60)) ?></td>
              <td class="text-muted small"><?= e(truncate($f['evidence']??'—',40)) ?></td>
              <td><?= status_badge($f['status']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
      <div class="fx-card-footer">
        <div data-bs-toggle="collapse" data-bs-target="#addFindingForm" style="cursor:pointer" class="d-flex align-items-center gap-2">
          <i class="bi bi-plus-circle text-primary"></i><strong class="text-primary">Add New Finding</strong>
        </div>
        <div class="collapse mt-3" id="addFindingForm">
          <form method="POST" action="<?= base_url('qms/audits/finding/'.$audit['id']) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Clause Reference</label>
                <input type="text" name="clause_reference" class="form-control" placeholder="e.g. 8.5.1">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Finding Type <span class="text-danger">*</span></label>
                <select name="finding_type" class="form-select" required>
                  <option value="major">Major NC</option>
                  <option value="minor">Minor NC</option>
                  <option value="observation">Observation</option>
                  <option value="opportunity">Opportunity for Improvement</option>
                </select>
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="corrective_action_required" value="1" id="caReq">
                  <label class="form-check-label" for="caReq">CAPA Required</label>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Finding Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="2" required placeholder="Describe the finding..."></textarea>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Objective Evidence</label>
                <textarea name="evidence" class="form-control" rows="2" placeholder="Evidence observed during audit..."></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-save"></i> Record Finding</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-bar-chart"></i> Findings Summary</h5></div>
      <div class="fx-card-body">
        <?php
        $ftCounts = array_count_values(array_column($findings,'finding_type'));
        $types = ['major'=>['label'=>'Major NCs','color'=>'danger'],'minor'=>['label'=>'Minor NCs','color'=>'warning'],'observation'=>['label'=>'Observations','color'=>'info'],'opportunity'=>['label'=>'OFIs','color'=>'success']];
        foreach($types as $type=>$meta): $cnt=$ftCounts[$type]??0; ?>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="badge bg-<?= $meta['color'] ?>"><?= $meta['label'] ?></span>
          <strong><?= $cnt ?></strong>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
