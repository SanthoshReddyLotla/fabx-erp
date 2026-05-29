<?php /** FabX ERP - Sales Pipeline (Kanban) View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-columns-gap"></i> Sales Pipeline</h1>
</div>

<div class="row g-3" style="flex-wrap:nowrap;overflow-x:auto;padding-bottom:1rem;">
    <?php
    $stages = [
        'new' => ['label' => 'New', 'icon' => 'bi-star', 'color' => 'info'],
        'contacted' => ['label' => 'Contacted', 'icon' => 'bi-telephone', 'color' => 'primary'],
        'qualified' => ['label' => 'Qualified', 'icon' => 'bi-check-circle', 'color' => 'secondary'],
        'proposal_sent' => ['label' => 'Proposal Sent', 'icon' => 'bi-send', 'color' => 'warning'],
        'negotiation' => ['label' => 'Negotiation', 'icon' => 'bi-chat-dots', 'color' => 'orange'],
        'won' => ['label' => 'Won', 'icon' => 'bi-trophy', 'color' => 'success'],
    ];
    foreach ($stages as $key => $stage):
        $leads = $pipeline[$key] ?? [];
    ?>
    <div style="min-width:240px;">
        <div class="fx-card h-100">
            <div class="fx-card-header bg-<?= $stage['color'] === 'orange' ? 'warning' : $stage['color'] ?> text-<?= in_array($stage['color'], ['warning']) ? 'dark' : 'white' ?> p-2">
                <strong><i class="bi <?= $stage['icon'] ?>"></i> <?= $stage['label'] ?></strong>
                <span class="badge bg-white text-dark float-end"><?= count($leads) ?></span>
            </div>
            <div class="fx-card-body p-2" style="min-height:200px;">
                <?php if (!empty($leads)): ?>
                    <?php foreach ($leads as $lead): ?>
                        <div class="card mb-2 border-0 shadow-sm">
                            <div class="card-body p-2">
                                <div class="fw-bold small"><?= e($lead['company_name'] ?? 'Unknown') ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= e($lead['contact_person'] ?? '') ?></div>
                                <?php if ($lead['estimated_value'] ?? 0): ?>
                                    <div class="text-success small mt-1"><?= format_currency($lead['estimated_value']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted p-3" style="font-size:0.8rem;">No leads</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
