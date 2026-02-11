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
    <?php $currentPage = 'logs-login'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><i class="bi bi-person-badge"></i> ประวัติการเข้าสู่ระบบ</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <!-- Filters -->
            <div class="professional-card mb-4">
                <div class="professional-card-body">
                    <form action="<?= url('logs/login') ?>" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="username" class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= e($filters['username'] ?? '') ?>" placeholder="ค้นหา username...">
                            </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">สถานะ</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">ทั้งหมด</option>
                                <option value="success" <?= ($filters['status'] ?? '') === 'success' ? 'selected' : '' ?>>Success</option>
                                <option value="failed" <?= ($filters['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
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
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn-professional w-100 me-2">
                                <i class="bi bi-search"></i> ค้นหา
                            </button>
                            <a href="<?= url('logs/login') ?>" class="btn-professional-outline">
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
                                    <th>เวลา (Login Time)</th>
                                    <th>ผู้ใช้งาน (Username)</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>สถานะ</th>
                                    <th>หมายเหตุ (Reason)</th>
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
                                            <td><?= date('d/m/Y H:i:s', strtotime($log['login_time'])) ?></td>
                                            <td class="fw-bold"><?= e($log['username']) ?></td>
                                            <td><?= e($log['ip_address']) ?></td>
                                            <td title="<?= e($log['user_agent']) ?>">
                                                <?php
                                                $ua = $log['user_agent'];
                                                if (strpos($ua, 'Windows') !== false) echo '<i class="bi bi-windows"></i> Windows';
                                                elseif (strpos($ua, 'Mac') !== false) echo '<i class="bi bi-apple"></i> MacOS';
                                                elseif (strpos($ua, 'Linux') !== false) echo '<i class="bi bi-ubuntu"></i> Linux';
                                                elseif (strpos($ua, 'Android') !== false) echo '<i class="bi bi-android2"></i> Android';
                                                elseif (strpos($ua, 'iPhone') !== false) echo '<i class="bi bi-phone"></i> iOS';
                                                else echo 'Unknown';

                                                if (strpos($ua, 'Chrome') !== false) echo ' (Chrome)';
                                                elseif (strpos($ua, 'Firefox') !== false) echo ' (Firefox)';
                                                elseif (strpos($ua, 'Edge') !== false) echo ' (Edge)';
                                                elseif (strpos($ua, 'Safari') !== false) echo ' (Safari)';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($log['status'] === 'success'): ?>
                                                    <span class="badge-professional">Success</span>
                                                <?php else: ?>
                                                    <span class="badge-professional">Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= e($log['failure_reason'] ?? '-') ?></td>
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
                                <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&username=<?= $filters['username'] ?>&status=<?= $filters['status'] ?>&date_from=<?= $filters['date_from'] ?>&date_to=<?= $filters['date_to'] ?>">Previous</a>
                            </li>
                            <li class="page-item active"><span class="page-link"><?= $pagination['current_page'] ?></span></li>
                            <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&username=<?= $filters['username'] ?>&status=<?= $filters['status'] ?>&date_from=<?= $filters['date_from'] ?>&date_to=<?= $filters['date_to'] ?>">Next</a>
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
