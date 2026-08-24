<?php
/*
 * admin/includes/admin-header.php
 * ----------------------------------------------------------
 * The top bar + side menu shown on every admin page.
 * Pages set $page_title and $active before including this.
 * Pages must call require_admin_login() BEFORE including it.
 * ----------------------------------------------------------
 */
$page_title = $page_title ?? 'Admin';
$active     = $active ?? '';

// Small helper: prints "active" if the given menu key is the current page.
function nav_active($key, $active)
{
    return $key === $active ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> | ShopEasy Admin</title>
    <!-- Admin pages are inside /admin, so the CSS is one folder up -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">

<!-- Top bar -->
<header class="admin-topbar">
    <button class="nav-toggle" id="adminNavToggle" aria-label="Menu">&#9776;</button>
    <span class="admin-logo">Shop<span>Easy</span> Admin</span>
    <div class="admin-topbar-right">
        <span class="admin-hello">Hello, <?php echo e($_SESSION['admin_username'] ?? 'Admin'); ?></span>
        <a href="logout.php" class="btn btn-small">Logout</a>
    </div>
</header>

<div class="admin-layout">
    <!-- Side menu -->
    <aside class="admin-sidebar" id="adminSidebar">
        <nav>
            <a href="index.php"      class="<?php echo nav_active('dashboard', $active); ?>">Dashboard</a>
            <a href="products.php"   class="<?php echo nav_active('products', $active); ?>">Products</a>
            <a href="categories.php" class="<?php echo nav_active('categories', $active); ?>">Categories</a>
            <a href="orders.php"     class="<?php echo nav_active('orders', $active); ?>">Orders</a>
            <a href="customers.php"  class="<?php echo nav_active('customers', $active); ?>">Customers</a>
            <a href="../index.php" target="_blank">View Store &#8599;</a>
        </nav>
    </aside>

    <!-- Main content area -->
    <main class="admin-main">
