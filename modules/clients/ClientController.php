<?php
/**
 * FabX ERP - Client Controller
 * Client management, portal, support tickets, AMC
 */

namespace Modules\Clients;

use Core\Controller;

class ClientController extends Controller {
    protected string $module = 'clients';

    public function __construct() { 
        parent::__construct(); 
        require_auth(); 
    }

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
                 input('client_type', 'direct'), (float)input('credit_limit', 0), (int)input('credit_days', 30),
                 input('payment_terms'), current_user_id()]
            );
            
            if ($id) { 
                $this->log('CLIENT_CREATED', "Client {$clientCode} onboarded successfully");
                $this->flash('success', 'Client created successfully.'); 
                $this->redirect('/clients'); 
            }
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

    // ==================== SUPPORT TICKETS ====================

    public function tickets(): void {
        $this->requireCan('read');
        
        if (is_post()) {
            $this->requireCan('create');
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/clients/tickets'); }
            
            $action = input('action');
            if ($action === 'create') {
                $ticketNo = 'TKT-' . date('Ymd') . '-' . rand(1000, 9999);
                $this->db->insert(
                    "INSERT INTO " . $this->db->table("support_tickets") . " 
                    (ticket_no, client_id, project_id, subject, description, priority, category, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'open', NOW())",
                    [$ticketNo, (int)input('client_id'), input('project_id') ?: null, input('subject'), input('description'), input('priority', 'medium'), input('category', 'general')]
                );
                $this->log('TICKET_CREATED', "Support ticket {$ticketNo} logged");
                $this->flash('success', "Support ticket {$ticketNo} logged successfully.");
            } elseif ($action === 'update') {
                $this->requireCan('edit');
                $ticketId = (int)input('ticket_id');
                $status = input('status');
                $resolution = input('resolution');
                $resolvedAt = in_array($status, ['resolved', 'closed']) ? date('Y-m-d H:i:s') : null;
                
                $this->db->execute(
                    "UPDATE " . $this->db->table("support_tickets") . " 
                     SET status = ?, resolution = ?, resolved_at = ?
                     WHERE id = ?",
                    [$status, $resolution, $resolvedAt, $ticketId]
                );
                $this->log('TICKET_UPDATED', "Support ticket ID {$ticketId} status changed to {$status}");
                $this->flash('success', "Ticket status updated to " . ucfirst($status));
            }
            $this->redirect('/clients/tickets');
        }
        
        $tickets = $this->db->fetchAll(
            "SELECT t.*, c.company_name as client_name, p.project_name
             FROM " . $this->db->table("support_tickets") . " t
             LEFT JOIN " . $this->db->table("clients") . " c ON t.client_id = c.id
             LEFT JOIN " . $this->db->table("projects") . " p ON t.project_id = p.id
             ORDER BY t.created_at DESC"
        );
        
        $clients = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        $projects = $this->db->fetchAll("SELECT id, project_code, project_name FROM " . $this->db->table("projects") . " WHERE status = 'active'");
        
        $this->view('tickets', [
            'page_title' => 'Support Tickets', 'breadcrumb_module' => 'Clients', 'breadcrumb_page' => 'Support Tickets',
            'tickets' => $tickets, 'clients' => $clients, 'projects' => $projects
        ]);
    }

    // ==================== AMC CONTRACTS ====================

    public function amc(): void {
        $this->requireCan('read');
        
        if (is_post()) {
            $this->requireCan('create');
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/clients/amc'); }
            
            $action = input('action');
            if ($action === 'create') {
                $amcNo = input('amc_no');
                if (!$amcNo) {
                    $amcNo = 'AMC-' . date('Ymd') . '-' . rand(1000, 9999);
                }
                $this->db->insert(
                    "INSERT INTO " . $this->db->table("amc") . " 
                    (amc_no, client_id, project_id, description, start_date, end_date, value, visit_frequency, total_visits, completed_visits, status, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())",
                    [$amcNo, (int)input('client_id'), input('project_id') ?: null, input('description'), input('start_date'), input('end_date'), (float)input('value'), input('visit_frequency', 'quarterly'), (int)input('total_visits', 4), (int)input('completed_visits', 0), current_user_id()]
                );
                $this->log('AMC_CREATED', "AMC contract {$amcNo} generated");
                $this->flash('success', "AMC contract {$amcNo} registered successfully.");
            } elseif ($action === 'update_visits') {
                $this->requireCan('edit');
                $amcId = (int)input('amc_id');
                $completed = (int)input('completed_visits');
                $status = input('status');
                
                $this->db->execute(
                    "UPDATE " . $this->db->table("amc") . " 
                     SET completed_visits = ?, status = ?
                     WHERE id = ?",
                    [$completed, $status, $amcId]
                );
                $this->log('AMC_UPDATED', "AMC contract ID {$amcId} updated");
                $this->flash('success', "AMC contract visits and status updated.");
            }
            $this->redirect('/clients/amc');
        }
        
        $contracts = $this->db->fetchAll(
            "SELECT a.*, c.company_name as client_name, p.project_name, p.project_code
             FROM " . $this->db->table("amc") . " a
             LEFT JOIN " . $this->db->table("clients") . " c ON a.client_id = c.id
             LEFT JOIN " . $this->db->table("projects") . " p ON a.project_id = p.id
             ORDER BY a.created_at DESC"
        );
        
        $clients = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        $projects = $this->db->fetchAll("SELECT id, project_code, project_name FROM " . $this->db->table("projects") . " WHERE status = 'active'");
        
        $this->view('amc', [
            'page_title' => 'Annual Maintenance Contracts (AMC)', 'breadcrumb_module' => 'Clients', 'breadcrumb_page' => 'AMC Contracts',
            'contracts' => $contracts, 'clients' => $clients, 'projects' => $projects
        ]);
    }
}
