<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lomba_id'])) {
    $lomba_id = intval($_POST['lomba_id']);

    if (isset($_FILES['file_berkas']) && $_FILES['file_berkas']['error'] == 0) {
        $nama_file = $_FILES['file_berkas']['name'];
        $tmp_file = $_FILES['file_berkas']['tmp_name'];

        $nama_baru = time() . "_" . str_replace(" ", "_", $nama_file);
        $lokasi_simpan = "../assets/uploads/" . $nama_baru;

        if (move_uploaded_file($tmp_file, $lokasi_simpan)) {
            
            $query = "UPDATE lomba SET juknis = '$nama_baru' WHERE lomba_id = $lomba_id";
            
            if (mysqli_query($conn, $query)) {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            } else {
                echo "Gagal menyimpan: " . mysqli_error($conn);
                exit();
            }
        }
    }
}
?>