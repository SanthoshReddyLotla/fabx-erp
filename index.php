<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * FabX Engineering ERP - Front Controller
 * Single entry point - Routes all requests to appropriate controllers
 */

// Define root path
define('FABX_ROOT', __DIR__);

// Load configuration
require_once FABX_ROOT . '/config/config.php';

// Set security headers
set_security_headers();

// Simple routing system
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
$uri = substr($uri, strlen($basePath));
$uri = trim($uri, '/');

// Extract route segments
$segments = explode('/', $uri);
$module = $segments[0] ?? 'dashboard';
$controller = $segments[0] ?? 'dashboard';
$action = $segments[1] ?? 'index';
$params = array_slice($segments, 2);

// Route mapping
$routes = [
    // Auth routes (no auth required)
    'auth/login' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'login', 'auth' => false],
    'auth/authenticate' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'authenticate', 'auth' => false],
    'auth/logout' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'logout', 'auth' => false],
    'auth/forgot-password' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'forgotPassword', 'auth' => false],
    'auth/send-reset' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'sendResetLink', 'auth' => false],
    'auth/reset-password' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'resetPassword', 'auth' => false],
    'auth/update-password' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'updatePassword', 'auth' => false],
    'auth/heartbeat' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'heartbeat', 'auth' => true],
    'auth/toggle-theme' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'toggleTheme', 'auth' => true],
    'auth/toggle-sidebar' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'toggleSidebar', 'auth' => true],
    'auth/profile' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'profile', 'auth' => true],
    'auth/update-profile' => ['module' => 'auth', 'controller' => 'AuthController', 'action' => 'updateProfile', 'auth' => true],

    // Dashboard
    'dashboard' => ['module' => 'dashboard', 'controller' => 'DashboardController', 'action' => 'index', 'auth' => true],
    'dashboard/api' => ['module' => 'dashboard', 'controller' => 'DashboardController', 'action' => 'apiData', 'auth' => true],

    // QMS Module
    'qms/dashboard' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'index', 'auth' => true],
    'qms/documents/status' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'updateDocumentStatus', 'auth' => true],
    'qms/documents/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createDocument', 'auth' => true],
    'qms/documents/edit' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'editDocument', 'auth' => true],
    'qms/documents/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewDocument', 'auth' => true],
    'qms/documents' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'documents', 'auth' => true],
    'qms/ncr/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createNCR', 'auth' => true],
    'qms/ncr/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewNCR', 'auth' => true],
    'qms/ncr/update' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'updateNCR', 'auth' => true],
    'qms/ncr' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'ncr', 'auth' => true],
    'qms/capa/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createCAPA', 'auth' => true],
    'qms/capa/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewCAPA', 'auth' => true],
    'qms/capa/effectiveness' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'updateCAPAEffectiveness', 'auth' => true],
    'qms/capa' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'capa', 'auth' => true],
    'qms/audits/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createAudit', 'auth' => true],
    'qms/audits/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewAudit', 'auth' => true],
    'qms/audits/finding' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'addAuditFinding', 'auth' => true],
    'qms/audits' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'audits', 'auth' => true],
    'qms/calibration/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createCalibration', 'auth' => true],
    'qms/calibration/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewCalibration', 'auth' => true],
    'qms/calibration' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'calibration', 'auth' => true],
    'qms/training/competency' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'competency', 'auth' => true],
    'qms/training/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createTraining', 'auth' => true],
    'qms/training/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewTraining', 'auth' => true],
    'qms/training/participants' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'updateParticipants', 'auth' => true],
    'qms/training' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'training', 'auth' => true],
    'qms/complaints/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createComplaint', 'auth' => true],
    'qms/complaints/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewComplaint', 'auth' => true],
    'qms/complaints/status' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'updateComplaintStatus', 'auth' => true],
    'qms/complaints' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'complaints', 'auth' => true],
    'qms/risks/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createRisk', 'auth' => true],
    'qms/risks/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewRisk', 'auth' => true],
    'qms/risks' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'risks', 'auth' => true],
    'qms/reviews/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createReview', 'auth' => true],
    'qms/reviews/view' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'viewReview', 'auth' => true],
    'qms/reviews' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'reviews', 'auth' => true],
    'qms/kpi/create' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'createKPI', 'auth' => true],
    'qms/kpi' => ['module' => 'qms', 'controller' => 'QMSController', 'action' => 'kpi', 'auth' => true],

    // Projects Module
    'projects' => ['module' => 'projects', 'controller' => 'ProjectController', 'action' => 'index', 'auth' => true],
    'projects/create' => ['module' => 'projects', 'controller' => 'ProjectController', 'action' => 'create', 'auth' => true],
    'projects/view' => ['module' => 'projects', 'controller' => 'ProjectController', 'action' => 'show', 'auth' => true],
    'projects/gantt' => ['module' => 'projects', 'controller' => 'ProjectController', 'action' => 'gantt', 'auth' => true],
    'projects/boq' => ['module' => 'projects', 'controller' => 'ProjectController', 'action' => 'boq', 'auth' => true],
    'projects/work-orders' => ['module' => 'projects', 'controller' => 'ProjectController', 'action' => 'workOrders', 'auth' => true],
    'projects/production' => ['module' => 'projects', 'controller' => 'ProjectController', 'action' => 'production', 'auth' => true],
    'projects/drawings' => ['module' => 'projects', 'controller' => 'ProjectController', 'action' => 'drawings', 'auth' => true],

    // CRM Module
    'crm/leads' => ['module' => 'crm', 'controller' => 'CRMController', 'action' => 'leads', 'auth' => true],
    'crm/inquiries' => ['module' => 'crm', 'controller' => 'CRMController', 'action' => 'inquiries', 'auth' => true],
    'crm/quotations' => ['module' => 'crm', 'controller' => 'CRMController', 'action' => 'quotations', 'auth' => true],
    'crm/quotations/create' => ['module' => 'crm', 'controller' => 'CRMController', 'action' => 'createQuotation', 'auth' => true],
    'crm/followups' => ['module' => 'crm', 'controller' => 'CRMController', 'action' => 'followups', 'auth' => true],
    'crm/pipeline' => ['module' => 'crm', 'controller' => 'CRMController', 'action' => 'pipeline', 'auth' => true],

    // Clients Module
    'clients' => ['module' => 'clients', 'controller' => 'ClientController', 'action' => 'index', 'auth' => true],
    'clients/create' => ['module' => 'clients', 'controller' => 'ClientController', 'action' => 'create', 'auth' => true],
    'clients/view' => ['module' => 'clients', 'controller' => 'ClientController', 'action' => 'show', 'auth' => true],
    'clients/tickets' => ['module' => 'clients', 'controller' => 'ClientController', 'action' => 'tickets', 'auth' => true],
    'clients/amc' => ['module' => 'clients', 'controller' => 'ClientController', 'action' => 'amc', 'auth' => true],

    // Vendors Module
    'vendors' => ['module' => 'vendors', 'controller' => 'VendorController', 'action' => 'index', 'auth' => true],
    'vendors/create' => ['module' => 'vendors', 'controller' => 'VendorController', 'action' => 'create', 'auth' => true],
    'vendors/view' => ['module' => 'vendors', 'controller' => 'VendorController', 'action' => 'show', 'auth' => true],

    // Purchase Module
    'purchase/requisitions' => ['module' => 'purchase', 'controller' => 'PurchaseController', 'action' => 'requisitions', 'auth' => true],
    'purchase/requisitions/create' => ['module' => 'purchase', 'controller' => 'PurchaseController', 'action' => 'createRequisition', 'auth' => true],
    'purchase/orders' => ['module' => 'purchase', 'controller' => 'PurchaseController', 'action' => 'orders', 'auth' => true],
    'purchase/orders/create' => ['module' => 'purchase', 'controller' => 'PurchaseController', 'action' => 'createOrder', 'auth' => true],
    'purchase/grn' => ['module' => 'purchase', 'controller' => 'PurchaseController', 'action' => 'grn', 'auth' => true],
    'purchase/inventory' => ['module' => 'purchase', 'controller' => 'PurchaseController', 'action' => 'inventory', 'auth' => true],
    'purchase/issues' => ['module' => 'purchase', 'controller' => 'PurchaseController', 'action' => 'issues', 'auth' => true],

    // Accounts Module
    'accounts/invoices' => ['module' => 'accounts', 'controller' => 'AccountsController', 'action' => 'invoices', 'auth' => true],
    'accounts/invoices/create' => ['module' => 'accounts', 'controller' => 'AccountsController', 'action' => 'createInvoice', 'auth' => true],
    'accounts/payments' => ['module' => 'accounts', 'controller' => 'AccountsController', 'action' => 'payments', 'auth' => true],
    'accounts/expenses' => ['module' => 'accounts', 'controller' => 'AccountsController', 'action' => 'expenses', 'auth' => true],
    'accounts/vendor-payments' => ['module' => 'accounts', 'controller' => 'AccountsController', 'action' => 'vendorPayments', 'auth' => true],
    'accounts/ledger' => ['module' => 'accounts', 'controller' => 'AccountsController', 'action' => 'ledger', 'auth' => true],

    // HR Module
    'hr/employees' => ['module' => 'hr', 'controller' => 'HRController', 'action' => 'employees', 'auth' => true],
    'hr/attendance' => ['module' => 'hr', 'controller' => 'HRController', 'action' => 'attendance', 'auth' => true],
    'hr/leaves' => ['module' => 'hr', 'controller' => 'HRController', 'action' => 'leaves', 'auth' => true],
    'hr/training' => ['module' => 'hr', 'controller' => 'HRController', 'action' => 'training', 'auth' => true],
    'hr/appraisals' => ['module' => 'hr', 'controller' => 'HRController', 'action' => 'appraisals', 'auth' => true],

    // Files Module
    'files' => ['module' => 'files', 'controller' => 'FileController', 'action' => 'index', 'auth' => true],

    // Reports Module
    'reports/production' => ['module' => 'reports', 'controller' => 'ReportController', 'action' => 'production', 'auth' => true],
    'reports/quality' => ['module' => 'reports', 'controller' => 'ReportController', 'action' => 'quality', 'auth' => true],
    'reports/sales' => ['module' => 'reports', 'controller' => 'ReportController', 'action' => 'sales', 'auth' => true],
    'reports/inventory' => ['module' => 'reports', 'controller' => 'ReportController', 'action' => 'inventory', 'auth' => true],
    'reports/finance' => ['module' => 'reports', 'controller' => 'ReportController', 'action' => 'finance', 'auth' => true],

    // Admin Module
    'admin/users' => ['module' => 'admin', 'controller' => 'AdminController', 'action' => 'users', 'auth' => true],
    'admin/roles' => ['module' => 'admin', 'controller' => 'AdminController', 'action' => 'roles', 'auth' => true],
    'admin/departments' => ['module' => 'admin', 'controller' => 'AdminController', 'action' => 'departments', 'auth' => true],
    'admin/settings' => ['module' => 'admin', 'controller' => 'AdminController', 'action' => 'settings', 'auth' => true],
    'admin/logs' => ['module' => 'admin', 'controller' => 'AdminController', 'action' => 'logs', 'auth' => true],
    'admin/master-setup' => ['module' => 'admin', 'controller' => 'AdminController', 'action' => 'masterSetup', 'auth' => true],
];

