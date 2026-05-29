<?php
/**
 * FabX ERP - CRM Controller
 * Customer Relationship Management
 */

namespace Modules\Crm;

use Core\Controller;

class CRMController extends Controller {
    protected string $module = 'crm';

    public function __construct() {
        parent::__construct();
        require_auth();
    }

    // ==================== LEADS ====================

    public function leads(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        
        $where = "WHERE 1=1"; $params = [];
        if ($status) { $where .= " AND status = ?"; $params[] = $status; }
        
        $leads = $this->db->fetchAll(
            "SELECT l.*, CONCAT(u.first_name, ' ', u.last_name) as assigned_name
             FROM " . $this->db->table("leads") . " l
             LEFT JOIN " . $this->db->table("users") . " u ON l.assigned_to = u.id
             {$where} ORDER BY l.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("leads") . " l {$where}", $params);
        
        $this->view('leads/list', [
            'page_title' => 'Leads', 'breadcrumb_module' => 'CRM', 'breadcrumb_page' => 'Leads',
            'leads' => $leads, 'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== INQUIRIES ====================

    public function inquiries(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $inquiries = $this->db->fetchAll(
            "SELECT * FROM " . $this->db->table("leads") . " WHERE status = 'new' ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("leads") . " WHERE status = 'new'");
        
        $this->view('inquiries/list', [
            'page_title' => 'Inquiries', 'breadcrumb_module' => 'CRM', 'breadcrumb_page' => 'Inquiries',
            'inquiries' => $inquiries, 'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== QUOTATIONS ====================

    public function quotations(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        
        $where = "WHERE 1=1"; $params = [];
        if ($status) { $where .= " AND q.status = ?"; $params[] = $status; }
        
        $quotations = $this->db->fetchAll(
            "SELECT q.*, c.company_name as client_name, CONCAT(u.first_name, ' ', u.last_name) as prepared_by_name
             FROM " . $this->db->table("quotations") . " q
             LEFT JOIN " . $this->db->table("clients") . " c ON q.client_id = c.id
             LEFT JOIN " . $this->db->table("users") . " u ON q.prepared_by = u.id
             {$where} ORDER BY q.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("quotations") . " q {$where}", $params);
        
        $this->view('quotations/list', [
            'page_title' => 'Quotations', 'breadcrumb_module' => 'CRM', 'breadcrumb_page' => 'Quotations',
            'quotations' => $quotations, 'pagination' => paginate($total, $page)
        ]);
    }

    public function createQuotation(): void {
        $this->requireCan('create');
        
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/crm/quotations/create'); }
            
            $qtNo = generate_quotation_no();
            $subtotal = 0;
            $items = $_POST['items'] ?? [];
            foreach ($items as $item) { $subtotal += ($item['quantity'] ?? 0) * ($item['unit_rate'] ?? 0); }
            
            $gstRate = (float)(input('gst_rate', DEFAULT_GST_RATE));
            $gstAmount = ($subtotal * $gstRate) / 100;
            $totalAmount = $subtotal + $gstAmount;
            
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("quotations") . " 
                (quotation_no, quotation_date, client_id, contact_person, subject, description,
                 terms_conditions, delivery_terms, payment_terms, subtotal, gst_rate, gst_amount, 
                 total_amount, prepared_by, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', NOW())",
                [$qtNo, input('quotation_date'), input('client_id'), input('contact_person'),
                 input('subject'), input('description'), input('terms_conditions'),
                 input('delivery_terms'), input('payment_terms'), $subtotal, $gstRate, $gstAmount,
                 $totalAmount, current_user_id()]
            );
            
            if ($id && !empty($items)) {
                foreach ($items as $index => $item) {
                    $itemTotal = ($item['quantity'] ?? 0) * ($item['unit_rate'] ?? 0);
                    $this->db->execute(
                        "INSERT INTO " . $this->db->table("quotation_items") . " 
                        (quotation_id, sr_no, description, specification, quantity, uom, unit_rate, total_amount)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                        [$id, $index + 1, $item['description'] ?? '', $item['specification'] ?? '',
                         $item['quantity'] ?? 0, $item['uom'] ?? 'Nos', $item['unit_rate'] ?? 0, $itemTotal]
                    );
                }
            }
            
            $this->log('QUOTATION_CREATED', "Quotation {$qtNo} created");
            $this->flash('success', "Quotation {$qtNo} created successfully.");
            $this->redirect('/crm/quotations');
        }
        
        $clients = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        $this->view('quotations/create', [
            'page_title' => 'Create Quotation', 'breadcrumb_module' => 'CRM', 'breadcrumb_page' => 'Create Quotation',
            'clients' => $clients, 'quotation_no' => generate_quotation_no()
        ]);
    }

    // ==================== FOLLOW-UPS ====================

    public function followups(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $followups = $this->db->fetchAll(
            "SELECT f.*, l.company_name as lead_name, CONCAT(u.first_name, ' ', u.last_name) as conducted_by_name
             FROM " . $this->db->table("followups") . " f
             LEFT JOIN " . $this->db->table("leads") . " l ON f.lead_id = l.id
             LEFT JOIN " . $this->db->table("users") . " u ON f.conducted_by = u.id
             ORDER BY f.followup_date DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("followups"));
        
        $this->view('followups/list', [
            'page_title' => 'Follow-ups', 'breadcrumb_module' => 'CRM', 'breadcrumb_page' => 'Follow-ups',
            'followups' => $followups, 'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== SALES PIPELINE ====================

    public function pipeline(): void {
        $this->requireCan('read');
        
        $pipeline = [
            'new' => $this->db->fetchAll("SELECT * FROM " . $this->db->table("leads") . " WHERE status = 'new' ORDER BY created_at DESC LIMIT 10"),
            'contacted' => $this->db->fetchAll("SELECT * FROM " . $this->db->table("leads") . " WHERE status = 'contacted' ORDER BY created_at DESC LIMIT 10"),
            'qualified' => $this->db->fetchAll("SELECT * FROM " . $this->db->table("leads") . " WHERE status = 'qualified' ORDER BY created_at DESC LIMIT 10"),
            'proposal_sent' => $this->db->fetchAll("SELECT * FROM " . $this->db->table("leads") . " WHERE status = 'proposal_sent' ORDER BY created_at DESC LIMIT 10"),
            'negotiation' => $this->db->fetchAll("SELECT * FROM " . $this->db->table("leads") . " WHERE status = 'negotiation' ORDER BY created_at DESC LIMIT 10"),
            'won' => $this->db->fetchAll("SELECT * FROM " . $this->db->table("leads") . " WHERE status = 'won' ORDER BY created_at DESC LIMIT 10"),
        ];
        
        $this->view('pipeline/index', [
            'page_title' => 'Sales Pipeline', 'breadcrumb_module' => 'CRM', 'breadcrumb_page' => 'Pipeline',
            'pipeline' => $pipeline
        ]);
    }
}
