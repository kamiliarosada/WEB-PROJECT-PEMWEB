<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $email = $_POST['email'];

  $query = "SELECT * FROM users WHERE username='$username' AND email='$email'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) == 1) {
    session_start();
    $_SESSION['reset_user'] = $username;
    header("Location: reset.php");
    exit;
  } else {
    echo "<script>alert('Data tidak cocok!'); window.location='forgot.php';</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - Locals One</title>
  <link rel="stylesheet" href="assets/style_login.css">
</head>
<body>
<div class="container">
  <div class="left">
    <img src="assets/img/login.jpg" alt="Forgot Image">
  </div>
  <div class="right">
    <h2>Welcome from Locals One!</h2>
    <p class="form-title">Forgot Password</p>
    <form action="reset.php" method="POST">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="email" name="email" placeholder="Enter Email" required>
      <button type="submit" class="btn primary">Next</button>
      <div class="bottom-links">
        <a href="login.php">🔙 Back to Login</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
