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
    <?php $currentPage = 'assignments'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><i class="bi bi-list-task"></i> รายการงานที่มอบหมาย</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <?php if (isset($_SESSION['_flash']['success'])): ?>
                <div class="alert-professional alert-professional-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> <?= e($_SESSION['_flash']['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['_flash']['success']); ?>
            <?php endif; ?>

            <!-- Filters -->
            <div class="professional-card mb-4">
                <div class="professional-card-body">
                    <form action="<?= url('assignments') ?>" method="GET" class="row g-3">
                        <?php if ($_SESSION['user_role'] != 'officer'): ?>
                        <div class="col-md-4">
                            <label for="officer_id" class="form-label">เจ้าหน้าที่</label>
                            <select class="form-select" id="officer_id" name="officer_id">
                                <option value="">-- ทั้งหมด --</option>
                                 <?php if (isset($officers)): ?>
                                    <?php foreach ($officers as $officer): ?>
                                        <option value="<?= $officer['id'] ?>" <?= ($filters['officer_id'] ?? '') == $officer['id'] ? 'selected' : '' ?>>
                                            <?= e($officer['fullname']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <label for="status" class="form-label">สถานะงาน</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">-- ทั้งหมด --</option>
                                <option value="assigned" <?= ($filters['status'] ?? '') == 'assigned' ? 'selected' : '' ?>>Assigned (รอดำเนินการ)</option>
                                <option value="in_progress" <?= ($filters['status'] ?? '') == 'in_progress' ? 'selected' : '' ?>>In Progress (กำลังทำ)</option>
                                <option value="completed" <?= ($filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Completed (เสร็จสิ้น)</option>
                                <option value="cancelled" <?= ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled (ยกเลิก)</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn-professional w-100">
                                <i class="bi bi-filter"></i> กรองข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="professional-card">
                <div class="professional-card-body p-0">
                    <div class="table-responsive">
                        <table class="table-professional">
                            <thead>
                                <tr>
                                    <th>วันที่มอบหมาย</th>
                                    <th>เลขที่สัญญา / คดีแดง</th>
                                    <th>ลูกหนี้</th>
                                    <th>เจ้าหน้าที่ผู้รับผิดชอบ</th>
                                    <th>สถานะ</th>
                                    <th>ดำเนินการล่าสุด</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($assignments)): ?>
                                    <tr><td colspan="7" class="text-center py-4 text-muted-professional">ไม่พบข้อมูล</td></tr>
                                <?php else: ?>
                                    <?php foreach ($assignments as $assign): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($assign['assigned_date'])) ?></td>
                                            <td>
                                                <a href="<?= url('cases/' . $assign['case_id']) ?>" class="text-decoration-none fw-bold">
                                                    <?= e($assign['contract_no']) ?>
                                                </a>
                                                <div class="small text-muted-professional"><?= e($assign['red_case'] ?: '-') ?></div>
                                            </td>
                                            <td><?= e($assign['debtor_name']) ?></td>
                                            <td>
                                                <div>
                                                    <?= e($assign['officer_name']) ?>
                                                    <div class="small text-muted-professional"><?= e($assign['officer_username']) ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-professional">
                                                    <?= ucfirst(str_replace('_', ' ', $assign['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($assign['work_date']): ?>
                                                    <?= date('d/m/Y H:i', strtotime($assign['work_date'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted-professional">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= url('cases/' . $assign['case_id']) ?>" class="btn-professional-light btn-sm" title="ดูรายละเอียดคดี">
                                                    <i class="bi bi-eye"></i>
                                                </a>
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
                        แสดง <?= $pagination['start'] ?> ถึง <?= $pagination['end'] ?> จาก <?= $pagination['total'] ?> รายการ
                    </small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                             <?php if ($pagination['has_more'] || $pagination['current_page'] > 1): ?>
                                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>">Previous</a>
                                </li>
                                <li class="page-item active"><span class="page-link"><?= $pagination['current_page'] ?></span></li>
                                <li class="page-item <?= !$pagination['has_more'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>">Next</a>
                                </li>
                             <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
