<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name     = $_POST['name'];
  $username = $_POST['username'];
  $email    = $_POST['email'];
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
  $phone    = $_POST['phone'];
  $country  = $_POST['country'];
  $gender   = $_POST['gender'];
  $role     = 'user';

  $profile = $_FILES['profile_picture']['name'];
  $target = "uploads/" . basename($profile);
  move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target);

  $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
  if ($check->num_rows > 0) {
    echo "<script>alert('Username atau email sudah terdaftar!'); window.location='register.php';</script>";
  } else {
    $insert = "INSERT INTO users (name, username, email, password, phone, country, gender, profile_picture, role)
               VALUES ('$name', '$username', '$email', '$password', '$phone', '$country', '$gender', '$profile', '$role')";

    if ($conn->query($insert)) {
      echo "<script>alert('Register berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
      echo "<script>alert('Register gagal!'); window.location='register.php';</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Locals One</title>
  <link rel="stylesheet" href="assets/style_login.css">
</head>
<body>
  <div class="container">
    <div class="left">
      <img src="assets/img/register.jpg" alt="Register Image">
    </div>
    <div class="right">
      <h2>Welcome to ReparoTech!</h2>
      <p class="form-title">Registration Form</p>
      <form action="register.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Enter Your Name" required>
        <input type="text" name="username" placeholder="Enter Your Username" required>
        <input type="email" name="email" placeholder="Enter Email Address" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="tel" name="phone" placeholder="Enter Phone" required>
        <select name="country" required>
          <option value="">Select Country</option>
          <option value="Indonesia">Indonesia</option>
          <option value="USA">USA</option>
          <option value="UK">UK</option>
        </select>
        <div class="gender">
          Gender:
          <label><input type="radio" name="gender" value="Male" checked> Male</label>
          <label><input type="radio" name="gender" value="Female"> Female</label>
        </div>
        <label class="upload">Profile Picture:
          <input type="file" name="profile_picture">
        </label>
        <label class="terms">
          <input type="checkbox" required> I agree to Locals One Terms and Conditions.
        </label>
        <button type="submit" class="btn yellow">Register</button>
        <div class="bottom-links">
          <a href="login.php">🔑 Already Member?</a>
          <span>|</span>
          <a href="index.php">🏠 Back to Home</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
