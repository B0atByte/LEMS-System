-- ========================================
-- LEMS - Legal Enforcement Management System Bargainpoint
-- Database Schema
-- Version: 1.0 Enterprise Edition
-- Database: MySQL 8.0+
-- ========================================

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS report_images;
DROP TABLE IF EXISTS field_reports;
DROP TABLE IF EXISTS assignments;
DROP TABLE IF EXISTS cases;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS login_logs;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

-- ========================================
-- TABLE: users
-- ========================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password using password_hash()',
    role ENUM('super_admin', 'it', 'admin', 'officer') NOT NULL DEFAULT 'officer',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User accounts and roles';

-- ========================================
-- TABLE: login_logs
-- ========================================
CREATE TABLE login_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    username VARCHAR(100) NULL COMMENT 'Store username even if user is deleted',
    ip_address VARCHAR(45) NULL COMMENT 'Support IPv4 and IPv6',
    user_agent TEXT NULL,
    login_time DATETIME NOT NULL,
    logout_time DATETIME NULL,
    status ENUM('success', 'failed') NOT NULL DEFAULT 'success',
    failure_reason VARCHAR(255) NULL COMMENT 'Reason for failed login',
    INDEX idx_user_id (user_id),
    INDEX idx_username (username),
    INDEX idx_login_time (login_time),
    INDEX idx_status (status),
    INDEX idx_ip_address (ip_address),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Login/Logout activity logs';

