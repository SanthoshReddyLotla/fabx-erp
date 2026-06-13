<?php
/**
 * FabX ERP - Accounts Controller
 * Invoicing, payments, expenses, ledgers
 */

namespace Modules\Accounts;

use Core\Controller;

class AccountsController extends Controller {
    protected string $module = 'accounts';

    public function __construct() {
        parent::__construct();
        require_auth();
    }

    /**
     * Compute invoice line/tax totals from the submitted form, honouring the
     * GST classification (intra/interstate) and the rates the user actually
     * saw on screen. Shared by create and edit so the stored figures can never
     * disagree with the on-screen summary the user confirmed.
     */
    private function computeInvoiceTotals(): array {
        $items = $_POST['items'] ?? [];
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += ((float)($item['quantity'] ?? 0)) * ((float)($item['unit_rate'] ?? 0));
        }
        $subtotal = round($subtotal, 2);

        $discount = max(0, (float)input('discount_amount', 0));
        $taxable = round(max(0, $subtotal - $discount), 2);

        $gstType = input('gst_type', 'intrastate');
        $cgstRate = $sgstRate = $igstRate = 0.0;
        $cgstAmt = $sgstAmt = $igstAmt = 0.0;

        if ($gstType === 'interstate') {
            $igstRate = max(0, (float)input('igst_rate', IGST_RATE));
            $igstAmt = round($taxable * $igstRate / 100, 2);
        } else {
            $cgstRate = max(0, (float)input('cgst_rate', CGST_RATE));
            $sgstRate = max(0, (float)input('sgst_rate', SGST_RATE));
            $cgstAmt = round($taxable * $cgstRate / 100, 2);
            $sgstAmt = round($taxable * $sgstRate / 100, 2);
        }

        $roundOff = (float)input('round_off', 0);
        $totalAmount = round($taxable + $cgstAmt + $sgstAmt + $igstAmt, 2);
        $grandTotal = round($totalAmount + $roundOff, 2);

