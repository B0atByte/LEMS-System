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
    <?php $currentPage = 'logs-activity'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><i class="bi bi-clock-history"></i> ประวัติการใช้งาน</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <!-- Filters -->
            <div class="professional-card mb-4">
                <div class="professional-card-body">
                    <form action="<?= url('logs/activity') ?>" method="GET" class="row g-3">
                        <div class="col-md-2">
                            <label for="username" class="form-label">ผู้ใช้งาน</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= e($filters['username'] ?? '') ?>" placeholder="Username...">
                        </div>
                        <div class="col-md-2">
                            <label for="module" class="form-label">โมดูล</label>
                            <select class="form-select" id="module" name="module">
                                <option value="">ทั้งหมด</option>
                                <option value="users" <?= ($filters['module'] ?? '') === 'users' ? 'selected' : '' ?>>Users</option>
                                <option value="cases" <?= ($filters['module'] ?? '') === 'cases' ? 'selected' : '' ?>>Cases</option>
                                <option value="assignments" <?= ($filters['module'] ?? '') === 'assignments' ? 'selected' : '' ?>>Assignments</option>
                                <option value="field_reports" <?= ($filters['module'] ?? '') === 'field_reports' ? 'selected' : '' ?>>Field Reports</option>
                                <option value="auth" <?= ($filters['module'] ?? '') === 'auth' ? 'selected' : '' ?>>Auth</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="action_type" class="form-label">การกระทำ</label>
                            <select class="form-select" id="action_type" name="action_type">
                                <option value="">ทั้งหมด</option>
                                <option value="CREATE" <?= ($filters['action_type'] ?? '') === 'CREATE' ? 'selected' : '' ?>>CREATE</option>
                                <option value="UPDATE" <?= ($filters['action_type'] ?? '') === 'UPDATE' ? 'selected' : '' ?>>UPDATE</option>
                                <option value="DELETE" <?= ($filters['action_type'] ?? '') === 'DELETE' ? 'selected' : '' ?>>DELETE</option>
                                <option value="LOGIN" <?= ($filters['action_type'] ?? '') === 'LOGIN' ? 'selected' : '' ?>>LOGIN</option>
                                <option value="UPLOAD" <?= ($filters['action_type'] ?? '') === 'UPLOAD' ? 'selected' : '' ?>>UPLOAD</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">ตั้งแต่วันที่</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="<?= e($filters['date_from'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">ถึงวันที่</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="<?= e($filters['date_to'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn-professional w-100 me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="<?= url('logs/activity') ?>" class="btn-professional-outline">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="professional-card">
                <div class="professional-card-body p-0">
                    <div class="table-responsive">
                        <table class="table-professional">
                            <thead>
                                <tr>
                                    <th style="width: 15%">เวลา</th>
                                    <th style="width: 15%">ผู้ใช้งาน</th>
                                    <th style="width: 10%">Action</th>
                                    <th style="width: 10%">Module</th>
                                    <th style="width: 40%">รายละเอียด</th>
                                    <th style="width: 10%">Tools</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted-professional">ไม่พบข้อมูล</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                            <td>
                                                <div class="fw-bold"><?= e($log['username']) ?></div>
                                                <small class="text-muted-professional"><?= e($log['ip_address']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge-professional">
                                                    <?= e($log['action_type']) ?>
                                                </span>
                                            </td>
                                            <td><span class="badge-professional"><?= e($log['module']) ?></span></td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 400px;" title="<?= e($log['description']) ?>">
                                                    <?= e($log['description']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($log['action_type'] === 'UPDATE' || $log['old_data'] || $log['new_data']): ?>
                                                    <a href="<?= url('logs/audit/' . $log['module'] . '/' . $log['reference_id']) ?>" class="btn-professional-light btn-sm" title="ดูประวัติการแก้ไข">
                                                        <i class="bi bi-clock-history"></i> Diff
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="professional-card-footer d-flex justify-content-between align-items-center">
                     <small class="text-muted-professional">
                        แสดง <?= ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 ?> ถึง <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?> จาก <?= $pagination['total'] ?> รายการ
                    </small>

                    <?php if ($pagination['total_pages'] > 1): ?>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&username=<?= $filters['username'] ?>&module=<?= $filters['module'] ?>&action_type=<?= $filters['action_type'] ?>&date_from=<?= $filters['date_from'] ?>&date_to=<?= $filters['date_to'] ?>">Previous</a>
                            </li>
                            <li class="page-item active"><span class="page-link"><?= $pagination['current_page'] ?></span></li>
                            <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&username=<?= $filters['username'] ?>&module=<?= $filters['module'] ?>&action_type=<?= $filters['action_type'] ?>&date_from=<?= $filters['date_from'] ?>&date_to=<?= $filters['date_to'] ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
