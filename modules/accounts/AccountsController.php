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
            foreach ($items as $item) { $subtotal += ($item['amount'] ?? 0); }
            
            $taxable = $subtotal - (float)(input('discount_amount', 0));
            $cgstRate = (float)(input('cgst_rate', CGST_RATE));
            $sgstRate = (float)(input('sgst_rate', SGST_RATE));
            $cgstAmt = ($taxable * $cgstRate) / 100;
            $sgstAmt = ($taxable * $sgstRate) / 100;
            $grandTotal = $taxable + $cgstAmt + $sgstAmt;
            
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("invoices") . " 
                (invoice_no, invoice_date, due_date, client_id, po_reference, billing_address,
                 subtotal, discount_amount, taxable_amount, cgst_rate, cgst_amount, sgst_rate, sgst_amount,
                 total_amount, grand_total, terms_conditions, bank_details, status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?, NOW())",
                [$invNo, input('invoice_date'), input('due_date'), input('client_id'), input('po_reference'),
                 input('billing_address'), $subtotal, input('discount_amount', 0), $taxable,
                 $cgstRate, $cgstAmt, $sgstRate, $sgstAmt, $subtotal + $cgstAmt + $sgstAmt,
                 $grandTotal, input('terms_conditions'), input('bank_details'), current_user_id()]
            );
            
            if ($id && !empty($items)) {
                foreach ($items as $index => $item) {
                    $this->db->execute(
                        "INSERT INTO " . $this->db->table("invoice_items") . " 
                        (invoice_id, sr_no, description, hsn_code, quantity, uom, unit_rate, amount)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                        [$id, $index + 1, $item['description'], $item['hsn_code'] ?? '',
                         $item['quantity'] ?? 0, $item['uom'] ?? 'Nos', $item['unit_rate'] ?? 0, $item['amount'] ?? 0]
                    );
                }
            }
            
            $this->log('INVOICE_CREATED', "Invoice {$invNo} created");
            $this->flash('success', "Invoice {$invNo} created successfully.");
            $this->redirect('/accounts/invoices');
        }
        
        $clients = $this->db->fetchAll("SELECT id, company_name, gstin, address FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        $this->view('invoices/create', [
            'page_title' => 'Create Invoice', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Create Invoice',
            'clients' => $clients, 'invoice_no' => generate_invoice_no()
        ]);
    }

    public function payments(): void {
        $this->requireCan('read');
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
        
        $this->view('payments/list', [
            'page_title' => 'Payments', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Payments',
            'payments' => $payments, 'pagination' => paginate($total, $page)
        ]);
    }

    public function expenses(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $expenses = $this->db->fetchAll(
            "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) as created_name
             FROM " . $this->db->table("expenses") . " e
             LEFT JOIN " . $this->db->table("users") . " u ON e.created_by = u.id
             ORDER BY e.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("expenses"));
        
        $this->view('expenses/list', [
            'page_title' => 'Expenses', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Expenses',
            'expenses' => $expenses, 'pagination' => paginate($total, $page)
        ]);
    }

    public function vendorPayments(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $payments = $this->db->fetchAll(
            "SELECT vp.*, v.company_name as vendor_name
             FROM " . $this->db->table("vendor_payments") . " vp
             LEFT JOIN " . $this->db->table("vendors") . " v ON vp.vendor_id = v.id
             ORDER BY vp.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("vendor_payments"));
        
        $this->view('vendor_payments/list', [
            'page_title' => 'Vendor Payments', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Vendor Payments',
            'payments' => $payments, 'pagination' => paginate($total, $page)
        ]);
    }

    public function ledger(): void {
        $this->requireCan('read');
        $this->view('ledger', [
            'page_title' => 'Ledger Report', 'breadcrumb_module' => 'Accounts', 'breadcrumb_page' => 'Ledger'
        ]);
    }
}
