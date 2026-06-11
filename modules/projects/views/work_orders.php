<?php /** FabX ERP - Shop Floor Work Orders Tracking Board */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-tools"></i> Shop Floor Work Orders Ledger</h1>
</div>

<!-- Filters Bar -->
<div class="filters-bar mb-4">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <label class="text-muted small fw-semibold me-1">Filter by Project:</label>
        <select name="project_id" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">-- All Projects --</option>
            <?php foreach ($projects as $proj): ?>
                <option value="<?= $proj['id'] ?>" <?= (input('project_id') == $proj['id']) ? 'selected' : '' ?>>
                    <?= e($proj['project_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (input('project_id')): ?>
            <a href="<?= base_url('projects/work-orders') ?>" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
        <?php endif; ?>
    </form>
</div>

<!-- Work Orders Table Card -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-card-checklist"></i> Active Production & Routing Ledger</h5>
        <span class="badge bg-dark border border-secondary text-muted">Page <?= $pagination['page'] ?? 1 ?></span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>WO Number</th>
                        <th>Project</th>
                        <th>Work Description</th>
                        <th>Assigned Operator</th>
                        <th>Machine Allocation</th>
                        <th>Timeline</th>
                        <th class="text-center">Hours (Est/Act)</th>
                        <th class="text-center">Priority</th>
                        <th class="text-center">WO Status</th>
                        <th class="text-center">Quality Check</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($work_orders)): ?>
                        <?php foreach ($work_orders as $wo): 
                            $woStatusClass = match($wo['status']) {
                                'completed' => 'badge-fx-success',
                                'in_progress' => 'badge-fx-primary',
                                'on_hold' => 'badge-fx-warning',
                                'cancelled' => 'badge-fx-danger',
                                default => 'badge-fx-secondary'
                            };
                            
                            $priorityClass = match($wo['priority']) {
                                'low' => 'bg-secondary text-light',
                                'medium' => 'bg-info text-dark',
                                'high' => 'bg-warning text-dark',
                                'urgent' => 'bg-danger text-white',
                                default => 'bg-secondary text-light'
                            };
                            
                            $qualityClass = match($wo['quality_status']) {
                                'accepted' => 'badge-fx-success',
                                'rejected' => 'badge-fx-danger',
                                'rework' => 'badge-fx-warning',
                                default => 'badge-fx-secondary'
                            };
                        ?>
                            <tr>
                                <td><strong><?= e($wo['wo_no']) ?></strong></td>
                                <td>
                                    <div class="fw-bold text-light-heading text-truncate" style="max-width: 150px;" title="<?= e($wo['project_name']) ?>">
                                        <?= e($wo['project_name']) ?>
                                    </div>
                                    <small class="text-muted d-block" style="font-size:0.7rem;">Date: <?= format_date($wo['wo_date']) ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-light-heading text-wrap" style="max-width: 200px;"><?= e($wo['description']) ?></div>
                                    <?php if ($wo['uom'] && $wo['quantity']): ?>
                                        <small class="text-muted">Target Qty: <?= number_format($wo['quantity'], 2) ?> <?= e($wo['uom']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center text-primary fw-bold" style="width:30px; height:30px; font-size:0.8rem;">
                                            <?= strtoupper(substr($wo['assigned_name'] ?? 'U', 0, 2)) ?>
                                        </div>
                                        <span class="small fw-semibold"><?= e($wo['assigned_name'] ?? 'Unassigned') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($wo['machine_id'])): ?>
                                        <span class="badge bg-dark border border-secondary text-light-heading font-monospace py-2 px-3">
                                            <i class="bi bi-cpu text-primary me-1"></i> MC-<?= sprintf('%03d', $wo['machine_id']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">No Allocation</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <div>Start: <?= format_date($wo['start_date']) ?></div>
                                    <div class="text-danger">End: <?= format_date($wo['completion_date']) ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-light-heading"><?= number_format($wo['estimated_hours'] ?? 0, 1) ?>h</div>
                                    <div class="small text-info"><?= number_format($wo['actual_hours'] ?? 0, 1) ?>h actual</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $priorityClass ?> text-uppercase px-2 py-1" style="font-size: 0.7rem;">
                                        <?= e($wo['priority']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $woStatusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $wo['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $qualityClass ?>">
                                        <?= ucfirst($wo['quality_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">
                                <div class="empty-state py-5">
                                    <i class="bi bi-tools display-4 mb-3 d-block text-muted"></i>
                                    <h5>No Work Orders Found</h5>
                                    <p>Select another project or dispatch a new work order.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
        <div class="fx-card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> items</small>
                <nav><div class="pagination-fx">
                    <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['page'] - 1 ?><?= input('project_id') ? '&project_id='.input('project_id') : '' ?>">&laquo;</a><?php endif; ?>
                    <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
                        <a href="?page=<?= $i ?><?= input('project_id') ? '&project_id='.input('project_id') : '' ?>" class="<?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['page'] + 1 ?><?= input('project_id') ? '&project_id='.input('project_id') : '' ?>">&raquo;</a><?php endif; ?>
                </div></nav>
            </div>
        </div>
    <?php endif; ?>
</div>
