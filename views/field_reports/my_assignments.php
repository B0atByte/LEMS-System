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
        /* Mobile optimizations */
        .card-assignment {
            border-left: 5px solid #6c757d;
        }
        .card-assignment.status-assigned { border-left-color: #6c757d; }
        .card-assignment.status-in_progress { border-left-color: #6c757d; }
        .card-assignment.status-completed { border-left-color: #6c757d; }

        .btn-action {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body class="pb-5">
    <?php $currentPage = 'my-assignments'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><i class="bi bi-list-task"></i> งานของฉัน</h1>
                </div>
                <div class="dropdown">
                    <button class="btn-professional-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        สถานะ: <?= $status ? ucfirst($status) : 'ทั้งหมด' ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="?">ทั้งหมด</a></li>
                        <li><a class="dropdown-item" href="?status=assigned">Assigned (รอรับงาน)</a></li>
                        <li><a class="dropdown-item" href="?status=in_progress">In Progress (กำลังทำ)</a></li>
                        <li><a class="dropdown-item" href="?status=completed">Completed (เสร็จสิ้น)</a></li>
                    </ul>
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

            <?php if (empty($assignments)): ?>
                <div class="text-center py-5 text-muted-professional">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">ไม่มีงานที่ได้รับมอบหมายในขณะนี้</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($assignments as $assign): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="professional-card card-assignment status-<?= $assign['status'] ?>">
                                <div class="professional-card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge-professional"><?= date('d/m/y', strtotime($assign['assigned_date'])) ?></span>
                                        <span class="badge-professional"><?= ucfirst(str_replace('_', ' ', $assign['status'])) ?></span>
                                    </div>

                                    <h5 class="card-title mb-1">
                                        <i class="bi bi-file-text"></i> <?= e($assign['contract_no']) ?>
                                    </h5>
                                    <p class="card-text mb-1 fw-bold"><?= e($assign['debtor_name']) ?></p>
                                    <p class="card-text small text-muted-professional mb-2">
                                        <i class="bi bi-geo-alt"></i> <?= e($assign['address'] ?? 'ไม่ระบุที่อยู่') ?>
                                    </p>

                                    <?php if (!empty($assign['remarks'])): ?>
                                        <div class="alert-professional alert-professional-info py-1 px-2 small mb-2">
                                            <i class="bi bi-chat-quote"></i> <?= e($assign['remarks']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-3">
                                        <?php if ($assign['status'] == 'assigned'): ?>
                                            <form action="<?= url('assignments/' . $assign['id'] . '/start-work') ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-professional btn-action">
                                                    <i class="bi bi-play-circle"></i> เริ่มงาน (Check-in)
                                                </button>
                                            </form>
                                        <?php elseif ($assign['status'] == 'in_progress'): ?>
                                            <a href="<?= url('assignments/' . $assign['id'] . '/report') ?>" class="btn-professional btn-action">
                                                <i class="bi bi-pencil-square"></i> บันทึกรายงาน
                                            </a>
                                        <?php elseif ($assign['status'] == 'completed'): ?>
                                            <a href="<?= url('reports/' . ($assign['report_id'] ?? '#')) ?>" class="btn-professional-outline btn-action">
                                                <i class="bi bi-eye"></i> ดูรายงานที่ส่งแล้ว
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&status=<?= $status ?>">Previous</a>
                        </li>
                        <li class="page-item active">
                            <span class="page-link"><?= $pagination['current_page'] ?> / <?= $pagination['total_pages'] ?></span>
                        </li>
                        <li class="page-item <?= !$pagination['has_more'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&status=<?= $status ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
