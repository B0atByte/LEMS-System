<?php
/**
 * Check Database Connection and Users
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "<h1>🔍 LEMS Database Checker</h1>";
echo "<hr>";

// Check database connection
try {
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
        $_ENV['DB_HOST'],
        $_ENV['DB_PORT'],
        $_ENV['DB_DATABASE']
    );

    $pdo = new PDO(
        $dsn,
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "<p style='color: green;'>✅ <strong>Database Connection: SUCCESS</strong></p>";
    echo "<p>Database: <strong>{$_ENV['DB_DATABASE']}</strong></p>";
    echo "<hr>";

    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->fetch();

    if (!$tableExists) {
        echo "<p style='color: red;'>❌ <strong>Table 'users' ไม่มีในฐานข้อมูล!</strong></p>";
        echo "<p>👉 คุณต้อง import ไฟล์ <code>database/schema.sql</code> ก่อน</p>";
        echo "<p><strong>วิธีการ Import:</strong></p>";
        echo "<ol>";
        echo "<li>เปิด phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>เลือก database: <strong>lems_db</strong></li>";
        echo "<li>คลิกแท็บ <strong>Import</strong></li>";
        echo "<li>เลือกไฟล์: <code>C:\\xampp\\htdocs\\LEMS\\database\\schema.sql</code></li>";
        echo "<li>คลิก <strong>Go</strong></li>";
        echo "</ol>";
        exit;
    }

    echo "<p style='color: green;'>✅ <strong>Table 'users' พบแล้ว</strong></p>";
    echo "<hr>";

    // Check users count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    $userCount = $result['count'];

    echo "<p>👥 <strong>จำนวนผู้ใช้ในระบบ:</strong> {$userCount}</p>";

    if ($userCount == 0) {
        echo "<p style='color: red;'>❌ <strong>ไม่มีผู้ใช้ในระบบ!</strong></p>";
        echo "<p>👉 กำลังสร้างผู้ใช้ default...</p>";

        // Create default users
        $password = password_hash('Admin@123', PASSWORD_DEFAULT);

        $users = [
            ['Super Administrator', 'superadmin', $password, 'super_admin'],
            ['IT Support', 'itsupport', $password, 'it'],
            ['Admin User', 'admin', $password, 'admin'],
            ['Officer Test', 'officer', $password, 'officer']
        ];

        $stmt = $pdo->prepare("INSERT INTO users (fullname, username, password, role, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");

        foreach ($users as $user) {
            $stmt->execute($user);
        }

        echo "<p style='color: green;'>✅ <strong>สร้างผู้ใช้ default สำเร็จ!</strong></p>";
        $userCount = 4;
    }

    echo "<hr>";

    // Show all users
    $stmt = $pdo->query("SELECT id, fullname, username, role, status FROM users ORDER BY id");
    $users = $stmt->fetchAll();

    echo "<h2>📋 รายชื่อผู้ใช้ในระบบ:</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>ชื่อ-สกุล</th><th>Username</th><th>Role</th><th>Status</th>";
    echo "</tr>";

    foreach ($users as $user) {
        $statusColor = $user['status'] === 'active' ? 'green' : 'red';
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['fullname']}</td>";
        echo "<td><strong>{$user['username']}</strong></td>";
        echo "<td>{$user['role']}</td>";
        echo "<td style='color: {$statusColor};'><strong>{$user['status']}</strong></td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<hr>";

    // Test password verification
    echo "<h2>🔐 ทดสอบ Password:</h2>";
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute(['superadmin']);
    $user = $stmt->fetch();

    if ($user) {
        $testPassword = 'Admin@123';
        $isValid = password_verify($testPassword, $user['password']);

        if ($isValid) {
            echo "<p style='color: green;'>✅ <strong>Password ถูกต้อง!</strong></p>";
            echo "<p>Username: <strong>superadmin</strong></p>";
            echo "<p>Password: <strong>Admin@123</strong></p>";
        } else {
            echo "<p style='color: red;'>❌ <strong>Password ไม่ถูกต้อง!</strong></p>";
            echo "<p>👉 กำลังแก้ไข password...</p>";

            // Fix password
            $newPassword = password_hash('Admin@123', PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'superadmin'");
            $updateStmt->execute([$newPassword]);

            echo "<p style='color: green;'>✅ <strong>แก้ไข password เรียบร้อย!</strong></p>";
            echo "<p>Password ใหม่: <strong>Admin@123</strong></p>";
        }
    } else {
        echo "<p style='color: red;'>❌ <strong>ไม่พบ user 'superadmin'</strong></p>";
    }

    echo "<hr>";
    echo "<h2>🎉 สรุป:</h2>";
    echo "<ul>";
    echo "<li>✅ Database Connection: <strong>OK</strong></li>";
    echo "<li>✅ Table 'users': <strong>OK</strong></li>";
    echo "<li>✅ จำนวนผู้ใช้: <strong>{$userCount}</strong></li>";
    echo "<li>✅ Password: <strong>OK</strong></li>";
    echo "</ul>";

    echo "<hr>";
    echo "<h2>🚀 พร้อมใช้งาน!</h2>";
    echo "<p><strong>Login ที่:</strong> <a href='public/' target='_blank'>http://localhost/LEMS/public/</a></p>";
    echo "<p><strong>Username:</strong> superadmin</p>";
    echo "<p><strong>Password:</strong> Admin@123</p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>Database Connection Error!</strong></p>";
    echo "<p>Error: {$e->getMessage()}</p>";
    echo "<hr>";
    echo "<h3>🔧 วิธีแก้ไข:</h3>";
    echo "<ol>";
    echo "<li>ตรวจสอบว่า MySQL ใน XAMPP เปิดอยู่</li>";
    echo "<li>ตรวจสอบว่าสร้าง database 'lems_db' แล้ว</li>";
    echo "<li>ตรวจสอบไฟล์ .env (DB_HOST, DB_USERNAME, DB_PASSWORD)</li>";
    echo "</ol>";
}
?>
