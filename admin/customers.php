<?php
/*
 * admin/customers.php  -  CUSTOMER LIST
 * ----------------------------------------------------------
 * Shows every registered customer. Admins can view them but do
 * not edit passwords (as required by the project).
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../config/database.php';
require_admin_login();

// Get all customers, plus how many orders each one has placed.
$customers = $conn->query(
    "SELECT u.*, (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS order_count
     FROM users u
     ORDER BY u.created_at DESC"
);

$page_title = 'Customers';
$active     = 'customers';
include __DIR__ . '/includes/admin-header.php';
?>

<h1 class="admin-title">Customers</h1>

<div class="admin-panel">
    <?php if ($customers->num_rows === 0): ?>
        <p class="empty-message">No customers registered yet.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Orders</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = $customers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo (int)$c['id']; ?></td>
                        <td><?php echo e($c['name']); ?></td>
                        <td><?php echo e($c['email']); ?></td>
                        <td><?php echo (int)$c['order_count']; ?></td>
                        <td><?php echo date('d M Y', strtotime($c['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
