<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if ($action === 'add' && $product_id > 0) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        // Fetch product details to ensure it exists and get price/name/image
        $stmt = $conn->prepare("SELECT id, name, price, image, category FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'category' => $product['category'],
                    'quantity' => $quantity
                ];
            }
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Product added to cart',
                'total_items' => getCartItemCount()
            ]);
            exit;
        }
    }
    
    else if ($action === 'update' && $product_id > 0) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if (isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Cart updated',
                'total_items' => getCartItemCount()
            ]);
            exit;
        }
    }
    
    else if ($action === 'remove' && $product_id > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Product removed from cart',
                'total_items' => getCartItemCount()
            ]);
            exit;
        }
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    exit;
}
?>
