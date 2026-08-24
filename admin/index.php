<?php
/*
 * admin/index.php  -  ADMIN DASHBOARD
 * ----------------------------------------------------------
 * Shows quick statistics: total products, customers, orders and
 * sales, plus the most recent orders.
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../config/database.php';

// Only logged-in admins can see this page.
require_admin_login();

// --- Gather the statistics ---
$total_products  = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$total_customers = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$total_orders    = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];

// Total sales = sum of all order totals that were NOT cancelled.
$total_sales = $conn->query(
    "SELECT COALESCE(SUM(total_amount), 0) AS s FROM orders WHERE status <> 'Cancelled'"
)->fetch_assoc()['s'];

// The 5 most recent orders (with the customer's name).
$recent_orders = $conn->query(
    "SELECT o.*, u.name AS customer_name
     FROM orders o
     LEFT JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC
     LIMIT 5"
);

$page_title = 'Dashboard';
$active     = 'dashboard';
include __DIR__ . '/includes/admin-header.php';
?>

<h1 class="admin-title">Dashboard</h1>

<!-- Statistic cards -->
<div class="stats-grid">
    <div class="stat-card stat-blue">
        <span class="stat-label">Total Products</span>
        <span class="stat-value"><?php echo (int)$total_products; ?></span>
    </div>
    <div class="stat-card stat-green">
        <span class="stat-label">Total Customers</span>
        <span class="stat-value"><?php echo (int)$total_customers; ?></span>
    </div>
    <div class="stat-card stat-orange">
        <span class="stat-label">Total Orders</span>
        <span class="stat-value"><?php echo (int)$total_orders; ?></span>
    </div>
    <div class="stat-card stat-purple">
        <span class="stat-label">Total Sales</span>
        <span class="stat-value"><?php echo money($total_sales); ?></span>
    </div>
</div>

<!-- Recent orders -->
<div class="admin-panel">
    <div class="admin-panel-head">
        <h2>Recent Orders</h2>
        <a href="orders.php" class="btn btn-small btn-outline">View All</a>
    </div>

    <?php if ($recent_orders->num_rows === 0): ?>
        <p class="empty-message">No orders yet.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php while ($order = $recent_orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo (int)$order['id']; ?></td>
                        <td><?php echo e($order['customer_name'] ?? 'Unknown'); ?></td>
                        <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                        <td><?php echo money($order['total_amount']); ?></td>
                        <td><span class="badge status-<?php echo strtolower($order['status']); ?>">
                            <?php echo e($order['status']); ?></span></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
