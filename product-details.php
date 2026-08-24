<?php
/*
 * product-details.php  -  SINGLE PRODUCT PAGE
 * ----------------------------------------------------------
 * Shows one product in detail with a quantity selector and an
 * "Add to Cart" button. The quantity cannot be more than stock.
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Which product? Read the id from the URL (?id=5).
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch that product (with its category name) using a prepared statement.
$stmt = $conn->prepare(
    "SELECT p.*, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

$page_title = $product ? $product['name'] : 'Product';
include 'includes/header.php';
?>

<?php if (!$product): ?>
    <!-- The product id was wrong / does not exist -->
    <p class="empty-message">Sorry, that product was not found.</p>
    <div class="center"><a href="products.php" class="btn">Back to Products</a></div>
<?php else: ?>
    <?php $inStock = (int)$product['stock'] > 0; ?>

    <a href="products.php" class="back-link">&larr; Back to Products</a>

    <div class="product-detail">
        <!-- Large product image -->
        <div class="product-detail-image">
            <img src="images/<?php echo e($product['image']); ?>"
                 alt="<?php echo e($product['name']); ?>"
                 onerror="this.onerror=null;this.src='images/placeholder.svg';">
        </div>

        <!-- Product information -->
        <div class="product-detail-info">
            <span class="product-category"><?php echo e($product['category_name'] ?? 'Uncategorized'); ?></span>
            <h1><?php echo e($product['name']); ?></h1>
            <p class="product-price large"><?php echo money($product['price']); ?></p>

            <?php if ($inStock): ?>
                <p class="stock in-stock">In Stock: <?php echo (int)$product['stock']; ?> available</p>
            <?php else: ?>
                <p class="stock out-stock">Out of Stock</p>
            <?php endif; ?>

            <p class="product-full-desc"><?php echo nl2br(e($product['description'])); ?></p>

            <?php if ($inStock): ?>
                <!-- Add to cart form. JavaScript + PHP both keep quantity within stock. -->
                <form method="post" action="cart.php" class="add-to-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">

                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity"
                           value="1" min="1" max="<?php echo (int)$product['stock']; ?>"
                           data-max-stock="<?php echo (int)$product['stock']; ?>">

                    <button type="submit" class="btn btn-large">Add to Cart</button>
                </form>
            <?php else: ?>
                <button class="btn btn-large" disabled>Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
