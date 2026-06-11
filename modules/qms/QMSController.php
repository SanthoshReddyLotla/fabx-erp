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
        // NEW CODE
$sql = "SELECT d.*, dc.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as prepared_by_name 
        FROM " . $this->db->table("documents") . " d
        LEFT JOIN " . $this->db->table("doc_categories") . " dc ON d.category_id = dc.id
        LEFT JOIN " . $this->db->table("users") . " u ON d.prepared_by = u.id
        WHERE 1=1"; // Changed d.is_deleted = 0 to 1=1 to allow safe appending of further filters

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

    public function printNCR($id = null): void {
        $this->requireCan('read');
        $id = (int)($id ?: input('id'));

        $ncr = $this->db->fetchOne(
            "SELECT n.*, CONCAT(u.first_name, ' ', u.last_name) as reported_by_name,
                    CONCAT(r.first_name, ' ', r.last_name) as responsible_name,
                    CONCAT(v.first_name, ' ', v.last_name) as verified_by_name,
                    d.name as department_name, p.project_name
             FROM " . $this->db->table("ncr") . " n
             LEFT JOIN " . $this->db->table("users") . " u ON n.reported_by = u.id
             LEFT JOIN " . $this->db->table("users") . " r ON n.responsible_person = r.id
             LEFT JOIN " . $this->db->table("users") . " v ON n.verified_by = v.id
             LEFT JOIN " . $this->db->table("departments") . " d ON n.department_id = d.id
             LEFT JOIN " . $this->db->table("projects") . " p ON n.project_id = p.id
             WHERE n.id = ?",
            [$id]
        );
        if (!$ncr) {
            $this->flash('error', 'NCR not found.');
            $this->redirect('/qms/ncr');
        }

        $this->printView('ncr/print', 'NCR ' . $ncr['ncr_no'], [
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

    public function updateDocumentStatus($id = null): void {
        $this->requireCan('update');
        if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/documents'); }
        $doc = $this->db->fetchOne("SELECT * FROM ".$this->db->table("documents")." WHERE id = ?", [$id]);
        if (!$doc) { $this->flash('error','Document not found.'); $this->redirect('/qms/documents'); }
        $transitions = ['draft'=>'under_review','under_review'=>'approved','approved'=>'obsolete'];
        $newStatus = input('status') ?: ($transitions[$doc['status']] ?? null);
        if (!$newStatus) { $this->flash('error','Invalid status transition.'); $this->redirect('/qms/documents/view/'.$id); }
        $history = json_decode($doc['change_history'] ?? '[]', true) ?: [];
        $history[] = ['date'=>date('Y-m-d H:i:s'),'user_id'=>current_user_id(),'status'=>$newStatus,'revision'=>$doc['revision_no'],'notes'=>input('change_notes','')];
        $newRevision = ($newStatus === 'approved') ? $doc['revision_no'] + 1 : $doc['revision_no'];
        $this->db->execute("UPDATE ".$this->db->table("documents")." SET status=?,revision_no=?,change_history=?,approved_by=?,updated_at=NOW() WHERE id=?",
            [$newStatus,$newRevision,json_encode($history),current_user_id(),$id]);
        $this->log('DOCUMENT_STATUS_CHANGED',"Document {$doc['doc_code']} status changed to {$newStatus}");
        $this->flash('success',"Document status updated to ".ucfirst(str_replace('_',' ',$newStatus)).".");
        $this->redirect('/qms/documents/view/'.$id);
    }

    public function updateNCR($id = null): void {
        $this->requireCan('update');
        if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/ncr'); }
        $ncr = $this->db->fetchOne("SELECT * FROM ".$this->db->table("ncr")." WHERE id = ?", [$id]);
        if (!$ncr) { $this->flash('error','NCR not found.'); $this->redirect('/qms/ncr'); }
        $this->db->execute("UPDATE ".$this->db->table("ncr")." SET root_cause=?,corrective_action=?,preventive_action=?,responsible_person=?,target_date=?,status=?,updated_at=NOW() WHERE id=?",
            [input('root_cause'),input('corrective_action'),input('preventive_action'),input('responsible_person')?:null,input('target_date')?:null,input('status')?:$ncr['status'],$id]);
        if (input('create_capa') === '1') {
            $capaNo = generate_capa_no();
            $this->db->insert("INSERT INTO ".$this->db->table("capa")." (capa_no,source_type,source_id,description,root_cause_analysis,corrective_action,responsible_person,department_id,target_date,status,created_by,created_at) VALUES (?,?,?,?,?,?,?,?,?,'open',?,NOW())",
                [$capaNo,'ncr',$id,$ncr['description'],input('root_cause'),input('corrective_action'),input('responsible_person')?:null,$ncr['department_id'],input('target_date')?:null,current_user_id()]);
            $this->flash('success',"NCR updated and CAPA {$capaNo} created.");
        } else {
            $this->flash('success','NCR updated successfully.');
        }
        $this->redirect('/qms/ncr/view/'.$id);
    }

    public function viewCAPA($id = null): void {
        $this->requireCan('read');
        $capa = $this->db->fetchOne("SELECT c.*,CONCAT(u.first_name,' ',u.last_name) as responsible_name,d.name as department_name,CONCAT(v.first_name,' ',v.last_name) as verified_by_name FROM ".$this->db->table("capa")." c LEFT JOIN ".$this->db->table("users")." u ON c.responsible_person=u.id LEFT JOIN ".$this->db->table("departments")." d ON c.department_id=d.id LEFT JOIN ".$this->db->table("users")." v ON c.effectiveness_verified_by=v.id WHERE c.id=?",[$id]);
        if (!$capa) { $this->flash('error','CAPA not found.'); $this->redirect('/qms/capa'); }
        $users = $this->db->fetchAll("SELECT id,CONCAT(first_name,' ',last_name) as name FROM ".$this->db->table("users")." WHERE status='active'");
        $this->view('capa/view',['page_title'=>'CAPA '.$capa['capa_no'],'breadcrumb_module'=>'QMS','breadcrumb_page'=>$capa['capa_no'],'capa'=>$capa,'users'=>$users]);
    }

    public function updateCAPAEffectiveness($id = null): void {
        $this->requireCan('update');
        if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/capa/view/'.$id); }
        $this->db->execute("UPDATE ".$this->db->table("capa")." SET effectiveness_check=?,effectiveness_result=?,effectiveness_verified_by=?,effectiveness_date=?,status='verified',updated_at=NOW() WHERE id=?",
            [input('effectiveness_check'),input('effectiveness_result'),current_user_id(),date('Y-m-d'),$id]);
        $this->log('CAPA_EFFECTIVENESS_UPDATED',"CAPA #{$id} effectiveness verified");
        $this->flash('success','Effectiveness verification saved.');
        $this->redirect('/qms/capa/view/'.$id);
    }

    public function viewAudit($id = null): void {
        $this->requireCan('read');
        $audit = $this->db->fetchOne("SELECT a.*,CONCAT(u.first_name,' ',u.last_name) as auditor_name,d.name as department_name FROM ".$this->db->table("audits")." a LEFT JOIN ".$this->db->table("users")." u ON a.auditor_id=u.id LEFT JOIN ".$this->db->table("departments")." d ON a.department_id=d.id WHERE a.id=?",[$id]);
        if (!$audit) { $this->flash('error','Audit not found.'); $this->redirect('/qms/audits'); }
        $findings = $this->db->fetchAll("SELECT * FROM ".$this->db->table("audit_findings")." WHERE audit_id=? ORDER BY id DESC",[$id]);
        $this->view('audits/view',['page_title'=>'Audit '.$audit['audit_no'],'breadcrumb_module'=>'QMS','breadcrumb_page'=>$audit['audit_no'],'audit'=>$audit,'findings'=>$findings]);
    }

    public function addAuditFinding($id = null): void {
        $this->requireCan('create');
        if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/audits/view/'.$id); }
        $this->db->insert("INSERT INTO ".$this->db->table("audit_findings")." (audit_id,clause_reference,finding_type,description,evidence,corrective_action_required,status) VALUES (?,?,?,?,?,?,?)",
            [$id,input('clause_reference'),input('finding_type'),input('description'),input('evidence'),input('corrective_action_required')?1:0,'open']);
        $this->log('AUDIT_FINDING_ADDED',"Finding added to audit #{$id}");
        $this->flash('success','Finding recorded.');
        $this->redirect('/qms/audits/view/'.$id);
    }

    public function createCalibration(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/calibration/create'); }
            $id = $this->db->insert("INSERT INTO ".$this->db->table("calibrations")." (equipment_id,equipment_name,manufacturer,model_no,serial_no,location,department_id,range_value,accuracy,frequency,last_calibration_date,next_calibration_date,calibration_certificate_no,calibrated_by,status,remarks,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,'active',?,NOW())",
                [input('equipment_id'),input('equipment_name'),input('manufacturer'),input('model_no'),input('serial_no'),input('location'),input('department_id')?:null,input('range_value'),input('accuracy'),input('frequency'),input('last_calibration_date')?:null,input('next_calibration_date')?:null,input('calibration_certificate_no'),input('calibrated_by'),input('remarks')]);
            if ($id) { $this->log('CALIBRATION_CREATED',"Equipment {".input('equipment_id')."} calibration added"); $this->flash('success','Calibration record created.'); $this->redirect('/qms/calibration'); }
            $this->flash('error','Failed to create record.');
        }
        $departments = $this->db->fetchAll("SELECT * FROM ".$this->db->table("departments")." WHERE status='active'");
        $this->view('calibration/create',['page_title'=>'Add Calibration Record','breadcrumb_module'=>'QMS','breadcrumb_page'=>'New Calibration','departments'=>$departments]);
    }

    public function viewCalibration($id = null): void {
        $this->requireCan('read');
        $cal = $this->db->fetchOne("SELECT c.*,d.name as department_name FROM ".$this->db->table("calibrations")." c LEFT JOIN ".$this->db->table("departments")." d ON c.department_id=d.id WHERE c.id=?",[$id]);
        if (!$cal) { $this->flash('error','Record not found.'); $this->redirect('/qms/calibration'); }
        $this->view('calibration/view',['page_title'=>'Calibration: '.e($cal['equipment_name']),'breadcrumb_module'=>'QMS','breadcrumb_page'=>$cal['equipment_id'],'calibration'=>$cal]);
    }

    public function createTraining(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/training/create'); }
            $code = input('training_code') ?: generate_code('TRN');
            $id = $this->db->insert("INSERT INTO ".$this->db->table("training")." (training_code,title,description,training_type,department_id,trainer_id,external_trainer,training_mode,start_date,end_date,duration_hours,venue,status,created_by,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,'planned',?,NOW())",
                [$code,input('title'),input('description'),input('training_type'),input('department_id')?:null,input('trainer_id')?:null,input('external_trainer'),input('training_mode'),input('start_date')?:null,input('end_date')?:null,input('duration_hours')?:null,input('venue'),current_user_id()]);
            if ($id) { $this->log('TRAINING_CREATED',"Training {$code} created"); $this->flash('success','Training scheduled.'); $this->redirect('/qms/training'); }
            $this->flash('error','Failed to create training.');
        }
        $users = $this->db->fetchAll("SELECT id,CONCAT(first_name,' ',last_name) as name FROM ".$this->db->table("users")." WHERE status='active'");
        $departments = $this->db->fetchAll("SELECT * FROM ".$this->db->table("departments")." WHERE status='active'");
        $this->view('training/create',['page_title'=>'Schedule Training','breadcrumb_module'=>'QMS','breadcrumb_page'=>'New Training','users'=>$users,'departments'=>$departments]);
    }

    public function viewTraining($id = null): void {
        $this->requireCan('read');
        $training = $this->db->fetchOne("SELECT t.*,CONCAT(u.first_name,' ',u.last_name) as trainer_name,d.name as department_name FROM ".$this->db->table("training")." t LEFT JOIN ".$this->db->table("users")." u ON t.trainer_id=u.id LEFT JOIN ".$this->db->table("departments")." d ON t.department_id=d.id WHERE t.id=?",[$id]);
        if (!$training) { $this->flash('error','Training not found.'); $this->redirect('/qms/training'); }
        $participants = $this->db->fetchAll("SELECT tp.*,CONCAT(u.first_name,' ',u.last_name) as employee_name,u.employee_code FROM ".$this->db->table("training_participants")." tp LEFT JOIN ".$this->db->table("users")." u ON tp.employee_id=u.id WHERE tp.training_id=?",[$id]);
        $allEmployees = $this->db->fetchAll("SELECT id,CONCAT(first_name,' ',last_name) as name,employee_code FROM ".$this->db->table("users")." WHERE status='active' ORDER BY first_name");
        $this->view('training/view',['page_title'=>'Training: '.$training['training_code'],'breadcrumb_module'=>'QMS','breadcrumb_page'=>$training['training_code'],'training'=>$training,'participants'=>$participants,'all_employees'=>$allEmployees]);
    }

    public function updateParticipants($id = null): void {
        $this->requireCan('update');
        if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/training/view/'.$id); }
        $rows = $_POST['participants'] ?? [];
        foreach ($rows as $empId => $row) {
            $empId = (int)$empId;
            $exists = $this->db->fetchValue("SELECT id FROM ".$this->db->table("training_participants")." WHERE training_id=? AND employee_id=?",[$id,$empId]);
            if ($exists) {
                $this->db->execute("UPDATE ".$this->db->table("training_participants")." SET attendance=?,score=?,result=?,certificate_issued=?,certificate_no=? WHERE training_id=? AND employee_id=?",
                    [$row['attendance']??'present',$row['score']?:null,$row['result']??'pending',$row['certificate_issued']??0,$row['certificate_no']??null,$id,$empId]);
            } else {
                $this->db->insert("INSERT INTO ".$this->db->table("training_participants")." (training_id,employee_id,attendance,score,result,certificate_issued,certificate_no) VALUES (?,?,?,?,?,?,?)",
                    [$id,$empId,$row['attendance']??'present',$row['score']?:null,$row['result']??'pending',$row['certificate_issued']??0,$row['certificate_no']??null]);
            }
        }
        $this->log('TRAINING_PARTICIPANTS_UPDATED',"Training #{$id} roster updated");
        $this->flash('success','Attendance and scores saved.');
        $this->redirect('/qms/training/view/'.$id);
    }

    public function competency(): void {
        $this->requireCan('read');
        $deptFilter = input('department_id');
        $where = "WHERE 1=1"; $params = [];
        if ($deptFilter) { $where .= " AND u.department_id=?"; $params[] = $deptFilter; }
        $matrix = $this->db->fetchAll("SELECT cm.*,CONCAT(u.first_name,' ',u.last_name) as employee_name,u.employee_code,d.name as department_name FROM ".$this->db->table("competency_matrix")." cm LEFT JOIN ".$this->db->table("users")." u ON cm.employee_id=u.id LEFT JOIN ".$this->db->table("departments")." d ON u.department_id=d.id {$where} ORDER BY u.first_name,cm.skill_name",$params);
        $skills = array_unique(array_column($matrix,'skill_name'));
        $employees = []; foreach ($matrix as $row) { $employees[$row['employee_id']]['name']=$row['employee_name']; $employees[$row['employee_id']]['code']=$row['employee_code']; $employees[$row['employee_id']]['dept']=$row['department_name']; $employees[$row['employee_id']]['skills'][$row['skill_name']]=$row; }
        $departments = $this->db->fetchAll("SELECT * FROM ".$this->db->table("departments")." WHERE status='active'");
        $this->view('training/competency',['page_title'=>'Competency Matrix','breadcrumb_module'=>'QMS','breadcrumb_page'=>'Competency Matrix','employees'=>$employees,'skills'=>$skills,'departments'=>$departments,'dept_filter'=>$deptFilter]);
    }

    public function createComplaint(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/complaints/create'); }
            $no = generate_code('CMP');
            $id = $this->db->insert("INSERT INTO ".$this->db->table("complaints")." (complaint_no,complaint_date,client_id,project_id,received_by,source,description,severity,immediate_action,status,created_at) VALUES (?,?,?,?,?,?,?,?,?,'open',NOW())",
                [$no,input('complaint_date'),input('client_id'),input('project_id')?:null,current_user_id(),input('source'),input('description'),input('severity'),input('immediate_action')]);
            if ($id) { $this->log('COMPLAINT_CREATED',"Complaint {$no} created"); $this->flash('success',"Complaint {$no} logged."); $this->redirect('/qms/complaints'); }
            $this->flash('error','Failed to log complaint.');
        }
        $clients = $this->db->fetchAll("SELECT id,company_name FROM ".$this->db->table("clients")." ORDER BY company_name");
        $projects = $this->db->fetchAll("SELECT id,project_code,project_name FROM ".$this->db->table("projects")." WHERE status='active'");
        $this->view('complaints/create',['page_title'=>'Log Complaint','breadcrumb_module'=>'QMS','breadcrumb_page'=>'New Complaint','clients'=>$clients,'projects'=>$projects]);
    }

    public function viewComplaint($id = null): void {
        $this->requireCan('read');
        $complaint = $this->db->fetchOne("SELECT c.*,cl.company_name as client_name,CONCAT(u.first_name,' ',u.last_name) as received_by_name,p.project_name FROM ".$this->db->table("complaints")." c LEFT JOIN ".$this->db->table("clients")." cl ON c.client_id=cl.id LEFT JOIN ".$this->db->table("users")." u ON c.received_by=u.id LEFT JOIN ".$this->db->table("projects")." p ON c.project_id=p.id WHERE c.id=?",[$id]);
        if (!$complaint) { $this->flash('error','Complaint not found.'); $this->redirect('/qms/complaints'); }
        $this->view('complaints/view',['page_title'=>'Complaint '.$complaint['complaint_no'],'breadcrumb_module'=>'QMS','breadcrumb_page'=>$complaint['complaint_no'],'complaint'=>$complaint]);
    }

    public function updateComplaintStatus($id = null): void {
        $this->requireCan('update');
        if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/complaints/view/'.$id); }
        $fields = "status=?,investigation_findings=?,root_cause=?,corrective_action=?,customer_satisfaction=?,updated_at=NOW()";
        $params = [input('status'),input('investigation_findings'),input('root_cause'),input('corrective_action'),input('customer_satisfaction'),$id];
        if (input('status')==='closed') { $fields = "status=?,investigation_findings=?,root_cause=?,corrective_action=?,customer_satisfaction=?,closure_date=CURDATE(),closed_by=?,updated_at=NOW()"; $params = [input('status'),input('investigation_findings'),input('root_cause'),input('corrective_action'),input('customer_satisfaction'),current_user_id(),$id]; }
        $this->db->execute("UPDATE ".$this->db->table("complaints")." SET {$fields} WHERE id=?",$params);
        $this->flash('success','Complaint updated.');
        $this->redirect('/qms/complaints/view/'.$id);
    }

    public function createRisk(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/risks/create'); }
            $riskNo = generate_code('RSK');
            $id = $this->db->insert("INSERT INTO ".$this->db->table("risks")." (risk_no,description,category,department_id,probability,impact,mitigation_plan,contingency_plan,risk_owner,review_date,status,created_by,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,'open',?,NOW())",
                [$riskNo,input('description'),input('category'),input('department_id')?:null,input('probability'),input('impact'),input('mitigation_plan'),input('contingency_plan'),input('risk_owner')?:null,input('review_date')?:null,current_user_id()]);
            if ($id) { $this->log('RISK_CREATED',"Risk {$riskNo} created"); $this->flash('success',"Risk {$riskNo} registered."); $this->redirect('/qms/risks'); }
            $this->flash('error','Failed to register risk.');
        }
        $departments = $this->db->fetchAll("SELECT * FROM ".$this->db->table("departments")." WHERE status='active'");
        $users = $this->db->fetchAll("SELECT id,CONCAT(first_name,' ',last_name) as name FROM ".$this->db->table("users")." WHERE status='active'");
        $this->view('risks/create',['page_title'=>'Register Risk','breadcrumb_module'=>'QMS','breadcrumb_page'=>'New Risk','departments'=>$departments,'users'=>$users]);
    }

    public function viewRisk($id = null): void {
        $this->requireCan('read');
        $risk = $this->db->fetchOne("SELECT r.*,CONCAT(u.first_name,' ',u.last_name) as risk_owner_name,d.name as department_name FROM ".$this->db->table("risks")." r LEFT JOIN ".$this->db->table("users")." u ON r.risk_owner=u.id LEFT JOIN ".$this->db->table("departments")." d ON r.department_id=d.id WHERE r.id=?",[$id]);
        if (!$risk) { $this->flash('error','Risk not found.'); $this->redirect('/qms/risks'); }
        $this->view('risks/view',['page_title'=>'Risk '.$risk['risk_no'],'breadcrumb_module'=>'QMS','breadcrumb_page'=>$risk['risk_no'],'risk'=>$risk]);
    }

    public function createReview(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/reviews/create'); }
            $reviewNo = generate_code('MRM');
            $decisions = array_values(array_filter($_POST['decisions'] ?? []));
            $actionItems = [];
            $aiDesc = $_POST['ai_description'] ?? [];
            $aiResp = $_POST['ai_responsible'] ?? [];
            $aiDue  = $_POST['ai_due_date'] ?? [];
            foreach ($aiDesc as $k => $desc) { if (trim($desc)) $actionItems[] = ['description'=>$desc,'responsible'=>$aiResp[$k]??'','due_date'=>$aiDue[$k]??'']; }
            $attendees = $_POST['attendees'] ?? [];
            $id = $this->db->insert("INSERT INTO ".$this->db->table("management_reviews")." (review_no,review_date,chaired_by,attendees,agenda,minutes,decisions,action_items,next_review_date,status,created_by,created_at) VALUES (?,?,?,?,?,?,?,?,?,'completed',?,NOW())",
                [$reviewNo,input('review_date'),input('chaired_by'),json_encode($attendees),input('agenda'),input('minutes'),json_encode($decisions),json_encode($actionItems),input('next_review_date')?:null,current_user_id()]);
            if ($id) { $this->log('REVIEW_CREATED',"Management Review {$reviewNo} recorded"); $this->flash('success',"Review {$reviewNo} saved."); $this->redirect('/qms/reviews'); }
            $this->flash('error','Failed to save review.');
        }
        $users = $this->db->fetchAll("SELECT id,CONCAT(first_name,' ',last_name) as name FROM ".$this->db->table("users")." WHERE status='active'");
        $this->view('reviews/create',['page_title'=>'Management Review','breadcrumb_module'=>'QMS','breadcrumb_page'=>'New Review','users'=>$users]);
    }

    public function viewReview($id = null): void {
        $this->requireCan('read');
        $review = $this->db->fetchOne("SELECT r.*,CONCAT(u.first_name,' ',u.last_name) as chaired_by_name FROM ".$this->db->table("management_reviews")." r LEFT JOIN ".$this->db->table("users")." u ON r.chaired_by=u.id WHERE r.id=?",[$id]);
        if (!$review) { $this->flash('error','Review not found.'); $this->redirect('/qms/reviews'); }
        $this->view('reviews/view',['page_title'=>'Review '.$review['review_no'],'breadcrumb_module'=>'QMS','breadcrumb_page'=>$review['review_no'],'review'=>$review]);
    }

    public function createKPI(): void {
        $this->requireCan('create');
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error','Invalid token.'); $this->redirect('/qms/kpi/create'); }
            $kpiCode = input('kpi_code') ?: generate_code('KPI');
            $target = (float)input('target_value',0);
            $actual = (float)input('actual_value',0);
            $pct = $target > 0 ? round(($actual/$target)*100,2) : 0;
            $status = $pct >= 90 ? 'on_track' : ($pct >= 60 ? 'at_risk' : 'off_track');
            $id = $this->db->insert("INSERT INTO ".$this->db->table("quality_objectives")." (objective,kpi_name,kpi_code,target_value,actual_value,unit,frequency,department_id,responsible_person,year,month,quarter,achievement_percentage,status,remarks,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())",
                [input('objective'),input('kpi_name'),$kpiCode,$target,$actual,input('unit'),input('frequency'),input('department_id')?:null,input('responsible_person')?:null,(int)input('year',date('Y')),(int)input('month',0)?:null,(int)input('quarter',0)?:null,$pct,$status,input('remarks')]);
            if ($id) { $this->log('KPI_CREATED',"KPI {$kpiCode} created"); $this->flash('success','KPI recorded.'); $this->redirect('/qms/kpi'); }
            $this->flash('error','Failed to create KPI.');
        }
        $departments = $this->db->fetchAll("SELECT * FROM ".$this->db->table("departments")." WHERE status='active'");
        $users = $this->db->fetchAll("SELECT id,CONCAT(first_name,' ',last_name) as name FROM ".$this->db->table("users")." WHERE status='active'");
        $this->view('kpi/create',['page_title'=>'Add KPI','breadcrumb_module'=>'QMS','breadcrumb_page'=>'New KPI','departments'=>$departments,'users'=>$users]);
}
}
