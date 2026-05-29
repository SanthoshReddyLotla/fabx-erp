<?php /** FabX ERP - CRM Inquiries List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-envelope-open"></i> Inquiries</h1>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="inquiriesTable">
                <thead>
                    <tr>
                        <th>Company</th><th>Contact Person</th><th>Email</th>
                        <th>Phone</th><th>Source</th><th>Value</th><th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inquiries)): ?>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr>
                                <td><strong><?= e($inq['company_name'] ?? '-') ?></strong></td>
                                <td><?= e($inq['contact_person'] ?? '-') ?></td>
                                <td><?= e($inq['email'] ?? '-') ?></td>
                                <td><?= e($inq['phone'] ?? '-') ?></td>
                                <td><?= e(ucfirst($inq['source'] ?? '-')) ?></td>
                                <td><?= format_currency($inq['estimated_value'] ?? 0) ?></td>
                                <td><?= format_date($inq['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-envelope-open"></i>
                                <h5>No new inquiries</h5>
                                <p>New inquiries will appear here.</p>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
