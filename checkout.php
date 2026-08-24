<?php
/*
 * checkout.php  -  CHECKOUT / PLACE ORDER
 * ----------------------------------------------------------
 * Shows the order summary and a delivery form. When submitted:
 *   1. Saves the order in the "orders" table.
 *   2. Saves each item in the "order_details" table.
 *   3. Reduces the stock of each product.
 *   4. Empties the cart.
 *   5. Goes to the order confirmation page.
 *
 * All the database changes happen inside a TRANSACTION so that
 * either everything succeeds together, or nothing changes.
 *
 * Payment method: Cash on Delivery (no real payment gateway).
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// You must be logged in to check out.
require_login();

// If the cart is empty there is nothing to check out.
if (empty($_SESSION['cart'])) {
    redirect('cart.php');
}

// Get the logged-in user's details (to pre-fill name and email).
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Build the cart items and subtotal (same idea as cart.php).
$cart_items = [];
$subtotal   = 0;
foreach ($_SESSION['cart'] as $product_id => $qty) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    if ($product) {
        $product['cart_qty']   = $qty;
        $product['line_total'] = $product['price'] * $qty;
        $subtotal += $product['line_total'];
        $cart_items[] = $product;
    }
}

$errors = [];

/* ---------- PLACE THE ORDER (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate the delivery details.
    if ($phone === '') {
        $errors[] = 'Please enter your phone number.';
    }
    if ($address === '') {
        $errors[] = 'Please enter your delivery address.';
    }
    if (empty($cart_items)) {
        $errors[] = 'Your cart is empty.';
    }

    if (empty($errors)) {
        // Start a transaction so all changes happen together.
        $conn->begin_transaction();
        try {
            // 1) Insert the order record.
            $stmt = $conn->prepare(
                "INSERT INTO orders (user_id, total_amount, phone, address, status)
                 VALUES (?, ?, ?, ?, 'Pending')"
            );
            $stmt->bind_param('idss', $_SESSION['user_id'], $subtotal, $phone, $address);
            $stmt->execute();
            $order_id = $conn->insert_id;   // the new order's id

            // 2) Insert each item and 3) reduce the stock.
            $itemStmt  = $conn->prepare(
                "INSERT INTO order_details (order_id, product_id, quantity, price)
                 VALUES (?, ?, ?, ?)"
            );
            $stockStmt = $conn->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ?"
            );

            foreach ($cart_items as $item) {
                $itemStmt->bind_param('iiid', $order_id, $item['id'], $item['cart_qty'], $item['price']);
                $itemStmt->execute();

                $stockStmt->bind_param('ii', $item['cart_qty'], $item['id']);
                $stockStmt->execute();
            }

            // Everything worked - save the changes for good.
            $conn->commit();

            // 4) Empty the cart.
            $_SESSION['cart'] = [];

            // 5) Show the confirmation page.
            redirect('order-confirmation.php?id=' . $order_id);

        } catch (mysqli_sql_exception $e) {
            // Something failed - undo all changes.
            $conn->rollback();
            $errors[] = 'Sorry, something went wrong while placing your order. Please try again.';
        }
    }
}

$page_title = 'Checkout';
include 'includes/header.php';
?>

<h1 class="page-title">Checkout</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo e($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="checkout-grid">
    <!-- Delivery details form -->
    <div class="card">
        <h2>Delivery Details</h2>
        <form method="post" action="checkout.php" id="checkoutForm" novalidate>
            <label for="name">Full Name</label>
            <input type="text" id="name" value="<?php echo e($user['name']); ?>" readonly>

            <label for="email">Email</label>
            <input type="email" id="email" value="<?php echo e($user['email']); ?>" readonly>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone"
                   value="<?php echo e($_POST['phone'] ?? ''); ?>" required>

            <label for="address">Delivery Address</label>
            <textarea id="address" name="address" rows="3" required><?php echo e($_POST['address'] ?? ''); ?></textarea>

            <label>Payment Method</label>
            <input type="text" value="Cash on Delivery" readonly>

            <button type="submit" class="btn btn-large btn-block">Place Order</button>
        </form>
    </div>

    <!-- Order summary -->
    <div class="card">
        <h2>Order Summary</h2>
        <table class="data-table">
            <thead>
                <tr><th>Product</th><th>Qty</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo e($item['name']); ?></td>
                        <td><?php echo (int)$item['cart_qty']; ?></td>
                        <td><?php echo money($item['line_total']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right"><strong>Total</strong></td>
                    <td><strong><?php echo money($subtotal); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
