<?php /** FabX ERP - Gantt Chart */ ?>
<?php
// Calculate dynamic schedule bounds based on active projects
$minTime = null;
$maxTime = null;
if (!empty($projects)) {
    foreach ($projects as $p) {
        if ($p['start_date']) {
            $t = strtotime($p['start_date']);
            if ($minTime === null || $t < $minTime) { $minTime = $t; }
        }
        if ($p['target_end_date']) {
            $t = strtotime($p['target_end_date']);
            if ($maxTime === null || $t > $maxTime) { $maxTime = $t; }
        }
    }
}

// Fallback dates
if (!$minTime) { $minTime = strtotime('2024-01-01'); }
if (!$maxTime) { $maxTime = strtotime('2024-12-31'); }

// Round bounds to start/end of month
$startYear = (int)date('Y', $minTime);
$startMonth = (int)date('m', $minTime);
$endYear = (int)date('Y', $maxTime);
$endMonth = (int)date('m', $maxTime);

$startDateStr = "$startYear-$startMonth-01";
$endDateStr = date('Y-m-t', strtotime("$endYear-$endMonth-01"));

$startTimestamp = strtotime($startDateStr);
$endTimestamp = strtotime($endDateStr);

// Ensure bounds make sense
if ($endTimestamp <= $startTimestamp) {
    $endTimestamp = strtotime("+11 months", $startTimestamp);
    $endDateStr = date('Y-m-t', $endTimestamp);
}

$totalDays = max(1, ($endTimestamp - $startTimestamp) / 86400);

// Generate months list
$months = [];
$curr = $startTimestamp;
while ($curr <= $endTimestamp) {
    $months[] = [
        'name' => date('M Y', $curr),
        'days' => (int)date('t', $curr),
        'timestamp' => $curr
    ];
    $curr = strtotime("+1 month", $curr);
    
    // Safety break
    if (count($months) > 36) { break; }
}

$numMonths = count($months);
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bar-chart-gantt"></i> Project Schedules (Gantt Chart)</h1>
    <div class="page-actions">
        <a href="<?= base_url('projects') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-list-task"></i> Grid View
        </a>
    </div>
</div>

