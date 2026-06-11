<?php /** Print template: Payment Receipt */ ?>

<div class="doc-band">
    <h1>Payment Receipt</h1>
    <span class="doc-copy">Acknowledgement of Payment</span>
</div>

<div class="blocks">
    <div class="block">
        <h4>Received From</h4>
        <div class="party-name"><?= e($payment['client_name'] ?? '-') ?></div>
        <?php if (!empty($payment['client_address'])): ?>
            <p><?= e($payment['client_address']) ?></p>
        <?php endif; ?>
        <?php if (!empty($payment['client_gstin'])): ?>
            <p style="margin-top:4px"><strong>GSTIN:</strong> <span class="mono"><?= e($payment['client_gstin']) ?></span></p>
        <?php endif; ?>
    </div>
    <div class="block" style="flex:0.9">
        <h4>Receipt Details</h4>
        <table class="meta">
            <tr><td class="k">Receipt No</td><td class="v mono"><?= e($payment['receipt_no']) ?></td></tr>
            <tr><td class="k">Receipt Date</td><td class="v"><?= format_date($payment['receipt_date']) ?></td></tr>
            <?php if (!empty($payment['invoice_no'])): ?>
                <tr><td class="k">Against Invoice</td><td class="v mono"><?= e($payment['invoice_no']) ?></td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<table class="fields">
    <tr>
        <th>Payment Mode</th>
        <td><?= strtoupper(e($payment['payment_mode'] ?? '-')) ?></td>
        <th>Transaction Ref</th>
        <td class="mono"><?= e($payment['transaction_ref'] ?: '-') ?></td>
    </tr>
    <tr>
        <th>Transaction Date</th>
        <td><?= format_date($payment['transaction_date']) ?></td>
        <th>Received By</th>
        <td><?= e($payment['received_by_name'] ?? '-') ?></td>
    </tr>
    <?php if (!empty($payment['remarks'])): ?>
        <tr><th>Remarks</th><td colspan="3"><?= e($payment['remarks']) ?></td></tr>
    <?php endif; ?>
</table>

<div class="totals-row">
    <div class="totals-left"></div>
    <div class="totals-box">
        <table class="totals">
            <tr><td class="k">Amount Received</td><td class="v"><?= number_format($payment['amount'], 2) ?></td></tr>
            <?php if ((float)($payment['tds_amount'] ?? 0) > 0): ?>
                <tr><td class="k">TDS Deducted</td><td class="v">- <?= number_format($payment['tds_amount'], 2) ?></td></tr>
            <?php endif; ?>
            <tr class="grand"><td class="k">Net Amount</td><td class="v">&#8377; <?= number_format($payment['net_amount'] ?? $payment['amount'], 2) ?></td></tr>
        </table>
        <div class="in-words">
            <strong>Amount in words:</strong><br>
            <?= e(amount_in_words((float)($payment['net_amount'] ?? $payment['amount']))) ?>
        </div>
    </div>
</div>

<?php if (!empty($payment['invoice_no'])): ?>
<div class="section-title">Invoice Allocation</div>
<table class="items">
    <thead>
        <tr>
            <th>Invoice No</th>
            <th class="num">Invoice Total (&#8377;)</th>
            <th class="num">Paid To Date (&#8377;)</th>
            <th class="num">Balance (&#8377;)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="mono"><?= e($payment['invoice_no']) ?></td>
            <td class="num"><?= number_format($payment['invoice_total'] ?? 0, 2) ?></td>
            <td class="num"><?= number_format($payment['invoice_paid'] ?? 0, 2) ?></td>
            <td class="num"><?= number_format(max(0, ($payment['invoice_total'] ?? 0) - ($payment['invoice_paid'] ?? 0)), 2) ?></td>
        </tr>
    </tbody>
</table>
<?php endif; ?>

<div class="signatures">
    <div class="sig" style="flex:2.5"></div>
    <div class="sig"><div class="line"></div>For <?= e($company['company_name'] ?? COMPANY_NAME) ?><br><span class="role">Authorised Signatory</span></div>
</div>
