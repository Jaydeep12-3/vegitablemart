<?php
require_once '../includes/admin-header.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header("Location: products.php");
    exit;
}

$error = '';
$success = '';

// Fetch existing product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $category = sanitizeInput($_POST['category']);
    $price = (float)$_POST['price'];
    $description = sanitizeInput($_POST['description']);
    
    $image_name = $product['image']; // Keep old image by default
    
    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = uniqid('prod_') . '.' . $file_ext;
            $upload_dir = '../assets/images/products/';
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                // Delete old image if it exists
                if (!empty($product['image']) && file_exists($upload_dir . $product['image'])) {
                    unlink($upload_dir . $product['image']);
                }
                $image_name = $new_filename;
            } else {
                $error = "Failed to upload new image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
        }
    }
    
    if (empty($error)) {
        if (empty($name) || empty($price)) {
            $error = "Name and Price are required.";
        } else {
            $stmt = $conn->prepare("UPDATE products SET name = :name, description = :desc, price = :price, category = :cat, image = :img WHERE id = :id");
            $result = $stmt->execute([
                ':name' => $name,
                ':desc' => $description,
                ':price' => $price,
                ':cat' => $category,
                ':img' => $image_name,
                ':id' => $product_id
            ]);
            
            if ($result) {
                $success = "Product updated successfully.";
                // Refresh product data
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
                $stmt->execute([':id' => $product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Failed to update product in database.";
            }
        }
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 class="page-title" style="margin-bottom: 0;">Edit Product</h1>
    <a href="products.php" class="btn-sm btn-primary-sm" style="background: #7f8c8d;"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="admin-form-container">
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        
        <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label for="price">Price ($) *</label>
                <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" required value="<?php echo htmlspecialchars($product['price']); ?>">
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label for="category">Category *</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="Fruits" <?php echo ($product['category'] == 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
                    <option value="Vegetables" <?php echo ($product['category'] == 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Current Image</label>
            <div>
                <?php if (!empty($product['image']) && file_exists('../assets/images/products/' . $product['image'])): ?>
                    <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" style="max-width: 150px; border-radius: 4px; border: 1px solid #ddd; margin-bottom: 10px;">
                <?php else: ?>
                    <p style="color: #888; font-style: italic; margin-bottom: 10px;">No image uploaded.</p>
                <?php endif; ?>
            </div>
            
            <label for="image">Replace Image</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <small style="color: #666; display: block; margin-top: 5px;">Leave blank if you don't want to change the image.</small>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        
        <button type="submit" class="btn-sm btn-primary-sm" style="font-size: 1rem; padding: 10px 20px;">Update Product</button>
    </form>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
