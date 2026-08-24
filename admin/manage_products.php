<?php
/*
 * admin/manage_products.php  -  LEGACY FILE (kept only as a redirect)
 * ----------------------------------------------------------
 * This was an older, duplicate "product list" page. It has been
 * replaced by products.php, which is the page linked from the
 * admin menu.
 *
 * Why it was replaced:
 *   - It checked $_SESSION['admin_logged_in'], a session key that
 *     the login page never sets, so the page could never open.
 *   - It showed images from /uploads/ while the whole site stores
 *     product images in /images/.
 *   - It had no category, stock or edit support.
 *
 * The old address still works: it simply forwards to the current
 * page so any saved link or bookmark does not break.
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin_login();

redirect('products.php');
