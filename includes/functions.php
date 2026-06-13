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
 * Random uppercase alphanumeric suffix for document numbers
 */
function code_suffix(int $length = 5): string {
    $chars = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $out = '';
    for ($i = 0; $i < $length; $i++) {
        $out .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $out;
}

/**
 * Generate a unique code
 */
function generate_code(string $prefix, int $length = 5): string {
    $timestamp = date('Ymd');
    return $prefix . '-' . $timestamp . '-' . code_suffix($length);
}

/**
 * Generate quotation number
 */
function generate_quotation_no(): string {
    $year = date('Y');
    $month = date('m');
    return 'QT-' . $year . $month . '-' . code_suffix(4);
}

/**
 * Generate invoice number
 */
function generate_invoice_no(): string {
    $year = date('Y');
    $month = date('m');
    return 'INV-' . $year . $month . '-' . code_suffix(4);
}

/**
 * Generate PO number
 */
function generate_po_no(): string {
    $year = date('Y');
    $month = date('m');
    return 'PO-' . $year . $month . '-' . code_suffix(4);
}

/**
 * Generate NCR number
 */
function generate_ncr_no(): string {
    $year = date('Y');
    return 'NCR-' . $year . '-' . code_suffix(4);
}

/**
 * Generate CAPA number
 */
function generate_capa_no(): string {
    $year = date('Y');
    return 'CAPA-' . $year . '-' . code_suffix(4);
}

/**
 * Generate project code
 */
function generate_project_code(): string {
    $year = date('Y');
    return 'PRJ-' . $year . '-' . code_suffix(5);
}

/**
 * Generate work order number
 */
function generate_wo_no(): string {
    $year = date('Y');
    $month = date('m');
    return 'WO-' . $year . $month . '-' . code_suffix(4);
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
 * Get raw request value without sanitization.
 * Use for passwords: sanitize() would strip/encode characters and silently
 * change the credential before hashing or verification.
 */
function input_raw(string $key, $default = null) {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
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
 * Create an in-app notification for a user and/or department
 */
function notify(string $title, string $message = '', string $type = 'info', ?int $userId = null, ?string $department = null, string $module = '', string $link = ''): void {
    try {
        $db = Core\Database::getInstance();
        $db->execute(
            "INSERT INTO " . DB_PREFIX . "notifications (user_id, department, title, message, type, module, link, is_read, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW())",
            [$userId, $department, $title, $message, $type, $module, $link]
        );
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
    }
}

/**
 * Send an email. Uses PHP mail(); failures are logged, never fatal.
 */
function send_email(string $to, string $subject, string $htmlBody): bool {
    if (!ENABLE_EMAIL_NOTIFICATIONS) return false;
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM . '>',
        'Reply-To: ' . SMTP_FROM,
        'X-Mailer: FabX-ERP',
    ];
    try {
        $sent = @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
        if (!$sent) {
            error_log("send_email failed for {$to}: {$subject}");
        }
        return $sent;
    } catch (Exception $e) {
        error_log("send_email error: " . $e->getMessage());
        return false;
    }
}

/**
 * Convert an amount to words using the Indian numbering system
 * e.g. 125000.50 -> "Rupees One Lakh Twenty Five Thousand and Fifty Paise Only"
 */
function amount_in_words(float $amount): string {
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    $twoDigits = function (int $n) use ($ones, $tens): string {
        if ($n < 20) return $ones[$n];
        return trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]);
    };

    $convert = function (int $n) use (&$convert, $twoDigits): string {
        if ($n == 0) return '';
        if ($n < 100) return $twoDigits($n);
        if ($n < 1000) return trim($twoDigits(intdiv($n, 100)) . ' Hundred ' . $convert($n % 100));
        if ($n < 100000) return trim($convert(intdiv($n, 1000)) . ' Thousand ' . $convert($n % 1000));
        if ($n < 10000000) return trim($convert(intdiv($n, 100000)) . ' Lakh ' . $convert($n % 100000));
        return trim($convert(intdiv($n, 10000000)) . ' Crore ' . $convert($n % 10000000));
    };

    $rupees = (int)floor(abs($amount));
    $paise = (int)round((abs($amount) - $rupees) * 100);

    $out = 'Rupees ' . ($rupees > 0 ? $convert($rupees) : 'Zero');
    if ($paise > 0) {
        $out .= ' and ' . $convert($paise) . ' Paise';
    }
    return preg_replace('/\s+/', ' ', $out) . ' Only';
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

    if (!verify_upload_security($file, $ext)) {
        $result['error'] = 'File content does not match its extension.';
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
    $totalPages = max(1, (int)ceil($total / $perPage));
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

    // text-bg-* picks a contrasting text color automatically (BS 5.3)
    return '<span class="badge text-bg-' . $class . '">' . $label . '</span>';
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
    return '<span class="badge text-bg-' . $class . '">' . ucfirst($priority) . '</span>';
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
    return preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', strtoupper(trim($gstin))) === 1;
}

/**
 * Map of GST state codes (first two digits of a GSTIN) to state names.
 */
function gstin_states(): array {
    return [
        '01' => 'Jammu and Kashmir', '02' => 'Himachal Pradesh', '03' => 'Punjab',
        '04' => 'Chandigarh', '05' => 'Uttarakhand', '06' => 'Haryana', '07' => 'Delhi',
        '08' => 'Rajasthan', '09' => 'Uttar Pradesh', '10' => 'Bihar', '11' => 'Sikkim',
        '12' => 'Arunachal Pradesh', '13' => 'Nagaland', '14' => 'Manipur', '15' => 'Mizoram',
        '16' => 'Tripura', '17' => 'Meghalaya', '18' => 'Assam', '19' => 'West Bengal',
        '20' => 'Jharkhand', '21' => 'Odisha', '22' => 'Chhattisgarh', '23' => 'Madhya Pradesh',
        '24' => 'Gujarat', '25' => 'Daman and Diu', '26' => 'Dadra and Nagar Haveli',
        '27' => 'Maharashtra', '28' => 'Andhra Pradesh (Old)', '29' => 'Karnataka',
        '30' => 'Goa', '31' => 'Lakshadweep', '32' => 'Kerala', '33' => 'Tamil Nadu',
        '34' => 'Puducherry', '35' => 'Andaman and Nicobar Islands', '36' => 'Telangana',
        '37' => 'Andhra Pradesh', '38' => 'Ladakh', '97' => 'Other Territory', '99' => 'Centre Jurisdiction',
    ];
}

/**
 * Validate the GSTIN check digit (base-36 Luhn variant used by the GST system).
 */
function validate_gstin_checksum(string $gstin): bool {
    $gstin = strtoupper(trim($gstin));
    if (strlen($gstin) !== 15) return false;
    $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $mod = 36;
    $factor = 2;
    $sum = 0;
    for ($i = 13; $i >= 0; $i--) {
        $cp = strpos($alphabet, $gstin[$i]);
        if ($cp === false) return false;
        $digit = $factor * $cp;
        $factor = ($factor === 2) ? 1 : 2;
        $sum += intdiv($digit, $mod) + ($digit % $mod);
    }
    $checkCp = ($mod - ($sum % $mod)) % $mod;
    return $alphabet[$checkCp] === $gstin[14];
}

/**
 * Decode everything that can be derived from a GSTIN offline (no API needed):
 * format/checksum validity, state, embedded PAN, and the entity type that the
 * PAN's 4th character encodes.
 */
function gstin_decode(string $gstin): array {
    $gstin = strtoupper(trim($gstin));
    $out = [
        'valid' => false, 'gstin' => $gstin, 'state_code' => null, 'state' => null,
        'pan' => null, 'entity_type' => null, 'registration_seq' => null, 'error' => null,
    ];

    if (!is_valid_gstin($gstin)) {
        $out['error'] = 'GSTIN must be 15 characters in the format 22AAAAA0000A1Z5.';
        return $out;
    }
    if (!validate_gstin_checksum($gstin)) {
        $out['error'] = 'GSTIN check digit is invalid - please re-check the number.';
        // still return the decoded parts below so the user sees what we parsed
    }

    $stateCode = substr($gstin, 0, 2);
    $pan = substr($gstin, 2, 10);
    $entityChar = $pan[3] ?? ''; // 4th char of PAN encodes the holder type
    $entityTypes = [
        'P' => 'Individual / Proprietor', 'C' => 'Private/Public Company', 'H' => 'Hindu Undivided Family (HUF)',
        'F' => 'Partnership Firm / LLP', 'A' => 'Association of Persons (AOP)', 'T' => 'Trust',
        'B' => 'Body of Individuals (BOI)', 'L' => 'Local Authority', 'J' => 'Artificial Juridical Person',
        'G' => 'Government',
    ];

    $out['valid'] = ($out['error'] === null);
    $out['state_code'] = $stateCode;
    $out['state'] = gstin_states()[$stateCode] ?? 'Unknown';
    $out['pan'] = $pan;
    $out['entity_type'] = $entityTypes[$entityChar] ?? 'Unknown';
    $out['registration_seq'] = $gstin[12]; // entity registration count within the state
    return $out;
}

/**
 * Look up full taxpayer details for a GSTIN via an external verification API,
 * if one is configured. Returns null when no API key is set or on failure -
 * callers fall back to the offline gstin_decode() data.
 *
 * Configure in Admin > Settings:
 *   gst_api_key - your provider key (e.g. appyflow / mastergst)
 *   gst_api_url - URL template with {GSTIN} and {KEY} placeholders
 *                 default: https://appyflow.in/api/verifyGST?gstNo={GSTIN}&key_secret={KEY}
 */
function gstin_api_lookup(string $gstin, string $apiKey, string $urlTemplate = ''): ?array {
    if ($apiKey === '') return null;
    $gstin = strtoupper(trim($gstin));
    $urlTemplate = $urlTemplate ?: 'https://appyflow.in/api/verifyGST?gstNo={GSTIN}&key_secret={KEY}';
    $url = str_replace(['{GSTIN}', '{KEY}'], [rawurlencode($gstin), rawurlencode($apiKey)], $urlTemplate);

    $raw = null;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'FabX-ERP',
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(['http' => ['timeout' => 8], 'https' => ['timeout' => 8]]);
        $raw = @file_get_contents($url, false, $ctx);
    }
    if (!$raw) return null;

    $data = json_decode($raw, true);
    if (!is_array($data)) return null;

    // Normalise the common appyflow / mastergst shapes into our field names.
    $tp = $data['taxpayerInfo'] ?? $data['data'] ?? $data;
    if (!is_array($tp) || empty($tp)) return null;

    $addr = $tp['pradr']['addr'] ?? [];
    $line = trim(implode(', ', array_filter([
        $addr['bno'] ?? null, $addr['bnm'] ?? null, $addr['st'] ?? null,
        $addr['loc'] ?? null, $addr['landMark'] ?? null,
    ])));

    return [
        'legal_name'   => $tp['lgnm'] ?? ($tp['legalName'] ?? null),
        'trade_name'   => $tp['tradeNam'] ?? ($tp['tradeName'] ?? null),
        'address'      => $line ?: ($tp['adr'] ?? null),
        'city'         => $addr['dst'] ?? ($addr['loc'] ?? null),
        'state'        => $addr['stcd'] ?? null,
        'pincode'      => $addr['pncd'] ?? null,
        'status'       => $tp['sts'] ?? null,
        'registered_on'=> $tp['rgdt'] ?? null,
        'constitution' => $tp['ctb'] ?? null,
    ];
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
