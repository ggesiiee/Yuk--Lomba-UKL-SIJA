<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if (isset($_GET['id'])) {
    $lomba_id = intval($_GET['id']);

    $role_aktif = $_SESSION['role'] ?? 'user';
    
    $cek_lomba = mysqli_query($conn, "SELECT user_id FROM lomba WHERE lomba_id = $lomba_id");
    
    if (mysqli_num_rows($cek_lomba) > 0) {
        $data_lomba = mysqli_fetch_assoc($cek_lomba);
        $pemilik_lomba = $data_lomba['user_id'];
        
        if ($role_aktif != 'guru' && $role_aktif != 'admin' && $pemilik_lomba != $user_id_aktif) {
            echo "<script>
                    alert('Akses Ditolak! Anda tidak memiliki izin untuk menghapus lomba ini.');
                    window.location.href = '../user/lomba.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Data lomba tidak ditemukan!');
                window.location.href = '../user/lomba.php';
              </script>";
        exit();
    }
    mysqli_begin_transaction($conn);

    try {
        $q_progress = "DELETE FROM progress WHERE lomba_id = $lomba_id";
        mysqli_query($conn, $q_progress);

        $q_daftar = "DELETE FROM pendaftaran WHERE lomba_id = $lomba_id";
        mysqli_query($conn, $q_daftar);

        $q_lomba = "DELETE FROM lomba WHERE lomba_id = $lomba_id";
        mysqli_query($conn, $q_lomba);

        mysqli_commit($conn);

        echo "<script>
                alert('Lomba berhasil dihapus!');
                window.location.href = '../user/lomba.php';
              </script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>
                alert('Gagal menghapus lomba. Error: " . $e->getMessage() . "');
                window.location.href = '../user/lomba.php';
              </script>";
    }
} else {
    header("Location: ../user/lomba.php");
    exit();
}
?>