<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
    <!-- Lightbox for images -->
    <link href="https://cdn.jsdelivr.net/npm/baguettebox.js@1.11.1/dist/baguetteBox.min.css" rel="stylesheet">
    <style>
        .report-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .report-image:hover {
            transform: scale(1.02);
        }
        .map-frame {
            border: 0;
            width: 100%;
            height: 300px;
            border-radius: 8px;
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
                    <h1 class="page-title"><i class="bi bi-file-text"></i> รายละเอียดรายงาน</h1>
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

            <!-- Assignment Header -->
            <div class="professional-card mb-4">
                <div class="professional-card-header d-flex justify-content-between">
                    <h5 class="mb-0">เลขที่สัญญา: <?= e($case['contract_no']) ?></h5>
                    <span class="badge-professional"><?= date('d/m/Y', strtotime($report['created_at'])) ?></span>
                </div>
                <div class="professional-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ผู้ส่งรายงาน:</strong> <?= e($officer['fullname']) ?></p>
                            <p><strong>ชื่อลูกหนี้:</strong> <?= e($case['debtor_name']) ?></p>
                            <p><strong>ที่อยู่:</strong> <?= e($case['address'] ?: 'ไม่ระบุ') ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p><strong>สถานะการอนุมัติ:</strong>
                                <?php if ($report['approved_by_admin']): ?>
                                    <span class="badge-professional">อนุมัติแล้ว</span>
                                <?php else: ?>
                                    <span class="badge-professional">รอการตรวจสอบ</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Content -->
            <div class="row">
                <!-- Left: Details & Map -->
                <div class="col-lg-8">
                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-file-text"></i> ผลการตรวจสอบ</h5>
                        </div>
                        <div class="professional-card-body">
                            <div class="alert-professional alert-professional-info mb-3">
                                <strong>ผลการตรวจสอบทรัพย์: </strong>
                                <span class="fw-bold">
                                    <?= e($report['asset_investigation']) ?>
                                </span>
                            </div>

                            <?php if ($report['seized_asset_type']): ?>
                                <div class="mb-3">
                                    <label class="fw-bold">ประเภททรัพย์:</label>
                                    <p><?= e($report['seized_asset_type']) ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="fw-bold">รายละเอียด:</label>
                                <p class="text-break"><?= nl2br(e($report['report_detail'])) ?></p>
                            </div>

                            <?php if ($report['latitude'] && $report['longitude']): ?>
                                <hr>
                                <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill"></i> พิกัดสถานที่ตรวจสอบ</h6>
                                <p class="small text-muted-professional">
                                    Lat: <?= $report['latitude'] ?>, Lng: <?= $report['longitude'] ?>
                                    (ความแม่นยำ: ±<?= round($report['location_accuracy']) ?>m)
                                </p>
                                <!-- Google Maps Embed -->
                                <div class="ratio ratio-16x9">
                                    <iframe
                                        src="https://maps.google.com/maps?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>&z=15&output=embed"
                                        class="map-frame"
                                        allowfullscreen
                                        loading="lazy">
                                    </iframe>
                                </div>
                                 <div class="mt-2 text-end">
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" target="_blank" class="btn-professional-light btn-sm">
                                        <i class="bi bi-box-arrow-up-right"></i> เปิดใน Google Maps
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right: Images -->
                <div class="col-lg-4">
                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-images"></i> รูปภาพประกอบ (<?= count($images) ?>)</h5>
                        </div>
                        <div class="professional-card-body">
                            <?php if (empty($images)): ?>
                                <p class="text-center text-muted-professional">ไม่มีรูปภาพแนบ</p>
                            <?php else: ?>
                                <div class="tz-gallery">
                                    <div class="row g-2">
                                        <?php foreach ($images as $img): ?>
                                            <div class="col-6 col-md-12 col-lg-6">
                                                <a class="lightbox" href="<?= url($img['image_path']) ?>">
                                                    <img src="<?= url($img['image_path']) ?>" alt="<?= e($img['image_name']) ?>" class="report-image border">
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Admin Action: Approve -->
                    <?php if (($_SESSION['user_role'] == 'super_admin' || $_SESSION['user_role'] == 'admin') && !$report['approved_by_admin']): ?>
                        <div class="professional-card">
                            <div class="professional-card-body text-center">
                                <h5 class="card-title">การอนุมัติรายงาน</h5>
                                <p class="card-text small text-muted-professional">ตรวจสอบความถูกต้องครบถ้วนของข้อมูลแล้วใช่หรือไม่?</p>
                                <form action="<?= url('reports/' . $report['id'] . '/approve') ?>" method="POST" onsubmit="return confirm('ยืนยันผลการอนุมัติ?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-professional w-100 btn-lg">
                                        <i class="bi bi-check-circle-fill"></i> อนุมัติรายงานนี้
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lightbox Script -->
    <script src="https://cdn.jsdelivr.net/npm/baguettebox.js@1.11.1/dist/baguetteBox.min.js"></script>
    <script>
        window.addEventListener('load', function() {
            baguetteBox.run('.tz-gallery');
        });
    </script>
</body>
</html>
