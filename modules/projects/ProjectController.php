<?php
/**
 * FabX ERP - Project Controller
 * Project Management with Gantt, BOQ, Work Orders
 */

namespace Modules\Projects;

use Core\Controller;

class ProjectController extends Controller {
    protected string $module = 'projects';

    public function __construct() {
        parent::__construct();
        require_auth();
    }

    public function index(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        $stage = input('stage');
        
        $where = "WHERE p.is_deleted = 0"; $params = [];
        if ($status) { $where .= " AND p.status = ?"; $params[] = $status; }
        if ($stage) { $where .= " AND p.current_stage = ?"; $params[] = $stage; }
        
        $projects = $this->db->fetchAll(
            "SELECT p.*, c.company_name as client_name, CONCAT(u.first_name, ' ', u.last_name) as pm_name
             FROM " . $this->db->table("projects") . " p
             LEFT JOIN " . $this->db->table("clients") . " c ON p.client_id = c.id
             LEFT JOIN " . $this->db->table("users") . " u ON p.project_manager_id = u.id
             {$where} ORDER BY p.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("projects") . " p {$where}", $params);
        
        $this->view('list', [
            'page_title' => 'Projects', 'breadcrumb_module' => 'Projects', 'breadcrumb_page' => 'All Projects',
            'projects' => $projects, 'pagination' => paginate($total, $page),
            'stats' => [
                'active' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("projects") . " WHERE status = 'active'"),
                'completed' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("projects") . " WHERE status = 'completed'"),
                'delayed' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("projects") . " WHERE status = 'delayed'"),
            ]
        ]);
    }

    public function create(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/projects/create'); }
            
            $prjCode = generate_project_code();
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("projects") . " 
                (project_code, project_name, client_id, description, project_type, contract_value,
                 start_date, target_end_date, project_manager_id, site_location, po_number, po_date,
                 status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())",
                [$prjCode, input('project_name'), input('client_id'), input('description'),
                 input('project_type'), input('contract_value'), input('start_date'),
                 input('target_end_date'), input('project_manager_id'), input('site_location'),
                 input('po_number'), input('po_date'), current_user_id()]
            );
            
            if ($id) {
                // Create project stages
                $stages = ['planning', 'design', 'procurement', 'production', 'assembly', 'painting', 'dispatch', 'installation'];
                foreach ($stages as $stage) {
                    $this->db->execute(
                        "INSERT INTO " . $this->db->table("project_stages") . " (project_id, stage_name, status) VALUES (?, ?, 'pending')",
                        [$id, $stage]
                    );
                }
                $this->log('PROJECT_CREATED', "Project {$prjCode} created");
                $this->flash('success', "Project {$prjCode} created successfully.");
                $this->redirect('/projects');
            }
            $this->flash('error', 'Failed to create project.');
        }
        
        $clients = $this->db->fetchAll("SELECT id, company_name FROM " . $this->db->table("clients") . " WHERE status = 'active'");
        $managers = $this->db->fetchAll("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM " . $this->db->table("users") . " WHERE status = 'active' AND role_id IN (SELECT id FROM " . $this->db->table("roles") . " WHERE name IN ('Project Manager', 'Production Manager', 'Director'))");
        
        $this->view('create', [
            'page_title' => 'Create Project', 'breadcrumb_module' => 'Projects', 'breadcrumb_page' => 'Create',
            'clients' => $clients, 'managers' => $managers
        ]);
    }

    public function show($id = null): void {
        $this->requireCan('read');
        $project = $this->db->fetchOne(
            "SELECT p.*, c.company_name as client_name, CONCAT(u.first_name, ' ', u.last_name) as pm_name
             FROM " . $this->db->table("projects") . " p
             LEFT JOIN " . $this->db->table("clients") . " c ON p.client_id = c.id
             LEFT JOIN " . $this->db->table("users") . " u ON p.project_manager_id = u.id
             WHERE p.id = ?", [$id]
        );
        if (!$project) { $this->flash('error', 'Project not found.'); $this->redirect('/projects'); }
        
        $stages = $this->db->fetchAll("SELECT * FROM " . $this->db->table("project_stages") . " WHERE project_id = ? ORDER BY id", [$id]);
        $boq = $this->db->fetchAll("SELECT * FROM " . $this->db->table("boq") . " WHERE project_id = ?", [$id]);
        $workOrders = $this->db->fetchAll("SELECT * FROM " . $this->db->table("work_orders") . " WHERE project_id = ? ORDER BY created_at DESC LIMIT 10", [$id]);
        
        $this->view('view', [
            'page_title' => $project['project_name'], 'breadcrumb_module' => 'Projects', 'breadcrumb_page' => $project['project_code'],
            'project' => $project, 'stages' => $stages, 'boq' => $boq, 'work_orders' => $workOrders
        ]);
    }

    public function gantt(): void {
        $this->requireCan('read');
        $projects = $this->db->fetchAll(
            "SELECT project_code, project_name, start_date, target_end_date, actual_end_date, progress_percentage, status, current_stage
             FROM " . $this->db->table("projects") . " WHERE status = 'active' ORDER BY start_date DESC LIMIT 20"
        );
        $this->view('gantt', [
            'page_title' => 'Gantt Chart', 'breadcrumb_module' => 'Projects', 'breadcrumb_page' => 'Gantt Chart',
            'projects' => $projects
        ]);
    }

    public function boq(): void {
        $this->requireCan('read');
        $projectId = input('project_id');
        $where = ""; $params = [];
        if ($projectId) { $where = "WHERE b.project_id = ?"; $params[] = $projectId; }
        
        $items = $this->db->fetchAll(
            "SELECT b.*, p.project_code, p.project_name FROM " . $this->db->table("boq") . " b
             LEFT JOIN " . $this->db->table("projects") . " p ON b.project_id = p.id {$where} ORDER BY b.id DESC LIMIT 100",
            $params
        );
        $this->view('boq', [
            'page_title' => 'Bill of Quantities', 'breadcrumb_module' => 'Projects', 'breadcrumb_page' => 'BOQ',
            'items' => $items
        ]);
    }

    public function workOrders(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $wos = $this->db->fetchAll(
            "SELECT wo.*, p.project_name, CONCAT(u.first_name, ' ', u.last_name) as assigned_name
             FROM " . $this->db->table("work_orders") . " wo
             LEFT JOIN " . $this->db->table("projects") . " p ON wo.project_id = p.id
             LEFT JOIN " . $this->db->table("users") . " u ON wo.assigned_to = u.id
             ORDER BY wo.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("work_orders"));
        
        $this->view('work_orders', [
            'page_title' => 'Work Orders', 'breadcrumb_module' => 'Projects', 'breadcrumb_page' => 'Work Orders',
            'work_orders' => $wos, 'pagination' => paginate($total, $page)
        ]);
    }

    public function production(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $reports = $this->db->fetchAll(
            "SELECT r.*, p.project_name, CONCAT(u.first_name, ' ', u.last_name) as reported_by_name
             FROM " . $this->db->table("production_reports") . " r
             LEFT JOIN " . $this->db->table("projects") . " p ON r.project_id = p.id
             LEFT JOIN " . $this->db->table("users") . " u ON r.reported_by = u.id
             ORDER BY r.report_date DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("production_reports"));
        
        $this->view('production', [
            'page_title' => 'Production Reports', 'breadcrumb_module' => 'Projects', 'breadcrumb_page' => 'Production',
            'reports' => $reports, 'pagination' => paginate($total, $page)
        ]);
    }

    public function drawings(): void {
        $this->requireCan('read');
        $projectId = input('project_id');
        $where = ""; $params = [];
        if ($projectId) { $where = "WHERE d.project_id = ?"; $params[] = $projectId; }
        
        $drawings = $this->db->fetchAll(
            "SELECT d.*, p.project_name, CONCAT(u.first_name, ' ', u.last_name) as prepared_name
             FROM " . $this->db->table("drawings") . " d
             LEFT JOIN " . $this->db->table("projects") . " p ON d.project_id = p.id
             LEFT JOIN " . $this->db->table("users") . " u ON d.prepared_by = u.id
             {$where} ORDER BY d.created_at DESC LIMIT 100",
            $params
        );
        $this->view('drawings', [
            'page_title' => 'Drawings', 'breadcrumb_module' => 'Projects', 'breadcrumb_page' => 'Drawings',
            'drawings' => $drawings
        ]);
    }
}
