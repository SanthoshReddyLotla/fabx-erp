<?php
/**
 * FabX ERP - Tax Invoice On-Screen View
 * Rendered as a paper-style document preview: always white with dark ink,
 * independent of the app theme, mirroring the print/PDF template.
 */
?>

<div class="page-header d-print-none">
    <h1 class="page-title"><i class="bi bi-receipt text-primary"></i> Tax Invoice Details</h1>
    <div class="page-actions d-flex gap-2">
        <a href="<?= base_url('accounts/invoices/print/' . $invoice['id']) ?>" target="_blank" class="btn btn-fx btn-fx-primary"><i class="bi bi-printer"></i> Print / Save PDF</a>
        <?php if (($invoice['status'] ?? '') === 'draft'): ?>
            <a href="<?= base_url('accounts/invoices/edit/' . $invoice['id']) ?>" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
        <?php endif; ?>
        <a href="<?= base_url('accounts/invoices') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to List</a>
    </div>
</div>

<div class="inv-paper shadow">
    <!-- Letterhead -->
    <div class="inv-letterhead">
        <div>
            <div class="inv-co-name"><?= e($company['company_name'] ?? 'FabX Engineering') ?></div>
            <?php if (!empty($company['company_tagline'])): ?>
                <div class="inv-co-tagline"><?= e($company['company_tagline']) ?></div>
            <?php endif; ?>
        </div>
        <div class="inv-co-contact">
            <?= nl2br(e($company['company_address'] ?? '')) ?><br>
            <?= e($company['company_phone'] ?? '') ?> &middot; <?= e($company['company_email'] ?? '') ?><br>
            <strong>GSTIN:</strong> <span class="inv-mono"><?= e($company['company_gstin'] ?? '-') ?></span>
            <?php if (!empty($company['company_pan'])): ?>
                &middot; <strong>PAN:</strong> <span class="inv-mono"><?= e($company['company_pan']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Title band -->
    <div class="inv-band">
        <h2>Tax Invoice</h2>
        <?php
        $sc = match ($invoice['status'] ?? '') {
            'paid' => 'inv-pill-green', 'partial' => 'inv-pill-amber',
            'overdue' => 'inv-pill-red', 'cancelled' => 'inv-pill-red',
            default => ''
        };
        ?>
        <span class="inv-pill <?= $sc ?>"><?= strtoupper(e($invoice['status'] ?? 'draft')) ?></span>
    </div>

    <!-- Party + meta blocks -->
    <div class="inv-blocks">
        <div class="inv-block">
            <h4>Billed To</h4>
            <div class="inv-party"><?= e($invoice['client_name']) ?></div>
            <p><?= e($invoice['billing_address'] ?: ($invoice['client_address'] ?? '')) ?></p>
            <?php if (!empty($invoice['client_gstin'])): ?>
                <p><strong>GSTIN:</strong> <span class="inv-mono"><?= e($invoice['client_gstin']) ?></span></p>
            <?php endif; ?>
        </div>
        <div class="inv-block">
            <h4>Ship To / Place of Supply</h4>
            <p><?= e($invoice['shipping_address'] ?: $invoice['billing_address'] ?: '-') ?></p>
            <?php if (!empty($invoice['supply_place'])): ?>
                <p><strong>Place of Supply:</strong> <?= e($invoice['supply_place']) ?></p>
            <?php endif; ?>
        </div>
        <div class="inv-block">
            <h4>Invoice Details</h4>
            <table class="inv-meta">
                <tr><td>Invoice No</td><td class="inv-mono"><?= e($invoice['invoice_no']) ?></td></tr>
                <tr><td>Invoice Date</td><td><?= format_date($invoice['invoice_date']) ?></td></tr>
                <tr><td>Due Date</td><td><?= format_date($invoice['due_date']) ?></td></tr>
                <?php if (!empty($invoice['po_reference'])): ?>
                    <tr><td>PO Reference</td><td class="inv-mono"><?= e($invoice['po_reference']) ?></td></tr>
                <?php endif; ?>
                <?php if (!empty($invoice['project_name'])): ?>
                    <tr><td>Project</td><td><?= e($invoice['project_name']) ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <!-- Line items -->
    <table class="inv-items">
        <thead>
            <tr>
                <th style="width:46px">#</th>
                <th>Description</th>
                <th style="width:110px">HSN / SAC</th>
                <th class="num" style="width:90px">Qty</th>
                <th style="width:70px">UOM</th>
                <th class="num" style="width:130px">Rate (&#8377;)</th>
                <th class="num" style="width:150px">Amount (&#8377;)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= (int)$item['sr_no'] ?></td>
                        <td><?= e($item['description']) ?></td>
                        <td class="inv-mono"><?= e($item['hsn_code'] ?: '-') ?></td>
                        <td class="num"><?= rtrim(rtrim(number_format($item['quantity'] ?? 0, 3), '0'), '.') ?></td>
                        <td><?= e($item['uom'] ?? 'Nos') ?></td>
                        <td class="num"><?= number_format($item['unit_rate'] ?? 0, 2) ?></td>
                        <td class="num"><strong><?= number_format($item['amount'] ?? 0, 2) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="inv-empty">No line items registered on this invoice.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Bank/terms + totals -->
    <div class="inv-bottom">
        <div class="inv-bottom-left">
            <div class="inv-block">
                <h4>Bank Details for Payment</h4>
                <table class="inv-meta">
                    <tr><td>Bank</td><td><?= e($company['company_bank'] ?? '-') ?></td></tr>
                    <tr><td>Account No</td><td class="inv-mono"><?= e($company['company_account_no'] ?? '-') ?></td></tr>
                    <tr><td>IFSC</td><td class="inv-mono"><?= e($company['company_ifsc'] ?? '-') ?></td></tr>
                </table>
            </div>
            <div class="inv-block">
                <h4>Terms &amp; Conditions</h4>
                <p class="inv-terms"><?= e($invoice['terms_conditions'] ?: "1. Payment due within the agreed credit period.\n2. Interest @ 18% p.a. applicable on overdue amounts.\n3. All disputes subject to local jurisdiction.") ?></p>
            </div>
        </div>
        <div class="inv-bottom-right">
            <table class="inv-totals">
                <tr><td>Subtotal</td><td class="num"><?= number_format($invoice['subtotal'] ?? 0, 2) ?></td></tr>
                <?php if ((float)$invoice['discount_amount'] > 0): ?>
                    <tr><td>Discount</td><td class="num">- <?= number_format($invoice['discount_amount'], 2) ?></td></tr>
                <?php endif; ?>
                <tr><td>Taxable Value</td><td class="num"><?= number_format($invoice['taxable_amount'] ?? 0, 2) ?></td></tr>
                <?php if ((float)($invoice['igst_amount'] ?? 0) > 0): ?>
                    <tr><td>IGST @ <?= number_format($invoice['igst_rate'], 1) ?>%</td><td class="num"><?= number_format($invoice['igst_amount'], 2) ?></td></tr>
                <?php else: ?>
                    <tr><td>CGST @ <?= number_format($invoice['cgst_rate'], 1) ?>%</td><td class="num"><?= number_format($invoice['cgst_amount'], 2) ?></td></tr>
                    <tr><td>SGST @ <?= number_format($invoice['sgst_rate'], 1) ?>%</td><td class="num"><?= number_format($invoice['sgst_amount'], 2) ?></td></tr>
                <?php endif; ?>
                <?php if ((float)($invoice['round_off'] ?? 0) != 0): ?>
                    <tr><td>Round Off</td><td class="num"><?= number_format($invoice['round_off'], 2) ?></td></tr>
                <?php endif; ?>
                <tr class="grand"><td>Grand Total</td><td class="num">&#8377; <?= number_format($invoice['grand_total'] ?? 0, 2) ?></td></tr>
                <?php if ((float)($invoice['paid_amount'] ?? 0) > 0): ?>
                    <tr><td>Paid</td><td class="num">- <?= number_format($invoice['paid_amount'], 2) ?></td></tr>
                    <tr><td><strong>Balance Due</strong></td><td class="num"><strong>&#8377; <?= number_format(max(0, ($invoice['grand_total'] ?? 0) - ($invoice['paid_amount'] ?? 0)), 2) ?></strong></td></tr>
                <?php endif; ?>
            </table>
            <div class="inv-words">
                <strong>Amount in words:</strong><br>
                <?= e($invoice['amount_in_words'] ?: amount_in_words((float)($invoice['grand_total'] ?? 0))) ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Paper-style invoice preview: fixed light colors on purpose so the
   document reads like paper in BOTH app themes. */
.inv-paper {
    max-width: 980px; margin: 0 auto;
    background: #ffffff; color: #1e293b;
    border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 2.2rem 2.4rem;
    font-size: 0.875rem;
}
.inv-letterhead {
    display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem;
    padding-bottom: 12px; border-bottom: 3px solid #2c3e50; position: relative;
}
.inv-letterhead::after {
    content: ''; position: absolute; left: 0; bottom: -6px;
    width: 90px; height: 3px; background: #e67e22;
}
.inv-co-name { font-size: 1.5rem; font-weight: 800; color: #2c3e50; }
.inv-co-tagline { font-size: 0.72rem; color: #e67e22; font-weight: 600; letter-spacing: 1.2px; text-transform: uppercase; }
.inv-co-contact { text-align: right; font-size: 0.78rem; color: #475569; line-height: 1.55; }
.inv-co-contact strong { color: #1e293b; }
.inv-mono { font-family: 'SF Mono', Consolas, Menlo, monospace; letter-spacing: 0.3px; }

.inv-band { display: flex; justify-content: space-between; align-items: center; margin: 16px 0 14px; }
.inv-band h2 { font-size: 1.15rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: #2c3e50; margin: 0; }
.inv-pill {
    padding: 3px 12px; border-radius: 20px; font-size: 0.68rem; font-weight: 700;
    letter-spacing: 0.6px; background: #eef2f7; color: #2c3e50; border: 1px solid #cbd5e1;
}
.inv-pill-green { background: #e8f7ef; color: #1d7a46; border-color: #b7e4c9; }
.inv-pill-red { background: #fdecea; color: #b3261e; border-color: #f5c2bd; }
.inv-pill-amber { background: #fef5e7; color: #9a6700; border-color: #f3ddb0; }

.inv-blocks { display: flex; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }
.inv-block { flex: 1; min-width: 220px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; font-size: 0.82rem; margin-bottom: 10px; }
.inv-block h4 { font-size: 0.66rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin: 0 0 5px; font-weight: 700; }
.inv-block p { color: #475569; white-space: pre-line; margin: 0 0 2px; }
.inv-party { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
.inv-meta { border-collapse: collapse; font-size: 0.8rem; width: 100%; }
.inv-meta td { padding: 1.5px 0; vertical-align: top; color: #1e293b; font-weight: 600; }
.inv-meta td:first-child { color: #64748b; font-weight: 400; padding-right: 12px; white-space: nowrap; width: 90px; }

.inv-items { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 0.82rem; }
.inv-items thead th {
    background: #2c3e50; color: #fff; font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.6px; padding: 8px; text-align: left;
}
.inv-items td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; color: #1e293b; vertical-align: top; }
.inv-items tbody tr:nth-child(even) td { background: #f8fafc; }
.inv-items .num, .inv-items th.num { text-align: right; font-family: 'SF Mono', Consolas, Menlo, monospace; white-space: nowrap; }
.inv-empty { text-align: center; color: #94a3b8; padding: 16px !important; }

.inv-bottom { display: flex; gap: 14px; flex-wrap: wrap; }
.inv-bottom-left { flex: 1.2; min-width: 260px; }
.inv-bottom-right { flex: 1; min-width: 260px; }
.inv-terms { font-size: 0.76rem; line-height: 1.6; }
.inv-totals { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
.inv-totals td { padding: 4px 10px; color: #64748b; }
.inv-totals td.num { text-align: right; font-family: 'SF Mono', Consolas, Menlo, monospace; color: #1e293b; }
.inv-totals tr.grand td { border-top: 2px solid #2c3e50; padding-top: 7px; font-size: 1rem; font-weight: 800; color: #2c3e50; }
.inv-words {
    margin-top: 8px; padding: 7px 10px; background: #f8fafc;
    border-left: 3px solid #e67e22; border-radius: 0 4px 4px 0;
    font-size: 0.78rem; color: #475569;
}
.inv-words strong { color: #1e293b; }

@media print {
    .inv-paper { border: none; box-shadow: none !important; padding: 0; max-width: none; }
}
</style>
