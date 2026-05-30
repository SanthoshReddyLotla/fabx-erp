<?php /** FabX ERP - Customer Complaints Tracker */ ?>
<div class="page-header">
  <h1 class="page-title"><i class="bi bi-chat-left-text"></i> Customer Complaints</h1>
  <div class="page-actions">
    <a href="<?= base_url('qms/complaints/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> Log Complaint</a>
  </div>
</div>
<!-- Pipeline Status Overview -->
<div class="row g-3 mb-4">
  <?php $pipeSteps=['open'=>['Open','info'],'under_investigation'=>['Investigating','warning'],'action_taken'=>['Action Taken','primary'],'verified'=>['Verified','secondary'],'closed'=>['Closed','success']];
  foreach ($pipeSteps as $step=>[$label,$color]): $cnt=count(array_filter($complaints,fn($c)=>$c['status']===$step)); ?>
  <div class="col"><div class="fx-card p-3 text-center"><div class="badge bg-<?= $color ?> mb-1"><?= $label ?></div><div class="fs-4 fw-bold"><?= $cnt ?></div></div></div>
  <?php endforeach; ?>
</div>
<div class="filters-bar">
  <form class="d-flex gap-2 flex-wrap w-100">
    <select name="status" class="form-select" style="width:180px" onchange="this.form.submit()">
      <option value="">All Status</option>
      <?php foreach($pipeSteps as $v=>[$l,$c]): ?>
        <option value="<?= $v ?>" <?= ($_GET['status']??'')===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>
<div class="fx-card">
  <div class="fx-card-body p-0">
    <div class="table-responsive-fx">
      <table class="fx-table mb-0" id="complaintsTable">
        <thead><tr><th>Complaint No</th><th>Date</th><th>Client</th><th>Source</th><th>Severity</th><th>Satisfaction</th><th>Status</th><th class="actions">Actions</th></tr></thead>
        <tbody>
          <?php if (!empty($complaints)): foreach ($complaints as $c): ?>
          <tr>
            <td><strong><?= e($c['complaint_no']) ?></strong></td>
            <td><?= format_date($c['complaint_date']) ?></td>
            <td><?= e($c['client_name']??'—') ?></td>
            <td><?= ucfirst($c['source']??'—') ?></td>
            <td><?= priority_badge($c['severity']) ?></td>
            <td>
              <?php $satIcons=['satisfied'=>['success','emoji-smile'],'not_satisfied'=>['danger','emoji-frown'],'pending'=>['secondary','hourglass']]; $si=$satIcons[$c['customer_satisfaction']??'pending']; ?>
              <span class="text-<?= $si[0] ?>"><i class="bi bi-<?= $si[1] ?>"></i> <?= ucfirst($c['customer_satisfaction']??'pending') ?></span>
            </td>
            <td><?= status_badge($c['status']) ?></td>
            <td class="actions">
              <a href="<?= base_url('qms/complaints/view/'.$c['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="8"><div class="empty-state"><i class="bi bi-emoji-smile"></i><h5>No complaints logged</h5><p class="mb-3">No customer complaints have been recorded.</p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
