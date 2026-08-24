<?php
/*
 * order-confirmation.php  -  ORDER CONFIRMATION / THANK YOU
 * ----------------------------------------------------------
 * Shown right after an order is placed. It reads the order id
 * from the URL and displays a summary. The order must belong
 * to the logged-in customer.
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

require_login();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the order, but only if it belongs to this user.
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// If it does not exist / is not theirs, go home.
if (!$order) {
    redirect('index.php');
}

// Get the order items.
$stmt = $conn->prepare(
    "SELECT oi.*, p.name AS product_name
     FROM order_details oi
     LEFT JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id = ?"
);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$items = $stmt->get_result();

$page_title = 'Order Confirmed';
include 'includes/header.php';
?>

<div class="confirmation">
    <div class="confirmation-icon">&#10004;</div>
    <h1>Thank you for your order!</h1>
    <p>Your order has been placed successfully.</p>
    <p class="order-number">Your Order ID is <strong>#<?php echo (int)$order['id']; ?></strong></p>

    <table class="data-table">
        <thead>
            <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($item['product_name'] ?? 'Product'); ?></td>
                    <td><?php echo money($item['price']); ?></td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td><?php echo money($item['price'] * $item['quantity']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total</strong></td>
                <td><strong><?php echo money($order['total_amount']); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <p><strong>Payment Method:</strong> Cash on Delivery</p>
    <p><strong>Delivery Address:</strong> <?php echo e($order['address']); ?></p>

    <div class="confirmation-actions">
        <a href="orders.php" class="btn">View My Orders</a>
        <a href="products.php" class="btn btn-outline">Continue Shopping</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
