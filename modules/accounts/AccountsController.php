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
            $subtotal = 0;
            $items = $_POST['items'] ?? [];
            foreach ($items as $item) { 
                $qty = (float)($item['quantity'] ?? 0);
                $rate = (float)($item['unit_rate'] ?? 0);
                $subtotal += ($qty * $rate); 
            }
            
            $discount = (float)(input('discount_amount', 0));
            $taxable = $subtotal - $discount;
            
            // Automatic GST Engine State Comparison
            $companyState = $this->db->fetchValue("SELECT setting_value FROM " . $this->db->table("settings") . " WHERE setting_key = 'company_state'") ?: 'Maharashtra';
            $companyGSTIN = $this->db->fetchValue("SELECT setting_value FROM " . $this->db->table("settings") . " WHERE setting_key = 'company_gstin'") ?: '27ABCDE1234F1Z1';
            
            $clientId = (int)input('client_id');
            $client = $this->db->fetchOne("SELECT state, gstin, address FROM " . $this->db->table("clients") . " WHERE id = ?", [$clientId]);
            $clientState = $client ? trim($client['state'] ?? '') : '';
            
            $isSameState = true;
            if ($client) {
                if (!empty($clientState)) {
                    if (strcasecmp($clientState, $companyState) !== 0) {
                        $isSameState = false;
                    }
                } else {
                    $clientGST = trim($client['gstin'] ?? '');
                    if (strlen($clientGST) >= 2 && strlen($companyGSTIN) >= 2) {
                        if (substr($clientGST, 0, 2) !== substr($companyGSTIN, 0, 2)) {
                            $isSameState = false;
                        }
                    }
                }
            }
            
            $cgstRate = 0; $cgstAmt = 0;
            $sgstRate = 0; $sgstAmt = 0;
            $igstRate = 0; $igstAmt = 0;
            
            if ($isSameState) {
                $cgstRate = 9.0;
                $sgstRate = 9.0;
                $cgstAmt = round(($taxable * $cgstRate) / 100, 2);
                $sgstAmt = round(($taxable * $sgstRate) / 100, 2);
            } else {
                $igstRate = 18.0;
                $igstAmt = round(($taxable * $igstRate) / 100, 2);
            }
            
            // Auto calculate round off and grand total
            $grandTotalBeforeRound = $taxable + $cgstAmt + $sgstAmt + $igstAmt;
            $grandTotal = round($grandTotalBeforeRound);
            $roundOff = round($grandTotal - $grandTotalBeforeRound, 2);
            
            $this->db->beginTransaction();
            try {
                $id = $this->db->insert(
                    "INSERT INTO " . $this->db->table("invoices") . " 
                    (invoice_no, invoice_date, due_date, client_id, po_reference, billing_address,
                     subtotal, discount_amount, taxable_amount, cgst_rate, cgst_amount, sgst_rate, sgst_amount,
                     igst_rate, igst_amount, total_amount, round_off, grand_total, terms_conditions, bank_details, status, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?, NOW())",
                    [$invNo, input('invoice_date'), input('due_date'), $clientId, input('po_reference'),
                     input('billing_address'), $subtotal, $discount, $taxable,
                     $cgstRate, $cgstAmt, $sgstRate, $sgstAmt, $igstRate, $igstAmt,
                     $taxable, $roundOff, $grandTotal, input('terms_conditions'), input('bank_details'), current_user_id()]
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
            $paymentDate = input('payment_date');
            
            if (!$receiptNo) {
                $receiptNo = 'REC-' . date('Ymd') . '-' . rand(1000, 9999);
            }
            
            $this->db->beginTransaction();
            try {
                // 1. Insert Payment
                $this->db->insert(
                    "INSERT INTO " . $this->db->table("payments") . " 
                    (receipt_no, receipt_date, invoice_id, client_id, amount, tds_amount, net_amount, payment_mode, transaction_ref, transaction_date, received_by, remarks, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                    [$receiptNo, $paymentDate, $invoiceId, $clientId, $amount, $tdsAmount, $netAmount, $paymentMode, $transactionRef, $paymentDate, current_user_id(), input('remarks')]
                );
                
                // 2. Real-Time Invoice Update
                $invoice = $this->db->fetchOne("SELECT grand_total, paid_amount FROM " . $this->db->table("invoices") . " WHERE id = ?", [$invoiceId]);
                if ($invoice) {
                    $newPaidAmount = (float)$invoice['paid_amount'] + $netAmount;
                    $status = ($newPaidAmount >= (float)$invoice['grand_total']) ? 'paid' : 'partial';
                    
                    $this->db->execute(
                        "UPDATE " . $this->db->table("invoices") . " 
                         SET paid_amount = ?, status = ?, paid_date = ?
                         WHERE id = ?",
                        [$newPaidAmount, $status, $paymentDate, $invoiceId]
                    );
                }
                
                $this->db->commit();
                $this->log('PAYMENT_RECEIVED', "Payment receipt {$receiptNo} recorded for invoice ID {$invoiceId}");
                $this->flash('success', "Payment of ₹" . number_format($amount, 2) . " received successfully.");
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
        $invoices = $this->db->fetchAll("SELECT id, invoice_no, grand_total, paid_amount, client_id FROM " . $this->db->table("invoices") . " WHERE status IN ('sent','partial','overdue')");
        
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
            
            $expNo = 'EXP-' . date('Ymd') . '-' . rand(1000, 9999);
            $amount = (float)input('amount');
            $gstAmount = (float)input('gst_amount', 0);
            $totalAmount = $amount + $gstAmount;
            
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
            
            $payNo = 'VP-' . date('Ymd') . '-' . rand(1000, 9999);
            $amount = (float)input('amount');
            $tdsAmount = (float)input('tds_amount', 0);
            $netAmount = $amount - $tdsAmount;
            
            $this->db->insert(
                "INSERT INTO " . $this->db->table("vendor_payments") . " 
                (payment_no, payment_date, vendor_id, po_id, grn_id, invoice_ref, amount, tds_amount, net_amount, payment_mode, transaction_ref, transaction_date, paid_by, remarks, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [$payNo, input('payment_date'), (int)input('vendor_id'), input('po_id') ?: null, input('grn_id') ?: null, input('invoice_ref'), $amount, $tdsAmount, $netAmount, input('payment_mode'), input('transaction_ref'), input('payment_date'), current_user_id(), input('remarks')]
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
                $statements = $this->db->fetchAll(
                    "SELECT 'invoice' as type, invoice_date as date, invoice_no as ref_no, 'Invoice Raised' as description, grand_total as debit, 0.00 as credit, created_at 
                     FROM " . $this->db->table("invoices") . " WHERE client_id = ?
                     UNION ALL
                     SELECT 'payment' as type, receipt_date as date, receipt_no as ref_no, CONCAT('Payment Mode: ', UPPER(payment_mode)) as description, 0.00 as debit, amount as credit, created_at 
                     FROM " . $this->db->table("payments") . " WHERE client_id = ?
                     ORDER BY date ASC, created_at ASC",
                    [$entityId, $entityId]
                );
            } elseif ($entityType === 'vendor') {
                $statements = $this->db->fetchAll(
                    "SELECT 'po' as type, po_date as date, po_no as ref_no, 'Purchase Order' as description, total_amount as debit, 0.00 as credit, created_at 
                     FROM " . $this->db->table("purchase_orders") . " WHERE vendor_id = ?
                     UNION ALL
                     SELECT 'payment' as type, payment_date as date, payment_no as ref_no, CONCAT('Payment Mode: ', UPPER(payment_mode)) as description, 0.00 as debit, amount as credit, created_at 
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

    public function viewInvoice($id = null): void {
        $this->requireCan('read');
        $id = (int)$id;
        
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
        
        // Fetch company profile settings
        $settingsRows = $this->db->fetchAll("SELECT setting_key, setting_value FROM " . $this->db->table("settings") . " WHERE setting_group = 'company'");
        $company = [];
        foreach ($settingsRows as $row) {
            $company[$row['setting_key']] = $row['setting_value'];
        }
        
        $this->view('invoices/view', [
            'page_title' => 'Invoice ' . $invoice['invoice_no'], 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Invoice Details',
            'invoice' => $invoice, 'items' => $items, 'company' => $company
        ]);
    }
}
