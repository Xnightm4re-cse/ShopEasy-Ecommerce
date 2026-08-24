<?php
/*
 * logout.php  -  CUSTOMER LOGOUT
 * ----------------------------------------------------------
 * Ends the session and returns to the home page.
 * ----------------------------------------------------------
 */

require_once 'includes/functions.php'; // starts the session

// Remove all session data and destroy the session.
$_SESSION = [];
session_destroy();

// Back to the home page.
header('Location: index.php');
exit;
