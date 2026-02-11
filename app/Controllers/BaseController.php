<?php
namespace App\Controllers;

use Core\Request;
use Core\Response;
use Core\Session;

/**
 * BaseController
 * Base class for all controllers
 */
abstract class BaseController
{
    protected $request;
    protected $response;
    protected $session;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->session = new Session();
    }

    /**
     * Render a view
     */
    protected function view(string $view, array $data = []): void
    {
        // Extract data to variables
        extract($data);

        // Check if view file exists
        $viewPath = BASE_PATH . '/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            $this->response->serverError("View {$view} not found");
            return;
        }

        require $viewPath;
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $path, int $statusCode = 302): void
    {
        $fullUrl = url($path);
        $this->response->redirect($fullUrl, $statusCode);
    }

    /**
     * Return JSON response
     */
    protected function json($data, int $statusCode = 200): void
    {
        $this->response->json($data, $statusCode);
    }

    /**
     * Return success JSON response
     */
    protected function success($data = [], string $message = 'Success', int $statusCode = 200): void
    {
        $this->response->success($data, $message, $statusCode);
    }

    /**
     * Return error JSON response
     */
    protected function error(string $message = 'Error', $errors = [], int $statusCode = 400): void
    {
        $this->response->error($message, $errors, $statusCode);
    }

    /**
     * Check if user is logged in
     */
    protected function requireAuth(): void
    {
        if (!$this->session->isLoggedIn()) {
            $this->session->flash('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
            $this->redirect('/login');
        }
    }

    /**
     * Check if user has required role
     */
    protected function requireRole(array $allowedRoles): void
    {
        $this->requireAuth();

        if (!$this->session->hasAnyRole($allowedRoles)) {
            $this->response->forbidden();
        }
    }

    /**
     * Verify CSRF token
     */
    protected function verifyCsrf(): bool
    {
        $token = $this->request->input('csrf_token');

        if (!$token || !csrf_verify($token)) {
            $this->session->flash('error', 'Invalid CSRF token');
            return false;
        }

        return true;
    }

    /**
     * Get current user ID
     */
    protected function getUserId(): ?int
    {
        return $this->session->getUserId();
    }

    /**
     * Get current username
     */
    protected function getUsername(): ?string
    {
        return $this->session->getUsername();
    }

    /**
     * Get current user role
     */
    protected function getUserRole(): ?string
    {
        return $this->session->getUserRole();
    }

    /**
     * Set flash message
     */
    protected function setFlash(string $type, string $message): void
    {
        $this->session->flash($type, $message);
    }

    /**
     * Get client IP
     */
    protected function getClientIp(): string
    {
        return $this->request->ip();
    }

    /**
     * Get user agent
     */
    protected function getUserAgent(): string
    {
        return $this->request->userAgent();
    }
}
