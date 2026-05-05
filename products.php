<?php
require_once 'includes/header.php';

// Pagination and Filtering logic
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT * FROM products";
$params = [];

if ($category_filter) {
    $query .= " WHERE category = :category";
    $params[':category'] = $category_filter;
}

$query .= " ORDER BY id DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = $category_filter ? htmlspecialchars($category_filter) : "All Products";
?>

<section class="products-section" style="padding-top: 40px;">
    <div class="container">
        <!-- Breadcrumb & Title -->
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="margin-bottom: 5px;"><?php echo $page_title; ?></h1>
                <p style="color: var(--text-light);">Home / Shop <?php echo $category_filter ? '/ ' . $page_title : ''; ?></p>
            </div>
            
            <!-- Category Filter Dropdown -->
            <div>
                <select class="form-control" style="width: 200px;" onchange="window.location.href=this.value;">
                    <option value="products.php" <?php echo !$category_filter ? 'selected' : ''; ?>>All Categories</option>
                    <option value="products.php?category=Fruits" <?php echo $category_filter == 'Fruits' ? 'selected' : ''; ?>>Fruits</option>
                    <option value="products.php?category=Vegetables" <?php echo $category_filter == 'Vegetables' ? 'selected' : ''; ?>>Vegetables</option>
                </select>
            </div>
        </div>

        <?php if (count($products) > 0): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-badge"><?php echo htmlspecialchars($product['category']); ?></div>
                        <div class="product-img-wrap">
                            <a href="product-details.php?id=<?php echo $product['id']; ?>">
                                <?php 
                                if (!empty($product['image']) && file_exists('assets/images/products/' . $product['image'])) {
                                    echo '<img src="assets/images/products/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">';
                                } else {
                                    $placeholder = ($product['category'] == 'Fruits') ? 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=300&h=300&fit=crop' : 'https://images.unsplash.com/photo-1566385101042-1a0aa0c1268c?w=300&h=300&fit=crop';
                                    echo '<img src="' . $placeholder . '" alt="' . htmlspecialchars($product['name']) . '">';
                                }
                                ?>
                            </a>
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                            <h3 class="product-title">
                                <a href="product-details.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                            </h3>
                            <div class="product-bottom">
                                <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <form class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="add-to-cart-btn" title="Add to Cart">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" style="text-align: center; margin-top: 40px;">
                No products found in this category.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
