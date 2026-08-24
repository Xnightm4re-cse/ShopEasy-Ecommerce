<?php
/*
 * config/database.php
 * ----------------------------------------------------------
 * Central MySQL Database Connection Configuration
 *
 * This file handles database connectivity for the entire application.
 *
 * LOCAL XAMPP DEFAULTS:
 *   - Host:     localhost
 *   - User:     root
 *   - Password: (empty)
 *   - Database: ecommerce_db
 *
 * ONLINE HOSTING:
 *   You can either:
 *   1. Update the variables below with your hosting database credentials, OR
 *   2. Set environment variables (DB_HOST, DB_USER, DB_PASS, DB_NAME).
 * ----------------------------------------------------------
 */

// Database connection settings
$db_host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
$db_user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'ecommerce_db';

// Make MySQLi throw an exception on errors for structured try/catch handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Open MySQL database connection
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Set charset to utf8mb4 for complete unicode support
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Friendly error message without exposing internal credentials
    die('Database connection failed. Please make sure MySQL is running '
        . 'and database credentials in config/database.php are correct.');
}

