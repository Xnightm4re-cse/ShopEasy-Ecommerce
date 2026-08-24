<?php
/*
 * orders.php  -  CUSTOMER ORDER HISTORY
 * ----------------------------------------------------------
 * Two modes:
 *   - No id in URL  -> show a list of all the customer's orders.
 *   - ?id=5 in URL  -> show the details of that single order.
 * A customer can only ever see their OWN orders.
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

require_login();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$page_title = 'My Orders';
include 'includes/header.php';
?>

<?php if ($order_id > 0): ?>
    <?php
    /* ---------- SINGLE ORDER DETAILS ---------- */
    // Get the order but only if it belongs to this logged-in user.
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    ?>

    <?php if (!$order): ?>
        <p class="empty-message">Order not found.</p>
        <div class="center"><a href="orders.php" class="btn">Back to Orders</a></div>
    <?php else: ?>
        <?php
        // Get the items in this order (with product names).
        $stmt = $conn->prepare(
            "SELECT oi.*, p.name AS product_name, p.image AS product_image
             FROM order_details oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?"
        );
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $items = $stmt->get_result();
        ?>

        <a href="orders.php" class="back-link">&larr; Back to Orders</a>
        <h1 class="page-title">Order #<?php echo (int)$order['id']; ?></h1>

        <div class="order-detail-head">
            <p><strong>Date:</strong> <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> <span class="badge status-<?php echo strtolower($order['status']); ?>">
               <?php echo e($order['status']); ?></span></p>
            <p><strong>Phone:</strong> <?php echo e($order['phone']); ?></p>
            <p><strong>Delivery Address:</strong> <?php echo e($order['address']); ?></p>
        </div>

        <table class="data-table">
            <thead>
                <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo e($item['product_name'] ?? 'Product removed'); ?></td>
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
    <?php endif; ?>

<?php else: ?>
    <?php
    /* ---------- LIST OF ALL ORDERS ---------- */
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $orders = $stmt->get_result();
    ?>

    <h1 class="page-title">My Orders</h1>

    <?php if ($orders->num_rows === 0): ?>
        <p class="empty-message">You have not placed any orders yet.
           <a href="products.php">Start shopping</a>.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo (int)$order['id']; ?></td>
                        <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                        <td><?php echo money($order['total_amount']); ?></td>
                        <td><span class="badge status-<?php echo strtolower($order['status']); ?>">
                            <?php echo e($order['status']); ?></span></td>
                        <td><a href="orders.php?id=<?php echo (int)$order['id']; ?>" class="link">View Details</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
