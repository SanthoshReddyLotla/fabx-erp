<?php /** FabX ERP - Sales Pipeline (Kanban) View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-columns-gap"></i> Sales Pipeline</h1>
</div>

<div class="row g-3 px-1" style="display: flex; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 1.5rem; min-height: calc(100vh - 220px);">
    <?php
    $stages = [
        'new' => ['label' => 'New Inquiries', 'icon' => 'bi-star', 'color' => 'info', 'badge' => 'badge-fx-info'],
        'contacted' => ['label' => 'Contacted', 'icon' => 'bi-telephone', 'color' => 'primary', 'badge' => 'badge-fx-primary'],
        'qualified' => ['label' => 'Qualified', 'icon' => 'bi-check-circle', 'color' => 'secondary', 'badge' => 'badge-fx-secondary'],
        'proposal_sent' => ['label' => 'Proposal Sent', 'icon' => 'bi-send', 'color' => 'warning', 'badge' => 'badge-fx-warning'],
        'negotiation' => ['label' => 'Negotiation', 'icon' => 'bi-chat-dots', 'color' => 'orange', 'badge' => 'badge-fx-warning'],
        'won' => ['label' => 'Won / Closed', 'icon' => 'bi-trophy', 'color' => 'success', 'badge' => 'badge-fx-success'],
    ];
    
    foreach ($stages as $key => $stage):
        $leads = $pipeline[$key] ?? [];
        $totalWeight = 0;
        foreach ($leads as $lead) {
            $totalWeight += (float)($lead['estimated_value'] ?? 0);
        }
    ?>
    <div class="col-12 col-md-4 col-lg-3 flex-shrink-0" style="width: 290px;">
        <div class="fx-card h-100 d-flex flex-column border border-secondary border-opacity-25" style="background-color: var(--card-bg);">
            <!-- Stage Header -->
            <div class="fx-card-header py-3 px-3 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 text-light-heading fw-bold d-flex align-items-center gap-2">
                        <i class="bi <?= $stage['icon'] ?> text-<?= $stage['color'] === 'orange' ? 'warning' : $stage['color'] ?>"></i>
                        <?= $stage['label'] ?>
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Weight: ₹<?= number_format($totalWeight, 0) ?></small>
                </div>
                <span class="badge bg-dark border border-secondary text-light-heading rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                    <?= count($leads) ?>
                </span>
            </div>
            
            <!-- Stage Cards Deck -->
            <div class="fx-card-body p-2 flex-grow-1 overflow-auto" style="max-height: 580px; min-height: 250px; background-color: rgba(0,0,0,0.15);">
                <?php if (!empty($leads)): ?>
                    <?php foreach ($leads as $lead): 
                        $priorityClass = match($lead['priority'] ?? 'medium') {
                            'hot' => 'bg-danger text-white',
                            'high' => 'bg-warning text-dark',
                            'low' => 'bg-secondary text-light',
                            default => 'bg-info text-dark'
                        };
                    ?>
                        <div class="fx-card mb-2.5 p-3 shadow-sm border border-secondary border-opacity-10 hover-effect transition-all" style="background-color: var(--body-bg); border-radius: 6px;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-dark text-muted font-monospace small px-2 py-0.5" style="font-size:0.65rem; border: 1px solid rgba(255,255,255,0.05);"><?= e($lead['lead_no']) ?></span>
                                <span class="badge <?= $priorityClass ?> text-uppercase fw-bold" style="font-size:0.6rem; letter-spacing: 0.3px; padding: 2px 6px; border-radius: 3px;"><?= e($lead['priority'] ?? 'medium') ?></span>
                            </div>
                            
                            <h6 class="fw-bold text-light-heading mb-1 text-truncate" title="<?= e($lead['company_name']) ?>" style="font-size: 0.875rem;">
                                <?= e($lead['company_name']) ?>
                            </h6>
                            <div class="text-muted mb-2 text-truncate" style="font-size: 0.75rem;"><i class="bi bi-person me-1"></i><?= e($lead['contact_person'] ?? '-') ?></div>
                            
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-10 mt-2">
                                <span class="fw-bold text-success" style="font-size: 0.85rem;">
                                    <?= format_currency($lead['estimated_value'] ?? 0) ?>
                                </span>
                                <span class="text-muted" style="font-size: 0.65rem;" title="Creation Date">
                                    <i class="bi bi-calendar-event me-1"></i><?= date('d M', strtotime($lead['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5 text-muted">
                        <i class="bi bi-inbox display-6 mb-2 opacity-25"></i>
                        <span style="font-size:0.75rem;">No Active Deals</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
/* CSS Scrollbar customization for Kanban row scroll */
.row.g-3 {
    scrollbar-width: thin;
    scrollbar-color: var(--border-color) transparent;
}
.row.g-3::-webkit-scrollbar {
    height: 8px;
}
.row.g-3::-webkit-scrollbar-thumb {
    background-color: var(--border-color);
    border-radius: 4px;
}
.row.g-3::-webkit-scrollbar-track {
    background: transparent;
}

/* Kanban Inner Deck Scrollbar */
.fx-card-body.overflow-auto {
    scrollbar-width: none; /* Hide standard firefox scrollbars */
}
.fx-card-body.overflow-auto::-webkit-scrollbar {
    width: 4px;
}
.fx-card-body.overflow-auto::-webkit-scrollbar-thumb {
    background-color: rgba(255,255,255,0.05);
    border-radius: 2px;
}
.fx-card-body.overflow-auto::-webkit-scrollbar-track {
    background: transparent;
}

/* Card hover animation */
.hover-effect:hover {
    transform: translateY(-2px);
    border-color: var(--primary-color) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
</style>
