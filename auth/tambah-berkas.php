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
            
            // PERHATIKAN BARIS INI: Kita coba update database
            $query = "UPDATE lomba SET juknis = '$nama_baru' WHERE lomba_id = $lomba_id";
            
            if (mysqli_query($conn, $query)) {
                // Jika sukses, kembali ke halaman awal
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            } else {
                // JIKA GAGAL, TAMPILKAN PESAN ERROR DATABASE-NYA!
                echo "<div style='font-family: sans-serif; padding: 20px;'>";
                echo "<h3 style='color: red;'>❌ Gagal Menyimpan ke Database!</h3>";
                echo "<b>Pesan Error dari MySQL:</b> " . mysqli_error($conn) . "<br><br>";
                echo "<b>Query yang dicoba:</b> <code>" . $query . "</code><br><br>";
                echo "💡 <b>Solusi:</b> Coba cek phpMyAdmin kamu, di tabel <b>lomba</b>, apa nama kolom paling pertamanya (Primary Key)? Apakah <b>lomba_id</b> atau <b>id_lomba</b>?<br><br>";
                echo "<a href='javascript:history.back()'>Kembali</a>";
                echo "</div>";
                exit(); // Hentikan eksekusi agar tidak nge-refresh
            }
        }
    }
}
?>