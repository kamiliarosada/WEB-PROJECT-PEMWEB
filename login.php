<?php include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST["user"];
    $pass = $_POST["password"];

    $res = $conn->query("SELECT * FROM users WHERE username='$user' OR email='$user'");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (password_verify($pass, $row["password"])) {
            $_SESSION["user"] = $row["username"];
            header("Location: index.php");
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Akun tidak ditemukan!";
    }
}
?>

<link rel="stylesheet" href="styles.css">
<div class="container">
<h2>Login</h2>
<form method="post">
    Username / Email: <input type="text" name="user" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Masuk</button>
</form>
<p><a href="forgot.php">Lupa Password?</a></p>
<p><a href="register.php">Belum punya akun?</a></p>
</div>
