<?php include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST["nama"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $alamat = $_POST["alamat"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
    if ($check->num_rows > 0) {
        echo "Username atau email sudah terdaftar!";
    } else {
        $conn->query("INSERT INTO users (nama, username, email, alamat, password)
                      VALUES ('$nama', '$username', '$email', '$alamat', '$password')");
        header("Location: login.php");
        exit;
    }
}
?>

<link rel="stylesheet" href="styles.css">
<div class="container">
<h2>Register</h2>
<form method="post">
    Nama Lengkap: <input type="text" name="nama" required><br>
    Username: <input type="text" name="username" required><br>
    Email: <input type="email" name="email" required><br>
    Alamat: <input type="text" name="alamat" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Daftar</button>
</form>
<p><a href='login.php'>Sudah punya akun?</a></p>
</div>
