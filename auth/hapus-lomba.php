<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

// Pastikan ada parameter ID yang dikirim
if (isset($_GET['id'])) {
    $lomba_id = intval($_GET['id']);

    // ==========================================
    // --- AWAL GEMBOK KEDUA (CEK HAK AKSES) ---
    // ==========================================
    $role_aktif = $_SESSION['role'] ?? 'user';
    
    // Cek siapa pemilik (pembuat) lomba ini di database
    $cek_lomba = mysqli_query($conn, "SELECT user_id FROM lomba WHERE lomba_id = $lomba_id");
    
    if (mysqli_num_rows($cek_lomba) > 0) {
        $data_lomba = mysqli_fetch_assoc($cek_lomba);
        $pemilik_lomba = $data_lomba['user_id'];
        
        // Validasi: Jika yang login BUKAN guru, BUKAN admin, DAN BUKAN pembuat lomba tersebut, maka TOLAK!
        // (Asumsi variabel ID user yang sedang login adalah $user_id_aktif dari file session.php)
        if ($role_aktif != 'guru' && $role_aktif != 'admin' && $pemilik_lomba != $user_id_aktif) {
            echo "<script>
                    alert('Akses Ditolak! Anda tidak memiliki izin untuk menghapus lomba ini.');
                    window.location.href = '../user/lomba.php';
                  </script>";
            exit(); // Sangat penting: Hentikan script di sini agar blok DELETE di bawah tidak tereksekusi
        }
    } else {
        // Jika ID lombanya dicari tidak ada di database
        echo "<script>
                alert('Data lomba tidak ditemukan!');
                window.location.href = '../user/lomba.php';
              </script>";
        exit();
    }
    // ==========================================
    // --- AKHIR GEMBOK KEDUA ---
    // ==========================================


    // Mulai transaksi untuk keamanan data (HANYA BERJALAN JIKA LOLOS PENGECEKAN DI ATAS)
    mysqli_begin_transaction($conn);

    try {
        // 1. Hapus data di tabel progress yang terkait dengan lomba ini (kalau ada)
        $q_progress = "DELETE FROM progress WHERE lomba_id = $lomba_id";
        mysqli_query($conn, $q_progress);

        // 2. Hapus data di tabel pendaftaran yang terkait dengan lomba ini (kalau ada)
        $q_daftar = "DELETE FROM pendaftaran WHERE lomba_id = $lomba_id";
        mysqli_query($conn, $q_daftar);

        // 3. Terakhir, baru hapus data lombanya
        $q_lomba = "DELETE FROM lomba WHERE lomba_id = $lomba_id";
        mysqli_query($conn, $q_lomba);

        // Jika semua sukses, simpan perubahan
        mysqli_commit($conn);

        // Redirect kembali dengan pesan sukses
        echo "<script>
                alert('Lomba berhasil dihapus!');
                window.location.href = '../user/lomba.php';
              </script>";
    } catch (Exception $e) {
        // Jika ada yang error, batalkan semua penghapusan
        mysqli_rollback($conn);
        echo "<script>
                alert('Gagal menghapus lomba. Error: " . $e->getMessage() . "');
                window.location.href = '../user/lomba.php';
              </script>";
    }
} else {
    // Jika tidak ada ID
    header("Location: ../user/lomba.php");
    exit();
}
?>