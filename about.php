<?php
/*
 * about.php  -  ABOUT PAGE
 * A simple static information page.
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$page_title = 'About';
include 'includes/header.php';
?>

<h1 class="page-title">About ShopEasy</h1>

<div class="text-page">
    <p>ShopEasy is a demonstration online store created as a <strong>university web
       programming project</strong>. It was built using HTML, CSS, JavaScript, PHP and
       MySQL, and runs on a local XAMPP server.</p>

    <h2>What you can do here</h2>
    <ul>
        <li>Browse products by category or search for them by name.</li>
        <li>View detailed information about each product.</li>
        <li>Add products to a shopping cart and change quantities.</li>
        <li>Create an account, log in, and place orders.</li>
        <li>View your order history in your account.</li>
    </ul>

    <h2>How it works</h2>
    <p>The website uses PHP to talk to a MySQL database. Products, categories, users
       and orders are all stored in database tables. When you place an order, it is
       saved in the database and the product stock is reduced automatically.</p>

    <p>This project is for educational purposes only. No real payments are taken and
       the only payment method is "Cash on Delivery".</p>
</div>

<?php include 'includes/footer.php'; ?>
