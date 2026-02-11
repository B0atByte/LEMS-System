# LEMS – Project Structure (MVC Architecture)

## 📁 Complete Directory Structure

```
LEMS/
│
├── 📁 app/
│   ├── 📁 Controllers/           # Application Controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── UserController.php
│   │   ├── CaseController.php
│   │   ├── AssignmentController.php
│   │   ├── FieldReportController.php
│   │   ├── LogController.php
│   │   └── ExportController.php
│   │
│   ├── 📁 Models/                # Application Models
│   │   ├── BaseModel.php         # Abstract base model
│   │   ├── User.php
│   │   ├── LoginLog.php
│   │   ├── ActivityLog.php
│   │   ├── Cases.php
│   │   ├── Assignment.php
│   │   ├── FieldReport.php
│   │   └── ReportImage.php
│   │
│   ├── 📁 Middleware/            # Middleware for auth & roles
│   │   ├── AuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── CsrfMiddleware.php
│   │
│   └── 📁 Helpers/               # Helper functions
│       ├── SecurityHelper.php    # Security utilities
│       ├── ValidationHelper.php  # Input validation
│       ├── DateHelper.php        # Date formatting
│       └── FileHelper.php        # File upload handling
│
├── 📁 public/                    # Public web root
│   ├── index.php                 # Application entry point
│   ├── .htaccess                 # Apache rewrite rules
│   │
│   ├── 📁 assets/
│   │   ├── 📁 css/
│   │   │   ├── bootstrap.min.css
│   │   │   ├── style.css         # Custom styles
│   │   │   └── responsive.css
│   │   │
│   │   ├── 📁 js/
│   │   │   ├── bootstrap.bundle.min.js
│   │   │   ├── jquery.min.js
│   │   │   ├── app.js            # Custom JS
│   │   │   ├── gps.js            # GPS handling
│   │   │   └── upload.js         # Multiple file upload
│   │   │
│   │   └── 📁 images/
│   │       ├── logo.png
│   │       └── favicon.ico
│   │
│   └── 📁 uploads/               # User uploaded files
│       └── 📁 field_reports/
│           └── [dynamic folders by date]
│
├── 📁 views/                     # View templates
│   ├── 📁 layouts/
│   │   ├── header.php            # Common header
│   │   ├── footer.php            # Common footer
│   │   ├── navbar.php            # Navigation bar
│   │   └── sidebar.php           # Sidebar (if needed)
│   │
│   ├── 📁 auth/
│   │   ├── login.php
│   │   └── change_password.php
│   │
│   ├── 📁 dashboard/
│   │   ├── super_admin.php
│   │   ├── it.php
│   │   ├── admin.php
│   │   └── officer.php
│   │
│   ├── 📁 users/
│   │   ├── index.php             # List users
│   │   ├── create.php            # Create user form
│   │   ├── edit.php              # Edit user form
│   │   └── view.php              # View user details
│   │
│   ├── 📁 cases/
│   │   ├── index.php             # List cases
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── view.php
│   │
│   ├── 📁 assignments/
│   │   ├── index.php             # List assignments
│   │   ├── create.php            # Assign work
│   │   └── view.php
│   │
│   ├── 📁 field_reports/
│   │   ├── my_assignments.php    # Officer view
│   │   ├── start_work.php        # Start work button
│   │   ├── create.php            # Submit report form
│   │   ├── view.php              # View report
│   │   └── approve.php           # Admin approval
│   │
│   ├── 📁 logs/
│   │   ├── login_logs.php
│   │   ├── activity_logs.php
│   │   └── audit_trail.php       # Compare old vs new data
│   │
│   └── 📁 reports/
│       ├── index.php             # Report filters
│       └── export.php            # Export options
│
├── 📁 config/                    # Configuration files
│   ├── database.php              # Database connection
│   ├── app.php                   # App settings
│   └── routes.php                # Route definitions
│
├── 📁 core/                      # Core framework files
│   ├── Router.php                # URL routing
│   ├── Request.php               # HTTP request handler
│   ├── Response.php              # HTTP response handler
│   ├── Session.php               # Session management
│   └── Database.php              # Database singleton
│
├── 📁 database/                  # Database files
│   ├── schema.sql                # Database schema
│   ├── ER_DIAGRAM.md             # ER Diagram documentation
│   └── migrations/               # Future migrations
│
├── 📁 storage/                   # Storage (not public)
│   ├── 📁 logs/
│   │   ├── app.log
│   │   ├── error.log
│   │   └── access.log
│   │
│   └── 📁 cache/
│       └── [temporary cache files]
│
├── 📁 vendor/                    # Composer dependencies
│   ├── autoload.php
│   └── [composer packages]
│
├── 📁 tests/                     # Unit tests (future)
│   └── README.md
│
├── .env                          # Environment variables (NEVER commit)
├── .env.example                  # Environment template
├── .gitignore                    # Git ignore rules
├── composer.json                 # Composer dependencies
├── composer.lock                 # Composer lock file
├── README.md                     # Project documentation
└── PROJECT_STRUCTURE.md          # This file
```

