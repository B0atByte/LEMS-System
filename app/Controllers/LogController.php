<?php
namespace App\Controllers;

use App\Models\LoginLog;
use App\Models\ActivityLog;

/**
 * LogController
 * ดู Login Logs และ Activity Logs
 */
class LogController extends BaseController
{
    private $loginLogModel;
    private $activityLogModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->loginLogModel = new LoginLog();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * View login logs
     */
    public function loginLogs(): void
    {
        $this->requireRole(['super_admin', 'it']);

        $page = $this->request->query('page', 1);
        $filters = [
            'username' => $this->request->query('username'),
            'status' => $this->request->query('status'),
            'date_from' => $this->request->query('date_from'),
            'date_to' => $this->request->query('date_to')
        ];

        $result = $this->loginLogModel->getLoginLogs($page, 20, $filters);

        $this->view('logs/login_logs', [
            'pageTitle' => 'Login Logs',
            'logs' => $result['items'],
            'pagination' => $result,
            'filters' => $filters
        ]);
    }

    /**
     * View activity logs
     */
    public function activityLogs(): void
    {
        $this->requireRole(['super_admin', 'it']);

        $page = $this->request->query('page', 1);
        $filters = [
            'username' => $this->request->query('username'),
            'action_type' => $this->request->query('action_type'),
            'module' => $this->request->query('module'),
            'date_from' => $this->request->query('date_from'),
            'date_to' => $this->request->query('date_to')
        ];

        $result = $this->activityLogModel->getActivityLogs($page, 20, $filters);

        $this->view('logs/activity_logs', [
            'pageTitle' => 'Activity Logs',
            'logs' => $result['items'],
            'pagination' => $result,
            'filters' => $filters
        ]);
    }

    /**
     * View audit trail for specific record
     */
    public function auditTrail(): void
    {
        $this->requireRole(['super_admin', 'it']);

        $module = $this->request->param('module');
        $id = $this->request->param('id');

        $logs = $this->activityLogModel->getAuditTrail($module, $id);

        $this->view('logs/audit_trail', [
            'pageTitle' => 'Audit Trail',
            'logs' => $logs,
            'module' => $module,
            'reference_id' => $id
        ]);
    }
}
