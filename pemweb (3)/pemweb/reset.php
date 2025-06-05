<?php
session_start();
include 'db.php';

if (!isset($_SESSION['reset_user'])) {
  header("Location: forgot.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $new_password = hash('sha256', $_POST['new_password']);
  $username = $_SESSION['reset_user'];

  $query = "UPDATE users SET password='$new_password' WHERE username='$username'";
  if (mysqli_query($conn, $query)) {
    unset($_SESSION['reset_user']);
    echo "<script>alert('Password berhasil diubah!'); window.location='index.php';</script>";
  } else {
    echo "<script>alert('Gagal reset password'); window.location='reset.php';</script>";
  }
}
?>
<?php
// Jika tidak ada POST data, arahkan balik
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: forgot.php');
  exit;
}

// Simpan username/email di session kalau perlu validasi, tapi di sini kita langsung ke form baru
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - Locals One</title>
  <link rel="stylesheet" href="assets/style_login.css">
</head>
<body>
<div class="container">
  <div class="left">
    <img src="assets/img/login.jpg" alt="Reset Image">
  </div>
  <div class="right">
    <h2>Welcome to ReparoTech!</h2>
    <p class="form-title">Reset Password</p>
    <form action="success.php" method="POST">
      <input type="password" name="new_password" placeholder="Enter New Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
      <button type="submit" class="btn yellow">Confirm</button>
      <div class="bottom-links">
        <a href="login.php">🔙 Back to Login</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
