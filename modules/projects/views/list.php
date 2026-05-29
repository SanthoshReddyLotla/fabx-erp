<?php /** FabX ERP - Projects List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-kanban"></i> Projects</h1>
    <div class="page-actions">
        <a href="<?= base_url('projects/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Project
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-primary"><?= $stats['active'] ?? 0 ?></div>
            <div class="text-muted small">Active Projects</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-success"><?= $stats['completed'] ?? 0 ?></div>
            <div class="text-muted small">Completed</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="fx-card text-center p-3">
            <div class="display-6 fw-bold text-danger"><?= $stats['delayed'] ?? 0 ?></div>
            <div class="text-muted small">Delayed</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" <?= (input('status') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="completed" <?= (input('status') === 'completed') ? 'selected' : '' ?>>Completed</option>
            <option value="on_hold" <?= (input('status') === 'on_hold') ? 'selected' : '' ?>>On Hold</option>
            <option value="delayed" <?= (input('status') === 'delayed') ? 'selected' : '' ?>>Delayed</option>
        </select>
        <a href="<?= base_url('projects/gantt') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-bar-chart-gantt"></i> Gantt View
        </a>
    </form>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="projectsTable">
                <thead>
                    <tr>
                        <th>Project Code</th>
                        <th>Project Name</th>
                        <th>Client</th>
                        <th>PM</th>
                        <th>Stage</th>
                        <th>Progress</th>
                        <th>Start Date</th>
                        <th>Target End</th>
                        <th>Status</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td><strong><?= e($p['project_code']) ?></strong></td>
                                <td><?= e($p['project_name']) ?></td>
                                <td><?= e($p['client_name'] ?? '-') ?></td>
                                <td><?= e($p['pm_name'] ?? '-') ?></td>
                                <td><?= e(ucfirst($p['current_stage'] ?? '-')) ?></td>
                                <td>
                                    <div class="progress" style="height:6px;width:80px;">
                                        <div class="progress-bar" style="width:<?= $p['progress_percentage'] ?? 0 ?>%"></div>
                                    </div>
                                    <small><?= $p['progress_percentage'] ?? 0 ?>%</small>
                                </td>
                                <td><?= format_date($p['start_date']) ?></td>
                                <td><?= format_date($p['target_end_date']) ?></td>
                                <td>
                                    <?php $sc = match($p['status'] ?? '') {
                                        'active' => 'badge-fx-success', 'completed' => 'badge-fx-info',
                                        'delayed' => 'badge-fx-danger', 'on_hold' => 'badge-fx-warning',
                                        default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $p['status'] ?? '')) ?></span>
                                </td>
                                <td class="actions">
                                    <a href="<?= base_url('projects/view/' . $p['id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10">
                            <div class="empty-state">
                                <i class="bi bi-kanban"></i>
                                <h5>No projects found</h5>
                                <p>Create your first project to get started.</p>
                                <a href="<?= base_url('projects/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Create Project</a>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
        <div class="fx-card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> projects</small>
                <nav><div class="pagination-fx">
                    <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['page'] - 1 ?>">&laquo;</a><?php endif; ?>
                    <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['page'] + 1 ?>">&raquo;</a><?php endif; ?>
                </div></nav>
            </div>
        </div>
    <?php endif; ?>
</div>
