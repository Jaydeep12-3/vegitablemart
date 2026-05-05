<?php
require_once 'includes/header.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
?>

<section class="cart-page">
    <div class="container">
        <h1 class="section-title">Your Shopping Cart</h1>
        
        <?php if (count($cart_items) > 0): ?>
            <div class="cart-container">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $id => $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $total_price += $subtotal;
                            ?>
                                <tr>
                                    <td>
                                        <div class="cart-item-info">
                                            <?php 
                                            if (!empty($item['image']) && file_exists('assets/images/products/' . $item['image'])) {
                                                echo '<img src="assets/images/products/' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['name']) . '">';
                                            } else {
                                                $placeholder = ($item['category'] == 'Fruits') ? 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=100&h=100&fit=crop' : 'https://images.unsplash.com/photo-1566385101042-1a0aa0c1268c?w=100&h=100&fit=crop';
                                                echo '<img src="' . $placeholder . '" alt="' . htmlspecialchars($item['name']) . '">';
                                            }
                                            ?>
                                            <a href="product-details.php?id=<?php echo $id; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <div class="quantity-selector" style="width: fit-content;">
                                            <button type="button" class="qty-btn minus">-</button>
                                            <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" data-product-id="<?php echo $id; ?>">
                                            <button type="button" class="qty-btn plus">+</button>
                                        </div>
                                    </td>
                                    <td><strong>$<?php echo number_format($subtotal, 2); ?></strong></td>
                                    <td>
                                        <button class="remove-btn" data-product-id="<?php echo $id; ?>" title="Remove Item">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="cart-summary">
                    <h3 style="margin-bottom: 20px;">Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>$5.00</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>$<?php echo number_format($total_price + 5, 2); ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn btn-primary btn-checkout">Proceed to Checkout</a>
                    <a href="products.php" class="btn btn-outline btn-checkout" style="margin-top: 10px;">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fa-solid fa-basket-shopping"></i>
                <h2>Your cart is empty</h2>
                <p style="color: var(--text-light); margin-bottom: 30px;">Looks like you haven't added any fresh items yet.</p>
                <a href="products.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
