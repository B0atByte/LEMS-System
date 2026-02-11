<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\ActivityLog;

/**
 * UserController
 * Manages user CRUD operations (Super Admin only)
 */
class UserController extends BaseController
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
     * List all users
     */
    public function index(): void
    {
        $this->requireRole(['super_admin']);

        $page = $this->request->query('page', 1);
        $perPage = 20;

        $result = $this->userModel->paginate($page, $perPage);

        $this->view('users/index', [
            'pageTitle' => 'User Management',
            'users' => $result['items'],
            'pagination' => $result
        ]);
    }

    /**
     * Show create user form
     */
    public function create(): void
    {
        $this->requireRole(['super_admin']);

        $this->view('users/create', [
            'pageTitle' => 'Create User'
        ]);
    }

    /**
     * Store new user
     */
    public function store(): void
    {
        $this->requireRole(['super_admin']);

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/users');
            return;
        }

        // Validate input
        $errors = $this->request->validate([
            'fullname' => 'required|min:3',
            'username' => 'required|min:4',
            'password' => 'required|min:8',
            'role' => 'required'
        ]);

        if (!empty($errors)) {
            $this->setFlash('error', 'กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง');
            $this->redirect('/users/create');
            return;
        }

        $username = $this->request->input('username');
        $fullname = $this->request->input('fullname');
        $password = $this->request->input('password');
        $role = $this->request->input('role');
        $status = $this->request->input('status', 'active');

        // Check if username exists
        if ($this->userModel->usernameExists($username)) {
            $this->setFlash('error', 'Username นี้ถูกใช้งานแล้ว');
            $this->redirect('/users/create');
            return;
        }

        // Create user
        $userId = $this->userModel->createUser([
            'fullname' => $fullname,
            'username' => $username,
            'password' => $password,
            'role' => $role,
            'status' => $status,
            'created_by' => $this->getUserId()
        ]);

        // Log activity
        $this->activityLogModel->log(
            $this->getUserId(),
            $this->getUsername(),
            'CREATE',
            'users',
            $userId,
            "Created new user: {$username}",
            null,
            ['username' => $username, 'role' => $role],
            $this->getClientIp(),
            $this->getUserAgent()
        );

        $this->setFlash('success', 'เพิ่มผู้ใช้สำเร็จ');
        $this->redirect('/users');
    }

    /**
     * Show user details
     */
    public function show(): void
    {
        $this->requireRole(['super_admin']);

        $id = $this->request->param('id');
        $user = $this->userModel->find($id);

        if (!$user) {
            $this->response->notFound();
            return;
        }

        $this->view('users/view', [
            'pageTitle' => 'User Details',
            'user' => $user
        ]);
    }

    /**
     * Show edit user form
     */
    public function edit(): void
    {
        $this->requireRole(['super_admin']);

        $id = $this->request->param('id');
        $user = $this->userModel->find($id);

        if (!$user) {
            $this->response->notFound();
            return;
        }

        $this->view('users/edit', [
            'pageTitle' => 'Edit User',
            'user' => $user
        ]);
    }

    /**
     * Update user
     */
    public function update(): void
    {
        $this->requireRole(['super_admin']);

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/users');
            return;
        }

        $id = $this->request->param('id');
        $oldUser = $this->userModel->find($id);

        if (!$oldUser) {
            $this->response->notFound();
            return;
        }

        $fullname = $this->request->input('fullname');
        $username = $this->request->input('username');
        $role = $this->request->input('role');
        $status = $this->request->input('status');

        // Check if username exists (excluding current user)
        if ($this->userModel->usernameExists($username, $id)) {
            $this->setFlash('error', 'Username นี้ถูกใช้งานแล้ว');
            $this->redirect("/users/{$id}/edit");
            return;
        }

        // Update user
        $this->userModel->update($id, [
            'fullname' => $fullname,
            'username' => $username,
            'role' => $role,
            'status' => $status,
            'updated_by' => $this->getUserId()
        ]);

        // Log activity
        $this->activityLogModel->log(
            $this->getUserId(),
            $this->getUsername(),
            'UPDATE',
            'users',
            $id,
            "Updated user: {$username}",
            $oldUser,
            ['fullname' => $fullname, 'username' => $username, 'role' => $role, 'status' => $status],
            $this->getClientIp(),
            $this->getUserAgent()
        );

        $this->setFlash('success', 'แก้ไขข้อมูลผู้ใช้สำเร็จ');
        $this->redirect('/users');
    }

    /**
     * Delete user
     */
    public function delete(): void
    {
        $this->requireRole(['super_admin']);

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/users');
            return;
        }

        $id = $this->request->param('id');
        $user = $this->userModel->find($id);

        if (!$user) {
            $this->response->notFound();
            return;
        }

        // Cannot delete yourself
        if ($id == $this->getUserId()) {
            $this->setFlash('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
            $this->redirect('/users');
            return;
        }

        // Delete user
        $this->userModel->delete($id);

        // Log activity
        $this->activityLogModel->log(
            $this->getUserId(),
            $this->getUsername(),
            'DELETE',
            'users',
            $id,
            "Deleted user: {$user['username']}",
            $user,
            null,
            $this->getClientIp(),
            $this->getUserAgent()
        );

        $this->setFlash('success', 'ลบผู้ใช้สำเร็จ');
        $this->redirect('/users');
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(): void
    {
        $this->requireRole(['super_admin']);

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/users');
            return;
        }

        $id = $this->request->param('id');

        // Cannot toggle yourself
        if ($id == $this->getUserId()) {
            $this->setFlash('error', 'ไม่สามารถเปลี่ยนสถานะของตัวเองได้');
            $this->redirect('/users');
            return;
        }

        $this->userModel->toggleStatus($id);

        $this->setFlash('success', 'เปลี่ยนสถานะผู้ใช้สำเร็จ');
        $this->redirect('/users');
    }

    /**
     * Reset user password
     */
    public function resetPassword(): void
    {
        $this->requireRole(['super_admin']);

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/users');
            return;
        }

        $id = $this->request->param('id');
        $user = $this->userModel->find($id);

        if (!$user) {
            $this->response->notFound();
            return;
        }

        // Reset to default password
        $newPassword = 'Admin@123';
        $this->userModel->updatePassword($id, $newPassword);

        // Log activity
        $this->activityLogModel->log(
            $this->getUserId(),
            $this->getUsername(),
            'UPDATE',
            'users',
            $id,
            "Reset password for user: {$user['username']}",
            null,
            null,
            $this->getClientIp(),
            $this->getUserAgent()
        );

        $this->setFlash('success', "รีเซ็ตรหัสผ่านสำเร็จ (Password ใหม่: {$newPassword})");
        $this->redirect('/users');
    }

    /**
     * Get officers for API (AJAX)
     */
    public function getOfficers(): void
    {
        $this->requireAuth();

        $officers = $this->userModel->getOfficers();

        $this->json($officers);
    }
}
