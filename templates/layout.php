<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= $_SESSION['theme'] ?? 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generate_csrf() ?>">
    <title><?= $page_title ?? APP_NAME ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <link href="<?= asset('css/fabx-theme.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/custom.css') ?>" rel="stylesheet">
    
    <?= $extra_css ?? '' ?>
</head>
<body class="<?= $body_class ?? 'erp-body' ?> <?= ($_SESSION['sidebar_collapsed'] ?? false) ? 'sidebar-collapsed' : '' ?>">

    
    <?php if (($hide_sidebar ?? false) === false): ?>
        <!-- Sidebar -->
        <?php require_once FABX_ROOT . '/templates/sidebar.php'; ?>
        
        <!-- Main Content Wrapper -->
        <div class="main-wrapper <?= ($_SESSION['sidebar_collapsed'] ?? false) ? 'sidebar-collapsed' : '' ?>">
            <!-- Top Navbar -->
            <?php require_once FABX_ROOT . '/templates/header.php'; ?>
            
            <!-- Page Content -->
            <main class="main-content">
                <?= $breadcrumbs ?? '' ?>
                
                <!-- Flash Messages -->
                <?php foreach (get_flash() as $flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'x-circle' : ($flash['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle')) ?> me-2"></i>
                        <?= $flash['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
                
                <!-- Dynamic Content -->
                <?= $content ?>
            </main>
            
            <!-- Footer -->
            <?php require_once FABX_ROOT . '/templates/footer.php'; ?>
        </div>
    <?php else: ?>
        <?= $content ?>
    <?php endif; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (for Select2 and some plugins) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="<?= asset('js/fabx-app.js') ?>"></script>
    <script src="<?= asset('js/charts.js') ?>"></script>
    
    <?= $extra_js ?? '' ?>
</body>
</html>
