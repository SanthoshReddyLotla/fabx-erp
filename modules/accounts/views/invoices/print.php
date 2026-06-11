<?php /** Print template: GST Tax Invoice */ ?>

<div class="doc-band">
    <h1><?= ($invoice['invoice_type'] ?? 'tax') === 'proforma' ? 'Proforma Invoice' : 'Tax Invoice' ?></h1>
    <span class="doc-copy">Original for Recipient</span>
</div>

<div class="blocks">
    <div class="block">
        <h4>Billed To</h4>
        <div class="party-name"><?= e($invoice['client_name']) ?></div>
        <p><?= e($invoice['billing_address'] ?: ($invoice['client_address'] ?? '')) ?></p>
        <?php if (!empty($invoice['client_gstin'])): ?>
            <p style="margin-top:4px"><strong>GSTIN:</strong> <span class="mono"><?= e($invoice['client_gstin']) ?></span></p>
        <?php endif; ?>
    </div>
    <div class="block">
        <h4>Ship To / Place of Supply</h4>
        <p><?= e($invoice['shipping_address'] ?: $invoice['billing_address'] ?: '-') ?></p>
        <?php if (!empty($invoice['supply_place'])): ?>
            <p style="margin-top:4px"><strong>Place of Supply:</strong> <?= e($invoice['supply_place']) ?></p>
        <?php endif; ?>
    </div>
    <div class="block" style="flex:0.9">
        <h4>Invoice Details</h4>
        <table class="meta">
            <tr><td class="k">Invoice No</td><td class="v mono"><?= e($invoice['invoice_no']) ?></td></tr>
            <tr><td class="k">Invoice Date</td><td class="v"><?= format_date($invoice['invoice_date']) ?></td></tr>
            <tr><td class="k">Due Date</td><td class="v"><?= format_date($invoice['due_date']) ?></td></tr>
            <?php if (!empty($invoice['po_reference'])): ?>
                <tr><td class="k">PO Reference</td><td class="v mono"><?= e($invoice['po_reference']) ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($invoice['project_name'])): ?>
                <tr><td class="k">Project</td><td class="v"><?= e($invoice['project_name']) ?></td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<table class="items">
    <thead>
        <tr>
            <th style="width:9mm">#</th>
            <th>Description</th>
            <th style="width:22mm">HSN / SAC</th>
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
                <td><?= e($item['description']) ?></td>
                <td class="mono"><?= e($item['hsn_code'] ?: '-') ?></td>
                <td class="num"><?= rtrim(rtrim(number_format($item['quantity'], 3), '0'), '.') ?></td>
                <td><?= e($item['uom'] ?: 'Nos') ?></td>
                <td class="num"><?= number_format($item['unit_rate'], 2) ?></td>
                <td class="num"><?= number_format($item['amount'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:14px">No line items</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="totals-row">
    <div class="totals-left">
        <div class="block" style="margin-bottom:10px">
            <h4>Bank Details for Payment</h4>
            <table class="meta">
                <tr><td class="k">Bank</td><td class="v"><?= e($company['company_bank'] ?? '-') ?></td></tr>
                <tr><td class="k">Account No</td><td class="v mono"><?= e($company['company_account_no'] ?? '-') ?></td></tr>
                <tr><td class="k">IFSC</td><td class="v mono"><?= e($company['company_ifsc'] ?? '-') ?></td></tr>
            </table>
        </div>
        <div class="block">
            <h4>Terms &amp; Conditions</h4>
            <p class="longtext" style="font-size:0.76rem"><?= e($invoice['terms_conditions'] ?: "1. Payment due within the agreed credit period.\n2. Interest @ 18% p.a. applicable on overdue amounts.\n3. All disputes subject to local jurisdiction.") ?></p>
        </div>
    </div>
    <div class="totals-box">
        <table class="totals">
            <tr><td class="k">Subtotal</td><td class="v"><?= number_format($invoice['subtotal'], 2) ?></td></tr>
            <?php if ((float)$invoice['discount_amount'] > 0): ?>
                <tr><td class="k">Discount</td><td class="v">- <?= number_format($invoice['discount_amount'], 2) ?></td></tr>
            <?php endif; ?>
            <tr><td class="k">Taxable Value</td><td class="v"><?= number_format($invoice['taxable_amount'], 2) ?></td></tr>
            <?php if ((float)$invoice['igst_amount'] > 0): ?>
                <tr><td class="k">IGST @ <?= number_format($invoice['igst_rate'], 1) ?>%</td><td class="v"><?= number_format($invoice['igst_amount'], 2) ?></td></tr>
            <?php else: ?>
                <tr><td class="k">CGST @ <?= number_format($invoice['cgst_rate'], 1) ?>%</td><td class="v"><?= number_format($invoice['cgst_amount'], 2) ?></td></tr>
                <tr><td class="k">SGST @ <?= number_format($invoice['sgst_rate'], 1) ?>%</td><td class="v"><?= number_format($invoice['sgst_amount'], 2) ?></td></tr>
            <?php endif; ?>
            <?php if ((float)$invoice['round_off'] != 0): ?>
                <tr><td class="k">Round Off</td><td class="v"><?= number_format($invoice['round_off'], 2) ?></td></tr>
            <?php endif; ?>
            <tr class="grand"><td class="k">Grand Total</td><td class="v">&#8377; <?= number_format($invoice['grand_total'], 2) ?></td></tr>
        </table>
        <div class="in-words">
            <strong>Amount in words:</strong><br>
            <?= e($invoice['amount_in_words'] ?: amount_in_words((float)$invoice['grand_total'])) ?>
        </div>
    </div>
</div>

<div class="signatures">
    <div class="sig"><div class="line"></div>Received By<br><span class="role">Customer</span></div>
    <div class="sig" style="flex:1.5"></div>
    <div class="sig"><div class="line"></div>For <?= e($company['company_name'] ?? COMPANY_NAME) ?><br><span class="role">Authorised Signatory</span></div>
</div>
