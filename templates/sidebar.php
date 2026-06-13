<?php
/**
 * FabX ERP - Sidebar Navigation
 * Role-based menu rendering
 */

$menuItems = [
    [
        'label' => 'Dashboard',
        'icon' => 'speedometer2',
        'url' => '/dashboard',
        'permission' => 'read',
        'badge' => null
    ],
    [
        'label' => 'QMS / ISO 9001',
        'icon' => 'shield-check',
        'permission' => 'read',
        'submenu' => [
            ['label' => 'Dashboard', 'url' => '/qms/dashboard', 'icon' => 'grid'],
            ['label' => 'Documents', 'url' => '/qms/documents', 'icon' => 'files'],
            ['label' => 'NCR', 'url' => '/qms/ncr', 'icon' => 'exclamation-triangle'],
            ['label' => 'CAPA', 'url' => '/qms/capa', 'icon' => 'arrow-repeat'],
            ['label' => 'Internal Audits', 'url' => '/qms/audits', 'icon' => 'clipboard-check'],
            ['label' => 'Calibration', 'url' => '/qms/calibration', 'icon' => 'thermometer'],
            ['label' => 'Training', 'url' => '/qms/training', 'icon' => 'mortarboard'],
            ['label' => 'Complaints', 'url' => '/qms/complaints', 'icon' => 'chat-left-text'],
            ['label' => 'Risk Assessment', 'url' => '/qms/risks', 'icon' => 'diagram-3'],
            ['label' => 'Management Review', 'url' => '/qms/reviews', 'icon' => 'people'],
            ['label' => 'KPI & Objectives', 'url' => '/qms/kpi', 'icon' => 'bar-chart'],
        ]
    ],
    [
        'label' => 'Projects',
        'icon' => 'kanban',
        'permission' => 'read',
        'submenu' => [
            ['label' => 'All Projects', 'url' => '/projects', 'icon' => 'list'],
            ['label' => 'Gantt Chart', 'url' => '/projects/gantt', 'icon' => 'calendar-week'],
            ['label' => 'BOQ', 'url' => '/projects/boq', 'icon' => 'table'],
            ['label' => 'Work Orders', 'url' => '/projects/work-orders', 'icon' => 'wrench'],
            ['label' => 'Production Report', 'url' => '/projects/production', 'icon' => 'factory'],
            ['label' => 'Drawings', 'url' => '/projects/drawings', 'icon' => 'image'],
        ]
    ],
    [
        'label' => 'CRM',
        'icon' => 'people-fill',
        'permission' => 'read',
        'submenu' => [
            ['label' => 'Leads', 'url' => '/crm/leads', 'icon' => 'bullseye'],
            ['label' => 'Inquiries', 'url' => '/crm/inquiries', 'icon' => 'envelope'],
            ['label' => 'Quotations', 'url' => '/crm/quotations', 'icon' => 'file-text'],
            ['label' => 'Follow-ups', 'url' => '/crm/followups', 'icon' => 'calendar-check'],
            ['label' => 'Sales Pipeline', 'url' => '/crm/pipeline', 'icon' => 'funnel'],
        ]
    ],
    [
        'label' => 'Clients',
        'icon' => 'building',
        'permission' => 'read',
        'submenu' => [
            ['label' => 'All Clients', 'url' => '/clients', 'icon' => 'list'],
            ['label' => 'Support Tickets', 'url' => '/clients/tickets', 'icon' => 'ticket-perforated'],
            ['label' => 'AMC Contracts', 'url' => '/clients/amc', 'icon' => 'shield-check'],
        ]
    ],
    [
        'label' => 'Vendors',
        'icon' => 'truck',
        'url' => '/vendors',
        'permission' => 'read',
    ],
    [
        'label' => 'Purchase',
        'icon' => 'cart',
        'permission' => 'read',
        'submenu' => [
            ['label' => 'Purchase Requisitions', 'url' => '/purchase/requisitions', 'icon' => 'file-earmark'],
            ['label' => 'Purchase Orders', 'url' => '/purchase/orders', 'icon' => 'bag'],
            ['label' => 'GRN', 'url' => '/purchase/grn', 'icon' => 'box-seam'],
            ['label' => 'Inventory', 'url' => '/purchase/inventory', 'icon' => 'box'],
            ['label' => 'Material Issue', 'url' => '/purchase/issues', 'icon' => 'arrow-up-circle'],
        ]
    ],
    [
        'label' => 'Accounts',
        'icon' => 'cash-stack',
        'permission' => 'read',
        'submenu' => [
            ['label' => 'Invoices', 'url' => '/accounts/invoices', 'icon' => 'receipt'],
            ['label' => 'Payments', 'url' => '/accounts/payments', 'icon' => 'credit-card'],
            ['label' => 'Expenses', 'url' => '/accounts/expenses', 'icon' => 'wallet'],
            ['label' => 'Vendor Payments', 'url' => '/accounts/vendor-payments', 'icon' => 'cash'],
            ['label' => 'Delivery Challans', 'url' => '/accounts/delivery-challans', 'icon' => 'truck'],
            ['label' => 'Ledger', 'url' => '/accounts/ledger', 'icon' => 'book'],
            ['label' => 'GST Summary', 'url' => '/accounts/gst', 'icon' => 'percent'],
        ]
    ],
    [
        'label' => 'HR',
        'icon' => 'person-badge',
        'permission' => 'read',
        'submenu' => [
            ['label' => 'Employees', 'url' => '/hr/employees', 'icon' => 'people'],
            ['label' => 'Attendance', 'url' => '/hr/attendance', 'icon' => 'calendar-week'],
            ['label' => 'Leaves', 'url' => '/hr/leaves', 'icon' => 'calendar-x'],
            ['label' => 'Training', 'url' => '/hr/training', 'icon' => 'mortarboard'],
            ['label' => 'Appraisals', 'url' => '/hr/appraisals', 'icon' => 'graph-up'],
        ]
    ],
    [
        'label' => 'Files',
        'icon' => 'folder',
        'url' => '/files',
        'permission' => 'read',
    ],
    [
        'label' => 'Reports',
        'icon' => 'graph-up-arrow',
        'permission' => 'read',
        'submenu' => [
            ['label' => 'Production', 'url' => '/reports/production', 'icon' => 'gear'],
            ['label' => 'Quality', 'url' => '/reports/quality', 'icon' => 'shield-check'],
            ['label' => 'Sales', 'url' => '/reports/sales', 'icon' => 'cart'],
            ['label' => 'Inventory', 'url' => '/reports/inventory', 'icon' => 'box'],
            ['label' => 'Finance', 'url' => '/reports/finance', 'icon' => 'cash-stack'],
        ]
    ],
];

