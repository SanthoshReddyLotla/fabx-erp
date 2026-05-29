<?php /** FabX ERP - Material Issues List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-arrow-bar-right"></i> Material Issues</h1>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Issue No</th><th>Project</th><th>Issued To</th>
                        <th>Issue Date</th><th>Purpose</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($issues)): ?>
                        <?php foreach ($issues as $iss): ?>
                            <tr>
                                <td><strong><?= e($iss['issue_no'] ?? '-') ?></strong></td>
                                <td><?= e($iss['project_name'] ?? '-') ?></td>
                                <td><?= e($iss['issued_to_name'] ?? '-') ?></td>
                                <td><?= format_date($iss['issue_date'] ?? null) ?></td>
                                <td><?= e(substr($iss['purpose'] ?? '', 0, 60)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5"><div class="empty-state"><i class="bi bi-arrow-bar-right"></i><h5>No material issues found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
