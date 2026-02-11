<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($pageTitle) ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
    <style>
        .menu-card-officer {
            transition: all 0.2s;
            border: 2px solid var(--border-gray);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background: white;
            color: var(--text-dark);
        }
        .menu-card-officer:active {
            transform: scale(0.98);
        }
        .menu-card-officer:hover {
            border-color: var(--primary-dark);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .menu-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--secondary-gray);
        }
    </style>
</head>
<body>
    <?php $currentPage = 'dashboard'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title">Dashboard - Officer</h1>
            </div>
            <div class="d-none d-md-block">
                <span class="text-muted me-2"><?= e($_SESSION['fullname']) ?></span>
            </div>
        </div>

        <div class="container-fluid p-4">
            <div class="text-center mb-5">
                <h3>ยินดีต้อนรับ, <?= e($_SESSION['fullname']) ?></h3>
                <p class="text-muted">เลือกเมนูเพื่อเริ่มปฏิบัติงาน</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- งานของฉัน -->
                <div class="col-6 col-md-4">
                    <a href="<?= url('my-assignments') ?>" class="text-decoration-none">
                        <div class="card menu-card-officer h-100 text-center p-4">
                            <i class="bi bi-list-task menu-icon"></i>
                            <h5>งานของฉัน</h5>
                            <small class="text-muted-professional">ดูรายการงานที่ได้รับมอบหมาย</small>
                        </div>
                    </a>
                </div>

                <!-- ประวัติงาน -->
                <div class="col-6 col-md-4">
                    <a href="<?= url('my-assignments?status=completed') ?>" class="text-decoration-none">
                        <div class="card menu-card-officer h-100 text-center p-4">
                            <i class="bi bi-clock-history menu-icon"></i>
                            <h5>ประวัติงาน</h5>
                            <small class="text-muted-professional">ดูงานที่ทำเสร็จแล้ว</small>
                        </div>
                    </a>
                </div>

                <!-- ข้อมูลส่วนตัว -->
                <div class="col-6 col-md-4">
                    <a href="<?= url('change-password') ?>" class="text-decoration-none">
                        <div class="card menu-card-officer h-100 text-center p-4">
                            <i class="bi bi-person-gear menu-icon"></i>
                            <h5>ตั้งค่า</h5>
                            <small class="text-muted-professional">เปลี่ยนรหัสผ่าน / ข้อมูลส่วนตัว</small>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-5 text-center text-muted-professional small">
                LEMS Bargainpoint - Officer Mobile Dashboard
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
