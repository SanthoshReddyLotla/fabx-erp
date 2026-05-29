<?php
/**
 * FabX ERP - Dashboard View
 * Main dashboard with KPIs, charts, and activity widgets
 */
$stats = $stats ?? [];
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </h1>
    <div class="page-actions">
        <span class="iso-badge-header me-2">
            <i class="bi bi-shield-check"></i> ISO 9001:2015
        </span>
        <span class="badge bg-success">
            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
            FY <?= date('Y') ?>-<?= date('Y')+1 ?>
        </span>
    </div>
</div>

<!-- KPI Stats Grid -->
<div class="dashboard-stats-grid">
    <!-- Active Projects -->
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="bi bi-kanban"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Active Projects</div>
            <div class="stat-value"><?= number_format($stats['active_projects'] ?? 0) ?></div>
            <div class="stat-change <?= ($stats['delayed_projects'] ?? 0) > 0 ? 'negative' : 'positive' ?>">
                <i class="bi bi-<?= ($stats['delayed_projects'] ?? 0) > 0 ? 'exclamation-triangle' : 'check-circle' ?>"></i>
                <?= ($stats['delayed_projects'] ?? 0) ?> delayed
            </div>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="stat-card stat-success">
        <div class="stat-icon">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Monthly Revenue</div>
            <div class="stat-value"><?= format_currency($stats['monthly_revenue'] ?? 0) ?></div>
            <div class="stat-change positive">
                <i class="bi bi-arrow-up-short"></i>
                <?= format_currency($stats['total_receivable'] ?? 0) ?> receivable
            </div>
        </div>
    </div>

    <!-- Open NCRs -->
    <div class="stat-card stat-danger">
        <div class="stat-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Open NCRs</div>
            <div class="stat-value"><?= number_format($stats['open_ncrs'] ?? 0) ?></div>
            <div class="stat-change <?= ($stats['open_capas'] ?? 0) > 0 ? 'warning' : 'positive' ?>">
                <i class="bi bi-arrow-repeat"></i>
                <?= $stats['open_capas'] ?? 0 ?> CAPA pending
            </div>
        </div>
    </div>

    <!-- Pending Quotations -->
    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <i class="bi bi-file-text"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Pending Quotations</div>
            <div class="stat-value"><?= number_format($stats['pending_quotations'] ?? 0) ?></div>
            <div class="stat-change positive">
                <i class="bi bi-clock"></i>
                Awaiting client response
            </div>
        </div>
    </div>

    <!-- Outstanding Invoices -->
    <div class="stat-card stat-accent">
        <div class="stat-icon">
            <i class="bi bi-receipt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Outstanding</div>
            <div class="stat-value"><?= format_currency($stats['outstanding_invoices'] ?? 0) ?></div>
            <div class="stat-change negative">
                <i class="bi bi-hourglass-split"></i>
                Pending collection
            </div>
        </div>
    </div>

    <!-- Employee Attendance -->
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Today's Attendance</div>
            <div class="stat-value"><?= $stats['todays_attendance'] ?? 0 ?>/<?= $stats['employee_count'] ?? 0 ?></div>
            <div class="stat-change positive">
                <i class="bi bi-check-circle"></i>
                <?= $stats['employee_count'] > 0 ? round((($stats['todays_attendance'] ?? 0) / $stats['employee_count']) * 100) : 0 ?>% present
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="dashboard-row">
    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-graph-up text-primary"></i>
                Revenue Trend (₹ Lakhs)
            </h5>
            <div class="card-actions">
                <button class="btn-icon" data-bs-toggle="tooltip" title="Refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button class="btn-icon" data-bs-toggle="tooltip" title="Export" onclick="exportChart('revenueChart')">
                    <i class="bi bi-download"></i>
                </button>
            </div>
        </div>
        <div class="fx-card-body">
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-shield-check text-success"></i>
                Quality Metrics
            </h5>
        </div>
        <div class="fx-card-body">
            <div class="chart-container-sm">
                <canvas id="qualityChart"></canvas>
            </div>
            <div class="text-center mt-3">
                <div class="d-flex justify-content-center gap-3" style="font-size: 0.8rem;">
                    <span><span class="status-dot online"></span> Accepted: 87%</span>
                    <span><span class="status-dot busy"></span> Rejected: 5%</span>
                    <span><span class="status-dot away"></span> Rework: 5%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="dashboard-row">
    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-factory text-warning"></i>
                Production by Category (MT)
            </h5>
            <div class="card-actions">
                <button class="btn-icon" data-bs-toggle="tooltip" title="Export">
                    <i class="bi bi-download"></i>
                </button>
            </div>
        </div>
        <div class="fx-card-body">
            <div class="chart-container">
                <canvas id="productionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-kanban text-info"></i>
                Projects by Stage
            </h5>
        </div>
        <div class="fx-card-body">
            <div class="chart-container">
                <canvas id="projectStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: NCR + Pipeline + Attendance -->
