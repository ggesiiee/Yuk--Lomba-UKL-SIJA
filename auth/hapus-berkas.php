<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if (isset($_GET['id'])) {
    $lomba_id = intval($_GET['id']);

    $q_file = mysqli_query($conn, "SELECT juknis FROM lomba WHERE lomba_id = $lomba_id");
    
    if ($q_file && mysqli_num_rows($q_file) > 0) {
        $dt = mysqli_fetch_assoc($q_file);
        $file_lama = $dt['juknis'];

        if (!empty($file_lama) && file_exists("../assets/uploads/" . $file_lama)) {
            unlink("../assets/uploads/" . $file_lama);
        }

        mysqli_query($conn, "UPDATE lomba SET juknis = NULL WHERE lomba_id = $lomba_id");
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>