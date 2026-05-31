<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "ukl_lomba";

$conn = new mysqli($host, $user, $pass, $db);

if (!$conn) {
 die("Koneksi database gagal: " . $conn->connect_error);
}
?>