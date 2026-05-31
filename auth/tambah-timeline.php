<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lomba_id = intval($_POST['lomba_id']);
    
    $step = mysqli_real_escape_string($conn, $_POST['step']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $created_at = mysqli_real_escape_string($conn, $_POST['created_at']);

    $q = "INSERT INTO timeline (lomba_id, step, status, created_at) 
          VALUES ($lomba_id, '$step', '$status', '$created_at')";
          
    if (mysqli_query($conn, $q)) {
        echo "<script>
                alert('Tahapan baru berhasil ditambahkan!');
                window.location.href = '../user/detail-lomba.php?id=$lomba_id';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan tahapan!');
                window.location.href = '../user/detail-lomba.php?id=$lomba_id';
              </script>";
    }
} else {
    header("Location: ../user/lomba.php");
}
?>