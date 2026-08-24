<?php
/*
 * admin/products.php  -  PRODUCT LIST (admin)
 * ----------------------------------------------------------
 * Shows all products in a table with Edit and Delete actions,
 * and a button to add a new product.
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../config/database.php';
require_admin_login();

// Get all products with their category name.
$products = $conn->query(
    "SELECT p.*, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     ORDER BY p.id DESC"
);

$page_title = 'Products';
$active     = 'products';
include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-panel-head">
    <h1 class="admin-title">Products</h1>
    <a href="add-product.php" class="btn">+ Add Product</a>
</div>

<!-- Show a success message after add/edit/delete -->
<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $messages = [
            'added'   => 'Product added successfully.',
            'updated' => 'Product updated successfully.',
            'deleted' => 'Product deleted successfully.',
        ];
        echo e($messages[$_GET['msg']] ?? 'Done.');
        ?>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <?php if ($products->num_rows === 0): ?>
        <p class="empty-message">No products yet. Click "Add Product" to create one.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo (int)$p['id']; ?></td>
                        <td>
                            <img class="table-thumb" src="../images/<?php echo e($p['image']); ?>"
                                 alt="<?php echo e($p['name']); ?>"
                                 onerror="this.onerror=null;this.src='../images/placeholder.svg';">
                        </td>
                        <td><?php echo e($p['name']); ?></td>
                        <td><?php echo e($p['category_name'] ?? 'Uncategorized'); ?></td>
                        <td><?php echo money($p['price']); ?></td>
                        <td><?php echo (int)$p['stock']; ?></td>
                        <td class="table-actions">
                            <a href="edit-product.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-small btn-outline">Edit</a>
                            <!-- Delete asks for confirmation via JavaScript before running -->
                            <a href="delete-product.php?id=<?php echo (int)$p['id']; ?>"
                               class="btn btn-small btn-danger"
                               onclick="return confirm('Delete this product? This cannot be undone.');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
