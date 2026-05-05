<?php
require_once '../includes/admin-header.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    header("Location: orders.php");
    exit;
}

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute([':id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $status = sanitizeInput($_POST['status']);
    $valid_statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
    
    if (in_array($status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
        if ($stmt->execute([':status' => $status, ':id' => $order_id])) {
            $order['status'] = $status; // update local var
            $success_msg = "Order status updated successfully.";
        }
    }
}

// Fetch order items with product details
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image, p.category 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = :order_id
");
$stmt->execute([':order_id' => $order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 class="page-title" style="margin-bottom: 0;">Order Details #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></h1>
    <a href="orders.php" class="btn-sm btn-primary-sm" style="background: #7f8c8d;"><i class="fa-solid fa-arrow-left"></i> Back to Orders</a>
</div>

<?php if (isset($success_msg)): ?>
    <div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    
    <!-- Left Column: Items -->
    <div>
        <div class="admin-table-container" style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">Items Ordered</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal = 0;
                    foreach ($items as $item): 
                        $item_subtotal = $item['price'] * $item['quantity'];
                        $subtotal += $item_subtotal;
                    ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if (!empty($item['image']) && file_exists('../assets/images/products/' . $item['image'])): ?>
                                        <img src="../assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" width="40" height="40" style="object-fit: contain; background: #f9f9f9; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; background: #eee; display:flex; align-items:center; justify-content:center; border-radius:4px;"><i class="fa-solid fa-image" style="color: #ccc;"></i></div>
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($item['name'] ?? 'Deleted Product'); ?></span>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><strong>$<?php echo number_format($item_subtotal, 2); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; text-align: right; font-size: 1.1rem;">
                <p style="margin-bottom: 10px;">Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
                <p style="margin-bottom: 10px;">Shipping: $5.00</p>
                <h3 style="color: var(--admin-primary);">Grand Total: $<?php echo number_format($order['total_price'], 2); ?></h3>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Customer Info & Status -->
    <div>
        <div class="admin-table-container" style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">Update Status</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Order Status</label>
                    <select name="status" class="form-control" style="margin-bottom: 15px;">
                        <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Processing" <?php echo ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                        <option value="Completed" <?php echo ($order['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn-sm btn-primary-sm" style="width: 100%;">Update Status</button>
            </form>
        </div>
        
        <div class="admin-table-container">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">Customer Details</h3>
            <div style="margin-bottom: 15px;">
                <strong style="display: block; color: #666; font-size: 0.9rem;">Name</strong>
                <p><?php echo htmlspecialchars($order['customer_name']); ?></p>
            </div>
            <div style="margin-bottom: 15px;">
                <strong style="display: block; color: #666; font-size: 0.9rem;">Phone</strong>
                <p><?php echo htmlspecialchars($order['phone']); ?></p>
            </div>
            <div style="margin-bottom: 15px;">
                <strong style="display: block; color: #666; font-size: 0.9rem;">Address</strong>
                <p><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
            </div>
            <div style="margin-bottom: 15px;">
                <strong style="display: block; color: #666; font-size: 0.9rem;">Order Date</strong>
                <p><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
            </div>
        </div>
    </div>
    
</div>

<?php require_once '../includes/admin-footer.php'; ?>