<div class="fx-card mb-4">
    <div class="fx-card-header py-3">
        <h5 class="mb-0"><i class="bi bi-calendar3"></i> Interactive Project Schedules Timeline</h5>
    </div>
    
    <div class="fx-card-body p-4">
        <?php if (!empty($projects)): ?>
            <!-- Timeline Container with horizontal scroll if needed -->
            <div class="gantt-scroll-container overflow-auto" style="border: 1px solid var(--border-color); border-radius: 8px;">
                <div class="gantt-chart-grid" style="min-width: <?= max(800, $numMonths * 100) ?>px;">
                    
                    <!-- Calendar Header Month Columns -->
                    <div class="d-flex border-bottom" style="background-color: var(--card-bg);">
                        <div class="gantt-sidebar-col p-3 border-end fw-semibold text-light-heading" style="width: 250px; flex-shrink: 0;">
                            Active Project Details
                        </div>
                        <div class="gantt-timeline-col flex-grow-1 d-flex">
                            <?php foreach ($months as $m): ?>
                                <div class="text-center p-3 border-end text-muted small fw-bold" style="width: <?= 100 / $numMonths ?>%; flex-grow: 1;">
                                    <?= e($m['name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Projects Schedule Rows -->
                    <div class="gantt-rows">
                        <?php foreach ($projects as $p): 
                            $pStart = strtotime($p['start_date']);
                            $pEnd = strtotime($p['target_end_date']);
                            
                            // Clamp offsets within timeline bounds
                            $startOffsetDays = max(0, ($pStart - $startTimestamp) / 86400);
                            $durationDays = max(1, ($pEnd - $pStart) / 86400);
                            
                            if ($pStart < $startTimestamp) {
                                $startOffsetDays = 0;
                                $durationDays = max(1, ($pEnd - $startTimestamp) / 86400);
                            }
                            
                            $leftPercent = min(100, max(0, ($startOffsetDays / $totalDays) * 100));
                            $widthPercent = min(100 - $leftPercent, max(3, ($durationDays / $totalDays) * 100));
                            
                            $barClass = 'bg-primary-gantt';
                            $stageBadge = match($p['current_stage']) {
                                'completed' => 'bg-success',
                                'delayed' => 'bg-danger',
                                'on_hold' => 'bg-warning text-dark',
                                default => 'bg-secondary'
                            };
                        ?>
                            <div class="d-flex border-bottom align-items-center" style="min-height: 70px;">
                                <!-- Project Left Sidebar Metadata -->
                                <div class="gantt-sidebar-col p-3 border-end d-flex flex-column justify-content-center" style="width: 250px; flex-shrink: 0;">
                                    <div class="fw-bold text-light-heading text-truncate" title="<?= e($p['project_name']) ?>">
                                        <?= e($p['project_name']) ?>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center mt-1">
                                        <span class="badge bg-dark border border-secondary text-muted small" style="font-size:0.7rem;"><?= e($p['project_code']) ?></span>
                                        <span class="badge <?= $stageBadge ?> small" style="font-size:0.65rem;"><?= e(ucfirst($p['current_stage'])) ?></span>
                                    </div>
                                </div>
                                
                                <!-- Project Visual Schedule Bar -->
                                <div class="gantt-timeline-col flex-grow-1 p-3 position-relative d-flex align-items-center h-100" style="background-color: rgba(255,255,255,0.01);">
                                    <!-- Background grid lines -->
                                    <div class="position-absolute top-0 bottom-0 start-0 end-0 d-flex pointer-events-none" style="z-index: 1;">
                                        <?php for ($i = 1; $i < $numMonths; $i++): ?>
                                            <div class="h-100 border-end border-secondary border-opacity-10" style="width: <?= 100 / $numMonths ?>%;"></div>
                                        <?php endfor; ?>
                                    </div>
                                    
                                    <!-- Visual schedule timeline bar -->
                                    <div class="position-relative w-100" style="height: 32px; z-index: 2;">
                                        <div class="gantt-bar position-absolute top-0 h-100 border border-primary border-opacity-20 shadow-sm" 
                                             style="left: <?= $leftPercent ?>%; width: <?= $widthPercent ?>%; border-radius: 6px; background-color: var(--border-color); overflow: hidden;"
                                             title="<?= e($p['project_name']) ?>: <?= format_date($p['start_date']) ?> to <?= format_date($p['target_end_date']) ?> (<?= $p['progress_percentage'] ?>%)">
                                            
                                            <!-- Completed Subbar -->
                                            <div class="h-100 bg-success bg-opacity-75" style="width: <?= $p['progress_percentage'] ?>%;"></div>
                                            
                                            <!-- Project Code and Progress Overlay -->
                                            <div class="position-absolute top-0 bottom-0 start-0 end-0 d-flex align-items-center px-2 pointer-events-none">
                                                <span class="text-white fw-bold small text-truncate shadow-text">
                                                    <?= (int)$p['progress_percentage'] ?>% Completed
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Gantt Legend -->
            <div class="d-flex gap-3 justify-content-end align-items-center mt-3 text-muted small">
                <div class="d-flex align-items-center gap-1">
                    <span class="d-inline-block rounded bg-success" style="width:12px; height:12px;"></span>
                    <span>Completed Progress</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <span class="d-inline-block rounded border border-primary border-opacity-20" style="width:12px; height:12px; background-color: var(--border-color);"></span>
                    <span>Planned Timeline</span>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center p-5 text-muted">
                <i class="bi bi-calendar3 display-4 mb-3 d-block"></i>
                <h5>No active projects available for visualization</h5>
                <p>Ensure you have projects with a status of 'active' to display in the Gantt timeline.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
