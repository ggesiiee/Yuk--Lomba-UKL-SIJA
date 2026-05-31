<?php
session_start();
require_once '../include/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../auth/login.php");
    exit();
}
$user_id_aktif = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['batal_pelacakan'])) {
    $id_record = (int)$_POST['id_record'];
    $tabel_sumber = $_POST['tabel_sumber'];
    
    if ($tabel_sumber == 'pendaftaran') {
        mysqli_query($conn, "UPDATE pendaftaran SET status = 'pending' WHERE pendaftaran_id = '$id_record'");
    } else if ($tabel_sumber == 'klaim_prestasi') {
        mysqli_query($conn, "UPDATE klaim_prestasi SET status_validasi = 'Menunggu Pengecekan' WHERE klaim_id = '$id_record'");
    }
    echo "<script>alert('Status aktivitas berhasil dibatalkan!'); window.location.href='home.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_pelacakan'])) {
    $id_record = (int)$_POST['id_record'];
    $tabel_sumber = $_POST['tabel_sumber'];
    
    if ($tabel_sumber == 'pendaftaran') {
        mysqli_query($conn, "DELETE FROM pendaftaran WHERE pendaftaran_id = '$id_record'");
    } else if ($tabel_sumber == 'klaim_prestasi') {
        mysqli_query($conn, "DELETE FROM klaim_prestasi WHERE klaim_id = '$id_record'");
    }
    echo "<script>alert('Riwayat aktivitas berhasil dihapus permanen!'); window.location.href='home.php';</script>";
    exit();
}

$bulan_sekarang = date('m');
$tahun_sekarang = date('Y');

