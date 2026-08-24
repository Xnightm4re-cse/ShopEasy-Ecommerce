<?php
/*
 * admin/orders.php  -  ORDER MANAGEMENT
 * ----------------------------------------------------------
 * Two modes:
 *   - ?id=5  -> show that order's details + a form to change status.
 *   - (none) -> list every order.
 * Admins can change an order's status (Pending / Processing /
 * Completed / Cancelled).
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../config/database.php';
require_admin_login();

// The four allowed statuses (orders.status is VARCHAR, so PHP validates them).
$statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];

/* ---------- CHANGE ORDER STATUS (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $order_id   = (int)($_POST['order_id'] ?? 0);
    $new_status = $_POST['status'] ?? '';

    // Only accept a status from our allowed list.
    if (in_array($new_status, $statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $new_status, $order_id);
        $stmt->execute();
    }
    // Go back to where we came from (details page or list).
    if ($order_id > 0 && isset($_POST['from_details'])) {
        redirect('orders.php?id=' . $order_id . '&msg=status');
    }
    redirect('orders.php?msg=status');
}

$view_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$page_title = 'Orders';
$active     = 'orders';
include __DIR__ . '/includes/admin-header.php';
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'status'): ?>
    <div class="alert alert-success">Order status updated.</div>
<?php endif; ?>

<?php if ($view_id > 0): ?>
    <?php
    /* ---------- SINGLE ORDER DETAILS ---------- */
    $stmt = $conn->prepare(
        "SELECT o.*, u.name AS customer_name, u.email AS customer_email
         FROM orders o
         LEFT JOIN users u ON o.user_id = u.id
         WHERE o.id = ?"
    );
    $stmt->bind_param('i', $view_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    ?>

    <?php if (!$order): ?>
        <p class="empty-message">Order not found.</p>
        <a href="orders.php" class="btn">Back to Orders</a>
    <?php else: ?>
        <?php
        // Items in this order.
        $stmt = $conn->prepare(
            "SELECT oi.*, p.name AS product_name
             FROM order_details oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?"
        );
        $stmt->bind_param('i', $view_id);
        $stmt->execute();
        $items = $stmt->get_result();
        ?>

        <div class="admin-panel-head">
            <h1 class="admin-title">Order #<?php echo (int)$order['id']; ?></h1>
            <a href="orders.php" class="btn btn-outline">Back to Orders</a>
        </div>

        <div class="admin-two-col">
            <div class="admin-panel">
                <h2>Customer &amp; Delivery</h2>
                <p><strong>Customer:</strong> <?php echo e($order['customer_name'] ?? 'Unknown'); ?></p>
                <p><strong>Email:</strong> <?php echo e($order['customer_email'] ?? '-'); ?></p>
                <p><strong>Phone:</strong> <?php echo e($order['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo e($order['address']); ?></p>
                <p><strong>Date:</strong> <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>

                <!-- Change status form -->
                <form method="post" action="orders.php" class="status-form">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
                    <input type="hidden" name="from_details" value="1">
                    <label for="status">Order Status</label>
                    <select id="status" name="status">
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>>
                                <?php echo $s; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Update Status</button>
                </form>
            </div>

            <div class="admin-panel">
                <h2>Items</h2>
                <table class="data-table">
                    <thead>
                        <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
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
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <?php
    /* ---------- LIST OF ALL ORDERS ---------- */
    $orders = $conn->query(
        "SELECT o.*, u.name AS customer_name
         FROM orders o
         LEFT JOIN users u ON o.user_id = u.id
         ORDER BY o.created_at DESC"
    );
    ?>

    <h1 class="admin-title">Orders</h1>

    <div class="admin-panel">
        <?php if ($orders->num_rows === 0): ?>
            <p class="empty-message">No orders yet.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
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
                            <td><?php echo e($order['customer_name'] ?? 'Unknown'); ?></td>
                            <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                            <td><?php echo money($order['total_amount']); ?></td>
                            <td>
                                <!-- Quick status change right in the list -->
                                <form method="post" action="orders.php" class="status-form-inline">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <?php foreach ($statuses as $s): ?>
                                            <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>>
                                                <?php echo $s; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td><a href="orders.php?id=<?php echo (int)$order['id']; ?>" class="btn btn-small btn-outline">View</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
