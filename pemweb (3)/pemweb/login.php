<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'technician':
                    header('Location: technician/dashboard.php');
                    break;
                default:
                    header('Location: index.php');
            }
            exit;
        }
    }

    echo "<script>
            alert('Login gagal: username atau password salah.');
            window.location = 'login.php';
          </script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Locals One</title>
  <link rel="stylesheet" href="assets/style_login.css">
</head>
<body>
  <div class="container">
    <div class="left">
      <img src="assets/img/login.jpg" alt="Login Image">
    </div>
    <div class="right">
      <h2>Welcome to ReparoTech!</h2>
      <p class="form-title">Login Form</p>
      <form action="login.php" method="POST">
        <input type="username" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <label class="remember">
          <input type="checkbox" name="remember"> Remember Me
        </label>
        <button type="submit" class="btn primary">Login</button>
        <a href="register.php" class="btn secondary">Create Account</a>
        <div class="bottom-links">
          <a href="forgot.php">🔑 Forgot Password?</a>
          <span>|</span>
          <a href="index.php">🏠 Back to Home</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
