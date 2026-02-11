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
                    <h1 class="page-title"><i class="bi bi-person-lines-fill"></i> รายละเอียดผู้ใช้</h1>
                </div>
                <div>
                    <a href="<?= url('users/' . $user['id'] . '/edit') ?>" class="btn-professional">
                        <i class="bi bi-pencil"></i> แก้ไขข้อมูล
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="professional-card">
                        <div class="professional-card-header">
                            <h5 class="mb-0">ข้อมูลบัญชีผู้ใช้: <?= e($user['username']) ?></h5>
                        </div>
                        <div class="professional-card-body">
                            <div class="table-responsive">
                                <table class="table-professional">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%;">ID</th>
                                            <td><?= e($user['id']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>ชื่อ-นามสกุล</th>
                                            <td><?= e($user['fullname']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Username</th>
                                            <td><?= e($user['username']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Role</th>
                                            <td>
                                                <?php
                                                $roleNames = [
                                                    'super_admin' => 'Super Admin',
                                                    'it' => 'IT Support',
                                                    'admin' => 'Admin (ผู้จัดการ)',
                                                    'officer' => 'Officer (เจ้าหน้าที่ภาคสนาม)'
                                                ];
                                                $roleLabel = $roleNames[$user['role']] ?? $user['role'];
                                                ?>
                                                <span class="badge-professional"><?= e($roleLabel) ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>สถานะ</th>
                                            <td>
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <span class="badge-professional">Active</span>
                                                <?php else: ?>
                                                    <span class="badge-professional">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>เข้าใช้งานล่าสุด</th>
                                            <td>
                                                <?php if ($user['last_login']): ?>
                                                    <?= date('d/m/Y H:i:s', strtotime($user['last_login'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted-professional">- ไม่เคยเข้าใช้งาน -</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>วันที่สร้าง</th>
                                            <td>
                                                <?php if (isset($user['created_at'])): ?>
                                                    <?= date('d/m/Y H:i:s', strtotime($user['created_at'])) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>แก้ไขล่าสุด</th>
                                            <td>
                                                <?php if (isset($user['updated_at'])): ?>
                                                    <?= date('d/m/Y H:i:s', strtotime($user['updated_at'])) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="professional-card-footer text-center py-3">
                            <a href="<?= url('users') ?>" class="btn-professional-outline px-4">
                                <i class="bi bi-arrow-left"></i> กลับหน้ารายการ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
