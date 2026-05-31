<?php
require_once '../include/session.php';
require_once '../include/koneksi.php'; 

if (isset($_POST['id']) && isset($_POST['status'])) {
    $note_id = intval($_POST['id']);
    $status = intval($_POST['status']);

    // Jika selesai (1) catat tanggal hari ini, jika batal selesai (0) kosongkan tanggal
    if ($status == 1) {
        $query = "UPDATE notes SET status = 1, tanggal_selesai = CURDATE() WHERE note_id = $note_id";
    } else {
        $query = "UPDATE notes SET status = 0, tanggal_selesai = NULL WHERE note_id = $note_id";
    }
    
    if (mysqli_query($conn, $query)) {
        echo "Sukses update data";
    } else {
        echo "Gagal database: " . mysqli_error($conn);
    }
} else {
    echo "Gagal: Data ID atau Status tidak dikirim oleh JavaScript.";
}
?>