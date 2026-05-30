<?php /** FabX ERP - Training List */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-mortarboard text-success"></i> Training Records</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/training/competency') ?>" class="btn btn-outline-secondary"><i class="bi bi-grid-3x3-gap"></i> Competency Matrix</a>
    <a href="<?= base_url('qms/training/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> Schedule Training</a>
  </div>
</div>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive-fx">
      <table class="fx-table mb-0" id="trainingTable">
        <thead><tr><th>Code</th><th>Title</th><th>Type</th><th>Mode</th><th>Trainer</th><th>Start Date</th><th>End Date</th><th>Duration</th><th>Status</th><th class="actions">Actions</th></tr></thead>
        <tbody>
          <?php if (!empty($trainings)): foreach ($trainings as $t): ?>
          <tr>
            <td><strong><?= e($t['training_code']) ?></strong></td>
            <td><?= e(truncate($t['title'],45)) ?></td>
            <td><span class="badge bg-secondary"><?= ucfirst(str_replace('_',' ',$t['training_type'])) ?></span></td>
            <td><?= ucfirst($t['training_mode']??'—') ?></td>
            <td><?= e($t['trainer_name']??$t['external_trainer']??'—') ?></td>
            <td><?= format_date($t['start_date']) ?></td>
            <td><?= format_date($t['end_date']) ?></td>
            <td><?= $t['duration_hours']?$t['duration_hours'].' hrs':'—' ?></td>
            <td><?= status_badge($t['status']) ?></td>
            <td class="actions">
              <a href="<?= base_url('qms/training/view/'.$t['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="10"><div class="empty-state"><i class="bi bi-mortarboard"></i><h5>No training records</h5><p class="mb-3">Schedule your first training programme.</p><a href="<?= base_url('qms/training/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Schedule</a></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
