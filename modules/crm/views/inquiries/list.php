<?php /** FabX ERP - CRM Inquiries List View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-envelope-open"></i> Inquiries (New Opportunities)</h1>
</div>

<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-shield-check"></i> Incoming Opportunity Matrix & Triage</h5>
        <span class="badge bg-dark border border-secondary text-muted">Showing <?= count($inquiries) ?> Leads</span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0" id="inquiriesTable">
                <thead>
                    <tr>
                        <th>Lead Ref</th>
                        <th>Company Name</th>
                        <th>Communication Points</th>
                        <th>Company Description / Requirements</th>
                        <th class="text-end">Est. Revenue</th>
                        <th>Source</th>
                        <th>Entry Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inquiries)): ?>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-dark border border-secondary font-monospace py-2 px-3">
                                        <?= e($inq['lead_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($inq['company_name']) ?></div>
                                    <?php if ($inq['industry']): ?>
                                        <small class="text-muted d-block"><?= e($inq['industry']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-light-heading"><i class="bi bi-person text-primary me-1"></i><?= e($inq['contact_person'] ?? '-') ?></div>
                                    <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= e($inq['email'] ?? '-') ?></div>
                                    <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= e($inq['phone'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <div class="fw-normal text-light-heading text-wrap" style="max-width: 320px; font-size: 0.85rem; line-height: 1.4;">
                                        <?= e($inq['requirements'] ?? 'No detailed description / requirements provided.') ?>
                                    </div>
                                    <?php if (!empty($inq['remarks'])): ?>
                                        <small class="text-muted d-block mt-1"><i class="bi bi-chat-left-dots text-warning me-1"></i><?= e($inq['remarks']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    <?= format_currency($inq['estimated_value'] ?? 0) ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 text-uppercase" style="font-size:0.65rem;">
                                        <?= e(ucfirst($inq['source'] ?? '-')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-light-heading"><?= format_date($inq['created_at']) ?></div>
                                    <small class="text-muted d-block" style="font-size:0.7rem;"><?= date('H:i A', strtotime($inq['created_at'])) ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state py-5">
                                    <i class="bi bi-envelope-open display-4 mb-3 d-block text-muted"></i>
                                    <h5>No New Inquiries Found</h5>
                                    <p>Triage directory is currently empty. Incoming website, cold calls, or referral inquiries will appear here.</p>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> items</small>
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
