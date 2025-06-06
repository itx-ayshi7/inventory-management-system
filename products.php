<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Add status column to product table if it doesn't exist
$conn->query("ALTER TABLE product ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'In Stock'");

// Update any existing 'Pending' values to 'In Stock'
$conn->query("UPDATE product SET status = 'In Stock' WHERE status = 'Pending' OR status IS NULL");

$user_id = $_SESSION['user_id'];
$message = "";

// Get message from URL if exists
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

// ADD PRODUCT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO product (product_name, category, quantity, price, status, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidss", $product_name, $category, $quantity, $price, $status, $user_id);

    if ($stmt->execute()) {
        $message = "Product added successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // First check if the product exists and belongs to the user
    $check_stmt = $conn->prepare("SELECT id FROM product WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Delete the product
        $delete_stmt = $conn->prepare("DELETE FROM product WHERE id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $id, $user_id);
        
        if ($delete_stmt->execute()) {
            $message = "Product deleted successfully.";
        } else {
            $message = "Error deleting product: " . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        $message = "Product not found or you don't have permission to delete it.";
    }
    $check_stmt->close();

    // Redirect to refresh the page
    header("Location: products.php?message=" . urlencode($message));
    exit();
}

// UPDATE PRODUCT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE product SET product_name = ?, category = ?, quantity = ?, price = ?, status = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssidssi", $product_name, $category, $quantity, $price, $status, $id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: products.php");
    exit();
}

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM product WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_product = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Invexa Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2c5282;
            --accent-color: #4299e1;
            --light-bg: #f7fafc;
            --text-color: #2d3748;
            --border-color: #e2e8f0;
            --sidebar-width: 250px;
            --page-title: #2b6cb0;
        }
        body.dark {
            --page-title: white;
            --light-bg: #1a202c;
            --text-color: #f7fafc;
            --border-color: #2d3748;
            --section-bg: #2d3748;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar-header h2 {
            color: white;
            font-size: 1.5rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: margin-left 0.3s ease;
            margin-top: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .page-title {
            color: var(--page-title);
            margin-bottom: 2rem;
            font-size: 2rem;
            margin-top: 1rem;
        }

        .add-product-form {
            background: var(--section-bg);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: var(--secondary-color);
        }

        .products-table {
            width: 100%;
            background: var(--section-bg);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-top: 0.8rem;
        }

        table {
            width: 100%;
            min-width: 500px;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        th, td {
            padding: 0.5rem 0.4rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            position: sticky;
            top: 0;
            font-size: 0.8rem;
        }

        td {
            font-size: 0.8rem;
        }

        tr:hover {
            background-color: var(--light-bg);
        }

        .action-btn {
            padding: 0.3rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 0.3rem;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        .edit-btn {
            background-color: var(--accent-color);
            color: white;
        }

        .delete-btn {
            background-color: #e53e3e;
            color: white;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background-color: #c6f6d5;
            color: #2f855a;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 1024px) {
            .product {
                width: 120px;
            }

            .product img {
                width: 120px;
                height: 120px;
            }

            .product h3 {
                font-size: 0.85rem;
            }

            .image {
                gap: 20px;
            }

            .add-product-form {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
                margin-top: 3.5rem;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .add-product-form {
                padding: 1.2rem;
                margin-bottom: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
                margin: 0.5rem 0 1.5rem;
            }

            .images {
                padding: 1rem;
                margin-bottom: 1.5rem;
            }

            .images h2 {
                font-size: 1rem;
                margin-bottom: 0.8rem;
            }

            .product {
                width: 100px;
            }

            .product img {
                width: 50px;
                height: 50px;
            }

            .product h3 {
                font-size: 0.8rem;
                margin-top: 0.5rem;
            }

            .image {
                gap: 10px;
                padding: 0 10px;
            }
        }

        @media (max-width: 480px) {
            .menu-toggle {
                top: 0.8rem;
                left: 0.8rem;
                width: 36px;
                height: 36px;
            }

            .main-content {
                padding: 0.8rem;
                margin-top: 3rem;
            }

            .product {
                width: 80px;
            }

            .product img {
                width: 80px;
                height: 80px;
            }

            .product h3 {
                font-size: 0.7rem;
                margin-top: 0.4rem;
            }

            .image {
                gap: 10px;
                padding: 0 5px;
            }

            .images {
                padding: 0.8rem;
            }

            .images h2 {
                font-size: 0.9rem;
                margin-bottom: 0.6rem;
            }
        }

        /* Improve table responsiveness */
        .products-table {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-top: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Add smooth transitions */
        .product, .action-btn, .btn, .sidebar {
            transition: all 0.3s ease;
        }

        /* Improve touch targets on mobile */
        @media (max-width: 768px) {
            .btn, .action-btn {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .nav-link {
                padding: 0.8rem 1rem;
            }
        }
        /* Product Display Responsiveness */
        .product {
            background-color: transparent;
            padding: 0;
            text-align: center;
            width: 150px;
            flex-shrink: 0;
            scroll-snap-align: start;
            transition: transform 0.3s ease;
        }

        .product img {
            border-radius: 8px;
            width: 150px;
            height: 150px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .product h3 {
            margin-top: 10px;
            font-size: 0.9rem;
            color: var(--text-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 5px;
        }

        .product:hover {
            transform: scale(1.05);
        }

        .product:hover img {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .prod {
            margin-bottom: 40px;
            overflow-x: auto;
            padding: 10px 0;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            position: relative;
        }

        .image {
            display: flex;
            justify-content: flex-start;
            flex-wrap: nowrap;
            gap: 25px;
            padding: 0 20px;
            min-width: max-content;
        }

        .images {
            padding: 2rem;
            background: var(--section-bg);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .images h2 {
            color: var(--page-title);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            padding-left: 20px;
            position: relative;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .image {
                gap: 20px;
            }
            
            .images {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .image {
                gap: 15px;
            }
            
            .images {
                padding: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .image {
                gap: 12px;
            }
            
            .images {
                padding: 1rem;
            }
        }

        /* Improve table responsiveness */
        .products-table {
            margin-top: 0.8rem;
        }

        @media (max-width: 480px) {
            th, td {
                padding: 0.4rem 0.3rem;
                font-size: 0.7rem;
            }

            .action-btn {
                padding: 0.2rem;
                font-size: 0.7rem;
            }
        }

        /* Table Responsive Adjustments */
        @media (max-width: 1024px) {
            table {
                min-width: 450px;
            }

            th, td {
                padding: 0.4rem 0.3rem;
                font-size: 0.75rem;
            }

            .action-btn {
                padding: 0.25rem;
                width: 26px;
                height: 26px;
            }
        }

        @media (max-width: 768px) {
            table {
                min-width: 400px;
            }

            th, td {
                padding: 0.35rem 0.25rem;
                font-size: 0.7rem;
            }

            .action-btn {
                padding: 0.2rem;
                width: 24px;
                height: 24px;
                margin-right: 0.2rem;
            }
        }

        @media (max-width: 480px) {
            table {
                min-width: 350px;
            }

            th, td {
                padding: 0.3rem 0.2rem;
                font-size: 0.65rem;
            }

            .action-btn {
                padding: 0.15rem;
                width: 22px;
                height: 22px;
                margin-right: 0.15rem;
            }

            .products-table {
                margin-top: 0.6rem;
            }
        }

        /* Add smooth transitions */
        .action-btn {
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        /* Improve table scrolling */
        .products-table {
            scrollbar-width: thin;
            scrollbar-color: var(--accent-color) var(--light-bg);
        }

        .products-table::-webkit-scrollbar {
            height: 6px;
        }

        .products-table::-webkit-scrollbar-track {
            background: var(--light-bg);
            border-radius: 3px;
        }

        .products-table::-webkit-scrollbar-thumb {
            background-color: var(--accent-color);
            border-radius: 3px;
        }

        /* Menu Toggle Button */
        .menu-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: none; /* Initially hidden */
            align-items: center;
            justify-content: center;
        }

        .status-cell {
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-align: center;
            display: inline-block;
            min-width: 100px;
        }

        .status-in-stock {
            background-color: #c6f6d5;
            color: #2f855a;
        }

        .status-out-of-stock {
            background-color: #fed7d7;
            color: #c53030;
        }

        /* Update the table cell for status */
        td:nth-child(6) {
            text-align: center;
        }

        /* Mobile responsive adjustments for status */
        @media (max-width: 768px) {
            .status-cell {
                min-width: 80px;
                font-size: 0.8rem;
                padding: 0.2rem 0.4rem;
            }
        }

        @media (max-width: 480px) {
            .status-cell {
                min-width: 70px;
                font-size: 0.7rem;
                padding: 0.15rem 0.3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Invexa Plus</h2>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="products.php" class="nav-link active">
                    <i class="fas fa-boxes"></i>
                    <span>Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="customer_order.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Customer Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="order.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="Trackorder.php" class="nav-link">
                    <i class="fas fa-truck"></i>
                    <span>Track Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="setting.php" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    
    <main class="main-content" id="main-content">
    <section class="images">
            <div class="prod">
                <h2>Electronics</h2>
            <div class="image">
                <div class="product"><img src="assets/mobile.jpg" width="100" height="100"> <h3>Mobiles</h3> </div>
                <div class="product"><img src="assets/laptop.jpg" width="100" height="100"> <h3>Laptops</h3></div>
                <div class="product"><img src="assets/tablets.jpg" width="100" height="100"> <h3>Tablets</h3> </div>
                <div class="product"><img src="assets/smartwatch.jpg" width="100" height="100"> <h3>SmartWatch</h3> </div>
                <div class="product"><img src="assets/keyboard and mice.jpg" width="100" height="100"> <h3>KeyBoard & Mice</h3> </div>
            </div>
            </div>
            <div class="prod">
            <h2>Furniture</h2>
            <div class="image">
                <div class="product"><img src="assets/bed.png" width="150" height="150"> <h3>Beds</h3> </div>
                <div class="product"><img src="assets/book.png" width="150" height="150"> <h3>BookShelfs</h3></div>
                <div class="product"><img src="assets/LuxiriousSofa.png" width="150" height="150"> <h3>Luxry Sofa</h3> </div>
                <div class="product"><img src="assets/table.png" width="150" height="150"> <h3>TableSets</h3> </div>
                <div class="product"><img src="assets/Study.png" width="150" height="150"> <h3>StudyTable</h3> </div>
            </div>
            </div>
            <div class="prod">
            <h2>Clothing</h2>
            <div class="image">
                <div class="product"><img src="assets/Dress2.png" width="150" height="150"> <h3>Dresses</h3> </div>
                <div class="product"><img src="assets/jackets.png" width="150" height="150"> <h3>Jackets</h3></div>
                <div class="product"><img src="assets/jeans.png" width="150" height="150"> <h3>Jeans</h3> </div>
                <div class="product"><img src="assets/tShirt.png" width="150" height="150"> <h3>tShirt</h3> </div>
                <div class="product"><img src="assets/shoes.png" width="150" height="150"> <h3>Shoes</h3> </div>
            </div>
            </div>
        </section>
        <div class="container">
            <h1 class="page-title">Products Management</h1>
            
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="add-product-form">
                <h2><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h2>
                <form method="POST">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="update" value="1">
                        <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="add" value="1">
                    <?php endif; ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product_name">Product Name</label>
                            <input type="text" id="product_name" name="product_name" required 
                                value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Electronics" <?php echo ($edit_product && $edit_product['category'] == 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                                <option value="Clothing" <?php echo ($edit_product && $edit_product['category'] == 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
                                <option value="Furniture" <?php echo ($edit_product && $edit_product['category'] == 'Furniture') ? 'selected' : ''; ?>>Furniture</option>
                                <option value="Other" <?php echo ($edit_product && $edit_product['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" id="price" name="price" step="0.01" required 
                                value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" id="quantity" name="quantity" required 
                                value="<?php echo $edit_product ? $edit_product['quantity'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="In Stock" <?php echo ($edit_product && $edit_product['status'] == 'In Stock') ? 'selected' : ''; ?>>In Stock</option>
                                <option value="Out of Stock" <?php echo ($edit_product && $edit_product['status'] == 'Out of Stock') ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn"><?php echo $edit_product ? 'Update Product' : 'Add Product'; ?></button>
                        <?php if ($edit_product): ?>
                            <a href="products.php" class="btn" style="background-color: #718096; margin-left: 10px;">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="products-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT id, product_name, category, quantity, price, status FROM product WHERE user_id = ?");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td>
                                <span class="status-cell status-<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?')" class="action-btn delete-btn">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="theme.js"></script>
    <script>
        // Mobile menu toggle functionality
        const menuToggle = document.createElement('button');
        menuToggle.className = 'menu-toggle';
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.appendChild(menuToggle);

        const sidebar = document.querySelector('.sidebar');

        // Function to handle menu toggle visibility
        function handleMenuToggle() {
            if (window.innerWidth <= 768) {
                menuToggle.style.display = 'flex';
            } else {
                menuToggle.style.display = 'none';
                sidebar.classList.remove('active');
            }
        }

        // Initial check
        handleMenuToggle();

        // Listen for window resize
        window.addEventListener('resize', handleMenuToggle);

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            // Toggle menu icon
            const icon = menuToggle.querySelector('i');
            if (sidebar.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
                // Reset menu icon
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    </script>
</body>
</html>

