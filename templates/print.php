<?php
/**
 * FabX ERP - Shared Print / PDF Document Layout
 *
 * Standalone A4 layout used by every printable document (invoice, quotation,
 * purchase order, receipt, NCR report ...). The calling controller provides:
 *   $doc_title - browser tab / file title
 *   $company   - company profile array (settings with config fallbacks)
 *   $content   - the document body rendered by the module print view
 *
 * Self-contained on purpose: no app chrome, no CDN dependencies, so the
 * printed output is identical everywhere and "Save as PDF" just works.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= e($doc_title) ?></title>
<style>
    @page { size: A4; margin: 14mm 12mm; }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html { font-size: 13px; }
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #1e293b;
        background: #64748b;
        line-height: 1.5;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* ---- Screen preview chrome (hidden in print) ---- */
    .print-toolbar {
        position: sticky; top: 0; z-index: 10;
        display: flex; justify-content: space-between; align-items: center;
        background: #1e293b; color: #f1f5f9;
        padding: 10px 24px;
    }
    .print-toolbar .hint { font-size: 0.85rem; color: #94a3b8; }
    .print-toolbar .btns { display: flex; gap: 8px; }
    .print-toolbar button {
        font: inherit; font-size: 0.85rem; font-weight: 600;
        padding: 7px 18px; border-radius: 6px; cursor: pointer; border: none;
    }
    .btn-print { background: #e67e22; color: #fff; }
    .btn-print:hover { background: #d35400; }
    .btn-close-win { background: transparent; color: #cbd5e1; border: 1px solid #475569 !important; }
    .btn-close-win:hover { background: #334155; }

    .sheet {
        width: 210mm; min-height: 295mm; margin: 24px auto;
        background: #fff; padding: 14mm 12mm;
        box-shadow: 0 4px 24px rgba(0,0,0,0.35);
        display: flex; flex-direction: column;
    }

    /* ---- Letterhead ---- */
    .letterhead {
        display: flex; justify-content: space-between; align-items: flex-end;
        padding-bottom: 12px;
        border-bottom: 3px solid #2c3e50;
        position: relative;
    }
    .letterhead::after {
        content: ''; position: absolute; left: 0; bottom: -6px;
        width: 90px; height: 3px; background: #e67e22;
    }
    .lh-name { font-size: 1.7rem; font-weight: 800; color: #2c3e50; letter-spacing: 0.3px; }
    .lh-tagline { font-size: 0.8rem; color: #e67e22; font-weight: 600; letter-spacing: 1.2px; text-transform: uppercase; margin-top: 1px; }
    .lh-contact { text-align: right; font-size: 0.78rem; color: #475569; line-height: 1.55; max-width: 70mm; }
    .lh-contact strong { color: #1e293b; }

    /* ---- Document title band ---- */
    .doc-band {
        display: flex; justify-content: space-between; align-items: center;
        margin: 16px 0 14px;
    }
    .doc-band h1 {
        font-size: 1.25rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 2.5px; color: #2c3e50;
    }
    .doc-band .doc-copy { font-size: 0.72rem; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; }

    /* ---- Meta table (doc no / date blocks) ---- */
    table.meta { border-collapse: collapse; font-size: 0.82rem; }
    table.meta td { padding: 1.5px 0; vertical-align: top; }
    table.meta td.k { color: #64748b; padding-right: 14px; white-space: nowrap; }
    table.meta td.v { color: #1e293b; font-weight: 600; }
    .mono { font-family: 'SF Mono', Consolas, Menlo, monospace; letter-spacing: 0.3px; }

    /* ---- Party / info blocks ---- */
    .blocks { display: flex; gap: 10px; margin-bottom: 14px; }
    .block {
        flex: 1; background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 6px; padding: 10px 12px; font-size: 0.82rem;
    }
    .block h4 {
        font-size: 0.68rem; text-transform: uppercase; letter-spacing: 1px;
        color: #94a3b8; margin-bottom: 5px; font-weight: 700;
    }
    .block .party-name { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
    .block p { color: #475569; white-space: pre-line; }

    /* ---- Items table ---- */
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 0.82rem; }
    table.items thead th {
        background: #2c3e50; color: #fff;
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
        padding: 7px 8px; text-align: left;
    }
    table.items td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; color: #1e293b; }
    table.items tbody tr:nth-child(even) td { background: #f8fafc; }
    table.items .num { text-align: right; font-family: 'SF Mono', Consolas, Menlo, monospace; white-space: nowrap; }
    table.items th.num { text-align: right; }
    table.items .spec { color: #64748b; font-size: 0.75rem; }

    /* ---- Totals ---- */
    .totals-row { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 14px; }
    .totals-left { flex: 1.2; }
    .totals-box { flex: 1; }
    table.totals { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
    table.totals td { padding: 4px 10px; }
    table.totals td.k { color: #64748b; }
    table.totals td.v { text-align: right; font-family: 'SF Mono', Consolas, Menlo, monospace; color: #1e293b; }
    table.totals tr.grand td {
        border-top: 2px solid #2c3e50; padding-top: 7px;
        font-size: 1rem; font-weight: 800; color: #2c3e50;
    }
    .in-words {
        margin-top: 6px; padding: 7px 10px;
        background: #f8fafc; border-left: 3px solid #e67e22; border-radius: 0 4px 4px 0;
        font-size: 0.78rem; color: #475569;
    }
    .in-words strong { color: #1e293b; }

    /* ---- Generic field grid (reports) ---- */
    table.fields { width: 100%; border-collapse: collapse; font-size: 0.84rem; margin-bottom: 14px; }
    table.fields th, table.fields td { border: 1px solid #e2e8f0; padding: 6px 10px; text-align: left; vertical-align: top; }
    table.fields th { background: #f8fafc; color: #64748b; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.6px; width: 32mm; font-weight: 700; }
    .section-title {
        font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px;
        color: #2c3e50; border-bottom: 1px solid #e2e8f0;
        padding-bottom: 4px; margin: 14px 0 8px;
    }
    .longtext { white-space: pre-line; color: #334155; font-size: 0.84rem; }
    .pill {
        display: inline-block; padding: 2px 10px; border-radius: 20px;
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
        background: #eef2f7; color: #2c3e50; border: 1px solid #cbd5e1;
    }
    .pill.green { background: #e8f7ef; color: #1d7a46; border-color: #b7e4c9; }
    .pill.red { background: #fdecea; color: #b3261e; border-color: #f5c2bd; }
    .pill.amber { background: #fef5e7; color: #9a6700; border-color: #f3ddb0; }

    /* ---- Signatures ---- */
    .signatures {
        display: flex; gap: 10px; margin-top: auto; padding-top: 26px;
    }
    .sig { flex: 1; text-align: center; font-size: 0.78rem; color: #475569; }
    .sig .line { border-top: 1px solid #94a3b8; margin: 34px 14px 5px; }
    .sig .role { font-weight: 700; color: #1e293b; }

    /* ---- Footer ---- */
    .doc-footer {
        margin-top: 14px; padding-top: 8px; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: space-between;
        font-size: 0.68rem; color: #94a3b8;
    }

    @media print {
        body { background: #fff; }
        .print-toolbar { display: none; }
        .sheet { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
    }
</style>
</head>
<body>
    <div class="print-toolbar">
        <span class="hint"><?= e($doc_title) ?> &mdash; use Print / Save as PDF for the final document</span>
        <div class="btns">
            <button class="btn-close-win" onclick="window.close()">Close</button>
            <button class="btn-print" onclick="window.print()">&#128424; Print / Save PDF</button>
        </div>
    </div>

    <div class="sheet">
        <div class="letterhead">
            <div>
                <div class="lh-name"><?= e($company['company_name'] ?? COMPANY_NAME) ?></div>
                <div class="lh-tagline"><?= e($company['company_tagline'] ?? COMPANY_TAGLINE) ?></div>
            </div>
            <div class="lh-contact">
                <?= nl2br(e($company['company_address'] ?? '')) ?><br>
                <?= e($company['company_phone'] ?? '') ?> &middot; <?= e($company['company_email'] ?? '') ?><br>
                <strong>GSTIN:</strong> <span class="mono"><?= e($company['company_gstin'] ?? '-') ?></span>
                <?php if (!empty($company['company_pan'])): ?>
                    &middot; <strong>PAN:</strong> <span class="mono"><?= e($company['company_pan']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?= $content ?>

        <div class="doc-footer">
            <span>This is a computer-generated document from <?= e($company['company_name'] ?? COMPANY_NAME) ?> ERP.</span>
            <span>Generated on <?= date('d-m-Y H:i') ?></span>
        </div>
    </div>
</body>
</html>
