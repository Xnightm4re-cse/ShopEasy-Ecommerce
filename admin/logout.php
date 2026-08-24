<?php
/*
 * admin/logout.php  -  ADMIN LOGOUT
 * ----------------------------------------------------------
 * Removes the admin login from the session, then returns to the
 * admin login page. (We only unset the admin keys so a customer
 * logged in on the same browser stays logged in.)
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';

unset($_SESSION['admin_id'], $_SESSION['admin_username']);

redirect('login.php');
