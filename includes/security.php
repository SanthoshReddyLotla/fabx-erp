<?php
/**
 * FabX ERP - Security Functions
 * SQL Injection, XSS, CSRF Protection
 */

/**
 * Secure database query using prepared statements
 */
function secure_query(mysqli $conn, string $query, array $params = []): mysqli_stmt|false {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Query preparation failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_bool($param)) {
                $types .= 'i';
                $param = $param ? 1 : 0;
            } elseif (is_null($param)) {
                $types .= 's';
                $param = '';
            } else {
                $types .= 's';
            }
            $values[] = $param;
        }
        
        $stmt->bind_param($types, ...$values);
    }
    
    return $stmt;
}

/**
 * Escape string for output
 */
function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Prevent XSS in output
 */
function xss_clean(string $data): string {
    $data = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $data);
    $data = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $data);
    $data = preg_replace('#<object(.*?)>(.*?)</object>#is', '', $data);
    $data = preg_replace('#<embed(.*?)>#is', '', $data);
    $data = preg_replace('#javascript:#i', '', $data);
    $data = preg_replace('#on\w+\s*=\s*"[^"]*"#i', '', $data);
    $data = preg_replace('#on\w+\s*=\s*\'[^\']*\'#i', '', $data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Rate limiting check
 */
function check_rate_limit(string $key, int $maxAttempts = 5, int $decayMinutes = 1): bool {
    $sessionKey = 'rate_limit_' . $key;
    $now = time();
    $window = $decayMinutes * 60;
    
    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = ['count' => 1, 'reset_at' => $now + $window];
        return true;
    }
    
    if ($now > $_SESSION[$sessionKey]['reset_at']) {
        $_SESSION[$sessionKey] = ['count' => 1, 'reset_at' => $now + $window];
        return true;
    }
    
    if ($_SESSION[$sessionKey]['count'] >= $maxAttempts) {
        return false;
    }
    
    $_SESSION[$sessionKey]['count']++;
    return true;
}

/**
 * Hash password securely
 */
function hash_password(string $password): string {
    return password_hash($password, PASSWORD_ALGO, ['cost' => PASSWORD_COST]);
}

/**
 * Verify password
 */
function verify_password(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Generate secure token
 */
function generate_token(int $length = 32): string {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Verify file upload security: the detected MIME type must be plausible
 * for the claimed extension. CAD/exchange formats (dwg, dxf, step, iges)
 * have no registered MIME type and commonly detect as octet-stream/text.
 */
function verify_upload_security(array $file, ?string $ext = null): bool {
    if (!is_uploaded_file($file['tmp_name'] ?? '')) {
        return false;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $ext = strtolower($ext ?? pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

    $mimeMap = [
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword', 'application/vnd.ms-office'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'xls'  => ['application/vnd.ms-excel', 'application/vnd.ms-office'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'dwg'  => ['application/octet-stream', 'image/vnd.dwg', 'application/acad'],
        'dxf'  => ['application/octet-stream', 'image/vnd.dxf', 'text/plain'],
        'step' => ['application/octet-stream', 'text/plain', 'application/step'],
        'iges' => ['application/octet-stream', 'text/plain', 'application/iges'],
    ];

    // Never accept anything that smells like executable script
    $blocked = ['text/x-php', 'application/x-php', 'application/x-httpd-php', 'text/html', 'application/javascript', 'text/javascript'];
    if (in_array($mimeType, $blocked, true)) {
        return false;
    }

    return isset($mimeMap[$ext]) && in_array($mimeType, $mimeMap[$ext], true);
}

/**
 * Log security event
 */
function log_security_event(string $event, string $details = ''): void {
    $logFile = FABX_ROOT . '/logs/security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $userId = current_user_id() ?? 'guest';
    
    $logEntry = "[$timestamp] [$ip] [User:$userId] $event - $details - UA: $userAgent" . PHP_EOL;
    
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    error_log($logEntry, 3, $logFile);
}

/**
 * Check permission and redirect if not authorized
 */
function require_permission(string $permission): void {
    if (!has_permission($permission)) {
        set_flash('error', 'You do not have permission to access this resource.');
        log_security_event('UNAUTHORIZED_ACCESS', "Permission required: $permission");
        redirect('/dashboard');
    }
}

/**
 * Try to restore a session from the remember-me cookie.
 * The cookie holds the raw token; the database stores its SHA-256 hash.
 */
function attempt_remember_login(): bool {
    if (is_logged_in() || empty($_COOKIE['remember_me'])) {
        return is_logged_in();
    }
    try {
        $db = Core\Database::getInstance();
        $user = $db->fetchOne(
            "SELECT u.*, r.name as role_name, r.permissions, d.name as department_name
             FROM " . DB_PREFIX . "users u
             LEFT JOIN " . DB_PREFIX . "roles r ON u.role_id = r.id
             LEFT JOIN " . DB_PREFIX . "departments d ON u.department_id = d.id
             WHERE u.remember_token = ? AND u.status = 'active' AND u.is_deleted = 0",
            [hash('sha256', $_COOKIE['remember_me'])]
        );
        if (!$user) {
            setcookie('remember_me', '', time() - 3600, '/', '', false, true);
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role_name'];
        $_SESSION['user_role_id'] = (int)$user['role_id'];
        $_SESSION['user_department'] = $user['department_name'];
        $_SESSION['user_department_id'] = (int)$user['department_id'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? '';
        $_SESSION['user_permissions'] = json_decode($user['permissions'] ?? '[]', true);
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
        log_activity('LOGIN', 'Session restored via remember-me cookie', (int)$user['id']);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Require authentication
 */
function require_auth(): void {
    if (!is_logged_in()) {
        set_flash('error', 'Please login to continue.');
        redirect('/auth/login');
    }
    check_session_timeout();
}

/**
 * Secure headers
 */
function set_security_headers(): void {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; font-src 'self' fonts.gstatic.com cdn.jsdelivr.net cdnjs.cloudflare.com; img-src 'self' data: blob:; connect-src 'self'; frame-ancestors 'none';");
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
