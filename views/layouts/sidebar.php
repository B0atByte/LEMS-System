<!-- Sidebar -->
<div class="sidebar-professional" id="sidebar">
    <div class="sidebar-header">
        <a href="<?= url('dashboard') ?>" class="sidebar-brand">
            LEMS Bargainpoint
        </a>
    </div>

    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="<?= url('dashboard') ?>" class="sidebar-menu-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 sidebar-menu-icon"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if (in_array($_SESSION['user_role'] ?? '', ['super_admin', 'admin'])): ?>
        <li class="sidebar-menu-item">
            <a href="<?= url('users') ?>" class="sidebar-menu-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                <i class="bi bi-people sidebar-menu-icon"></i>
                <span>จัดการผู้ใช้</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= url('cases') ?>" class="sidebar-menu-link <?= $currentPage === 'cases' ? 'active' : '' ?>">
                <i class="bi bi-folder sidebar-menu-icon"></i>
                <span>จัดการคดี</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= url('cases/create') ?>" class="sidebar-menu-link <?= $currentPage === 'cases-create' ? 'active' : '' ?>">
                <i class="bi bi-plus-circle sidebar-menu-icon"></i>
                <span>เพิ่มคดีใหม่</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= url('assignments') ?>" class="sidebar-menu-link <?= $currentPage === 'assignments' ? 'active' : '' ?>">
                <i class="bi bi-clipboard-check sidebar-menu-icon"></i>
                <span>มอบหมายงาน</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= url('assignments/create') ?>" class="sidebar-menu-link <?= $currentPage === 'assignments-create' ? 'active' : '' ?>">
                <i class="bi bi-person-plus sidebar-menu-icon"></i>
                <span>มอบหมายงานใหม่</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['user_role'] ?? '', ['super_admin', 'it'])): ?>
        <li class="sidebar-menu-item">
            <a href="<?= url('logs/login') ?>" class="sidebar-menu-link <?= $currentPage === 'logs-login' ? 'active' : '' ?>">
                <i class="bi bi-clock-history sidebar-menu-icon"></i>
                <span>Login Logs</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= url('logs/activity') ?>" class="sidebar-menu-link <?= $currentPage === 'logs-activity' ? 'active' : '' ?>">
                <i class="bi bi-activity sidebar-menu-icon"></i>
                <span>Activity Logs</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= url('logs/activity?action_type=UPDATE') ?>" class="sidebar-menu-link <?= $currentPage === 'logs-audit' ? 'active' : '' ?>">
                <i class="bi bi-shield-check sidebar-menu-icon"></i>
                <span>Audit Trail</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['user_role'] ?? '', ['officer'])): ?>
        <li class="sidebar-menu-item">
            <a href="<?= url('my-assignments') ?>" class="sidebar-menu-link <?= $currentPage === 'my-assignments' ? 'active' : '' ?>">
                <i class="bi bi-list-task sidebar-menu-icon"></i>
                <span>งานของฉัน</span>
            </a>
        </li>
        <?php endif; ?>

        <li class="sidebar-menu-item">
            <a href="<?= url('reports') ?>" class="sidebar-menu-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text sidebar-menu-icon"></i>
                <span>รายงาน</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <p class="sidebar-user-name"><?= e($_SESSION['fullname'] ?? 'User') ?></p>
                <p class="sidebar-user-role"><?= e($_SESSION['user_role'] ?? '') ?></p>
            </div>
        </div>
        <a href="<?= url('change-password') ?>" class="btn btn-professional-light btn-sm w-100 mb-2">
            เปลี่ยนรหัสผ่าน
        </a>
        <a href="<?= url('logout') ?>" class="btn btn-professional btn-sm w-100">
            ออกจากระบบ
        </a>
    </div>
</div>

<!-- Sidebar Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle for mobile
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }
});
</script>
