<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lomba_id'])) {
    $lomba_id = intval($_POST['lomba_id']);

    if (isset($_FILES['file_berkas']) && $_FILES['file_berkas']['error'] == 0) {
        
        // --- PROSES 1: HAPUS FILE LAMA DARI FOLDER ---
        $q_file = mysqli_query($conn, "SELECT file_berkas FROM lomba WHERE lomba_id = $lomba_id");
        if ($q_file && mysqli_num_rows($q_file) > 0) {
            $dt = mysqli_fetch_assoc($q_file);
            $file_lama = $dt['file_berkas'];
            
            // Cek jika file lama ada, hapus dari folder
            if (!empty($file_lama) && file_exists("../uploads/" . $file_lama)) {
                unlink("../uploads/" . $file_lama);
            }
        }

        // --- PROSES 2: UPLOAD FILE BARU ---
        $nama_file = $_FILES['file_berkas']['name'];
        $tmp_file = $_FILES['file_berkas']['tmp_name'];
        
        // Buat nama unik baru
        $nama_baru = time() . "_" . str_replace(" ", "_", $nama_file);
        $lokasi_simpan = "../uploads/" . $nama_baru;

        // Pindah file ke folder
        if (move_uploaded_file($tmp_file, $lokasi_simpan)) {
            // Update nama file baru ke database
            $query = "UPDATE lomba SET file_berkas = '$nama_baru' WHERE lomba_id = $lomba_id";
            mysqli_query($conn, $query);
        }
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>