$adminItems = [
    [
        'label' => 'Administration',
        'icon' => 'gear-wide-connected',
        'permission' => 'admin',
        'submenu' => [
            ['label' => 'Users', 'url' => '/admin/users', 'icon' => 'person-gear'],
            ['label' => 'Roles', 'url' => '/admin/roles', 'icon' => 'key'],
            ['label' => 'Departments', 'url' => '/admin/departments', 'icon' => 'building'],
            ['label' => 'Settings', 'url' => '/admin/settings', 'icon' => 'sliders'],
            ['label' => 'Activity Logs', 'url' => '/admin/logs', 'icon' => 'clock-history'],
            ['label' => 'Master Setup', 'url' => '/admin/master-setup', 'icon' => 'sliders'],
            ['label' => 'Backup', 'url' => '/admin/backup', 'icon' => 'cloud-arrow-down'],
        ]
    ]
];

$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
?>

<aside class="sidebar" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <a href="<?= base_url('dashboard') ?>" class="brand-link">
            <div class="brand-logo">
                <i class="bi bi-hexagon-fill"></i>
            </div>
            <div class="brand-text">
                <span class="brand-name">FabX</span>
                <span class="brand-tagline">Engineering ERP</span>
            </div>
        </a>
        <button class="sidebar-toggle d-lg-none" id="mobileSidebarToggle">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <!-- User Info (Mobile) -->
    <div class="sidebar-user d-lg-none">
        <div class="user-avatar">
            <?php if (!empty($user_avatar)): ?>
                <img src="<?= base_url($user_avatar) ?>" alt="">
            <?php else: ?>
                <span><?= strtoupper(substr($user_name ?? 'U', 0, 1)) ?></span>
            <?php endif; ?>
        </div>
        <div class="user-info">
            <span class="user-name"><?= $user_name ?? 'User' ?></span>
            <span class="user-role"><?= $user_role ?? '' ?></span>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <?php foreach ($menuItems as $item): ?>
                <?php 
                $hasSubmenu = !empty($item['submenu']);
                $isActive = false;
                $isOpen = false;
                
                if ($hasSubmenu) {
                    foreach ($item['submenu'] as $sub) {
                        $subPath = parse_url(base_url($sub['url']), PHP_URL_PATH);
                        if ($subPath && str_contains($currentUrl, $subPath)) {
                            $isActive = true;
                            $isOpen = true;
                            break;
                        }
                    }
                } else {
                    $path = parse_url(base_url($item['url']), PHP_URL_PATH);

                    $isActive = $path && str_contains($currentUrl, $path);
                }
                ?>
                
                <li class="nav-item <?= $hasSubmenu ? 'has-submenu' : '' ?> <?= $isActive ? 'active' : '' ?> <?= $isOpen ? 'open' : '' ?>">
                    <?php if ($hasSubmenu): ?>
                        <a href="#" class="nav-link submenu-toggle">
                            <i class="bi bi-<?= $item['icon'] ?>"></i>
                            <span><?= $item['label'] ?></span>
                            <i class="bi bi-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu <?= $isOpen ? 'show' : '' ?>">
                            <?php foreach ($item['submenu'] as $sub): ?>
                                <li class="<?= str_contains($currentUrl, parse_url(base_url($sub['url']), PHP_URL_PATH) ?: '') ? 'active' : '' ?>">
                                    <a href="<?= base_url($sub['url']) ?>">
                                        <i class="bi bi-<?= $sub['icon'] ?>"></i>
                                        <?= $sub['label'] ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <a href="<?= base_url($item['url']) ?>" class="nav-link">
                            <i class="bi bi-<?= $item['icon'] ?>"></i>
                            <span><?= $item['label'] ?></span>
                            <?php if (!empty($item['badge'])): ?>
                                <span class="badge bg-danger"><?= $item['badge'] ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            
            <?php if (($user_role ?? '') === 'Super Admin'): ?>
                <li class="nav-divider">
                    <span>ADMINISTRATION</span>
                </li>
                <?php foreach ($adminItems as $item): ?>
                    <?php
                    $hasSubmenu = !empty($item['submenu']);
                    $isOpen = false;
                    if ($hasSubmenu) {
                        foreach ($item['submenu'] as $sub) {
                            if (str_contains($currentUrl, $sub['url'])) {
                                $isOpen = true;
                                break;
                            }
                        }
                    }
                    ?>
                    <li class="nav-item has-submenu <?= $isOpen ? 'open' : '' ?>">
                        <a href="#" class="nav-link submenu-toggle">
                            <i class="bi bi-<?= $item['icon'] ?>"></i>
                            <span><?= $item['label'] ?></span>
                            <i class="bi bi-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu <?= $isOpen ? 'show' : '' ?>">
                            <?php foreach ($item['submenu'] as $sub): ?>
                                <li>
                                    <a href="<?= base_url($sub['url']) ?>">
                                        <i class="bi bi-<?= $sub['icon'] ?>"></i>
                                        <?= $sub['label'] ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="version-info">
            <span>v<?= APP_VERSION ?></span>
            <span class="iso-badge"><i class="bi bi-shield-check"></i> ISO 9001</span>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
