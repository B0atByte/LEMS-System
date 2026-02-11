<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'จัดการคดี') ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
</head>
<body>
    <?php $currentPage = 'cases'; ?>
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
                    <h1 class="page-title">จัดการงานบังคับคดี</h1>
                </div>
                <a href="<?= url('cases/create') ?>" class="btn btn-professional">
                    เพิ่มงานใหม่
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

            <!-- Search & Filter -->
            <div class="professional-card mb-4">
                <h5>ค้นหาและกรองข้อมูล</h5>
                <form action="<?= url('cases') ?>" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">ค้นหา</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="<?= e($search) ?>"
                               placeholder="ชื่อลูกหนี้, เลขสัญญา, เลขคดีดำ/แดง, เลขบัตรประชาชน">
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">สถานะ</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active (ดำเนินการ)</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive (พักงาน)</option>
                            <option value="closed" <?= $status === 'closed' ? 'selected' : '' ?>>Closed (ปิดบัญชี)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-professional w-100 me-2">
                            ค้นหา
                        </button>
                        <a href="<?= url('cases') ?>" class="btn btn-professional-light" title="ล้างค่า">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="professional-card">
                <div class="table-responsive">
                    <table class="table table-professional">
                        <thead>
                            <tr>
                                <th>เลขสัญญา</th>
                                <th>ชื่อลูกหนี้</th>
                                <th>เลขคดีแดง</th>
                                <th>วันที่ฟ้อง/พิพากษา</th>
                                <th class="text-end">ยอดรวม (บาท)</th>
                                <th>สถานะ</th>
                                <th>มอบหมาย</th>
                                <th class="text-end" style="min-width: 120px;">การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cases)): ?>
                                <tr><td colspan="8" class="text-center py-4 text-muted-professional">ไม่พบข้อมูล</td></tr>
                            <?php else: ?>
                                <?php foreach ($cases as $case): ?>
                                    <tr>
                                        <td class="fw-bold">
                                            <a href="<?= url('cases/' . $case['id']) ?>" class="text-decoration-none" style="color: var(--text-dark);">
                                                <?= e($case['contract_no']) ?>
                                            </a>
                                            <div class="small text-muted-professional"><?= e($case['product'] ?? '-') ?></div>
                                        </td>
                                        <td>
                                            <?= e($case['debtor_name']) ?>
                                            <div class="small text-muted-professional"><?= e($case['citizen_id']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge-professional-light"><?= e($case['red_case'] ?: '-') ?></span>
                                            <div class="small text-muted-professional">ดำ: <?= e($case['black_case'] ?: '-') ?></div>
                                        </td>
                                        <td>
                                            <div class="small">ฟ้อง: <?= $case['filing_date'] ? date('d/m/Y', strtotime($case['filing_date'])) : '-' ?></div>
                                            <div class="small">พ.พ.: <?= $case['judgment_date'] ? date('d/m/Y', strtotime($case['judgment_date'])) : '-' ?></div>
                                        </td>
                                        <td class="text-end" style="font-family: monospace;">
                                            <?= number_format($case['total_amount'] ?? 0, 2) ?>
                                        </td>
                                        <td>
                                            <span class="badge-professional"><?= ucfirst($case['status']) ?></span>
                                            <div class="small text-truncate text-muted-professional" style="max-width: 100px;"><?= e($case['enforcement_status']) ?></div>
                                        </td>
                                        <td>
                                            <span class="text-muted-professional small">- ยังไม่ระบุ -</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= url('cases/' . $case['id']) ?>" class="btn btn-professional-outline btn-sm" title="ดูรายละเอียด">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= url('assignments/create?case_id=' . $case['id']) ?>" class="btn btn-professional-outline btn-sm" title="มอบหมายงาน">
                                                    <i class="bi bi-person-plus-fill"></i>
                                                </a>
                                                <a href="<?= url('cases/' . $case['id'] . '/edit') ?>" class="btn btn-professional-outline btn-sm" title="แก้ไข">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top: 1px solid var(--border-gray);">
                    <small class="text-muted-professional">
                        แสดง <?= $pagination['start'] ?> ถึง <?= $pagination['end'] ?> จาก <?= $pagination['total'] ?> รายการ
                    </small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">Previous</a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <?php if ($i == 1 || $i == $pagination['total_pages'] || ($i >= $pagination['current_page'] - 2 && $i <= $pagination['current_page'] + 2)): ?>
                                    <li class="page-item <?= ($i == $pagination['current_page']) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"><?= $i ?></a>
                                    </li>
                                <?php elseif ($i == 2 || $i == $pagination['total_pages'] - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($pagination['has_more']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">Next</a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Next</span></li>
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
