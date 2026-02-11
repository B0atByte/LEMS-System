<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($pageTitle) ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin: 5px;
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
                    <h1 class="page-title"><i class="bi bi-pencil-square"></i> ส่งรายงาน</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <?php if (isset($_SESSION['_flash']['error'])): ?>
                <div class="alert-professional alert-professional-danger">
                    <?= e($_SESSION['_flash']['error']) ?>
                </div>
                <?php unset($_SESSION['_flash']['error']); ?>
            <?php endif; ?>

            <div class="professional-card mb-3">
                <div class="professional-card-header">
                    <h6 class="mb-0">ข้อมูลงาน</h6>
                </div>
                <div class="professional-card-body py-2">
                    <small class="text-muted-professional d-block">เลขที่สัญญา: <?= e($case['contract_no']) ?></small>
                    <strong><?= e($case['debtor_name']) ?></strong>
                    <p class="mb-0 small text-truncate"><?= e($case['address']) ?></p>
                </div>
            </div>

            <form action="<?= url('assignments/' . $assignment['id'] . '/report') ?>" method="POST" enctype="multipart/form-data" id="reportForm">
                <?= csrf_field() ?>

                <!-- GPS Location -->
                <div class="professional-card mb-3">
                    <div class="professional-card-body text-center">
                        <label class="form-label fw-bold"><i class="bi bi-geo-alt-fill"></i> พิกัด GPS</label>

                        <div id="gpsStatus" class="alert-professional alert-professional-warning py-1 small">กำลังค้นหาตำแหน่ง...</div>

                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <input type="hidden" id="accuracy" name="accuracy">

                        <div id="gpsData" style="display:none;">
                            <span class="badge-professional" id="gpsCoords"></span>
                            <button type="button" class="btn-professional-light btn-sm ms-2" onclick="getLocation()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Report Details -->
                <div class="professional-card mb-3">
                    <div class="professional-card-body">
                        <h6 class="card-title fw-bold">ผลการตรวจสอบทรัพย์</h6>

                        <div class="mb-3">
                            <label class="form-label">พบทรัพย์สินหรือไม่ <span class="text-danger">*</span></label>
                            <select class="form-select" name="asset_investigation" id="asset_investigation" required onchange="toggleAssetType()">
                                <option value="">-- กรุณาเลือก --</option>
                                <option value="พบทรัพย์">พบทรัพย์</option>
                                <option value="ไม่พบทรัพย์">ไม่พบทรัพย์</option>
                            </select>
                        </div>

                        <div class="mb-3" id="assetTypeGroup" style="display:none;">
                            <label class="form-label">ประเภททรัพย์ที่พบ</label>
                            <input type="text" class="form-control" name="seized_asset_type" placeholder="เช่น ที่ดินพร้อมสิ่งปลูกสร้าง, รถยนต์...">
                        </div>

                        <div class="mb-3">
                             <label class="form-label">สถานะการบังคับคดี (ล่าสุด)</label>
                             <select class="form-select" name="enforcement_status">
                                <option value="">-- ไม่เปลี่ยนแปลง --</option>
                                <option value="ยึด&อายัดเป็นผล">ยึด & อายัดเป็นผล</option>
                                <option value="ยึด&อายัดไม่เป็นผล">ยึด & อายัดไม่เป็นผล</option>
                                <option value="อยู่ระหว่างขายทอดตลาด">อยู่ระหว่างขายทอดตลาด</option>
                                 <option value="ถอนการบังคับคดี">ถอนการบังคับคดี</option>
                                <option value="อื่นๆ">อื่นๆ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">รายละเอียดการตรวจสอบ / บันทึกเพิ่มเติม</label>
                            <textarea class="form-control" name="report_detail" rows="4" placeholder="บันทึกรายละเอียดสิ่งที่พบเห็น หรือข้อมูลสภาพทรัพย์สิน..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="professional-card mb-3">
                    <div class="professional-card-body">
                        <h6 class="card-title fw-bold"><i class="bi bi-camera"></i> รูปภาพประกอบ</h6>
                        <input type="file" class="form-control" name="images[]" id="images" multiple accept="image/*" onchange="previewImages()">
                        <div class="form-text text-muted-professional">ถ่ายรูปหรือเลือกรูปภาพได้หลายรูป</div>
                        <div id="imagePreview" class="d-flex flex-wrap mt-2"></div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn-professional btn-lg" id="submitBtn">
                        <i class="bi bi-send-check"></i> ส่งรายงาน
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // GPS Logic
        document.addEventListener('DOMContentLoaded', function() {
            getLocation();
        });

        function getLocation() {
            const status = document.getElementById('gpsStatus');
            const dataDiv = document.getElementById('gpsData');
            const coordsSpan = document.getElementById('gpsCoords');

            if (!navigator.geolocation) {
                status.className = 'alert-professional alert-professional-danger py-1 small';
                status.innerHTML = 'Browser ไม่รองรับ GPS';
                return;
            }

            status.innerHTML = 'กำลังค้นหาตำแหน่ง... <div class="spinner-border spinner-border-sm" role="status"></div>';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const acc = position.coords.accuracy;

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('accuracy').value = acc;

                    status.style.display = 'none';
                    dataDiv.style.display = 'block';
                    coordsSpan.innerHTML = `${lat.toFixed(6)}, ${lng.toFixed(6)} (±${Math.round(acc)}m)`;
                },
                (error) => {
                    status.className = 'alert-professional alert-professional-danger py-1 small';
                    status.style.display = 'block';
                    dataDiv.style.display = 'none';
                    let msg = 'ไม่สามารถระบุตำแหน่งได้';
                    switch(error.code) {
                        case error.PERMISSION_DENIED: msg = "ผู้ใช้ปฏิเสธการขอพิกัด (กรุณาเปิด GPS)"; break;
                        case error.POSITION_UNAVAILABLE: msg = "สัญญาณ GPS ขัดข้อง"; break;
                        case error.TIMEOUT: msg = "หมดเวลาการค้นหาพิกัด"; break;
                    }
                    status.innerHTML = msg;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        // Image Preview
        function previewImages() {
            const preview = document.getElementById('imagePreview');
            const fileInput = document.getElementById('images');
            preview.innerHTML = '';

            if (fileInput.files) {
                Array.from(fileInput.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'preview-image shadow-sm border';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        // Toggle Asset Type
        function toggleAssetType() {
            const status = document.getElementById('asset_investigation').value;
            const group = document.getElementById('assetTypeGroup');
            if (status === 'พบทรัพย์') {
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
            }
        }

        // Validate before submit
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            const lat = document.getElementById('latitude').value;
            if (!lat) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่พบพิกัด GPS',
                    text: 'กรุณารอให้ระบบจับสัญญาณ GPS ได้ก่อนส่งรายงาน',
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    </script>
</body>
</html>
