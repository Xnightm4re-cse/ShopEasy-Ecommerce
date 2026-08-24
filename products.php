<?php
/*
 * products.php  -  PRODUCT LISTING PAGE
 * ----------------------------------------------------------
 * Shows all products. Supports:
 *   - Search by name       (?search=...)
 *   - Filter by category   (?category=ID)
 * Both use PREPARED STATEMENTS so user input is safe.
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// --- Read the search box text and selected category from the URL ---
$search      = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category']   : 0;

// --- Build the SQL query step by step depending on the filters ---
// We collect the WHERE conditions and the values separately, then
// bind the values with a prepared statement.
$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1";                 // 1=1 lets us add "AND ..." easily
$params = [];                       // values for the prepared statement
$types  = '';                       // data types for bind_param (s = string, i = int)

if ($search !== '') {
    $sql .= " AND p.name LIKE ?";
    $params[] = '%' . $search . '%';
    $types   .= 's';
}
if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types   .= 'i';
}
$sql .= " ORDER BY p.id DESC";

// --- Run the query safely with a prepared statement ---
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// --- Get all categories for the filter buttons ---
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$page_title = 'Products';
include 'includes/header.php';
?>

<h1 class="page-title">Our Products</h1>

<!-- ===================== SEARCH + FILTER BAR ===================== -->
<div id="categories" class="shop-toolbar">
    <!-- Search form (GET so the search term shows in the URL) -->
    <form method="get" action="products.php" class="search-form">
        <input type="text" name="search" placeholder="Search products..."
               value="<?php echo e($search); ?>">
        <button type="submit" class="btn">Search</button>
    </form>

    <!-- Category filter links -->
    <div class="category-filter">
        <a href="products.php" class="filter-chip <?php echo $category_id === 0 ? 'active' : ''; ?>">All</a>
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <a href="products.php?category=<?php echo (int)$cat['id']; ?>"
               class="filter-chip <?php echo $category_id === (int)$cat['id'] ? 'active' : ''; ?>">
                <?php echo e($cat['name']); ?>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<!-- ===================== PRODUCT GRID ===================== -->
<?php if ($products->num_rows === 0): ?>
    <p class="empty-message">No products found. Try a different search or category.</p>
<?php else: ?>
    <div class="product-grid">
        <?php while ($product = $products->fetch_assoc()): ?>
            <?php render_product_card($product); ?>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
