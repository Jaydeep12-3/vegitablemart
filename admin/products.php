<?php
require_once '../includes/admin-header.php';

// Handle Deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // First get the image to delete it from folder
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product && !empty($product['image'])) {
        $img_path = '../assets/images/products/' . $product['image'];
        if (file_exists($img_path)) {
            unlink($img_path);
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    if ($stmt->execute([':id' => $id])) {
        $success_msg = "Product deleted successfully.";
    }
}

// Fetch all products
$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 class="page-title" style="margin-bottom: 0;">Manage Products</h1>
    <a href="product-add.php" class="btn-sm btn-primary-sm" style="padding: 10px 15px;"><i class="fa-solid fa-plus"></i> Add New Product</a>
</div>

<?php if (isset($success_msg)): ?>
    <div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php endif; ?>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php if (!empty($product['image']) && file_exists('../assets/images/products/' . $product['image'])): ?>
                                <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" width="50" height="50" style="object-fit: contain; background: #f9f9f9; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #eee; display:flex; align-items:center; justify-content:center; border-radius:4px;">No Img</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn-sm btn-primary-sm" style="background: #f39c12; margin-right: 5px;"><i class="fa-solid fa-edit"></i></a>
                            <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn-sm btn-danger-sm" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No products found. Add one!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
