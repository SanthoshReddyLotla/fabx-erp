<?php /** FabX ERP - Drawings Cost & Variance Matrix */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-pencil-square"></i> Engineering Drawings & Blueprints Vault</h1>
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
            <a href="<?= base_url('projects/drawings') ?>" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
        <?php endif; ?>
    </form>
</div>

<!-- Drawings Ledger Card -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-ruled"></i> Blueprint Revision Tracker Grid</h5>
        <span class="badge bg-dark border border-secondary text-muted">Showing <?= count($drawings) ?> Blueprint Revisions</span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Drawing Number</th>
                        <th>Project</th>
                        <th>Title / Description</th>
                        <th>Drawing Type</th>
                        <th class="text-center">Revision</th>
                        <th>Prepared By</th>
                        <th>Approval Date</th>
                        <th class="text-center">Engineering Check Phase</th>
                        <th class="text-center">Document File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($drawings)): ?>
                        <?php foreach ($drawings as $drw): 
                            $statusClass = match($drw['status']) {
                                'approved' => 'badge-fx-success',
                                'draft' => 'badge-fx-secondary',
                                'for_check' => 'badge-fx-warning',
                                'for_revision' => 'badge-fx-danger',
                                'superseded' => 'bg-secondary text-dark opacity-50 border border-secondary',
                                default => 'badge-fx-secondary'
                            };
                            
                            $typeClass = match($drw['drawing_type']) {
                                'general' => 'bg-dark text-white border border-secondary border-opacity-50',
                                'fabrication' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                'assembly' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                'detail' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                'layout' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                default => 'bg-dark text-secondary border border-secondary border-opacity-25'
                            };
                        ?>
                            <tr>
                                <td><strong><?= e($drw['drawing_no']) ?></strong></td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($drw['project_name']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-light-heading"><?= e($drw['title']) ?></div>
                                    <small class="text-muted d-block text-wrap" style="max-width: 250px;"><?= e($drw['remarks'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge text-uppercase <?= $typeClass ?>" style="font-size:0.7rem; letter-spacing: 0.5px;">
                                        <?= e($drw['drawing_type']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-primary">Rev <?= e($drw['revision'] ?? 'A') ?></span>
                                </td>
                                <td>
                                    <div class="small fw-semibold"><?= e($drw['prepared_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <span class="small text-muted">
                                        <?= $drw['approval_date'] ? format_date($drw['approval_date']) : 'Pending Gate' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $statusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $drw['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($drw['file_path']): ?>
                                        <a href="<?= e($drw['file_path']) ?>" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            <span>Download</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">No File Uploaded</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state py-5">
                                    <i class="bi bi-pencil-square display-4 mb-3 d-block text-muted"></i>
                                    <h5>No Drawings Uploaded</h5>
                                    <p>Select another project or upload blueprint revisions.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
