<?php
require_once 'includes/header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    header("Location: index.php");
    exit;
}
?>

<section class="success-page">
    <div class="container">
        <i class="fa-solid fa-circle-check success-icon"></i>
        <h1 class="section-title" style="margin-bottom: 20px;">Order Successful!</h1>
        
        <div style="max-width: 600px; margin: 0 auto; background: var(--bg-white); padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <p style="font-size: 1.2rem; margin-bottom: 20px;">Thank you for your purchase. Your order has been placed successfully.</p>
            <p style="margin-bottom: 30px; color: var(--text-light);">Order ID: <strong>#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></strong></p>
            
            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
