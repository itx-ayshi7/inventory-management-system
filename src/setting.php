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

// Get message from URL if exists
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

// Handle username change request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_username'])) {
    $email = $_POST['email'];
    $new_username = $_POST['new_username'];
    
    // Verify email matches user's email
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['email'] === $email) {
        // Check if username is already taken
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Update username
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->bind_param("si", $new_username, $user_id);
            
            if ($stmt->execute()) {
                $message = "Username updated successfully!";
            } else {
                $message = "Error updating username.";
            }
        } else {
            $message = "Username is already taken.";
        }
    } else {
        $message = "Email verification failed. Please enter your correct email address.";
    }
    $stmt->close();
}

// Get current user info
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings - Invexa Plus</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
        :root {
          --primary-color: #1a365d;
          --secondary-color: #2c5282;
      --accent-color: #4299e1;
      --light-bg: #f7fafc;
      --text-color: #2d3748;
      --border-color: #e2e8f0;
      --section-bg: white;
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

    .settings-section {
      background: var(--section-bg);
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 2rem;
      overflow: hidden;
    }

    .settings-section h2 {
      color: var(--page-title);
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
    }

    .settings-grid {
      display: flex;
      flex-wrap: nowrap;
      gap: 20px;
      overflow-x: auto;
      padding: 10px 0;
      min-width: max-content;
    }

    .setting-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      transition: all 0.3s ease;
      min-width: 300px;
      flex-shrink: 0;
    }

    .setting-item:hover {
      border-color: var(--accent-color);
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .setting-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .setting-info i {
      font-size: 1.5rem;
      color: var(--accent-color);
    }

    .setting-details h3 {
      margin-bottom: 0.25rem;
      font-size: 1.1rem;
    }

    .setting-details p {
      color: var(--text-color);
      opacity: 0.8;
      font-size: 0.9rem;
    }

    /* Toggle Switch */
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: var(--accent-color);
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

    /* Mobile Responsive Styles */
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
    }

    /* Add these styles to your existing CSS */
    .password-form {
      max-width: 500px;
      margin: 0 auto;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
      color: var(--text-color);
      font-weight: 500;
    }

    .form-group label i {
      color: var(--accent-color);
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--border-color);
      border-radius: 4px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--accent-color);
      box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
    }

    .btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      background-color: var(--accent-color);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 4px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: var(--secondary-color);
    }

    .btn i {
      font-size: 1rem;
    }

    .error-message {
      color: #e53e3e;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      display: none;
    }

    .success-message {
      color: #38a169;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      display: none;
    }

    /* Hide scrollbar but keep functionality */
    .settings-grid {
      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none; /* IE and Edge */
    }

    .settings-grid::-webkit-scrollbar {
      display: none; /* Chrome, Safari, Opera */
    }

    /* Account Settings Form Styles */
    .account-form {
        max-width: 500px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-color);
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 1rem;
        background-color: var(--section-bg);
        color: var(--text-color);
    }

    .form-group input[readonly] {
        background-color: var(--light-bg);
        cursor: not-allowed;
    }

    .message {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 4px;
        background-color: #c6f6d5;
        color: #2f855a;
    }

    .message.error {
        background-color: #fed7d7;
        color: #c53030;
    }

    /* Mobile Responsive Styles for Account Form */
    @media (max-width: 768px) {
        .account-form {
            padding: 0 1rem;
        }

        .form-group input {
            font-size: 0.9rem;
        }
    }

    /* Password Form Styles */
    .password-requirements {
      font-size: 0.8rem;
      color: var(--text-color);
      opacity: 0.8;
      margin-top: 0.25rem;
    }

    .password-match-message {
      font-size: 0.8rem;
      margin-top: 0.25rem;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--accent-color);
      box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
    }

    .form-group input.error {
      border-color: #e53e3e;
    }

    .form-group input.success {
      border-color: #38a169;
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
        <a href="setting.php" class="nav-link active">
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
      <h1 class="page-title">Settings</h1>
      <div class="settings-section">
        <h2>Appearance</h2>
        <div class="settings-grid">
          <div class="setting-item">
            <div class="setting-info">
              <i class="fas fa-moon"></i>
              <div class="setting-details">
                <h3>Dark Mode</h3>
                <p>Toggle between light and dark theme</p>
              </div>
            </div>
            <label class="switch">
              <input type="checkbox" id="themeToggle">
              <span class="slider"></span>
            </label>
          </div>
        </div>
      </div>
      
      <?php if ($message): ?>
          <div class="message"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <!-- Account Settings Section -->
      <div class="settings-section">
          <h2>Account Settings</h2>
          <form method="POST" class="account-form">
              <div class="form-group">
                  <label for="current_username">Current Username</label>
                  <input type="text" id="current_username" value="<?php echo htmlspecialchars($user_info['username']); ?>" readonly>
              </div>
              <div class="form-group">
                  <label for="email">Email Verification</label>
                  <input type="email" id="email" name="email" required placeholder="Enter your email to verify">
              </div>
              <div class="form-group">
                  <label for="new_username">New Username</label>
                  <input type="text" id="new_username" name="new_username" required placeholder="Enter new username">
              </div>
              <button type="submit" name="change_username" class="btn">Update Username</button>
          </form>
      </div>


      <div class="settings-section">
        <h2>Change Password</h2>
        <form id="changePasswordForm" class="password-form" method="POST" action="change_password.php" onsubmit="return validatePasswordForm()">
          <div class="form-group">
            <label for="currentPassword">
              <i class="fas fa-key"></i>
              Current Password
            </label>
            <input type="password" id="currentPassword" name="currentPassword" required>
          </div>
          <div class="form-group">
            <label for="newPassword">
              <i class="fas fa-lock"></i>
              New Password
            </label>
            <input type="password" id="newPassword" name="newPassword" required minlength="8">
            <div class="password-requirements">
              Password must be at least 8 characters long
            </div>
          </div>
          <div class="form-group">
            <label for="confirmPassword">
              <i class="fas fa-lock"></i>
              Confirm New Password
            </label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
            <div class="password-match-message"></div>
          </div>
          <button type="submit" class="btn">
            <i class="fas fa-save"></i>
            Update Password
          </button>
        </form>
      </div>
    </div>
  </main>

  <script src="theme.js"></script>
  <script>
    // Theme toggle handling
    document.addEventListener('DOMContentLoaded', function() {
      const themeToggle = document.getElementById('themeToggle');
      
      // Handle theme toggle
      themeToggle.addEventListener('change', function() {
        if (this.checked) {
          document.body.classList.add('dark');
          localStorage.setItem('theme', 'dark');
        } else {
          document.body.classList.remove('dark');
          localStorage.setItem('theme', 'light');
        }
      });

      // Password validation
      const newPassword = document.getElementById('newPassword');
      const confirmPassword = document.getElementById('confirmPassword');
      const passwordMatchMessage = document.querySelector('.password-match-message');

      function checkPasswordMatch() {
        if (newPassword.value && confirmPassword.value) {
          if (newPassword.value === confirmPassword.value) {
            passwordMatchMessage.textContent = 'Passwords match';
            passwordMatchMessage.style.color = '#38a169';
          } else {
            passwordMatchMessage.textContent = 'Passwords do not match';
            passwordMatchMessage.style.color = '#e53e3e';
          }
        } else {
          passwordMatchMessage.textContent = '';
        }
      }

      newPassword.addEventListener('input', checkPasswordMatch);
      confirmPassword.addEventListener('input', checkPasswordMatch);
    });

    function validatePasswordForm() {
      const newPassword = document.getElementById('newPassword');
      const confirmPassword = document.getElementById('confirmPassword');
      
      if (newPassword.value.length < 8) {
        alert('New password must be at least 8 characters long');
        return false;
      }
      
      if (newPassword.value !== confirmPassword.value) {
        alert('Passwords do not match');
        return false;
      }
      
      return true;
    }
  </script>
</body>
</html>