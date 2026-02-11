<?php
namespace Core;

/**
 * Session Class
 * Manages user sessions securely
 */
class Session
{
    private static $started = false;
    private $config;

    public function __construct()
    {
        $appConfig = require __DIR__ . '/../config/app.php';
        $this->config = $appConfig['session'];
        $this->start();
    }

    /**
     * Start session if not already started
     */
    public function start(): void
    {
        if (!self::$started && session_status() === PHP_SESSION_NONE) {
            // Configure session
            ini_set('session.cookie_httponly', $this->config['httponly'] ? '1' : '0');
            ini_set('session.cookie_secure', $this->config['secure'] ? '1' : '0');
            ini_set('session.cookie_samesite', $this->config['samesite']);
            ini_set('session.gc_maxlifetime', $this->config['lifetime'] * 60);

            session_name($this->config['name']);
            session_start();
            self::$started = true;

            // Regenerate session ID periodically
            if (!$this->has('_session_started')) {
                session_regenerate_id(true);
                $this->set('_session_started', time());
            }

            // Check session timeout
            $this->checkTimeout();
        }
    }

    /**
     * Set session variable
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
        $_SESSION['_last_activity'] = time();
    }

    /**
     * Get session variable
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session variable exists
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session variable
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy session
     */
    public function destroy(): void
    {
        $_SESSION = [];

        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(
                session_name(),
                '',
                time() - 3600,
                '/',
                '',
                $this->config['secure'],
                $this->config['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    /**
     * Check session timeout
     */
    private function checkTimeout(): void
    {
        $lastActivity = $this->get('_last_activity', time());
        $timeout = $this->config['lifetime'] * 60;

        if (time() - $lastActivity > $timeout) {
            $this->destroy();
            header('Location: /login?timeout=1');
            exit;
        }

        $this->set('_last_activity', time());
    }

    /**
     * Flash message - Set message for next request only
     */
    public function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get flash message
     */
    public function getFlash(string $key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Check if flash message exists
     */
    public function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    /**
     * Get all flash messages
     */
    public function getAllFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flash;
    }

    /**
     * Get user ID from session
     */
    public function getUserId(): ?int
    {
        return $this->get('user_id');
    }

    /**
     * Get user role from session
     */
    public function getUserRole(): ?string
    {
        return $this->get('user_role');
    }

    /**
     * Get username from session
     */
    public function getUsername(): ?string
    {
        return $this->get('username');
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool
    {
        return $this->has('user_id');
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->getUserRole() === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->getUserRole(), $roles);
    }

    /**
     * Set user session data after login
     */
    public function setUserData(array $user): void
    {
        $this->set('user_id', $user['id']);
        $this->set('username', $user['username']);
        $this->set('fullname', $user['fullname']);
        $this->set('user_role', $user['role']);
        $this->set('login_time', time());

        // Regenerate session ID for security
        session_regenerate_id(true);
    }

    /**
     * Clear user session data on logout
     */
    public function clearUserData(): void
    {
        $this->remove('user_id');
        $this->remove('username');
        $this->remove('fullname');
        $this->remove('user_role');
        $this->remove('login_time');
    }
}
