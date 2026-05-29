<?php /** FabX ERP - Production Reports View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bar-chart-line"></i> Production Reports</h1>
    <div class="page-actions">
        <button onclick="exportTableToCSV('prodTable','production_report.csv')" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export</button>
    </div>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="prodTable">
                <thead>
                    <tr>
                        <th>Date</th><th>Project</th><th>Activity</th>
                        <th>Qty Planned</th><th>Qty Completed</th><th>% Done</th><th>Reported By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td><?= format_date($r['report_date']) ?></td>
                                <td><?= e($r['project_name'] ?? '-') ?></td>
                                <td><?= e($r['activity'] ?? '-') ?></td>
                                <td><?= e($r['planned_quantity'] ?? '-') ?></td>
                                <td><?= e($r['completed_quantity'] ?? '-') ?></td>
                                <td>
                                    <?php $pct = $r['planned_quantity'] ? round(($r['completed_quantity'] / $r['planned_quantity']) * 100) : 0; ?>
                                    <div class="progress" style="height:6px;width:60px;display:inline-block;vertical-align:middle;">
                                        <div class="progress-bar" style="width:<?= $pct ?>%"></div>
                                    </div>
                                    <small class="ms-1"><?= $pct ?>%</small>
                                </td>
                                <td><?= e($r['reported_by_name'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-bar-chart-line"></i><h5>No production reports found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
