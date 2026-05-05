<?php
require_once 'includes/header.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += ($item['price'] * $item['quantity']);
}
$shipping = 5.00;
$grand_total = $total_price + $shipping;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['customer_name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    
    if (empty($name) || empty($phone) || empty($address)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            $conn->beginTransaction();
            
            // Insert Order
            $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, address, total_price) VALUES (:name, :phone, :address, :total)");
            $stmt->execute([
                ':name' => $name,
                ':phone' => $phone,
                ':address' => $address,
                ':total' => $grand_total
            ]);
            
            $order_id = $conn->lastInsertId();
            
            // Insert Order Items
            $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :qty, :price)");
            
            foreach ($cart_items as $id => $item) {
                $stmt_items->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $id,
                    ':qty' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }
            
            $conn->commit();
            
            // Clear cart and redirect
            unset($_SESSION['cart']);
            header("Location: success.php?order_id=" . $order_id);
            exit;
            
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "Failed to place order. Please try again.";
        }
    }
}
?>

<section class="checkout-page">
    <div class="container">
        <h1 class="section-title">Checkout</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="cart-container">
            <!-- Checkout Form -->
            <div class="checkout-form">
                <h3 style="margin-bottom: 20px;">Shipping Details</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="customer_name">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" required value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Delivery Address</label>
                        <textarea id="address" name="address" class="form-control" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; margin-top: 10px;">Place Order</button>
                </form>
            </div>
            
            <!-- Order Summary Sidebar -->
            <div class="cart-summary">
                <h3 style="margin-bottom: 20px;">Your Order</h3>
                
                <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                    <?php foreach ($cart_items as $item): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.95rem;">
                            <span><?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['name']); ?></span>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($total_price, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>$<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-row summary-total">
                    <span>Grand Total</span>
                    <span>$<?php echo number_format($grand_total, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
