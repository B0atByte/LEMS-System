<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\LoginLog;
use App\Models\ActivityLog;

/**
 * AuthController
 * Handles authentication (login, logout, password change)
 */
class AuthController extends BaseController
{
    private $userModel;
    private $loginLogModel;
    private $activityLogModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->userModel = new User();
        $this->loginLogModel = new LoginLog();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Show login page
     */
    public function showLogin(): void
    {
        // Redirect if already logged in
        if ($this->session->isLoggedIn()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->view('auth/login', [
            'pageTitle' => 'Login - LEMS'
        ]);
    }

    /**
     * Process login
     */
    public function login(): void
    {
        if (!$this->request->isPost()) {
            $this->redirect('/login');
            return;
        }

        // Verify CSRF token
        if (!$this->verifyCsrf()) {
            $this->redirect('/login');
            return;
        }

        $username = $this->request->input('username');
        $password = $this->request->input('password');

        // Validate input
        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'กรุณากรอก Username และ Password');
            $this->redirect('/login');
            return;
        }

        // Verify credentials
        $user = $this->userModel->verifyCredentials($username, $password);

        if (!$user) {
            // Log failed attempt
            $this->loginLogModel->logFailedLogin(
                $username,
                $this->getClientIp(),
                $this->getUserAgent(),
                'Invalid username or password'
            );

            $this->setFlash('error', 'Username หรือ Password ไม่ถูกต้อง');
            $this->redirect('/login');
            return;
        }

        // Check if user is active
        if ($user['status'] !== 'active') {
            $this->loginLogModel->logFailedLogin(
                $username,
                $this->getClientIp(),
                $this->getUserAgent(),
                'Account is inactive'
            );

            $this->setFlash('error', 'บัญชีผู้ใช้ถูกระงับการใช้งาน');
            $this->redirect('/login');
            return;
        }

        // Login successful - Set session
        $this->session->setUserData($user);

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Log successful login
        $loginLogId = $this->loginLogModel->logLogin(
            $user['id'],
            $user['username'],
            $this->getClientIp(),
            $this->getUserAgent()
        );

        // Store login log ID in session for logout
        $this->session->set('login_log_id', $loginLogId);

        // Log activity
        $this->activityLogModel->log(
            $user['id'],
            $user['username'],
            'LOGIN',
            'auth',
            null,
            'User logged in successfully',
            null,
            null,
            $this->getClientIp(),
            $this->getUserAgent()
        );

        $this->setFlash('success', 'เข้าสู่ระบบสำเร็จ');
        $this->redirect('/dashboard');
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }

        $userId = $this->getUserId();
        $username = $this->getUsername();
        $loginLogId = $this->session->get('login_log_id');

        // Log logout
        if ($loginLogId) {
            $this->loginLogModel->logLogout($loginLogId);
        }

        // Log activity
        $this->activityLogModel->log(
            $userId,
            $username,
            'LOGOUT',
            'auth',
            null,
            'User logged out',
            null,
            null,
            $this->getClientIp(),
            $this->getUserAgent()
        );

        // Clear session
        $this->session->clearUserData();

        $this->setFlash('success', 'ออกจากระบบสำเร็จ');
        $this->redirect('/login');
    }

    /**
     * Show change password form
     */
    public function showChangePassword(): void
    {
        $this->requireAuth();

        $this->view('auth/change_password', [
            'pageTitle' => 'Change Password - LEMS'
        ]);
    }

    /**
     * Process change password
     */
    public function changePassword(): void
    {
        $this->requireAuth();

        if (!$this->request->isPost()) {
            $this->redirect('/change-password');
            return;
        }

        // Verify CSRF token
        if (!$this->verifyCsrf()) {
            $this->redirect('/change-password');
            return;
        }

        $currentPassword = $this->request->input('current_password');
        $newPassword = $this->request->input('new_password');
        $confirmPassword = $this->request->input('confirm_password');

        // Validate input
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->setFlash('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
            $this->redirect('/change-password');
            return;
        }

        // Check password length
        if (strlen($newPassword) < 8) {
            $this->setFlash('error', 'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร');
            $this->redirect('/change-password');
            return;
        }

        // Check if passwords match
        if ($newPassword !== $confirmPassword) {
            $this->setFlash('error', 'รหัสผ่านใหม่ไม่ตรงกัน');
            $this->redirect('/change-password');
            return;
        }

        // Verify current password
        $user = $this->userModel->find($this->getUserId());
        if (!password_verify($currentPassword, $user['password'])) {
            $this->setFlash('error', 'รหัสผ่านปัจจุบันไม่ถูกต้อง');
            $this->redirect('/change-password');
            return;
        }

        // Update password
        $this->userModel->updatePassword($this->getUserId(), $newPassword);

        // Log activity
        $this->activityLogModel->log(
            $this->getUserId(),
            $this->getUsername(),
            'UPDATE',
            'users',
            $this->getUserId(),
            'User changed password',
            null,
            null,
            $this->getClientIp(),
            $this->getUserAgent()
        );

        $this->setFlash('success', 'เปลี่ยนรหัสผ่านสำเร็จ');
        $this->redirect('/dashboard');
    }
}
