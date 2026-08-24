<?php
/*
 * cart.php  -  SHOPPING CART
 * ----------------------------------------------------------
 * The cart is kept in the SESSION as a simple array:
 *      $_SESSION['cart'] = [ product_id => quantity ]
 *
 * This file does two jobs:
 *   1. Handle POST actions (add / update / remove / clear) and
 *      then redirect back (POST-Redirect-GET avoids double submits).
 *   2. Display the current cart contents.
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Make sure the cart array exists.
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ============================================================
 *  STEP 1: HANDLE FORM ACTIONS (only on POST requests)
 * ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // If a "Remove" button was clicked it sends remove_id. Handle it first.
    if (isset($_POST['remove_id'])) {
        $remove_id = (int)$_POST['remove_id'];
        unset($_SESSION['cart'][$remove_id]);
        redirect('cart.php');
    }

    $action = $_POST['action'] ?? '';

    // ---- ADD a product to the cart (from product cards / details page) ----
    if ($action === 'add') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity   = (int)($_POST['quantity'] ?? 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        // Look up the product to check it exists and read its stock.
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if ($product) {
            // How many of this item are already in the cart?
            $already = $_SESSION['cart'][$product_id] ?? 0;
            $newQty  = $already + $quantity;

            // Never allow more than the available stock.
            if ($newQty > $product['stock']) {
                $newQty = (int)$product['stock'];
            }
            if ($newQty > 0) {
                $_SESSION['cart'][$product_id] = $newQty;
            }
        }
        redirect('cart.php');
    }

    // ---- UPDATE quantities (the "Update Cart" button) ----
    if ($action === 'update') {
        // $_POST['qty'] is an array: [ product_id => new quantity ]
        $quantities = $_POST['qty'] ?? [];
        foreach ($quantities as $product_id => $qty) {
            $product_id = (int)$product_id;
            $qty        = (int)$qty;

            if ($qty <= 0) {
                // Zero (or less) means remove the item.
                unset($_SESSION['cart'][$product_id]);
                continue;
            }

            // Clamp the quantity to the available stock.
            $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            if ($product) {
                if ($qty > $product['stock']) {
                    $qty = (int)$product['stock'];
                }
                $_SESSION['cart'][$product_id] = $qty;
            }
        }
        redirect('cart.php');
    }

    // ---- CLEAR the whole cart ----
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        redirect('cart.php');
    }
}

/* ============================================================
 *  STEP 2: BUILD THE LIST OF CART ITEMS FOR DISPLAY
 * ============================================================ */
$cart_items = [];
$subtotal   = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $qty) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if ($product) {
            $lineTotal  = $product['price'] * $qty;
            $subtotal  += $lineTotal;
            $product['cart_qty']   = $qty;
            $product['line_total'] = $lineTotal;
            $cart_items[] = $product;
        } else {
            // Product no longer exists: silently drop it from the cart.
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

$page_title = 'Shopping Cart';
include 'includes/header.php';
?>

<h1 class="page-title">Shopping Cart</h1>

<?php if (empty($cart_items)): ?>
    <p class="empty-message">Your cart is empty. <a href="products.php">Go shopping</a>.</p>
<?php else: ?>

    <!-- One form wraps the table. The "Update Cart" button submits every quantity.
         Each row's "Remove" button submits the same form but with remove_id set. -->
    <form method="post" action="cart.php">
        <input type="hidden" name="action" value="update">

        <table class="data-table cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td class="cart-product">
                            <img src="images/<?php echo e($item['image']); ?>"
                                 alt="<?php echo e($item['name']); ?>"
                                 onerror="this.onerror=null;this.src='images/placeholder.svg';">
                            <span><?php echo e($item['name']); ?></span>
                        </td>
                        <td><?php echo money($item['price']); ?></td>
                        <td>
                            <!-- max = stock so the quantity can never exceed availability -->
                            <input type="number"
                                   name="qty[<?php echo (int)$item['id']; ?>]"
                                   value="<?php echo (int)$item['cart_qty']; ?>"
                                   min="0" max="<?php echo (int)$item['stock']; ?>"
                                   data-max-stock="<?php echo (int)$item['stock']; ?>"
                                   class="qty-input">
                        </td>
                        <td><?php echo money($item['line_total']); ?></td>
                        <td>
                            <button type="submit" name="remove_id" value="<?php echo (int)$item['id']; ?>"
                                    class="btn btn-small btn-danger"
                                    onclick="return confirm('Remove this item from the cart?');">
                                Remove
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-buttons">
            <button type="submit" class="btn btn-outline">Update Cart</button>
        </div>
    </form>

    <!-- Cart summary + checkout -->
    <div class="cart-summary">
        <div class="summary-line">
            <span>Total Items:</span>
            <strong><?php echo cart_count(); ?></strong>
        </div>
        <div class="summary-line total">
            <span>Subtotal:</span>
            <strong><?php echo money($subtotal); ?></strong>
        </div>
        <div class="cart-summary-actions">
            <!-- Clear cart is its own small form -->
            <form method="post" action="cart.php" class="inline-form">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="btn btn-outline btn-danger"
                        onclick="return confirm('Clear the whole cart?');">Clear Cart</button>
            </form>
            <a href="checkout.php" class="btn btn-large">Proceed to Checkout</a>
        </div>
    </div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
