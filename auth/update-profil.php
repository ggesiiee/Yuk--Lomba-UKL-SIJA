<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

$user_id_aktif = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $kelas   = mysqli_real_escape_string($conn, $_POST['kelas']);
    $sekolah = mysqli_real_escape_string($conn, $_POST['sekolah']);

    $query_update_users = "UPDATE users SET nama = '$nama', email = '$email' WHERE user_id = $user_id_aktif";
    $run_users = mysqli_query($conn, $query_update_users);

    $cek_profil = mysqli_query($conn, "SELECT profil_id FROM profil WHERE user_id = $user_id_aktif");
    
    if (mysqli_num_rows($cek_profil) > 0) {
        $query_profil = "UPDATE profil SET nama = '$nama', kelas = '$kelas', sekolah = '$sekolah' WHERE user_id = $user_id_aktif";
    } else {
        $query_profil = "INSERT INTO profil (user_id, nama, kelas, sekolah) VALUES ($user_id_aktif, '$nama', '$kelas', '$sekolah')";
    }
    
    $run_profil = mysqli_query($conn, $query_profil);

    if ($run_users && $run_profil) {
        echo "<script>
                alert('Yey! Profil kamu berhasil diperbarui.');
                window.location.href = '../user/profil.php'; 
              </script>";
    } else {
        echo "<script>
                alert('Waduh, gagal menyimpan: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
    }

} else {
    header("Location: ../user/home.php");
    exit();
}
?>