<?php
/*
 * admin/categories.php  -  CATEGORY MANAGEMENT
 * ----------------------------------------------------------
 * A single page that can add, edit, delete and list categories.
 *   - The form at the top adds a new category, OR edits one when
 *     ?edit=ID is in the URL (then it pre-fills with that category).
 *   - Delete is a link with ?delete=ID (JavaScript asks to confirm).
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../config/database.php';
require_admin_login();

$errors = [];

/* ---------- ADD or UPDATE a category (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $edit_id = (int)($_POST['edit_id'] ?? 0);

    if ($name === '') {
        $errors[] = 'Category name is required.';
    } else {
        if ($edit_id > 0) {
            // Update an existing category.
            $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->bind_param('si', $name, $edit_id);
            $stmt->execute();
            redirect('categories.php?msg=updated');
        } else {
            // Add a new category.
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param('s', $name);
            $stmt->execute();
            redirect('categories.php?msg=added');
        }
    }
}

/* ---------- DELETE a category (GET ?delete=ID) ---------- */
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    // Because of the foreign key (ON DELETE SET NULL), products in this
    // category are kept but become "Uncategorized".
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    redirect('categories.php?msg=deleted');
}

/* ---------- Are we editing? Load that category to pre-fill the form ---------- */
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $edit_category = $stmt->get_result()->fetch_assoc();
}

/* ---------- Get all categories, with how many products each has ---------- */
$categories = $conn->query(
    "SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
     FROM categories c
     ORDER BY c.name"
);

$page_title = 'Categories';
$active     = 'categories';
include __DIR__ . '/includes/admin-header.php';
?>

<h1 class="admin-title">Categories</h1>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $messages = [
            'added'   => 'Category added successfully.',
            'updated' => 'Category updated successfully.',
            'deleted' => 'Category deleted successfully.',
        ];
        echo e($messages[$_GET['msg']] ?? 'Done.');
        ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo e($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="admin-two-col">
    <!-- Add / Edit form -->
    <div class="admin-panel">
        <h2><?php echo $edit_category ? 'Edit Category' : 'Add Category'; ?></h2>
        <form method="post" action="categories.php" class="admin-form">
            <!-- If editing, send the id so PHP updates instead of inserting -->
            <?php if ($edit_category): ?>
                <input type="hidden" name="edit_id" value="<?php echo (int)$edit_category['id']; ?>">
            <?php endif; ?>

            <label for="name">Category Name</label>
            <input type="text" id="name" name="name"
                   value="<?php echo e($edit_category['name'] ?? ''); ?>" required>

            <button type="submit" class="btn">
                <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
            </button>
            <?php if ($edit_category): ?>
                <a href="categories.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Category list -->
    <div class="admin-panel">
        <h2>All Categories</h2>
        <?php if ($categories->num_rows === 0): ?>
            <p class="empty-message">No categories yet.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Products</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo (int)$cat['id']; ?></td>
                            <td><?php echo e($cat['name']); ?></td>
                            <td><?php echo (int)$cat['product_count']; ?></td>
                            <td class="table-actions">
                                <a href="categories.php?edit=<?php echo (int)$cat['id']; ?>" class="btn btn-small btn-outline">Edit</a>
                                <a href="categories.php?delete=<?php echo (int)$cat['id']; ?>"
                                   class="btn btn-small btn-danger"
                                   onclick="return confirm('Delete this category? Its products will become Uncategorized.');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
