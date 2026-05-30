<?php /** FabX ERP - CAPA List */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-arrow-repeat text-warning"></i> Corrective & Preventive Actions</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/capa/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> New CAPA</a>
  </div>
</div>
<div class="filters-bar">
  <form class="d-flex gap-2 flex-wrap w-100">
    <select name="status" class="form-select" style="width:180px" onchange="this.form.submit()">
      <option value="">All Status</option>
      <?php foreach(['open'=>'Open','in_progress'=>'In Progress','implemented'=>'Implemented','verified'=>'Verified','closed'=>'Closed'] as $v=>$l): ?>
        <option value="<?= $v ?>" <?= ($filters['status']??'')===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <button type="button" class="btn btn-outline-secondary" onclick="exportTableToCSV('capaTable','capa_<?= date('Y-m-d') ?>.csv')"><i class="bi bi-download"></i> Export</button>
  </form>
</div>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive-fx">
      <table class="fx-table mb-0" id="capaTable">
        <thead><tr><th>CAPA No</th><th>Source</th><th>Description</th><th>Root Cause Method</th><th>Responsible</th><th>Target Date</th><th>Status</th><th class="actions">Actions</th></tr></thead>
        <tbody>
          <?php if (!empty($capas)): foreach ($capas as $c): ?>
          <tr>
            <td><strong><?= e($c['capa_no']) ?></strong></td>
            <td><span class="badge bg-secondary"><?= ucfirst(str_replace('_',' ',$c['source_type'])) ?></span></td>
            <td><?= e(truncate($c['description'],55)) ?></td>
            <td><?= ucfirst(str_replace('_',' ',$c['root_cause_method'] ?? '—')) ?></td>
            <td><?= e($c['responsible_name'] ?? '—') ?></td>
            <td><?php $td=$c['target_date']; $overdue=$td&&strtotime($td)<time()&&$c['status']!=='closed'; ?>
              <span class="<?= $overdue?'text-danger fw-bold':'' ?>"><?= format_date($td) ?></span>
            </td>
            <td><?= status_badge($c['status']) ?></td>
            <td class="actions">
              <a href="<?= base_url('qms/capa/view/'.$c['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="8"><div class="empty-state"><i class="bi bi-arrow-repeat"></i><h5>No CAPAs found</h5><p class="mb-3">No corrective actions recorded yet.</p><a href="<?= base_url('qms/capa/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Create CAPA</a></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if (($pagination['total_pages']??1)>1): ?>
  <div class="fx-card-footer">
    <div class="d-flex justify-content-between align-items-center">
      <small class="text-muted">Showing <?= $pagination['offset']+1 ?>–<?= min($pagination['offset']+$pagination['per_page'],$pagination['total']) ?> of <?= $pagination['total'] ?></small>
      <div class="pagination-fx">
        <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['page']-1 ?>&status=<?= $filters['status']??'' ?>">&laquo;</a><?php endif; ?>
        <?php for ($i=max(1,$pagination['page']-2);$i<=min($pagination['total_pages'],$pagination['page']+2);$i++): ?>
          <a href="?page=<?= $i ?>&status=<?= $filters['status']??'' ?>" class="<?= $i===$pagination['page']?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['page']+1 ?>&status=<?= $filters['status']??'' ?>">&raquo;</a><?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