<div class="dashboard-row-3">
    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-exclamation-triangle text-danger"></i>
                NCR Trend
            </h5>
        </div>
        <div class="fx-card-body">
            <div class="chart-container-sm">
                <canvas id="ncrTrendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-funnel text-primary"></i>
                Sales Pipeline
            </h5>
        </div>
        <div class="fx-card-body">
            <div class="chart-container-sm">
                <canvas id="salesPipelineChart"></canvas>
            </div>
        </div>
    </div>

    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-calendar-check text-success"></i>
                Weekly Attendance
            </h5>
        </div>
        <div class="fx-card-body">
            <div class="chart-container-sm">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Recent Activity & Upcoming -->
<div class="dashboard-row">
    <!-- Recent Projects -->
    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-kanban text-primary"></i>
                Active Projects
            </h5>
            <a href="<?= base_url('projects') ?>" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="fx-card-body p-0">
            <div class="table-responsive-fx">
                <table class="fx-table mb-0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Stage</th>
                            <th>Progress</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_projects)): ?>
                            <?php foreach ($recent_projects as $project): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($project['project_code']) ?></strong>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= e(truncate($project['project_name'], 30)) ?></div>
                                    </td>
                                    <td><?= e($project['client_name'] ?? '-') ?></td>
                                    <td><?= status_badge($project['current_stage'] ?? 'planning') ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-fx flex-grow-1" style="width: 80px;">
                                                <div class="progress-fx-bar" style="width: <?= $project['progress_percentage'] ?? 0 ?>"></div>
                                            </div>
                                            <small class="text-muted"><?= round($project['progress_percentage'] ?? 0) ?>%</small>
                                        </div>
                                    </td>
                                    <td><?= format_currency($project['contract_value'] ?? 0) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No active projects</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upcoming Tasks & Calendar -->
    <div class="fx-card">
        <div class="fx-card-header">
            <h5 class="fx-card-title">
                <i class="bi bi-calendar-event text-warning"></i>
                Upcoming Tasks (14 Days)
            </h5>
        </div>
        <div class="fx-card-body">
            <div class="fx-timeline">
                <?php if (!empty($upcoming_tasks)): ?>
                    <?php foreach ($upcoming_tasks as $task): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot <?= $task['priority'] ?? 'info' ?>"></div>
                            <div class="timeline-content">
                                <p class="timeline-title"><?= e($task['title']) ?></p>
                                <p class="timeline-text"><?= ucfirst($task['type']) ?> - Due <?= format_date($task['due_date']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state py-4">
                        <i class="bi bi-calendar-check"></i>
                        <p class="mb-0">No upcoming tasks</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent NCRs Row -->
<div class="fx-card mt-4">
    <div class="fx-card-header">
        <h5 class="fx-card-title">
            <i class="bi bi-exclamation-triangle text-danger"></i>
            Recent Open NCRs
        </h5>
        <a href="<?= base_url('qms/ncr') ?>" class="btn btn-sm btn-outline-danger">View All</a>
    </div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>NCR No</th>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Description</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Reported By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_ncrs)): ?>
                        <?php foreach ($recent_ncrs as $ncr): ?>
                            <tr>
                                <td><strong><?= e($ncr['ncr_no']) ?></strong></td>
                                <td><?= format_date($ncr['ncr_date']) ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $ncr['source'])) ?></td>
                                <td><?= e(truncate($ncr['description'], 50)) ?></td>
                                <td><?= priority_badge($ncr['severity']) ?></td>
                                <td><?= status_badge($ncr['status']) ?></td>
                                <td><?= e($ncr['reported_by_name'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No open NCRs - Great job!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Export chart as image
function exportChart(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (canvas) {
        const link = document.createElement('a');
        link.download = canvasId + '_<?= date('Y-m-d') ?>.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
}
</script>
