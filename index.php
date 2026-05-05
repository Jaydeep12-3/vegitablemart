<?php
require_once 'includes/header.php';

// Fetch featured products (latest 4)
$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container hero-container">
        <div class="hero-content">
            <h1>Fresh & Organic <span>Groceries</span> Delivered</h1>
            <p>Get the freshest fruits and vegetables delivered straight to your doorstep. Handpicked with love from local farms to ensure maximum quality and nutrition for your family.</p>
            <a href="products.php" class="btn btn-primary">Shop Now</a>
            <a href="#categories" class="btn btn-outline" style="margin-left: 10px;">Explore</a>
        </div>
        <div class="hero-image">
            <img src="assets/images/hero-bowl.png" alt="Fresh Vegetables Bowl" style="border-radius: var(--radius-lg); width: 100%; height: auto; object-fit: cover;">
        </div>
    </div>
</section>

<!-- Categories Section -->
<section id="categories" class="categories">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="category-grid">
            <a href="products.php?category=Fruits" class="category-card" style="background-image: url('https://images.unsplash.com/photo-1619566636858-adf3ef46400b?q=80&w=400&auto=format&fit=crop');">
                <div class="category-content">
                    <h3>Fresh Fruits</h3>
                    <span class="btn btn-secondary">View Products</span>
                </div>
            </a>
            <a href="products.php?category=Vegetables" class="category-card" style="background-image: url('https://images.unsplash.com/photo-1566385101042-1a0aa0c1268c?q=80&w=400&auto=format&fit=crop');">
                <div class="category-content">
                    <h3>Organic Vegetables</h3>
                    <span class="btn btn-secondary">View Products</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="products-section">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        <div class="product-grid">
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-badge"><?php echo htmlspecialchars($product['category']); ?></div>
                    <div class="product-img-wrap">
                        <a href="product-details.php?id=<?php echo $product['id']; ?>">
                            <?php 
                            // If an image was uploaded, use it. Otherwise use a placeholder based on category
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
        <div style="text-align: center; margin-top: 40px;">
            <a href="products.php" class="btn btn-primary">View All Products</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
