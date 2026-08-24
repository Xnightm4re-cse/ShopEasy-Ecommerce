<?php
/*
 * config/db.php
 * ----------------------------------------------------------
 * Older name for the database connection file.
 *
 * The project now keeps the real connection settings in ONE
 * place: config/database.php. This file simply loads that one,
 * so there is only a single copy of the username / password /
 * database name to maintain.
 *
 * After including this file you still get the $conn object,
 * exactly as before.
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/database.php';
