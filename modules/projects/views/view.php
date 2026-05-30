<?php /** FabX ERP - Project Details */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-kanban"></i> Project: <?= e($project['project_name']) ?></h1>
    <div class="page-actions gap-2">
        <a href="<?= base_url('projects') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Projects
        </a>
    </div>
</div>

<!-- Project Overview Card -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="fx-card h-100">
            <div class="fx-card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Project Code</div>
                        <div class="fs-5 fw-semibold text-primary"><?= e($project['project_code']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Client</div>
                        <div class="fs-5 fw-semibold text-light-heading"><?= e($project['client_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Project Manager</div>
                        <div class="fs-6 fw-semibold"><?= e($project['pm_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Site Location</div>
                        <div class="fs-6 fw-semibold text-truncate" title="<?= e($project['site_location']) ?>"><?= e($project['site_location'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">PO Number</div>
                        <div class="small fw-semibold"><?= e($project['po_number'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">PO Date</div>
                        <div class="small fw-semibold"><?= $project['po_date'] ? format_date($project['po_date']) : '-' ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Contract Value</div>
                        <div class="small fw-semibold"><?= e($project['currency'] ?? 'INR') ?> <?= number_format($project['contract_value'] ?? 0, 2) ?></div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Overall Project Completion</span>
                        <span class="fs-5 fw-bold text-success"><?= $project['progress_percentage'] ?? 0 ?>%</span>
                    </div>
                    <div class="progress" style="height:12px; border-radius: 6px; background-color: var(--border-color);">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $project['progress_percentage'] ?? 0 ?>%" aria-valuenow="<?= $project['progress_percentage'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="fx-card h-100">
            <div class="fx-card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="text-muted small mb-1">Current Status</div>
                    <?php 
                    $statusClass = match($project['status'] ?? '') {
                        'active' => 'badge-fx-success',
                        'completed' => 'badge-fx-info',
                        'delayed' => 'badge-fx-danger',
                        'on_hold' => 'badge-fx-warning',
                        default => 'badge-fx-secondary'
                    };
                    ?>
                    <span class="badge-fx fs-6 px-3 py-2 <?= $statusClass ?> mb-3 d-inline-block">
                        <?= ucfirst(str_replace('_', ' ', $project['status'] ?? '')) ?>
                    </span>
                    
                    <div class="text-muted small mb-1">Current Stage</div>
                    <span class="badge-fx bg-dark text-white border border-secondary fs-6 px-3 py-2 d-inline-block mb-3">
                        <?= e(ucfirst($project['current_stage'] ?? 'planning')) ?>
                    </span>
                    
                    <?php if (!empty($project['delay_reason'])): ?>
                        <div class="alert alert-danger-fx p-3 mt-2 rounded">
                            <h6 class="alert-heading text-danger small mb-1"><i class="bi bi-exclamation-triangle"></i> Delay Reason</h6>
                            <p class="mb-0 small"><?= e($project['delay_reason']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="pt-3 border-top mt-3">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="text-muted small">Start Date</div>
                            <div class="fw-semibold small"><?= format_date($project['start_date']) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Target End Date</div>
                            <div class="fw-semibold small text-danger"><?= format_date($project['target_end_date']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stage Progression Pipeline Grid -->
<div class="fx-card mb-4">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-bezier2"></i> Stage Progression Milestone Pipeline</h5>
        <span class="small text-muted">Update stage progression below</span>
    </div>
    <div class="fx-card-body p-4">
        <!-- Horizontal Pipeline Flow -->
        <div class="pipeline-container mb-4 overflow-auto">
            <div class="d-flex align-items-center justify-content-between min-w-800 py-3 position-relative">
                <?php 
                $stageNames = ['planning', 'design', 'procurement', 'production', 'assembly', 'painting', 'dispatch', 'installation'];
                foreach ($stageNames as $index => $name):
                    $currentStageInfo = null;
                    foreach ($stages as $stg) {
                        if ($stg['stage_name'] === $name) {
                            $currentStageInfo = $stg;
                            break;
                        }
                    }
                    $stgStatus = $currentStageInfo['status'] ?? 'pending';
                    $stgProgress = $currentStageInfo['progress_percentage'] ?? 0;
                    
                    $bubbleClass = 'border-secondary bg-dark text-muted';
                    if ($stgStatus === 'completed') {
                        $bubbleClass = 'border-success bg-success text-white';
                    } elseif ($stgStatus === 'in_progress') {
                        $bubbleClass = 'border-primary bg-primary text-white';
                    } elseif ($stgStatus === 'delayed') {
                        $bubbleClass = 'border-danger bg-danger text-white';
                    } elseif ($stgStatus === 'on_hold') {
                        $bubbleClass = 'border-warning bg-warning text-white';
                    }
                ?>
                    <div class="pipeline-step text-center flex-fill position-relative z-1">
                        <div class="d-flex flex-column align-items-center">
                            <div class="rounded-circle border border-3 d-flex align-items-center justify-content-center mb-2 <?= $bubbleClass ?>" style="width: 45px; height: 45px;">
                                <span class="fw-bold"><?= $index + 1 ?></span>
                            </div>
                            <div class="fw-bold small text-light-heading"><?= ucfirst($name) ?></div>
                            <div class="text-muted small"><?= $stgProgress ?>% Completed</div>
                            <span class="badge bg-opacity-10 mt-1
                                <?= $stgStatus === 'completed' ? 'bg-success text-success' : '' ?>
                                <?= $stgStatus === 'in_progress' ? 'bg-primary text-primary' : '' ?>
                                <?= $stgStatus === 'delayed' ? 'bg-danger text-danger' : '' ?>
                                <?= $stgStatus === 'on_hold' ? 'bg-warning text-warning' : '' ?>
                                <?= $stgStatus === 'pending' ? 'bg-secondary text-muted' : '' ?>
                            " style="font-size: 0.7rem;">
                                <?= ucfirst(str_replace('_', ' ', $stgStatus)) ?>
                            </span>
                        </div>
                    </div>
                    <?php if ($index < count($stageNames) - 1): ?>
                        <div class="pipeline-line flex-grow-1 bg-secondary opacity-25" style="height: 3px; min-width: 30px; margin-top: -35px;"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Stage Actions / Details Table & Form Grid -->
        <div class="row g-4 mt-2">
            <div class="col-lg-8">
                <h6 class="fw-semibold mb-3 text-light-heading"><i class="bi bi-info-circle"></i> Milestone Breakdown</h6>
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle">
                        <thead>
                            <tr>
                                <th>Stage</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Planned Dates</th>
                                <th>Actual Dates</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stages as $stg): ?>
                                <tr>
                                    <td><strong><?= ucfirst($stg['stage_name']) ?></strong></td>
                                    <td>
                                        <?php 
                                        $sc = match($stg['status']) {
                                            'completed' => 'badge-fx-success',
                                            'in_progress' => 'badge-fx-primary',
                                            'delayed' => 'badge-fx-danger',
                                            'on_hold' => 'badge-fx-warning',
                                            default => 'badge-fx-secondary'
                                        };
                                        ?>
                                        <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $stg['status'])) ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; min-width: 60px;">
                                                <div class="progress-bar bg-success" style="width: <?= $stg['progress_percentage'] ?>%"></div>
                                            </div>
                                            <small><?= (int)$stg['progress_percentage'] ?>%</small>
                                        </div>
                                    </td>
                                    <td class="small">
                                        <?= $stg['planned_start'] ? format_date($stg['planned_start']) : '-' ?> to 
                                        <?= $stg['planned_end'] ? format_date($stg['planned_end']) : '-' ?>
                                    </td>
                                    <td class="small">
                                        <?= $stg['actual_start'] ? format_date($stg['actual_start']) : '-' ?> to 
                                        <?= $stg['actual_end'] ? format_date($stg['actual_end']) : '-' ?>
                                    </td>
                                    <td class="small text-muted"><?= e($stg['remarks'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="fx-card bg-dark border-secondary">
                    <div class="fx-card-body p-4">
                        <h6 class="fw-semibold mb-3 text-light-heading"><i class="bi bi-pencil-square"></i> Update Stage Status</h6>
                        <form method="POST" action="<?= base_url('projects/view/' . $project['id']) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="update_stage">
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Select Stage</label>
                                <select name="stage_id" class="form-select bg-dark border-secondary text-white" required>
                                    <option value="">-- Choose Stage --</option>
                                    <?php foreach ($stages as $stg): ?>
                                        <option value="<?= $stg['id'] ?>"><?= ucfirst($stg['stage_name']) ?> (Current: <?= $stg['progress_percentage'] ?>%)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">New Status</label>
                                <select name="status" class="form-select bg-dark border-secondary text-white" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="delayed">Delayed</option>
                                    <option value="on_hold">On Hold</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Progress Percentage</label>
                                <div class="input-group">
                                    <input type="number" name="progress_percentage" min="0" max="100" class="form-control bg-dark border-secondary text-white" placeholder="e.g. 50">
                                    <span class="input-group-text bg-dark border-secondary text-white">%</span>
                                </div>
                                <small class="text-muted" style="font-size:0.75rem;">Completed stages will automatically mark as 100% completed.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Remarks / Notes</label>
                                <textarea name="remarks" rows="2" class="form-control bg-dark border-secondary text-white" placeholder="Progress notes or reasons for delay..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-fx btn-fx-primary w-100 mt-2">
                                <i class="bi bi-save"></i> Save Stage Updates
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Linked Records Overview (BOQ, Work Orders, Drawings) -->
<div class="row g-4">
    <!-- BOQ Overview Card -->
    <div class="col-12 col-xl-6">
        <div class="fx-card h-100">
            <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Bill of Quantities (BOQ)</h5>
                <a href="<?= base_url('projects/boq?project_id=' . $project['id']) ?>" class="btn btn-sm btn-outline-primary">
                    View Master BOQ
                </a>
            </div>
            <div class="fx-card-body p-0">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle">
                        <thead>
                            <tr>
                                <th>Item No</th>
                                <th>Description</th>
                                <th>UOM</th>
                                <th class="text-end">Est Qty</th>
                                <th class="text-end">Act Qty</th>
                                <th class="text-end">Variance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($boq)): ?>
                                <?php foreach ($boq as $item): 
                                    $var = (float)($item['actual_quantity'] ?? 0) - (float)$item['quantity'];
                                ?>
                                    <tr>
                                        <td><strong><?= e($item['item_no']) ?></strong></td>
                                        <td>
                                            <div class="fw-semibold text-light-heading"><?= e($item['description']) ?></div>
                                            <small class="text-muted text-truncate d-block" style="max-width: 250px;"><?= e($item['specification'] ?? '-') ?></small>
                                        </td>
                                        <td><span class="badge bg-secondary opacity-75"><?= e($item['uom']) ?></span></td>
                                        <td class="text-end fw-semibold"><?= number_format($item['quantity'], 3) ?></td>
                                        <td class="text-end fw-semibold"><?= number_format($item['actual_quantity'] ?? 0, 3) ?></td>
                                        <td class="text-end fw-bold <?= $var > 0 ? 'text-danger' : 'text-success' ?>">
                                            <?= ($var > 0 ? '+' : '') . number_format($var, 3) ?>
                                            <?php if ($var > 0): ?>
                                                <span class="badge bg-danger rounded-circle p-1" title="Limit Exceeded"><i class="bi bi-exclamation-triangle-fill" style="font-size:0.65rem;"></i></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-4">No BOQ items linked to this project.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Work Orders Overview Card -->
    <div class="col-12 col-xl-6">
        <div class="fx-card h-100">
            <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-tools"></i> Active Work Orders</h5>
                <a href="<?= base_url('projects/work-orders?project_id=' . $project['id']) ?>" class="btn btn-sm btn-outline-primary">
                    View Ledger
                </a>
            </div>
            <div class="fx-card-body p-0">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle">
                        <thead>
                            <tr>
                                <th>WO No</th>
                                <th>Description</th>
                                <th>Assigned To</th>
                                <th>Est / Act Hours</th>
                                <th>Priority</th>
                                <th>QC Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($work_orders)): ?>
                                <?php foreach ($work_orders as $wo): ?>
                                    <tr>
                                        <td><strong><?= e($wo['wo_no']) ?></strong></td>
                                        <td>
                                            <div class="fw-semibold text-light-heading text-truncate" style="max-width: 180px;"><?= e($wo['description']) ?></div>
                                            <small class="text-muted">Start: <?= format_date($wo['start_date']) ?></small>
                                        </td>
                                        <td><span class="small fw-semibold"><?= e($wo['assigned_name'] ?? '-') ?></span></td>
                                        <td>
                                            <span class="fw-semibold"><?= number_format($wo['estimated_hours'] ?? 0, 1) ?> hrs</span> / 
                                            <span class="text-info"><?= number_format($wo['actual_hours'] ?? 0, 1) ?> hrs</span>
                                        </td>
                                        <td>
                                            <?php 
                                            $pb = match($wo['priority']) {
                                                'low' => 'bg-secondary text-light',
                                                'medium' => 'bg-info text-dark',
                                                'high' => 'bg-warning text-dark',
                                                'urgent' => 'bg-danger text-white',
                                                default => 'bg-secondary text-light'
                                            };
                                            ?>
                                            <span class="badge <?= $pb ?> text-uppercase" style="font-size:0.7rem;"><?= e($wo['priority']) ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                            $qb = match($wo['quality_status']) {
                                                'pending' => 'badge-fx-secondary',
                                                'accepted' => 'badge-fx-success',
                                                'rejected' => 'badge-fx-danger',
                                                'rework' => 'badge-fx-warning',
                                                default => 'badge-fx-secondary'
                                            };
                                            ?>
                                            <span class="badge-fx <?= $qb ?>"><?= ucfirst($wo['quality_status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-4">No active work orders.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Engineering Drawings Vault Overview Card -->
    <div class="col-12 mt-4">
        <div class="fx-card">
            <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Engineering Drawings Vault</h5>
                <a href="<?= base_url('projects/drawings?project_id=' . $project['id']) ?>" class="btn btn-sm btn-outline-primary">
                    View Blueprint Vault
                </a>
            </div>
            <div class="fx-card-body p-0">
                <div class="table-responsive-fx">
                    <table class="fx-table align-middle">
                        <thead>
                            <tr>
                                <th>Drawing No</th>
                                <th>Title</th>
                                <th>Drawing Type</th>
                                <th>Revision</th>
                                <th>Prepared By</th>
                                <th>Approval Gate Status</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($drawings)): ?>
                                <?php foreach ($drawings as $drw): ?>
                                    <tr>
                                        <td><strong><?= e($drw['drawing_no']) ?></strong></td>
                                        <td><?= e($drw['title']) ?></td>
                                        <td><span class="badge bg-dark text-secondary border border-secondary"><?= ucfirst($drw['drawing_type']) ?></span></td>
                                        <td><span class="fw-bold text-primary">Rev <?= e($drw['revision'] ?? 'A') ?></span></td>
                                        <td><span class="small"><?= e($drw['prepared_name'] ?? '-') ?></span></td>
                                        <td>
                                            <?php 
                                            $db = match($drw['status']) {
                                                'draft' => 'badge-fx-secondary',
                                                'for_check' => 'badge-fx-warning',
                                                'approved' => 'badge-fx-success',
                                                'for_revision' => 'badge-fx-danger',
                                                'superseded' => 'bg-secondary text-dark opacity-50 border border-secondary',
                                                default => 'badge-fx-secondary'
                                            };
                                            ?>
                                            <span class="badge-fx <?= $db ?>"><?= ucfirst(str_replace('_', ' ', $drw['status'])) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($drw['file_path']): ?>
                                                <a href="<?= e($drw['file_path']) ?>" class="btn btn-xs btn-outline-light" target="_blank">
                                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted p-4">No drawings uploaded.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
