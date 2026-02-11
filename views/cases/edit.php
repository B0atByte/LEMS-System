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
    <?php $currentPage = 'cases'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><i class="bi bi-pencil-square"></i> แก้ไขงานบังคับคดี: <?= e($case['contract_no']) ?></h1>
                </div>
                <div>
                    <a href="<?= url('cases/' . $case['id']) ?>" class="btn-professional-light">
                        <i class="bi bi-eye"></i> ดูข้อมูล
                    </a>
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

            <form action="<?= url('cases/' . $case['id'] . '/update') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- ข้อมูลผลิตภัณฑ์และสัญญา -->
                <div class="professional-card mb-4">
                    <div class="professional-card-header">
                        <h5 class="mb-0">ข้อมูลผลิตภัณฑ์และสัญญา</h5>
                    </div>
                    <div class="professional-card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="product" class="form-label">ประเภทผลิตภัณฑ์</label>
                                <input type="text" class="form-control" id="product" name="product" value="<?= e($case['product']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="contract_no" class="form-label">เลขที่สัญญา <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contract_no" name="contract_no" value="<?= e($case['contract_no']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="contract_date" class="form-label">วันที่ทำสัญญา</label>
                                <input type="date" class="form-control" id="contract_date" name="contract_date" value="<?= e($case['contract_date']) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลลูกหนี้ -->
                <div class="professional-card mb-4">
                    <div class="professional-card-header">
                        <h5 class="mb-0">ข้อมูลลูกหนี้</h5>
                    </div>
                    <div class="professional-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="debtor_name" class="form-label">ชื่อ-นามสกุลลูกหนี้ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="debtor_name" name="debtor_name" value="<?= e($case['debtor_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="citizen_id" class="form-label">เลขบัตรประชาชน (13 หลัก) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="citizen_id" name="citizen_id" value="<?= e($case['citizen_id']) ?>" required maxlength="13" pattern="\d{13}">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">ที่อยู่ตามสัญญา/ภูมิลำเนา</label>
                                <textarea class="form-control" id="address" name="address" rows="2"><?= e($case['address']) ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= e($case['phone']) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลคดีความ -->
                <div class="professional-card mb-4">
                    <div class="professional-card-header">
                        <h5 class="mb-0">ข้อมูลคดีความ (ศาล)</h5>
                    </div>
                    <div class="professional-card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="court" class="form-label">ศาล</label>
                                <input type="text" class="form-control" id="court" name="court" value="<?= e($case['court']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="black_case" class="form-label">หมายเลขคดีดำ</label>
                                <input type="text" class="form-control" id="black_case" name="black_case" value="<?= e($case['black_case']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="red_case" class="form-label">หมายเลขคดีแดง</label>
                                <input type="text" class="form-control" id="red_case" name="red_case" value="<?= e($case['red_case']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="filing_date" class="form-label">วันที่ฟ้อง</label>
                                <input type="date" class="form-control" id="filing_date" name="filing_date" value="<?= e($case['filing_date']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="judgment_date" class="form-label">วันที่พิพากษา</label>
                                <input type="date" class="form-control" id="judgment_date" name="judgment_date" value="<?= e($case['judgment_date']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="enforcement_status" class="form-label">สถานะการบังคับคดี</label>
                                <input type="text" class="form-control" id="enforcement_status" name="enforcement_status" value="<?= e($case['enforcement_status']) ?>" list="statusOptions">
                                <datalist id="statusOptions">
                                    <option value="รอตรวจทรัพย์">
                                    <option value="รอยึดทรัพย์">
                                    <option value="รอขายทอดตลาด">
                                    <option value="ยึดทรัพย์แล้ว">
                                    <option value="อายัดบัญชีแล้ว">
                                    <option value="ปิดบัญชี">
                                </datalist>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลยอดหนี้ -->
                <div class="professional-card mb-4">
                    <div class="professional-card-header">
                        <h5 class="mb-0">ข้อมูลยอดหนี้</h5>
                    </div>
                    <div class="professional-card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="principal_amount" class="form-label">เงินต้น (บาท)</label>
                                <input type="number" step="0.01" class="form-control" id="principal_amount" name="principal_amount" value="<?= e($case['principal_amount']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="interest_amount" class="form-label">ดอกเบี้ย (บาท)</label>
                                <input type="number" step="0.01" class="form-control" id="interest_amount" name="interest_amount" value="<?= e($case['interest_amount']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="total_amount" class="form-label">ยอดรวม (บาท)</label>
                                <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" value="<?= e($case['total_amount']) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- สถานะและหมายเหตุ -->
                <div class="professional-card mb-4">
                    <div class="professional-card-header">
                        <h5 class="mb-0">สถานะและหมายเหตุ</h5>
                    </div>
                    <div class="professional-card-body">
                         <div class="row g-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">สถานะงาน <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" <?= $case['status'] == 'active' ? 'selected' : '' ?>>Active (กำลังดำเนินการ)</option>
                                    <option value="inactive" <?= $case['status'] == 'inactive' ? 'selected' : '' ?>>Inactive (พักงาน)</option>
                                    <option value="closed" <?= $case['status'] == 'closed' ? 'selected' : '' ?>>Closed (ปิดบัญชี/จบงาน)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="notes" class="form-label">หมายเหตุเพิ่มเติม</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?= e($case['notes']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <a href="<?= url('cases') ?>" class="btn-professional-outline btn-lg">
                        <i class="bi bi-arrow-left"></i> ย้อนกลับ
                    </a>
                    <button type="submit" class="btn-professional btn-lg">
                        <i class="bi bi-save"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>

            <div class="professional-card mt-5">
                <div class="professional-card-header">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> พื้นที่อันตราย</h5>
                </div>
                <div class="professional-card-body">
                    <p>การลบข้อมูลคดีจะไม่สามารถกู้ข้อมูลคืนได้</p>
                    <?php if ($_SESSION['user_role'] == 'super_admin' || $_SESSION['user_role'] == 'admin'): ?>
                    <form action="<?= url('cases/' . $case['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลคดีนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-professional-outline">
                            <i class="bi bi-trash"></i> ลบข้อมูลคดีนี้
                        </button>
                    </form>
                    <?php else: ?>
                        <div class="alert-professional alert-professional-info">คุณไม่มีสิทธิ์ลบข้อมูลนี้</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
