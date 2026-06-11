<?php
/**
 * FabX ERP - Base Controller
 * All module controllers extend this
 */

namespace Core;

class Controller {
    protected Database $db;
    protected string $module = '';
    protected array $data = [];
    protected array $layoutData = [];

    public function __construct() {
        $this->db = Database::getInstance();
        $this->initLayoutData();
    }

    /**
     * Initialize common layout data
     */
    protected function initLayoutData(): void {
        $this->layoutData = [
            'app_name' => APP_NAME,
            'app_version' => APP_VERSION,
            'company_name' => COMPANY_NAME,
            'user_name' => $_SESSION['user_name'] ?? 'Guest',
            'user_role' => $_SESSION['user_role'] ?? '',
            'user_avatar' => $_SESSION['user_avatar'] ?? '',
            'user_department' => $_SESSION['user_department'] ?? '',
            'notifications' => $this->getNotifications(),
            'unread_notifications' => $this->getUnreadNotificationCount(),
            'current_module' => $this->module,
            'theme' => $_SESSION['theme'] ?? 'light',
            'csrf_token' => generate_csrf(),
            'sidebar_collapsed' => $_SESSION['sidebar_collapsed'] ?? false,
        ];
    }

    /**
     * Render view with layout
     */
    protected function view(string $view, array $data = []): void {
        $this->data = array_merge($this->layoutData, $data);
        
        // Extract data for view
        extract($this->data);
        
        // Start output buffering
        ob_start();
        
        $viewFile = FABX_ROOT . '/modules/' . $this->module . '/views/' . $view . '.php';
        
        // Fallback to shared views if module view doesn't exist
        if (!file_exists($viewFile)) {
            $viewFile = FABX_ROOT . '/templates/components/' . $view . '.php';
        }
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<div class='alert alert-danger'>View not found: {$view}</div>";
        }
        
        $content = ob_get_clean();
        
        // Include layout
        require_once FABX_ROOT . '/templates/layout.php';
    }

    /**
     * Render a print-ready document (standalone A4 layout, no app chrome).
     * The module print view supplies the document body; templates/print.php
     * wraps it with the company letterhead, styling and print toolbar.
     */
    protected function printView(string $view, string $docTitle, array $data = []): void {
        $data['company'] = $data['company'] ?? $this->companyProfile();
        extract($data);

        ob_start();
        $viewFile = FABX_ROOT . '/modules/' . $this->module . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<p>Print template not found: {$view}</p>";
        }
        $content = ob_get_clean();

        $company = $data['company'];
        $doc_title = $docTitle;
        require FABX_ROOT . '/templates/print.php';
        exit();
    }

    /**
     * Company profile from settings, with config fallbacks
     */
    protected function companyProfile(): array {
        $company = [
            'company_name' => COMPANY_NAME,
            'company_tagline' => COMPANY_TAGLINE,
            'company_address' => COMPANY_ADDRESS,
            'company_phone' => COMPANY_PHONE,
            'company_email' => COMPANY_EMAIL,
            'company_gstin' => COMPANY_GSTIN,
        ];
        try {
            $rows = $this->db->fetchAll(
                "SELECT setting_key, setting_value FROM " . $this->db->table("settings") . " WHERE setting_group = 'company'"
            );
            foreach ($rows as $row) {
                if ($row['setting_value'] !== null && $row['setting_value'] !== '') {
                    $company[$row['setting_key']] = $row['setting_value'];
                }
            }
        } catch (\Exception $e) {
            // fall back to config constants
        }
        return $company;
    }

    /**
     * Render partial view (no layout)
     */
    protected function partial(string $view, array $data = []): void {
        extract(array_merge($this->layoutData, $data));
        
        $viewFile = FABX_ROOT . '/modules/' . $this->module . '/views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            $viewFile = FABX_ROOT . '/templates/components/' . $view . '.php';
        }
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        }
    }

    /**
     * JSON response
     */
    protected function json(bool $success, string $message = '', array $data = []): void {
        json_response($success, $message, $data);
    }

    /**
     * Redirect
     */
    protected function redirect(string $url): void {
        redirect($url);
    }

    /**
     * Set flash message
     */
    protected function flash(string $type, string $message): void {
        set_flash($type, $message);
    }

    /**
     * Check permission
     */
    protected function can(string $permission): bool {
        return has_permission($permission);
    }

    /**
     * Require permission
     */
    protected function requireCan(string $permission): void {
        require_permission($permission);
    }

    /**
     * Get notifications for current user
     */
    protected function getNotifications(int $limit = 10): array {
        if (!is_logged_in()) return [];
        
        try {
            return $this->db->fetchAll(
                "SELECT * FROM " . $this->db->table("notifications") . " 
                 WHERE user_id = ? OR department = ? 
                 ORDER BY created_at DESC LIMIT ?",
                [current_user_id(), $_SESSION['user_department'] ?? '', $limit]
            );
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get unread notification count
     */
    protected function getUnreadNotificationCount(): int {
        if (!is_logged_in()) return 0;
        
        try {
            return (int)$this->db->fetchValue(
                "SELECT COUNT(*) FROM " . $this->db->table("notifications") . " 
                 WHERE (user_id = ? OR department = ?) AND is_read = 0",
                [current_user_id(), $_SESSION['user_department'] ?? '']
            );
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Log activity
     */
    protected function log(string $action, string $description = ''): void {
        log_activity($action, $description);
    }

    /**
     * Paginate query results
     */
    protected function paginate(string $table, array $where = [], string $orderBy = 'id DESC', int $perPage = DEFAULT_PER_PAGE): array {
        $page = (int)($_GET['page'] ?? 1);
        $page = max(1, $page);
        
        $whereClause = '';
        $params = [];
        
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
                $params[] = $value;
            }
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }
        
        $total = (int)$this->db->fetchValue(
            "SELECT COUNT(*) FROM " . $this->db->table($table) . " $whereClause",
            $params
        );
        
        $pagination = paginate($total, $page, $perPage);
        
        $items = $this->db->fetchAll(
            "SELECT * FROM " . $this->db->table($table) . " $whereClause ORDER BY $orderBy LIMIT ? OFFSET ?",
            array_merge($params, [$pagination['per_page'], $pagination['offset']])
        );
        
        return [
            'items' => $items,
            'pagination' => $pagination
        ];
    }
}
