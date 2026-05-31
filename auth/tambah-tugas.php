<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_tugas'])) {

    $lomba_id  = $_POST['lomba_id'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $tanggal   = date('Y-m-d');

    $query = "INSERT INTO progress (user_id, lomba_id, deskripsi, presentase, tanggal_update) 
              VALUES ($user_id_aktif, $lomba_id, '$deskripsi', 0, '$tanggal')";
    
    if (mysqli_query($conn, $query)) {

        header("Location: ../user/detail-lomba.php?id=$lomba_id");
        exit();
    } else {
        echo "<script>
                alert('Gagal menambah tugas: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: ../user/home.php");
    exit();
}
?>