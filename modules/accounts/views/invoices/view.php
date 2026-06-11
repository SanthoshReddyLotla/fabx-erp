<?php
/**
 * FabX ERP - View / Print Tax Invoice Details
 */

if (!function_exists('number_to_words')) {
    function number_to_words($number) {
        $no = (int)floor($number);
        $point = (int)round(($number - $no) * 100);
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
        );
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $num_chunk = $no % $divider;
            $no = (int)($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($num_chunk) {
                $counter = count($str);
                $hundred = ($counter == 1 && isset($str[0]) && $str[0]) ? ' and ' : null;
                $str [] = ($num_chunk < 21) 
                    ? $words[$num_chunk] . ' ' . $digits[$counter] . ' ' . $hundred 
                    : $words[(int)floor($num_chunk / 10) * 10] . ' ' . $words[$num_chunk % 10] . ' ' . $digits[$counter] . ' ' . $hundred;
            } else {
                $str[] = null;
            }
        }
        $Rupees = implode('', array_reverse(array_filter($str)));
        $paise = ($point > 0) ? " and " . (isset($words[(int)floor($point / 10) * 10]) ? $words[(int)floor($point / 10) * 10] : '') . " " . $words[$point % 10] . ' paise' : '';
        return trim($Rupees) . trim($paise);
    }
}
?>

<div class="page-header d-print-none">
    <h1 class="page-title"><i class="bi bi-receipt text-primary"></i> Tax Invoice Details</h1>
    <div class="page-actions d-flex gap-2">
        <a href="<?= base_url('accounts/invoices/print/' . $invoice['id']) ?>" target="_blank" class="btn btn-fx btn-fx-primary"><i class="bi bi-printer"></i> Print / Save PDF</a>
        <a href="<?= base_url('accounts/invoices') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to List</a>
    </div>
</div>