## 🔄 Request Flow (MVC Pattern)

```
1. Browser Request
   ↓
2. public/index.php (Entry Point)
   ↓
3. core/Router.php (Route matching)
   ↓
4. Middleware (Auth, CSRF, Role check)
   ↓
5. Controller (Business logic)
   ↓
6. Model (Database operations)
   ↓
7. Controller (Process data)
   ↓
8. View (Render HTML)
   ↓
9. Response to Browser
```

## 🎯 File Naming Conventions

| Type | Convention | Example |
|------|-----------|---------|
| Controllers | PascalCase + Controller suffix | `UserController.php` |
| Models | PascalCase | `User.php`, `ActivityLog.php` |
| Views | lowercase with underscores | `user_list.php`, `create_case.php` |
| Helpers | PascalCase + Helper suffix | `SecurityHelper.php` |
| Config | lowercase | `database.php`, `app.php` |

## 🔐 Security Structure

### Protected Directories
```
app/          - Not accessible via web
config/       - Not accessible via web
core/         - Not accessible via web
database/     - Not accessible via web
storage/      - Not accessible via web
vendor/       - Not accessible via web
```

### Public Directory
```
public/       - Only directory accessible via web
  ├── index.php (entry point)
  ├── assets/ (CSS, JS, images)
  └── uploads/ (with .htaccess protection)
```

## 📦 Composer Packages Required

```json
{
    "require": {
        "php": "^8.2",
        "vlucas/phpdotenv": "^5.5",
        "phpoffice/phpspreadsheet": "^1.29",
        "phpoffice/phpword": "^1.1"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Core\\": "core/"
        }
    }
}
```

## 🚀 Installation Steps

1. **Clone/Create Project**
   ```bash
   cd C:\xampp\htdocs\LEMS
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Setup Environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

4. **Create Database**
   ```sql
   CREATE DATABASE lems_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. **Import Schema**
   ```bash
   mysql -u root -p lems_db < database/schema.sql
   ```

6. **Set Permissions**
   ```bash
   chmod 755 public/uploads
   chmod 755 storage/logs
   chmod 755 storage/cache
   ```

7. **Configure Apache**
   - Point DocumentRoot to `public/` folder
   - Enable mod_rewrite

8. **Access Application**
   ```
   http://localhost/LEMS/public
   ```

## 🎨 View Template Structure

Each view should follow this structure:

```php
<?php
// views/users/index.php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

        <main class="col-md-12 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">User Management</h1>
            </div>

            <!-- Page Content Here -->

        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
```

## 🔧 Configuration Files

### .env Example
```env
# Application
APP_NAME="LEMS - Legal Enforcement Management System"
APP_ENV=development
APP_URL=http://localhost/LEMS/public
APP_VERSION=1.0

# Database
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=lems_db
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_LIFETIME=120
SESSION_NAME=LEMS_SESSION

# Upload
MAX_UPLOAD_SIZE=5242880
ALLOWED_EXTENSIONS=jpg,jpeg,png

# Pagination
ITEMS_PER_PAGE=20
```

## 📊 Architecture Principles

1. **Separation of Concerns**
   - Controllers: Handle HTTP requests
   - Models: Database operations
   - Views: Presentation layer

2. **DRY (Don't Repeat Yourself)**
   - Reusable components in helpers
   - Base model for common operations
   - Layout templates for UI

3. **Security First**
   - PDO prepared statements
   - CSRF protection
   - XSS prevention
   - Role-based access control

4. **Performance**
   - Lazy loading
   - Query optimization
   - Pagination
   - Caching (future)

5. **Scalability**
   - RESTful design ready
   - Modular structure
   - Easy to extend

## 🧪 Testing Structure (Future)

```
tests/
├── Unit/
│   ├── Models/
│   └── Helpers/
├── Integration/
│   └── Controllers/
└── Feature/
    └── Authentication/
```

## 📝 Coding Standards

- **PHP**: PSR-12
- **JavaScript**: ES6+
- **CSS**: BEM methodology
- **SQL**: Uppercase keywords
- **Comments**: PHPDoc format

## 🔄 Version Control

```
.gitignore should include:
/vendor/
/storage/logs/*
/storage/cache/*
/public/uploads/*
.env
.DS_Store
Thumbs.db
```
