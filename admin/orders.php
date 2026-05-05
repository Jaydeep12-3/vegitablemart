<?php
require_once '../includes/admin-header.php';

// Handle Status Update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitizeInput($_POST['status']);
    
    $valid_statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
    
    if (in_array($status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
        if ($stmt->execute([':status' => $status, ':id' => $order_id])) {
            $success_msg = "Order #{$order_id} status updated to {$status}.";
        }
    }
}

// Fetch all orders
$stmt = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 class="page-title" style="margin-bottom: 0;">Manage Orders</h1>
</div>

<?php if (isset($success_msg)): ?>
    <div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php endif; ?>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td>
                            <?php 
                            $status_class = 'status-pending';
                            if ($order['status'] == 'Completed') $status_class = 'status-completed';
                            if ($order['status'] == 'Processing') $status_class = 'status-processing';
                            if ($order['status'] == 'Cancelled') $status_class = 'status-cancelled';
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $order['status']; ?></span>
                        </td>
                        <td>
                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-sm btn-primary-sm"><i class="fa-solid fa-eye"></i> View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
