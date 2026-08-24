<?php
/*
 * includes/auth.php
 * ----------------------------------------------------------
 * Helper functions for CUSTOMER login state.
 * The session stores 'user_id' and 'user_name' after login.
 * ----------------------------------------------------------
 */

/*
 * is_logged_in() - true if a customer is currently logged in.
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/*
 * require_login() - protect a page so only logged-in customers can see it.
 * If nobody is logged in, send them to the login page.
 */
function require_login()
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}
