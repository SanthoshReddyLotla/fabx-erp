<?php /** Print template: Purchase Order */ ?>

<div class="doc-band">
    <h1>Purchase Order</h1>
    <span class="doc-copy">
        <?php $st = $order['status'] ?? 'draft'; ?>
        <span class="pill <?= in_array($st, ['received','closed']) ? 'green' : ($st === 'cancelled' ? 'red' : '') ?>"><?= e(ucfirst($st)) ?></span>
    </span>
</div>

<div class="blocks">
    <div class="block">
        <h4>Vendor / Supplier</h4>
        <div class="party-name"><?= e($order['vendor_name'] ?? '-') ?></div>
        <?php if (!empty($order['vendor_address'])): ?>
            <p><?= e($order['vendor_address']) ?></p>
        <?php endif; ?>
        <?php if (!empty($order['vendor_gstin'])): ?>
            <p style="margin-top:4px"><strong>GSTIN:</strong> <span class="mono"><?= e($order['vendor_gstin']) ?></span></p>
        <?php endif; ?>
        <?php if (!empty($order['vendor_contact'])): ?>
            <p>Attn: <?= e($order['vendor_contact']) ?> <?= !empty($order['vendor_phone']) ? '&middot; ' . e($order['vendor_phone']) : '' ?></p>
        <?php endif; ?>
    </div>
    <div class="block">
        <h4>Deliver To</h4>
        <p><?= e($order['delivery_location'] ?: ($company['company_address'] ?? '-')) ?></p>
        <?php if (!empty($order['delivery_date'])): ?>
            <p style="margin-top:4px"><strong>Required By:</strong> <?= format_date($order['delivery_date']) ?></p>
        <?php endif; ?>
    </div>
    <div class="block" style="flex:0.9">
        <h4>PO Details</h4>
        <table class="meta">
            <tr><td class="k">PO No</td><td class="v mono"><?= e($order['po_no']) ?></td></tr>
            <tr><td class="k">PO Date</td><td class="v"><?= format_date($order['po_date']) ?></td></tr>
            <?php if (!empty($order['pr_no'])): ?>
                <tr><td class="k">PR Reference</td><td class="v mono"><?= e($order['pr_no']) ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($order['quotation_ref'])): ?>
                <tr><td class="k">Quote Ref</td><td class="v mono"><?= e($order['quotation_ref']) ?></td></tr>
            <?php endif; ?>
            <tr><td class="k">Prepared By</td><td class="v"><?= e($order['prepared_by_name'] ?? '-') ?></td></tr>
        </table>
    </div>
</div>

<table class="items">
    <thead>
        <tr>
            <th style="width:9mm">#</th>
            <th>Description</th>
            <th class="num" style="width:20mm">Qty</th>
            <th style="width:14mm">UOM</th>
            <th class="num" style="width:26mm">Rate (&#8377;)</th>
            <th class="num" style="width:30mm">Amount (&#8377;)</th>
        </tr>
    </thead>
    <tbody>
        <?php $sr = 0; foreach ($items as $item): $sr++; ?>
            <tr>
                <td><?= $sr ?></td>
                <td>
                    <?= e($item['description']) ?>
                    <?php if (!empty($item['specification'])): ?>
                        <div class="spec"><?= e($item['specification']) ?></div>
                    <?php endif; ?>
                </td>
                <td class="num"><?= rtrim(rtrim(number_format($item['quantity'], 3), '0'), '.') ?></td>
                <td><?= e($item['uom'] ?: 'Nos') ?></td>
                <td class="num"><?= number_format($item['unit_rate'], 2) ?></td>
                <td class="num"><?= number_format($item['total_amount'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:14px">No line items</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="totals-row">
    <div class="totals-left">
        <div class="block">
            <h4>Terms &amp; Instructions</h4>
            <table class="meta">
                <?php if (!empty($order['payment_terms'])): ?>
                    <tr><td class="k">Payment</td><td class="v" style="font-weight:400"><?= e($order['payment_terms']) ?></td></tr>
                <?php endif; ?>
            </table>
            <p class="longtext" style="font-size:0.76rem;margin-top:6px"><?= e($order['terms_conditions'] ?: "1. Material must conform to the specifications stated above.\n2. Test certificates to accompany each delivery.\n3. Quote our PO number on all challans and invoices.") ?></p>
        </div>
    </div>
    <div class="totals-box">
        <table class="totals">
            <tr><td class="k">Subtotal</td><td class="v"><?= number_format($order['subtotal'], 2) ?></td></tr>
            <?php if ((float)($order['discount'] ?? 0) > 0): ?>
                <tr><td class="k">Discount</td><td class="v">- <?= number_format($order['discount'], 2) ?></td></tr>
            <?php endif; ?>
            <?php if ((float)($order['freight_amount'] ?? 0) > 0): ?>
                <tr><td class="k">Freight</td><td class="v"><?= number_format($order['freight_amount'], 2) ?></td></tr>
            <?php endif; ?>
            <tr><td class="k">GST</td><td class="v"><?= number_format($order['gst_amount'], 2) ?></td></tr>
            <tr class="grand"><td class="k">Total</td><td class="v">&#8377; <?= number_format($order['total_amount'], 2) ?></td></tr>
        </table>
        <div class="in-words">
            <strong>Amount in words:</strong><br>
            <?= e(amount_in_words((float)$order['total_amount'])) ?>
        </div>
    </div>
</div>

<div class="signatures">
    <div class="sig"><div class="line"></div><span class="role">Prepared By</span></div>
    <div class="sig"><div class="line"></div><span class="role">Approved By</span></div>
    <div class="sig"><div class="line"></div>For <?= e($company['company_name'] ?? COMPANY_NAME) ?><br><span class="role">Authorised Signatory</span></div>
</div>
