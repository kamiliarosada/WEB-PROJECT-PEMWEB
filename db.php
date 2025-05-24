<?php
$conn = new mysqli("localhost", "root", "", "reparo");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>