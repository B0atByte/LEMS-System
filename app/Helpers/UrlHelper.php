<?php
/**
 * URL Helper Functions
 * Provides URL generation utilities
 */

/**
 * Get base path for URLs
 */
function base_path_url(): string {
    static $basePath = null;

    if ($basePath === null) {
        $scriptName = $_SERVER['SCRIPT_NAME']; // /LEMS/public/index.php
        $basePath = dirname($scriptName); // /LEMS/public
    }

    return $basePath;
}

/**
 * Generate full URL with base path
 */
function url(string $path = ''): string {
    $basePath = base_path_url();

    // Remove leading slash from path if exists
    $path = ltrim($path, '/');

    // Ensure base path ends without slash
    $basePath = rtrim($basePath, '/');

    return $basePath . '/' . $path;
}

/**
 * Redirect to URL with base path
 */
function redirect_to(string $path, int $statusCode = 302): void {
    $url = url($path);
    header("Location: {$url}", true, $statusCode);
    exit;
}

/**
 * Get current URL
 */
function current_url(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Get asset URL
 */
function asset(string $path): string {
    $basePath = base_path_url();
    $path = ltrim($path, '/');
    return $basePath . '/' . $path;
}
