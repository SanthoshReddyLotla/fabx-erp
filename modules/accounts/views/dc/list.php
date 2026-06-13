<?php /** FabX ERP - Delivery Challans list */
$reasonLabels = ['supply'=>'Supply','job_work'=>'Job Work','sample'=>'Sample','approval'=>'On Approval','return'=>'Sales Return','others'=>'Others'];
?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-truck text-primary"></i> Delivery Challans</h1>
    <div class="page-actions">
        <a href="<?= base_url('accounts/delivery-challans/create') ?>" class="btn btn-fx btn-fx-primary"><i class="bi bi-plus-lg"></i> New Delivery Challan</a>
    </div>
</div>

<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>DC No</th><th>Date</th><th>Consignee</th><th>Reason</th>
                        <th>Project</th><th>Vehicle</th><th>Status</th><th class="actions text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($challans)): ?>
                        <?php foreach ($challans as $dc): ?>
                            <tr>
                                <td class="fw-semibold text-info"><?= e($dc['dc_no']) ?></td>
                                <td class="small"><?= format_date($dc['dc_date']) ?></td>
                                <td><?= e($dc['client_name'] ?? '-') ?></td>
                                <td><span class="badge-fx badge-fx-secondary"><?= e($reasonLabels[$dc['reason']] ?? ucfirst($dc['reason'])) ?></span></td>
                                <td class="small font-monospace"><?= e($dc['project_code'] ?? '-') ?></td>
                                <td class="small"><?= e($dc['vehicle_no'] ?: '-') ?></td>
                                <td>
                                    <?php $sc = match($dc['status'] ?? '') {
                                        'delivered' => 'badge-fx-success', 'dispatched' => 'badge-fx-info',
                                        'cancelled' => 'badge-fx-danger', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($dc['status'] ?? '') ?></span>
                                </td>
                                <td class="actions text-end">
                                    <a href="<?= base_url('accounts/delivery-challans/print/' . $dc['id']) ?>" target="_blank" class="btn btn-sm btn-light" title="Print / PDF"><i class="bi bi-printer"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-truck"></i>
                                <h5>No delivery challans yet</h5>
                                <a href="<?= base_url('accounts/delivery-challans/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Create Delivery Challan</a>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
