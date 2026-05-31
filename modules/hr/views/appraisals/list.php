<?php /** FabX ERP - Performance Appraisals */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-award-fill text-warning"></i> Performance Appraisal Records</h1>
    <div class="page-actions">
        <a href="<?= base_url('hr/appraisals/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Appraisal
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="draft" <?= (input('status') === 'draft') ? 'selected' : '' ?>>Draft</option>
            <option value="in_progress" <?= (input('status') === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
            <option value="completed" <?= (input('status') === 'completed') ? 'selected' : '' ?>>Completed</option>
        </select>
        <select name="period" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All Periods</option>
            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                <option value="<?= $y ?>" <?= (input('period') == $y) ? 'selected' : '' ?>>FY <?= $y ?></option>
            <?php endfor; ?>
        </select>
        <?php if (input('status') || input('period')): ?>
            <a href="<?= base_url('hr/appraisals') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Appraisals Table -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-stars"></i> Performance Review Register</h5>
        <span class="badge bg-dark border border-secondary text-muted"><?= count($appraisals) ?> reviews</span>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Review Period</th>
                        <th>Reviewer</th>
                        <th>Overall Score</th>
                        <th class="text-center">Grade</th>
                        <th>KRA Score</th>
                        <th>Attendance Score</th>
                        <th class="text-center">Status</th>
                        <th>Review Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($appraisals)): ?>
                        <?php foreach ($appraisals as $a): ?>
                            <?php
                            $sc = match($a['status'] ?? '') {
                                'completed' => 'badge-fx-success',
                                'in_progress' => 'badge-fx-warning',
                                'draft' => 'badge-fx-secondary',
                                default => 'badge-fx-secondary'
                            };
                            $score = (float)($a['overall_score'] ?? 0);
                            $grade = $a['grade'] ?? '-';
                            $gradeClass = match($grade) {
                                'A+', 'A' => 'text-success',
                                'B+', 'B' => 'text-info',
                                'C' => 'text-warning',
                                'D', 'F' => 'text-danger',
                                default => 'text-muted'
                            };
                            $barClass = $score >= 80 ? 'bg-success' : ($score >= 60 ? 'bg-info' : ($score >= 40 ? 'bg-warning' : 'bg-danger'));
                            $kraScore = (float)($a['kra_score'] ?? 0);
                            $attnScore = (float)($a['attendance_score'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($a['employee_name'] ?? '-') ?></div>
                                    <small class="text-muted"><?= e($a['designation'] ?? '') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-dark border border-secondary text-muted"><?= e($a['review_period'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center text-warning fw-bold flex-shrink-0" style="width:28px;height:28px;font-size:0.7rem;">
                                            <?= strtoupper(substr($a['reviewer_name'] ?? 'R', 0, 2)) ?>
                                        </div>
                                        <small><?= e($a['reviewer_name'] ?? '-') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($score > 0): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:8px; min-width:60px; border-radius:4px;">
                                                <div class="progress-bar <?= $barClass ?>" style="width:<?= $score ?>%"></div>
                                            </div>
                                            <span class="fw-bold small <?= $barClass === 'bg-success' ? 'text-success' : ($barClass === 'bg-info' ? 'text-info' : ($barClass === 'bg-warning' ? 'text-warning' : 'text-danger')) ?>"><?= (int)$score ?>%</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Not scored</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="h5 fw-bold mb-0 <?= $gradeClass ?>"><?= $grade ?></span>
                                </td>
                                <td>
                                    <?php if ($kraScore > 0): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress flex-grow-1" style="height:5px; min-width:50px;">
                                                <div class="progress-bar bg-primary" style="width:<?= $kraScore ?>%"></div>
                                            </div>
                                            <small><?= (int)$kraScore ?>%</small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($attnScore > 0): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress flex-grow-1" style="height:5px; min-width:50px;">
                                                <div class="progress-bar bg-info" style="width:<?= $attnScore ?>%"></div>
                                            </div>
                                            <small><?= (int)$attnScore ?>%</small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst(str_replace('_', ' ', $a['status'] ?? '')) ?></span>
                                </td>
                                <td><small><?= format_date($a['created_at']) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-star-half"></i>
                                <h5>No appraisal records found</h5>
                                <a href="<?= base_url('hr/appraisals/create') ?>" class="btn btn-primary btn-sm">Create First Appraisal</a>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> – <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> appraisals</small>
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
