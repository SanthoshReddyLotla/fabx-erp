<?php
/**
 * FabX ERP - Monthly GST Summary (filing helper)
 */
?>

<div class="page-header d-print-none">
    <h1 class="page-title"><i class="bi bi-percent text-primary"></i> GST Summary &mdash; <?= e($period_label) ?></h1>
    <div class="page-actions d-flex gap-2">
        <form method="GET" action="<?= base_url('accounts/gst') ?>" class="d-flex align-items-center gap-2 mb-0">
            <input type="month" class="form-control bg-dark border-secondary text-white" name="month" value="<?= e($month) ?>" max="<?= date('Y-m') ?>" onchange="this.form.submit()">
        </form>
        <a href="<?= base_url('accounts/gst') ?>?month=<?= e($month) ?>&amp;export=csv" class="btn btn-outline-secondary"><i class="bi bi-filetype-csv"></i> Export Sales</a>
        <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Print</button>
    </div>
</div>

<!-- Net position cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="fx-card p-3 text-center h-100">
            <div class="text-muted small mb-1"><i class="bi bi-arrow-up-circle text-danger"></i> Output GST (Sales)</div>
            <h3 class="fw-bold text-danger mb-0"><?= format_currency($output_gst) ?></h3>
            <div class="text-muted small mt-1"><?= (int)$out['cnt'] ?> tax invoice(s)</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="fx-card p-3 text-center h-100">
            <div class="text-muted small mb-1"><i class="bi bi-arrow-down-circle text-success"></i> Input GST / ITC (Expenses)</div>
            <h3 class="fw-bold text-success mb-0"><?= format_currency($input_gst) ?></h3>
            <div class="text-muted small mt-1"><?= (int)$in['cnt'] ?> expense(s)</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="fx-card p-3 text-center h-100 border border-primary border-opacity-25">
            <div class="text-muted small mb-1"><i class="bi bi-cash-coin text-primary"></i> Net GST <?= $net_payable >= 0 ? 'Payable' : 'Credit (carry forward)' ?></div>
            <h3 class="fw-bold <?= $net_payable >= 0 ? 'text-primary' : 'text-success' ?> mb-0"><?= format_currency(abs($net_payable)) ?></h3>
            <div class="text-muted small mt-1">Output &minus; Input</div>
        </div>
    </div>
</div>

<!-- Output tax split -->
<div class="fx-card mb-4">
    <div class="fx-card-header"><h5 class="fx-card-title mb-0"><i class="bi bi-diagram-3"></i> Output Tax Breakup (to pay)</h5></div>
    <div class="fx-card-body">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3">
                <div class="text-muted small">Taxable Value</div>
                <div class="h5 fw-bold mb-0"><?= format_currency($out['taxable']) ?></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small">CGST</div>
                <div class="h5 fw-bold mb-0"><?= format_currency($out['cgst']) ?></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small">SGST</div>
                <div class="h5 fw-bold mb-0"><?= format_currency($out['sgst']) ?></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small">IGST</div>
                <div class="h5 fw-bold mb-0"><?= format_currency($out['igst']) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Sales register -->
<div class="fx-card mb-4">
    <div class="fx-card-header"><h5 class="fx-card-title mb-0"><i class="bi bi-receipt"></i> Sales Register (GSTR-1 data)</h5></div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice No</th><th>Date</th><th>Client</th><th>GSTIN</th>
                        <th class="text-end">Taxable</th><th class="text-end">CGST</th>
                        <th class="text-end">SGST</th><th class="text-end">IGST</th><th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php foreach ($sales as $s): ?>
                            <tr>
                                <td class="fw-semibold text-info"><?= e($s['invoice_no']) ?></td>
                                <td class="small"><?= format_date($s['invoice_date']) ?></td>
                                <td><?= e($s['client_name'] ?? '-') ?></td>
                                <td class="font-monospace small"><?= e($s['client_gstin'] ?: '-') ?></td>
                                <td class="text-end font-monospace"><?= number_format((float)$s['taxable_amount'], 2) ?></td>
                                <td class="text-end font-monospace"><?= number_format((float)$s['cgst_amount'], 2) ?></td>
                                <td class="text-end font-monospace"><?= number_format((float)$s['sgst_amount'], 2) ?></td>
                                <td class="text-end font-monospace"><?= number_format((float)$s['igst_amount'], 2) ?></td>
                                <td class="text-end font-monospace fw-bold"><?= number_format((float)$s['grand_total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="fw-bold" style="border-top:2px solid var(--border-color);">
                            <td colspan="4" class="text-end">Total</td>
                            <td class="text-end font-monospace"><?= number_format((float)$out['taxable'], 2) ?></td>
                            <td class="text-end font-monospace"><?= number_format((float)$out['cgst'], 2) ?></td>
                            <td class="text-end font-monospace"><?= number_format((float)$out['sgst'], 2) ?></td>
                            <td class="text-end font-monospace"><?= number_format((float)$out['igst'], 2) ?></td>
                            <td class="text-end font-monospace text-success"><?= number_format((float)$out['total'], 2) ?></td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="9"><div class="empty-state py-4 text-center text-muted"><i class="bi bi-receipt display-6 d-block mb-2 opacity-25"></i>No tax invoices issued in <?= e($period_label) ?>.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Input tax / ITC register -->
<div class="fx-card mb-4">
    <div class="fx-card-header"><h5 class="fx-card-title mb-0"><i class="bi bi-wallet2"></i> Input Tax Credit (Expenses with GST)</h5></div>
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Expense No</th><th>Date</th><th>Category</th><th>Vendor</th>
                        <th class="text-end">Base</th><th class="text-end">GST (ITC)</th><th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expenses)): ?>
                        <?php foreach ($expenses as $ex): ?>
                            <tr>
                                <td class="fw-semibold"><?= e($ex['expense_no']) ?></td>
                                <td class="small"><?= format_date($ex['expense_date']) ?></td>
                                <td class="text-capitalize small"><?= e(str_replace('_', ' ', $ex['category'] ?? '-')) ?></td>
                                <td><?= e($ex['vendor'] ?: '-') ?></td>
                                <td class="text-end font-monospace"><?= number_format((float)$ex['amount'], 2) ?></td>
                                <td class="text-end font-monospace text-success"><?= number_format((float)$ex['gst_amount'], 2) ?></td>
                                <td class="text-end font-monospace fw-bold"><?= number_format((float)$ex['total_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="fw-bold" style="border-top:2px solid var(--border-color);">
                            <td colspan="4" class="text-end">Total</td>
                            <td class="text-end font-monospace"><?= number_format((float)$in['base'], 2) ?></td>
                            <td class="text-end font-monospace text-success"><?= number_format((float)$in['gst'], 2) ?></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="7"><div class="empty-state py-4 text-center text-muted"><i class="bi bi-wallet2 display-6 d-block mb-2 opacity-25"></i>No GST expenses recorded in <?= e($period_label) ?>.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<p class="text-muted small d-print-none">
    <i class="bi bi-info-circle"></i> This summary is a filing aid. Output tax is from issued tax invoices (proformas, drafts and cancelled invoices are excluded). Input tax credit is taken from the GST recorded on expenses &mdash; please confirm eligibility (some expenses are blocked credits) with your accountant before filing GSTR-3B.
</p>
