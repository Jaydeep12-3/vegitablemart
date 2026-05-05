<?php
require_once '../includes/admin-header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $category = sanitizeInput($_POST['category']);
    $price = (float)$_POST['price'];
    $description = sanitizeInput($_POST['description']);
    
    $image_name = '';
    
    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = uniqid('prod_') . '.' . $file_ext;
            $upload_dir = '../assets/images/products/';
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                $image_name = $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
        }
    }
    
    if (empty($error)) {
        if (empty($name) || empty($price)) {
            $error = "Name and Price are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image) VALUES (:name, :desc, :price, :cat, :img)");
            $result = $stmt->execute([
                ':name' => $name,
                ':desc' => $description,
                ':price' => $price,
                ':cat' => $category,
                ':img' => $image_name
            ]);
            
            if ($result) {
                $success = "Product added successfully.";
                // Clear post data
                $_POST = [];
            } else {
                $error = "Failed to add product to database.";
            }
        }
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 class="page-title" style="margin-bottom: 0;">Add New Product</h1>
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
            <input type="text" id="name" name="name" class="form-control" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>
        
        <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label for="price">Price ($) *</label>
                <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" required value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label for="category">Category *</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="Fruits" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
                    <option value="Vegetables" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="image">Product Image</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <small style="color: #666; display: block; margin-top: 5px;">Recommended size: 600x600px. Formats: JPG, PNG, WEBP.</small>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
        </div>
        
        <button type="submit" class="btn-sm btn-primary-sm" style="font-size: 1rem; padding: 10px 20px;">Save Product</button>
    </form>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
