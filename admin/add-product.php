<?php
/*
 * admin/add-product.php  -  ADD A NEW PRODUCT
 * ----------------------------------------------------------
 * Shows a form and, when submitted, inserts a new product.
 * Handles an optional image upload (saved into the images folder).
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../config/database.php';
require_admin_login();

// Get categories for the dropdown.
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$errors = [];
// Default values (used to refill the form if there is an error).
$name = $description = '';
$price = $stock = '';
$category_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    $stock       = trim($_POST['stock'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);

    // --- Validation ---
    if ($name === '') {
        $errors[] = 'Product name is required.';
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = 'Price must be a number (0 or more).';
    }
    if (!ctype_digit($stock)) {
        $errors[] = 'Stock must be a whole number (0 or more).';
    }
    if ($category_id <= 0) {
        $errors[] = 'Please choose a category.';
    }

    // --- Handle the image upload (optional) ---
    $image_name = 'placeholder.svg'; // default if no image uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Image must be a JPG, PNG, GIF, WEBP or SVG file.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image must be smaller than 2 MB.';
        } else {
            // Build a unique file name so uploads never overwrite each other.
            $image_name = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target = __DIR__ . '/../images/' . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $errors[] = 'Could not save the uploaded image.';
                $image_name = 'placeholder.svg';
            }
        }
    }

    // --- If valid, insert the product ---
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO products (category_id, name, description, price, stock, image)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('issdis', $category_id, $name, $description, $price, $stock, $image_name);
        $stmt->execute();

        redirect('products.php?msg=added');
    }
}

$page_title = 'Add Product';
$active     = 'products';
include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-panel-head">
    <h1 class="admin-title">Add Product</h1>
    <a href="products.php" class="btn btn-outline">Back to Products</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo e($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <!-- enctype is required so the image file can be uploaded -->
    <form method="post" action="add-product.php" enctype="multipart/form-data" class="admin-form">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?php echo e($name); ?>" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Select a category --</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo (int)$cat['id']; ?>"
                    <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo e($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?php echo e($description); ?></textarea>

        <div class="form-row">
            <div>
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" min="0"
                       value="<?php echo e($price); ?>" required>
            </div>
            <div>
                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock" min="0"
                       value="<?php echo e($stock); ?>" required>
            </div>
        </div>

        <label for="image">Product Image (optional)</label>
        <input type="file" id="image" name="image" accept="image/*">
        <small class="form-hint">If you do not upload an image, a placeholder is used.</small>

        <button type="submit" class="btn btn-large">Add Product</button>
    </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
