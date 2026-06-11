<?php /** Print template: Non-Conformance Report (ISO 9001) */ ?>

<div class="doc-band">
    <h1>Non-Conformance Report</h1>
    <span class="doc-copy">
        ISO 9001:2015 &middot;
        <?php $st = $ncr['status'] ?? 'open'; ?>
        <span class="pill <?= $st === 'closed' ? 'green' : ($st === 'open' ? 'red' : 'amber') ?>"><?= e(ucwords(str_replace('_', ' ', $st))) ?></span>
    </span>
</div>

<table class="fields">
    <tr>
        <th>NCR No</th><td class="mono"><?= e($ncr['ncr_no']) ?></td>
        <th>NCR Date</th><td><?= format_date($ncr['ncr_date']) ?></td>
    </tr>
    <tr>
        <th>Source</th><td><?= e(ucwords(str_replace('_', ' ', $ncr['source'] ?? '-'))) ?></td>
        <th>Category</th><td><?= e(ucfirst($ncr['category'] ?? '-')) ?></td>
    </tr>
    <tr>
        <th>Severity</th>
        <td><span class="pill <?= ($ncr['severity'] ?? '') === 'critical' ? 'red' : (($ncr['severity'] ?? '') === 'major' ? 'amber' : '') ?>"><?= e(ucfirst($ncr['severity'] ?? '-')) ?></span></td>
        <th>Department</th><td><?= e($ncr['department_name'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Project</th><td><?= e($ncr['project_name'] ?? '-') ?></td>
        <th>Reported By</th><td><?= e($ncr['reported_by_name'] ?? '-') ?> on <?= format_date($ncr['reported_date']) ?></td>
    </tr>
</table>

<div class="section-title">1. Description of Non-Conformance</div>
<p class="longtext"><?= e($ncr['description'] ?: '-') ?></p>

<div class="section-title">2. Immediate / Containment Action</div>
<p class="longtext"><?= e($ncr['immediate_action'] ?: '-') ?></p>

<div class="section-title">3. Root Cause Analysis</div>
<p class="longtext"><?= e($ncr['root_cause'] ?: 'Pending investigation') ?></p>

<div class="section-title">4. Corrective Action</div>
<p class="longtext"><?= e($ncr['corrective_action'] ?: 'Pending') ?></p>

<div class="section-title">5. Preventive Action</div>
<p class="longtext"><?= e($ncr['preventive_action'] ?: 'Pending') ?></p>

<div class="section-title">6. Responsibility &amp; Verification</div>
<table class="fields">
    <tr>
        <th>Responsible</th><td><?= e($ncr['responsible_name'] ?? '-') ?></td>
        <th>Target Date</th><td><?= format_date($ncr['target_date']) ?></td>
    </tr>
    <tr>
        <th>Completed On</th><td><?= format_date($ncr['completion_date']) ?></td>
        <th>Verified By</th><td><?= e($ncr['verified_by_name'] ?? '-') ?> <?= !empty($ncr['verification_date']) ? '(' . format_date($ncr['verification_date']) . ')' : '' ?></td>
    </tr>
    <?php if (!empty($ncr['verification_method'])): ?>
        <tr><th>Verification Method</th><td colspan="3"><?= e($ncr['verification_method']) ?></td></tr>
    <?php endif; ?>
</table>

<div class="signatures">
    <div class="sig"><div class="line"></div><span class="role">Reported By</span></div>
    <div class="sig"><div class="line"></div><span class="role">Quality Manager</span></div>
    <div class="sig"><div class="line"></div><span class="role">Verified / Closed By</span></div>
</div>
