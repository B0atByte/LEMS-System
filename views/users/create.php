<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
</head>
<body>
    <?php $currentPage = 'users'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><i class="bi bi-person-plus-fill"></i> เพิ่มผู้ใช้ใหม่</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <?php if (isset($_SESSION['_flash']['error'])): ?>
                <div class="alert-professional alert-professional-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> <?= e($_SESSION['_flash']['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['_flash']['error']); ?>
            <?php endif; ?>

            <div class="professional-card">
                <div class="professional-card-body">
                    <form action="<?= url('users/store') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="fullname" class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required minlength="3">
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required minlength="4">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div class="form-text">รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร</div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- เลือกบทบาท --</option>
                                <option value="super_admin">Super Admin</option>
                                <option value="admin">Admin (ผู้จัดการ)</option>
                                <option value="officer">Officer (เจ้าหน้าที่ภาคสนาม)</option>
                                <option value="it">IT Support</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะ</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= url('users') ?>" class="btn-professional-outline">
                                <i class="bi bi-arrow-left"></i> ย้อนกลับ
                            </a>
                            <button type="submit" class="btn-professional">
                                <i class="bi bi-save"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
