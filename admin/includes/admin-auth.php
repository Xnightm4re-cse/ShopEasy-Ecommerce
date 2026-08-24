<?php
/*
 * admin/includes/admin-auth.php
 * ----------------------------------------------------------
 * Helper functions for ADMIN login state, plus it loads the
 * shared helper functions (which also start the session).
 *
 * The admin session uses different keys ('admin_id') from the
 * customer session ('user_id'), so a logged-in customer is NOT
 * automatically an admin, and vice versa.
 * ----------------------------------------------------------
 */

// Load the shared helpers (e(), money(), redirect(), and session start).
// __DIR__ is this file's folder (admin/includes), so we go up two levels.
require_once __DIR__ . '/../../includes/functions.php';

/*
 * is_admin_logged_in() - true if an admin is logged in.
 */
function is_admin_logged_in()
{
    return isset($_SESSION['admin_id']);
}

/*
 * require_admin_login() - protect an admin page.
 * If no admin is logged in, send them to the admin login page.
 */
function require_admin_login()
{
    if (!is_admin_logged_in()) {
        redirect('login.php');
    }
}
