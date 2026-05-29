<?php
/**
 * FabX ERP - File Management Controller
 * Central document repository with version control
 */

namespace Modules\Files;

use Core\Controller;

class FileController extends Controller {
    protected string $module = 'files';

    public function __construct() { parent::__construct(); require_auth(); }

    public function index(): void {
        $this->requireCan('read');
        $page = (int)($_GET['page'] ?? 1);
        $folderId = input('folder');
        
        $where = "WHERE 1=1"; $params = [];
        if ($folderId) { $where .= " AND folder_id = ?"; $params[] = $folderId; }
        
        $files = $this->db->fetchAll(
            "SELECT f.*, CONCAT(u.first_name, ' ', u.last_name) as uploaded_by_name
             FROM " . $this->db->table("files") . " f
             LEFT JOIN " . $this->db->table("users") . " u ON f.uploaded_by = u.id
             {$where} ORDER BY f.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [DEFAULT_PER_PAGE, ($page-1)*DEFAULT_PER_PAGE])
        );
        $total = (int)$this->db->fetchValue("SELECT COUNT(*) FROM " . $this->db->table("files") . " f {$where}", $params);
        
        $folders = $this->db->fetchAll("SELECT * FROM " . $this->db->table("folders") . " ORDER BY name");
        
        $this->view('index', [
            'page_title' => 'Document Repository', 'breadcrumb_module' => 'Files', 'breadcrumb_page' => 'All Files',
            'files' => $files, 'folders' => $folders, 'pagination' => paginate($total, $page),
            'total_size' => $this->db->fetchValue("SELECT SUM(file_size) FROM " . $this->db->table("files")) ?? 0
        ]);
    }
}
