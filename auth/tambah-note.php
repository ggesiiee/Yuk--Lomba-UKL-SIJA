<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

$isi_note = mysqli_real_escape_string($conn, $_POST['isi_note']);

$user_id = $_SESSION['user_id']; 

$tanggal_sekarang = date('Y-m-d');

$query = "INSERT INTO notes (user_id, isi_note, tanggal) VALUES ('$user_id', '$isi_note', '$tanggal_sekarang')";

if (mysqli_query($conn, $query)) {
    header("Location: ../user/home.php");
    exit();
} else {
    echo "Gagal menyimpan: " . mysqli_error($conn);
}
?>