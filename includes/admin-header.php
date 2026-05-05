<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Protect admin pages
requireAdmin();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FreshVeg</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-leaf"></i> FreshVeg Admin
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="products.php" class="<?php echo ($current_page == 'products.php' || $current_page == 'product-add.php' || $current_page == 'product-edit.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-box"></i> Products
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="<?php echo ($current_page == 'orders.php' || $current_page == 'order-details.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-shopping-cart"></i> Orders
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-topbar">
                <div class="user-info">
                    <span>Welcome, Admin</span> | 
                    <a href="logout.php" style="color: #e74c3c; margin-left: 10px;"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
                </div>
            </header>
            
            <div class="admin-content">
