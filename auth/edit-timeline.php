<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $timeline_id = intval($_POST['timeline_id']);
    $lomba_id = intval($_POST['lomba_id']);
    
    $step = mysqli_real_escape_string($conn, $_POST['step']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $created_at = mysqli_real_escape_string($conn, $_POST['created_at']);

    $q = "UPDATE timeline SET 
            step = '$step', 
            status = '$status', 
            created_at = '$created_at' 
          WHERE timeline_id = $timeline_id";
          
    if (mysqli_query($conn, $q)) {
        echo "<script>
                alert('Tahapan berhasil diubah!');
                window.location.href = '../user/detail-lomba.php?id=$lomba_id';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mengubah tahapan!');
                window.location.href = '../user/detail-lomba.php?id=$lomba_id';
              </script>";
    }
} else {
    header("Location: ../user/lomba.php");
}
?>