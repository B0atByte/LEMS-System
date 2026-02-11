<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'LEMS') ?> - LEMS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Professional Theme -->
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">

    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body class="bg-light-professional">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-professional">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('dashboard') ?>">
                LEMS Bargainpoint
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('dashboard') ?>">Dashboard</a>
                    </li>
                    <?php if (in_array($_SESSION['user_role'] ?? '', ['super_admin', 'admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('users') ?>">จัดการผู้ใช้</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('cases') ?>">จัดการคดี</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('assignments') ?>">มอบหมายงาน</a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($_SESSION['user_role'] ?? '', ['super_admin', 'it'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('logs/activity') ?>">Logs</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?= e($_SESSION['fullname'] ?? 'User') ?> (<?= e($_SESSION['user_role'] ?? '') ?>)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('change-password') ?>">เปลี่ยนรหัสผ่าน</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('logout') ?>">ออกจากระบบ</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <?php if (isset($pageHeader)): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0" style="font-weight: 600; color: var(--text-dark);">
                    <?= $pageHeader ?>
                </h2>
                <?php if (isset($headerActions)): ?>
                    <?= $headerActions ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['_flash']['success'])): ?>
            <div class="alert alert-professional alert-professional-success alert-dismissible fade show" role="alert">
                <?= e($_SESSION['_flash']['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['_flash']['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['_flash']['error'])): ?>
            <div class="alert alert-professional alert-professional-danger alert-dismissible fade show" role="alert">
                <?= e($_SESSION['_flash']['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['_flash']['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['_flash']['warning'])): ?>
            <div class="alert alert-professional alert-professional-warning alert-dismissible fade show" role="alert">
                <?= e($_SESSION['_flash']['warning']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['_flash']['warning']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['_flash']['info'])): ?>
            <div class="alert alert-professional alert-professional-info alert-dismissible fade show" role="alert">
                <?= e($_SESSION['_flash']['info']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['_flash']['info']); ?>
        <?php endif; ?>

        <!-- Page Content -->
        <?= $content ?? '' ?>
    </div>

    <!-- Footer -->
    <div class="footer-professional">
        <div class="container">
            <p class="mb-0">LEMS Bargainpoint v1.0 Enterprise Edition</p>
            <small>&copy; <?= date('Y') ?> Bargainpoint. All rights reserved.</small>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>
</body>
</html>
