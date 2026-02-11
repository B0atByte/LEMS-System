<?php
/**
 * LEMS - Root Index
 * Redirects to public directory
 */

// Get the base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$publicUrl = $protocol . '://' . $host . $scriptDir . '/public/';

header('Location: ' . $publicUrl);
exit;