// Find matching route
$routeKey = $uri;
$route = $routes[$routeKey] ?? null;

// Fallback: try to match partial routes
if (!$route) {
    foreach ($routes as $key => $value) {
        if (strpos($uri, $key) === 0) {
            $route = $value;
            // Extract remaining params
            $remaining = substr($uri, strlen($key));
            $remainingParams = array_filter(explode('/', trim($remaining, '/')));
            $params = array_merge($params, $remainingParams);
            break;
        }
    }
}

// Default to dashboard if no route found
if (!$route) {
    // Check if user is logged in
    if (!is_logged_in()) {
        redirect('/auth/login');
    }
    $route = $routes['dashboard'];
}

// Check authentication
if (($route['auth'] ?? true) && !is_logged_in()) {
    set_flash('error', 'Please login to access this page.');
    redirect('/auth/login');
}

// Load and execute controller
$controllerClass = "Modules\\" . ucfirst($route['module']) . "\\" . $route['controller'];
$controllerFile = FABX_ROOT . '/modules/' . $route['module'] . '/' . $route['controller'] . '.php';

try {
    if (file_exists($controllerFile)) {
        require_once $controllerFile;

        if (class_exists($controllerClass)) {
            $instance = new $controllerClass();
            $action = $route['action'];

            if (method_exists($instance, $action)) {
                // Call action with params
                if (!empty($params)) {
                    $instance->$action(...$params);
                } else {
                    $instance->$action();
                }
            } else {
                throw new Exception("Action '{$action}' not found in controller '{$controllerClass}'");
            }
        } else {
            throw new Exception("Controller class '{$controllerClass}' not found");
        }
    } else {
        // Controller doesn't exist yet, show placeholder
        if (is_logged_in()) {
            http_response_code(404);
            echo "
            <!DOCTYPE html>
            <html>
            <head><title>Module Under Development</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
            </head>
            <body class='bg-light'>
            <div class='container py-5 text-center'>
                <div class='card shadow-sm mx-auto' style='max-width:500px'>
                    <div class='card-body p-5'>
                        <i class='bi bi-cone-striped display-1 text-warning'></i>
                        <h3 class='mt-3'>Module Under Development</h3>
                        <p class='text-muted'>The <strong>{$route['module']}</strong> module is being built.</p>
                        <a href='" . base_url('dashboard') . "' class='btn btn-primary'>
                            <i class='bi bi-arrow-left'></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            </body></html>";
        } else {
            redirect('/auth/login');
        }
    }
} catch (Exception $e) {
    error_log("FabX ERP Error: " . $e->getMessage());

    if (APP_ENV === 'development') {
        echo "<div style='padding:2rem;font-family:sans-serif;'>
            <h2 style='color:#e74c3c'>Error</h2>
            <p>" . $e->getMessage() . "</p>
            <pre>" . $e->getTraceAsString() . "</pre>
        </div>";
    } else {
        http_response_code(500);
        set_flash('error', 'An error occurred. Please try again.');
        redirect('/dashboard');
    }
}
