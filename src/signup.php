<?php
include 'db.php';
session_start();

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } 
    // *** Added password validation here ***
    elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[\W_]/', $password)) {
        $error = "Password must contain at least one special character.";
    }
    // *** end of added validation ***
    else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $stmt->insert_id;
                header("Location: products.php");
                exit();
            } else {
                $error = "Signup failed. Please try again.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up - Invexa Plus</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/theme.css" />
  <link rel="stylesheet" href="css/responsive.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <style>
    /* Use the same styles as login.php for consistency */
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

    .signup-container {
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

    .signup-box {
      display: flex;
      width: 800px;
      border-radius: 10px;
      overflow: hidden;
      background: linear-gradient(to right, #000, #000);
      box-shadow: 0 0 50px white;
      animation: fadeInScale 0.8s ease-out;
    }

    .signup-left,
    .signup-right {
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

    .signup-left {
      background-color: #000;
      color: white;
    }

    .signup-left h2 {
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

    .signup-btn {
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

    .signup-btn:hover {
      background: linear-gradient(to left, #00f0ff,#2b6cb0);
    }

    .login-text {
      margin-top: 20px;
      font-size: 14px;
      color: #aaa;
    }

    .login-text a {
      color: #00f0ff;
      text-decoration: none;
    }

    .error-message {
      margin-top: 10px;
      font-size: 14px;
      color: #ff6666;
    }

    .signup-right {
      background: linear-gradient(to bottom right, #1a365d ,#2b6cb0);
      color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .signup-right video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0.5;
      z-index: 1;
    }

    .signup-right-content {
      position: relative;
      z-index: 2;
      padding: 20px;
    }

    .signup-right h1 {
      font-size: 32px;
      font-weight: 900;
      line-height: 1.2;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .signup-right span {
      color: #fff;
    }

    .signup-right p {
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
  <div class="signup-container">
    <div class="signup-box">
      <div class="signup-left">
        <h2>Create Account</h2>
        <form method="POST" action="signup.php">
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username ?? ''); ?>" />
          </div>
          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email ?? ''); ?>" />
          </div>
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required />
          </div>
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required />
          </div>
          <button type="submit" class="signup-btn">Sign Up</button>
          <p class="login-text">Already have an account? <a href="login.php">Login</a></p>
          <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
          <?php endif; ?>
        </form>
      </div>
      <div class="signup-right">
        <video autoplay muted loop>
          <source src="assets/loginv.mp4" type="video/mp4">
        </video>
        <div class="signup-right-content">
          <h1>JOIN<br><span>US!</span></h1>
          <p>Create your Invexa Plus account.</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
