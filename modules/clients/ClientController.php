<?php
/**
 * FabX ERP - Client Controller
 * Client management, portal, support tickets, AMC
 */

namespace Modules\Clients;

use Core\Controller;

class ClientController extends Controller {
    protected string $module = 'clients';

    public function __construct() { parent::__construct(); require_auth(); }

    public function index(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        $where = "WHERE 1=1"; $params = [];
        if ($status) { $where .= " AND status = ?"; $params[] = $status; }
        
        $clients = $this->db->fetchAll(
            "SELECT * FROM " . $this->db->table("clients") . " {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("clients") . " {$where}", $params);
        
        $this->view('list', [
            'page_title' => 'Clients', 'breadcrumb_module' => 'Clients', 'breadcrumb_page' => 'All Clients',
            'clients' => $clients, 'pagination' => paginate($total, $page),
            'stats' => [
                'total' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("clients") . " WHERE status = 'active'") ?? 0,
                'total_receivable' => $this->db->fetchValue("SELECT SUM(grand_total - paid_amount) FROM " . $this->db->table("invoices") . " WHERE status IN ('sent','partial','overdue')") ?? 0,
            ]
        ]);
    }

    public function create(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/clients/create'); }
            
            $clientCode = generate_code('CL');
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("clients") . " 
                (client_code, company_name, contact_person, email, phone, alt_phone, address, city, state,
                 country, pincode, gstin, pan, website, industry, client_type, credit_limit, credit_days,
                 payment_terms, status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())",
                [$clientCode, input('company_name'), input('contact_person'), input('email'), input('phone'),
                 input('alt_phone'), input('address'), input('city'), input('state'), input('country', 'India'),
                 input('pincode'), input('gstin'), input('pan'), input('website'), input('industry'),
                 input('client_type', 'direct'), input('credit_limit', 0), input('credit_days', 30),
                 input('payment_terms'), current_user_id()]
            );
            
            if ($id) { $this->flash('success', 'Client created successfully.'); $this->redirect('/clients'); }
            $this->flash('error', 'Failed to create client.');
        }
        $this->view('create', ['page_title' => 'Create Client', 'breadcrumb_module' => 'Clients', 'breadcrumb_page' => 'Create']);
    }

    public function show($id = null): void {
        $this->requireCan('read');
        $client = $this->db->fetchOne("SELECT * FROM " . $this->db->table("clients") . " WHERE id = ?", [$id]);
        if (!$client) { $this->flash('error', 'Client not found.'); $this->redirect('/clients'); }
        
        $contacts = $this->db->fetchAll("SELECT * FROM " . $this->db->table("client_contacts") . " WHERE client_id = ?", [$id]);
        $projects = $this->db->fetchAll("SELECT * FROM " . $this->db->table("projects") . " WHERE client_id = ? ORDER BY created_at DESC LIMIT 10", [$id]);
        $invoices = $this->db->fetchAll("SELECT * FROM " . $this->db->table("invoices") . " WHERE client_id = ? ORDER BY created_at DESC LIMIT 10", [$id]);
        $tickets = $this->db->fetchAll("SELECT * FROM " . $this->db->table("support_tickets") . " WHERE client_id = ? ORDER BY created_at DESC LIMIT 5", [$id]);
        
        $this->view('view', [
            'page_title' => $client['company_name'], 'breadcrumb_module' => 'Clients', 'breadcrumb_page' => $client['company_name'],
            'client' => $client, 'contacts' => $contacts, 'projects' => $projects, 'invoices' => $invoices, 'tickets' => $tickets
        ]);
    }
}
