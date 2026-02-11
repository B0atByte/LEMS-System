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
                    <h1 class="page-title"><i class="bi bi-person-gear"></i> แก้ไขข้อมูลผู้ใช้</h1>
                </div>
                <div>
                    <a href="<?= url('users/' . $user['id']) ?>" class="btn-professional-light">
                        <i class="bi bi-eye"></i> ดูข้อมูล
                    </a>
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

            <?php if (isset($_SESSION['_flash']['success'])): ?>
                <div class="alert-professional alert-professional-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> <?= e($_SESSION['_flash']['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['_flash']['success']); ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="professional-card">
                        <div class="professional-card-header">
                            <h5 class="mb-0">ข้อมูลพื้นฐาน</h5>
                        </div>
                        <div class="professional-card-body">
                        <form action="<?= url('users/' . $user['id'] . '/update') ?>" method="POST">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="fullname" class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fullname" name="fullname" value="<?= e($user['fullname']) ?>" required minlength="3">
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= e($user['username']) ?>" required minlength="4">
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">-- เลือกบทบาท --</option>
                                    <option value="super_admin" <?= $user['role'] == 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin (ผู้จัดการ)</option>
                                    <option value="officer" <?= $user['role'] == 'officer' ? 'selected' : '' ?>>Officer (เจ้าหน้าที่ภาคสนาม)</option>
                                    <option value="it" <?= $user['role'] == 'it' ? 'selected' : '' ?>>IT Support</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">สถานะ</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="<?= url('users') ?>" class="btn-professional-outline">
                                        <i class="bi bi-arrow-left"></i> ย้อนกลับ
                                    </a>
                                    <button type="submit" class="btn-professional">
                                        <i class="bi bi-save"></i> บันทึกการเปลี่ยนแปลง
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="professional-card mb-3">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-key"></i> จัดการรหัสผ่าน</h5>
                        </div>
                        <div class="professional-card-body">
                            <p class="text-muted-professional small">
                                การรีเซ็ตรหัสผ่านจะตั้งค่าเป็น: <strong>Admin@123</strong>
                            </p>
                            <form action="<?= url('users/' . $user['id'] . '/reset-password') ?>" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะรีเซ็ตรหัสผ่าน?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-professional w-100">
                                    <i class="bi bi-arrow-clockwise"></i> รีเซ็ตรหัสผ่าน
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="professional-card">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-trash"></i> ลบผู้ใช้งาน</h5>
                        </div>
                        <div class="professional-card-body">
                            <p class="text-muted-professional small">
                                การลบผู้ใช้งานจะไม่สามารถกู้คืนได้ และอาจส่งผลต่อประวัติการทำงาน
                            </p>
                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                <div class="alert-professional alert-professional-warning py-1 small">คุณไม่สามารถลบบัญชีตัวเองได้</div>
                            <?php else: ?>
                                <form action="<?= url('users/' . $user['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้งานนี้ถาวร?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-professional-outline w-100">
                                        <i class="bi bi-trash"></i> ลบผู้ใช้งาน
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
