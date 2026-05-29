<?php
/**
 * FabX ERP - QMS Controller (ISO 9001)
 * Complete Quality Management System
 */

namespace Modules\Qms;

use Core\Controller;

class QMSController extends Controller {
    protected string $module = 'qms';

    public function __construct() {
        parent::__construct();
        require_auth();
    }

    // ==================== DASHBOARD ====================
    
    public function index(): void {
        $this->requireCan('read');
        
        $stats = [
            'total_documents' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("documents")) ?? 0,
            'active_ncrs' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("ncr") . " WHERE status IN ('open','in_progress')") ?? 0,
            'open_capas' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("capa") . " WHERE status IN ('open','in_progress')") ?? 0,
            'planned_audits' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("audits") . " WHERE status = 'planned'") ?? 0,
            'upcoming_calibrations' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("calibrations") . " WHERE next_calibration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)") ?? 0,
            'open_complaints' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("complaints") . " WHERE status != 'closed'") ?? 0,
            'overdue_calibrations' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("calibrations") . " WHERE next_calibration_date < CURDATE()") ?? 0,
            'active_risks' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("risks") . " WHERE status = 'open'") ?? 0,
        ];

        $this->view('index', [
            'page_title' => 'QMS Dashboard - ISO 9001',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Dashboard',
            'stats' => $stats
        ]);
    }

    // ==================== DOCUMENT CONTROL ====================

    public function documents(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $search = input('search');
        $category = input('category');
        $status = input('status');
        
        $where = [];
        $params = [];
        $sql = "SELECT d.*, dc.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as prepared_by_name 
                FROM " . $this->db->table("documents") . " d
                LEFT JOIN " . $this->db->table("doc_categories") . " dc ON d.category_id = dc.id
                LEFT JOIN " . $this->db->table("users") . " u ON d.prepared_by = u.id
                WHERE d.is_deleted = 0";
        
        if ($search) {
            $sql .= " AND (d.title LIKE ? OR d.doc_code LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($category) {
            $sql .= " AND d.category_id = ?";
            $params[] = $category;
        }
        if ($status) {
            $sql .= " AND d.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY d.created_at DESC LIMIT ? OFFSET ?";
        $params[] = DEFAULT_PER_PAGE;
        $params[] = ($page - 1) * DEFAULT_PER_PAGE;
        
        $documents = $this->db->fetchAll($sql, $params);
        
        // Count total
        $countSql = str_replace("SELECT d.*, dc.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as prepared_by_name", "SELECT COUNT(*)", $sql);
        $countSql = preg_replace('/LIMIT \? OFFSET \?$/', '', $countSql);
        array_pop($params); array_pop($params);
        $total = (int)$this->db->fetchValue($countSql, $params);
        
        $categories = $this->db->fetchAll("SELECT * FROM " . $this->db->table("doc_categories") . " WHERE status = 'active'");
        
        $this->view('documents/list', [
            'page_title' => 'Document Control',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Documents',
            'documents' => $documents,
            'categories' => $categories,
            'pagination' => paginate($total, $page),
            'filters' => compact('search', 'category', 'status')
        ]);
    }

    public function createDocument(): void {
        $this->requireCan('create');
        
        if (is_post()) {
            if (!validate_csrf()) {
                $this->flash('error', 'Invalid security token.');
                $this->redirect('/qms/documents/create');
            }

            $docCode = input('doc_code') ?: generate_code('DOC');
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("documents") . " 
                (doc_code, title, category_id, version, description, department_id, prepared_by, status, effective_date, created_by, created_at)
                VALUES (?, ?, ?, '1.0', ?, ?, ?, 'draft', ?, ?, NOW())",
                [
                    $docCode,
                    input('title'),
                    input('category_id'),
                    input('description'),
                    input('department_id'),
                    current_user_id(),
                    input('effective_date'),
                    current_user_id()
                ]
            );

            if ($id) {
                $this->log('DOCUMENT_CREATED', "Document {$docCode} created");
                $this->flash('success', 'Document created successfully.');
                $this->redirect('/qms/documents');
            } else {
                $this->flash('error', 'Failed to create document.');
            }
        }

        $categories = $this->db->fetchAll("SELECT * FROM " . $this->db->table("doc_categories") . " WHERE status = 'active'");
        $departments = $this->db->fetchAll("SELECT * FROM " . $this->db->table("departments") . " WHERE status = 'active'");
        
        $this->view('documents/create', [
            'page_title' => 'Create Document',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Create Document',
            'categories' => $categories,
            'departments' => $departments
        ]);
    }

    public function editDocument($id = null): void {
        $this->requireCan('update');
        $document = $this->db->fetchOne("SELECT * FROM " . $this->db->table("documents") . " WHERE id = ?", [$id]);
        if (!$document) { $this->flash('error', 'Document not found.'); $this->redirect('/qms/documents'); }
        
        if (is_post()) {
            $this->db->execute(
                "UPDATE " . $this->db->table("documents") . " 
                 SET title = ?, category_id = ?, description = ?, department_id = ?, status = ?, updated_at = NOW() 
                 WHERE id = ?",
                [input('title'), input('category_id'), input('description'), input('department_id'), input('status'), $id]
            );
            $this->log('DOCUMENT_UPDATED', "Document {$document['doc_code']} updated");
            $this->flash('success', 'Document updated successfully.');
            $this->redirect('/qms/documents');
        }

        $categories = $this->db->fetchAll("SELECT * FROM " . $this->db->table("doc_categories"));
        $departments = $this->db->fetchAll("SELECT * FROM " . $this->db->table("departments"));
        
        $this->view('documents/edit', [
            'page_title' => 'Edit Document',
            'breadcrumb_module' => 'QMS',
            'breadcrumb_page' => 'Edit Document',
            'document' => $document,
            'categories' => $categories,
            'departments' => $departments
        ]);
    }

    public function viewDocument($id = null): void {
        $this->requireCan('read');
        $document = $this->db->fetchOne(
            "SELECT d.*, dc.name as category_name, dept.name as department_name,
                    CONCAT(p.first_name, ' ', p.last_name) as prepared_by_name,
                    CONCAT(r.first_name, ' ', r.last_name) as reviewed_by_name,
                    CONCAT(a.first_name, ' ', a.last_name) as approved_by_name
             FROM " . $this->db->table("documents") . " d
             LEFT JOIN " . $this->db->table("doc_categories") . " dc ON d.category_id = dc.id
             LEFT JOIN " . $this->db->table("departments") . " dept ON d.department_id = dept.id
             LEFT JOIN " . $this->db->table("users") . " p ON d.prepared_by = p.id
             LEFT JOIN " . $this->db->table("users") . " r ON d.reviewed_by = r.id
             LEFT JOIN " . $this->db->table("users") . " a ON d.approved_by = a.id
             WHERE d.id = ?",
            [$id]
        );
        
        if (!$document) { $this->flash('error', 'Document not found.'); $this->redirect('/qms/documents'); }
        
        $this->view('documents/view', [
            'page_title' => 'View Document',
            'breadcrumb_module' => 'QMS',
            'breadcrumb_page' => $document['doc_code'],
            'document' => $document
        ]);
    }

    // ==================== NCR ====================

    public function ncr(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        $severity = input('severity');
        
        $where = "WHERE 1=1";
        $params = [];
        
        if ($status) { $where .= " AND n.status = ?"; $params[] = $status; }
        if ($severity) { $where .= " AND n.severity = ?"; $params[] = $severity; }
        
        $ncrs = $this->db->fetchAll(
            "SELECT n.*, CONCAT(u.first_name, ' ', u.last_name) as reported_by_name, d.name as department_name
             FROM " . $this->db->table("ncr") . " n
             LEFT JOIN " . $this->db->table("users") . " u ON n.reported_by = u.id
             LEFT JOIN " . $this->db->table("departments") . " d ON n.department_id = d.id
             {$where} ORDER BY n.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE])
        );
        
        $total = (int)$this->db->fetchValue(
            "SELECT COUNT(*) FROM " . $this->db->table("ncr") . " n {$where}",
            $params
        );
        
        $this->view('ncr/list', [
            'page_title' => 'Non-Conformance Reports',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'NCR',
            'ncrs' => $ncrs,
            'pagination' => paginate($total, $page),
            'filters' => compact('status', 'severity')
        ]);
    }

    public function createNCR(): void {
        $this->requireCan('create');
        
        if (is_post()) {
            if (!validate_csrf()) { $this->json(false, 'Invalid token'); }
            
            $ncrNo = generate_ncr_no();
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("ncr") . " 
                (ncr_no, ncr_date, source, project_id, department_id, reported_by, reported_date, 
                 description, severity, category, immediate_action, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'open', NOW())",
                [
                    $ncrNo,
                    input('ncr_date'),
                    input('source'),
                    input('project_id') ?: null,
                    input('department_id') ?: null,
                    current_user_id(),
                    date('Y-m-d'),
                    input('description'),
                    input('severity'),
                    input('category'),
                    input('immediate_action')
                ]
            );
            
            if ($id) {
                $this->log('NCR_CREATED', "NCR {$ncrNo} created");
                $this->flash('success', "NCR {$ncrNo} created successfully.");
                $this->redirect('/qms/ncr');
            }
            $this->flash('error', 'Failed to create NCR.');
        }
        
        $departments = $this->db->fetchAll("SELECT * FROM " . $this->db->table("departments"));
        $projects = $this->db->fetchAll("SELECT id, project_code, project_name FROM " . $this->db->table("projects") . " WHERE status = 'active'");
        
        $this->view('ncr/create', [
            'page_title' => 'Create NCR',
            'breadcrumb_module' => 'QMS',
            'breadcrumb_page' => 'Create NCR',
            'departments' => $departments,
            'projects' => $projects
        ]);
    }

    public function viewNCR($id = null): void {
        $this->requireCan('read');
        $ncr = $this->db->fetchOne(
            "SELECT n.*, CONCAT(u.first_name, ' ', u.last_name) as reported_by_name,
                    d.name as department_name, p.project_name
             FROM " . $this->db->table("ncr") . " n
             LEFT JOIN " . $this->db->table("users") . " u ON n.reported_by = u.id
             LEFT JOIN " . $this->db->table("departments") . " d ON n.department_id = d.id
             LEFT JOIN " . $this->db->table("projects") . " p ON n.project_id = p.id
             WHERE n.id = ?",
            [$id]
        );
        
        if (!$ncr) { $this->flash('error', 'NCR not found.'); $this->redirect('/qms/ncr'); }
        
        $this->view('ncr/view', [
            'page_title' => 'NCR ' . $ncr['ncr_no'],
            'breadcrumb_module' => 'QMS',
            'breadcrumb_page' => $ncr['ncr_no'],
            'ncr' => $ncr
        ]);
    }

    // ==================== CAPA ====================

    public function capa(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        
        $where = "WHERE 1=1";
        $params = [];
        if ($status) { $where .= " AND c.status = ?"; $params[] = $status; }
        
        $capas = $this->db->fetchAll(
            "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as responsible_name, d.name as department_name
             FROM " . $this->db->table("capa") . " c
             LEFT JOIN " . $this->db->table("users") . " u ON c.responsible_person = u.id
             LEFT JOIN " . $this->db->table("departments") . " d ON c.department_id = d.id
             {$where} ORDER BY c.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE])
        );
        
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("capa") . " c {$where}", $params);
        
        $this->view('capa/list', [
            'page_title' => 'CAPA',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'CAPA',
            'capas' => $capas,
            'pagination' => paginate($total, $page),
            'filters' => compact('status')
        ]);
    }

    public function createCAPA(): void {
        $this->requireCan('create');
        
        if (is_post()) {
            if (!validate_csrf()) { $this->json(false, 'Invalid token'); }
            
            $capaNo = generate_capa_no();
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("capa") . " 
                (capa_no, source_type, source_id, description, root_cause_analysis, root_cause_method,
                 corrective_action, preventive_action, responsible_person, department_id, target_date,
                 status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'open', ?, NOW())",
                [
                    $capaNo,
                    input('source_type'),
                    input('source_id') ?: null,
                    input('description'),
                    input('root_cause_analysis'),
                    input('root_cause_method') ?: '5_why',
                    input('corrective_action'),
                    input('preventive_action'),
                    input('responsible_person'),
                    input('department_id'),
                    input('target_date'),
                    current_user_id()
                ]
            );
            
            if ($id) {
                $this->log('CAPA_CREATED', "CAPA {$capaNo} created");
                $this->flash('success', "CAPA {$capaNo} created successfully.");
                $this->redirect('/qms/capa');
            }
            $this->flash('error', 'Failed to create CAPA.');
        }
        
        $users = $this->db->fetchAll("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM " . $this->db->table("users") . " WHERE status = 'active'");
        $departments = $this->db->fetchAll("SELECT * FROM " . $this->db->table("departments"));
        
        $this->view('capa/create', [
            'page_title' => 'Create CAPA',
            'breadcrumb_module' => 'QMS',
            'breadcrumb_page' => 'Create CAPA',
            'users' => $users,
            'departments' => $departments
        ]);
    }

    // ==================== AUDITS ====================

    public function audits(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        
        $where = "WHERE 1=1";
        $params = [];
        if ($status) { $where .= " AND a.status = ?"; $params[] = $status; }
        
        $audits = $this->db->fetchAll(
            "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as auditor_name, d.name as department_name
             FROM " . $this->db->table("audits") . " a
             LEFT JOIN " . $this->db->table("users") . " u ON a.auditor_id = u.id
             LEFT JOIN " . $this->db->table("departments") . " d ON a.department_id = d.id
             {$where} ORDER BY a.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE])
        );
        
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("audits") . " a {$where}", $params);
        
        $this->view('audits/list', [
            'page_title' => 'Internal Audits',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Audits',
            'audits' => $audits,
            'pagination' => paginate($total, $page)
        ]);
    }

    public function createAudit(): void {
        $this->requireCan('create');
        
        if (is_post()) {
            if (!validate_csrf()) { $this->json(false, 'Invalid token'); }
            
            $auditNo = generate_code('AUD');
            $id = $this->db->insert(
                "INSERT INTO " . $this->db->table("audits") . " 
                (audit_no, audit_type, title, department_id, auditor_id, scope, criteria, 
                 planned_start_date, planned_end_date, checklist, status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'planned', ?, NOW())",
                [
                    $auditNo, input('audit_type'), input('title'), input('department_id'),
                    input('auditor_id'), input('scope'), input('criteria'),
                    input('planned_start_date'), input('planned_end_date'),
                    json_encode($_POST['checklist'] ?? []), current_user_id()
                ]
            );
            
            if ($id) {
                $this->log('AUDIT_CREATED', "Audit {$auditNo} created");
                $this->flash('success', "Audit {$auditNo} scheduled successfully.");
                $this->redirect('/qms/audits');
            }
            $this->flash('error', 'Failed to schedule audit.');
        }
        
        $users = $this->db->fetchAll("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM " . $this->db->table("users") . " WHERE status = 'active'");
        $departments = $this->db->fetchAll("SELECT * FROM " . $this->db->table("departments"));
        
        $this->view('audits/create', [
            'page_title' => 'Schedule Audit',
            'breadcrumb_module' => 'QMS',
            'breadcrumb_page' => 'Schedule Audit',
            'users' => $users,
            'departments' => $departments
        ]);
    }

    // ==================== CALIBRATION ====================

    public function calibration(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        
        $where = "WHERE 1=1";
        $params = [];
        if ($status) { $where .= " AND c.status = ?"; $params[] = $status; }
        
        $calibrations = $this->db->fetchAll(
            "SELECT c.*, d.name as department_name
             FROM " . $this->db->table("calibrations") . " c
             LEFT JOIN " . $this->db->table("departments") . " d ON c.department_id = d.id
             {$where} ORDER BY c.next_calibration_date ASC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE])
        );
        
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("calibrations") . " c {$where}", $params);
        
        $this->view('calibration/list', [
            'page_title' => 'Calibration Tracking',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Calibration',
            'calibrations' => $calibrations,
            'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== TRAINING ====================

    public function training(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $trainings = $this->db->fetchAll(
            "SELECT t.*, CONCAT(u.first_name, ' ', u.last_name) as trainer_name, d.name as department_name
             FROM " . $this->db->table("training") . " t
             LEFT JOIN " . $this->db->table("users") . " u ON t.trainer_id = u.id
             LEFT JOIN " . $this->db->table("departments") . " d ON t.department_id = d.id
             ORDER BY t.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE]
        );
        
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("training"));
        
        $this->view('training/list', [
            'page_title' => 'Training Records',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Training',
            'trainings' => $trainings,
            'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== COMPLAINTS ====================

    public function complaints(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        
        $where = "WHERE 1=1";
        $params = [];
        if ($status) { $where .= " AND c.status = ?"; $params[] = $status; }
        
        $complaints = $this->db->fetchAll(
            "SELECT c.*, cl.company_name as client_name, CONCAT(u.first_name, ' ', u.last_name) as received_by_name
             FROM " . $this->db->table("complaints") . " c
             LEFT JOIN " . $this->db->table("clients") . " cl ON c.client_id = cl.id
             LEFT JOIN " . $this->db->table("users") . " u ON c.received_by = u.id
             {$where} ORDER BY c.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE])
        );
        
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("complaints") . " c {$where}", $params);
        
        $this->view('complaints/list', [
            'page_title' => 'Customer Complaints',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Complaints',
            'complaints' => $complaints,
            'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== RISK ASSESSMENT ====================

    public function risks(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $risks = $this->db->fetchAll(
            "SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as risk_owner_name, d.name as department_name
             FROM " . $this->db->table("risks") . " r
             LEFT JOIN " . $this->db->table("users") . " u ON r.risk_owner = u.id
             LEFT JOIN " . $this->db->table("departments") . " d ON r.department_id = d.id
             ORDER BY r.risk_score DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE]
        );
        
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("risks"));
        
        $this->view('risks/list', [
            'page_title' => 'Risk Assessment',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Risk Assessment',
            'risks' => $risks,
            'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== MANAGEMENT REVIEWS ====================

    public function reviews(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $reviews = $this->db->fetchAll(
            "SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as chaired_by_name
             FROM " . $this->db->table("management_reviews") . " r
             LEFT JOIN " . $this->db->table("users") . " u ON r.chaired_by = u.id
             ORDER BY r.review_date DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE]
        );
        
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("management_reviews"));
        
        $this->view('reviews/list', [
            'page_title' => 'Management Reviews',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'Management Reviews',
            'reviews' => $reviews,
            'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== KPI & OBJECTIVES ====================

    public function kpi(): void {
        $this->requireCan('read');
        
        $page = (int)($_GET['page'] ?? 1);
        $year = input('year', date('Y'));
        
        $kpis = $this->db->fetchAll(
            "SELECT k.*, CONCAT(u.first_name, ' ', u.last_name) as responsible_name, d.name as department_name
             FROM " . $this->db->table("quality_objectives") . " k
             LEFT JOIN " . $this->db->table("users") . " u ON k.responsible_person = u.id
             LEFT JOIN " . $this->db->table("departments") . " d ON k.department_id = d.id
             WHERE k.year = ? ORDER BY k.id DESC LIMIT ? OFFSET ?",
            [$year, DEFAULT_PER_PAGE, ($page - 1) * DEFAULT_PER_PAGE]
        );
        
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("quality_objectives") . " WHERE year = ?", [$year]);
        
        $this->view('kpi/list', [
            'page_title' => 'KPI & Quality Objectives',
            'breadcrumb_module' => 'QMS / ISO 9001',
            'breadcrumb_page' => 'KPI & Objectives',
            'kpis' => $kpis,
            'pagination' => paginate($total, $page),
            'year' => $year
        ]);
    }
}
