<?php
/*
 * account.php  -  CUSTOMER ACCOUNT PAGE
 * ----------------------------------------------------------
 * Shows the logged-in customer's profile information and their
 * most recent orders. Only logged-in customers can see it.
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Protect this page - send guests to the login page.
require_login();

// Get the current user's profile.
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get this user's 5 most recent orders.
$stmt = $conn->prepare(
    "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5"
);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result();

$page_title = 'My Account';
include 'includes/header.php';
?>

<h1 class="page-title">My Account</h1>

<div class="account-grid">
    <!-- Profile card -->
    <div class="card profile-card">
        <h2>Profile</h2>
        <p><strong>Name:</strong> <?php echo e($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo e($user['email']); ?></p>
        <p><strong>Member since:</strong> <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
        <a href="logout.php" class="btn btn-small">Logout</a>
    </div>

    <!-- Recent orders card -->
    <div class="card orders-card">
        <div class="card-head">
            <h2>Recent Orders</h2>
            <a href="orders.php" class="btn btn-small btn-outline">View All Orders</a>
        </div>

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
                        <th></th>
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
                            <td><a href="orders.php?id=<?php echo (int)$order['id']; ?>" class="link">View</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
