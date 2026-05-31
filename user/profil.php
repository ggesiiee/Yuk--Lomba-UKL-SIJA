<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_id_aktif = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $kelas   = mysqli_real_escape_string($conn, $_POST['kelas']);
    $sekolah = mysqli_real_escape_string($conn, $_POST['sekolah']);

    mysqli_query($conn, "UPDATE users SET email = '$email', nama = '$nama' WHERE user_id = $user_id_aktif");

    $cek_profil = mysqli_query($conn, "SELECT * FROM profil WHERE user_id = $user_id_aktif");
    $profil_ada = mysqli_num_rows($cek_profil) > 0;

    $foto_update_sql = "";
    
    if (isset($_FILES['foto_baru']) && $_FILES['foto_baru']['error'] == 0) {
        $foto_tmp = $_FILES['foto_baru']['tmp_name'];
        $foto_nama_asli = $_FILES['foto_baru']['name'];
        $ext = pathinfo($foto_nama_asli, PATHINFO_EXTENSION);
        
        $foto_baru = "profil_" . $user_id_aktif . "_" . time() . "." . $ext;
        
        $folder_tujuan = "../assets/img/" . $foto_baru;

        if (move_uploaded_file($foto_tmp, $folder_tujuan)) {
            $foto_update_sql = ", foto = '$foto_baru'";
        }
    }

    if ($profil_ada) {
        $query_profil = "UPDATE profil SET nama = '$nama', kelas = '$kelas', sekolah = '$sekolah' $foto_update_sql WHERE user_id = $user_id_aktif";
    } else {
        $foto_insert = ($foto_update_sql != "") ? $foto_baru : "profil.jpeg";
        $query_profil = "INSERT INTO profil (user_id, nama, kelas, sekolah, foto) VALUES ($user_id_aktif, '$nama', '$kelas', '$sekolah', '$foto_insert')";
    }
    
    mysqli_query($conn, $query_profil);

    header("Location: profil.php?status=sukses");
    exit;
}

$query = "SELECT u.nama as nama_akun, u.email, p.nama as nama_profil, p.kelas, p.sekolah, p.foto 
          FROM users u 
          LEFT JOIN profil p ON u.user_id = p.user_id 
          WHERE u.user_id = $user_id_aktif";
$q_user = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($q_user);

if(!$user) {
    $user = [
        'nama_akun' => 'Nama User',
        'email' => 'user@email.com',
        'kelas' => '',
        'sekolah' => 'Belum diatur',
        'foto' => '../assets/img/photo-default.jpg'
    ];
}

$nama_tampil = !empty($user['nama_profil']) ? $user['nama_profil'] : $user['nama_akun'];
$foto_tampil = !empty($user['foto']) ? $user['foto'] : 'photo-default.jpg';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya - Yuk! Lomba</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  
  <link rel="stylesheet" href="../assets/css/home-user.css">
  <link rel="stylesheet" href="../assets/css/style-profil.css">
</head>
<body>
  <div class="bungkus-semua">
    
    <aside class="menu-samping-kiri">
      <div class="logo-webnya">
        <img src="../assets/img/logoo.svg" alt="Logo">
        <a href="home.php" class="tulisan-logo">Yuk! Lomba</a>
      </div>

      <div class="grup-kumpulan-menu">
        <p class="judul-kategori-menu">HOME</p>
        <a href="home.php" class="link-menu-biasa"><i data-feather="home"></i> Dashboard</a>
        
        <a href="lomba.php" class="link-menu-biasa">
          <?php
            $q_lomba_aktif = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE user_id = $user_id_aktif AND status = 'aktif'");
            $jml_lomba_aktif = ($q_lomba_aktif && mysqli_num_rows($q_lomba_aktif) > 0) ? mysqli_fetch_assoc($q_lomba_aktif)['total'] : 0;
          ?>
          <i data-feather="file-text"></i> Lomba <span class="buletan-angka"><?= $jml_lomba_aktif ?: 0 ?></span>
        </a>
        <p class="judul-kategori-menu" style="margin-top: 20px;">AKUN</p>
        <a href="profil.php" class="link-menu-biasa menu-nyala"><i data-feather="user"></i> Profil Saya</a>
        <a href="../auth/logout.php" class="link-menu-biasa menu-keluar"><i data-feather="log-out"></i> Keluar</a>
      </div> 
    </aside>

    <main class="isi-konten-kanan">
        <header class="header-konten margin-bawah-20">
            <div class="header-pengaturan-akun">
                <h1>Pengaturan Akun</h1>
            </div>
        </header>

        <section class="kotak-putih-biasa kotak-profil-utama">
            
            <div class="konten-profil-utama">
                <form action="" method="POST" enctype="multipart/form-data" style="width: 100%;">
                    
                    <div class="info-atas-profil">
                            <div style="position: relative; display: inline-block;">
                                <img src="../assets/img/<?= htmlspecialchars($foto_tampil) ?>" alt="Foto Profil" class="foto-profil-besar" id="preview-foto">
                                
                                <input type="file" id="foto_baru" name="foto_baru" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="previewImage(event)">
                                
                                <button type="button" onclick="document.getElementById('foto_baru').click();" 
                                        style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); background: #1c7fff; color: white; border: none; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; cursor: pointer; white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    Ubah Foto
                                </button>
                            </div>

                            <div class="teks-nama-profil" style="margin-left: 20px;">
                                <h2><?= htmlspecialchars($nama_tampil) ?></h2>
                            </div>
                    </div>

                    <div class="layout-profil-grid">
                        <div class="kolom-edit-profil">
                            <h3 class="judul-detail-informasi">Detail Informasi</h3>
                            
                            <div class="form-grup-profil">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="input-profil" value="<?= htmlspecialchars($nama_tampil ?? '') ?>" required>
                            </div>
                            
                            <div class="form-grup-profil">
                                <label>Alamat Email</label>
                                <input type="email" name="email" class="input-profil" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>

                            <div class="form-grup-profil">
                                <label>Kelas</label>
                                <input type="text" name="kelas" class="input-profil" value="<?= htmlspecialchars($user['kelas'] ?? '') ?>" placeholder="Contoh: XII RPL 1">
                            </div>

                            <div class="form-grup-profil">
                                <label>Asal Sekolah</label>
                                <input type="text" name="sekolah" class="input-profil" value="<?= htmlspecialchars($user['sekolah'] ?? '') ?>" placeholder="Contoh: SMKN 1 Surabaya">
                            </div>

                            <button type="submit" class="btn-simpan-profil">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>
  </div>

  <script>
  // Fungsi untuk menampilkan preview foto profil
  function previewImage(event) {
    const file = event.target.files;
    
    if (file) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
        // Mencari elemen gambar (pastikan class gambar profilmu adalah 'foto-profil')
        const imgPreview = document.querySelector('.preview-foto');
        
        // Mengganti sumber gambar (src) dengan file yang baru dipilih
        if (imgPreview) {
          imgPreview.src = e.target.result;
        }
      }
      
      reader.readAsDataURL(file);
    }
  }

  // Menjalankan Icon Feather (biarkan bawaan aslimu tetap ada)
  feather.replace();
</script>
</body>
</html>