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
 * Verify file upload security
 */
function verify_upload_security(array $file): bool {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
        'image/jpg'
    ];
    
    return in_array($mimeType, $allowedMimes);
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
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com; img-src 'self' data: blob:; connect-src 'self'; frame-ancestors 'none';");
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
