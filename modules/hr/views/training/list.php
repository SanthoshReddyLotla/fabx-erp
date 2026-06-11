<?php /** FabX ERP - Training Records */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-mortarboard-fill text-info"></i> Training & Skill Development Records</h1>
    <div class="page-actions">
        <a href="<?= base_url('hr/training/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> Schedule Training
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="scheduled" <?= (input('status') === 'scheduled') ? 'selected' : '' ?>>Scheduled</option>
            <option value="in_progress" <?= (input('status') === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
            <option value="completed" <?= (input('status') === 'completed') ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= (input('status') === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <select name="type" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="induction" <?= (input('type') === 'induction') ? 'selected' : '' ?>>Induction</option>
            <option value="technical" <?= (input('type') === 'technical') ? 'selected' : '' ?>>Technical</option>
            <option value="safety" <?= (input('type') === 'safety') ? 'selected' : '' ?>>Safety / HSE</option>
            <option value="quality" <?= (input('type') === 'quality') ? 'selected' : '' ?>>Quality / ISO</option>
            <option value="soft_skills" <?= (input('type') === 'soft_skills') ? 'selected' : '' ?>>Soft Skills</option>
        </select>
        <?php if (input('status') || input('type')): ?>
            <a href="<?= base_url('hr/training') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Training Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-journal-bookmark-fill"></i> Training Programme Registry</h5>
        <span class="badge bg-dark border border-secondary text-muted"><?= count($trainings) ?> records</span>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Training Programme</th>
                        <th>Type</th>
                        <th>Trainer / Facilitator</th>
                        <th>Department</th>
                        <th>Scheduled Date</th>
                        <th class="text-center">Duration (hrs)</th>
                        <th class="text-center">Attendees</th>
                        <th class="text-center">Status</th>
                        <th>Effectiveness</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($trainings)): ?>
                        <?php foreach ($trainings as $t): ?>
                            <?php
                            $sc = match($t['status'] ?? '') {
                                'completed' => 'badge-fx-success',
                                'in_progress' => 'badge-fx-primary',
                                'scheduled' => 'badge-fx-info',
                                'cancelled' => 'badge-fx-danger',
                                default => 'badge-fx-secondary'
                            };
                            $typeClass = match($t['type'] ?? '') {
                                'safety' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                'quality' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                'technical' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                'induction' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                default => 'bg-secondary bg-opacity-10 text-secondary border border-secondary'
                            };
                            $effectiveness = (float)($t['effectiveness_score'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($t['title'] ?? '-') ?></div>
                                    <?php if ($t['description'] ?? ''): ?>
                                        <small class="text-muted text-truncate d-block" style="max-width:200px;"><?= e(substr($t['description'], 0, 60)) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge text-uppercase <?= $typeClass ?>" style="font-size:0.7rem;">
                                        <?= e(ucfirst(str_replace('_', ' ', $t['type'] ?? '-'))) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center text-info fw-bold flex-shrink-0" style="width:28px;height:28px;font-size:0.7rem;">
                                            <?= strtoupper(substr($t['trainer_name'] ?? 'T', 0, 2)) ?>
                                        </div>
                                        <span class="small"><?= e($t['trainer_name'] ?? 'External') ?></span>
                                    </div>
                                </td>
                                <td><?= e($t['department_name'] ?? 'All Departments') ?></td>
                                <td><?= format_date($t['scheduled_date'] ?? null) ?></td>
                                <td class="text-center fw-bold text-light-heading"><?= e($t['duration_hours'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-dark border border-secondary text-muted">
                                        <?= (int)($t['attendees_count'] ?? 0) ?> pax
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($t['status'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <?php if ($effectiveness > 0): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px; min-width:60px;">
                                                <div class="progress-bar <?= $effectiveness >= 70 ? 'bg-success' : ($effectiveness >= 40 ? 'bg-warning' : 'bg-danger') ?>" style="width:<?= $effectiveness ?>%"></div>
                                            </div>
                                            <small class="fw-bold"><?= (int)$effectiveness ?>%</small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Not Assessed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-mortarboard"></i>
                                <h5>No training records found</h5>
                                <a href="<?= base_url('hr/training/create') ?>" class="btn btn-primary btn-sm">Schedule Training</a>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> – <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> records</small>
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
