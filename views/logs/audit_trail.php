<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
    <style>
        .diff-added { background-color: #d4edda; color: #155724; }
        .diff-removed { background-color: #f8d7da; color: #721c24; text-decoration: line-through; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
        .timeline { position: relative; padding: 20px 0; }
    </style>
</head>
<body>
    <?php $currentPage = 'logs-audit'; ?>
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content-with-sidebar">
        <div class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><i class="bi bi-journal-text"></i> Audit Trail</h1>
                </div>
                <a href="<?= url('logs/activity') ?>" class="btn-professional-outline">
                    <i class="bi bi-arrow-left"></i> กลับไปหน้า Logs
                </a>
            </div>
        </div>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <p class="text-muted-professional mb-0">Module: <span class="badge-professional"><?= e($module) ?></span> Reference ID: <span class="badge-professional"><?= e($reference_id) ?></span></p>
            </div>

            <?php if (empty($logs)): ?>
                <div class="alert-professional alert-professional-info text-center py-5">
                    <i class="bi bi-info-circle fs-1"></i>
                    <h5 class="mt-3">ไม่พบรายการแก้ไข</h5>
                    <p class="mb-0 text-muted-professional">รายการนี้ยังไม่เคยมีการแก้ไขบันทึก</p>
                </div>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($logs as $log): ?>
                        <div class="professional-card mb-4">
                            <div class="professional-card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge-professional me-2">
                                        <?= e($log['action_type']) ?>
                                    </span>
                                    <span class="fw-bold me-2"><i class="bi bi-person-circle"></i> <?= e($log['username']) ?></span>
                                    <span class="text-muted-professional small">(<?= e($log['ip_address']) ?>)</span>
                                </div>
                                <small class="text-muted-professional">
                                    <i class="bi bi-clock"></i> <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                </small>
                            </div>
                            <div class="professional-card-body">
                                <p class="text-muted-professional mb-3 fst-italic">
                                    <i class="bi bi-chat-square-text me-1"></i> <?= e($log['description']) ?>
                                </p>

                                <?php
                                $oldData = $log['old_data'] ? json_decode($log['old_data'], true) : [];
                                $newData = $log['new_data'] ? json_decode($log['new_data'], true) : [];

                                if ($log['action_type'] === 'UPDATE'):
                                ?>
                                    <div class="table-responsive">
                                        <table class="table-professional">
                                            <thead>
                                                <tr>
                                                    <th style="width: 25%">Field Name</th>
                                                    <th style="width: 37%">Old Value</th>
                                                    <th style="width: 38%">New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $hasChanges = false;
                                                $allKeys = array_unique(array_merge(array_keys($oldData ?: []), array_keys($newData ?: [])));

                                                foreach ($allKeys as $key) {
                                                    if (in_array($key, ['updated_at', 'updated_by', 'created_at', 'created_by'])) continue;

                                                    $oldVal = isset($oldData[$key]) ? (is_array($oldData[$key]) ? json_encode($oldData[$key]) : $oldData[$key]) : null;
                                                    $newVal = isset($newData[$key]) ? (is_array($newData[$key]) ? json_encode($newData[$key]) : $newData[$key]) : null;

                                                    if ($oldVal != $newVal) {
                                                        $hasChanges = true;
                                                        echo "<tr>";
                                                        echo "<td class='fw-bold'>" . e(ucfirst(str_replace('_', ' ', $key))) . "</td>";
                                                        echo "<td class='text-break'>" . e($oldVal) . "</td>";
                                                        echo "<td class='text-break'>" . e($newVal) . "</td>";
                                                        echo "</tr>";
                                                    }
                                                }
                                                if (!$hasChanges) {
                                                    echo "<tr><td colspan='3' class='text-center text-muted-professional'>No significant changes found</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php elseif ($log['action_type'] === 'CREATE'): ?>
                                    <button class="btn-professional-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#createDetails<?= $log['id'] ?>">
                                        <i class="bi bi-eye"></i> ดูข้อมูลที่สร้าง
                                    </button>
                                    <div class="collapse mt-2" id="createDetails<?= $log['id'] ?>">
                                        <div class="professional-card">
                                            <div class="professional-card-body">
                                                <pre class="mb-0 small"><?= e(json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($log['action_type'] === 'DELETE'): ?>
                                    <div class="alert-professional alert-professional-danger mb-0 py-2">
                                        <h6 class="alert-heading mb-1"><i class="bi bi-trash"></i> ข้อมูลที่ถูกลบ:</h6>
                                        <hr class="my-1">
                                        <pre class="mb-0 small"><?= e(json_encode($oldData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
