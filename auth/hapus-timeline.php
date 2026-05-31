<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if (isset($_GET['id']) && isset($_GET['lomba_id'])) {
    $timeline_id = intval($_GET['id']);
    $lomba_id = intval($_GET['lomba_id']);

    $q = "DELETE FROM timeline WHERE timeline_id = $timeline_id";
    
    if (mysqli_query($conn, $q)) {
        echo "<script>
                alert('Tahapan berhasil dihapus!');
                window.location.href = '../user/detail-lomba.php?id=$lomba_id';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus tahapan!');
                window.location.href = '../user/detail-lomba.php?id=$lomba_id';
              </script>";
    }
} else {
    header("Location: ../user/lomba.php");
}
?>