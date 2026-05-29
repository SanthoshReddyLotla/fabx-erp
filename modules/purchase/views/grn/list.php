<?php /** FabX ERP - GRN List */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-box-seam"></i> Goods Receipt Notes (GRN)</h1>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0">
                <thead>
                    <tr>
                        <th>GRN No</th><th>PO No</th><th>Vendor</th>
                        <th>Receipt Date</th><th>Invoice No</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($grns)): ?>
                        <?php foreach ($grns as $grn): ?>
                            <tr>
                                <td><strong><?= e($grn['grn_no'] ?? '-') ?></strong></td>
                                <td><?= e($grn['po_no'] ?? '-') ?></td>
                                <td><?= e($grn['vendor_name'] ?? '-') ?></td>
                                <td><?= format_date($grn['receipt_date'] ?? null) ?></td>
                                <td><?= e($grn['vendor_invoice_no'] ?? '-') ?></td>
                                <td>
                                    <?php $sc = match($grn['inspection_status'] ?? '') {
                                        'passed' => 'badge-fx-success', 'failed' => 'badge-fx-danger',
                                        'pending' => 'badge-fx-warning', default => 'badge-fx-secondary'
                                    }; ?>
                                    <span class="badge-fx <?= $sc ?>"><?= ucfirst($grn['inspection_status'] ?? 'pending') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-box-seam"></i><h5>No GRN records found</h5></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
