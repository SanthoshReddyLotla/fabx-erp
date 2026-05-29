<?php /** FabX ERP - Management Review View */
$attendees = json_decode($review['attendees']??'[]',true) ?: [];
$decisions = json_decode($review['decisions']??'[]',true) ?: [];
$actionItems = json_decode($review['action_items']??'[]',true) ?: [];
?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-people-fill"></i> <?= e($review['review_no']) ?></h1>
  <div class="page-actions">
    <?= status_badge($review['status']) ?>
    <a href="<?= base_url('qms/reviews') ?>" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="fx-card mb-4">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Meeting Summary</h5></div>
      <div class="fx-card-body">
        <div class="row g-3 mb-3">
          <div class="col-md-4"><div class="small text-muted">Review Date</div><strong><?= format_date($review['review_date']) ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Chaired By</div><strong><?= e($review['chaired_by_name']??'—') ?></strong></div>
          <div class="col-md-4"><div class="small text-muted">Next Review</div><strong><?= format_date($review['next_review_date']) ?></strong></div>
        </div>
        <?php if (!empty($attendees)): ?>
          <div class="mb-3"><div class="small text-muted mb-1">Attendees (<?= count($attendees) ?>)</div><div class="d-flex gap-2 flex-wrap"><?php foreach($attendees as $att): ?><span class="badge bg-light text-dark border"><?= e($att) ?></span><?php endforeach; ?></div></div>
        <?php endif; ?>
        <?php if ($review['agenda']): ?><div class="mb-3"><div class="small text-muted mb-1">Agenda</div><div class="bg-light rounded p-3"><?= nl2br(e($review['agenda'])) ?></div></div><?php endif; ?>
        <?php if ($review['minutes']): ?><div><div class="small text-muted mb-1">Minutes of Meeting</div><div class="bg-light rounded p-3"><?= nl2br(e($review['minutes'])) ?></div></div><?php endif; ?>
      </div>
    </div>
    <?php if (!empty($decisions)): ?>
    <div class="fx-card mb-4">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-check2-square"></i> Decisions Made (<?= count($decisions) ?>)</h5></div>
      <div class="fx-card-body">
        <ol class="mb-0">
          <?php foreach($decisions as $dec): ?><li class="mb-2"><?= e($dec) ?></li><?php endforeach; ?>
        </ol>
      </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($actionItems)): ?>
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-list-task"></i> Action Items (<?= count($actionItems) ?>)</h5></div>
      <div class="fx-card-body p-0">
        <table class="fx-table mb-0">
          <thead><tr><th>#</th><th>Action</th><th>Responsible</th><th>Due Date</th></tr></thead>
          <tbody>
            <?php foreach($actionItems as $i=>$ai): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= e($ai['description']??'—') ?></td>
              <td><?= e($ai['responsible']??'—') ?></td>
              <td><?= format_date($ai['due_date']??null) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <div class="col-lg-4">
    <div class="fx-card">
      <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-bar-chart"></i> Review Summary</h5></div>
      <div class="fx-card-body">
        <div class="d-flex justify-content-between mb-2"><span>Attendees</span><strong><?= count($attendees) ?></strong></div>
        <div class="d-flex justify-content-between mb-2"><span>Decisions</span><strong><?= count($decisions) ?></strong></div>
        <div class="d-flex justify-content-between"><span>Action Items</span><strong><?= count($actionItems) ?></strong></div>
      </div>
    </div>
  </div>
</div>
