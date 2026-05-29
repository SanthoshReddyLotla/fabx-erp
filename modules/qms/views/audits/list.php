<?php /** FabX ERP - Audits List */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-clipboard-check" style="color:#9b59b6"></i> Internal Audits</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/audits/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> Schedule Audit</a>
  </div>
</div>
<div class="filters-bar">
  <form class="d-flex gap-2 flex-wrap w-100">
    <select name="status" class="form-select" style="width:170px" onchange="this.form.submit()">
      <option value="">All Status</option>
      <?php foreach(['planned'=>'Planned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled','overdue'=>'Overdue'] as $v=>$l): ?>
        <option value="<?= $v ?>" <?= ($_GET['status']??'')===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive-fx">
      <table class="fx-table mb-0" id="auditsTable">
        <thead><tr><th>Audit No</th><th>Type</th><th>Title</th><th>Department</th><th>Auditor</th><th>Planned Start</th><th>Planned End</th><th>Status</th><th class="actions">Actions</th></tr></thead>
        <tbody>
          <?php if (!empty($audits)): foreach($audits as $a): ?>
          <tr>
            <td><strong><?= e($a['audit_no']) ?></strong></td>
            <td><span class="badge bg-secondary"><?= ucfirst($a['audit_type']) ?></span></td>
            <td><?= e(truncate($a['title'],45)) ?></td>
            <td><?= e($a['department_name']??'—') ?></td>
            <td><?= e($a['auditor_name']??'—') ?></td>
            <td><?= format_date($a['planned_start_date']) ?></td>
            <td><?= format_date($a['planned_end_date']) ?></td>
            <td><?= status_badge($a['status']) ?></td>
            <td class="actions">
              <a href="<?= base_url('qms/audits/view/'.$a['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="9"><div class="empty-state"><i class="bi bi-clipboard-check"></i><h5>No audits scheduled</h5><p class="mb-3">Schedule your first internal audit.</p><a href="<?= base_url('qms/audits/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Schedule</a></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