<!-- Printable Tax Invoice Wrapper -->
<div class="fx-card p-5 border border-secondary border-opacity-10 invoice-print-wrapper bg-dark text-white">
    <!-- Invoice Header Row -->
    <div class="row align-items-start border-bottom border-secondary border-opacity-25 pb-4 mb-4">
        <div class="col-sm-6">
            <!-- Company details -->
            <h3 class="fw-bold text-light-heading mb-1"><i class="bi bi-hexagon-fill text-primary me-2"></i><?= e($company['company_name'] ?? 'FabX Engineering') ?></h3>
            <p class="text-muted small mb-2" style="white-space: pre-line; line-height: 1.5;"><?= e($company['company_address'] ?? 'Industrial Estate, Manufacturing Zone') ?></p>
            <div class="small text-muted">
                <span><strong>Phone:</strong> <?= e($company['company_phone'] ?? '-') ?></span> &bull; 
                <span><strong>Email:</strong> <?= e($company['company_email'] ?? '-') ?></span>
            </div>
            <div class="small text-muted mt-1">
                <span><strong>GSTIN:</strong> <span class="font-monospace text-uppercase"><?= e($company['company_gstin'] ?? '-') ?></span></span> &bull; 
                <span><strong>PAN:</strong> <span class="font-monospace text-uppercase"><?= e($company['company_pan'] ?? '-') ?></span></span>
            </div>
        </div>
        <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
            <h1 class="h2 fw-bold text-uppercase text-primary mb-1">Tax Invoice</h1>
            <div class="small text-muted mb-3">Original for Recipient</div>
            
            <table class="table table-sm table-borderless text-white small d-inline-block w-auto text-start">
                <tr><td class="text-muted pe-3 ps-0">Invoice No:</td><td><strong class="text-light-heading font-monospace"><?= e($invoice['invoice_no']) ?></strong></td></tr>
                <tr><td class="text-muted pe-3 ps-0">Invoice Date:</td><td><?= format_date($invoice['invoice_date']) ?></td></tr>
                <tr><td class="text-muted pe-3 ps-0">Due Date:</td><td><?= format_date($invoice['due_date']) ?></td></tr>
                <tr><td class="text-muted pe-3 ps-0">PO Reference:</td><td class="font-monospace text-uppercase"><?= e($invoice['po_reference'] ?: '-') ?></td></tr>
                <?php if (!empty($invoice['project_name'])): ?>
                    <tr><td class="text-muted pe-3 ps-0">Project:</td><td><strong class="text-light-heading"><?= e($invoice['project_name']) ?></strong> <span class="text-muted small font-monospace">(<?= e($invoice['project_code']) ?>)</span></td></tr>
                <?php endif; ?>
                <tr><td class="text-muted pe-3 ps-0">Status:</td><td>
                    <?php 
                    $sc = match($invoice['status'] ?? '') {
                        'paid' => 'bg-success text-white',
                        'partial' => 'bg-warning text-dark',
                        'sent' => 'bg-info text-dark',
                        'overdue' => 'bg-danger text-white',
                        default => 'bg-secondary text-light'
                    };
                    ?>
                    <span class="badge <?= $sc ?> text-uppercase" style="font-size:0.6rem; letter-spacing: 0.5px;"><?= e($invoice['status'] ?? 'draft') ?></span>
                </td></tr>
            </table>
        </div>
    </div>

    <!-- Client / Buyer Coordinates -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6">
            <div class="card bg-dark border-secondary border-opacity-10 p-3">
                <h6 class="text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px;"><i class="bi bi-person-fill text-primary me-1"></i> Billed To (Buyer)</h6>
                <h5 class="text-light-heading fw-bold mb-1"><?= e($invoice['client_name']) ?></h5>
                <p class="text-muted small mb-2" style="white-space: pre-line; line-height: 1.5;"><?= e($invoice['billing_address']) ?></p>
                <?php if ($invoice['client_gstin']): ?>
                    <div class="small">
                        <strong class="text-muted">GSTIN:</strong> 
                        <span class="font-monospace text-uppercase text-light-heading"><?= e($invoice['client_gstin']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card bg-dark border-secondary border-opacity-10 p-3 h-100">
                <h6 class="text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px;"><i class="bi bi-truck-flatbed text-primary me-1"></i> Shipped / Place of Supply</h6>
                <p class="text-muted small mb-2" style="white-space: pre-line; line-height: 1.5;"><?= e($invoice['shipping_address'] ?: $invoice['billing_address']) ?></p>
                <?php if ($invoice['supply_place']): ?>
                    <div class="small">
                        <strong class="text-muted">Place of Supply:</strong> 
                        <span class="text-light-heading text-capitalize"><?= e($invoice['supply_place']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Line items Table Grid -->
    <div class="table-responsive-fx mb-4">
        <table class="fx-table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">Sr No</th>
                    <th>Product / Service Description</th>
                    <th style="width: 120px;">HSN / SAC</th>
                    <th style="width: 100px;" class="text-end">Qty</th>
                    <th style="width: 90px;">UOM</th>
                    <th style="width: 140px;" class="text-end">Unit Rate (₹)</th>
                    <th style="width: 160px;" class="text-end">Line Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= (int)$item['sr_no'] ?></td>
                            <td>
                                <div class="fw-semibold text-light-heading"><?= e($item['description']) ?></div>
                            </td>
                            <td><span class="font-monospace small"><?= e($item['hsn_code'] ?: '-') ?></span></td>
                            <td class="text-end font-monospace"><?= number_format($item['quantity'] ?? 0, 3) ?></td>
                            <td><?= e($item['uom'] ?? 'Nos') ?></td>
                            <td class="text-end font-monospace"><?= number_format($item['unit_rate'] ?? 0, 2) ?></td>
                            <td class="text-end fw-bold font-monospace text-light-heading"><?= number_format($item['amount'] ?? 0, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No line items registered on this invoice sheet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Calculations and Split Summary Row -->
    <div class="row align-items-start g-4">
        <div class="col-md-6 order-2 order-md-1">
            <!-- Bank & Terms Panel -->
            <div class="card bg-dark border-secondary border-opacity-10 p-3 mb-3">
                <h6 class="text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px;"><i class="bi bi-bank text-primary me-1"></i> Bank Account Information</h6>
                <table class="table table-sm table-borderless text-white small mb-0">
                    <tr><td class="text-muted ps-0 pe-2" style="width: 110px;">Bank Name:</td><td><strong><?= e($company['company_bank'] ?? '-') ?></strong></td></tr>
                    <tr><td class="text-muted ps-0 pe-2">Account No:</td><td class="font-monospace text-light-heading"><strong><?= e($company['company_account_no'] ?? '-') ?></strong></td></tr>
                    <tr><td class="text-muted ps-0 pe-2">IFSC Code:</td><td class="font-monospace text-light-heading"><strong><?= e($company['company_ifsc'] ?? '-') ?></strong></td></tr>
                </table>
            </div>

            <div class="card bg-dark border-secondary border-opacity-10 p-3">
                <h6 class="text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px;"><i class="bi bi-file-earmark-check text-primary me-1"></i> Declarations & Contract Terms</h6>
                <div class="small text-muted" style="line-height: 1.6; white-space: pre-line;">
                    <?= e($invoice['terms_conditions'] ?: "1. Disputes subject to local jurisdiction.\n2. Interest @ 18% p.a. charged after due date.\n3. Goods once sold will not be taken back.") ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 order-1 order-md-2">
            <!-- Summary calculations grid -->
            <div class="card bg-dark border-secondary border-opacity-10 p-4">
                <table class="table table-sm table-borderless text-white small mb-0">
                    <tr>
                        <td class="text-muted ps-0">Items Subtotal:</td>
                        <td class="text-end font-monospace"><?= format_currency($invoice['subtotal'] ?? 0) ?></td>
                    </tr>
                    
                    <?php if ((float)$invoice['discount_amount'] > 0): ?>
                        <tr>
                            <td class="text-muted ps-0">Trade Discount (-):</td>
                            <td class="text-end text-danger font-monospace"><?= format_currency($invoice['discount_amount']) ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr class="border-bottom border-secondary border-opacity-10 pb-2">
                        <td class="text-muted ps-0">Taxable amount:</td>
                        <td class="text-end fw-bold font-monospace"><?= format_currency($invoice['taxable_amount'] ?? 0) ?></td>
                    </tr>

                    <!-- GST splits breakdown -->
                    <?php if ((float)($invoice['igst_amount'] ?? 0) > 0): ?>
                        <tr>
                            <td class="text-muted ps-0">Integrated GST (IGST @ <?= number_format($invoice['igst_rate'], 1) ?>%):</td>
                            <td class="text-end font-monospace text-light-heading"><?= format_currency($invoice['igst_amount']) ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td class="text-muted ps-0">Central GST (CGST @ <?= number_format($invoice['cgst_rate'], 1) ?>%):</td>
                            <td class="text-end font-monospace text-light-heading"><?= format_currency($invoice['cgst_amount']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">State GST (SGST @ <?= number_format($invoice['sgst_rate'], 1) ?>%):</td>
                            <td class="text-end font-monospace text-light-heading"><?= format_currency($invoice['sgst_amount']) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ((float)($invoice['round_off'] ?? 0) != 0): ?>
                        <tr>
                            <td class="text-muted ps-0">Adjustments / Round Off:</td>
                            <td class="text-end font-monospace"><?= format_currency($invoice['round_off']) ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr class="border-top border-secondary border-opacity-25 pt-2" style="border-top: 1.5px solid var(--border-color) !important;">
                        <td class="h5 mb-0 text-primary fw-bold ps-0 py-2">Grand Total:</td>
                        <td class="h5 mb-0 text-success text-end fw-bold font-monospace py-2"><?= format_currency($invoice['grand_total'] ?? 0) ?></td>
                    </tr>
                    
                    <tr>
                        <td class="text-muted ps-0 py-1">Amount In Words:</td>
                        <td class="text-end text-muted small py-1"><?= e($invoice['amount_in_words'] ?: "Rupees " . ucwords(str_replace('-', ' ', number_to_words((float)($invoice['grand_total'] ?? 0)))) . " Only") ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background: #fff !important;
        color: #000 !important;
    }
    .invoice-print-wrapper {
        background: #fff !important;
        color: #000 !important;
        border: none !important;
        padding: 0 !important;
    }
    .invoice-print-wrapper table, 
    .invoice-print-wrapper tr, 
    .invoice-print-wrapper td,
    .invoice-print-wrapper th {
        color: #000 !important;
        border-color: #ddd !important;
    }
    .text-muted {
        color: #555 !important;
    }
    .text-light-heading {
        color: #000 !important;
    }
    .text-success {
        color: #198754 !important;
    }
    .card {
        background: #fff !important;
        border: 1px solid #ddd !important;
        color: #000 !important;
    }
}
</style>
