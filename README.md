# LEMS – Legal Enforcement Management System Bargainpoint

**Version:** 1.0 Enterprise Edition
**Technology:** PHP 8.2+ | MySQL 8+ | Bootstrap 5 | MVC Architecture

## 📋 Overview

LEMS (Legal Enforcement Management System) เป็นระบบบริหารงานบังคับคดีและงานภาคสนามระดับองค์กร ออกแบบเพื่อรองรับการใช้งานผ่านมือถือ 100% พร้อมระบบ Audit Trail ที่สมบูรณ์

### ✨ Key Features

- ✅ ระบบจัดการคดีบังคับคดี (100,000+ records)
- ✅ มอบหมายงานให้เจ้าหน้าที่ภาคสนาม
- ✅ รายงานผลผ่านมือถือ + GPS Location
- ✅ อัปโหลดรูปภาพหลายไฟล์
- ✅ Audit Trail ครบถ้วน (ตรวจสอบย้อนหลังทุกการเปลี่ยนแปลง)
- ✅ Export รายงาน Excel และ Word
- ✅ Role-Based Access Control (Super Admin, IT, Admin, Officer)
- ✅ Responsive Design (Bootstrap 5)

---

## 🚀 Installation Guide

### Prerequisites

- **XAMPP** (PHP 8.2+, MySQL 8+, Apache)
- **Composer** (PHP Dependency Manager)
- **Git** (Optional)

### Step 1: Clone/Download Project

```bash
cd C:\xampp\htdocs
git clone <repository-url> LEMS
# หรือ copy โฟลเดอร์ LEMS ลงใน htdocs
```

### Step 2: Install Dependencies

```bash
cd C:\xampp\htdocs\LEMS
composer install
```

### Step 3: Configure Environment

```bash
# Copy .env.example to .env
copy .env.example .env

# แก้ไข .env ให้ตรงกับ database ของคุณ
```

**ตัวอย่าง `.env`:**
```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=lems_db
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Create Database

เปิด **phpMyAdmin** หรือใช้ command line:

```sql
CREATE DATABASE lems_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 5: Import Database Schema

```bash
# Windows Command Prompt
cd C:\xampp\mysql\bin
mysql -u root -p lems_db < C:\xampp\htdocs\LEMS\database\schema.sql

# หรือ Import ผ่าน phpMyAdmin
```

### Step 6: Set Permissions (Optional)

```bash
# ใน Linux/Mac
chmod 755 public/uploads
chmod 755 storage/logs
chmod 755 storage/cache
```

### Step 7: Access Application

เปิดเบราว์เซอร์แล้วไปที่:

```
http://localhost/LEMS
หรือ
http://localhost/LEMS/public
```

---

## 🔐 Default Users

ระบบมีผู้ใช้ทดสอบ 4 roles:

| Role | Username | Password | Description |
|------|----------|----------|-------------|
| Super Admin | `superadmin` | `Admin@123` | ควบคุมระบบทั้งหมด |
| IT | `itsupport` | `Admin@123` | ดู Logs ทั้งหมด |
| Admin | `admin` | `Admin@123` | จัดการคดี/มอบหมายงาน |
| Officer | `officer` | `Admin@123` | รับงาน/รายงานภาคสนาม |

> **⚠️ สำคัญ:** กรุณาเปลี่ยนรหัสผ่านทันทีหลังติดตั้ง!

---

## 📁 Project Structure

```
LEMS/
├── app/
│   ├── Controllers/          # Application controllers
│   ├── Models/              # Data models
│   ├── Middleware/          # Authentication & authorization
│   └── Helpers/             # Helper functions
├── config/                  # Configuration files
├── core/                    # Core framework files
├── database/                # Database schema & migrations
├── public/                  # Public web root (entry point)
│   ├── assets/             # CSS, JS, images
│   ├── uploads/            # User uploaded files
│   └── index.php           # Application entry point
├── storage/                 # Logs & cache
├── views/                   # View templates
├── vendor/                  # Composer dependencies
├── .env                     # Environment configuration
├── .htaccess               # Apache configuration
├── composer.json           # PHP dependencies
└── README.md               # This file
```

---

## 🎯 User Roles & Permissions

### 1. Super Admin
- ✅ สิทธิ์เต็มทุกอย่าง
- ✅ สร้าง/แก้ไข/ลบ ผู้ใช้ทุก Role
- ✅ กำหนดสิทธิ์ผู้ใช้
- ✅ ดู Audit Trail ทั้งหมด
- ✅ Reset Password

### 2. IT
- ✅ ดู Login Logs
- ✅ ดู Activity Logs
- ✅ ดู Audit Trail
- ❌ ไม่สามารถแก้ไขข้อมูลคดี

