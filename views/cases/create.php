<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'เพิ่มคดีใหม่') ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
</head>
<body>
    <?php $currentPage = 'cases-create'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content-with-sidebar">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title">เพิ่มงานบังคับคดีใหม่</h1>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            <?php if (isset($_SESSION['_flash']['error'])): ?>
                <div class="alert alert-professional alert-professional-danger alert-dismissible fade show">
                    <?= e($_SESSION['_flash']['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['_flash']['error']); ?>
            <?php endif; ?>

            <form action="<?= url('cases/store') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- ข้อมูลผลิตภัณฑ์และสัญญา -->
                <div class="professional-card mb-4">
                    <h5>ข้อมูลผลิตภัณฑ์และสัญญา</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="product" class="form-label">ประเภทผลิตภัณฑ์</label>
                            <input type="text" class="form-control" id="product" name="product" placeholder="เช่น บัตรเครดิต, สินเชื่อส่วนบุคคล">
                        </div>
                        <div class="col-md-4">
                            <label for="contract_no" class="form-label">เลขที่สัญญา <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contract_no" name="contract_no" required>
                        </div>
                        <div class="col-md-4">
                            <label for="contract_date" class="form-label">วันที่ทำสัญญา</label>
                            <input type="date" class="form-control" id="contract_date" name="contract_date">
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลลูกหนี้ -->
                <div class="professional-card mb-4">
                    <h5>ข้อมูลลูกหนี้</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="debtor_name" class="form-label">ชื่อ-นามสกุลลูกหนี้ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="debtor_name" name="debtor_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="citizen_id" class="form-label">เลขบัตรประชาชน (13 หลัก) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="citizen_id" name="citizen_id" required maxlength="13" pattern="\d{13}" title="กรุณากรอกเลข 13 หลัก">
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">ที่อยู่ตามสัญญา/ภูมิลำเนา</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลคดีความ -->
                <div class="professional-card mb-4">
                    <h5>ข้อมูลคดีความ (ศาล)</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="court" class="form-label">ศาล</label>
                            <input type="text" class="form-control" id="court" name="court" placeholder="ระบุชื่อศาล">
                        </div>
                        <div class="col-md-4">
                            <label for="black_case" class="form-label">หมายเลขคดีดำ</label>
                            <input type="text" class="form-control" id="black_case" name="black_case">
                        </div>
                        <div class="col-md-4">
                            <label for="red_case" class="form-label">หมายเลขคดีแดง</label>
                            <input type="text" class="form-control" id="red_case" name="red_case">
                        </div>
                        <div class="col-md-4">
                            <label for="filing_date" class="form-label">วันที่ฟ้อง</label>
                            <input type="date" class="form-control" id="filing_date" name="filing_date">
                        </div>
                        <div class="col-md-4">
                            <label for="judgment_date" class="form-label">วันที่พิพากษา</label>
                            <input type="date" class="form-control" id="judgment_date" name="judgment_date">
                        </div>
                        <div class="col-md-4">
                            <label for="enforcement_status" class="form-label">สถานะการบังคับคดี</label>
                            <select class="form-select" id="enforcement_status" name="enforcement_status">
                                <option value="">-- เลือกสถานะ --</option>
                                <option value="รอตรวจทรัพย์">รอตรวจทรัพย์</option>
                                <option value="รอยึดทรัพย์">รอยึดทรัพย์</option>
                                <option value="รอขายทอดตลาด">รอขายทอดตลาด</option>
                                <option value="ยึดทรัพย์แล้ว">ยึดทรัพย์แล้ว</option>
                                <option value="อายัดบัญชีแล้ว">อายัดบัญชีแล้ว</option>
                                <option value="ปิดบัญชี">ปิดบัญชี</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลยอดหนี้ -->
                <div class="professional-card mb-4">
                    <h5>ข้อมูลยอดหนี้</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="principal_amount" class="form-label">เงินต้น (บาท)</label>
                            <input type="number" step="0.01" class="form-control" id="principal_amount" name="principal_amount">
                        </div>
                        <div class="col-md-4">
                            <label for="interest_amount" class="form-label">ดอกเบี้ย (บาท)</label>
                            <input type="number" step="0.01" class="form-control" id="interest_amount" name="interest_amount">
                        </div>
                        <div class="col-md-4">
                            <label for="total_amount" class="form-label">ยอดรวม (บาท)</label>
                            <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" readonly style="background-color: #f5f5f5;">
                        </div>
                    </div>
                </div>

                <!-- หมายเหตุ -->
                <div class="professional-card mb-4">
                    <h5>หมายเหตุเพิ่มเติม</h5>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <a href="<?= url('cases') ?>" class="btn btn-professional-light">
                        ย้อนกลับ
                    </a>
                    <button type="submit" class="btn btn-professional">
                        บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto calculate total amount
        const principal = document.getElementById('principal_amount');
        const interest = document.getElementById('interest_amount');
        const total = document.getElementById('total_amount');

        function calculateTotal() {
            const p = parseFloat(principal.value) || 0;
            const i = parseFloat(interest.value) || 0;
            total.value = (p + i).toFixed(2);
        }

        principal.addEventListener('input', calculateTotal);
        interest.addEventListener('input', calculateTotal);
    </script>
</body>
</html>
