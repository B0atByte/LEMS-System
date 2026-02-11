<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LEMS Bargainpoint</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Professional Theme -->
    <link href="<?= url('assets/css/professional.css') ?>" rel="stylesheet">
</head>
<body class="login-professional">
    <div class="login-container">
        <div class="login-card-professional">
            <div class="login-header-professional">
                <h1>LEMS</h1>
                <p>Legal Enforcement Management System</p>
                <p style="font-size: 12px; margin-top: 8px; color: #999;">Bargainpoint Enterprise Edition</p>
            </div>

            <div class="login-body-professional">
                <?php if (isset($_SESSION['_flash']['error'])): ?>
                    <div class="alert alert-professional alert-professional-danger alert-dismissible fade show" role="alert">
                        <?= e($_SESSION['_flash']['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['_flash']['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['_flash']['success'])): ?>
                    <div class="alert alert-professional alert-professional-success alert-dismissible fade show" role="alert">
                        <?= e($_SESSION['_flash']['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['_flash']['success']); ?>
                <?php endif; ?>

                <?php if (isset($_GET['timeout'])): ?>
                    <div class="alert alert-professional alert-professional-warning alert-dismissible fade show" role="alert">
                        Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= url('login') ?>" method="POST" autocomplete="off" class="form-professional">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">
                            Username
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            placeholder="กรอก Username"
                            required
                            autofocus
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                        </label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="กรอก Password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-professional w-100 mt-3">
                        เข้าสู่ระบบ
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted-professional">
                        Default username: <strong>superadmin</strong> / Password: <strong>Admin@123</strong>
                    </small>
                </div>
            </div>

            <div class="login-footer-professional">
                <div>
                    LEMS Bargainpoint v1.0 Enterprise Edition
                </div>
                <div style="margin-top: 8px; color: #999;">
                    &copy; <?= date('Y') ?> Bargainpoint. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