-- ========================================
-- TABLE: activity_logs (Audit Trail)
-- ========================================
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    username VARCHAR(100) NULL COMMENT 'Store username even if user is deleted',
    action_type ENUM('CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'EXPORT', 'ASSIGN', 'APPROVE', 'START_WORK', 'UPLOAD', 'VIEW') NOT NULL,
    module VARCHAR(50) NOT NULL COMMENT 'users, cases, assignments, reports, etc.',
    reference_id INT UNSIGNED NULL COMMENT 'ID of the affected record',
    description TEXT NULL COMMENT 'Human-readable description',
    old_data JSON NULL COMMENT 'Previous data before change',
    new_data JSON NULL COMMENT 'New data after change',
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_username (username),
    INDEX idx_action_type (action_type),
    INDEX idx_module (module),
    INDEX idx_reference_id (reference_id),
    INDEX idx_created_at (created_at),
    INDEX idx_module_reference (module, reference_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Complete audit trail for all activities';

-- ========================================
-- TABLE: cases (งานบังคับคดี)
-- ========================================
CREATE TABLE cases (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- ข้อมูลผลิตภัณฑ์
    product VARCHAR(100) NULL COMMENT 'ประเภทผลิตภัณฑ์',

    -- ข้อมูลลูกหนี้
    debtor_name VARCHAR(255) NOT NULL,
    citizen_id VARCHAR(13) NULL COMMENT 'เลขบัตรประชาชน 13 หลัก',
    address TEXT NULL,
    phone VARCHAR(20) NULL,

    -- ข้อมูลสัญญา
    contract_no VARCHAR(100) NULL,
    contract_date DATE NULL,

    -- ข้อมูลศาล
    court VARCHAR(255) NULL COMMENT 'ชื่อศาล',
    black_case VARCHAR(100) NULL COMMENT 'เลขคดีดำ',
    red_case VARCHAR(100) NULL COMMENT 'เลขคดีแดง',
    filing_date DATE NULL COMMENT 'วันที่ฟ้อง',
    judgment_date DATE NULL COMMENT 'วันที่พิพากษา',

    -- สถานะการบังคับคดี
    enforcement_status VARCHAR(100) NULL COMMENT 'สถานะการบังคับคดี',

    -- จำนวนเงิน
    principal_amount DECIMAL(15,2) NULL COMMENT 'ยอดเงินต้น',
    interest_amount DECIMAL(15,2) NULL COMMENT 'ดอกเบี้ย',
    total_amount DECIMAL(15,2) NULL COMMENT 'ยอดรวม',

    -- รายละเอียดเพิ่มเติม
    notes TEXT NULL,

    -- สถานะ
    status ENUM('active', 'inactive', 'closed') DEFAULT 'active',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,

    -- Indexes for performance (รองรับ 100,000+ records)
    INDEX idx_debtor_name (debtor_name),
    INDEX idx_citizen_id (citizen_id),
    INDEX idx_contract_no (contract_no),
    INDEX idx_black_case (black_case),
    INDEX idx_red_case (red_case),
    INDEX idx_filing_date (filing_date),
    INDEX idx_judgment_date (judgment_date),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_fulltext_search (debtor_name, contract_no, black_case, red_case),

    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cases (งานบังคับคดี)';

-- ========================================
-- TABLE: assignments (การมอบหมายงาน)
-- ========================================
CREATE TABLE assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    case_id INT UNSIGNED NOT NULL,
    officer_id INT UNSIGNED NOT NULL,
    assigned_date DATE NOT NULL COMMENT 'วันที่มอบหมาย',
    work_date DATETIME NULL COMMENT 'วันเวลาที่เจ้าหน้าที่กดเริ่มงาน',
    status ENUM('assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'assigned',
    remarks TEXT NULL COMMENT 'หมายเหตุจาก Admin',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,

    INDEX idx_case_id (case_id),
    INDEX idx_officer_id (officer_id),
    INDEX idx_assigned_date (assigned_date),
    INDEX idx_status (status),
    INDEX idx_work_date (work_date),

    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY (officer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Work assignments for officers';

-- ========================================
-- TABLE: field_reports (รายงานภาคสนาม)
-- ========================================
CREATE TABLE field_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT UNSIGNED NOT NULL,

    -- ผลการตรวจสอบทรัพย์
    asset_investigation ENUM('พบทรัพย์', 'ไม่พบทรัพย์') NOT NULL,

    -- ประเภททรัพย์ที่ยึด
    seized_asset_type VARCHAR(255) NULL COMMENT 'ที่ดินพร้อมสิ่งปลูกสร้าง / ชื่อที่+ตั้งบริษัท / อื่นๆ',

    -- สถานะการบังคับคดี
    enforcement_status ENUM(
        'ยึด&อายัดเป็นผล',
        'ยึด&อายัดไม่เป็นผล',
        'อยู่ระหว่างขายทอดตลาด',
        'ถอนการบังคับคดี',
        'อื่นๆ'
    ) NULL,

    -- รายละเอียด
    report_detail TEXT NOT NULL COMMENT 'รายละเอียดการตรวจสอบ',
    extra_detail TEXT NULL COMMENT 'ข้อมูลเพิ่มเติม',

    -- GPS Location
    latitude DECIMAL(10, 8) NULL COMMENT 'ละติจูด',
    longitude DECIMAL(11, 8) NULL COMMENT 'ลองจิจูด',
    location_accuracy FLOAT NULL COMMENT 'ความแม่นยำของ GPS (เมตร)',

    -- การอนุมัติ
    approved_by_admin BOOLEAN DEFAULT FALSE,
    approved_by INT UNSIGNED NULL,
    approved_at DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,

    INDEX idx_assignment_id (assignment_id),
    INDEX idx_asset_investigation (asset_investigation),
    INDEX idx_enforcement_status (enforcement_status),
    INDEX idx_approved (approved_by_admin),
    INDEX idx_created_at (created_at),

    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Field operation reports';

-- ========================================
-- TABLE: report_images (รูปภาพรายงาน)
-- ========================================
CREATE TABLE report_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    image_name VARCHAR(255) NOT NULL COMMENT 'Original filename',
    file_size INT UNSIGNED NULL COMMENT 'File size in bytes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT UNSIGNED NULL,

    INDEX idx_report_id (report_id),
    INDEX idx_created_at (created_at),

    FOREIGN KEY (report_id) REFERENCES field_reports(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Images attached to field reports';

-- ========================================
-- INSERT DEFAULT DATA
-- ========================================

-- Default Super Admin (password: Admin@123)
INSERT INTO users (fullname, username, password, role, status, created_at) VALUES
('Super Administrator', 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active', NOW()),
('IT Support', 'itsupport', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'it', 'active', NOW()),
('Admin User', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW()),
('Officer Test', 'officer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer', 'active', NOW());

-- Note: Default password for all users is 'Admin@123'
-- Password hash generated using: password_hash('Admin@123', PASSWORD_DEFAULT)

-- ========================================
-- VIEWS FOR REPORTING
-- ========================================

-- View: Complete assignment details with case and officer info
CREATE OR REPLACE VIEW vw_assignment_details AS
SELECT
    a.id as assignment_id,
    a.assigned_date,
    a.work_date,
    a.status as assignment_status,
    c.id as case_id,
    c.debtor_name,
    c.citizen_id,
    c.contract_no,
    c.black_case,
    c.red_case,
    c.court,
    u.id as officer_id,
    u.fullname as officer_name,
    u.username as officer_username,
    fr.id as report_id,
    fr.asset_investigation,
    fr.seized_asset_type,
    fr.enforcement_status,
    fr.approved_by_admin,
    fr.latitude,
    fr.longitude,
    a.created_at
FROM assignments a
INNER JOIN cases c ON a.case_id = c.id
INNER JOIN users u ON a.officer_id = u.id
LEFT JOIN field_reports fr ON a.id = fr.assignment_id;

-- ========================================
-- STORED PROCEDURES
-- ========================================

DELIMITER $$

-- Procedure: Get user statistics
CREATE PROCEDURE sp_get_user_stats()
BEGIN
    SELECT
        role,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_count
    FROM users
    GROUP BY role;
END$$

-- Procedure: Get case statistics
CREATE PROCEDURE sp_get_case_stats()
BEGIN
    SELECT
        status,
        COUNT(*) as total,
        SUM(total_amount) as total_value
    FROM cases
    GROUP BY status;
END$$

-- Procedure: Get officer workload
CREATE PROCEDURE sp_get_officer_workload()
BEGIN
    SELECT
        u.id,
        u.fullname,
        COUNT(a.id) as total_assignments,
        SUM(CASE WHEN a.status = 'assigned' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN a.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed
    FROM users u
    LEFT JOIN assignments a ON u.id = a.officer_id
    WHERE u.role = 'officer' AND u.status = 'active'
    GROUP BY u.id, u.fullname;
END$$

DELIMITER ;

-- ========================================
-- END OF SCHEMA
-- ========================================
