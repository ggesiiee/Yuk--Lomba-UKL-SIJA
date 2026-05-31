<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $status = intval($_POST['status']);
    
    $presentase = ($status == 1) ? 100 : 0;

    $query = "UPDATE progress SET presentase = $presentase WHERE progress_id = $id";
    
    if (mysqli_query($conn, $query)) {
        echo "Berhasil";
    } else {
        http_response_code(500);
        echo "Gagal: " . mysqli_error($conn);
    }
}
?>