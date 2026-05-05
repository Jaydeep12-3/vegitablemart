<?php
require_once '../includes/admin-header.php';

// Get Dashboard Statistics
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $conn->query("SELECT SUM(total_price) FROM orders WHERE status != 'Cancelled'")->fetchColumn();

// Get Recent Orders
$stmt = $conn->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="page-title">Dashboard Overview</h1>

<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
        <div class="stat-info">
            <h3>Total Products</h3>
            <div class="num"><?php echo $total_products; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-shopping-cart"></i></div>
        <div class="stat-info">
            <h3>Total Orders</h3>
            <div class="num"><?php echo $total_orders; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-dollar-sign"></i></div>
        <div class="stat-info">
            <h3>Total Revenue</h3>
            <div class="num">$<?php echo number_format($total_revenue ?: 0, 2); ?></div>
        </div>
    </div>
</div>

<h2 style="margin-bottom: 20px; color: var(--admin-primary);">Recent Orders</h2>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($recent_orders) > 0): ?>
                <?php foreach ($recent_orders as $order): ?>
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
                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-sm btn-primary-sm">View</a>
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
    <div style="margin-top: 20px; text-align: right;">
        <a href="orders.php" style="color: var(--admin-secondary); font-weight: 500;">View All Orders &rarr;</a>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
