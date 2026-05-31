<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php"); 
    exit;
}

$user_id_aktif = $_SESSION['user_id'];

$role_aktif = $_SESSION['role'] ?? ''; 
if ($role_aktif !== 'siswa') {
    header("Location: ../admin/home.php"); 
    exit();
}
?>