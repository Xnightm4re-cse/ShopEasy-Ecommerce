<?php
/*
 * config/database.php
 * ----------------------------------------------------------
 * Creates the connection to the MySQL database.
 * Every page that needs the database includes this file.
 *
 * We use MySQLi (MySQL Improved) with the default XAMPP settings:
 *   host     = localhost
 *   username = root
 *   password = (empty)
 *   database = ecommerce_db
 * ----------------------------------------------------------
 */

// --- Database settings (default XAMPP values) ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'ecommerce_db';

// Make MySQLi throw an exception when a query fails.
// This makes errors easy to see and lets us use try/catch (e.g. at checkout).
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Open the connection.
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Use UTF-8 so special characters are stored and shown correctly.
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Friendly message if the database is not ready.
    die('Database connection failed. Please make sure MySQL is running in XAMPP '
        . 'and that you have imported database.sql into phpMyAdmin.');
}
