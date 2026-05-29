<?php /** FabX ERP - CRM Follow-ups List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-clock-history"></i> Follow-ups</h1>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>Lead / Company</th><th>Type</th><th>Notes</th>
                        <th>Conducted By</th><th>Follow-up Date</th><th>Next Follow-up</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($followups)): ?>
                        <?php foreach ($followups as $f): ?>
                            <tr>
                                <td><strong><?= e($f['lead_name'] ?? '-') ?></strong></td>
                                <td><?= e(ucfirst($f['type'] ?? '-')) ?></td>
                                <td><?= e(substr($f['notes'] ?? '', 0, 80)) ?><?= strlen($f['notes'] ?? '') > 80 ? '...' : '' ?></td>
                                <td><?= e($f['conducted_by_name'] ?? '-') ?></td>
                                <td><?= format_date($f['followup_date']) ?></td>
                                <td><?= format_date($f['next_followup_date'] ?? null) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-clock-history"></i>
                                <h5>No follow-ups recorded</h5>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
