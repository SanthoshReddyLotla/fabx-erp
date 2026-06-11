<?php
/**
 * QMS Dashboard View - ISO 9001
 */
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-shield-check"></i>
        QMS Dashboard
    </h1>
    <span class="iso-badge-header">
        <i class="bi bi-shield-check"></i> ISO 9001:2015 Certified
    </span>
</div>

<!-- QMS Stats Grid -->
<div class="dashboard-stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="bi bi-files"></i></div>
        <div class="stat-content">
            <div class="stat-label">Controlled Documents</div>
            <div class="stat-value"><?= number_format($stats['total_documents'] ?? 0) ?></div>
            <div class="stat-change positive"><i class="bi bi-check-circle"></i> Document control active</div>
        </div>
    </div>

    <div class="stat-card stat-danger">
        <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="stat-content">
            <div class="stat-label">Open NCRs</div>
            <div class="stat-value"><?= number_format($stats['active_ncrs'] ?? 0) ?></div>
            <div class="stat-change <?= ($stats['active_ncrs'] ?? 0) > 0 ? 'negative' : 'positive' ?>">
                <i class="bi bi-<?= ($stats['active_ncrs'] ?? 0) > 0 ? 'exclamation-circle' : 'check-circle' ?>"></i>
                <?= ($stats['active_ncrs'] ?? 0) > 0 ? 'Requires attention' : 'All closed' ?>
            </div>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
        <div class="stat-content">
            <div class="stat-label">Open CAPAs</div>
            <div class="stat-value"><?= number_format($stats['open_capas'] ?? 0) ?></div>
            <div class="stat-change <?= ($stats['open_capas'] ?? 0) > 0 ? 'warning' : 'positive' ?>">
                <i class="bi bi-clock"></i> Awaiting implementation
            </div>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
        <div class="stat-content">
            <div class="stat-label">Planned Audits</div>
            <div class="stat-value"><?= number_format($stats['planned_audits'] ?? 0) ?></div>
            <div class="stat-change positive"><i class="bi bi-calendar"></i> Scheduled</div>
        </div>
    </div>

    <div class="stat-card stat-accent">
        <div class="stat-icon"><i class="bi bi-thermometer"></i></div>
        <div class="stat-content">
            <div class="stat-label">Calibration Due</div>
            <div class="stat-value"><?= number_format($stats['upcoming_calibrations'] ?? 0) ?></div>
            <div class="stat-change <?= ($stats['overdue_calibrations'] ?? 0) > 0 ? 'negative' : 'warning' ?>">
                <i class="bi bi-<?= ($stats['overdue_calibrations'] ?? 0) > 0 ? 'exclamation-circle' : 'bell' ?>"></i>
                <?= ($stats['overdue_calibrations'] ?? 0) ?> overdue
            </div>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="bi bi-chat-left-text"></i></div>
        <div class="stat-content">
            <div class="stat-label">Open Complaints</div>
            <div class="stat-value"><?= number_format($stats['open_complaints'] ?? 0) ?></div>
            <div class="stat-change <?= ($stats['open_complaints'] ?? 0) > 0 ? 'warning' : 'positive' ?>">
                <i class="bi bi-<?= ($stats['open_complaints'] ?? 0) > 0 ? 'hourglass' : 'check-circle' ?>"></i>
                <?= ($stats['open_complaints'] ?? 0) > 0 ? 'Under investigation' : 'All resolved' ?>
            </div>
        </div>
    </div>
</div>

<!-- QMS Module Cards -->
<div class="row g-3">
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/documents') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(52,152,219,0.1)">
                        <i class="bi bi-files fs-2 text-primary"></i>
                    </div>
                </div>
                <h5>Document Control</h5>
                <p class="text-muted small mb-0">Manage SOPs, work instructions, quality manual, forms & formats with version control and approval workflow.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/ncr') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(231,76,60,0.1)">
                        <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                    </div>
                </div>
                <h5>NCR Management</h5>
                <p class="text-muted small mb-0">Non-conformance reports, root cause analysis, corrective actions and verification tracking.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/capa') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(243,156,18,0.1)">
                        <i class="bi bi-arrow-repeat fs-2 text-warning"></i>
                    </div>
                </div>
                <h5>CAPA</h5>
                <p class="text-muted small mb-0">Corrective and preventive actions with effectiveness verification and closure tracking.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/audits') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(155,89,182,0.1)">
                        <i class="bi bi-clipboard-check fs-2" style="color:#9b59b6"></i>
                    </div>
                </div>
                <h5>Internal Audits</h5>
                <p class="text-muted small mb-0">Schedule audits, manage checklists, record findings and track closure of non-conformities.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/calibration') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(230,126,34,0.1)">
                        <i class="bi bi-thermometer fs-2" style="color:#e67e22"></i>
                    </div>
                </div>
                <h5>Calibration</h5>
                <p class="text-muted small mb-0">Track equipment calibration schedules, certificates and due date alerts.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/training') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(39,174,96,0.1)">
                        <i class="bi bi-mortarboard fs-2 text-success"></i>
                    </div>
                </div>
                <h5>Training Records</h5>
                <p class="text-muted small mb-0">Employee training management, competency matrix and certification tracking.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/complaints') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(52,73,94,0.1)">
                        <i class="bi bi-chat-left-text fs-2" style="color:#34495e"></i>
                    </div>
                </div>
                <h5>Complaints</h5>
                <p class="text-muted small mb-0">Customer complaint handling, investigation and resolution tracking.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/risks') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(231,76,60,0.1)">
                        <i class="bi bi-diagram-3 fs-2 text-danger"></i>
                    </div>
                </div>
                <h5>Risk Assessment</h5>
                <p class="text-muted small mb-0">Identify, assess and mitigate operational, quality and compliance risks.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="<?= base_url('qms/kpi') ?>" class="text-decoration-none">
            <div class="fx-card h-100 p-4 text-center hover-lift">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:rgba(52,152,219,0.1)">
                        <i class="bi bi-bar-chart fs-2 text-primary"></i>
                    </div>
                </div>
                <h5>KPI & Objectives</h5>
                <p class="text-muted small mb-0">Track quality objectives, KPIs and management review action items.</p>
            </div>
        </a>
    </div>
</div>
