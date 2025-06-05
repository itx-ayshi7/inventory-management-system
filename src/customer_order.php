<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $total_amount = $_POST['total_amount'];
    $order_status = 'Pending';

    // Start transaction
    $conn->begin_transaction();

    try {
        // First, create the order
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, product_name, quantity, status, user_id) 
                               SELECT ?, p.product_name, ?, ?, ? 
                               FROM product p WHERE p.id = ?");
        $stmt->bind_param("sissi", $customer_name, $quantity, $order_status, $user_id, $product_id);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Then, create the customer order with the order_id
        $stmt = $conn->prepare("INSERT INTO customer_orders (order_id, user_id, customer_name, customer_email, customer_phone, customer_address, product_id, quantity, total_amount, order_status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssssids", $order_id, $user_id, $customer_name, $customer_email, $customer_phone, $customer_address, $product_id, $quantity, $total_amount, $order_status);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        $message = "Order placed successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
    $stmt->close();
}

// Fetch products for dropdown
$products_query = "SELECT id, product_name, price, category FROM product WHERE user_id = ? ORDER BY category, product_name";
$stmt = $conn->prepare($products_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$products = $stmt->get_result();

// Group products by category
$grouped_products = array();
while ($product = $products->fetch_assoc()) {
    $grouped_products[$product['category']][] = $product;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders - Invexa Plus</title>
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
        }

        .order-form {
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

        .view-products-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .view-products-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background-color: #c6f6d5;
            color: #2f855a;
        }

        .orders-table {
            width: 100%;
            background: var(--section-bg);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        tr:hover {
            background-color: var(--light-bg);
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex !important;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
                margin-top: 3.5rem;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                width: 280px;
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .order-form {
                padding: 1.2rem;
            }

            .page-title {
                font-size: 1.5rem;
            }
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
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .menu-toggle:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        /* Add overlay when sidebar is open */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
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
                <a href="products.php" class="nav-link">
                    <i class="fas fa-boxes"></i>
                    <span>Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="customer_order.php" class="nav-link active">
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
    <main class="main-content">
        <div class="container">
            <h1 class="page-title">Customer Orders</h1>
            
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <a href="customer_products.php" class="view-products-btn">
                <i class="fas fa-boxes"></i> View Products
            </a>

            <div class="order-form">
                <h2>New Customer Order</h2>
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">Email</label>
                            <input type="email" id="customer_email" name="customer_email" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_phone">Phone</label>
                            <input type="tel" id="customer_phone" name="customer_phone" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_address">Address</label>
                            <input type="text" id="customer_address" name="customer_address" required>
                        </div>
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select id="product_id" name="product_id" required>
                                <option value="">Select Product</option>
                                <?php foreach ($grouped_products as $category => $category_products): ?>
                                    <optgroup label="<?php echo htmlspecialchars($category); ?>">
                                        <?php foreach ($category_products as $product): ?>
                                            <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                                                <?php echo htmlspecialchars($product['product_name']); ?> - $<?php echo $product['price']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" id="quantity" name="quantity" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="total_amount">Total Amount</label>
                            <input type="number" id="total_amount" name="total_amount" step="0.01" readonly>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Place Order</button>
                    </div>
                </form>
            </div>

            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>Customer Order ID</th>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders_query = "SELECT co.*, p.product_name, o.id as linked_order_id, o.status as order_status 
                                       FROM customer_orders co 
                                       JOIN product p ON co.product_id = p.id 
                                       JOIN orders o ON co.order_id = o.id
                                       WHERE co.user_id = ? 
                                       ORDER BY co.id DESC";
                        $stmt = $conn->prepare($orders_query);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $orders = $stmt->get_result();
                        
                        while ($order = $orders->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td>#<?php echo str_pad($order['linked_order_id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-cell status-<?php echo strtolower(str_replace(' ', '-', $order['order_status'])); ?>">
                                    <?php echo htmlspecialchars($order['order_status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="theme.js"></script>
    <script>
        // Calculate total amount when product or quantity changes
        document.getElementById('product_id').addEventListener('change', calculateTotal);
        document.getElementById('quantity').addEventListener('input', calculateTotal);

        function calculateTotal() {
            const productSelect = document.getElementById('product_id');
            const quantity = document.getElementById('quantity').value;
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            
            if (selectedOption && quantity) {
                const price = selectedOption.getAttribute('data-price');
                const total = price * quantity;
                document.getElementById('total_amount').value = total.toFixed(2);
            } else {
                document.getElementById('total_amount').value = '';
            }
        }

        // Mobile menu functionality
        const menuToggle = document.createElement('button');
        menuToggle.className = 'menu-toggle';
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.appendChild(menuToggle);

        const sidebar = document.querySelector('.sidebar');
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);

        // Function to handle menu toggle visibility
        function handleMenuToggle() {
            if (window.innerWidth <= 768) {
                menuToggle.style.display = 'flex';
            } else {
                menuToggle.style.display = 'none';
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        }

        // Initial check
        handleMenuToggle();

        // Listen for window resize
        window.addEventListener('resize', handleMenuToggle);

        // Toggle menu
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            const icon = menuToggle.querySelector('i');
            if (sidebar.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close menu when clicking overlay
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        });

        // Handle orientation change
        window.addEventListener('orientationchange', () => {
            handleMenuToggle();
        });
    </script>
</body>
</html> 