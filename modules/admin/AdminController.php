<?php
/**
 * FabX ERP - Admin Controller
 * User management, roles, departments, settings, logs
 */

namespace Modules\Admin;

use Core\Controller;

class AdminController extends Controller {
    protected string $module = 'admin';

    public function __construct() {
        parent::__construct();
        require_auth();
        if ($_SESSION['user_role'] !== 'Super Admin') {
            set_flash('error', 'Access denied. Super Admin only.');
            redirect('/dashboard');
        }
    }

    // ==================== USERS ====================

    public function users(): void {
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
        
        $this->view('users/list', [
            'page_title' => 'Users', 'breadcrumb_module' => 'Admin', 'breadcrumb_page' => 'Users',
            'users' => $users, 'pagination' => paginate($total, $page)
        ]);
    }

    // ==================== ROLES ====================

    public function roles(): void {
        $roles = $this->db->fetchAll("SELECT * FROM " . $this->db->table("roles") . " ORDER BY id");
        $permissions = ['create', 'read', 'update', 'delete', 'approve', 'reject', 'export', 'print', 'admin'];
        
        $this->view('roles/list', [
            'page_title' => 'Roles & Permissions', 'breadcrumb_module' => 'Admin', 'breadcrumb_page' => 'Roles',
            'roles' => $roles, 'permissions' => $permissions
        ]);
    }

    // ==================== DEPARTMENTS ====================

    public function departments(): void {
        $departments = $this->db->fetchAll(
            "SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as head_name,
                    (SELECT COUNT(*) FROM " . $this->db->table("users") . " WHERE department_id = d.id AND status = 'active') as employee_count
             FROM " . $this->db->table("departments") . " d
             LEFT JOIN " . $this->db->table("users") . " u ON d.head_id = u.id
             ORDER BY d.name"
        );
        $this->view('departments/list', [
            'page_title' => 'Departments', 'breadcrumb_module' => 'Admin', 'breadcrumb_page' => 'Departments',
            'departments' => $departments
        ]);
    }

    // ==================== SETTINGS ====================

    public function settings(): void {
        if (is_post()) {
            if (!validate_csrf()) { $this->flash('error', 'Invalid token'); $this->redirect('/admin/settings'); }
            
            foreach ($_POST['settings'] as $key => $value) {
                $this->db->execute(
                    "INSERT INTO " . $this->db->table("settings") . " (setting_key, setting_value, updated_by, updated_at)
                    VALUES (?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?, updated_at = NOW()",
                    [$key, $value, current_user_id(), $value, current_user_id()]
                );
            }
            $this->flash('success', 'Settings updated successfully.');
            $this->redirect('/admin/settings');
        }
        
        $settings = $this->db->fetchAll("SELECT * FROM " . $this->db->table("settings") . " ORDER BY setting_group, setting_key");
        $grouped = [];
        foreach ($settings as $s) { $grouped[$s['setting_group']][] = $s; }
        
        $this->view('settings', [
            'page_title' => 'System Settings', 'breadcrumb_module' => 'Admin', 'breadcrumb_page' => 'Settings',
            'settings' => $grouped
        ]);
    }

    // ==================== ACTIVITY LOGS ====================

    public function logs(): void {
        $page = (int)($_GET['page'] ?? 1);
        $action = input('action');
        $where = "WHERE 1=1"; $params = [];
        if ($action) { $where .= " AND action = ?"; $params[] = $action; }
        
        $logs = $this->db->fetchAll(
            "SELECT l.*, CONCAT(u.first_name, ' ', u.last_name) as user_name
             FROM " . $this->db->table("activity_logs") . " l
             LEFT JOIN " . $this->db->table("users") . " u ON l.user_id = u.id
             {$where} ORDER BY l.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("activity_logs") . " l {$where}", $params);
        
        $this->view('logs', [
            'page_title' => 'Activity Logs', 'breadcrumb_module' => 'Admin', 'breadcrumb_page' => 'Logs',
            'logs' => $logs, 'pagination' => paginate($total, $page)
        ]);
    }
}
