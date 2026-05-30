<?php /** FabX ERP - Production Reports */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bar-chart-line"></i> Daily Production & Shift Progress Reports</h1>
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
            <a href="<?= base_url('projects/production') ?>" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
        <?php endif; ?>
    </form>
</div>

<!-- Shift Activity Ledger -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-journal-text"></i> Shift Activity Tracker Grid</h5>
        <span class="badge bg-dark border border-secondary text-muted">Page <?= $pagination['page'] ?? 1 ?></span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date & Shift</th>
                        <th>Project</th>
                        <th>Shift Operator</th>
                        <th>Specific Progress Achievements</th>
                        <th class="text-center">Manpower</th>
                        <th>Blockers / Issues</th>
                        <th>Next Shift Plan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $rep): 
                            $shiftIcon = match($rep['shift']) {
                                'day' => '<i class="bi bi-brightness-high text-warning" title="Day Shift"></i>',
                                'night' => '<i class="bi bi-moon-stars text-info" title="Night Shift"></i>',
                                default => '<i class="bi bi-clock-history text-secondary" title="General Shift"></i>'
                            };
                            
                            $statusClass = match($rep['status']) {
                                'approved' => 'badge-fx-success',
                                'submitted' => 'badge-fx-primary',
                                'rejected' => 'badge-fx-danger',
                                default => 'badge-fx-secondary'
                            };
                        ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-5"><?= $shiftIcon ?></span>
                                        <div>
                                            <div class="fw-bold text-light-heading"><?= format_date($rep['report_date']) ?></div>
                                            <small class="text-muted text-uppercase" style="font-size:0.65rem;"><?= e($rep['shift']) ?> Shift</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($rep['project_name']) ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center text-info fw-bold" style="width:30px; height:30px; font-size:0.8rem;">
                                            <?= strtoupper(substr($rep['reported_by_name'] ?? 'O', 0, 2)) ?>
                                        </div>
                                        <span class="small fw-semibold"><?= e($rep['reported_by_name'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-light-heading mb-1"><?= e($rep['work_description']) ?></div>
                                    <?php if ($rep['progress_today']): ?>
                                        <div class="small text-muted"><span class="text-success fw-bold">✓</span> <?= e($rep['progress_today']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-bold text-primary">
                                    <?= (int)($rep['manpower_used'] ?? 0) ?> Operators
                                </td>
                                <td>
                                    <?php if (!empty($rep['issues'])): ?>
                                        <div class="alert alert-danger-fx py-1 px-2 rounded small m-0" style="max-width: 250px;">
                                            <i class="bi bi-exclamation-octagon text-danger"></i> <?= e($rep['issues']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-success small"><i class="bi bi-check-circle"></i> Clear (No Blockers)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="small text-muted"><?= e($rep['tomorrow_plan'] ?? '-') ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-fx <?= $statusClass ?>">
                                        <?= ucfirst($rep['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state py-5">
                                    <i class="bi bi-bar-chart-line display-4 mb-3 d-block text-muted"></i>
                                    <h5>No Production Reports Found</h5>
                                    <p>Select another project or log a daily shift production report.</p>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> reports</small>
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
