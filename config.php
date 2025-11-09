<?php
session_start();

// Database configuration - WILL BE SET IN ENVIRONMENT
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'vladastore');
define('DB_USER', getenv('DB_USER') ?: 'vladastore_user');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Telegram configuration
define('TELEGRAM_API_URL', 'https://api.telegram.org/bot');

// Base URL - WILL BE SET IN ENVIRONMENT
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost:8000');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ... rest of the config remains the same
?>