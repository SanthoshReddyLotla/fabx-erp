<?php /** FabX ERP - Management Reviews List */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-people-fill"></i> Management Reviews</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/reviews/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> Record Review</a>
  </div>
</div>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive-fx">
      <table class="fx-table mb-0">
        <thead><tr><th>Review No</th><th>Date</th><th>Chaired By</th><th>Attendees</th><th>Next Review</th><th>Status</th><th class="actions">Actions</th></tr></thead>
        <tbody>
          <?php if (!empty($reviews)): foreach ($reviews as $r):
            $attendees = json_decode($r['attendees']??'[]',true) ?: [];
          ?>
          <tr>
            <td><strong><?= e($r['review_no']) ?></strong></td>
            <td><?= format_date($r['review_date']) ?></td>
            <td><?= e($r['chaired_by_name']??'—') ?></td>
            <td><span class="badge bg-secondary"><?= count($attendees) ?> attendees</span></td>
            <td><?= format_date($r['next_review_date']) ?></td>
            <td><?= status_badge($r['status']) ?></td>
            <td class="actions"><a href="<?= base_url('qms/reviews/view/'.$r['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a></td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i><h5>No reviews recorded</h5><p class="mb-3">Record your management review meetings.</p><a href="<?= base_url('qms/reviews/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Record Review</a></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
