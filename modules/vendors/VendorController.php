<?php
/**
 * FabX ERP - Vendor Controller
 * Vendor management, evaluation, compliance
 */

namespace Modules\Vendors;

use Core\Controller;

class VendorController extends Controller {
    protected string $module = 'vendors';

    public function __construct() { parent::__construct(); require_auth(); }

    public function index(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        $where = "WHERE 1=1"; $params = [];
        if ($status) { $where .= " AND status = ?"; $params[] = $status; }
        
        $vendors = $this->db->fetchAll(
            "SELECT v.*, CONCAT(u.first_name, ' ', u.last_name) as approved_by_name
             FROM " . $this->db->table("vendors") . " v
             LEFT JOIN " . $this->db->table("users") . " u ON v.approved_by = u.id
             {$where} ORDER BY v.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("vendors") . " {$where}", $params);
        
        $this->view('list', [
            'page_title' => 'Vendors', 'breadcrumb_module' => 'Vendors', 'breadcrumb_page' => 'All Vendors',
            'vendors' => $vendors, 'pagination' => paginate($total, $page),
            'stats' => [
                'total' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("vendors") . " WHERE status = 'active'") ?? 0,
                'pending' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("vendors") . " WHERE approval_status = 'pending'") ?? 0,
            ]
        ]);
    }

    public function create(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/vendors/create'); }
            
            $vendorCode = generate_code('VN');
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("vendors") . " 
                (vendor_code, company_name, contact_person, email, phone, address, city, state, country,
                 pincode, gstin, pan, vendor_type, category, credit_days, bank_name, bank_account_no,
                 bank_ifsc, approval_status, status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'active', ?, NOW())",
                [$vendorCode, input('company_name'), input('contact_person'), input('email'), input('phone'),
                 input('address'), input('city'), input('state'), input('country', 'India'), input('pincode'),
                 input('gstin'), input('pan'), input('vendor_type'), input('category'), input('credit_days', 30),
                 input('bank_name'), input('bank_account_no'), input('bank_ifsc'), current_user_id()]
            );
            
            if ($id) { $this->flash('success', 'Vendor submitted for approval.'); $this->redirect('/vendors'); }
            $this->flash('error', 'Failed to create vendor.');
        }
        $this->view('create', ['page_title' => 'Create Vendor', 'breadcrumb_module' => 'Vendors', 'breadcrumb_page' => 'Create']);
    }

    public function show($id = null): void {
        $this->requireCan('read');
        $vendor = $this->db->fetchOne("SELECT * FROM " . $this->db->table("vendors") . " WHERE id = ?", [$id]);
        if (!$vendor) { $this->flash('error', 'Vendor not found.'); $this->redirect('/vendors'); }
        
        $evaluations = $this->db->fetchAll("SELECT * FROM " . $this->db->table("vendor_evaluations") . " WHERE vendor_id = ? ORDER BY evaluation_date DESC", [$id]);
        $pos = $this->db->fetchAll("SELECT * FROM " . $this->db->table("purchase_orders") . " WHERE vendor_id = ? ORDER BY created_at DESC LIMIT 10", [$id]);
        
        $this->view('view', [
            'page_title' => $vendor['company_name'], 'breadcrumb_module' => 'Vendors', 'breadcrumb_page' => $vendor['company_name'],
            'vendor' => $vendor, 'evaluations' => $evaluations, 'purchase_orders' => $pos
        ]);
    }
}
