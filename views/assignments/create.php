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
                    <h1 class="page-title"><i class="bi bi-person-plus-fill"></i> มอบหมายงานใหม่</h1>
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

            <div class="row">
                <!-- Left: Case Info (Read Only) -->
                <div class="col-md-6">
                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0">ข้อมูลคดีที่จะมอบหมาย</h5>
                        </div>
                        <div class="professional-card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">เลขที่สัญญา</dt>
                                <dd class="col-sm-8"><?= e($case['contract_no']) ?></dd>

                                <dt class="col-sm-4">ลูกหนี้</dt>
                                <dd class="col-sm-8"><?= e($case['debtor_name']) ?></dd>

                                <dt class="col-sm-4">บัตรประชาชน</dt>
                                <dd class="col-sm-8"><?= e($case['citizen_id']) ?></dd>

                                <dt class="col-sm-4">ที่อยู่</dt>
                                <dd class="col-sm-8 text-muted-professional"><?= e($case['address'] ?: '-') ?></dd>

                                <dt class="col-sm-4">ยอดรวม</dt>
                                <dd class="col-sm-8 fw-bold"><?= number_format($case['total_amount'] ?? 0, 2) ?> บาท</dd>

                                <dt class="col-sm-4">สถานะคดี</dt>
                                <dd class="col-sm-8"><span class="badge-professional"><?= e($case['status']) ?></span></dd>
                            </dl>
                        </div>
                        <div class="professional-card-footer text-end">
                            <a href="<?= url('cases/' . $case['id']) ?>" class="btn-professional-light btn-sm" target="_blank">
                                ดูรายละเอียดเต็ม <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: Assignment Form -->
                <div class="col-md-6">
                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0">รายละเอียดการมอบหมาย</h5>
                        </div>
                        <div class="professional-card-body">
                            <form action="<?= url('assignments/store') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="case_id" value="<?= e($case['id']) ?>">

                                <div class="mb-4">
                                    <label for="officer_id" class="form-label">เลือกเจ้าหน้าที่ผู้รับผิดชอบ <span class="text-danger">*</span></label>
                                    <select class="form-select" id="officer_id" name="officer_id" required>
                                        <option value="">-- เลือกเจ้าหน้าที่ --</option>
                                        <?php foreach ($officers as $officer): ?>
                                            <option value="<?= $officer['id'] ?>">
                                                <?= e($officer['fullname']) ?> (<?= e($officer['role']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text text-muted-professional">
                                        เจ้าหน้าที่ที่เลือกจะสามารถดูข้อมูลและบันทึกรายงานภาคสนามสำหรับคดีนี้ได้
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="remarks" class="form-label">หมายเหตุ / คำสั่งเพิ่มเติม</label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="เช่น ให้ตรวจสอบทรัพย์สินที่อยู่ตามทะเบียนบ้าน..."></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn-professional btn-lg">
                                        <i class="bi bi-check-circle"></i> ยืนยันการมอบหมายงาน
                                    </button>
                                    <a href="<?= url('cases/' . $case['id']) ?>" class="btn-professional-outline">
                                        ยกเลิก
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
