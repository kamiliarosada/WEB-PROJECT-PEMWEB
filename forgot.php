<?php include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $new1 = $_POST["new_password"];
    $new2 = $_POST["confirm_password"];

    if ($new1 !== $new2) {
        echo "Password tidak cocok!";
    } else {
        $res = $conn->query("SELECT * FROM users WHERE username='$username' AND email='$email'");
        if ($res->num_rows > 0) {
            $hash = password_hash($new1, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$hash' WHERE username='$username'");
            echo "Password berhasil diubah. <a href='login.php'>Login sekarang</a>";
        } else {
            echo "Username dan email tidak cocok!";
        }
    }
}
?>

<link rel="stylesheet" href="styles.css">
<div class="container">
<h2>Lupa Password</h2>
<form method="post">
    Username: <input type="text" name="username" required><br>
    Email: <input type="email" name="email" required><br>
    Password Baru: <input type="password" name="new_password" required><br>
    Ulangi Password: <input type="password" name="confirm_password" required><br>
    <button type="submit">Reset Password</button>
</form>
<p><a href='login.php'>Kembali ke Login</a></p>
</div>
