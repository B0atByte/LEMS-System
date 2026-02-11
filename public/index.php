<?php
/**
 * LEMS - Legal Enforcement Management System Bargainpoint
 * Application Entry Point
 * Version: 1.0 Enterprise Edition
 */

// Error Reporting (based on environment)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('Asia/Bangkok');

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoload dependencies
require_once BASE_PATH . '/vendor/autoload.php';

// Load environment variables
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Enable display errors if in development
if ($_ENV['APP_ENV'] === 'development' && $_ENV['APP_DEBUG'] === 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

// Start session
use Core\Session;
$session = new Session();

// Create request and response objects
use Core\Request;
use Core\Response;
$request = new Request();
$response = new Response();

// Create router
use Core\Router;
$router = new Router($request, $response);

// Load routes
$routes = require_once BASE_PATH . '/config/routes.php';
$router->loadRoutes($routes);

// Dispatch request
try {
    $router->dispatch();
} catch (Exception $e) {
    // Log error
    error_log('Application Error: ' . $e->getMessage());

    // Show error page
    if ($_ENV['APP_DEBUG'] === 'true') {
        echo '<h1>Application Error</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        $response->serverError();
    }
}
