<?php
/**
 * Security Helper Functions
 * Provides security-related utility functions
 */

/**
 * Escape HTML output to prevent XSS
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrf_token(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF token field for forms
 */
function csrf_field(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF token
 */
function csrf_verify(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash password
 */
function hash_password(string $password): string {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verify_password(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Generate random string
 */
function random_string(int $length = 32): string {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Sanitize input
 */
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Clean input array
 */
function clean_array(array $data): array {
    $cleaned = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $cleaned[$key] = clean_array($value);
        } else {
            $cleaned[$key] = sanitize($value);
        }
    }
    return $cleaned;
}

/**
 * Get current user ID from session
 */
function user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username from session
 */
function username(): ?string {
    return $_SESSION['username'] ?? null;
}

/**
 * Get current user role from session
 */
function user_role(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 */
function has_role(string $role): bool {
    return user_role() === $role;
}

/**
 * Check if user has any of the specified roles
 */
function has_any_role(array $roles): bool {
    return in_array(user_role(), $roles);
}

/**
 * Redirect with authorization check
 */
function authorize_or_redirect(array $allowedRoles, string $redirectUrl = '/login'): void {
    if (!is_logged_in() || !has_any_role($allowedRoles)) {
        header('Location: ' . $redirectUrl);
        exit;
    }
}
