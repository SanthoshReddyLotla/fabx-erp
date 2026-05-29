<?php
/**
 * FabX ERP - Dashboard Controller
 * Main dashboard with KPIs, charts, and widgets
 */

namespace Modules\Dashboard;

use Core\Controller;

class DashboardController extends Controller {
    protected string $module = 'dashboard';

    public function __construct() {
        parent::__construct();
        require_auth();
    }

    /**
     * Main dashboard
     */
    public function index(): void {
        $stats = $this->getDashboardStats();
        $recentProjects = $this->getRecentProjects();
        $recentNCRs = $this->getRecentNCRs();
        $upcomingTasks = $this->getUpcomingTasks();
        $calendarEvents = $this->getCalendarEvents();
        $notifications = $this->getRecentNotifications();

        $this->view('index', [
            'page_title' => 'Dashboard - ' . APP_NAME,
            'breadcrumb_module' => 'Home',
            'breadcrumb_page' => 'Dashboard',
            'stats' => $stats,
            'recent_projects' => $recentProjects,
            'recent_ncrs' => $recentNCRs,
            'upcoming_tasks' => $upcomingTasks,
            'calendar_events' => $calendarEvents,
            'recent_notifications' => $notifications,
            'extra_js' => '<script>document.addEventListener("DOMContentLoaded", function() { if(window.FabXCharts) FabXCharts.init(); });</script>'
        ]);
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array {
        return [
            'active_projects' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("projects") . " WHERE status = 'active'") ?? 0,
            'delayed_projects' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("projects") . " WHERE status = 'delayed'") ?? 0,
            'pending_quotations' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("quotations") . " WHERE status IN ('draft','sent','under_review')") ?? 0,
            'open_ncrs' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("ncr") . " WHERE status IN ('open','in_progress')") ?? 0,
            'open_capas' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("capa") . " WHERE status IN ('open','in_progress')") ?? 0,
            'outstanding_invoices' => $this->db->fetchValue("SELECT SUM(grand_total - paid_amount) FROM " . $this->db->table("invoices") . " WHERE status IN ('sent','partial','overdue')") ?? 0,
            'pending_prs' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("purchase_requisitions") . " WHERE status = 'submitted'") ?? 0,
            'upcoming_calibrations' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("calibrations") . " WHERE next_calibration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND status = 'active'") ?? 0,
            'monthly_revenue' => $this->db->fetchValue("SELECT SUM(paid_amount) FROM " . $this->db->table("invoices") . " WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())") ?? 0,
            'total_receivable' => $this->db->fetchValue("SELECT SUM(grand_total - paid_amount) FROM " . $this->db->table("invoices") . " WHERE status IN ('sent','partial','overdue')") ?? 0,
            'employee_count' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("users") . " WHERE status = 'active' AND is_deleted = 0") ?? 0,
            'todays_attendance' => $this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("attendance") . " WHERE attendance_date = CURDATE() AND status IN ('present','half_day')") ?? 0,
        ];
    }

    /**
     * Get recent projects
     */
    private function getRecentProjects(int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT p.*, c.company_name as client_name 
             FROM " . $this->db->table("projects") . " p
             LEFT JOIN " . $this->db->table("clients") . " c ON p.client_id = c.id
             WHERE p.status = 'active'
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get recent NCRs
     */
    private function getRecentNCRs(int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT n.*, CONCAT(u.first_name, ' ', u.last_name) as reported_by_name
             FROM " . $this->db->table("ncr") . " n
             LEFT JOIN " . $this->db->table("users") . " u ON n.reported_by = u.id
             WHERE n.status IN ('open','in_progress')
             ORDER BY n.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get upcoming tasks
     */
    private function getUpcomingTasks(int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT * FROM (
                SELECT 'Calibration' as type, equipment_name as title, next_calibration_date as due_date, 'warning' as priority
                FROM " . $this->db->table("calibrations") . " WHERE next_calibration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY) AND status = 'active'
                UNION ALL
                SELECT 'Audit' as type, title, planned_start_date as due_date, 'info' as priority
                FROM " . $this->db->table("audits") . " WHERE planned_start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY) AND status = 'planned'
                UNION ALL
                SELECT 'Project' as type, project_name, target_end_date as due_date, 'danger' as priority
                FROM " . $this->db->table("projects") . " WHERE target_end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY) AND status = 'active'
            ) tasks ORDER BY due_date ASC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get calendar events
     */
    private function getCalendarEvents(): array {
        return $this->db->fetchAll(
            "SELECT * FROM (
                SELECT 'audit' as type, audit_no as title, audit_date as event_date, 'info' as color
                FROM " . $this->db->table("audits") . " WHERE audit_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                UNION ALL
                SELECT 'training', title, start_date, 'success'
                FROM " . $this->db->table("training") . " WHERE start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                UNION ALL
                SELECT 'review', review_no, review_date, 'warning'
                FROM " . $this->db->table("management_reviews") . " WHERE review_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ) events ORDER BY event_date ASC LIMIT 10"
        );
    }

    /**
     * Get recent notifications
     */
    private function getRecentNotifications(int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT * FROM " . $this->db->table("notifications") . " 
             WHERE user_id = ? OR department = ?
             ORDER BY created_at DESC LIMIT ?",
            [current_user_id(), $_SESSION['user_department'] ?? '', $limit]
        );
    }

    /**
     * API: Get dashboard data (JSON)
     */
    public function apiData(): void {
        if (!is_ajax()) {
            $this->json(false, 'Invalid request');
        }

        $this->json(true, 'Dashboard data', [
            'stats' => $this->getDashboardStats(),
            'timestamp' => date('c')
        ]);
    }
}
