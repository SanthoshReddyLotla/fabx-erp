<?php
/** Print template: Delivery Challan (Rule 55) */
$reasonLabels = [
    'supply' => 'Supply', 'job_work' => 'Job Work', 'sample' => 'Sample / Display',
    'approval' => 'Supply on Approval', 'return' => 'Sales Return', 'others' => 'Others',
];
$totalQty = 0; $totalVal = 0;
foreach ($items as $it) { $totalQty += (float)$it['quantity']; $totalVal += (float)$it['value']; }
$hasValue = $totalVal > 0;
?>

<div class="doc-band">
    <h1>Delivery Challan</h1>
    <span class="doc-copy">Not a Tax Invoice &middot; Rule 55</span>
</div>

<div class="blocks">
    <div class="block">
        <h4>Consignee (Ship To)</h4>
        <div class="party-name"><?= e($dc['client_name'] ?? '-') ?></div>
        <p><?= e($dc['ship_to_address'] ?: ($dc['client_address'] ?? '')) ?></p>
        <?php if (!empty($dc['client_gstin'])): ?>
            <p style="margin-top:4px"><strong>GSTIN:</strong> <span class="mono"><?= e($dc['client_gstin']) ?></span></p>
        <?php endif; ?>
    </div>
    <div class="block" style="flex:0.9">
        <h4>Challan Details</h4>
        <table class="meta">
            <tr><td class="k">DC No</td><td class="v mono"><?= e($dc['dc_no']) ?></td></tr>
            <tr><td class="k">DC Date</td><td class="v"><?= format_date($dc['dc_date']) ?></td></tr>
            <tr><td class="k">Reason</td><td class="v"><?= e($reasonLabels[$dc['reason']] ?? ucfirst($dc['reason'])) ?></td></tr>
            <?php if (!empty($dc['project_code'])): ?>
                <tr><td class="k">Project</td><td class="v"><?= e($dc['project_code']) ?></td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<table class="fields" style="margin-bottom:14px">
    <tr>
        <th>Vehicle No</th><td><?= e($dc['vehicle_no'] ?: '-') ?></td>
        <th>Transport Mode</th><td><?= e($dc['transport_mode'] ?: '-') ?></td>
    </tr>
    <tr>
        <th>Transporter</th><td><?= e($dc['transporter'] ?: '-') ?></td>
        <th>E-Way Bill No</th><td class="mono"><?= e($dc['eway_bill_no'] ?: '-') ?></td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <th style="width:9mm">#</th>
            <th>Description of Goods</th>
            <th style="width:24mm">HSN / SAC</th>
            <th class="num" style="width:24mm">Qty</th>
            <th style="width:18mm">UOM</th>
            <?php if ($hasValue): ?><th class="num" style="width:30mm">Value (&#8377;)</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= (int)$it['sr_no'] ?></td>
                <td>
                    <?= e($it['description']) ?>
                    <?php if (!empty($it['remarks'])): ?><div class="spec"><?= e($it['remarks']) ?></div><?php endif; ?>
                </td>
                <td class="mono"><?= e($it['hsn_code'] ?: '-') ?></td>
                <td class="num"><?= rtrim(rtrim(number_format((float)$it['quantity'], 3), '0'), '.') ?></td>
                <td><?= e($it['uom'] ?: 'Nos') ?></td>
                <?php if ($hasValue): ?><td class="num"><?= number_format((float)$it['value'], 2) ?></td><?php endif; ?>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
            <tr><td colspan="<?= $hasValue ? 6 : 5 ?>" style="text-align:center;color:#94a3b8;padding:14px">No items</td></tr>
        <?php endif; ?>
        <tr style="font-weight:700;border-top:2px solid #2c3e50">
            <td colspan="3" style="text-align:right">Total</td>
            <td class="num"><?= rtrim(rtrim(number_format($totalQty, 3), '0'), '.') ?></td>
            <td></td>
            <?php if ($hasValue): ?><td class="num"><?= number_format($totalVal, 2) ?></td><?php endif; ?>
        </tr>
    </tbody>
</table>

<?php if ($hasValue): ?>
    <div class="in-words" style="margin-bottom:14px">
        <strong>Value in words:</strong> <?= e(amount_in_words($totalVal)) ?>
    </div>
<?php endif; ?>

<?php if (!empty($dc['remarks'])): ?>
    <div class="section-title">Remarks</div>
    <p class="longtext"><?= e($dc['remarks']) ?></p>
<?php endif; ?>

<p style="font-size:0.76rem;color:#64748b;margin-bottom:6px">
    Certified that the particulars given above are true and correct. The goods described are dispatched for the purpose stated and this challan does not constitute a tax invoice.
</p>

<div class="signatures">
    <div class="sig"><div class="line"></div>Receiver's Signature</div>
    <div class="sig" style="flex:1.5"></div>
    <div class="sig"><div class="line"></div>For <?= e($company['company_name'] ?? COMPANY_NAME) ?><br><span class="role">Authorised Signatory</span></div>
</div>
