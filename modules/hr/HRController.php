<?php
/**
 * FabX ERP - HR Controller
 * Employee management, attendance, leaves, training, appraisals
 */

namespace Modules\Hr;

use Core\Controller;

class HRController extends Controller {
    protected string $module = 'hr';

    public function __construct() {
        parent::__construct();
        require_auth();
    }

    public function employees(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status', 'active');
        
        $users = $this->db->fetchAll(
            "SELECT u.*, r.name as role_name, d.name as department_name
             FROM " . $this->db->table("users") . " u
             LEFT JOIN " . $this->db->table("roles") . " r ON u.role_id = r.id
             LEFT JOIN " . $this->db->table("departments") . " d ON u.department_id = d.id
             WHERE u.is_deleted = 0 AND u.status = ?
             ORDER BY u.created_at DESC LIMIT ? OFFSET ?",
            [$status, DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("users") . " WHERE is_deleted = 0 AND status = ?", [$status]);
        
        $this->view('employees/list', [
            'page_title' => 'Employees', 'breadcrumb_module' => 'HR', 'breadcrumb_page' => 'Employees',
            'employees' => $users, 'pagination' => paginate($total, $page),
            'stats' => [
                'total' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("users") . " WHERE is_deleted = 0 AND status = 'active'") ?? 0,
                'new_this_month' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("users") . " WHERE MONTH(created_at) = MONTH(CURDATE()) AND is_deleted = 0") ?? 0,
            ]
        ]);
    }

    public function attendance(): void {
        $this->requireCan('read');
        $date = input('date', date('Y-m-d'));
        $attendance = $this->db->fetchAll(
            "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as employee_name, u.employee_code
             FROM " . $this->db->table("attendance") . " a
             LEFT JOIN " . $this->db->table("users") . " u ON a.employee_id = u.id
             WHERE a.attendance_date = ? ORDER BY u.first_name",
            [$date]
        );
        $this->view('attendance/index', [
            'page_title' => 'Attendance', 'breadcrumb_module' => 'HR', 'breadcrumb_page' => 'Attendance',
            'attendance' => $attendance, 'date' => $date
        ]);
    }

    public function leaves(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $status = input('status');
        $where = "WHERE 1=1"; $params = [];
        if ($status) { $where .= " AND l.status = ?"; $params[] = $status; }
        
        $leaves = $this->db->fetchAll(
            "SELECT l.*, CONCAT(u.first_name, ' ', u.last_name) as employee_name
             FROM " . $this->db->table("leaves") . " l
             LEFT JOIN " . $this->db->table("users") . " u ON l.employee_id = u.id
             {$where} ORDER BY l.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("leaves") . " l {$where}", $params);
        
        $this->view('leaves/list', [
            'page_title' => 'Leave Management', 'breadcrumb_module' => 'HR', 'breadcrumb_page' => 'Leaves',
            'leaves' => $leaves, 'pagination' => paginate($total, $page)
        ]);
    }

    public function training(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $trainings = $this->db->fetchAll(
            "SELECT t.*, CONCAT(u.first_name, ' ', u.last_name) as trainer_name, d.name as department_name
             FROM " . $this->db->table("training") . " t
             LEFT JOIN " . $this->db->table("users") . " u ON t.trainer_id = u.id
             LEFT JOIN " . $this->db->table("departments") . " d ON t.department_id = d.id
             ORDER BY t.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("training"));
        
        $this->view('training/list', [
            'page_title' => 'Training Records', 'breadcrumb_module' => 'HR', 'breadcrumb_page' => 'Training',
            'trainings' => $trainings, 'pagination' => paginate($total, $page)
        ]);
    }

    public function appraisals(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $appraisals = $this->db->fetchAll(
            "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as employee_name,
                    CONCAT(r.first_name, ' ', r.last_name) as reviewer_name
             FROM " . $this->db->table("appraisals") . " a
             LEFT JOIN " . $this->db->table("users") . " u ON a.employee_id = u.id
             LEFT JOIN " . $this->db->table("users") . " r ON a.reviewer_id = r.id
             ORDER BY a.created_at DESC LIMIT ? OFFSET ?",
            [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE]
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("appraisals"));
        
        $this->view('appraisals/list', [
            'page_title' => 'Performance Appraisals', 'breadcrumb_module' => 'HR', 'breadcrumb_page' => 'Appraisals',
            'appraisals' => $appraisals, 'pagination' => paginate($total, $page)
        ]);
    }
}
