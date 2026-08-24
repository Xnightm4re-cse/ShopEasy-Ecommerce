<?php
/*
 * includes/header.php
 * ----------------------------------------------------------
 * The top part of every customer page: the <head> section and
 * the navigation bar. Pages set $page_title before including it.
 *
 * This file expects that config/database.php, includes/functions.php
 * and includes/auth.php have already been included by the page.
 * ----------------------------------------------------------
 */
$page_title = $page_title ?? 'ShopEasy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- The viewport tag makes the site responsive on phones/tablets -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> | ShopEasy</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ===================== NAVIGATION BAR ===================== -->
<header class="navbar">
    <div class="container nav-inner">
        <!-- Logo / site name -->
        <a href="index.php" class="logo">Shop<span>Easy</span></a>

        <!-- Hamburger button (only visible on small screens, toggled by JS) -->
        <button class="nav-toggle" id="navToggle" aria-label="Menu">&#9776;</button>

        <!-- Navigation links -->
        <nav class="nav-links" id="navLinks">
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="products.php#categories">Categories</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>

            <!-- Cart link with a badge showing how many items are inside -->
            <a href="cart.php" class="cart-link">
                Cart
                <span class="cart-badge"><?php echo cart_count(); ?></span>
            </a>

            <?php if (is_logged_in()): ?>
                <!-- Logged in: show the customer's name and a logout link -->
                <a href="account.php" class="nav-user">Hi, <?php echo e($_SESSION['user_name']); ?></a>
                <a href="logout.php" class="btn btn-small">Logout</a>
            <?php else: ?>
                <!-- Not logged in: show login / register -->
                <a href="login.php">Login</a>
                <a href="register.php" class="btn btn-small">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- Page content starts here -->
<main class="container page-content">
