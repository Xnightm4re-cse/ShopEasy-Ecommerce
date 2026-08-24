<?php
/*
 * admin/edit-product.php  -  EDIT AN EXISTING PRODUCT
 * ----------------------------------------------------------
 * Loads a product by id, shows a pre-filled form and updates it.
 * The image is only changed if a new one is uploaded.
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../config/database.php';
require_admin_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load the product to edit.
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// If it does not exist, go back to the list.
if (!$product) {
    redirect('products.php');
}

// Categories for the dropdown.
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    $stock       = trim($_POST['stock'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);

    // --- Validation (same rules as add-product) ---
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

    // Keep the old image unless a new one is uploaded.
    $image_name = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Image must be a JPG, PNG, GIF, WEBP or SVG file.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image must be smaller than 2 MB.';
        } else {
            $image_name = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target = __DIR__ . '/../images/' . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $errors[] = 'Could not save the uploaded image.';
                $image_name = $product['image'];
            }
        }
    }

    // --- If valid, update the product ---
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE products
             SET category_id = ?, name = ?, description = ?, price = ?, stock = ?, image = ?
             WHERE id = ?"
        );
        $stmt->bind_param('issdisi', $category_id, $name, $description, $price, $stock, $image_name, $id);
        $stmt->execute();

        redirect('products.php?msg=updated');
    }

    // If there were errors, keep what the admin typed on screen.
    $product['name']        = $name;
    $product['description'] = $description;
    $product['price']       = $price;
    $product['stock']       = $stock;
    $product['category_id'] = $category_id;
}

$page_title = 'Edit Product';
$active     = 'products';
include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-panel-head">
    <h1 class="admin-title">Edit Product</h1>
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
    <form method="post" action="edit-product.php?id=<?php echo (int)$id; ?>"
          enctype="multipart/form-data" class="admin-form">

        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?php echo e($product['name']); ?>" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Select a category --</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo (int)$cat['id']; ?>"
                    <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo e($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?php echo e($product['description']); ?></textarea>

        <div class="form-row">
            <div>
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" min="0"
                       value="<?php echo e($product['price']); ?>" required>
            </div>
            <div>
                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock" min="0"
                       value="<?php echo e($product['stock']); ?>" required>
            </div>
        </div>

        <label>Current Image</label>
        <img class="table-thumb" src="../images/<?php echo e($product['image']); ?>"
             alt="<?php echo e($product['name']); ?>"
             onerror="this.onerror=null;this.src='../images/placeholder.svg';">

        <label for="image">Change Image (optional)</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit" class="btn btn-large">Update Product</button>
    </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