### 3. Admin
- ✅ เพิ่ม/แก้ไข/ลบ คดี
- ✅ มอบหมายงานให้เจ้าหน้าที่
- ✅ อนุมัติรายงานภาคสนาม
- ✅ Export รายงาน

### 4. Officer
- ✅ ดูงานที่ได้รับมอบหมาย
- ✅ กด "เริ่มงาน"
- ✅ ลงผลการดำเนินงาน
- ✅ อัปโหลดรูปภาพ
- ✅ บันทึก GPS Location

---

## 🛠️ Troubleshooting

### ปัญหา: Forbidden (403)

**สาเหตุ:** Apache ไม่อนุญาตให้เข้าถึง directory

**วิธีแก้:**
```bash
# แก้ไข Apache config (httpd.conf)
# ค้นหา AllowOverride None แล้วเปลี่ยนเป็น:
AllowOverride All

# Restart Apache
```

### ปัญหา: Database connection error

**สาเหตุ:** ค่า config ใน `.env` ไม่ถูกต้อง

**วิธีแก้:**
1. ตรวจสอบ `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` ใน `.env`
2. ตรวจสอบว่า MySQL service ทำงานอยู่
3. ตรวจสอบว่า database `lems_db` ถูกสร้างแล้ว

### ปัญหา: Class not found

**สาเหตุ:** Composer autoload ยังไม่ถูกสร้าง

**วิธีแก้:**
```bash
composer dump-autoload
```

### ปัญหา: CSRF token invalid

**สาเหตุ:** Session ไม่ทำงาน หรือ cookie ถูกบล็อก

**วิธีแก้:**
1. ตรวจสอบว่า session_start() ทำงาน
2. Clear browser cookies
3. ลอง incognito/private mode

---

## 📊 Database Schema

### Main Tables

1. **users** - ผู้ใช้ระบบ
2. **login_logs** - บันทึกการ Login/Logout
3. **activity_logs** - บันทึกการใช้งานทั้งหมด (Audit Trail)
4. **cases** - ข้อมูลคดี
5. **assignments** - การมอบหมายงาน
6. **field_reports** - รายงานภาคสนาม
7. **report_images** - รูปภาพรายงาน

ดู **ER Diagram** ฉบับสมบูรณ์ที่: `database/ER_DIAGRAM.md`

---

## 🔒 Security Features

- ✅ **PDO Prepared Statements** - ป้องกัน SQL Injection
- ✅ **CSRF Protection** - ป้องกัน Cross-Site Request Forgery
- ✅ **XSS Prevention** - Escape output ทุกจุด
- ✅ **Password Hashing** - ใช้ bcrypt
- ✅ **Session Management** - Auto timeout
- ✅ **Role-Based Access Control** - ตรวจสอบสิทธิ์ทุก request
- ✅ **Audit Trail** - บันทึกทุกการเปลี่ยนแปลง
- ✅ **File Upload Validation** - จำกัดประเภทและขนาดไฟล์

---

## 📱 Mobile Support

- ✅ Responsive Design (Bootstrap 5)
- ✅ รองรับ Touch Gestures
- ✅ GPS Location Tracking
- ✅ Camera/Photo Upload
- ✅ Optimized for 3G/4G

---

## 📈 Performance

- ✅ รองรับ 100,000+ records
- ✅ Database Indexing
- ✅ Pagination
- ✅ Lazy Loading
- ✅ Query Optimization

---

## 🔄 Future Enhancements

- 🔄 REST API
- 🔄 Mobile App (iOS/Android)
- 🔄 Real-time Notifications
- 🔄 Advanced Reporting
- 🔄 Integration with External Systems
- 🔄 Redis Caching
- 🔄 Elasticsearch

---

## 📞 Support

สำหรับคำถามหรือปัญหาในการใช้งาน กรุณาติดต่อ:

Email: boatzaha2905@gmail.com

---

## 📄 License

**Proprietary** - © 2026 Bargainpoint. All rights reserved.

---

## 🎉 Quick Start Checklist

- [ ] ติดตั้ง XAMPP
- [ ] ติดตั้ง Composer
- [ ] Clone โปรเจค
- [ ] Run `composer install`
- [ ] Copy `.env.example` to `.env`
- [ ] สร้าง database
- [ ] Import `schema.sql`
- [ ] เข้าระบบด้วย username: `superadmin` / password: `Admin@123`
- [ ] เปลี่ยนรหัสผ่าน
- [ ] เริ่มใช้งาน! 🚀

---

**LEMS Bargainpoint v1.0 Enterprise Edition**
*Legal Enforcement Management System - Built with ❤️ for Enterprise*
