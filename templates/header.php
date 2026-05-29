<?php
/**
 * FabX ERP - Top Header Navbar
 */
?>

<header class="top-navbar">
    <div class="navbar-left">
        <!-- Sidebar toggle: collapses on desktop, opens drawer on mobile -->
        <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>
        
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="bi bi-house-door"></i></a></li>
                <?php if (!empty($breadcrumb_module)): ?>
                    <li class="breadcrumb-item active"><?= $breadcrumb_module ?></li>
                <?php endif; ?>
                <?php if (!empty($breadcrumb_page)): ?>
                    <li class="breadcrumb-item active"><?= $breadcrumb_page ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
    
    <div class="navbar-right">
        <!-- Search -->
        <div class="nav-search d-none d-lg-block">
            <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Search projects, documents, invoices..." id="globalSearch">
                <kbd>Ctrl + K</kbd>
            </div>
        </div>
        
        <!-- Theme Toggle -->
        <button class="nav-icon-btn" id="themeToggle" title="Toggle Theme">
            <i class="bi bi-<?= ($_SESSION['theme'] ?? 'light') === 'light' ? 'moon-stars' : 'sun' ?>"></i>
        </button>
        
        <!-- Notifications -->
        <div class="dropdown">
            <button class="nav-icon-btn position-relative" data-bs-toggle="dropdown" id="notificationBtn">
                <i class="bi bi-bell"></i>
                <?php if (($unread_notifications ?? 0) > 0): ?>
                    <span class="notification-badge"><?= $unread_notifications ?></span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown shadow">
                <div class="notification-header">
                    <h6 class="mb-0">Notifications</h6>
                    <a href="#" class="text-small" onclick="markAllRead()">Mark all read</a>
                </div>
                <div class="notification-list">
                    <?php if (!empty($notifications)): ?>
                        <?php foreach ($notifications as $notif): ?>
                            <a href="<?= $notif['link'] ?? '#' ?>" class="notification-item <?= empty($notif['is_read']) ? 'unread' : '' ?>">
                                <div class="notification-icon bg-<?= $notif['type'] ?>">
                                    <i class="bi bi-<?= match($notif['type']) {
                                        'success' => 'check-circle',
                                        'warning' => 'exclamation-triangle',
                                        'danger' => 'x-circle',
                                        default => 'info-circle'
                                    } ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <p class="notification-title"><?= $notif['title'] ?></p>
                                    <p class="notification-text"><?= truncate($notif['message'] ?? '', 60) ?></p>
                                    <span class="notification-time"><?= time_ago($notif['created_at']) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="notification-empty">
                            <i class="bi bi-bell-slash"></i>
                            <p>No notifications</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="notification-footer">
                    <a href="<?= base_url('notifications') ?>">View All Notifications</a>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="dropdown d-none d-md-block">
            <button class="nav-icon-btn" data-bs-toggle="dropdown">
                <i class="bi bi-plus-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow">
                <h6 class="dropdown-header">Quick Actions</h6>
                <a class="dropdown-item" href="<?= base_url('projects/create') ?>"><i class="bi bi-kanban me-2"></i> New Project</a>
                <a class="dropdown-item" href="<?= base_url('crm/quotations/create') ?>"><i class="bi bi-file-text me-2"></i> New Quotation</a>
                <a class="dropdown-item" href="<?= base_url('accounts/invoices/create') ?>"><i class="bi bi-receipt me-2"></i> New Invoice</a>
                <a class="dropdown-item" href="<?= base_url('purchase/orders/create') ?>"><i class="bi bi-bag me-2"></i> New PO</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= base_url('qms/ncr/create') ?>"><i class="bi bi-exclamation-triangle me-2"></i> Raise NCR</a>
                <a class="dropdown-item" href="<?= base_url('purchase/requisitions/create') ?>"><i class="bi bi-file-earmark me-2"></i> New PR</a>
            </div>
        </div>
        
        <!-- User Dropdown -->
        <div class="dropdown user-dropdown">
            <button class="user-toggle" data-bs-toggle="dropdown">
                <div class="user-avatar-sm">
                    <?php if (!empty($user_avatar)): ?>
                        <img src="<?= base_url($user_avatar) ?>" alt="">
                    <?php else: ?>
                        <span><?= strtoupper(substr($user_name ?? 'U', 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <div class="user-info d-none d-md-block">
                    <span class="user-name"><?= explode(' ', $user_name ?? 'User')[0] ?></span>
                    <span class="user-dept"><?= $user_department ?? '' ?></span>
                </div>
                <i class="bi bi-chevron-down d-none d-md-block"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow">
                <div class="dropdown-header">
                    <span class="fw-semibold"><?= $user_name ?? 'User' ?></span>
                    <small class="text-muted d-block"><?= $user_role ?? '' ?></small>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= base_url('auth/profile') ?>"><i class="bi bi-person me-2"></i> My Profile</a>
                <a class="dropdown-item" href="<?= base_url('auth/profile#settings') ?>"><i class="bi bi-gear me-2"></i> Account Settings</a>
                <a class="dropdown-item" href="<?= base_url('help') ?>"><i class="bi bi-question-circle me-2"></i> Help Center</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</header>
