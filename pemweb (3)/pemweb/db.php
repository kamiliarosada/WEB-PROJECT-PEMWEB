<?php
$host = "localhost";
$user = "root";
$pass = ""; // ganti jika password ada
$db   = "pemweb";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
