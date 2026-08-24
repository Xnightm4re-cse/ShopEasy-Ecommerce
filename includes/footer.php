<?php
/*
 * includes/footer.php
 * ----------------------------------------------------------
 * The bottom part of every customer page: the footer and the
 * closing HTML tags. It also loads the JavaScript file.
 * ----------------------------------------------------------
 */
?>
</main><!-- /.page-content -->

<!-- ===================== FOOTER ===================== -->
<footer class="footer">
    <div class="container footer-inner">
        <div class="footer-col">
            <h3 class="logo">Shop<span>Easy</span></h3>
            <p>Your simple online shop for electronics, clothing, shoes, accessories and books.</p>
        </div>
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Account</h4>
            <ul>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="cart.php">Shopping Cart</a></li>
                <li><a href="account.php">My Account</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact</h4>
            <p>Email: support@shopeasy.test</p>
            <p>Phone: +1 555 000 1234</p>
            <p>123 Main Street, Springfield</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> ShopEasy. Academic project - not a real store.</p>
    </div>
</footer>

<script src="js/script.js"></script>
</body>
</html>
