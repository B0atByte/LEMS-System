<?php
// Test file to check if Apache is working
echo "<h1>✅ Apache is working!</h1>";
echo "<p>SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Test mod_rewrite
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✅ mod_rewrite is ENABLED</p>";
    } else {
        echo "<p style='color: red;'>❌ mod_rewrite is DISABLED</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Cannot check mod_rewrite status</p>";
}

echo "<hr>";
echo "<h2>Session Test</h2>";
session_start();
echo "<p>Session ID: " . session_id() . "</p>";

echo "<hr>";
echo "<h2>Database Config</h2>";
require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
echo "<p>DB_HOST: " . $_ENV['DB_HOST'] . "</p>";
echo "<p>DB_DATABASE: " . $_ENV['DB_DATABASE'] . "</p>";
echo "<p>DB_USERNAME: " . $_ENV['DB_USERNAME'] . "</p>";