        return compact(
            'items', 'subtotal', 'discount', 'taxable',
            'cgstRate', 'cgstAmt', 'sgstRate', 'sgstAmt', 'igstRate', 'igstAmt',
            'roundOff', 'totalAmount', 'grandTotal'
        );
    }

    // ==================== INVOICES ====================

    public function invoices(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        
        $where = "WHERE 1=1"; $params = [];
        if ($status) { $where .= " AND i.status = ?"; $params[] = $status; }
        
        $invoices = $this->db->fetchAll(
            "SELECT i.*, c.company_name as client_name, CONCAT(u.first_name, ' ', u.last_name) as created_name
             FROM " . $this->db->table("invoices") . " i
             LEFT JOIN " . $this->db->table("clients") . " c ON i.client_id = c.id
             LEFT JOIN " . $this->db->table("users") . " u ON i.created_by = u.id
             {$where} ORDER BY i.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("invoices") . " i {$where}", $params);
        
        $this->view('invoices/list', [
            'page_title' => 'Invoices', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Invoices',
            'invoices' => $invoices, 'pagination' => paginate($total, $page),
            'total_outstanding' => $this->db->fetchValue("SELECT SUM(grand_total - paid_amount) FROM " . $this->db->table("invoices") . " WHERE status IN ('sent','partial','overdue')") ?? 0
        ]);
    }

    public function createInvoice(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/accounts/invoices/create'); }
            
            $invNo = generate_invoice_no();
            $clientId = (int)input('client_id');

            // Totals honour the GST type / rates / round-off the user saw.
            $t = $this->computeInvoiceTotals();
            $items = $t['items'];

            // Validation: a real invoice needs a client and at least one priced line.
            if (!$clientId) {
                $this->flash('error', 'Please select a client for the invoice.');
                $this->redirect('/accounts/invoices/create');
            }
            if (empty($items) || $t['grandTotal'] <= 0) {
                $this->flash('error', 'Add at least one line item with a quantity and rate.');
                $this->redirect('/accounts/invoices/create');
            }

            $this->db->beginTransaction();
            try {
                $id = $this->db->insert(
                    "INSERT INTO " . $this->db->table("invoices") . "
                    (invoice_no, invoice_date, due_date, client_id, po_reference, billing_address,
                     subtotal, discount_amount, taxable_amount, cgst_rate, cgst_amount, sgst_rate, sgst_amount,
                     igst_rate, igst_amount, total_amount, round_off, grand_total, terms_conditions, bank_details, status, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?, NOW())",
                    [$invNo, input('invoice_date'), input('due_date'), $clientId, input('po_reference'),
                     input('billing_address'), $t['subtotal'], $t['discount'], $t['taxable'],
                     $t['cgstRate'], $t['cgstAmt'], $t['sgstRate'], $t['sgstAmt'], $t['igstRate'], $t['igstAmt'],
                     $t['totalAmount'], $t['roundOff'], $t['grandTotal'], input('terms_conditions'), input('bank_details'), current_user_id()]
                );

                if ($id && !empty($items)) {
                    foreach ($items as $index => $item) {
                        $qty = (float)($item['quantity'] ?? 0);
                        $rate = (float)($item['unit_rate'] ?? 0);
                        $itemAmt = $qty * $rate;
                        $this->db->execute(
                            "INSERT INTO " . $this->db->table("invoice_items") . " 
                            (invoice_id, sr_no, description, hsn_code, quantity, uom, unit_rate, amount)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                            [$id, $index + 1, $item['description'], $item['hsn_code'] ?? '',
                             $qty, $item['uom'] ?? 'Nos', $rate, $itemAmt]
                        );
                    }
                }
                $this->db->commit();
                $this->log('INVOICE_CREATED', "Invoice {$invNo} created");
                $this->flash('success', "Invoice {$invNo} created successfully.");
                $this->redirect('/accounts/invoices');
            } catch (\Exception $e) {
                $this->db->rollback();
                $this->flash('error', 'Database transaction failed: ' . $e->getMessage());
                $this->redirect('/accounts/invoices/create');
            }
        }
        
        $clients = $this->db->fetchAll("SELECT id, company_name, gstin, address FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        $this->view('invoices/create', [
            'page_title' => 'Create Invoice', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Create Invoice',
            'clients' => $clients, 'invoice_no' => generate_invoice_no()
        ]);
    }

    // ==================== PAYMENTS RECEIVED ====================

    public function payments(): void {
        $this->requireCan('read');
        
        if (is_post()) {
            $this->requireCan('create');
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/accounts/payments'); }
            
            $clientId = (int)input('client_id');
            $invoiceId = (int)input('invoice_id');
            $amount = (float)input('amount');
            $tdsAmount = (float)input('tds_amount', 0);
            $netAmount = $amount - $tdsAmount;
            $paymentMode = input('payment_mode');
            $receiptNo = input('receipt_no');
            $transactionRef = input('transaction_ref');
            $paymentDate = input('payment_date') ?: date('Y-m-d');

            // Validation
            if ($amount <= 0) {
                $this->flash('error', 'Payment amount must be greater than zero.');
                $this->redirect('/accounts/payments');
            }
            if ($tdsAmount < 0 || $tdsAmount > $amount) {
                $this->flash('error', 'TDS amount cannot be negative or exceed the payment amount.');
                $this->redirect('/accounts/payments');
            }

            // If an invoice is selected, trust the invoice's own client so the
            // payment can never be filed against the wrong party's ledger.
            $invoice = $invoiceId
                ? $this->db->fetchOne("SELECT id, client_id, grand_total, paid_amount, status FROM " . $this->db->table("invoices") . " WHERE id = ?", [$invoiceId])
                : null;
            if ($invoice) {
                $clientId = (int)$invoice['client_id'];
            }
            if (!$clientId) {
                $this->flash('error', 'Please select a client (or an invoice) for this payment.');
                $this->redirect('/accounts/payments');
            }

            if (!$receiptNo) {
                $receiptNo = 'REC-' . date('Ymd') . '-' . code_suffix(4);
            }

            $this->db->beginTransaction();
            try {
                // 1. Insert Payment
                $this->db->insert(
                    "INSERT INTO " . $this->db->table("payments") . "
                    (receipt_no, receipt_date, invoice_id, client_id, amount, tds_amount, net_amount, payment_mode, transaction_ref, transaction_date, received_by, remarks, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                    [$receiptNo, $paymentDate, $invoiceId ?: null, $clientId, $amount, $tdsAmount, $netAmount, $paymentMode, $transactionRef, $paymentDate, current_user_id(), input('remarks')]
                );

                // 2. Settle the invoice. The full gross amount (including any TDS
                // withheld by the client) discharges the receivable - TDS is
                // remitted to the tax authority on our behalf, so it still counts
                // as settled. Using net here would leave TDS invoices permanently
                // stuck at 'partial'.
                if ($invoice) {
                    $newPaidAmount = round((float)$invoice['paid_amount'] + $amount, 2);
                    $status = ($newPaidAmount >= (float)$invoice['grand_total'] - 0.01) ? 'paid' : 'partial';

                    $this->db->execute(
                        "UPDATE " . $this->db->table("invoices") . "
                         SET paid_amount = ?, status = ?, paid_date = ?
                         WHERE id = ?",
                        [$newPaidAmount, $status, $paymentDate, $invoiceId]
                    );
                }

                $this->db->commit();
                $this->log('PAYMENT_RECEIVED', "Payment receipt {$receiptNo} recorded" . ($invoiceId ? " for invoice ID {$invoiceId}" : ' (on account)'));
                $this->flash('success', "Payment of " . format_currency($amount) . " received successfully (receipt {$receiptNo}).");
            } catch (\Exception $e) {
                $this->db->rollback();
                $this->flash('error', 'Failed to process payment entry: ' . $e->getMessage());
            }
            $this->redirect('/accounts/payments');
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $payments = $this->db->fetchAll(
            "SELECT p.*, c.company_name as client_name, i.invoice_no
             FROM " . $this->db->table("payments") . " p
             LEFT JOIN " . $this->db->table("clients") . " c ON p.client_id = c.id
             LEFT JOIN " . $this->db->table("invoices") . " i ON p.invoice_id = i.id
             ORDER BY p.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("payments"));
        
        $clients = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        // Any non-cancelled invoice that still has a balance can take a payment.
        $invoices = $this->db->fetchAll(
            "SELECT id, invoice_no, grand_total, paid_amount, client_id FROM " . $this->db->table("invoices") . "
             WHERE status NOT IN ('paid','cancelled') ORDER BY invoice_no DESC"
        );

        $this->view('payments/list', [
            'page_title' => 'Payments', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Payments',
            'payments' => $payments, 'pagination' => paginate($total, $page),
            'clients' => $clients, 'invoices' => $invoices
        ]);
    }

    // ==================== EXPENSES ====================

    public function expenses(): void {
        $this->requireCan('read');
        
        if (is_post()) {
            $this->requireCan('create');
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/accounts/expenses'); }
            
            $amount = (float)input('amount');
            $gstAmount = (float)input('gst_amount', 0);
            $totalAmount = $amount + $gstAmount;

            if ($amount <= 0) {
                $this->flash('error', 'Expense amount must be greater than zero.');
                $this->redirect('/accounts/expenses');
            }

            $expNo = 'EXP-' . date('Ymd') . '-' . code_suffix(4);

            $this->db->insert(
                "INSERT INTO " . $this->db->table("expenses") . " 
                (expense_no, expense_date, category, description, amount, gst_amount, total_amount, vendor, project_id, payment_mode, reference_no, status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', ?, NOW())",
                [$expNo, input('expense_date'), input('category'), input('description'), $amount, $gstAmount, $totalAmount, input('vendor'), input('project_id') ?: null, input('payment_mode'), input('reference_no'), current_user_id()]
            );
            
            $this->log('EXPENSE_CREATED', "Expense logged {$expNo}");
            $this->flash('success', "Expense {$expNo} logged successfully.");
            $this->redirect('/accounts/expenses');
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $expenses = $this->db->fetchAll(
            "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) as created_name, p.project_name
             FROM " . $this->db->table("expenses") . " e
             LEFT JOIN " . $this->db->table("users") . " u ON e.created_by = u.id
             LEFT JOIN " . $this->db->table("projects") . " p ON e.project_id = p.id
             ORDER BY e.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("expenses"));
        $projects = $this->db->fetchAll("SELECT id, project_code, project_name FROM " . $this->db->table("projects") . " WHERE status = 'active'");
        
        $this->view('expenses/list', [
            'page_title' => 'Expenses', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Expenses',
            'expenses' => $expenses, 'pagination' => paginate($total, $page),
            'projects' => $projects
        ]);
    }

    // ==================== VENDOR PAYMENTS ====================

    public function vendorPayments(): void {
        $this->requireCan('read');
        
        if (is_post()) {
            $this->requireCan('create');
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/accounts/vendor-payments'); }
            
            $vendorId = (int)input('vendor_id');
            $amount = (float)input('amount');
            $tdsAmount = (float)input('tds_amount', 0);
            $netAmount = $amount - $tdsAmount;

            if (!$vendorId) {
                $this->flash('error', 'Please select a vendor for this payment.');
                $this->redirect('/accounts/vendor-payments');
            }
            if ($amount <= 0) {
                $this->flash('error', 'Payment amount must be greater than zero.');
                $this->redirect('/accounts/vendor-payments');
            }
            if ($tdsAmount < 0 || $tdsAmount > $amount) {
                $this->flash('error', 'TDS amount cannot be negative or exceed the payment amount.');
                $this->redirect('/accounts/vendor-payments');
            }

            $payNo = 'VP-' . date('Ymd') . '-' . code_suffix(4);

            $this->db->insert(
                "INSERT INTO " . $this->db->table("vendor_payments") . "
                (payment_no, payment_date, vendor_id, po_id, grn_id, invoice_ref, amount, tds_amount, net_amount, payment_mode, transaction_ref, transaction_date, paid_by, remarks, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [$payNo, input('payment_date') ?: date('Y-m-d'), $vendorId, input('po_id') ?: null, input('grn_id') ?: null, input('invoice_ref'), $amount, $tdsAmount, $netAmount, input('payment_mode'), input('transaction_ref'), input('payment_date') ?: date('Y-m-d'), current_user_id(), input('remarks')]
            );
            
            $this->log('VENDOR_PAYMENT_CREATED', "Vendor payment logged {$payNo}");
            $this->flash('success', "Vendor payment {$payNo} logged successfully.");
            $this->redirect('/accounts/vendor-payments');
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $payments = $this->db->fetchAll(
            "SELECT vp.*, v.company_name as vendor_name, po.po_no
             FROM " . $this->db->table("vendor_payments") . " vp
             LEFT JOIN " . $this->db->table("vendors") . " v ON vp.vendor_id = v.id
             LEFT JOIN " . $this->db->table("purchase_orders") . " po ON vp.po_id = po.id
             ORDER BY vp.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("vendor_payments"));
        
        $vendors = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("vendors") . " WHERE status = 'active'");
        $pos = $this->db->fetchAll("SELECT id, po_no FROM " . $this->db->table("purchase_orders") . " WHERE status IN ('sent','acknowledged','partial')");
        
        $this->view('vendor_payments/list', [
            'page_title' => 'Vendor Payments', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Vendor Payments',
            'payments' => $payments, 'pagination' => paginate($total, $page),
            'vendors' => $vendors, 'purchase_orders' => $pos
        ]);
    }

    // ==================== LEDGERS ====================

    public function ledger(): void {
        $this->requireCan('read');
        
        $entityType = input('entity_type'); // 'client' or 'vendor'
        $entityId = (int)input('entity_id');
        $statements = [];
        
        if ($entityType && $entityId) {
            if ($entityType === 'client') {
                // Only issued invoices are receivables: drafts are work-in-progress
                // and cancelled invoices are void, so neither belongs in the ledger.
                $statements = $this->db->fetchAll(
                    "SELECT 'invoice' as type, invoice_date as date, invoice_no as ref_no, 'Invoice Raised' as description, grand_total as debit, 0.00 as credit, created_at
                     FROM " . $this->db->table("invoices") . " WHERE client_id = ? AND status NOT IN ('draft','cancelled')
                     UNION ALL
                     SELECT 'payment' as type, receipt_date as date, receipt_no as ref_no, CONCAT('Receipt - ', UPPER(payment_mode)) as description, 0.00 as debit, amount as credit, created_at
                     FROM " . $this->db->table("payments") . " WHERE client_id = ?
                     ORDER BY date ASC, created_at ASC",
                    [$entityId, $entityId]
                );
            } elseif ($entityType === 'vendor') {
                $statements = $this->db->fetchAll(
                    "SELECT 'po' as type, po_date as date, po_no as ref_no, 'Purchase Order' as description, total_amount as debit, 0.00 as credit, created_at
                     FROM " . $this->db->table("purchase_orders") . " WHERE vendor_id = ? AND status NOT IN ('draft','cancelled')
                     UNION ALL
                     SELECT 'payment' as type, payment_date as date, payment_no as ref_no, CONCAT('Payment - ', UPPER(payment_mode)) as description, 0.00 as debit, amount as credit, created_at
                     FROM " . $this->db->table("vendor_payments") . " WHERE vendor_id = ?
                     ORDER BY date ASC, created_at ASC",
                    [$entityId, $entityId]
                );
            }
        }
        
        $clients = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        $vendors = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("vendors") . " WHERE status = 'active'");
        
        $this->view('ledger', [
            'page_title' => 'Ledger Report', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Ledger',
            'clients' => $clients, 'vendors' => $vendors, 'entity_type' => $entityType, 'entity_id' => $entityId,
            'statements' => $statements
        ]);
    }

    // ==================== GST SUMMARY (MONTHLY FILING) ====================

    /**
     * Monthly GST position for return filing: output tax collected on sales
     * invoices vs input tax (ITC) on purchase expenses, and the net payable.
     */
    public function gst(): void {
        $this->requireCan('read');

        $month = (string)input('month');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        // OUTPUT TAX — issued tax invoices for the month (drafts/cancelled and
        // proformas excluded; a proforma is not a tax invoice).
        $out = $this->db->fetchOne(
            "SELECT COUNT(*) AS cnt,
                    COALESCE(SUM(taxable_amount),0) AS taxable,
                    COALESCE(SUM(cgst_amount),0)   AS cgst,
                    COALESCE(SUM(sgst_amount),0)   AS sgst,
                    COALESCE(SUM(igst_amount),0)   AS igst,
                    COALESCE(SUM(grand_total),0)   AS total
             FROM " . $this->db->table("invoices") . "
             WHERE status NOT IN ('draft','cancelled') AND invoice_type <> 'proforma'
               AND DATE_FORMAT(invoice_date, '%Y-%m') = ?",
            [$month]
        );

        // INPUT TAX (ITC) — GST paid on expenses recorded in the month.
        $in = $this->db->fetchOne(
            "SELECT COUNT(*) AS cnt,
                    COALESCE(SUM(amount),0)     AS base,
                    COALESCE(SUM(gst_amount),0) AS gst
             FROM " . $this->db->table("expenses") . "
             WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?",
            [$month]
        );

        $outputGst = round((float)$out['cgst'] + (float)$out['sgst'] + (float)$out['igst'], 2);
        $inputGst  = round((float)$in['gst'], 2);
        $netPayable = round($outputGst - $inputGst, 2);

        // Sales register (GSTR-1 cross-check data)
        $sales = $this->db->fetchAll(
            "SELECT i.invoice_no, i.invoice_date, i.taxable_amount, i.cgst_amount, i.sgst_amount,
                    i.igst_amount, i.grand_total, c.company_name AS client_name, c.gstin AS client_gstin
             FROM " . $this->db->table("invoices") . " i
             LEFT JOIN " . $this->db->table("clients") . " c ON i.client_id = c.id
             WHERE i.status NOT IN ('draft','cancelled') AND i.invoice_type <> 'proforma'
               AND DATE_FORMAT(i.invoice_date, '%Y-%m') = ?
             ORDER BY i.invoice_date ASC, i.invoice_no ASC",
            [$month]
        );

        // Expense / ITC register
        $expenses = $this->db->fetchAll(
            "SELECT expense_no, expense_date, category, vendor, amount, gst_amount, total_amount
             FROM " . $this->db->table("expenses") . "
             WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?
             ORDER BY expense_date ASC",
            [$month]
        );

        // CSV export of the sales register for the month
        if (input('export') === 'csv') {
            $rows = [];
            foreach ($sales as $s) {
                $rows[] = [
                    $s['invoice_no'], format_date($s['invoice_date']), $s['client_name'] ?? '',
                    $s['client_gstin'] ?? '', number_format((float)$s['taxable_amount'], 2, '.', ''),
                    number_format((float)$s['cgst_amount'], 2, '.', ''), number_format((float)$s['sgst_amount'], 2, '.', ''),
                    number_format((float)$s['igst_amount'], 2, '.', ''), number_format((float)$s['grand_total'], 2, '.', ''),
                ];
            }
            export_csv($rows, ['Invoice No', 'Date', 'Client', 'GSTIN', 'Taxable', 'CGST', 'SGST', 'IGST', 'Total'], "GST-Sales-Register-{$month}.csv");
        }

        $this->view('gst', [
            'page_title' => 'GST Summary', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'GST Summary',
            'month' => $month,
            'period_label' => date('F Y', strtotime($month . '-01')),
            'out' => $out, 'in' => $in,
            'output_gst' => $outputGst, 'input_gst' => $inputGst, 'net_payable' => $netPayable,
            'sales' => $sales, 'expenses' => $expenses,
        ]);
    }

    public function viewInvoice($id = null): void {
        $this->requireCan('read');
        if (!$id) {
            $id = (int)input('id');
        } else {
            $id = (int)$id;
        }
        
        $invoice = $this->db->fetchOne(
            "SELECT i.*, 
                    c.company_name as client_name, c.gstin as client_gstin, c.email as client_email, c.phone as client_phone, c.address as client_address,
                    p.project_name, p.project_code
             FROM " . $this->db->table("invoices") . " i
             INNER JOIN " . $this->db->table("clients") . " c ON i.client_id = c.id
             LEFT JOIN " . $this->db->table("projects") . " p ON i.project_id = p.id
             WHERE i.id = ?",
            [$id]
        );
        
        if (!$invoice) {
            $this->flash('error', 'Invoice not found.');
            $this->redirect('/accounts/invoices');
        }
        
        $items = $this->db->fetchAll(
            "SELECT * FROM " . $this->db->table("invoice_items") . " WHERE invoice_id = ? ORDER BY sr_no ASC",
            [$id]
        );
        
        $company = $this->companyProfile();

        $this->view('invoices/view', [
            'page_title' => 'Invoice ' . $invoice['invoice_no'], 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Invoice Details',
            'invoice' => $invoice, 'items' => $items, 'company' => $company
        ]);
    }

    public function printInvoice($id = null): void {
        $this->requireCan('read');
        $id = (int)($id ?: input('id'));

        $invoice = $this->db->fetchOne(
            "SELECT i.*,
                    c.company_name as client_name, c.gstin as client_gstin, c.address as client_address,
                    p.project_name, p.project_code
             FROM " . $this->db->table("invoices") . " i
             INNER JOIN " . $this->db->table("clients") . " c ON i.client_id = c.id
             LEFT JOIN " . $this->db->table("projects") . " p ON i.project_id = p.id
             WHERE i.id = ?",
            [$id]
        );
        if (!$invoice) {
            $this->flash('error', 'Invoice not found.');
            $this->redirect('/accounts/invoices');
        }

        $items = $this->db->fetchAll(
            "SELECT * FROM " . $this->db->table("invoice_items") . " WHERE invoice_id = ? ORDER BY sr_no ASC",
            [$id]
        );

        $this->printView('invoices/print', 'Invoice ' . $invoice['invoice_no'], [
            'invoice' => $invoice,
            'items' => $items
        ]);
    }

    public function printReceipt($id = null): void {
        $this->requireCan('read');
        $id = (int)($id ?: input('id'));

        $payment = $this->db->fetchOne(
            "SELECT p.*, c.company_name as client_name, c.address as client_address, c.gstin as client_gstin,
                    i.invoice_no, i.grand_total as invoice_total, i.paid_amount as invoice_paid,
                    CONCAT(u.first_name, ' ', u.last_name) as received_by_name
             FROM " . $this->db->table("payments") . " p
             LEFT JOIN " . $this->db->table("clients") . " c ON p.client_id = c.id
             LEFT JOIN " . $this->db->table("invoices") . " i ON p.invoice_id = i.id
             LEFT JOIN " . $this->db->table("users") . " u ON p.received_by = u.id
             WHERE p.id = ?",
            [$id]
        );
        if (!$payment) {
            $this->flash('error', 'Payment not found.');
            $this->redirect('/accounts/payments');
        }

        $this->printView('payments/print', 'Receipt ' . $payment['receipt_no'], [
            'payment' => $payment
        ]);
    }

    public function editInvoice($id = null): void {
        $this->requireCan('update');
        if (!$id) {
            $id = (int)input('id');
        } else {
            $id = (int)$id;
        }

        $invoice = $this->db->fetchOne("SELECT * FROM " . $this->db->table("invoices") . " WHERE id = ?", [$id]);
        if (!$invoice) {
            $this->flash('error', 'Invoice not found.');
            $this->redirect('/accounts/invoices');
        }

        if ($invoice['status'] !== 'draft') {
            $this->flash('error', 'Only draft invoices can be edited.');
            $this->redirect('/accounts/invoices');
        }

        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/accounts/invoices/edit/' . $id); }
            
            $clientId = (int)input('client_id');

            // Totals honour the GST type / rates / round-off the user saw.
            $t = $this->computeInvoiceTotals();
            $items = $t['items'];

            if (!$clientId) {
                $this->flash('error', 'Please select a client for the invoice.');
                $this->redirect('/accounts/invoices/edit/' . $id);
            }
            if (empty($items) || $t['grandTotal'] <= 0) {
                $this->flash('error', 'Add at least one line item with a quantity and rate.');
                $this->redirect('/accounts/invoices/edit/' . $id);
            }

            $this->db->beginTransaction();
            try {
                $this->db->execute(
                    "UPDATE " . $this->db->table("invoices") . "
                     SET invoice_date = ?, due_date = ?, client_id = ?, po_reference = ?, billing_address = ?,
                         subtotal = ?, discount_amount = ?, taxable_amount = ?, cgst_rate = ?, cgst_amount = ?,
                         sgst_rate = ?, sgst_amount = ?, igst_rate = ?, igst_amount = ?, total_amount = ?,
                         round_off = ?, grand_total = ?, terms_conditions = ?, bank_details = ?, updated_at = NOW()
                     WHERE id = ?",
                    [input('invoice_date'), input('due_date'), $clientId, input('po_reference'), input('billing_address'),
                     $t['subtotal'], $t['discount'], $t['taxable'], $t['cgstRate'], $t['cgstAmt'], $t['sgstRate'], $t['sgstAmt'], $t['igstRate'], $t['igstAmt'],
                     $t['totalAmount'], $t['roundOff'], $t['grandTotal'], input('terms_conditions'), input('bank_details'), $id]
                );
                
                // Clear old items
                $this->db->execute("DELETE FROM " . $this->db->table("invoice_items") . " WHERE invoice_id = ?", [$id]);
                
                // Re-insert new items
                if (!empty($items)) {
                    foreach ($items as $index => $item) {
                        $qty = (float)($item['quantity'] ?? 0);
                        $rate = (float)($item['unit_rate'] ?? 0);
                        $itemAmt = $qty * $rate;
                        $this->db->execute(
                            "INSERT INTO " . $this->db->table("invoice_items") . " 
                            (invoice_id, sr_no, description, hsn_code, quantity, uom, unit_rate, amount)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                            [$id, $index + 1, $item['description'], $item['hsn_code'] ?? '',
                             $qty, $item['uom'] ?? 'Nos', $rate, $itemAmt]
                        );
                    }
                }
                
                $this->db->commit();
                $this->log('INVOICE_UPDATED', "Invoice ID {$id} updated successfully");
                $this->flash('success', "Invoice updated successfully.");
                $this->redirect('/accounts/invoices');
            } catch (\Exception $e) {
                $this->db->rollback();
                $this->flash('error', 'Database transaction failed: ' . $e->getMessage());
                $this->redirect('/accounts/invoices/edit/' . $id);
            }
        }

        $invoiceItems = $this->db->fetchAll("SELECT * FROM " . $this->db->table("invoice_items") . " WHERE invoice_id = ? ORDER BY sr_no ASC", [$id]);
        $clients = $this->db->fetchAll("SELECT id, company_name, gstin, address FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        
        $this->view('invoices/edit', [
            'page_title' => 'Edit Invoice - ' . $invoice['invoice_no'], 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Edit Invoice',
            'invoice' => $invoice, 'items' => $invoiceItems, 'clients' => $clients
        ]);
    }

    public function markAsPaid($id = null): void {
        $this->requireCan('update');
        $id = (int)($id ?: input('id'));

        $invoice = $this->db->fetchOne("SELECT * FROM " . $this->db->table("invoices") . " WHERE id = ?", [$id]);
        if (!$invoice) {
            $this->flash('error', 'Invoice not found.');
            $this->redirect('/accounts/invoices');
        }

        if ($invoice['status'] === 'cancelled') {
            $this->flash('error', 'A cancelled invoice cannot be marked as paid.');
            $this->redirect('/accounts/invoices');
        }

        $grandTotal = (float)$invoice['grand_total'];
        // Base the balance on payments actually recorded, not the paid_amount
        // field. This self-heals invoices that were "marked paid" before
        // receipts were generated: their paid_amount is set but no payment row
        // exists, so the ledger never showed the credit.
        $recordedPayments = (float)$this->db->fetchValue(
            "SELECT COALESCE(SUM(amount), 0) FROM " . $this->db->table("payments") . " WHERE invoice_id = ?",
            [$id]
        );
        $balance = round($grandTotal - $recordedPayments, 2);

        if ($balance <= 0) {
            // Make sure the invoice flags agree with the recorded payments
            if ($invoice['status'] !== 'paid') {
                $this->db->execute(
                    "UPDATE " . $this->db->table("invoices") . " SET paid_amount = ?, status = 'paid', paid_date = CURDATE(), updated_at = NOW() WHERE id = ?",
                    [$recordedPayments, $id]
                );
            }
            $this->flash('info', "Invoice {$invoice['invoice_no']} is already fully settled.");
            $this->redirect('/accounts/invoices');
        }

        $this->db->beginTransaction();
        try {
            // Record a payment receipt for the outstanding balance so the
            // ledger, payments list and invoice all stay in agreement.
            $receiptNo = 'REC-' . date('Ymd') . '-' . code_suffix(4);
            $this->db->insert(
                "INSERT INTO " . $this->db->table("payments") . "
                (receipt_no, receipt_date, invoice_id, client_id, amount, tds_amount, net_amount, payment_mode, transaction_date, received_by, remarks, created_at)
                VALUES (?, CURDATE(), ?, ?, ?, 0, ?, 'other', CURDATE(), ?, ?, NOW())",
                [$receiptNo, $id, (int)$invoice['client_id'], $balance, $balance, current_user_id(), 'Invoice marked as fully paid']
            );

            $this->db->execute(
                "UPDATE " . $this->db->table("invoices") . "
                 SET paid_amount = ?, status = 'paid', paid_date = CURDATE(), updated_at = NOW()
                 WHERE id = ?",
                [$grandTotal, $id]
            );
            $this->db->commit();

            $this->log('INVOICE_MARKED_PAID', "Invoice {$invoice['invoice_no']} settled via receipt {$receiptNo}");
            $this->flash('success', "Invoice {$invoice['invoice_no']} marked as paid (receipt {$receiptNo} recorded).");
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->flash('error', 'Failed to mark invoice as paid: ' . $e->getMessage());
        }
        $this->redirect('/accounts/invoices');
    }

    /**
     * Issue a draft invoice (draft -> sent) so it becomes a receivable and can
     * accept payments. Without this, invoices were stranded in 'draft' forever.
     */
    public function markAsSent($id = null): void {
        $this->requireCan('update');
        $id = (int)($id ?: input('id'));

        $invoice = $this->db->fetchOne("SELECT * FROM " . $this->db->table("invoices") . " WHERE id = ?", [$id]);
        if (!$invoice) {
            $this->flash('error', 'Invoice not found.');
            $this->redirect('/accounts/invoices');
        }
        if ($invoice['status'] !== 'draft') {
            $this->flash('info', 'Only draft invoices can be issued.');
            $this->redirect('/accounts/invoices');
        }

        $this->db->execute(
            "UPDATE " . $this->db->table("invoices") . " SET status = 'sent', updated_at = NOW() WHERE id = ?",
            [$id]
        );
        $this->log('INVOICE_SENT', "Invoice {$invoice['invoice_no']} issued");
        $this->flash('success', "Invoice {$invoice['invoice_no']} has been issued.");
        $this->redirect('/accounts/invoices');
    }

    public function deleteInvoice($id = null): void {
        $this->requireCan('delete');
        if (!$id) {
            $id = (int)input('id');
        } else {
            $id = (int)$id;
        }

        $invoice = $this->db->fetchOne("SELECT * FROM " . $this->db->table("invoices") . " WHERE id = ?", [$id]);
        if (!$invoice) {
            $this->flash('error', 'Invoice not found.');
            $this->redirect('/accounts/invoices');
        }

        // Once an invoice is issued it becomes part of the audit trail (and may
        // have payments/ledger entries against it). Only drafts may be deleted;
        // issued invoices should be cancelled instead.
        if ($invoice['status'] !== 'draft') {
            $this->flash('error', 'Only draft invoices can be deleted. Issued invoices must be cancelled to preserve the audit trail.');
            $this->redirect('/accounts/invoices');
        }

        $this->db->beginTransaction();
        try {
            // Delete dependent line items
            $this->db->execute("DELETE FROM " . $this->db->table("invoice_items") . " WHERE invoice_id = ?", [$id]);

            // Delete header row
            $this->db->execute("DELETE FROM " . $this->db->table("invoices") . " WHERE id = ?", [$id]);

            $this->db->commit();

            $this->log('INVOICE_DELETED', "Draft invoice {$invoice['invoice_no']} deleted");
            $this->flash('success', "Invoice {$invoice['invoice_no']} was successfully deleted.");
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->flash('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
        $this->redirect('/accounts/invoices');
    }
}
