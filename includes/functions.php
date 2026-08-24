<?php
/*
 * includes/functions.php
 * ----------------------------------------------------------
 * Small helper functions used all over the website, plus the
 * session start. Include this file near the top of every page.
 * ----------------------------------------------------------
 */

// Start the PHP session if it has not been started yet.
// The session is how we "remember" the logged-in user and the cart.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * e() - "escape" text before printing it in HTML.
 * This converts characters like < > " & into safe HTML codes,
 * which prevents broken pages and XSS attacks.
 * Use it every time you print data that came from the user or database.
 */
function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/*
 * money() - format a number as a price, e.g. 9.5 becomes "$9.50".
 */
function money($amount)
{
    return '$' . number_format((float)$amount, 2);
}

/*
 * redirect() - send the browser to another page and stop the script.
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/*
 * short_text() - shorten a long description for product cards.
 */
function short_text($text, $limit = 70)
{
    $text = trim($text ?? '');
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . '...';
}

/* ---------------------------------------------------------
 *  SHOPPING CART HELPERS
 *  The cart is stored in the session as an array:
 *      $_SESSION['cart'] = [ product_id => quantity, ... ]
 * --------------------------------------------------------- */

/*
 * cart_count() - total number of items in the cart
 * (adds up all the quantities).
 */
function cart_count()
{
    $count = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $quantity) {
            $count += $quantity;
        }
    }
    return $count;
}

/*
 * render_product_card() - prints the HTML for one product "card".
 * Used on the home page and the products page so the design stays
 * the same in both places. $p is one product row from the database
 * (it must also include category_name from a JOIN).
 */
function render_product_card($p)
{
    $inStock = (int)$p['stock'] > 0;
    ?>
    <div class="product-card">
        <a href="product-details.php?id=<?php echo (int)$p['id']; ?>" class="product-image">
            <img src="images/<?php echo e($p['image']); ?>"
                 alt="<?php echo e($p['name']); ?>"
                 onerror="this.onerror=null;this.src='images/placeholder.svg';">
        </a>
        <div class="product-info">
            <span class="product-category"><?php echo e($p['category_name'] ?? 'Uncategorized'); ?></span>
            <h3 class="product-name">
                <a href="product-details.php?id=<?php echo (int)$p['id']; ?>"><?php echo e($p['name']); ?></a>
            </h3>
            <p class="product-desc"><?php echo e(short_text($p['description'], 65)); ?></p>
            <div class="product-meta">
                <span class="product-price"><?php echo money($p['price']); ?></span>
                <?php if ($inStock): ?>
                    <span class="stock in-stock">In Stock: <?php echo (int)$p['stock']; ?></span>
                <?php else: ?>
                    <span class="stock out-stock">Out of Stock</span>
                <?php endif; ?>
            </div>
            <div class="product-actions">
                <a href="product-details.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-outline btn-small">View Details</a>
                <?php if ($inStock): ?>
                    <!-- Add to cart is a small form that POSTs to cart.php -->
                    <form method="post" action="cart.php" class="inline-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-small">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-small" disabled>Add to Cart</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
