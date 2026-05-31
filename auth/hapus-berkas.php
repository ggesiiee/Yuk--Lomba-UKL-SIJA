<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if (isset($_GET['id'])) {
    $lomba_id = intval($_GET['id']);

    // 1. Cari tahu apa nama file yang tersimpan di database saat ini (Ganti ke kolom juknis)
    $q_file = mysqli_query($conn, "SELECT juknis FROM lomba WHERE lomba_id = $lomba_id");
    
    if ($q_file && mysqli_num_rows($q_file) > 0) {
        $dt = mysqli_fetch_assoc($q_file);
        $file_lama = $dt['juknis']; // Mengambil nama file dari kolom juknis

        // 2. Hapus file fisiknya dari folder 'assets/uploads' (Sesuaikan lokasi folder)
        if (!empty($file_lama) && file_exists("../assets/uploads/" . $file_lama)) {
            unlink("../assets/uploads/" . $file_lama); // unlink adalah perintah PHP untuk menghapus file
        }

        // 3. Kosongkan (NULL) kolom file di database (Update kolom juknis)
        mysqli_query($conn, "UPDATE lomba SET juknis = NULL WHERE lomba_id = $lomba_id");
    }
    
    // Kembalikan ke halaman sebelumnya
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>