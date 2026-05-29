<?php /** FabX ERP - Admin Departments View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-diagram-3"></i> Departments</h1>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Department Name</th><th>Code</th><th>Head</th>
                        <th>Employees</th><th>Cost Center</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($departments)): ?>
                        <?php foreach ($departments as $d): ?>
                            <tr>
                                <td><strong><?= e($d['name']) ?></strong></td>
                                <td><?= e($d['code'] ?? '-') ?></td>
                                <td><?= e($d['head_name'] ?? '-') ?></td>
                                <td><span class="badge bg-primary text-white"><?= e($d['employee_count'] ?? 0) ?></span></td>
                                <td><?= e($d['cost_center'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5"><div class="empty-state"><i class="bi bi-diagram-3"></i><h5>No departments found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
