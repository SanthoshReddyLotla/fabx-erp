<?php
/**
 * FabX ERP - Global Helper Functions
 */

/**
 * Redirect to a specific URL
 */
function redirect(string $url): void {
    header("Location: " . APP_URL . "/" . ltrim($url, '/'));
    exit();
}

/**
 * Get base URL
 */
function base_url(string $path = ''): string {
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Get asset URL
 */
function asset(string $path): string {
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Format date
 */
function format_date(?string $date, string $format = DATE_FORMAT): string {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    $d = DateTime::createFromFormat(DB_DATE_FORMAT, $date);
    if (!$d) {
        $d = new DateTime($date);
    }
    return $d->format($format);
}

/**
 * Format datetime
 */
function format_datetime(?string $datetime): string {
    return format_date($datetime, DATETIME_FORMAT);
}

/**
 * Format currency (Indian Rupees)
 */
function format_currency(?float $amount, bool $symbol = true): string {
    if ($amount === null) return $symbol ? '₹ 0.00' : '0.00';
    $formatted = number_format($amount, 2);
    return $symbol ? '₹ ' . $formatted : $formatted;
}

/**
 * Format number with Indian numbering system
 */
function format_indian_number(float $number): string {
    $decimal = round($number - floor($number), 2);
    $whole = floor($number);
    $wholeStr = (string)$whole;
    $length = strlen($wholeStr);
    
    if ($length <= 3) {
        $result = $wholeStr;
    } else {
        $lastThree = substr($wholeStr, -3);
        $remaining = substr($wholeStr, 0, -3);
        $remaining = preg_replace('/(?<=\d)(?=(\d{2})+$)/', ',', $remaining);
        $result = $remaining . ',' . $lastThree;
    }
    
    if ($decimal > 0) {
        $result .= substr(number_format($decimal, 2), 1);
    }
    
    return $result;
}

/**
 * Generate a unique code
 */
function generate_code(string $prefix, int $length = 5): string {
    $timestamp = date('Ymd');
    $random = strtoupper(substr(uniqid(), -$length));
    return $prefix . '-' . $timestamp . '-' . $random;
}

/**
 * Generate quotation number
 */
function generate_quotation_no(): string {
    $year = date('Y');
    $month = date('m');
    $random = strtoupper(substr(uniqid(), -4));
    return 'QT-' . $year . $month . '-' . $random;
}

/**
 * Generate invoice number
 */
function generate_invoice_no(): string {
    $year = date('Y');
    $month = date('m');
    $random = strtoupper(substr(uniqid(), -4));
    return 'INV-' . $year . $month . '-' . $random;
}

/**
 * Generate PO number
 */
function generate_po_no(): string {
    $year = date('Y');
    $month = date('m');
    $random = strtoupper(substr(uniqid(), -4));
    return 'PO-' . $year . $month . '-' . $random;
}

/**
 * Generate NCR number
 */
function generate_ncr_no(): string {
    $year = date('Y');
    $random = strtoupper(substr(uniqid(), -4));
    return 'NCR-' . $year . '-' . $random;
}

/**
 * Generate CAPA number
 */
function generate_capa_no(): string {
    $year = date('Y');
    $random = strtoupper(substr(uniqid(), -4));
    return 'CAPA-' . $year . '-' . $random;
}

/**
 * Generate project code
 */
function generate_project_code(): string {
    $year = date('Y');
    $random = strtoupper(substr(uniqid(), -5));
    return 'PRJ-' . $year . '-' . $random;
}

/**
 * Generate work order number
 */
function generate_wo_no(): string {
    $year = date('Y');
    $month = date('m');
    $random = strtoupper(substr(uniqid(), -4));
    return 'WO-' . $year . $month . '-' . $random;
}

/**
 * Sanitize input string
 */
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Clean array inputs
 */
function sanitize_array(array $input): array {
    $clean = [];
    foreach ($input as $key => $value) {
        if (is_array($value)) {
            $clean[$key] = sanitize_array($value);
        } else {
            $clean[$key] = sanitize((string)$value);
        }
    }
    return $clean;
}

/**
 * Get POST data safely
 */
function input(string $key, $default = null) {
    if (isset($_POST[$key])) {
        return is_array($_POST[$key]) ? sanitize_array($_POST[$key]) : sanitize($_POST[$key]);
    }
    if (isset($_GET[$key])) {
        return is_array($_GET[$key]) ? sanitize_array($_GET[$key]) : sanitize($_GET[$key]);
    }
    return $default;
}

/**
 * Check if request is POST
 */
function is_post(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is AJAX
 */
function is_ajax(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Set flash message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash_messages'][] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Get and clear flash messages
 */
function get_flash(): array {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 */
function has_role(string $role): bool {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Check user permission
 */
function has_permission(string $permission): bool {
    if (!is_logged_in()) return false;
    if ($_SESSION['user_role'] === 'Super Admin') return true;
    return isset($_SESSION['user_permissions']) && 
           in_array($permission, $_SESSION['user_permissions'] ?? []);
}

/**
 * Get current user ID
 */
function current_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function current_user_role(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Generate CSRF token
 */
function generate_csrf(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Validate CSRF token
 */
function validate_csrf(?string $token = null): bool {
    $token = $token ?? ($_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token);
}

/**
 * CSRF input field
 */
function csrf_field(): string {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . generate_csrf() . '">';
}

/**
 * Encrypt data
 */
function encrypt_data(string $data): string {
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt data
 */
function decrypt_data(string $data): ?string {
    try {
        $key = hash('sha256', ENCRYPTION_KEY, true);
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted !== false ? $decrypted : null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Log activity
 */
function log_activity(string $action, string $description = '', ?int $userId = null): void {
    try {
        $db = Core\Database::getInstance();
        $userId = $userId ?? current_user_id() ?? 0;
        $db->query(
            "INSERT INTO " . DB_PREFIX . "activity_logs (user_id, action, description, ip_address, user_agent, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())",
            [$userId, $action, $description, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']
        );
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}

/**
 * Calculate GST
 */
function calculate_gst(float $amount, float $rate = DEFAULT_GST_RATE): array {
    $gstAmount = ($amount * $rate) / 100;
    return [
        'cgst' => $gstAmount / 2,
        'sgst' => $gstAmount / 2,
        'igst' => $gstAmount,
        'total_gst' => $gstAmount,
        'total_with_gst' => $amount + $gstAmount
    ];
}

/**
 * Upload file
 */
function upload_file(array $file, string $directory = 'documents'): array {
    $result = ['success' => false, 'path' => '', 'filename' => '', 'error' => ''];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'Upload error: ' . $file['error'];
        return $result;
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $result['error'] = 'File size exceeds maximum limit of ' . (MAX_UPLOAD_SIZE / 1048576) . 'MB';
        return $result;
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        $result['error'] = 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS);
        return $result;
    }
    
    $uploadDir = UPLOAD_PATH . $directory . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $file['name']);
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $result['success'] = true;
        $result['path'] = 'assets/uploads/' . $directory . '/' . $filename;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'Failed to move uploaded file';
    }
    
    return $result;
}

/**
 * Generate PDF using basic HTML to PDF (placeholder for TCPDF/MPDF integration)
 */
function generate_pdf(string $html, string $filename = 'document.pdf'): void {
    // Render print-optimized HTML template that triggers the native browser print dialogue
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>" . htmlspecialchars(pathinfo($filename, PATHINFO_FILENAME)) . "</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css'>
        <style>
            body { background: #ffffff; color: #000000; font-family: 'Inter', sans-serif; padding: 40px; }
            .print-preview-header { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; margin-bottom: 30px; }
            @media print {
                body { padding: 0; }
                .no-print { display: none !important; }
            }
        </style>
    </head>
    <body onload='window.print()'>
        <div class='container'>
            <div class='d-flex justify-content-between align-items-center print-preview-header no-print'>
                <div>
                    <h5 class='mb-1 text-dark'><i class='bi bi-file-earmark-pdf-fill text-danger'></i> Document Print Preview</h5>
                    <span class='text-muted small'>Press print/save to generate your PDF document locally.</span>
                </div>
                <div class='d-flex gap-2'>
                    <button onclick='window.print()' class='btn btn-primary btn-sm'><i class='bi bi-printer'></i> Print / Save PDF</button>
                    <button onclick='window.close()' class='btn btn-outline-secondary btn-sm'>Close</button>
                </div>
            </div>
            {$html}
        </div>
    </body>
    </html>";
    exit();
}

/**
 * Send JSON response
 */
function json_response(bool $success, string $message = '', array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
        'timestamp' => date('c')
    ], $data));
    exit();
}

/**
 * Paginate results
 */
function paginate(int $total, int $page = 1, int $perPage = DEFAULT_PER_PAGE): array {
    $page = max(1, $page);
    $perPage = min(max(1, $perPage), MAX_PER_PAGE);
    $totalPages = (int)ceil($total / $perPage);
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;
    
    return [
        'page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_next' => $page < $totalPages,
        'has_prev' => $page > 1
    ];
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generate random password
 */
function generate_password(int $length = 12): string {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Calculate business days between two dates
 */
function business_days(string $start, string $end): int {
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $days = 0;
    
    while ($startDate <= $endDate) {
        $dayOfWeek = (int)$startDate->format('N');
        if ($dayOfWeek < 6) { // Monday to Friday
            $days++;
        }
        $startDate->modify('+1 day');
    }
    
    return $days;
}

/**
 * Get status badge HTML
 */
function status_badge(string $status): string {
    $badges = [
        'active' => 'success',
        'inactive' => 'secondary',
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'completed' => 'success',
        'in_progress' => 'primary',
        'on_hold' => 'warning',
        'cancelled' => 'danger',
        'delayed' => 'danger',
        'open' => 'info',
        'closed' => 'secondary',
        'draft' => 'light',
        'submitted' => 'primary',
        'under_review' => 'warning',
        'planning' => 'info',
        'design' => 'primary',
        'procurement' => 'warning',
        'production' => 'success',
        'assembly' => 'primary',
        'painting' => 'info',
        'dispatch' => 'warning',
        'installation' => 'primary',
        'paid' => 'success',
        'unpaid' => 'danger',
        'partial' => 'warning',
        'overdue' => 'danger',
    ];
    
    $class = $badges[strtolower($status)] ?? 'secondary';
    $label = ucwords(str_replace('_', ' ', $status));
    
    return '<span class="badge bg-' . $class . '">' . $label . '</span>';
}

/**
 * Get priority badge
 */
function priority_badge(string $priority): string {
    $badges = [
        'low' => 'success',
        'medium' => 'warning',
        'high' => 'danger',
        'critical' => 'danger',
        'urgent' => 'danger'
    ];
    
    $class = $badges[strtolower($priority)] ?? 'secondary';
    return '<span class="badge bg-' . $class . '">' . ucfirst($priority) . '</span>';
}

/**
 * Human readable time difference
 */
function time_ago(string $datetime): string {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    
    return format_date($datetime);
}

/**
 * Generate QR code data URL using a secure public QR Code API
 */
function generate_qr_data(string $data): string {
    return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($data);
}

/**
 * Export data to CSV
 */
function export_csv(array $data, array $headers, string $filename = 'export.csv'): void {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

/**
 * Validate email
 */
function is_valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Indian)
 */
function is_valid_phone(string $phone): bool {
    return preg_match('/^[6-9]\d{9}$/', $phone) === 1;
}

/**
 * Validate GSTIN
 */
function is_valid_gstin(string $gstin): bool {
    return preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $gstin) === 1;
}

/**
 * Generate breadcrumbs
 */
function breadcrumbs(array $items): string {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    foreach ($items as $label => $url) {
        if (is_int($label)) {
            $html .= '<li class="breadcrumb-item active">' . $url . '</li>';
        } elseif (end($items) === $url) {
            $html .= '<li class="breadcrumb-item active">' . $label . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . base_url($url) . '">' . $label . '</a></li>';
        }
    }
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Check session timeout
 */
function check_session_timeout(): void {
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive > SESSION_TIMEOUT) {
            session_destroy();
            redirect('/auth/login?timeout=1');
        }
    }
    $_SESSION['last_activity'] = time();
}
