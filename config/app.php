<?php
/**
 * LEMS - Application Configuration
 *
 * This file loads environment variables and provides application settings
 */

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

return [
    // Application
    'name' => $_ENV['APP_NAME'] ?? 'LEMS',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'version' => $_ENV['APP_VERSION'] ?? '1.0',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Asia/Bangkok',

    // Security
    'csrf_token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? 'csrf_token',
    'password_min_length' => (int) ($_ENV['PASSWORD_MIN_LENGTH'] ?? 8),

    // Session
    'session' => [
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120),
        'name' => $_ENV['SESSION_NAME'] ?? 'LEMS_SESSION',
        'secure' => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'httponly' => filter_var($_ENV['SESSION_HTTPONLY'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'samesite' => $_ENV['SESSION_SAMESITE'] ?? 'Lax',
    ],

    // Upload
    'upload' => [
        'max_size' => (int) ($_ENV['MAX_UPLOAD_SIZE'] ?? 5242880), // 5MB default
        'allowed_extensions' => explode(',', $_ENV['ALLOWED_IMAGE_EXTENSIONS'] ?? 'jpg,jpeg,png'),
        'path' => $_ENV['UPLOAD_PATH'] ?? 'uploads/field_reports',
    ],

    // Pagination
    'pagination' => [
        'per_page' => (int) ($_ENV['ITEMS_PER_PAGE'] ?? 20),
        'max_links' => (int) ($_ENV['MAX_PAGINATION_LINKS'] ?? 5),
    ],

    // Logging
    'logging' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
        'path' => $_ENV['LOG_PATH'] ?? 'storage/logs',
        'max_files' => (int) ($_ENV['LOG_MAX_FILES'] ?? 30),
    ],

    // Export
    'export' => [
        'temp_path' => $_ENV['EXPORT_TEMP_PATH'] ?? 'storage/cache',
        'memory_limit' => $_ENV['EXPORT_MEMORY_LIMIT'] ?? '512M',
    ],

    // Roles
    'roles' => [
        'super_admin' => 'super_admin',
        'it' => 'it',
        'admin' => 'admin',
        'officer' => 'officer',
    ],

    // Role Permissions
    'permissions' => [
        'super_admin' => ['*'], // All permissions
        'it' => ['view_logs', 'view_audit_trail'],
        'admin' => ['manage_cases', 'manage_assignments', 'approve_reports', 'export'],
        'officer' => ['view_assignments', 'submit_reports'],
    ],
];
