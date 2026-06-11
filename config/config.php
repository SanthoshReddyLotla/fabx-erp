<?php
/**
 * FabX Engineering ERP - Main Configuration File
 * Core PHP 8+ / MySQL / Bootstrap 5
 */

// Prevent direct access
if (!defined('FABX_ROOT')) {
    define('FABX_ROOT', dirname(__DIR__));
}

// Application Settings
define('APP_NAME', 'FabX Engineering ERP');
define('APP_VERSION', '1.0.0');

// Base URL: set FABX_APP_URL in the environment (or hardcode it here) to pin it;
// otherwise it is detected from the current request so the app works on any
// domain or sub-directory without code changes.
if (getenv('FABX_APP_URL')) {
    define('APP_URL', rtrim(getenv('FABX_APP_URL'), '/'));
} elseif (!empty($_SERVER['HTTP_HOST'])) {
    $fabxScheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        || ($_SERVER['SERVER_PORT'] ?? null) == 443) ? 'https' : 'http';
    $fabxBasePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    define('APP_URL', $fabxScheme . '://' . $_SERVER['HTTP_HOST'] . $fabxBasePath);
    unset($fabxScheme, $fabxBasePath);
} else {
    define('APP_URL', 'http://localhost'); // CLI fallback
}

define('APP_ENV', getenv('FABX_ENV') ?: 'production'); // development | production

// Company Info
define('COMPANY_NAME', 'FabX Engineering');
define('COMPANY_TAGLINE', 'Precision Mechanical Fabrication Solutions');
define('COMPANY_ADDRESS', 'Industrial Estate, Manufacturing Zone');
define('COMPANY_PHONE', '+91-XXXXXXXXXX');
define('COMPANY_EMAIL', 'info@fabxengineering.com');
define('COMPANY_GSTIN', 'XXABCXX1234X1ZX');
define('COMPANY_LOGO', 'assets/images/logo.png');

// Database Configuration
// SECURITY: prefer setting these via environment variables (FABX_DB_HOST etc.)
// so credentials are never committed to version control. The literals below
// are fallbacks for hosts where environment variables are not available.
define('DB_HOST', getenv('FABX_DB_HOST') ?: 'srv2205.hstgr.io');
define('DB_NAME', getenv('FABX_DB_NAME') ?: 'u627154334_fabx_erp');
define('DB_USER', getenv('FABX_DB_USER') ?: 'u627154334_fabx_erp');
define('DB_PASS', getenv('FABX_DB_PASS') ?: 'Tony@2180');
define('DB_CHARSET', 'utf8mb4');
define('DB_PREFIX', 'fabx_');

// Session Configuration
define('SESSION_NAME', 'fabx_session');
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_TIMEOUT', 1800); // 30 minutes idle timeout

// Security Configuration
define('CSRF_TOKEN_NAME', 'fabx_csrf_token');
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_COST', 12);
// SECURITY: set FABX_ENCRYPTION_KEY in the environment to a random 32-char
// string before encrypting any data you intend to keep.
define('ENCRYPTION_KEY', getenv('FABX_ENCRYPTION_KEY') ?: '12345678912345678912345678912345');
define('MAX_FAILED_LOGINS', 5);
define('LOCKOUT_MINUTES', 15);

// File Upload Configuration
define('MAX_UPLOAD_SIZE', 10485760); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'dwg', 'dxf', 'step', 'iges']);
define('UPLOAD_PATH', FABX_ROOT . '/assets/uploads/');

// Pagination
define('DEFAULT_PER_PAGE', 25);
define('MAX_PER_PAGE', 100);

// Date/Time Settings
define('DEFAULT_TIMEZONE', 'Asia/Kolkata');
define('DATE_FORMAT', 'd-m-Y');
define('DATETIME_FORMAT', 'd-m-Y H:i:s');
define('DB_DATE_FORMAT', 'Y-m-d');

// GST Configuration (India)
define('GST_ENABLED', true);
define('DEFAULT_GST_RATE', 18);
define('CGST_RATE', 9);
define('SGST_RATE', 9);
define('IGST_RATE', 18);

// ISO 9001 Settings
define('ISO_CERTIFICATE_NO', 'ISO 9001:2015');
define('CERTIFICATION_BODY', 'TUV SUD / BV / DNV');
define('CERTIFICATE_EXPIRY', '2027-12-31');
define('AUDIT_CYCLE', 'ANNUAL');

// Email Configuration (SMTP ready)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM', 'noreply@fabxengineering.com');
define('SMTP_FROM_NAME', 'FabX Engineering');

// Feature Flags
define('ENABLE_WHATSAPP', false);
define('ENABLE_EMAIL_NOTIFICATIONS', true);
define('ENABLE_SMS_NOTIFICATIONS', false);
define('ENABLE_BARCODE', true);
define('ENABLE_DIGITAL_SIGNATURE', true);
define('ENABLE_CLIENT_PORTAL', true);
define('ENABLE_VENDOR_PORTAL', true);

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', FABX_ROOT . '/logs/error.log');
}

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_name(SESSION_NAME);
    session_start();
}

// Autoloader
spl_autoload_register(function ($class) {
    $prefixes = [
        'Core\\' => FABX_ROOT . '/core/',
        'Modules\\' => FABX_ROOT . '/modules/',
    ];
    
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Include helper functions
require_once FABX_ROOT . '/includes/functions.php';
require_once FABX_ROOT . '/includes/security.php';
