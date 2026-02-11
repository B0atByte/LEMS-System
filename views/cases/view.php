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
                    <h1 class="page-title"><i class="bi bi-folder2-open"></i> รายละเอียดงานบังคับคดี: <?= e($case['contract_no']) ?></h1>
                </div>
                <div>
                    <a href="<?= url('cases/' . $case['id'] . '/edit') ?>" class="btn-professional me-2">
                        <i class="bi bi-pencil"></i> แก้ไขข้อมูล
                    </a>
                    <a href="<?= url('cases') ?>" class="btn-professional-outline">
                        <i class="bi bi-arrow-left"></i> ย้อนกลับ
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <div class="row">
                <!-- Left Column: Case Details -->
                <div class="col-md-8">
                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> ข้อมูลทั่วไป</h5>
                        </div>
                        <div class="professional-card-body">
                            <table class="table-professional">
                                <tbody>
                                    <tr>
                                        <th style="width: 25%;">ชื่อลูกหนี้</th>
                                        <td><?= e($case['debtor_name']) ?></td>
                                        <th style="width: 25%;">เลขบัตรประชาชน</th>
                                        <td><?= e($case['citizen_id']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>ที่อยู่</th>
                                        <td colspan="3"><?= e($case['address'] ?: '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th>เบอร์โทรศัพท์</th>
                                        <td colspan="3"><?= e($case['phone'] ?: '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th>ประเภทผลิตภัณฑ์</th>
                                        <td><?= e($case['product'] ?: '-') ?></td>
                                        <th>เลขที่สัญญา</th>
                                        <td><?= e($case['contract_no']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>วันที่สัญญา</th>
                                        <td><?= $case['contract_date'] ? date('d/m/Y', strtotime($case['contract_date'])) : '-' ?></td>
                                        <th>สถานะงาน</th>
                                        <td>
                                            <span class="badge-professional">
                                                <?= ucfirst($case['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-bank"></i> ข้อมูลทางกฎหมาย</h5>
                        </div>
                        <div class="professional-card-body">
                            <table class="table-professional">
                                <tbody>
                                    <tr>
                                        <th style="width: 25%;">ศาล</th>
                                        <td colspan="3"><?= e($case['court'] ?: '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th>คดีดำ</th>
                                        <td><?= e($case['black_case'] ?: '-') ?></td>
                                        <th>คดีแดง</th>
                                        <td><?= e($case['red_case'] ?: '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th>วันที่ฟ้อง</th>
                                        <td><?= $case['filing_date'] ? date('d/m/Y', strtotime($case['filing_date'])) : '-' ?></td>
                                        <th>วันที่พิพากษา</th>
                                        <td><?= $case['judgment_date'] ? date('d/m/Y', strtotime($case['judgment_date'])) : '-' ?></td>
                                    </tr>
                                    <tr>
                                        <th>สถานะบังคับคดี</th>
                                        <td colspan="3"><?= e($case['enforcement_status'] ?: 'ยังไม่ระบุ') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Financial & Meta -->
                <div class="col-md-4">
                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-cash-coin"></i> รายละเอียดหนี้</h5>
                        </div>
                        <div class="professional-card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    เงินต้น
                                    <span class="fw-bold"><?= number_format($case['principal_amount'] ?? 0, 2) ?> บาท</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ดอกเบี้ย
                                    <span class="fw-bold"><?= number_format($case['interest_amount'] ?? 0, 2) ?> บาท</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">ยอดรวมสุทธิ</span>
                                    <span class="fw-bold fs-5"><?= number_format($case['total_amount'] ?? 0, 2) ?> บาท</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> การมอบหมายงาน</h5>
                        </div>
                        <div class="professional-card-body">
                            <?php if (empty($assignments)): ?>
                                <p class="text-center text-muted-professional py-3">ยังไม่มีการมอบหมายงาน</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush mb-3">
                                    <?php foreach ($assignments as $assign): ?>
                                    <li class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-bold"><?= e($assign['officer_name']) ?></div>
                                                <small class="text-muted-professional">
                                                    <?= date('d/m/Y', strtotime($assign['assigned_date'])) ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge-professional mb-1 d-inline-block"><?= ucfirst($assign['status']) ?></span>
                                                <?php if ($assign['status'] == 'completed' && !empty($assign['report_id'])): ?>
                                                    <a href="<?= url('reports/' . $assign['report_id']) ?>" class="btn-professional-light btn-sm d-block text-decoration-none mt-1" style="font-size: 11px;">
                                                        <i class="bi bi-file-text"></i> ดูรายงาน
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            
                            <div class="d-grid">
                                <a href="<?= url('assignments/create?case_id=' . $case['id']) ?>" class="btn-professional-outline">
                                    <i class="bi bi-person-plus"></i> มอบหมายงานเพิ่ม
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="professional-card mb-4">
                        <div class="professional-card-header">
                            <h5 class="mb-0">หมายเหตุ</h5>
                        </div>
                        <div class="professional-card-body">
                            <p class="mb-0"><?= nl2br(e($case['notes'] ?: '- ไม่มีหมายเหตุ -')) ?></p>
                        </div>
                        <div class="professional-card-footer text-muted-professional small">
                            สร้างเมื่อ: <?= date('d/m/Y H:i', strtotime($case['created_at'])) ?><br>
                            แก้ไขล่าสุด: <?= $case['updated_at'] ? date('d/m/Y H:i', strtotime($case['updated_at'])) : '-' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
