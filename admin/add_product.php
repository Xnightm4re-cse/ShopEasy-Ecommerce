<?php
/*
 * admin/add_product.php  -  LEGACY FILE (kept only as a redirect)
 * ----------------------------------------------------------
 * This was an older, duplicate "add product" page. It has been
 * replaced by add-product.php (note the hyphen), which is the
 * page linked from the admin menu.
 *
 * Why it was replaced:
 *   - It checked $_SESSION['admin_logged_in'], a session key that
 *     the login page never sets, so the page could never open.
 *   - It saved images into /uploads/ while the whole site reads
 *     product images from /images/, so pictures never appeared.
 *   - It did not save category_id or stock.
 *
 * The old address still works: it simply forwards to the current
 * page so any saved link or bookmark does not break.
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin_login();

redirect('add-product.php');
