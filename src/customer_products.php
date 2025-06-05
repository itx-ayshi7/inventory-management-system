<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch products grouped by category
$products_query = "SELECT id, product_name, price, category, quantity, status FROM product WHERE user_id = ? ORDER BY category, product_name";
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
    <title>Products - Invexa Plus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2c5282;
            --accent-color: #4299e1;
            --light-bg: #ffffff;
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
            background-color: #ffffff;
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
            background: #ffffff;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            color: #1a365d;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            font-weight: 700;
        }

        .category-section {
            margin-bottom: 2.5rem;
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .category-title {
            color: #2d3748;
            font-size: 1.8rem;
            margin-bottom: 1.2rem;
            padding-bottom: 0.4rem;
            border-bottom: 2px solid var(--accent-color);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            font-weight: 600;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            padding: 0.5rem 0;
        }

        .product-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            max-width: 220px;
            margin: 0 auto;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            transition: transform 0.3s ease;
            background-color: #f7fafc;
            position: relative;
        }

        .product-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0));
            pointer-events: none;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1rem;
            background: #ffffff;
        }

        .product-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2d3748;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-price {
            font-size: 1.1rem;
            color: var(--accent-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .product-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-in-stock {
            background-color: #c6f6d5;
            color: #2f855a;
            box-shadow: 0 2px 4px rgba(47, 133, 90, 0.2);
        }

        .status-out-of-stock {
            background-color: #fed7d7;
            color: #c53030;
            box-shadow: 0 2px 4px rgba(197, 48, 48, 0.2);
        }

        .order-btn {
            display: inline-block;
            background-color: var(--accent-color);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            box-shadow: 0 2px 4px rgba(66, 153, 225, 0.2);
        }

        .order-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(44, 82, 130, 0.3);
        }

        .order-btn:disabled {
            background-color: #cbd5e0;
            color: #4a5568;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Dark mode text adjustments */
        body.dark .product-name {
            color: #2d3748;
        }

        body.dark .product-price {
            color: #63b3ed;
        }

        body.dark .product-info {
            background: linear-gradient(to bottom, #2d3748, #1a202c);
        }

        body.dark .status-in-stock {
            background-color: #2f855a;
            color: #ffffff;
        }

        body.dark .status-out-of-stock {
            background-color: #c53030;
            color: #ffffff;
        }

        body.dark .order-btn:disabled {
            background-color: #4a5568;
            color: #a0aec0;
        }

        /* Mobile text adjustments */
        @media (max-width: 768px) {
            .product-name {
                font-size: 0.9rem;
                font-weight: 700;
            }

            .product-price {
                font-size: 1rem;
                font-weight: 800;
            }

            .product-status {
                font-size: 0.75rem;
                font-weight: 700;
            }

            .order-btn {
                font-size: 0.8rem;
                font-weight: 700;
            }
        }

        @media (max-width: 480px) {
            .product-name {
                font-size: 0.85rem;
            }

            .product-price {
                font-size: 0.95rem;
            }

            .product-status {
                font-size: 0.7rem;
            }

            .order-btn {
                font-size: 0.75rem;
            }
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
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
                align-items: center;
                justify-content: center;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
                margin-top: 3.5rem;
                width: 100%;
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

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 1rem;
                padding: 0.5rem;
            }

            .product-card {
                max-width: 160px;
                margin: 0 auto;
            }

            .product-image {
                height: 140px;
            }

            .product-info {
                padding: 0.8rem;
            }

            .product-name {
                font-size: 0.9rem;
                margin-bottom: 0.4rem;
            }

            .product-price {
                font-size: 0.95rem;
                margin-bottom: 0.4rem;
            }

            .product-status {
                font-size: 0.7rem;
                padding: 0.2rem 0.5rem;
                margin-bottom: 0.6rem;
            }

            .order-btn {
                padding: 0.5rem 0.8rem;
                font-size: 0.75rem;
            }

            .category-section {
                padding: 1rem;
                margin-bottom: 1.5rem;
            }

            .category-title {
                font-size: 1.2rem;
                margin-bottom: 1rem;
            }

            .page-title {
                font-size: 2rem;
                color: #1a365d;
            }

            .container {
                padding: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.8rem;
            }

            .product-card {
                max-width: 100%;
            }

            .product-image {
                height: 120px;
            }

            .product-info {
                padding: 0.6rem;
            }

            .product-name {
                font-size: 0.8rem;
                margin-bottom: 0.3rem;
            }

            .product-price {
                font-size: 0.85rem;
                margin-bottom: 0.3rem;
            }

            .product-status {
                font-size: 0.65rem;
                padding: 0.15rem 0.4rem;
                margin-bottom: 0.4rem;
            }

            .order-btn {
                padding: 0.4rem 0.6rem;
                font-size: 0.7rem;
            }

            .category-section {
                padding: 0.8rem;
                margin-bottom: 1.2rem;
            }

            .category-title {
                font-size: 1.3rem;
                color: #2d3748;
            }

            .page-title {
                font-size: 1.8rem;
                color: #1a365d;
            }

            .container {
                padding: 0.3rem;
            }

            /* Improve touch targets */
            .nav-link {
                padding: 0.8rem 1rem;
                min-height: 44px;
            }

            .menu-toggle {
                width: 44px;
                height: 44px;
            }
        }

        /* Add smooth transitions for mobile menu */
        .sidebar {
            transition: transform 0.3s ease-in-out;
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

        /* Improve scroll behavior */
        .products-grid {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        /* Add pull-to-refresh visual feedback */
        .pull-to-refresh {
            text-align: center;
            padding: 1rem;
            color: var(--text-color);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .pull-to-refresh.active {
            opacity: 1;
        }

        /* Add animation for product cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Add loading animation for images */
        @keyframes imageLoading {
            0% { opacity: 0; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .product-image.loading {
            animation: imageLoading 1s infinite;
        }

        /* Add error state for images */
        .product-image.error {
            background-color: #f7fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a0aec0;
            font-size: 0.875rem;
        }

        /* Dark mode adjustments for headings */
        body.dark .page-title {
            color: #1a365d;
        }

        body.dark .category-title {
            color: #2d3748;
        }

        body.dark .product-name {
            color: #2d3748;
        }

        /* Mobile adjustments for headings */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
                color: #1a365d;
            }

            .category-title {
                font-size: 1.5rem;
                color: #2d3748;
            }

            .product-name {
                font-size: 0.9rem;
                color: #2d3748;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.8rem;
                color: #1a365d;
            }

            .category-title {
                font-size: 1.3rem;
                color: #2d3748;
            }

            .product-name {
                font-size: 0.85rem;
                color: #2d3748;
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
                <a href="products.php" class="nav-link">
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
    <main class="main-content">
        <div class="container">
            <h1 class="page-title">Available Products</h1>

            <?php foreach ($grouped_products as $category => $products): ?>
            <section class="category-section">
                <h2 class="category-title"><?php echo htmlspecialchars($category); ?></h2>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php
                        // Determine the image path based on product name and category
                        $image_path = 'assets/default-product.jpg';
                        $product_name = strtolower(str_replace(' ', '_', $product['product_name']));
                        
                        // Map product names to image files
                        $image_mapping = [
                            // Electronics
                            'mobile' => 'mobile.jpg',
                            'laptop' => 'laptop.jpg',
                            'tablet' => 'tablets.jpg',
                            'smartwatch' => 'smartwatch.jpg',
                            'keyboard' => 'keyboard and mice.jpg',
                            
                            // Furniture
                            'bed' => 'bed.png',
                            'bookshelf' => 'book.png',
                            'sofa' => 'LuxiriousSofa.png',
                            'table' => 'table.png',
                            'study_table' => 'Study.png',
                            
                            // Clothing
                            'dress' => 'Dress2.png',
                            'jacket' => 'jackets.png',
                            'jeans' => 'jeans.png',
                            'tshirt' => 'tShirt.png',
                            'shoes' => 'shoes.png'
                        ];

                        // Try to find a matching image
                        foreach ($image_mapping as $key => $image) {
                            if (strpos($product_name, $key) !== false) {
                                $image_path = 'assets/' . $image;
                                break;
                            }
                        }
                        ?>
                        <img src="<?php echo $image_path; ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                             class="product-image"
                             onerror="this.src='assets/default-product.jpg'">
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                            <span class="product-status status-<?php echo strtolower(str_replace(' ', '-', $product['status'])); ?>">
                                <?php echo htmlspecialchars($product['status']); ?>
                            </span>
                            <a href="customer_order.php?product_id=<?php echo $product['id']; ?>" 
                               class="order-btn" 
                               <?php echo $product['status'] === 'Out of Stock' ? 'disabled' : ''; ?>>
                                <?php echo $product['status'] === 'Out of Stock' ? 'Out of Stock' : 'Order Now'; ?>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="theme.js"></script>
    <script>
        // Mobile menu functionality
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);

        // Toggle menu
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        });

        // Close menu when clicking overlay
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Handle orientation change
        window.addEventListener('orientationchange', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Add pull-to-refresh functionality
        let touchStart = 0;
        let touchEnd = 0;
        const pullThreshold = 100;

        document.addEventListener('touchstart', (e) => {
            touchStart = e.touches[0].clientY;
        });

        document.addEventListener('touchmove', (e) => {
            touchEnd = e.touches[0].clientY;
            const pull = touchEnd - touchStart;
            
            if (window.scrollY === 0 && pull > 0) {
                e.preventDefault();
                const pullIndicator = document.querySelector('.pull-to-refresh');
                if (pullIndicator) {
                    pullIndicator.classList.add('active');
                }
            }
        });

        document.addEventListener('touchend', () => {
            const pull = touchEnd - touchStart;
            if (pull > pullThreshold) {
                window.location.reload();
            }
            const pullIndicator = document.querySelector('.pull-to-refresh');
            if (pullIndicator) {
                pullIndicator.classList.remove('active');
            }
        });

        // Add this to your existing script section
        document.addEventListener('DOMContentLoaded', function() {
            // Handle image loading and errors
            const productImages = document.querySelectorAll('.product-image');
            productImages.forEach(img => {
                img.classList.add('loading');
                
                img.onload = function() {
                    this.classList.remove('loading');
                };
                
                img.onerror = function() {
                    this.classList.remove('loading');
                    this.classList.add('error');
                    this.src = 'assets/default-product.jpg';
                };
            });
        });
    </script>
</body>
</html> 