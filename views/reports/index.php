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
    <?php $currentPage = 'reports'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><i class="bi bi-file-earmark-spreadsheet"></i> ระบบรายงานและการส่งออก</h1>
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

            <div class="row g-4">
                <!-- 1. รายงานสรุปคดี -->
                <div class="col-md-6">
                    <div class="professional-card h-100">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-folder2-open"></i> รายงานสรุปข้อมูลคดี</h5>
                        </div>
                        <div class="professional-card-body">
                            <p class="text-muted-professional">Export ข้อมูลคดีทั้งหมดตามช่วงวันที่สร้าง เพื่อนำไปวิเคราะห์ต่อ</p>
                            <form action="<?= url('reports/export-excel') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="report_type" value="cases_summary">

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label">ตั้งแต่วันที่</label>
                                        <input type="date" class="form-control" name="date_from">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">ถึงวันที่</label>
                                        <input type="date" class="form-control" name="date_to">
                                    </div>
                                </div>
                                <button type="submit" class="btn-professional w-100">
                                    <i class="bi bi-file-earmark-excel"></i> ดาวน์โหลด Excel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- 2. รายงานผลการปฏิบัติงาน -->
                <div class="col-md-6">
                    <div class="professional-card h-100">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-person-check"></i> รายงานผลการปฏิบัติงานเจ้าหน้าที่</h5>
                        </div>
                        <div class="professional-card-body">
                            <p class="text-muted-professional">Export รายงานภาคสนาม พร้อมข้อมูลพิกัด GPS และผลการตรวจสอบ</p>
                            <form action="<?= url('reports/export-excel') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="report_type" value="field_performance">

                                <div class="mb-3">
                                    <label class="form-label">เจ้าหน้าที่</label>
                                    <select class="form-select" name="officer_id">
                                        <option value="">-- ทั้งหมด --</option>
                                        <?php foreach ($officers as $officer): ?>
                                            <option value="<?= $officer['id'] ?>"><?= e($officer['fullname']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label">ตั้งแต่วันที่</label>
                                        <input type="date" class="form-control" name="date_from">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">ถึงวันที่</label>
                                        <input type="date" class="form-control" name="date_to">
                                    </div>
                                </div>
                                <button type="submit" class="btn-professional w-100">
                                    <i class="bi bi-file-earmark-excel"></i> ดาวน์โหลด Excel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- 3. System Logs -->
                <div class="col-md-6">
                    <div class="professional-card h-100">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-cpu"></i> รายงาน System Logs</h5>
                        </div>
                        <div class="professional-card-body">
                            <p class="text-muted-professional">Export ประวัติการใช้งานระบบ (System Activity Logs)</p>
                            <form action="<?= url('reports/export-excel') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="report_type" value="system_logs">

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label">ตั้งแต่วันที่</label>
                                        <input type="date" class="form-control" name="date_from">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">ถึงวันที่</label>
                                        <input type="date" class="form-control" name="date_to">
                                    </div>
                                </div>
                                <button type="submit" class="btn-professional w-100">
                                    <i class="bi bi-file-text"></i> ดาวน์โหลด Logs
                                </button>
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
