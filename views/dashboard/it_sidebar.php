<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
</head>
<body>
    <?php $currentPage = 'dashboard'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content-with-sidebar">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title">Dashboard - <?= e($_SESSION['user_role']) ?></h1>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            <?php if (isset($_SESSION['_flash']['success'])): ?>
                <div class="alert alert-professional alert-professional-success alert-dismissible fade show">
                    <?= e($_SESSION['_flash']['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['_flash']['success']); ?>
            <?php endif; ?>

            <!-- Welcome Message -->
            <div class="professional-card mb-4">
                <h5>ยินดีต้อนรับ, <?= e($_SESSION['fullname']) ?></h5>
                <p class="text-muted-professional mb-0">ระบบจัดการงานบังคับคดี LEMS Bargainpoint</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card-professional text-center">
                        <div class="stat-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number">-</div>
                        <div class="stat-label">ผู้ใช้ทั้งหมด</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-professional text-center">
                        <div class="stat-icon">
                            <i class="bi bi-folder-fill"></i>
                        </div>
                        <div class="stat-number">-</div>
                        <div class="stat-label">คดีทั้งหมด</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-professional text-center">
                        <div class="stat-icon">
                            <i class="bi bi-clipboard-check-fill"></i>
                        </div>
                        <div class="stat-number">-</div>
                        <div class="stat-label">งานที่มอบหมาย</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-professional text-center">
                        <div class="stat-icon">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div class="stat-number">-</div>
                        <div class="stat-label">รายงานภาคสนาม</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="professional-card">
                        <h5>เมนูด่วน</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="<?= url('users/create') ?>" class="quick-action-professional">
                                    <i class="bi bi-person-plus-fill"></i>
                                    <span>เพิ่มผู้ใช้ใหม่</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= url('cases/create') ?>" class="quick-action-professional">
                                    <i class="bi bi-folder-plus"></i>
                                    <span>เพิ่มคดีใหม่</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= url('logs/activity') ?>" class="quick-action-professional">
                                    <i class="bi bi-clock-history"></i>
                                    <span>ดู Activity Logs</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= url('reports') ?>" class="quick-action-professional">
                                    <i class="bi bi-file-earmark-excel"></i>
                                    <span>Export รายงาน</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-professional alert-professional-info">
                        <h6 class="mb-3" style="font-weight: 600; text-transform: uppercase; font-size: 13px; letter-spacing: 0.3px;">
                            ข้อมูลระบบ
                        </h6>
                        <p class="mb-2"><strong>ผู้ใช้งาน:</strong> <?= e($_SESSION['fullname']) ?> (<?= e($_SESSION['user_role']) ?>)</p>
                        <p class="mb-2"><strong>เวลาเข้าสู่ระบบ:</strong> <?= date('d/m/Y H:i:s', $_SESSION['login_time'] ?? time()) ?> น.</p>
                        <p class="mb-0"><strong>เวอร์ชัน:</strong> LEMS Bargainpoint v1.0 Enterprise Edition</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
