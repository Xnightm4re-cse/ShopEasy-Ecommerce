<?php
/*
 * index.php  -  HOME PAGE
 * ----------------------------------------------------------
 * Shows a hero banner, the product categories, a few featured
 * products and a short about section.
 * ----------------------------------------------------------
 */

// 1) Load the database connection and helper files.
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// 2) Get the categories (for the "Shop by Category" section).
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

// 3) Get up to 8 featured products (newest first).
//    We JOIN categories so each product also knows its category name.
$featured = $conn->query(
    "SELECT p.*, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     ORDER BY p.id DESC
     LIMIT 8"
);

$page_title = 'Home';
include 'includes/header.php';
?>

<!-- ===================== HERO / BANNER ===================== -->
<section class="hero">
    <div class="hero-text">
        <h1>Shop Smarter, <span>Not Harder</span></h1>
        <p>Discover great products at great prices. Electronics, clothing, shoes,
           accessories and books - all in one simple store.</p>
        <a href="products.php" class="btn btn-large">Shop Now</a>
    </div>
</section>

<!-- ===================== CATEGORIES ===================== -->
<section id="categories" class="section">
    <h2 class="section-title">Shop by Category</h2>
    <div class="category-grid">
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <!-- Clicking a category opens the products page filtered by it -->
            <a href="products.php?category=<?php echo (int)$cat['id']; ?>" class="category-card">
                <span class="category-icon">&#128717;</span>
                <span class="category-title"><?php echo e($cat['name']); ?></span>
            </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- ===================== FEATURED PRODUCTS ===================== -->
<section class="section">
    <h2 class="section-title">Featured Products</h2>
    <div class="product-grid">
        <?php while ($product = $featured->fetch_assoc()): ?>
            <?php render_product_card($product); // shared card layout ?>
        <?php endwhile; ?>
    </div>
    <div class="center">
        <a href="products.php" class="btn btn-outline">View All Products</a>
    </div>
</section>

<!-- ===================== SHORT ABOUT ===================== -->
<section class="section about-strip">
    <div class="about-strip-inner">
        <h2>About ShopEasy</h2>
        <p>ShopEasy is a demo online store built as a university web programming project.
           It shows how a real e-commerce site works: browsing products, adding them to a
           cart, creating an account and placing orders - all powered by PHP and MySQL.</p>
        <a href="about.php" class="btn btn-outline">Learn More</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
