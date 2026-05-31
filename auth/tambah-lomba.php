<?php
require_once '../include/session.php';
require_once '../include/koneksi.php'; 

$pesan = "";

if (isset($_POST['simpan'])) {
    $nama_lomba           = mysqli_real_escape_string($conn, $_POST['nama_lomba']);
    $kategori_id          = (int)($_POST['kategori_id'] ?? 0);
    $tingkat              = mysqli_real_escape_string($conn, $_POST['tingkat'] ?? '');
    $status_daftar        = mysqli_real_escape_string($conn, $_POST['status_daftar'] ?? 'belum'); 
    $biaya_pendaftaran    = (int)($_POST['biaya_pendaftaran'] ?? 0);
    $deskripsi            = mysqli_real_escape_string($conn, $_POST['deskripsi'] ?? '');
    
    $deadline_pendaftaran = !empty($_POST['deadline_pendaftaran']) ? "'" . mysqli_real_escape_string($conn, $_POST['deadline_pendaftaran']) . "'" : "NULL";
    $deadline       = !empty($_POST['deadline']) ? "'" . mysqli_real_escape_string($conn, $_POST['deadline']) . "'" : "NULL";
    
    $juknis = ""; 
    
    if (isset($_FILES['juknis']) && $_FILES['juknis']['error'] === UPLOAD_ERR_OK) {
        $nama_file_asli = $_FILES['juknis']['name'];
        $tmp_file = $_FILES['juknis']['tmp_name'];
        
        $ekstensi_diizinkan = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];
        $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
        
        if (in_array($ekstensi_file, $ekstensi_diizinkan)) {
            $juknis = uniqid('juknis_') . '.' . $ekstensi_file;
            $folder_tujuan = '../assets/uploads/file/';
            
            if (!is_dir($folder_tujuan)) {
                mkdir($folder_tujuan, 0777, true);
            }
            
            move_uploaded_file($tmp_file, $folder_tujuan . $juknis);
        } else {
            echo "<script>alert('Gagal! Format file Juknis harus PDF, DOC, PNG, atau JPG.'); window.history.back();</script>";
            exit;
        }
    }

    $query_insert = "INSERT INTO lomba (
                        user_id, 
                        nama_lomba, 
                        kategori_id, 
                        tingkat, 
                        deadline_pendaftaran, 
                        deadline, 
                        status_daftar, 
                        biaya_pendaftaran, 
                        deskripsi, 
                        juknis
                     ) VALUES (
                        '$user_id_aktif', 
                        '$nama_lomba', 
                        '$kategori_id', 
                        '$tingkat', 
                        $deadline_pendaftaran, 
                        $deadline, 
                        '$status_daftar', 
                        '$biaya_pendaftaran', 
                        '$deskripsi', 
                        '$juknis'
                     )";

    if (mysqli_query($conn, $query_insert)) {
        echo "<script>alert('Lomba berhasil ditambahkan!'); window.location.href='../user/lomba.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan data ke database!'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Lomba - Yuk! Lomba</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
  
  <link rel="stylesheet" href="../assets/css/home-user.css">
  <link rel="stylesheet" href="../assets/css/tambah-lomba.css">
</head>
<body>
  <div class="bungkus-semua">
    
    <aside class="menu-samping-kiri">
      <div class="logo-webnya">
        <img src="/project_ukl/assets/img/logoo.svg" alt="Logo">
        <a href="../user/index.php" class="tulisan-logo">Yuk! Lomba</a>
      </div>
      <div class="grup-kumpulan-menu">
        <p class="judul-kategori-menu">HOME</p>
        <a href="../user/home.php" class="link-menu-biasa"><i data-feather="home"></i> <span class="teks-menu">Dashboard</span></a>
        <a href="../user/lomba.php" class="link-menu-biasa menu-nyala"><i data-feather="file-text"></i> <span class="teks-menu">Lomba</span></a>
      </div>
    </aside>

    <main class="isi-konten-kanan">
      
      <header class="bagian-atas-header header-detail-spesifik">
        <div class="wrapper-judul-kiri">
          <a href="../user/home.php" class="tombol-kembali"><i data-feather="arrow-left"></i></a>
          <h1 class="judul-halaman-teks">Tambah Lomba Baru</h1>
        </div>
      </header>

      <section class="area-form">
        <?= $pesan; ?>

        <div class="kartu-form">
          <form id="formTambahLomba" action="" method="POST" enctype="multipart/form-data" novalidate>
            
            <div class="input-grup" style="margin-bottom: 20px;">
              <label>Nama Lomba</label>
              <input type="text" name="nama_lomba" placeholder="Masukkan nama lomba..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1;" required>
            </div>

            <div class="dua-kolom">
              <div class="input-grup">
                <label>Kategori Lomba</label>
                <select name="kategori_id" required>
                  <option value="" disabled selected>Pilih Kategori...</option>
                  <?php
                    $q_kat = mysqli_query($conn, "SELECT * FROM kategori");
                    while($k = mysqli_fetch_assoc($q_kat)) {
                        echo "<option value='{$k['kategori_id']}'>{$k['nama_kategori']}</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="input-grup">
                <label>Tingkat Lomba</label>
                <select name="tingkat" required>
                  <option value="" disabled selected>-- Pilih Tingkat --</option>
                  <option value="Sekolah">Sekolah</option>
                  <option value="Kota/Kabupaten">Kota/Kabupaten</option>
                  <option value="Provinsi">Provinsi</option>
                  <option value="Nasional">Nasional</option>
                  <option value="Internasional">Internasional</option>
                </select>
              </div>
            </div>

            <div class="dua-kolom">
              <div class="input-grup">
                <label>Deadline Pendaftaran</label>
                <input type="date" name="deadline_pendaftaran" required>
              </div>
              <div class="input-grup">
                <label>Deadline Tahap 1</label>
                <input type="date" name="deadline" required>
              </div>
            </div>

            <div class="dua-kolom">
              <div class="input-grup">
                <label>Biaya Pendaftaran (Rp)</label>
                <input type="number" name="biaya_pendaftaran" placeholder="0 jika gratis" required>
              </div>
              <div class="input-grup">
                <label>Apakah Sudah Mendaftar?</label>
                <div class="opsi-radio">
                  <label class="radio-label"><input type="radio" name="status_daftar" value="sudah" required> Sudah</label>
                  <label class="radio-label"><input type="radio" name="status_daftar" value="belum" required> Belum</label>
                </div>
              </div>
            </div>

            <div class="input-grup">
              <label>File Juknis (Opsional)</label>
              <div class="custom-file-upload">
                <input type="file" name="juknis" id="juknis" accept=".pdf,.doc,.docx,.png,.jpg">
                <label for="juknis" class="area-drop">
                  <i data-feather="upload-cloud"></i>
                  <span>Klik untuk pilih file atau drag ke sini</span>
                </label>
              </div>
            </div>

            <div class="input-grup">
              <label>Deskripsi Singkat Lomba</label>
              <textarea name="deskripsi" rows="4" placeholder="Jelaskan detail lomba, syarat, dsb..."></textarea>
            </div>

            <div class="baris-tombol">
              <button type="button" class="btn-batal" onclick="handleBatal()">Batal</button>
              <button type="submit" name="simpan" class="btn-simpan">Simpan Lomba <i data-feather="check"></i></button>
            </div>

          </form>
        </div>
      </section>

    </main>
  </div>

  <script>
    feather.replace();
  </script>
  <script src="../assets/js/tambah-lomba.js"></script>
</body>
</html>