<?php
/**
 * FabX ERP - Purchase Controller
 * Purchase requisitions, orders, GRN, inventory
 */

namespace Modules\Purchase;

use Core\Controller;

class PurchaseController extends Controller {
    protected string $module = 'purchase';

    public function __construct() {
        parent::__construct();
        require_auth();
    }

    // ==================== PURCHASE REQUISITIONS ====================

    public function requisitions(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        
        $where = "WHERE 1=1"; $params = [];
        if ($status) { $where .= " AND pr.status = ?"; $params[] = $status; }
        
        $prs = $this->db->fetchAll(
            "SELECT pr.*, d.name as department_name, CONCAT(u.first_name, ' ', u.last_name) as requested_by_name
             FROM " . $this->db->table("purchase_requisitions") . " pr
             LEFT JOIN " . $this->db->table("departments") . " d ON pr.department_id = d.id
             LEFT JOIN " . $this->db->table("users") . " u ON pr.requested_by = u.id
             {$where} ORDER BY pr.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("purchase_requisitions") . " pr {$where}", $params);
        
        $this->view('requisitions/list', [
            'page_title' => 'Purchase Requisitions', 'breadcrumb_module' => 'Purchase', 'breadcrumb_page' => 'PR',
            'requisitions' => $prs, 'pagination' => paginate($total, $page)
        ]);
    }

    public function createRequisition(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/purchase/requisitions/create'); }
            
            $prNo = generate_code('PR');
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("purchase_requisitions") . " 
                (pr_no, pr_date, department_id, project_id, required_by_date, justification, requested_by, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'submitted', NOW())",
                [$prNo, date('Y-m-d'), input('department_id'), input('project_id') ?: null,
                 input('required_by_date'), input('justification'), current_user_id()]
            );
            
            if ($id && !empty($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    $this->db->execute(
                        "INSERT INTO " . $this->db->table("pr_items") . " (pr_id, item_id, description, quantity, uom, required_by_date, purpose)
                        VALUES (?, ?, ?, ?, ?, ?, ?)",
                        [$id, $item['item_id'] ?: null, $item['description'], $item['quantity'], $item['uom'] ?? 'Nos', $item['required_date'] ?? null, $item['purpose'] ?? '']
                    );
                }
            }
            
