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

    // ==================== MASTER SETUP & CONFIGURATION ====================

    public function masterSetup(): void {
        if (is_post()) {
            if (!validate_csrf()) {
                $this->flash('error', 'Invalid security token.');
                $this->redirect('/admin/master-setup');
            }
            $action = input('action');
            $tab = input('tab', 'calibrations');
            
            if ($action === 'create_calibration') {
                $equipmentId = input('equipment_id');
                if (empty($equipmentId)) {
                    $equipmentId = 'EQ-' . strtoupper(substr(md5(uniqid()), 0, 8));
                }
                
                $this->db->execute(
                    "INSERT INTO " . $this->db->table("calibrations") . " 
                    (equipment_id, equipment_name, manufacturer, model_no, serial_no, location, department_id, range_value, accuracy, frequency, last_calibration_date, next_calibration_date, status, remarks, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())",
                    [
                        $equipmentId,
                        input('equipment_name'),
                        input('manufacturer'),
                        input('model_no'),
                        input('serial_no'),
                        input('location'),
                        input('department_id') ?: null,
                        input('range_value'),
                        input('accuracy'),
                        input('frequency'),
                        input('last_calibration_date') ?: null,
                        input('next_calibration_date') ?: null,
                        input('remarks')
                    ]
                );
                $this->log('CALIBRATION_CREATED', "Calibration device {$equipmentId} onboarded");
                $this->flash('success', 'Calibration asset onboarded successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'update_calibration') {
                $id = (int)input('id');
                $this->db->execute(
                    "UPDATE " . $this->db->table("calibrations") . " 
                    SET equipment_name = ?, manufacturer = ?, model_no = ?, serial_no = ?, location = ?, department_id = ?, range_value = ?, accuracy = ?, frequency = ?, last_calibration_date = ?, next_calibration_date = ?, remarks = ?
                    WHERE id = ?",
                    [
                        input('equipment_name'),
                        input('manufacturer'),
                        input('model_no'),
                        input('serial_no'),
                        input('location'),
                        input('department_id') ?: null,
                        input('range_value'),
                        input('accuracy'),
                        input('frequency'),
                        input('last_calibration_date') ?: null,
                        input('next_calibration_date') ?: null,
                        input('remarks'),
                        $id
                    ]
                );
                $this->log('CALIBRATION_UPDATED', "Calibration asset #{$id} updated");
                $this->flash('success', 'Calibration asset updated successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'delete_calibration') {
                $id = (int)input('id');
                $this->db->execute("DELETE FROM " . $this->db->table("calibrations") . " WHERE id = ?", [$id]);
                $this->log('CALIBRATION_DELETED', "Calibration asset #{$id} deleted");
                $this->flash('success', 'Calibration asset deleted successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'create_doc_category') {
                $this->db->execute(
                    "INSERT INTO " . $this->db->table("doc_categories") . " (name, code, description, retention_period) VALUES (?, ?, ?, ?)",
                    [input('name'), input('code'), input('description'), (int)input('retention_period')]
                );
                $this->log('DOC_CATEGORY_CREATED', "Document category " . input('code') . " created");
                $this->flash('success', 'Document category created successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'update_doc_category') {
                $id = (int)input('id');
                $this->db->execute(
                    "UPDATE " . $this->db->table("doc_categories") . " SET name = ?, code = ?, description = ?, retention_period = ? WHERE id = ?",
                    [input('name'), input('code'), input('description'), (int)input('retention_period'), $id]
                );
                $this->log('DOC_CATEGORY_UPDATED', "Document category #{$id} updated");
                $this->flash('success', 'Document category updated successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'delete_doc_category') {
                $id = (int)input('id');
                $this->db->execute("DELETE FROM " . $this->db->table("doc_categories") . " WHERE id = ?", [$id]);
                $this->log('DOC_CATEGORY_DELETED', "Document category #{$id} deleted");
                $this->flash('success', 'Document category deleted successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'create_item_category') {
                $this->db->execute(
                    "INSERT INTO " . $this->db->table("item_categories") . " (name, code, description, parent_id, status) VALUES (?, ?, ?, ?, 'active')",
                    [input('name'), input('code'), input('description'), input('parent_id') ?: null]
                );
                $this->log('ITEM_CATEGORY_CREATED', "Inventory category " . input('code') . " created");
                $this->flash('success', 'Inventory category created successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'update_item_category') {
                $id = (int)input('id');
                $this->db->execute(
                    "UPDATE " . $this->db->table("item_categories") . " SET name = ?, code = ?, description = ?, parent_id = ? WHERE id = ?",
                    [input('name'), input('code'), input('description'), input('parent_id') ?: null, $id]
                );
                $this->log('ITEM_CATEGORY_UPDATED', "Inventory category #{$id} updated");
                $this->flash('success', 'Inventory category updated successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'delete_item_category') {
                $id = (int)input('id');
                $this->db->execute("DELETE FROM " . $this->db->table("item_categories") . " WHERE id = ?", [$id]);
                $this->log('ITEM_CATEGORY_DELETED', "Inventory category #{$id} deleted");
                $this->flash('success', 'Inventory category deleted successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'create_department') {
                $this->db->execute(
                    "INSERT INTO " . $this->db->table("departments") . " (name, code, description, cost_center, head_id, status) VALUES (?, ?, ?, ?, ?, 'active')",
                    [input('name'), input('code'), input('description'), input('cost_center'), input('head_id') ?: null]
                );
                $this->log('DEPARTMENT_CREATED', "Department " . input('code') . " created");
                $this->flash('success', 'Department created successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'update_department') {
                $id = (int)input('id');
                $this->db->execute(
                    "UPDATE " . $this->db->table("departments") . " SET name = ?, code = ?, description = ?, cost_center = ?, head_id = ? WHERE id = ?",
                    [input('name'), input('code'), input('description'), input('cost_center'), input('head_id') ?: null, $id]
                );
                $this->log('DEPARTMENT_UPDATED', "Department #{$id} updated");
                $this->flash('success', 'Department updated successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
            
            elseif ($action === 'delete_department') {
                $id = (int)input('id');
                $this->db->execute("DELETE FROM " . $this->db->table("departments") . " WHERE id = ?", [$id]);
                $this->log('DEPARTMENT_DELETED', "Department #{$id} deleted");
                $this->flash('success', 'Department deleted successfully.');
                $this->redirect('/admin/master-setup?tab=' . $tab);
            }
        }
        
        // GET Request Handling
        $calibrations = $this->db->fetchAll(
            "SELECT c.*, d.name as department_name 
             FROM " . $this->db->table("calibrations") . " c
             LEFT JOIN " . $this->db->table("departments") . " d ON c.department_id = d.id 
             ORDER BY c.equipment_id"
        );
        
        $docCategories = $this->db->fetchAll("SELECT * FROM " . $this->db->table("doc_categories") . " ORDER BY code");
        
        $itemCategories = $this->db->fetchAll(
            "SELECT ic.*, pic.name as parent_name 
             FROM " . $this->db->table("item_categories") . " ic
             LEFT JOIN " . $this->db->table("item_categories") . " pic ON ic.parent_id = pic.id 
             ORDER BY ic.code"
        );
        
        $departments = $this->db->fetchAll(
            "SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as head_name 
             FROM " . $this->db->table("departments") . " d
             LEFT JOIN " . $this->db->table("users") . " u ON d.head_id = u.id 
             ORDER BY d.code"
        );
        
        $users = $this->db->fetchAll(
            "SELECT id, CONCAT(first_name, ' ', last_name) as name 
             FROM " . $this->db->table("users") . " 
             WHERE status = 'active' AND is_deleted = 0 
             ORDER BY first_name"
        );
        
        // Handling GET-based inline edit fetches
        $editItem = null;
        $editType = input('edit_type');
        $editId = (int)input('edit_id');
        if ($editType && $editId) {
            if ($editType === 'calibration') {
                $editItem = $this->db->fetchOne("SELECT * FROM " . $this->db->table("calibrations") . " WHERE id = ?", [$editId]);
            } elseif ($editType === 'doc_category') {
                $editItem = $this->db->fetchOne("SELECT * FROM " . $this->db->table("doc_categories") . " WHERE id = ?", [$editId]);
            } elseif ($editType === 'item_category') {
                $editItem = $this->db->fetchOne("SELECT * FROM " . $this->db->table("item_categories") . " WHERE id = ?", [$editId]);
            } elseif ($editType === 'department') {
                $editItem = $this->db->fetchOne("SELECT * FROM " . $this->db->table("departments") . " WHERE id = ?", [$editId]);
            }
        }
        
        $this->view('master_setup', [
            'page_title' => 'Master Setup & Configuration', 'breadcrumb_module' => 'Admin', 'breadcrumb_page' => 'Master Setup',
            'calibrations' => $calibrations,
            'doc_categories' => $docCategories,
            'item_categories' => $itemCategories,
            'departments' => $departments,
            'users' => $users,
            'edit_item' => $editItem,
            'edit_type' => $editType,
            'active_tab' => input('tab', 'calibrations')
        ]);
    }
}
