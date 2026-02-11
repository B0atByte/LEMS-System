<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'จัดการผู้ใช้') ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
</head>
<body>
    <?php $currentPage = 'users'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content-with-sidebar">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title">จัดการผู้ใช้</h1>
                </div>
                <a href="<?= url('users/create') ?>" class="btn btn-professional">
                    เพิ่มผู้ใช้ใหม่
                </a>
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

            <?php if (isset($_SESSION['_flash']['error'])): ?>
                <div class="alert alert-professional alert-professional-danger alert-dismissible fade show">
                    <?= e($_SESSION['_flash']['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['_flash']['error']); ?>
            <?php endif; ?>

            <div class="professional-card">
                <div class="table-responsive">
                    <table class="table table-professional">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ชื่อ-สกุล</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th width="200">การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr><td colspan="7" class="text-center text-muted-professional">ไม่มีข้อมูล</td></tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= e($user['id']) ?></td>
                                        <td><?= e($user['fullname']) ?></td>
                                        <td><?= e($user['username']) ?></td>
                                        <td>
                                            <span class="badge-professional"><?= e($user['role']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($user['status'] === 'active'): ?>
                                                <span class="badge-professional">Active</span>
                                            <?php else: ?>
                                                <span class="badge-professional badge-professional-light">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <small><?= date('d/m/Y H:i', strtotime($user['last_login'])) ?></small>
                                            <?php else: ?>
                                                <small class="text-muted-professional">ไม่เคย login</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= url('users/' . $user['id']) ?>" class="btn btn-professional-outline btn-sm" title="ดู">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= url('users/' . $user['id'] . '/edit') ?>" class="btn btn-professional-outline btn-sm" title="แก้ไข">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                    <button class="btn btn-professional-outline btn-sm" onclick="confirmDelete(<?= $user['id'] ?>)" title="ลบ">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <form id="deleteForm" method="POST" style="display: none;">
        <?= csrf_field() ?>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(userId) {
        if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?')) {
            const form = document.getElementById('deleteForm');
            form.action = '<?= url('') ?>' + '/users/' + userId + '/delete';
            form.submit();
        }
    }
    </script>
</body>
</html>
