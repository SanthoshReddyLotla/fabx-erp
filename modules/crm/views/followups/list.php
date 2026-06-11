<?php /** FabX ERP - CRM Follow-ups Ledger View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-clock-history"></i> Follow-ups</h1>
</div>

<!-- Follow-ups Card -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Next-Action Schedule & Lifecycle Communications Register</h5>
        <span class="badge bg-dark border border-secondary text-muted">Page <?= $pagination['page'] ?? 1 ?></span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Lead / Company</th>
                        <th>Communication Method</th>
                        <th>Conversation Notes</th>
                        <th>Outcome / Next Steps</th>
                        <th>Conducted By</th>
                        <th>Conducted On</th>
                        <th>Next Action Deadline</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($followups)): ?>
                        <?php foreach ($followups as $f): 
                            $methodIcon = match($f['followup_type'] ?? '') {
                                'call' => '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1"><i class="bi bi-telephone-outbound me-1"></i> Call</span>',
                                'email' => '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1"><i class="bi bi-envelope me-1"></i> Email</span>',
                                'meeting' => '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 text-dark"><i class="bi bi-people me-1"></i> Meeting</span>',
                                'site_visit' => '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="bi bi-geo-alt me-1"></i> Site Visit</span>',
                                default => '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1"><i class="bi bi-chat-dots me-1"></i> Other</span>'
                            };
                            
                            $statusClass = match($f['status'] ?? '') {
                                'scheduled' => 'badge-fx-primary',
                                'completed' => 'badge-fx-success',
                                'cancelled' => 'badge-fx-secondary',
                                'overdue' => 'badge-fx-danger',
                                default => 'badge-fx-secondary'
                            };
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($f['lead_name'] ?? 'General Inquiry') ?></div>
                                </td>
                                <td>
                                    <?= $methodIcon ?>
                                </td>
                                <td>
                                    <div class="text-wrap" style="max-width: 300px; font-size: 0.85rem; line-height: 1.4;">
                                        <?= e($f['notes'] ?? 'No conversation notes registered.') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-wrap text-light-heading" style="max-width: 250px; font-size: 0.85rem; font-weight: 500;">
                                        <?= e($f['outcome'] ?? 'Pending Outcome Details') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center text-primary fw-bold" style="width:28px; height:28px; font-size:0.75rem;">
                                            <?= strtoupper(substr($f['conducted_by_name'] ?? 'O', 0, 2)) ?>
                                        </div>
                                        <span class="small fw-semibold"><?= e($f['conducted_by_name'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td class="small">
                                    <?= format_date($f['followup_date']) ?>
                                </td>
                                <td class="fw-semibold text-warning small">
                                    <?= $f['next_followup'] ? format_date($f['next_followup']) : '<span class="text-muted fw-normal">-</span>' ?>
                                </td>
                                <td>
                                    <span class="badge-fx <?= $statusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $f['status'] ?? '')) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state py-5">
                                    <i class="bi bi-clock-history display-4 mb-3 d-block text-muted"></i>
                                    <h5>No Follow-ups Logged</h5>
                                    <p>Start recording your sales interactions, customer follow-up calls, and scheduled emails here to build a comprehensive communication audit trail.</p>
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
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> follow-ups</small>
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
