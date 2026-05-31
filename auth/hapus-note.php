<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if (isset($_GET['id'])) {
    $id_yang_dihapus = intval($_GET['id']);

    $query = "DELETE FROM notes WHERE note_id = '$id_yang_dihapus'";

    if (mysqli_query($conn, $query)) {

        header("Location: ../user/home.php?pesan=hapus_berhasil");
    } else {

        echo "Gagal menghapus: " . mysqli_error($conn);
    }
} else {
    header("Location: ../user/home.php");
}
?>