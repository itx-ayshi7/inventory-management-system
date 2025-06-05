<?php
include 'db.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Prepare statement to avoid SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Invexa Plus</title>
  <link rel="stylesheet" href="css/theme.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <style>
    /* Same styles as before */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-image: url('assets/loginbg.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    @keyframes fadeInScale {
      from {
        opacity: 0;
        transform: scale(0.95);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .login-box {
      display: flex;
      width: 800px;
      border-radius: 10px;
      overflow: hidden;
      background: linear-gradient(to right, #000, #000);
      box-shadow: 0 0 50px white;
      animation: fadeInScale 0.8s ease-out;
    }

    .login-left, .login-right {
      padding: 40px;
      width: 50%;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .login-left {
      background-color: #000;
      color: white;
    }

    .login-left h2 {
      margin-bottom: 30px;
      font-size: 28px;
    }

    .input-group {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 1px solid #ccc;
      padding: 8px 0;
    }

    .input-group i {
      color: #ccc;
      margin-right: 10px;
    }

    .input-group input {
      background: none;
      border: none;
      outline: none;
      color: white;
      width: 100%;
      font-size: 16px;
    }

    .login-btn {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      background: linear-gradient(to right, #00f0ff,#2b6cb0);
      border: none;
      border-radius: 20px;
      color: #000;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-btn:hover {
      background: linear-gradient(to left, #00f0ff,#2b6cb0);
    }

    .signup-text {
      margin-top: 20px;
      font-size: 14px;
      color: #aaa;
    }

    .signup-text a {
      color: #00f0ff;
      text-decoration: none;
    }

    .error-message {
      margin-top: 10px;
      font-size: 14px;
      color: #ff6666;
    }

    .login-right {
      background: linear-gradient(to bottom right, #1a365d ,#2b6cb0);
      color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .login-right video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0.5;
      z-index: 1;
    }

    .login-right-content {
      position: relative;
      z-index: 2;
      padding: 20px;
    }

    .login-right h1 {
      font-size: 32px;
      font-weight: 900;
      line-height: 1.2;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .login-right span {
      color: #fff;
    }

    .login-right p {
      margin-top: 20px;
      font-size: 14px;
      max-width: 200px;
      margin-left: auto;
      margin-right: auto;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <div class="login-left">
        <h2>Login</h2>
        <form method="POST" action="login.php">
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username ?? ''); ?>" />
          </div>
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password " required />
          </div>
          <button type="submit" class="login-btn">Login</button>
          <p class="signup-text">Don't have an account? <a href="signup.php">Sign Up</a></p>
          <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
          <?php endif; ?>
        </form>
      </div>
      <div class="login-right">
        <video autoplay muted loop>
          <source src="assets/loginv.mp4" type="video/mp4">
        </video>
        <div class="login-right-content">
          <h1>WELCOME<br><span>BACK!</span></h1>
          <p>To Invexa Plus.</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
