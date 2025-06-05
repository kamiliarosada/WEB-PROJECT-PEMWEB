<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newPassword = $_POST['new_password'];
  $confirm = $_POST['confirm_password'];

  if ($newPassword === $confirm) {
    header("Refresh: 2; url=login.php");
    echo "<!DOCTYPE html>
    <html><head><title>Success</title>
    <link rel='stylesheet' href='assets/style_login.css'></head>
    <body><div class='container' style='text-align:center; padding:40px;'>
    <h2>Password berhasil diperbarui!</h2>
    <p>Kamu akan diarahkan ke halaman login...</p>
    </div></body></html>";
  } else {
    echo "<script>alert('Password tidak cocok!'); window.history.back();</script>";
  }
} else {
  header("Location: forgot.php");
}
?>
