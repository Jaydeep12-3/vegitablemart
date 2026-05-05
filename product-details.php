<?php
require_once 'includes/header.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    echo "<div class='container'><div class='alert alert-danger'>Product not found.</div></div>";
    require_once 'includes/footer.php';
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='container' style='margin-top: 40px;'><div class='alert alert-danger'>Product not found.</div></div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<section class="product-details">
    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="products.php" style="color: var(--text-light);"><i class="fa-solid fa-arrow-left"></i> Back to Shop</a>
        </div>
        
        <div class="details-container">
            <div class="details-image">
                <?php 
                if (!empty($product['image']) && file_exists('assets/images/products/' . $product['image'])) {
                    echo '<img src="assets/images/products/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">';
                } else {
                    $placeholder = ($product['category'] == 'Fruits') ? 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=600&h=600&fit=crop' : 'https://images.unsplash.com/photo-1566385101042-1a0aa0c1268c?w=600&h=600&fit=crop';
                    echo '<img src="' . $placeholder . '" alt="' . htmlspecialchars($product['name']) . '">';
                }
                ?>
            </div>
            
            <div class="details-info">
                <div class="details-category"><?php echo htmlspecialchars($product['category']); ?></div>
                <h1 class="details-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="details-price">$<?php echo number_format($product['price'], 2); ?></div>
                
                <div class="details-desc">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                
                <form class="add-to-cart-form details-action">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="quantity-selector">
                        <button type="button" class="qty-btn minus">-</button>
                        <input type="number" class="qty-input" name="quantity" value="1" min="1" max="99">
                        <button type="button" class="qty-btn plus">+</button>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-add-large">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
