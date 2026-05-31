<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

function hitungStatusLomba($deadline_tgl) {
    if (empty($deadline_tgl) || $deadline_tgl == '0000-00-00') {
        return ['teks' => 'TBA', 'class' => 'badge-info'];
    }

    $hari_ini = date('Y-m-d');
    $tanggal_lomba = date('Y-m-d', strtotime($deadline_tgl));

    if ($hari_ini <= $tanggal_lomba) {
        return [
            'teks' => 'Aktif',
            'class' => 'badge-sukses'
        ];
    } else {
        return [
            'teks' => 'Sudah Selesai',
            'class' => 'badge-bahaya'
        ];
    }
}

$role_aktif = $_SESSION['role'] ?? 'user'; 

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lomba - Yuk! Lomba</title>
  
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    
    <link rel="stylesheet" href="../assets/css/home-user.css">
    <link rel="stylesheet" href="../assets/css/lomba-user.css?v=<?= time(); ?>"> 
</head>
<body>
    <div class="bungkus-semua">
    
        <aside class="menu-samping-kiri">
        <div class="logo-webnya">
            <img src="/project_ukl/assets/img/logoo.svg" alt="Logo">
            <a href="home.php" class="tulisan-logo">Yuk! Lomba</a>
        </div>

        <div class="grup-kumpulan-menu">
            <p class="judul-kategori-menu">HOME</p>
            <a href="home.php" class="link-menu-biasa">
            <i data-feather="home"></i> Dashboard
            </a>
        <a href="lomba.php" class="link-menu-biasa menu-nyala">
            <?php
                $q_count_menu = mysqli_query($conn, "
                    SELECT COUNT(DISTINCT l.lomba_id) as total 
                    FROM lomba l 
                    LEFT JOIN pendaftaran p ON p.lomba_id = l.lomba_id AND p.user_id = $user_id_aktif
                    WHERE (p.user_id = $user_id_aktif AND p.status = 'aktif') OR l.user_id = $user_id_aktif
                ");
                $jml_lomba_aktif = ($q_count_menu && mysqli_num_rows($q_count_menu) > 0) ? mysqli_fetch_assoc($q_count_menu)['total'] : 0;
            ?>
            <i data-feather="file-text"></i> Lomba <span class="buletan-angka"><?= $jml_lomba_aktif ?: 0 ?></span>
        </a>
        
        <p class="judul-kategori-menu mt-20">AKUN</p>
        <a href="../user/profil.php" class="link-menu-biasa"><i data-feather="user"></i> Profil Saya</a>
        <a href="../auth/logout.php" class="link-menu-biasa menu-keluar"><i data-feather="log-out"></i> Keluar</a>
      </div>
    </aside>

    <main class="isi-konten-kanan">
        <div class="header-halaman-lomba">
            <div class="header-detail-spesifik">
                <a href="home.php" class="tombol-kembali"><i data-feather="arrow-left"></i></a>
                <h1 class="judul-halaman-teks">Lomba</h1>
            </div>

            <div class="tombol-tombol-kanan-atas">
                <a href="../auth/tambah-lomba.php" class="btn-tambah-lomba-header">
                    <i data-feather="plus"></i> Tambah Lomba
                </a>
            </div>
        </div>

      <div class="kontainer-lomba">
        
        <div class="filter-bar">
            <select class="select-filter" id="filterKategori">
                <option value="semua">Semua Kategori</option>
                <option value="UI/UX Design">UI/UX Design</option>
                <option value="Sains">Sains</option>
                <option value="Coding">Coding</option>
                <option value="Bisnis">Bisnis</option>
                <option value="Olimpiade">Olimpiade</option>
                <option value="Robotik">Robotik</option>
            </select>
            
            <select class="select-filter" id="filterTingkat">
                <option value="semua">Semua Tingkat</option>
                <option value="Nasional">Nasional</option>
                <option value="Internasional">Internasional</option>
                <option value="Provinsi">Provinsi</option>
            </select>
            
            <select class="select-filter" id="filterStatus">
                <option value="semua">Semua Status</option>
                <option value="Sudah Daftar">Sudah Daftar</option>
                <option value="Belum Daftar">Belum Daftar</option>
            </select>
        </div>

        <div id="pesanDataKosong" class="pesan-kosong-wrapper">
            <i data-feather="inbox" class="icon-empty"></i>
            <h3 class="title-empty">Tidak ada lomba yang ditemukan</h3>
            <p class="desc-empty">Coba ubah kombinasi kategori, tingkat, atau status filternya.</p>
        </div>

        <div class="grid-lomba" id="wadahKartuLomba">
            <?php
            $q = "SELECT l.*, k.nama_kategori, 
                  pd.status AS status_dari_pendaftaran,
                  (SELECT step FROM timeline tl WHERE tl.lomba_id = l.lomba_id AND tl.status = 'Dalam Proses' LIMIT 1) AS babak_sekarang,
                  (SELECT COUNT(*) FROM timeline tl WHERE tl.lomba_id = l.lomba_id) as total_timeline,
                  (SELECT COUNT(*) FROM timeline tl WHERE tl.lomba_id = l.lomba_id AND tl.status = 'Selesai') as timeline_selesai
                  FROM lomba l 
                  LEFT JOIN kategori k ON l.kategori_id = k.kategori_id 
                  LEFT JOIN pendaftaran pd ON l.lomba_id = pd.lomba_id AND pd.user_id = $user_id_aktif
                  WHERE l.user_id = $user_id_aktif OR pd.user_id = $user_id_aktif
                  GROUP BY l.lomba_id
                  ORDER BY l.lomba_id DESC";
                  
            $res = mysqli_query($conn, $q);
            
            if($res && mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) {
                    
                    if ($row['user_id'] == $user_id_aktif) {
                        $status_asli = $row['status_daftar'] ?? 'belum';
                        $sudah_daftar = ($status_asli == 'sudah');
                        $belum_daftar = ($status_asli == 'belum');
                    } else {
                        $sudah_daftar = !empty($row['status_dari_pendaftaran']);
                        $belum_daftar = !$sudah_daftar;
                    }

                    $deadline_cek = $belum_daftar ? $row['deadline_pendaftaran'] : $row['deadline']; 
                    
                    if (!empty($deadline_cek) && $deadline_cek != '0000-00-00') {
                        $tgl_tampil = date('d M Y', strtotime($deadline_cek));
                    } else {
                        $tgl_tampil = 'TBA';
                    }

                    $label_tampil = (!$belum_daftar) ? "Deadline Tahap 1" : "Deadline Daftar";
                    
                    $tgl_sekarang = date('Y-m-d');
                    $status_js = (!empty($deadline_cek) && $deadline_cek >= $tgl_sekarang) ? "aktif" : "berakhir";
                    
                    $biaya = ($row['biaya_pendaftaran'] == 0) ? 'Gratis' : 'Rp ' . number_format($row['biaya_pendaftaran'], 0, ',', '.');
                    
                    $total = (int)$row['total_timeline'];
                    $selesai = (int)$row['timeline_selesai'];
                    $persen = ($total > 0) ? round(($selesai / $total) * 100) : 0;

                    $babak = !empty($row['babak_sekarang']) ? $row['babak_sekarang'] : 'Pendaftaran / Tahap Awal';
                    if ($total == 0) {
                        $babak = "Belum Ada Timeline";
                    }
                    
                    $nama_kategori_js = htmlspecialchars($row['nama_kategori'] ?? 'Lainnya');
                    $tingkat_js = htmlspecialchars($row['tingkat'] ?? 'Tidak Diketahui');
                    $hasil_akhir = $row['hasil_akhir'] ?? 'Menunggu Pengumuman';
            ?>
            
            <div class="kartu-lomba kartu-item" 
                 data-kategori="<?= $nama_kategori_js ?>" 
                 data-tingkat="<?= $tingkat_js ?>" 
                 data-status="<?= $status_js ?>">
                 
                <div class="kartu-konten-atas">
                    <div class="kartu-header">
                        <span class="tag-kategori"><?= $nama_kategori_js ?></span>
                        
                        <?php if ($persen == 100): ?>
                            <span class="tag-selesai">Selesai</span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="judul-lomba"><?= htmlspecialchars($row['nama_lomba']) ?></h3>
                    
                    <div class="info-singkat">
                        <i data-feather="map-pin" class="info-icon"></i>
                        <span><?= $tingkat_js ?></span>
                    </div>
                    
                    <div class="info-singkat">
                        <i data-feather="layers" class="info-icon"></i>
                        <span>Babak: <strong><?= htmlspecialchars($babak) ?></strong></span>
                    </div>

                    <div class="info-singkat mt-10">
                        <i data-feather="dollar-sign" class="info-icon icon-biaya"></i>
                        <span><?= $biaya ?></span>
                        
                        <?php if($belum_daftar): ?>
                            <span class="badge-pendaftaran badge-belum-daftar">Belum Mendaftar</span>
                        <?php else: ?>
                            <span class="badge-pendaftaran badge-sudah-daftar">Sudah Daftar</span>
                        <?php endif; ?>
                    </div>

                    <div class="bagian-progress-bar">
                        <div class="teks-progressnya">
                            <span>Progress</span>
                            <span><?= $persen ?>%</span>
                        </div>
                        <div class="rel-progress-belakang">
                            <div class="rel-progress-depan" style="width: <?= $persen ?>%;"></div>
                        </div>
                    </div>

                    <a href="../auth/klaim-prestasi.php?id=<?= $row['lomba_id'] ?>" class="btn-klaim">
                        <i data-feather="award"></i> Lapor Hasil
                    </a>                
                    </div>
                
                <div class="kartu-konten-bawah">
                    <div class="deadline-badge">
                        <div class="deadline-info-wrapper">
                            <span class="txt-label-tampil">
                                <i data-feather="clock" class="icon-clock"></i><?= $label_tampil ?>
                            </span>
                            <span class="txt-tgl-tampil"><?= $tgl_tampil ?></span>
                        </div>
                    </div>
                    
                    <div class="kartu-aksi-wrapper">
                        <a href="detail-lomba.php?id=<?= $row['lomba_id'] ?>" class="tombol-biru-panjang">
                            Lihat Detail
                        </a>
                        
                        <?php 
                        if ($role_aktif == 'guru' || $row['user_id'] == $user_id_aktif): 
                        ?>
                            <a href="../auth/edit-lomba.php?id=<?= $row['lomba_id'] ?>" title="Edit Lomba" class="btn-aksi btn-aksi-edit">
                                <i data-feather="edit-2" class="icon-aksi"></i>
                            </a>

                            <a href="hapus-lomba.php?id=<?= $row['lomba_id'] ?>" 
                               class="btn-aksi btn-aksi-hapus" 
                               onclick="return confirm('Yakin ingin menghapus lomba ini?')">
                               <i data-feather="trash-2" class="icon-aksi"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div> 
            
            <?php 
                }
            } else {
                echo "<p class='teks-kosong-awal'>Belum ada lomba yang ditambahkan atau Anda ikuti.</p>";
            }
            ?>
        </div>
        
      </div>
    </main>
  </div>

  <script>
    feather.replace();
  </script>
  <script src="../assets/js/lomba.js"></script>
</body>
</html>