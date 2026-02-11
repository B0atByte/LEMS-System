<?php
/**
 * LEMS - Routes Configuration
 *
 * Define all application routes here
 * Format: 'METHOD /path' => 'Controller@method'
 */

return [
    // Authentication
    'GET /' => 'AuthController@showLogin',
    'GET /login' => 'AuthController@showLogin',
    'POST /login' => 'AuthController@login',
    'GET /logout' => 'AuthController@logout',
    'GET /change-password' => 'AuthController@showChangePassword',
    'POST /change-password' => 'AuthController@changePassword',

    // Dashboard
    'GET /dashboard' => 'DashboardController@index',

    // User Management (Super Admin only)
    'GET /users' => 'UserController@index',
    'GET /users/create' => 'UserController@create',
    'POST /users/create' => 'UserController@store',
    'POST /users/store' => 'UserController@store',
    'GET /users/:id' => 'UserController@show',
    'GET /users/:id/edit' => 'UserController@edit',
    'POST /users/:id/update' => 'UserController@update',
    'POST /users/:id/delete' => 'UserController@delete',
    'POST /users/:id/toggle-status' => 'UserController@toggleStatus',
    'POST /users/:id/reset-password' => 'UserController@resetPassword',

    // Case Management (Admin)
    'GET /cases' => 'CaseController@index',
    'GET /cases/create' => 'CaseController@create',
    'POST /cases/create' => 'CaseController@store',
    'POST /cases/store' => 'CaseController@store',
    'GET /cases/:id' => 'CaseController@show',
    'GET /cases/:id/edit' => 'CaseController@edit',
    'POST /cases/:id/update' => 'CaseController@update',
    'POST /cases/:id/delete' => 'CaseController@delete',

    // Assignment Management
    'GET /assignments' => 'AssignmentController@index',
    'GET /assignments/create' => 'AssignmentController@create',
    'POST /assignments/create' => 'AssignmentController@store',
    'POST /assignments/store' => 'AssignmentController@store',
    'GET /assignments/:id' => 'AssignmentController@show',
    'POST /assignments/:id/cancel' => 'AssignmentController@cancel',

    // Field Reports (Officer)
    'GET /my-assignments' => 'FieldReportController@myAssignments',
    'POST /assignments/:id/start-work' => 'FieldReportController@startWork',
    'GET /assignments/:id/report' => 'FieldReportController@createReport',
    'POST /assignments/:id/report' => 'FieldReportController@storeReport',
    'GET /reports/:id' => 'FieldReportController@show',
    'GET /reports/:id/edit' => 'FieldReportController@edit',
    'POST /reports/:id/update' => 'FieldReportController@update',
    'POST /reports/:id/approve' => 'FieldReportController@approve',

    // Logs (IT & Super Admin)
    'GET /logs/login' => 'LogController@loginLogs',
    'GET /logs/activity' => 'LogController@activityLogs',
    'GET /logs/audit/:module/:id' => 'LogController@auditTrail',

    // Reports & Export
    'GET /reports' => 'ExportController@index',
    'POST /reports/export-excel' => 'ExportController@exportExcel',
    'POST /reports/export-word' => 'ExportController@exportWord',

    // API endpoints (Future use)
    'GET /api/officers' => 'UserController@getOfficers',
    'GET /api/cases/search' => 'CaseController@search',
];
