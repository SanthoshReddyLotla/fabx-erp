<?php /** Print template: Quotation */ ?>

<div class="doc-band">
    <h1>Quotation</h1>
    <span class="doc-copy"><?= ((int)($quotation['revision_no'] ?? 0)) > 0 ? 'Revision ' . (int)$quotation['revision_no'] : 'Original' ?></span>
</div>

<div class="blocks">
    <div class="block">
        <h4>To</h4>
        <div class="party-name"><?= e($quotation['client_name'] ?? '-') ?></div>
        <?php if (!empty($quotation['contact_person'])): ?>
            <p>Kind Attn: <?= e($quotation['contact_person']) ?></p>
        <?php endif; ?>
        <?php if (!empty($quotation['client_address'])): ?>
            <p><?= e($quotation['client_address']) ?></p>
        <?php endif; ?>
    </div>
    <div class="block" style="flex:0.9">
        <h4>Quotation Details</h4>
        <table class="meta">
            <tr><td class="k">Quotation No</td><td class="v mono"><?= e($quotation['quotation_no']) ?></td></tr>
            <tr><td class="k">Date</td><td class="v"><?= format_date($quotation['quotation_date']) ?></td></tr>
            <tr><td class="k">Valid For</td><td class="v"><?= (int)($quotation['validity_days'] ?? 30) ?> days</td></tr>
            <tr><td class="k">Prepared By</td><td class="v"><?= e($quotation['prepared_by_name'] ?? '-') ?></td></tr>
        </table>
    </div>
</div>

<?php if (!empty($quotation['subject'])): ?>
    <p style="font-size:0.9rem;margin-bottom:6px"><strong>Subject: <?= e($quotation['subject']) ?></strong></p>
<?php endif; ?>
<?php if (!empty($quotation['description'])): ?>
    <p class="longtext" style="margin-bottom:12px"><?= e($quotation['description']) ?></p>
<?php endif; ?>

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
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= (int)$item['sr_no'] ?></td>
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
            <h4>Commercial Terms</h4>
            <table class="meta">
                <?php if (!empty($quotation['delivery_terms'])): ?>
                    <tr><td class="k">Delivery</td><td class="v" style="font-weight:400"><?= e($quotation['delivery_terms']) ?></td></tr>
                <?php endif; ?>
                <?php if (!empty($quotation['payment_terms'])): ?>
                    <tr><td class="k">Payment</td><td class="v" style="font-weight:400"><?= e($quotation['payment_terms']) ?></td></tr>
                <?php endif; ?>
                <?php if (!empty($quotation['warranty'])): ?>
                    <tr><td class="k">Warranty</td><td class="v" style="font-weight:400"><?= e($quotation['warranty']) ?></td></tr>
                <?php endif; ?>
            </table>
            <?php if (!empty($quotation['terms_conditions'])): ?>
                <p class="longtext" style="font-size:0.76rem;margin-top:6px"><?= e($quotation['terms_conditions']) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="totals-box">
        <table class="totals">
            <tr><td class="k">Subtotal</td><td class="v"><?= number_format($quotation['subtotal'], 2) ?></td></tr>
            <?php if ((float)($quotation['discount_amount'] ?? 0) > 0): ?>
                <tr><td class="k">Discount</td><td class="v">- <?= number_format($quotation['discount_amount'], 2) ?></td></tr>
            <?php endif; ?>
            <tr><td class="k">GST @ <?= number_format($quotation['gst_rate'], 1) ?>%</td><td class="v"><?= number_format($quotation['gst_amount'], 2) ?></td></tr>
            <tr class="grand"><td class="k">Total</td><td class="v">&#8377; <?= number_format($quotation['total_amount'], 2) ?></td></tr>
        </table>
        <div class="in-words">
            <strong>Amount in words:</strong><br>
            <?= e(amount_in_words((float)$quotation['total_amount'])) ?>
        </div>
    </div>
</div>

<p style="font-size:0.8rem;color:#475569;margin-bottom:6px">
    We trust the above is in line with your requirements and look forward to your valued order.
</p>

<div class="signatures">
    <div class="sig" style="flex:2.5;text-align:left">
        Thanking you,<br>Yours faithfully,
    </div>
    <div class="sig"><div class="line"></div>For <?= e($company['company_name'] ?? COMPANY_NAME) ?><br><span class="role">Authorised Signatory</span></div>
</div>
