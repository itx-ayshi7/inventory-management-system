<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$statusMessage = '';
$orderFound = false;
$status = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = trim($_POST['order_id']);

    if (!is_numeric($order_id)) {
        $statusMessage = "Invalid Order ID format.";
    } else {
        // Query the database
        $stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->bind_result($status);

        if ($stmt->fetch()) {
            $statusMessage = $status;
            $orderFound = true;
        } else {
            $statusMessage = "Order not found. Please check the ID.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Track Order | Invexa Plus</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/theme.css" />
  <link rel="stylesheet" href="css/responsive.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      --primary-color: #1a365d;
      --secondary-color: #2c5282;
      --accent-color: #4299e1;
      --light-bg: #f7fafc;
      --text-color: #2d3748;
      --border-color: #e2e8f0;
      --sidebar-width: 250px;
      --section-bg: white;
      --page-title: #2b6cb0;
    }
    body.dark{
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

    /* Mobile Responsive Styles */
    @media (max-width: 1024px) {
      .track-container {
        max-width: 500px;
        margin: 60px auto;
        padding: 30px;
      }

      .order-progress-bar {
        margin: 25px 0 15px 0;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        z-index: 1000;
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
        padding: 1rem;
      }

      .menu-toggle {
        display: block;
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
      }

      .track-container {
        margin: 40px auto;
        padding: 20px;
      }

      .track-container h1 {
        font-size: 1.5rem;
        margin-bottom: 20px;
      }

      .order-progress-bar {
        flex-wrap: wrap;
        gap: 10px;
      }

      .order-progress-bar .step {
        flex: 1 1 calc(50% - 10px);
        min-width: 120px;
        font-size: 0.9rem;
      }

      .order-progress-bar .step:not(:last-child)::after {
        display: none;
      }

      .status-box {
        margin-top: 20px;
        padding: 12px;
      }
    }

    @media (max-width: 480px) {
      .track-container {
        margin: 20px auto;
        padding: 15px;
      }

      .track-container h1 {
        font-size: 1.3rem;
      }

      .track-container input {
        padding: 10px;
        font-size: 14px;
      }

      .track-container button {
        padding: 10px;
        font-size: 14px;
      }

      .order-progress-bar .step {
        flex: 1 1 100%;
        font-size: 0.8rem;
      }

      .status-box {
        font-size: 0.9rem;
      }

      .back-link {
        font-size: 0.9rem;
      }
    }

    /* Add smooth transitions */
    .track-container, .order-progress-bar .step, .status-box {
      transition: all 0.3s ease;
    }

    /* Improve touch targets on mobile */
    @media (max-width: 768px) {
      .track-container button, .back-link {
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
    }

    .track-container {
      max-width: 600px;
      margin: 80px auto;
      padding: 40px;
      background: var(--section-bg);
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }

    .track-container h1 {
      color: var(--page-title);
      text-align: center;
      margin-bottom: 30px;
    }

    .track-container form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .track-container input {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .track-container button {
      padding: 12px;
      background-color: #2b6cb0;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .track-container button:hover {
      background-color: #1a4c8b;
    }

    .status-box {
      margin-top: 25px;
      padding: 15px;
      border-left: 4px solid #2b6cb0;
      background-color: var(--light-bg);
      border-radius: 6px;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      text-decoration: none;
      color: var(--page-title);
      font-weight: bold;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    /* Step Progress Bar Styles */
    .order-progress-bar {
      display: flex;
      justify-content: space-between;
      margin: 30px 0 20px 0;
      font-weight: 600;
      color: #a0aec0; /* gray for inactive */
      user-select: none;
    }

    .order-progress-bar .step {
      position: relative;
      flex: 1;
      text-align: center;
      padding: 10px 5px 0 5px;
    }

    .order-progress-bar .step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 22px;
      right: 0;
      width: 100%;
      height: 4px;
      background-color: #cbd5e0; /* light gray line */
      z-index: -1;
    }

    .order-progress-bar .step.active {
      color: var(--text-color); /* blue active text */
    }

    .order-progress-bar .step.active::before {
      content: '●';
      display: block;
      margin: 0 auto 8px auto;
      font-size: 18px;
      color: #2b6cb0;
    }

    .order-progress-bar .step:not(.active)::before {
      content: '○';
      display: block;
      margin: 0 auto 8px auto;
      font-size: 18px;
      color: #cbd5e0;
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
      <a href="Trackorder.php" class="nav-link active">
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
    <h1 class="page-title">Track Orders</h1>
    <div class="track-container">
      <h1>Track Your Order</h1>
      <form action="trackOrder.php" method="POST">
        <input type="text" name="order_id" placeholder="Enter Order ID (e.g., 1001)" required />
        <button type="submit">Check Status</button>
      </form>

      <?php if ($orderFound): ?>
        <div class="order-progress-bar">
          <div class="step <?php echo ($status == 'Order Placed') ? 'active' : ''; ?>">Order Placed</div>
          <div class="step <?php echo ($status == 'Processing') ? 'active' : ''; ?>">Processing</div>
          <div class="step <?php echo ($status == 'Shipped') ? 'active' : ''; ?>">Shipped</div>
          <div class="step <?php echo ($status == 'Delivered') ? 'active' : ''; ?>">Delivered</div>
        </div>
      <?php endif; ?>

      <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <div class="status-box">
          <strong>Status:</strong> <span><?php echo htmlspecialchars($statusMessage); ?></span>
        </div>
      <?php endif; ?>

      <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
  </div>
</main>

<script>
  // Mobile menu toggle functionality
  const menuToggle = document.createElement('button');
  menuToggle.className = 'menu-toggle';
  menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
  document.body.appendChild(menuToggle);

  const sidebar = document.querySelector('.sidebar');

  menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && 
      !sidebar.contains(e.target) && 
      !menuToggle.contains(e.target)) {
      sidebar.classList.remove('active');
    }
  });
</script>
<script src="theme.js"></script>
<script src="js/script.js"></script>
<script src="js/theme.js"></script>
</body>
</html>

