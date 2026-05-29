<?php
/**
 * FabX ERP - Reports Controller
 * Production, Quality, Sales, Inventory, Finance reports
 */

namespace Modules\Reports;

use Core\Controller;

class ReportController extends Controller {
    protected string $module = 'reports';

    public function __construct() { parent::__construct(); require_auth(); }

    public function production(): void {
        $this->requireCan('read');
        $reports = $this->db->fetchAll(
            "SELECT r.*, p.project_name, CONCAT(u.first_name, ' ', u.last_name) as reported_by_name
             FROM " . $this->db->table("production_reports") . " r
             LEFT JOIN " . $this->db->table("projects") . " p ON r.project_id = p.id
             LEFT JOIN " . $this->db->table("users") . " u ON r.reported_by = u.id
             ORDER BY r.report_date DESC LIMIT 100"
        );
        $this->view('production', [
            'page_title' => 'Production Reports', 'breadcrumb_module' => 'Reports', 'breadcrumb_page' => 'Production',
            'reports' => $reports
        ]);
    }

    public function quality(): void {
        $this->requireCan('read');
        $ncrByMonth = $this->db->fetchAll(
            "SELECT DATE_FORMAT(ncr_date, '%Y-%m') as month, COUNT(*) as count, severity
             FROM " . $this->db->table("ncr") . " 
             WHERE ncr_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY month, severity ORDER BY month"
        );
        $capaStatus = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count FROM " . $this->db->table("capa") . " GROUP BY status"
        );
        $this->view('quality', [
            'page_title' => 'Quality Reports', 'breadcrumb_module' => 'Reports', 'breadcrumb_page' => 'Quality',
            'ncr_trend' => $ncrByMonth, 'capa_status' => $capaStatus
        ]);
    }

    public function sales(): void {
        $this->requireCan('read');
        $monthlySales = $this->db->fetchAll(
            "SELECT DATE_FORMAT(quotation_date, '%Y-%m') as month, 
                    COUNT(*) as quotations, SUM(total_amount) as value
             FROM " . $this->db->table("quotations") . "
             WHERE quotation_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY month ORDER BY month"
        );
        $this->view('sales', [
            'page_title' => 'Sales Reports', 'breadcrumb_module' => 'Reports', 'breadcrumb_page' => 'Sales',
            'monthly_sales' => $monthlySales
        ]);
    }

    public function inventory(): void {
        $this->requireCan('read');
        $stock = $this->db->fetchAll(
            "SELECT i.*, ic.name as category_name,
                    CASE WHEN i.current_stock <= i.reorder_level THEN 'low' ELSE 'ok' END as stock_status
             FROM " . $this->db->table("items") . " i
             LEFT JOIN " . $this->db->table("item_categories") . " ic ON i.category_id = ic.id
             ORDER BY i.item_code LIMIT 200"
        );
        $this->view('inventory', [
            'page_title' => 'Inventory Reports', 'breadcrumb_module' => 'Reports', 'breadcrumb_page' => 'Inventory',
            'stock' => $stock
        ]);
    }

    public function finance(): void {
        $this->requireCan('read');
        $monthlyRevenue = $this->db->fetchAll(
            "SELECT DATE_FORMAT(invoice_date, '%Y-%m') as month,
                    SUM(grand_total) as invoiced, SUM(paid_amount) as collected
             FROM " . $this->db->table("invoices") . "
             WHERE invoice_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY month ORDER BY month"
        );
        $this->view('finance', [
            'page_title' => 'Finance Reports', 'breadcrumb_module' => 'Reports', 'breadcrumb_page' => 'Finance',
            'monthly_revenue' => $monthlyRevenue
        ]);
    }
}
