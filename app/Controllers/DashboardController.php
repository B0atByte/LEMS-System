<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\ActivityLog;

/**
 * DashboardController
 * Handles dashboard display for different roles
 */
class DashboardController extends BaseController
{
    private $userModel;
    private $activityLogModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Show dashboard based on user role
     */
    public function index(): void
    {
        $this->requireAuth();

        $role = $this->getUserRole();

        // Route to appropriate dashboard based on role
        switch ($role) {
            case 'super_admin':
                $this->showSuperAdminDashboard();
                break;
            case 'it':
                $this->showITDashboard();
                break;
            case 'admin':
                $this->showAdminDashboard();
                break;
            case 'officer':
                $this->showOfficerDashboard();
                break;
            default:
                $this->response->forbidden();
        }
    }

    /**
     * Super Admin Dashboard
     */
    private function showSuperAdminDashboard(): void
    {
        $this->view('dashboard/super_admin', [
            'pageTitle' => 'Dashboard - Super Admin',
            'role' => 'super_admin'
        ]);
    }

    /**
     * IT Dashboard
     */
    private function showITDashboard(): void
    {
        $this->view('dashboard/it', [
            'pageTitle' => 'Dashboard - IT',
            'role' => 'it'
        ]);
    }

    /**
     * Admin Dashboard
     */
    private function showAdminDashboard(): void
    {
        $this->view('dashboard/admin', [
            'pageTitle' => 'Dashboard - Admin',
            'role' => 'admin'
        ]);
    }

    /**
     * Officer Dashboard
     */
    private function showOfficerDashboard(): void
    {
        $this->view('dashboard/officer', [
            'pageTitle' => 'Dashboard - Officer',
            'role' => 'officer'
        ]);
    }
}