$q_lomba_aktif = mysqli_query($conn, "SELECT COUNT(*) as total FROM lomba WHERE MONTH(deadline_pendaftaran) = '$bulan_sekarang' AND YEAR(deadline_pendaftaran) = '$tahun_sekarang'");
$data_lomba_aktif = mysqli_fetch_assoc($q_lomba_aktif);
$jumlah_lomba_aktif = $data_lomba_aktif['total'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_lomba'])) {
    $nama_lomba = mysqli_real_escape_string($conn, $_POST['nama_lomba']);
    $kategori_id = mysqli_real_escape_string($conn, $_POST['kategori_id']);
    $tingkat = mysqli_real_escape_string($conn, $_POST['tingkat']);
    $biaya_pendaftaran = mysqli_real_escape_string($conn, $_POST['biaya_pendaftaran']);
    $deadline_pendaftaran = mysqli_real_escape_string($conn, $_POST['deadline_pendaftaran']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $link_lomba = mysqli_real_escape_string($conn, $_POST['link_lomba']);
    
    $poster_db = ""; 
    if (!empty($_FILES['poster']['name'])) {
        $poster_unik = time() . "_" . $_FILES['poster']['name'];
        if (move_uploaded_file($_FILES['poster']['tmp_name'], "../assets/uploads/img/" . $poster_unik)) {
            $poster_db = $poster_unik;
        }
    }
    
    $juknis_db = "";
    if (isset($_FILES['juknis']) && $_FILES['juknis']['error'] == 0) {
        $ext = pathinfo($_FILES['juknis']['name'], PATHINFO_EXTENSION);
        $juknis_unik = "juknis_" . time() . "." . $ext;
        if (move_uploaded_file($_FILES['juknis']['tmp_name'], "../assets/uploads/file/" . $juknis_unik)) {
            $juknis_db = $juknis_unik;
        }
    }

    $query_tambah = "INSERT INTO lomba (user_id, nama_lomba, kategori_id, tingkat, biaya_pendaftaran, deadline_pendaftaran, deadline, deskripsi, juknis, status_daftar, poster, link_lomba) 
                    VALUES ('$user_id_aktif', '$nama_lomba', '$kategori_id', '$tingkat', '$biaya_pendaftaran', '$deadline_pendaftaran', '$deadline', '$deskripsi', '$juknis_db', 'belum', '$poster_db', '$link_lomba')";

    mysqli_query($conn, $query_tambah);
    echo "<script>alert('Berhasil menambahkan lomba!'); window.location.href='home.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi_validasi'])) {
    $klaim_id = intval($_POST['klaim_id']);
    $status_baru = $_POST['status_baru']; 
    
    $q_detail = mysqli_query($conn, "SELECT * FROM klaim_prestasi WHERE klaim_id = $klaim_id");
    if (mysqli_num_rows($q_detail) > 0) {
        $d_klaim = mysqli_fetch_assoc($q_detail);
        $user_id_siswa = $d_klaim['user_id'];
        $lomba_id_siswa = $d_klaim['lomba_id'];
        $tipe_lomba = $d_klaim['tipe_lomba'];
        $hasil_klaim = $d_klaim['hasil_klaim'];
        
        $update_klaim = mysqli_query($conn, "UPDATE klaim_prestasi SET status_validasi = '$status_baru' WHERE klaim_id = $klaim_id");
        
        if ($update_klaim && $status_baru === 'Disetujui') {
            if ($tipe_lomba === 'mandiri') {
                mysqli_query($conn, "UPDATE lomba SET hasil_akhir = '$hasil_klaim' WHERE lomba_id = $lomba_id_siswa AND user_id = '$user_id_siswa'");
            } else if ($tipe_lomba === 'sekolah') {
                mysqli_query($conn, "UPDATE pendaftaran SET status = '$hasil_klaim' WHERE lomba_id = $lomba_id_siswa AND user_id = '$user_id_siswa'");
            }
        }
        
        echo "<script>alert('Klaim prestasi berhasil di-".strtolower($status_baru)."!'); window.location.href='home.php';</script>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_lomba'])) {
    $id_lomba = mysqli_real_escape_string($conn, $_POST['id_lomba']);
    $nama_lomba = mysqli_real_escape_string($conn, $_POST['nama_lomba']);
    $kategori_id = mysqli_real_escape_string($conn, $_POST['kategori_id']);
    $tingkat = mysqli_real_escape_string($conn, $_POST['tingkat']);
    $biaya_pendaftaran = mysqli_real_escape_string($conn, $_POST['biaya_pendaftaran']);
    $deadline_pendaftaran = mysqli_real_escape_string($conn, $_POST['deadline_pendaftaran']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $link_lomba = mysqli_real_escape_string($conn, $_POST['link_lomba']);

    $q_lama = mysqli_query($conn, "SELECT poster, juknis FROM lomba WHERE lomba_id = '$id_lomba' AND user_id = '$user_id_aktif'");
    $d_lama = mysqli_fetch_assoc($q_lama);

    $query_update = "UPDATE lomba SET nama_lomba='$nama_lomba', kategori_id='$kategori_id', tingkat='$tingkat', biaya_pendaftaran='$biaya_pendaftaran', deadline_pendaftaran='$deadline_pendaftaran', deadline='$deadline', deskripsi='$deskripsi', link_lomba='$link_lomba'";

    if (!empty($_FILES['poster']['name'])) {
        if (!empty($d_lama['poster']) && file_exists("../assets/uploads/img/" . $d_lama['poster'])) {
            unlink("../assets/uploads/img/" . $d_lama['poster']);
        }
        $poster_unik = time() . "_" . $_FILES['poster']['name'];
        if (move_uploaded_file($_FILES['poster']['tmp_name'], "../assets/uploads/img/" . $poster_unik)) {
            $query_update .= ", poster='$poster_unik'";
        }
    }

    if (isset($_POST['hapus_juknis_lama']) && $_POST['hapus_juknis_lama'] == '1') {
        if (!empty($d_lama['juknis']) && file_exists("../assets/uploads/file/" . $d_lama['juknis'])) {
            unlink("../assets/uploads/file/" . $d_lama['juknis']); 
        }
        $query_update .= ", juknis=''";
    } else if (isset($_FILES['juknis']) && $_FILES['juknis']['error'] == 0) {
        if (!empty($d_lama['juknis']) && file_exists("../assets/uploads/file/" . $d_lama['juknis'])) {
            unlink("../assets/uploads/file/" . $d_lama['juknis']);
        }
        $ext = pathinfo($_FILES['juknis']['name'], PATHINFO_EXTENSION);
        $nama_file_juknis = "juknis_" . time() . "." . $ext;
        if (move_uploaded_file($_FILES['juknis']['tmp_name'], "../assets/uploads/file/" . $nama_file_juknis)) {
            $query_update .= ", juknis='$nama_file_juknis'";
        }
    }

    $query_update .= " WHERE lomba_id='$id_lomba' AND user_id='$user_id_aktif'";

    mysqli_query($conn, $query_update);
    echo "<script>alert('Berhasil mengubah lomba!'); window.location.href='home.php';</script>";
    exit();
}

if (isset($_GET['hapus_lomba'])) {
    $id_hapus = (int)$_GET['hapus_lomba'];
    
    $q_lama = mysqli_query($conn, "SELECT poster, juknis FROM lomba WHERE lomba_id = '$id_hapus' AND user_id = '$user_id_aktif'");
    if ($row = mysqli_fetch_assoc($q_lama)) {
        if (!empty($row['poster']) && file_exists("../assets/uploads/img/" . $row['poster'])) {
            unlink("../assets/uploads/img/" . $row['poster']);
        }
        if (!empty($row['juknis']) && file_exists("../assets/uploads/file/" . $row['juknis'])) {
            unlink("../assets/uploads/file/" . $row['juknis']);
        }
        mysqli_query($conn, "DELETE FROM pendaftaran WHERE lomba_id = '$id_hapus'");
        mysqli_query($conn, "DELETE FROM lomba WHERE lomba_id = '$id_hapus' AND user_id = '$user_id_aktif'");
        echo "<script>alert('Lomba dan file terkait berhasil dihapus!'); window.location.href='home.php';</script>";
    }
    exit();
}

if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    if (!empty($nama_kategori)) {
        $insert = mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')");
        if ($insert) {
            echo "<script>alert('Kategori berhasil ditambahkan!'); window.location.href='home.php';</script>";
            exit();
        }
    }
}

if (isset($_GET['hapus_kategori'])) {
    $id_hapus = intval($_GET['hapus_kategori']);
    $cek_lomba = mysqli_query($conn, "SELECT COUNT(*) as jml FROM lomba WHERE kategori_id = $id_hapus");
    $data_cek = mysqli_fetch_assoc($cek_lomba);
    
    if ($data_cek['jml'] > 0) {
        echo "<script>alert('Gagal menghapus! Kategori ini sedang digunakan oleh beberapa lomba.'); window.location.href='home.php';</script>";
        exit();
    } else {
        $delete = mysqli_query($conn, "DELETE FROM kategori WHERE kategori_id = $id_hapus");
        if ($delete) {
            echo "<script>alert('Kategori berhasil dihapus!'); window.location.href='home.php';</script>";
            exit();
        }
    }
}

$q_lomba_sekolah = mysqli_query($conn, "SELECT COUNT(*) as total FROM lomba JOIN users ON lomba.user_id = users.user_id WHERE users.role = 'guru' OR users.role = 'admin'");
$total_lomba_sekolah = mysqli_fetch_assoc($q_lomba_sekolah)['total'];

$q_siswa_aktif = mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as total FROM pendaftaran WHERE status = 'aktif'");
$total_siswa_aktif = mysqli_fetch_assoc($q_siswa_aktif)['total'];

$q_prestasi = mysqli_query($conn, "SELECT COUNT(*) as total FROM lomba WHERE hasil_akhir NOT IN ('Menunggu Pengumuman', 'Apresiasi / Peserta', 'Peserta', 'batal Ikut', 'Menunggu Pengecekan') AND hasil_akhir IS NOT NULL AND hasil_akhir != ''");
$total_prestasi = mysqli_fetch_assoc($q_prestasi)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - Yuk! Lomba</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="../assets/css/home-admin.css"> 
</head>
<body>

  <nav class="navbar">    
    <div class="admin-badge">
      <i data-feather="check-circle" style="width:14px;"></i> Mode Administrator / Guru
    </div>
    <div class="wrapper">
      <button onclick="document.getElementById('modalTambahLomba').style.display='flex'" class="menu-tambah">
        <i data-feather="plus" style="width:16px;"></i> Tambah Lomba
      </button>
      <a href="../auth/logout.php" class="menu-keluar" onclick="return confirm('Yakin ingin keluar?')">Keluar</a>
    </div>
  </nav>

  <div class="top-highlight-grid">
    <div class="card-premium banner-dashboard">
      <div class="banner-left">
        <h3>Selamat Datang di Dashboard,</h3>
        <h2 class="title-welcome"><?= htmlspecialchars($_SESSION['nama']) ?></h2>
        <div class="footer-text date-wrapper">
          <i data-feather="calendar" class="icon-small"></i> 
          <?= date('d M Y') ?>
        </div>
      </div>
      <div class="banner-right stats-glass-box">
        <div class="stats-text">
          <span class="stats-title">Total Lomba Aktif</span>
          <span class="stats-subtitle">Bulan Ini</span>
        </div>
        <div class="stats-number-wrapper">
          <div class="icon-circle">
            <i data-feather="activity" class="icon-large"></i>
          </div>
          <span class="angka-lomba"><?= $jumlah_lomba_aktif ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="lomba-wrapper-card">
    <h3>Daftar Lomba yang Anda Kelola</h3>
    <div class="lomba-grid">
      <?php
      $q_tampil_lomba = mysqli_query($conn, "SELECT l.*, k.nama_kategori FROM lomba l LEFT JOIN kategori k ON l.kategori_id = k.kategori_id WHERE l.user_id = '$user_id_aktif' ORDER BY l.lomba_id DESC");
      if (mysqli_num_rows($q_tampil_lomba) == 0) {
          echo "<div class='lomba-empty-state'>Belum ada lomba yang Anda buat. Silakan tambah lomba baru.</div>";
      }
      while ($row_lomba = mysqli_fetch_assoc($q_tampil_lomba)) {
          $poster_src = !empty($row_lomba['poster']) ? "../assets/uploads/img/" . $row_lomba['poster'] : "../assets/uploads/img/default-poster.jpeg"; 
          $biaya_display = $row_lomba['biaya_pendaftaran'] == 0 ? "Gratis" : "Rp " . number_format($row_lomba['biaya_pendaftaran'], 0, ',', '.');
      ?>
          <div class="lomba-card">
            <div class="lomba-poster-wrapper">
              <a href="<?= $poster_src ?>" target="_blank" title="Klik untuk memperbesar gambar">
                <img src="<?= $poster_src ?>" alt="Poster Lomba" class="lomba-poster-img" style="cursor: zoom-in;">
              </a>
              <span class="lomba-badge-kategori"><?= htmlspecialchars($row_lomba['nama_kategori']) ?></span>
            </div>
            <div class="lomba-content">
              <div>
                <h4 class="lomba-nama"><?= htmlspecialchars($row_lomba['nama_lomba']) ?></h4>
                <p class="lomba-deskripsi"><?= !empty($row_lomba['deskripsi']) ? htmlspecialchars($row_lomba['deskripsi']) : 'Tidak ada deskripsi singkat.' ?></p>
                <a href="<?= htmlspecialchars($row_lomba['link_lomba']) ?>" target="_blank" class="btn-link-lomba" style="display: inline-flex; align-items: center; gap: 5px; margin-top: 10px; color: #1c7fff; text-decoration: none; font-weight: bold; font-size: 0.9rem;">
                  <i data-feather="external-link" style="width: 16px; height: 16px;"></i> Kunjungi Link Lomba
                </a>
              </div>
              <div class="lomba-meta">
                <div class="lomba-meta-row"><span class="lomba-meta-label">Tingkat:</span><span class="lomba-meta-value-success"><?= htmlspecialchars($row_lomba['tingkat']) ?></span></div>
                <div class="lomba-meta-row"><span class="lomba-meta-label">Biaya Daftar:</span><span class="lomba-meta-value-success"><?= $biaya_display ?></span></div>
                <div class="lomba-meta-row"><span class="lomba-meta-label">Batas Pendaftaran:</span><span class="lomba-meta-value-danger"><?= date('d M Y', strtotime($row_lomba['deadline_pendaftaran'])) ?></span></div>
              </div>
              <div class="lomba-actions">
                <button type="button" 
                        data-id="<?= $row_lomba['lomba_id'] ?>" data-nama="<?= htmlspecialchars($row_lomba['nama_lomba'], ENT_QUOTES) ?>"
                        data-kategori="<?= $row_lomba['kategori_id'] ?>" data-tingkat="<?= htmlspecialchars($row_lomba['tingkat'], ENT_QUOTES) ?>"
                        data-biaya="<?= $row_lomba['biaya_pendaftaran'] ?>" data-deadline-daftar="<?= $row_lomba['deadline_pendaftaran'] ?>"
                        data-deadline-lomba="<?= $row_lomba['deadline'] ?>" data-deskripsi="<?= htmlspecialchars($row_lomba['deskripsi'], ENT_QUOTES) ?>"
                        data-juknis="<?= htmlspecialchars($row_lomba['juknis']) ?>" data-link-lomba="<?= htmlspecialchars($row_lomba['link_lomba']) ?>"
                        onclick="bukaModalEdit(this)" class="btn-action-edit">Edit</button>
                <a href="home.php?hapus_lomba=<?= $row_lomba['lomba_id'] ?>" onclick="return confirm('Yakin ingin menghapus lomba?')" class="btn-action-hapus">Hapus</a>
              </div>
            </div>
          </div>
      <?php } ?>
    </div>
  </div>

  <div class="card-premium" style="margin-top: 30px;">
    <div class="table-wrapper scroll-box">
        <div class="flex-header">
          <h3 class="table-title">Menunggu Validasi Sertifikat & Prestasi</h3>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Nama Siswa</th>
                <th>Nama Lomba</th>
                <th>Klaim Hasil</th>
                <th>Berkas Bukti</th>
                <th>Aksi Guru</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $q_verifikasi = mysqli_query($conn, "
                  SELECT kp.*, u.nama, l.nama_lomba 
                  FROM klaim_prestasi kp
                  JOIN users u ON kp.user_id = u.user_id
                  JOIN lomba l ON kp.lomba_id = l.lomba_id
                  WHERE kp.status_validasi = 'Menunggu Pengecekan' 
                  ORDER BY kp.tanggal_klaim ASC
              ");
              
              if (mysqli_num_rows($q_verifikasi) > 0) {
                  while ($row_v = mysqli_fetch_assoc($q_verifikasi)) {
                      ?>
                      <tr>
                          <td><strong class="text-dark-bold"><?= htmlspecialchars($row_v['nama']) ?></strong></td>
                          <td><?= htmlspecialchars($row_v['nama_lomba']) ?></td>
                          
                          <td><strong style="color: #10b981;"><?= htmlspecialchars($row_v['hasil_klaim']) ?></strong></td>
                          <td>
                            <?php
                            $nama_file_bersih = str_replace(' ', '', $row_v['file_sertifikat']);
                            $link_sertifikat = "../assets/uploads/img/" . $nama_file_bersih; 
                            ?>
                            <a href="<?= $link_sertifikat ?>" target="_blank" class="crm-badge badge-active" style="text-decoration:none;">
                            Lihat Sertifikat
                            </a>
                          </td>
                          <td>
                            <div style="display: flex; gap: 5px;">
                              <form action="" method="POST" onsubmit="return confirm('Setujui prestasi ini?')">
                                <input type="hidden" name="klaim_id" value="<?= $row_v['klaim_id'] ?>">
                                <input type="hidden" name="status_baru" value="Disetujui">
                                <button type="submit" name="aksi_validasi" class="crm-badge badge-success" style="border:none; cursor:pointer;">Terima</button>
                              </form>
                              
                              <form action="" method="POST" onsubmit="return confirm('Tolak klaim prestasi ini?')">
                                <input type="hidden" name="klaim_id" value="<?= $row_v['klaim_id'] ?>">
                                <input type="hidden" name="status_baru" value="Ditolak">
                                <button type="submit" name="aksi_validasi" class="crm-badge badge-danger" style="border:none; cursor:pointer;">Tolak</button>
                              </form>
                            </div>
                          </td>
                      </tr>
                      <?php
                  }
              } else {
                  echo "<tr><td colspan='5' class='text-center-empty'>Semua sertifikat aman. Tidak ada klaim prestasi yang menggantung.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
       </div>
      </div>

  <div class="crm-container">
    <div class="mini-stats-grid">
      <div class="card-premium mini-card">
        <div class="mini-card-icon icon-blue"><i data-feather="award"></i></div>
        <div><span>Lomba dari Sekolah</span><strong><?= $total_lomba_sekolah ?> <small>Event</small></strong></div>
      </div>
      <div class="card-premium mini-card">
        <div class="mini-card-icon icon-orange"><i data-feather="users"></i></div>
        <div><span>Siswa Aktif Lomba</span><strong><?= $total_siswa_aktif ?> <small>Siswa</small></strong></div>
      </div>
      <div class="card-premium mini-card">
        <div class="mini-card-icon icon-gold"><i data-feather="trending-up"></i></div>
        <div><span>Prestasi Tercatat</span><strong><?= $total_prestasi ?> <small>Juara</small></strong></div>
      </div>
      <div class="card-premium mini-card" style="cursor: pointer;" onclick="document.getElementById('modalKategori').style.display='flex'">
        <div class="mini-card-icon icon-green"><i data-feather="list"></i></div>
        <div><span>Kategori</span><strong>Kelola</strong></div>
      </div>
    </div>

    <div class="double-column">
      <div class="card-premium">
        <div class="table-wrapper scroll-box" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
          <div class="flex-header" style="position: sticky; top: 0; background: #fff; z-index: 10; padding-bottom: 10px;">
            <h3 class="table-title">Pelacakan Aktivitas Lomba Siswa</h3>
          </div>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Nama Siswa</th>
                  <th>Nama Perlombaan</th>
                  <th>Tanggal Daftar / Klaim</th>
                  <th>Hasil / Status</th>
                  <th>Aksi</th> </tr>
              </thead>
              <tbody>
                <?php
                $query_gabungan = "
                  (SELECT p.pendaftaran_id AS id_record, 'pendaftaran' AS tabel_sumber, u.nama, l.nama_lomba, p.tanggal_daftar AS tanggal_aktivitas, p.status AS status_tampil, '' AS file_sertifikat
                   FROM pendaftaran p
                   JOIN users u ON p.user_id = u.user_id
                   JOIN lomba l ON p.lomba_id = l.lomba_id
                   WHERE l.user_id = '$user_id_aktif')
                  UNION 
                  (SELECT kp.klaim_id AS id_record, 'klaim_prestasi' AS tabel_sumber, u.nama, l.nama_lomba, kp.tanggal_klaim AS tanggal_aktivitas, kp.hasil_klaim AS status_tampil, kp.file_sertifikat AS file_sertifikat
                   FROM klaim_prestasi kp
                   JOIN users u ON kp.user_id = u.user_id
                   JOIN lomba l ON kp.lomba_id = l.lomba_id
                   WHERE kp.status_validasi = 'Disetujui')
                  ORDER BY tanggal_aktivitas DESC";

                $q_tracking = mysqli_query($conn, $query_gabungan);
                
                if (mysqli_num_rows($q_tracking) > 0) {
                    while ($r = mysqli_fetch_assoc($q_tracking)) {
                        $hasil = $r['status_tampil'] ? $r['status_tampil'] : 'Menunggu Pengecekan';
                        $status_class = 'badge-info';

                        if (strpos($hasil, 'Juara') !== false || strpos($hasil, 'Harapan') !== false || $hasil == 'selesai') {
                            $status_class = 'badge-success';
                        } elseif (strpos($hasil, 'Lolos') !== false || $hasil == 'aktif') {
                             $status_class = 'badge-active';
                        } elseif (strpos($hasil, 'Batal') !== false || $hasil == 'batal') {
                            $status_class = 'badge-danger';
                        } elseif (strpos($hasil, 'Tidak Lolos') !== false || strpos($hasil, 'Gagal') !== false) {
                            $status_class = 'badge-warning';
                        }
                        $tanggal_tampil = ($r['tanggal_aktivitas']) ? date('d M Y', strtotime($r['tanggal_aktivitas'])) : '-';
                        ?>
                        <tr>
                            <td><strong class="text-dark-bold"><?= htmlspecialchars($r['nama']) ?></strong></td>
                            
                            <td><?= htmlspecialchars($r['nama_lomba']) ?></td> 
                            
                            <td><?= $tanggal_tampil ?></td>
                            <td><span class="crm-badge <?= $status_class ?>"><?= ucwords(htmlspecialchars($hasil)) ?></span></td>
                            
                            <td>
                              <div style="display: flex; gap: 5px;">
                                <form action="" method="POST" onsubmit="return confirm('Batalkan status ini?')">
                                  <input type="hidden" name="id_record" value="<?= $r['id_record'] ?>">
                                  <input type="hidden" name="tabel_sumber" value="<?= $r['tabel_sumber'] ?>">
                                  <button type="submit" name="batal_pelacakan" class="crm-badge badge-info" style="border:none; cursor:pointer;">Batal</button>
                                </form>
                                <form action="" method="POST" onsubmit="return confirm('Hapus riwayat ini permanen?')">
                                  <input type="hidden" name="id_record" value="<?= $r['id_record'] ?>">
                                  <input type="hidden" name="tabel_sumber" value="<?= $r['tabel_sumber'] ?>">
                                  <button type="submit" name="hapus_pelacakan" class="crm-badge badge-danger" style="border:none; cursor:pointer;">Hapus</button>
                                </form>
                              </div>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center-empty'>Belum ada riwayat aktivitas kompetisi siswa ditemukan.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
                
      <div class="card-premium">
        <div class="ranking-wrapper scroll-box">
          <h3 class="card-title">Minat Lomba Per Kategori</h3>
          
          <div class="ranking-list">
            <?php
            $q_kategori_ranking = mysqli_query($conn, "
                SELECT k.nama_kategori, COUNT(p.pendaftaran_id) AS total_peminat
                FROM kategori k
                LEFT JOIN lomba l ON k.kategori_id = l.kategori_id
                LEFT JOIN pendaftaran p ON l.lomba_id = p.lomba_id
                GROUP BY k.kategori_id
                ORDER BY total_peminat DESC, k.nama_kategori ASC
            ");

            if (mysqli_num_rows($q_kategori_ranking) > 0) {
                $no = 1;
                while ($kat = mysqli_fetch_assoc($q_kategori_ranking)) {
                    $total = $kat['total_peminat'];
                    ?>
                    
                    <div class="ranking-item">
                        <div class="ranking-left">
                            <div class="ranking-number"><?= $no ?></div>
                            <div class="ranking-name">
                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                            </div>
                        </div>
                        <div class="ranking-right">
                            <span class="crm-badge badge-peminat">
                                <?= $total ?> Siswa
                            </span>
                        </div>
                    </div>
                    
                    <?php
                    $no++;
                }
            } else {
                echo "<p class='text-center-empty'>Belum ada data kategori lomba.</p>";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

    <div id="modalKategori" class="modal-premium-overlay" style="display: none;">
    <div class="modal-premium-content">
      <div class="modal-premium-header">
        <h4>Kelola Kategori Lomba</h4>
        <button class="btn-close-modal" onclick="document.getElementById('modalKategori').style.display='none'">&times;</button>
      </div>
      <form action="" method="POST" class="form-tambah-kategori">
        <div class="input-group-premium">
          <input type="text" name="nama_kategori" placeholder="Ketik nama kategori baru..." required>
          <button type="submit" name="tambah_kategori" class="btn-simpan-kategori">Tambah</button>
        </div>
      </form>
      <div class="modal-table-wrapper">
        <table class="table-modal-kategori">
          <thead>
            <tr><th>Nama Kategori</th><th style="text-align: center; width: 80px;">Aksi</th></tr>
          </thead>
          <tbody>
            <?php
            $q_modal_kat = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
            if (mysqli_num_rows($q_modal_kat) > 0) {
                while ($kat = mysqli_fetch_assoc($q_modal_kat)) {
                    echo "<tr>
                            <td><span class='nama-kat-item'>".htmlspecialchars($kat['nama_kategori'])."</span></td>
                            <td style='text-align: center;'>
                              <a href='home.php?hapus_kategori=".$kat['kategori_id']."' class='btn-action-premium delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus kategori ini?\")' title='Hapus Kategori'>
                                <i data-feather='trash-2' style='width: 16px; height: 16px; color:#ef4444;'></i>
                              </a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2' style='text-align:center; color:var(--gray);'>Belum ada kategori.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div id="modalTambahLomba" class="modal-overlay" onclick="if(event.target === this) this.style.display='none'">
    <div class="modal-card modal-card-scrollable">
      <h3 class="modal-card-title">Tambah Lomba Baru</h3>
      <form action="" method="POST" enctype="multipart/form-data">
        <label class="form-label">Nama Lomba</label>
        <input type="text" name="nama_lomba" class="form-input" required>
        <div class="form-row">
            <div class="form-col">
                <label class="form-label">Kategori</label>
                <select name="kategori_id" class="form-input" required>
                  <option value="">-- Pilih Kategori --</option>
                  <?php
                  $q_kat_dropdown = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                  while($k = mysqli_fetch_assoc($q_kat_dropdown)) { echo "<option value='{$k['kategori_id']}'>{$k['nama_kategori']}</option>"; }
                  ?>
                </select>
            </div>
            <div class="form-col">
                <label class="form-label">Tingkat</label>
                <select name="tingkat" class="form-input" required>
                  <option value="Sekolah">Sekolah</option>
                  <option value="Kota/Kabupaten">Kota/Kabupaten</option>
                  <option value="Provinsi">Provinsi</option>
                  <option value="Nasional">Nasional</option>
                  <option value="Internasional">Internasional</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-col">
                <label class="form-label">Deadline Pendaftaran</label>
                <input type="date" name="deadline_pendaftaran" class="form-input" required>
            </div>
            <div class="form-col">
                <label class="form-label">Deadline Tahap 1 (Lomba)</label>
                <input type="date" name="deadline" class="form-input" required>
            </div>
        </div>
        <label class="form-label">Biaya Pendaftaran (Rp)</label>
        <input type="number" name="biaya_pendaftaran" value="0" class="form-input" required>
        <label class="form-label">Poster Lomba (Gambar)</label>
        <input type="file" name="poster" accept="image/*" class="form-input">
        <label class="form-label">File Juknis</label>
        <input type="file" name="juknis" class="form-input" accept=".pdf,.png,.jpg,.jpeg">
        <label class="form-label">Link Resmi Lomba (Opsional)</label>
        <input type="url" name="link_lomba" class="form-input" placeholder="https://contoh-link-lomba.com">
        <label class="form-label">Deskripsi Singkat</label>
        <textarea name="deskripsi" rows="3" class="form-input"></textarea>
        <div class="btn-row">
            <button type="button" class="btn-modal-cancel" onclick="document.getElementById('modalTambahLomba').style.display='none'">Batal</button>
            <button type="submit" name="tambah_lomba" class="btn-modal-save">Simpan Lomba</button>
        </div>
      </form>
    </div>
  </div>

  <div id="modalEditLomba" class="modal-overlay" onclick="if(event.target === this) this.style.display='none'">
    <div class="modal-card modal-card-scrollable">
      <h3 class="modal-card-title">Edit Lomba</h3>
      <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_lomba" id="edit_id_lomba">
        <label class="form-label">Nama Lomba</label>
        <input type="text" name="nama_lomba" id="edit_nama_lomba" class="form-input" required>
        <div class="form-row">
            <div class="form-col">
                <label class="form-label">Kategori</label>
                <select name="kategori_id" id="edit_kategori_id" class="form-input" required>
                  <?php
                  $q_kat_dropdown2 = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                  while($k = mysqli_fetch_assoc($q_kat_dropdown2)) { echo "<option value='{$k['kategori_id']}'>{$k['nama_kategori']}</option>"; }
                  ?>
                </select>
            </div>
            <div class="form-col">
                <label class="form-label">Tingkat</label>
                <select name="tingkat" id="edit_tingkat" class="form-input" required>
                  <option value="Sekolah">Sekolah</option>
                  <option value="Kota/Kabupaten">Kota/Kabupaten</option>
                  <option value="Provinsi">Provinsi</option>
                  <option value="Nasional">Nasional</option>
                  <option value="Internasional">Internasional</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-col">
                <label class="form-label">Deadline Pendaftaran</label>
                <input type="date" name="deadline_pendaftaran" id="edit_deadline_pendaftaran" class="form-input" required>
            </div>
            <div class="form-col">
                <label class="form-label">Deadline Tahap 1 (Lomba)</label>
                <input type="date" name="deadline" id="edit_deadline" class="form-input" required>
            </div>
        </div>
        <label class="form-label">Biaya Pendaftaran (Rp)</label>
        <input type="number" name="biaya_pendaftaran" id="edit_biaya_pendaftaran" class="form-input" required>
        <label class="form-label">Poster Lomba</label>
        <input type="file" name="poster" id="edit_poster" accept="image/*" class="form-input">
        <label class="form-label">Ubah File Juknis</label>
        <div class="form-file-wrapper">
            <input type="file" name="juknis" id="edit_juknis" class="form-input form-input-flex" accept=".pdf,.png,.jpg,.jpeg">
            <button type="button" onclick="resetJuknis()" class="btn-reset-file">Reset</button>
        </div>
        <small id="info_juknis_sekarang" class="info-juknis-text"></small>
        <div style="display: none;"><input type="checkbox" name="hapus_juknis_lama" id="hapus_juknis_lama" value="1"></div>
        <label class="form-label">Link Resmi Lomba</label>
        <input type="url" name="link_lomba" id="edit_link_lomba" class="form-input">
        <label class="form-label">Deskripsi Singkat</label>
        <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="form-input"></textarea>
        <div class="btn-row">
            <button type="button" class="btn-modal-cancel" onclick="document.getElementById('modalEditLomba').style.display='none'">Batal</button>
            <button type="submit" name="edit_lomba" class="btn-modal-save">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    feather.replace(); 

    function resetPoster() { document.getElementById('edit_poster').value = ''; }
    function resetJuknis() {
        document.getElementById('edit_juknis').value = '';
        document.getElementById('info_juknis_sekarang').innerHTML = "<span style='color: #dc3545; font-weight: bold;'>❌ Juknis akan dihapus saat disimpan.</span>";
        const checkboxHapus = document.getElementById('hapus_juknis_lama');
        if (checkboxHapus) checkboxHapus.checked = true;
    }

    function bukaModalEdit(button) {
        const id = button.getAttribute('data-id');
        const nama = button.getAttribute('data-nama');
        const kategori = button.getAttribute('data-kategori');
        const tingkat = button.getAttribute('data-tingkat');
        const biaya = button.getAttribute('data-biaya');
        const deadlineDaftar = button.getAttribute('data-deadline-daftar');
        const deadlineLomba = button.getAttribute('data-deadline-lomba');
        const deskripsi = button.getAttribute('data-deskripsi');
        const juknis = button.getAttribute('data-juknis');
        const linkLomba = button.getAttribute('data-link-lomba');

        document.getElementById('edit_id_lomba').value = id;
        document.getElementById('edit_nama_lomba').value = nama;
        document.getElementById('edit_kategori_id').value = kategori;
        document.getElementById('edit_tingkat').value = tingkat;
        document.getElementById('edit_biaya_pendaftaran').value = biaya;
        document.getElementById('edit_deadline_pendaftaran').value = deadlineDaftar;
        document.getElementById('edit_deadline').value = deadlineLomba;
        document.getElementById('edit_deskripsi').value = deskripsi;
        document.getElementById('edit_link_lomba').value = linkLomba || ''; 
        
        const infoJuknis = document.getElementById('info_juknis_sekarang');
        if(juknis && juknis.trim() !== "") {
            infoJuknis.innerHTML = "📄 File aktif: <span style='color: #1c7fff fly;'>" + juknis + "</span>";
        } else {
            infoJuknis.innerHTML = "❌ Belum ada file juknis diunggah untuk lomba ini.";
        }

        const checkboxHapus = document.getElementById('hapus_juknis_lama');
        if (checkboxHapus) checkboxHapus.checked = false;

        document.getElementById('modalEditLomba').style.display = 'flex';
    }
  </script>
</body>
</html>