            $this->log('PR_CREATED', "PR {$prNo} created");
            $this->flash('success', "PR {$prNo} submitted successfully.");
            $this->redirect('/purchase/requisitions');
        }
        
        $departments = $this->db->fetchAll("SELECT * FROM " . $this->db->table("departments"));
        $projects = $this->db->fetchAll("SELECT id, project_name FROM " . $this->db->table("projects") . " WHERE status = 'active'");
        
        $this->view('requisitions/create', [
            'page_title' => 'Create PR', 'breadcrumb_module' => 'Purchase', 'breadcrumb_page' => 'Create PR',
            'departments' => $departments, 'projects' => $projects
        ]);
    }

    // ==================== PURCHASE ORDERS ====================

    public function orders(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $pos = $this->db->fetchAll(
            "SELECT po.*, v.company_name as vendor_name, CONCAT(u.first_name, ' ', u.last_name) as prepared_by_name
             FROM " . $this->db->table("purchase_orders") . " po
             LEFT JOIN " . $this->db->table("vendors") . " v ON po.vendor_id = v.id
             LEFT JOIN " . $this->db->table("users") . " u ON po.prepared_by = u.id
             ORDER BY po.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("purchase_orders"));
        
        $this->view('orders/list', [
            'page_title' => 'Purchase Orders', 'breadcrumb_module' => 'Purchase', 'breadcrumb_page' => 'PO',
            'orders' => $pos, 'pagination' => paginate($total, $page)
        ]);
    }

    public function createOrder(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/purchase/orders/create'); }
            
            $poNo = generate_po_no();
            $subtotal = 0;
            $items = $_POST['items'] ?? [];
            foreach ($items as $item) { $subtotal += ($item['quantity'] ?? 0) * ($item['unit_rate'] ?? 0); }
            $gstAmount = ($subtotal * DEFAULT_GST_RATE) / 100;
            $totalAmount = $subtotal + $gstAmount;
            
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("purchase_orders") . " 
                (po_no, po_date, pr_id, vendor_id, delivery_date, delivery_location, terms_conditions,
                 payment_terms, subtotal, gst_amount, total_amount, prepared_by, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', NOW())",
                [$poNo, date('Y-m-d'), input('pr_id') ?: null, input('vendor_id'), input('delivery_date'),
                 input('delivery_location'), input('terms_conditions'), input('payment_terms'),
                 $subtotal, $gstAmount, $totalAmount, current_user_id()]
            );
            
            if ($id && !empty($items)) {
                foreach ($items as $index => $item) {
                    $itemTotal = ($item['quantity'] ?? 0) * ($item['unit_rate'] ?? 0);
                    $this->db->execute(
                        "INSERT INTO " . $this->db->table("po_items") . " (po_id, item_id, description, specification, quantity, uom, unit_rate, total_amount)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                        [$id, $item['item_id'] ?: null, $item['description'] ?? '', $item['specification'] ?? '',
                         $item['quantity'] ?? 0, $item['uom'] ?? 'Nos', $item['unit_rate'] ?? 0, $itemTotal]
                    );
                }
            }
            
            $this->log('PO_CREATED', "PO {$poNo} created");
            $this->flash('success', "PO {$poNo} created successfully.");
            $this->redirect('/purchase/orders');
        }
        
        $vendors = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("vendors") . " WHERE status = 'active' AND approval_status = 'approved'");
        $items = $this->db->fetchAll("SELECT id, item_code, name, uom FROM " . $this->db->table("items") . " WHERE status = 'active'");
        
        $this->view('orders/create', [
            'page_title' => 'Create PO', 'breadcrumb_module' => 'Purchase', 'breadcrumb_page' => 'Create PO',
            'vendors' => $vendors, 'items' => $items
        ]);
    }

    // ==================== GRN ====================

    public function grn(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $grns = $this->db->fetchAll(
            "SELECT g.*, v.company_name as vendor_name, po.po_no
             FROM " . $this->db->table("grn") . " g
             LEFT JOIN " . $this->db->table("vendors") . " v ON g.vendor_id = v.id
             LEFT JOIN " . $this->db->table("purchase_orders") . " po ON g.po_id = po.id
             ORDER BY g.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("grn"));
        
        $this->view('grn/list', [
            'page_title' => 'GRN', 'breadcrumb_module' => 'Purchase', 'breadcrumb_page' => 'GRN',
            'grns' => $grns, 'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== INVENTORY ====================

    public function inventory(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $items = $this->db->fetchAll(
            "SELECT i.*, ic.name as category_name
             FROM " . $this->db->table("items") . " i
             LEFT JOIN " . $this->db->table("item_categories") . " ic ON i.category_id = ic.id
             ORDER BY i.item_code LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("items"));
        
        $lowStock = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("items") . " WHERE current_stock <= reorder_level AND status = 'active'");
        
        $this->view('inventory/list', [
            'page_title' => 'Inventory', 'breadcrumb_module' => 'Purchase', 'breadcrumb_page' => 'Inventory',
            'items' => $items, 'pagination' => paginate($total, $page), 'low_stock_count' => $lowStock
        ]);
    }

    // ==================== MATERIAL ISSUES ====================

    public function issues(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $issues = $this->db->fetchAll(
            "SELECT mi.*, p.project_name, CONCAT(u.first_name, ' ', u.last_name) as issued_to_name
             FROM " . $this->db->table("material_issues") . " mi
             LEFT JOIN " . $this->db->table("projects") . " p ON mi.project_id = p.id
             LEFT JOIN " . $this->db->table("users") . " u ON mi.issued_to = u.id
             ORDER BY mi.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("material_issues"));
        
        $this->view('issues/list', [
            'page_title' => 'Material Issues', 'breadcrumb_module' => 'Purchase', 'breadcrumb_page' => 'Issues',
            'issues' => $issues, 'pagination' => paginate($total, $page)
        ]);
    }
}
