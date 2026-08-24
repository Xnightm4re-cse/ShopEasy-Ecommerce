<?php
/*
 * admin/delete-product.php  -  DELETE A PRODUCT
 * ----------------------------------------------------------
 * Deletes the product whose id is given in the URL, then goes
 * back to the product list. The confirmation dialog is shown by
 * JavaScript on the products page before this file is opened.
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../config/database.php';
require_admin_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Delete the product (prepared statement).
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

redirect('products.php?msg=deleted');
