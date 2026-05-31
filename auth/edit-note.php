<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if (isset($_POST['update_note'])) {
    $note_id = intval($_POST['note_id']);
    $isi_note = mysqli_real_escape_string($conn, $_POST['isi_note']);

    // Update data di database berdasarkan note_id
    $query = "UPDATE notes SET isi_note = '$isi_note' WHERE note_id = $note_id";

    if (mysqli_query($conn, $query)) {
        // Jika berhasil, kembalikan ke halaman dashboard user
        header("Location: ../user/home.php?pesan=edit_berhasil");
        exit();
    } else {
        echo "Gagal memperbarui catatan: " . mysqli_error($conn);
    }
} else {
    header("Location: ../user/home.php");
    exit();
}
?>