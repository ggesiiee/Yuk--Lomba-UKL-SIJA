<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ubah_status_keikutsertaan'])) {
    $lomba_id = (int)$_POST['lomba_id'];
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']); 

    $status_diizinkan = ['aktif', 'selesai', 'batal', 'pending'];
    
    if (in_array($status_baru, $status_diizinkan)) {
        $q_update_status = "UPDATE pendaftaran 
                            SET status = '$status_baru' 
                            WHERE user_id = '$user_id_aktif' AND lomba_id = '$lomba_id'";
        
        if (mysqli_query($conn, $q_update_status)) {
            echo "<script>
                    alert('Status keikutsertaan berhasil diperbarui!'); 
                    window.location.href='home.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal memperbarui status: " . mysqli_error($conn) . "'); 
                    window.location.href='home.php';
                  </script>";
        }
    } else {
        echo "<script>alert('Status tidak valid!'); window.location.href='home.php';</script>";
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ikuti_lomba'])) {
    $lomba_id = (int)$_POST['lomba_id'];
    $tanggal_sekarang = date('Y-m-d');

    $cek_pendaftaran = mysqli_query($conn, "SELECT * FROM pendaftaran WHERE user_id = '$user_id_aktif' AND lomba_id = '$lomba_id'");
    
    if (mysqli_num_rows($cek_pendaftaran) > 0) {
        echo "<script>alert('Anda sudah terdaftar dalam lomba ini!'); window.location.href='home.php';</script>";
    } else {
        $query_daftar = "INSERT INTO pendaftaran (user_id, lomba_id, status, tanggal_daftar) 
                         VALUES ('$user_id_aktif', '$lomba_id', 'aktif', '$tanggal_sekarang')";
        
        if (mysqli_query($conn, $query_daftar)) {
            echo "<script>alert('Pendaftaran Berhasil! Lomba kini aktif di dashboard Anda.'); window.location.href='home.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan sistem, gagal mendaftar.'); window.location.href='home.php';</script>";
        }
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['batal_ikuti'])) {
    $lomba_id = (int)$_POST['lomba_id'];
    
    $query_batal = "DELETE FROM pendaftaran WHERE user_id = '$user_id_aktif' AND lomba_id = '$lomba_id'";
    
    if (mysqli_query($conn, $query_batal)) {
        echo "<script>alert('Keikutsertaan berhasil dibatalkan!'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan sistem, gagal membatalkan.'); window.location.href='home.php';</script>";
    }
    exit();
}

$q_lomba_aktif = mysqli_query($conn, "
    SELECT l.* FROM lomba l
    LEFT JOIN pendaftaran p ON p.lomba_id = l.lomba_id AND p.user_id = $user_id_aktif
    WHERE (p.user_id = $user_id_aktif AND p.status = 'aktif') OR l.user_id = $user_id_aktif
    ORDER BY l.lomba_id DESC LIMIT 1
");
$lomba_aktif = mysqli_fetch_assoc($q_lomba_aktif);

$persentase = 0;
$target_terdekat = "Belum ada target";
$tanggal_target = "-";

if ($lomba_aktif) {
    $id_lomba = $lomba_aktif['lomba_id'];

    $q_hitung = mysqli_query($conn, "SELECT 
        COUNT(*) as total_tugas, 
        SUM(IF(presentase = 100, 1, 0)) as tugas_selesai 
        FROM progress WHERE lomba_id = $id_lomba AND user_id = $user_id_aktif");
    
    $data_hitung = mysqli_fetch_assoc($q_hitung);
    
    $total = $data_hitung['total_tugas'] ? $data_hitung['total_tugas'] : 0;
    $selesai = $data_hitung['tugas_selesai'] ? $data_hitung['tugas_selesai'] : 0;
    
    if ($total > 0) {
        $persentase = round(($selesai / $total) * 100);
    }

    $q_target = mysqli_query($conn, "SELECT deskripsi, tanggal_update FROM progress WHERE lomba_id = $id_lomba AND user_id = $user_id_aktif AND presentase = 0 ORDER BY tanggal_update ASC LIMIT 1");
    if ($q_target && mysqli_num_rows($q_target) > 0) {
        $target_data = mysqli_fetch_assoc($q_target);
        $target_terdekat = $target_data['deskripsi'];
        
        if (!empty($target_data['tanggal_update'])) {
            $tanggal_target = date('d F Y', strtotime($target_data['tanggal_update']));
        }
    } else {
        $target_terdekat = "Semua tugas selesai!";
        $tanggal_target = "Hore!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Yuk! Lomba</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/feather-icons"></script>
    
    <link rel="stylesheet" href="../assets/css/home-user.css">
</head>
<body>
    <div class="bungkus-semua">
        
        <aside class="menu-samping-kiri">
            <div class="logo-webnya">
                <img src="/project_ukl/assets/img/logoo.svg" alt="Logo">
                <a href="../index.php" class="tulisan-logo">Yuk! Lomba</a>
            </div>

            <div class="grup-kumpulan-menu">
                <p class="judul-kategori-menu">HOME</p>
                <a href="home.php" class="link-menu-biasa menu-nyala">
                    <i data-feather="home"></i> Dashboard
                </a>
                <a href="lomba.php" class="link-menu-biasa">
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

                <p class="judul-kategori-menu" style="margin-top: 20px;">AKUN</p>
                <a href="../user/profil.php" class="link-menu-biasa"><i data-feather="user"></i> Profil Saya</a>
                <a href="../auth/logout.php" class="link-menu-biasa menu-keluar"><i data-feather="log-out"></i> Keluar</a>
            </div>
        </aside>

        <main class="isi-konten-kanan">
            
            <header class="bagian-atas-header">
                <?php
                    $q_user = mysqli_query($conn, "SELECT u.nama, p.foto FROM users u LEFT JOIN profil p ON u.user_id = p.user_id WHERE u.user_id = $user_id_aktif LIMIT 1");
                    
                    $nama_asli_user = "User";
                    $foto_profil_home = "photo-default.jpg";
                
                    if ($q_user && mysqli_num_rows($q_user) > 0) {
                        $dt_user = mysqli_fetch_assoc($q_user);
                        
                        if (!empty($dt_user['nama'])) {
                            $nama_asli_user = strtok($dt_user['nama'], " "); 
                        }
                        if (!empty($dt_user['foto'])) {
                            $foto_profil_home = $dt_user['foto'];
                        }
                    }
                ?>
                <div class="foto-dan-halo">
                    <img src="../assets/img/<?= htmlspecialchars($foto_profil_home) ?>" alt="Avatar">
                    <div class="teks-header-kiri"> 
                        <h1>Welcome, <?= htmlspecialchars((string)$nama_asli_user) ?></h1>

                        <div class="kalender-minimalis">
                            <div class="info-tgl">
                                <span id="hari-ini" class="teks-hari"></span>
                                <h2 id="tanggal-bulan-ini" class="teks-tgl"></h2>
                                <span id="tahun-ini" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tombol-tombol-kanan-atas">
                    <a href="history-notes.php" class="tombol-ikon-bulet">
                        <i data-feather="clock"></i>
                    </a>
                    <a href="../auth/tambah-lomba.php" class="btn-tambah-lomba-header">
                        <i data-feather="plus"></i> Tambah Lomba
                    </a>
                </div>
            </header>

            <div class="header-konten">
                <div class="kotak-progress-header">
                    <h3><i data-feather="activity" style="width: 18px;"></i> Progress Lomba Aktif</h3>
                    <div class="area-scroll-kartu">
                        <?php
                        $q_progress = "
                            SELECT 
                                l.lomba_id, 
                                l.nama_lomba, 
                                l.deadline,
                                (SELECT COUNT(*) FROM timeline t WHERE t.lomba_id = l.lomba_id AND t.status = 'selesai') AS total_selesai,
                                (SELECT COUNT(*) FROM timeline t WHERE t.lomba_id = l.lomba_id) AS total_tahapan
                            FROM lomba l
                            LEFT JOIN pendaftaran p ON l.lomba_id = p.lomba_id AND p.user_id = $user_id_aktif
                            WHERE (p.user_id = $user_id_aktif AND p.status = 'aktif') OR l.user_id = $user_id_aktif
                            GROUP BY l.lomba_id
                            ORDER BY l.deadline ASC 
                        ";
                        $res_progress = mysqli_query($conn, $q_progress);

                        if($res_progress && mysqli_num_rows($res_progress) > 0) {
                            while($row = mysqli_fetch_assoc($res_progress)) {
                                $tgl_dl = $row['deadline'] ? date('d M Y', strtotime($row['deadline'])) : 'TBA';
                                
                                $total_selesai = (int)$row['total_selesai'];
                                $total_tahapan = (int)$row['total_tahapan'];
                                $presentase = ($total_tahapan > 0) ? round(($total_selesai / $total_tahapan) * 100) : 0;
                        ?>
                        
                        <div class="item-lomba-kecil" onclick="window.location.href='detail-lomba.php?id=<?= $row['lomba_id'] ?>'" style="cursor: pointer;" title="Lihat Detail <?= htmlspecialchars($row['nama_lomba']) ?>">
                            <div class="info-teks">
                                <span><?= htmlspecialchars($row['nama_lomba']) ?></span>
                                <span style="color: var(--primary-color);"><?= $presentase ?>%</span>
                            </div>
                            <div class="bar-bg">
                                <div class="bar-fill" style="width: <?= $presentase ?>%;"></div>
                            </div>
                            <div class="tgl-deadline-progress">
                                <i data-feather="calendar"></i> Deadline: <?= $tgl_dl ?>
                            </div>
                        </div>
                        
                        <?php 
                            } 
                        } else { 
                            echo "<p style='padding:10px; color: var(--gray);'>Belum ada progress aktif.</p>"; 
                        } 
                        ?>
                    </div>
                    <button class="tombol-biru-panjang" style="margin-top: 10px;" onclick="window.location.href='lomba.php'">
                        Lihat Semua Progres <i data-feather="arrow-right"></i>
                    </button>
                </div>
            </div> 

            <div class="baris-layout margin-bawah">
                
                <div class="kotak-putih-biasa flex-1">
                    <div class="atasnya-kanban">
                        <?php
                            $q_count_progress = mysqli_query($conn, "
                                SELECT COUNT(DISTINCT l.lomba_id) as total 
                                FROM lomba l 
                                LEFT JOIN pendaftaran p ON p.lomba_id = l.lomba_id AND p.user_id = $user_id_aktif
                                WHERE (p.user_id = $user_id_aktif AND p.status = 'aktif') OR l.user_id = $user_id_aktif
                            ");
                            $total_progress = ($q_count_progress && mysqli_num_rows($q_count_progress) > 0) ? mysqli_fetch_assoc($q_count_progress)['total'] : 0;
                        ?>
                        <span class="tulisan-tab-nyala">Progress Terakhir <span class="angka-kecil-tab"><?= $total_progress ?></span></span>
                    </div>
                    
                    <div class="jejeran-tugas" style="display: flex; flex-direction: column; gap: 15px;">
                        <?php
                        $q_progress_bawah = "
                            SELECT p.tanggal_daftar, l.lomba_id, l.nama_lomba, l.tingkat, l.deadline, k.nama_kategori,
                                (SELECT COUNT(*) FROM timeline t WHERE t.lomba_id = l.lomba_id) as total_timeline,
                                (SELECT COUNT(*) FROM timeline t WHERE t.lomba_id = l.lomba_id AND t.status = 'Selesai') as timeline_selesai,
                                (SELECT step FROM timeline t WHERE t.lomba_id = l.lomba_id AND t.status = 'Dalam Proses' LIMIT 1) as babak_sekarang,
                                (SELECT MAX(created_at) FROM timeline t WHERE t.lomba_id = l.lomba_id AND t.status = 'Selesai') as update_terakhir
                            FROM lomba l
                            LEFT JOIN pendaftaran p ON l.lomba_id = p.lomba_id AND p.user_id = $user_id_aktif
                            LEFT JOIN kategori k ON l.kategori_id = k.kategori_id 
                            WHERE (p.user_id = $user_id_aktif AND p.status = 'aktif') OR l.user_id = $user_id_aktif
                            GROUP BY l.lomba_id
                            ORDER BY update_terakhir DESC, l.lomba_id DESC LIMIT 2
                        ";
                        
                        $res_progress_bawah = mysqli_query($conn, $q_progress_bawah);
                        
                        if($res_progress_bawah && mysqli_num_rows($res_progress_bawah) > 0) {
                            while($row_p = mysqli_fetch_assoc($res_progress_bawah)) {
                                
                                $tot = isset($row_p['total_timeline']) ? (int)$row_p['total_timeline'] : 0;
                                $sel = isset($row_p['timeline_selesai']) ? (int)$row_p['timeline_selesai'] : 0;
                                $persen_p = ($tot > 0) ? round(($sel / $tot) * 100) : 0; 
                                
                                $tgl_dl = $row_p['deadline'] ? date('d M Y', strtotime($row_p['deadline'])) : 'TBA';
                                $tgl_update = $row_p['update_terakhir'] ? date('d M Y', strtotime($row_p['update_terakhir'])) : 'Belum ada aksi';

                                $nama_kategori = htmlspecialchars($row_p['nama_kategori'] ?? 'Lainnya');
                                $tingkat = htmlspecialchars($row_p['tingkat'] ?? 'Tingkat');
                                $babak = !empty($row_p['babak_sekarang']) ? $row_p['babak_sekarang'] : 'Pendaftaran / Penyisihan';
                        ?>
                        
                        <a href="detail-lomba.php?id=<?= $row_p['lomba_id'] ?>&from=home" class="kartu-lomba">
                            <div>
                                <span class="tag-kategori"><?= $nama_kategori ?></span>
                                <h3 class="judul-lomba"><?= htmlspecialchars($row_p['nama_lomba']) ?></h3>
                                
                                <div class="info-singkat">
                                    <i data-feather="map-pin" style="width:14px; height:14px;"></i>
                                    <span><?= $tingkat ?></span>
                                </div>
                                
                                <div class="info-singkat">
                                    <i data-feather="layers" style="width:14px; height:14px;"></i>
                                    <span>Babak: <strong><?= htmlspecialchars($babak) ?></strong></span>
                                </div>

                                <div class="bagian-progress-bar">
                                    <div class="teks-progressnya">
                                        <span>Progress</span>
                                        <span><?= $persen_p ?>%</span>
                                    </div>
                                    <div class="rel-progress-belakang">
                                        <div class="rel-progress-depan" style="width: <?= $persen_p ?>%;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="bawah-kartu-progress">
                                <div class="deadline-badge">
                                    <i data-feather="clock" style="width:14px; height:14px;"></i> Deadline: <?= $tgl_dl ?>
                                </div>
                                <div class="update-badge">
                                    <i data-feather="check-circle" style="width:12px; height:12px; color: var(--secondary-color);"></i> Update: <?= $tgl_update ?>
                                </div>
                            </div>
                        </a>
                        
                        <?php 
                            }
                        } else {
                            echo "<p style='color: var(--gray); padding: 20px; text-align: center; font-size: 0.9rem;'>Belum ada progress lomba aktif.</p>";
                        } 
                        ?>
                    </div>
                </div>

                <div class="kotak-putih-biasa flex-1">
                    <div class="atasnya-kanban">
                        <?php
                            $q_count_dl = mysqli_query($conn, "
                                SELECT COUNT(DISTINCT l.lomba_id) as total 
                                FROM lomba l 
                                LEFT JOIN pendaftaran p ON p.lomba_id = l.lomba_id AND p.user_id = $user_id_aktif
                                WHERE ((p.user_id = $user_id_aktif AND p.status = 'aktif') OR l.user_id = $user_id_aktif) AND l.deadline >= CURDATE()
                            ");
                            $total_dl = ($q_count_dl && mysqli_num_rows($q_count_dl) > 0) ? mysqli_fetch_assoc($q_count_dl)['total'] : 0;
                        ?>
                        <span class="tulisan-tab-nyala">Deadline Terdekat <span class="angka-kecil-tab"><?= $total_dl ?></span></span>
                    </div>
                    <div class="jejeran-tugas" style="display: flex; flex-direction: column; gap: 15px;">
                        
                        <?php
                            $q_deadline = "
                                SELECT l.user_id AS pembuat_lomba, p.status AS status_pendaftaran, l.lomba_id, l.nama_lomba, l.deadline, l.deadline_pendaftaran, l.tingkat, l.biaya_pendaftaran, k.nama_kategori,
                                    (SELECT COUNT(*) FROM timeline t WHERE t.lomba_id = l.lomba_id) as total_timeline,
                                    (SELECT COUNT(*) FROM timeline t WHERE t.lomba_id = l.lomba_id AND t.status = 'selesai') as timeline_selesai,
                                    (SELECT step FROM timeline t WHERE t.lomba_id = l.lomba_id AND t.status = 'Dalam Proses' LIMIT 1) as babak_sekarang
                                FROM lomba l 
                                LEFT JOIN pendaftaran p ON l.lomba_id = p.lomba_id AND p.user_id = $user_id_aktif
                                LEFT JOIN kategori k ON l.kategori_id = k.kategori_id 
                                WHERE ((p.user_id = $user_id_aktif AND p.status = 'aktif') OR l.user_id = $user_id_aktif) AND l.deadline >= CURDATE()
                                GROUP BY l.lomba_id
                                ORDER BY l.deadline ASC LIMIT 2
                            ";

                            $res_deadline = mysqli_query($conn, $q_deadline);

                            if($res_deadline && mysqli_num_rows($res_deadline) > 0) {
                                while($row_dl = mysqli_fetch_assoc($res_deadline)) {
                                    
                                    $tot_dl = isset($row_dl['total_timeline']) ? (int)$row_dl['total_timeline'] : 0;
                                    $sel_dl = isset($row_dl['timeline_selesai']) ? (int)$row_dl['timeline_selesai'] : 0;
                                    $persen_dl = ($tot_dl > 0) ? round(($sel_dl / $tot_dl) * 100) : 0; 
                                    
                                    $tgl_sekarang = date('Y-m-d');
                                    $sudah_daftar_badge = (!empty($row_dl['status_pendaftaran']) || $row_dl['pembuat_lomba'] == $user_id_aktif);
                                    $belum_daftar = !$sudah_daftar_badge;                                    
                                    $deadline_cek = $row_dl['deadline']; 
                                    $tgl_tampil = date('d M Y', strtotime($deadline_cek));
                                    $label_tampil = (!$belum_daftar) ? "Deadline Tahap 1" : "Deadline Daftar";
                                    
                                    $status_js = ($deadline_cek >= $tgl_sekarang) ? "aktif" : "berakhir";
                                    $biaya = ($row_dl['biaya_pendaftaran'] == 0) ? 'Gratis' : 'Rp ' . number_format($row_dl['biaya_pendaftaran'], 0, ',', '.');
                                    
                                    $babak = !empty($row_dl['babak_sekarang']) ? $row_dl['babak_sekarang'] : 'Pendaftaran / Tahap Awal';
                                    if ($tot_dl == 0) { $babak = "Belum Ada Timeline"; }

                                    $nama_kategori_js = htmlspecialchars($row_dl['nama_kategori'] ?? 'Lainnya');
                                    $tingkat_js = htmlspecialchars($row_dl['tingkat'] ?? 'Tidak Diketahui');
                            ?>

                            <div class="kartu-lomba kartu-item" style="width: 100%;">
                                
                                <div class="kartu-konten-atas">
                                    <div class="kartu-header">
                                        <span class="tag-kategori"><?= $nama_kategori_js ?></span>
                                        <?php if ($persen_dl == 100): ?>
                                            <span class="tag-selesai">Selesai</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h3 class="judul-lomba"><?= htmlspecialchars($row_dl['nama_lomba']) ?></h3>
                                    
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
                                            <span><?= $persen_dl ?>%</span>
                                        </div>
                                        <div class="rel-progress-belakang">
                                            <div class="rel-progress-depan" style="width: <?= $persen_dl ?>%;"></div>
                                        </div>
                                    </div>
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
                                </div>
                            </div>

                            <?php 
                                }
                            } else {
                                echo "<p style='color: var(--gray); padding: 20px; text-align: center; font-size: 0.9rem;'>Tidak ada deadline mendesak saat ini.</p>";
                            } 
                            ?>

                    </div>
                    <a href="lomba.php" class="tombol-biru-panjang jarak-atas-dikit" style="display: flex; justify-content: center; align-items: center;">
                        Lihat Lebih Banyak <i data-feather="arrow-right" style="margin-left: 5px;"></i>
                    </a>
                </div>
            </div>

            <div class="baris-layout">
                <div class="kotak-putih-biasa flex-2">
                    <h1 class="judul-gede-todo">Fokus Hari Ini</h1>
                    <div class="daftar-list-todo">
                        
                        <?php
                        $q_notes = mysqli_query($conn, "SELECT * FROM notes WHERE user_id = $user_id_aktif AND (status = 0 OR (status = 1 AND tanggal_selesai = CURDATE())) ORDER BY created_at DESC LIMIT 5");
                        if($q_notes && mysqli_num_rows($q_notes) > 0) {
                            while($row_note = mysqli_fetch_assoc($q_notes)) {
                                $tgl_note = isset($row_note['created_at']) ? date('F d, Y', strtotime($row_note['created_at'])) : date('F d, Y');
                        ?>

                        <div class="baris-todo-satu" id="bungkus-note-<?= $row_note['note_id'] ?>" style="<?= (isset($row_note['status']) && $row_note['status'] == 1) ? 'text-decoration: line-through; opacity: 0.5;' : '' ?>"> 
                            <label class="kiri-todo">
                                <input type="checkbox" 
                                    class="cek-todo" 
                                    data-id="<?= $row_note['note_id'] ?>" 
                                    <?= (isset($row_note['status']) && $row_note['status'] == 1) ? 'checked' : '' ?> 
                                    onchange="updateStatusNote(this)" />
                    
                                <span><?= htmlspecialchars($row_note['isi_note']) ?></span>
                            </label>
                            
                            <div class="kanan-todo">
                                <span class="tanggal-abu"><?= $tgl_note ?></span>
                            
                                <a href="javascript:void(0)" 
                                   class="btn-edit-note" 
                                   onclick="bukaModalEdit(<?= $row_note['note_id'] ?>, '<?= mysqli_real_escape_string($conn, $row_note['isi_note']) ?>')"
                                   title="Edit Tugas">
                                    <i data-feather="edit-2" style="width: 16px; height: 16px;"></i>
                                </a> 

                                <a href="../auth/hapus-note.php?id=<?= $row_note['note_id'] ?>" 
                                   class="btn-hapus-note"
                                   onclick="return confirm('Yakin ingin menghapus tugas ini?')">
                                    <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                </a>
                            </div>
                        </div>
                        <?php 
                            } 
                        } else { 
                            echo "<p class='tanggal-abu'>Belum ada tugas hari ini.</p>"; 
                        } 
                        ?>
                        
                        <div class="tambah-tugas-baru" style="cursor: pointer;" onclick="bukaModal()">
                            <i data-feather="plus"></i> Tambah Note
                        </div>
                    </div>
                </div>
            </div>

            <div class="user-lomba-container">
                <h2 class="user-lomba-title">
                    <i data-feather="award"></i> Jelajahi & Ikuti Lomba Tersedia
                </h2>

                <div class="user-lomba-grid">
                    <?php
                    $q_lomba_user = mysqli_query($conn, "
                        SELECT l.*, k.nama_kategori, u.nama AS nama_guru 
                        FROM lomba l 
                        LEFT JOIN kategori k ON l.kategori_id = k.kategori_id 
                        JOIN users u ON l.user_id = u.user_id 
                        WHERE u.role = 'guru' 
                        ORDER BY l.lomba_id DESC
                    ");

                    if (!$q_lomba_user || mysqli_num_rows($q_lomba_user) == 0) {
                        echo "<div class='user-lomba-empty'>Saat ini belum ada kompetisi dari Guru yang tersedia untuk Anda ikuti.</div>";
                    }

                    while ($row = mysqli_fetch_assoc($q_lomba_user)) {
                        $poster_path = !empty($row['poster']) ? "../assets/uploads/img/" . $row['poster'] : "../assets/uploads/img/default-poster.jpeg";
                        $biaya_txt = $row['biaya_pendaftaran'] == 0 ? "Gratis" : "Rp " . number_format($row['biaya_pendaftaran'], 0, ',', '.');
                        
                        // PERBAIKAN TANGGAL: Cek nilai kosong agar tidak 01 Jan 1970
                        $deadline_cek = $row['deadline'];
                        if (!empty($deadline_cek) && $deadline_cek != '0000-00-00') {
                            $deadline_reg = date('d M Y', strtotime($deadline_cek));
                        } else {
                            $deadline_reg = 'TBA'; // Menampilkan 'TBA' jika tanggal belum diisi
                        }

                        // Cek apakah user sedang aktif mengikuti lomba ini
                        $id_lomba_cek = $row['lomba_id'];
                        $status_ikut = mysqli_query($conn, "SELECT status FROM pendaftaran WHERE user_id = '$user_id_aktif' AND lomba_id = '$id_lomba_cek' LIMIT 1");
                        $sudah_daftar = (mysqli_num_rows($status_ikut) > 0);
                    ?>
                    <div class="user-lomba-card">
                        <div class="user-lomba-banner">
                            <a href="<?= $poster_path ?>" target="_blank" title="Klik untuk memperbesar gambar">
                                <img src="<?= $poster_path ?>" alt="Poster Lomba" class="user-lomba-poster" style="cursor: zoom-in;">
                            </a>
                            <span class="user-lomba-tag"><?= htmlspecialchars($row['nama_kategori'] ?? 'Lainnya') ?></span>
                        </div>

                        <div class="user-lomba-body">
                            <div>
                                <h3 class="user-lomba-name">
                                    <a href="detail-lomba.php?id=<?= $row['lomba_id'] ?>" style="color: var(--dark); text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--dark)'">
                                        <?= htmlspecialchars($row['nama_lomba']) ?>
                                    </a>
                                </h3>

                                <p class="user-lomba-desc"><?= !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : 'Tidak ada deskripsi detail.' ?></p>
                            </div>

                            <div class="user-lomba-meta">
                                <div class="user-lomba-meta-item">
                                    <span class="meta-lbl">Tingkat:</span>
                                    <span class="meta-val"><?= htmlspecialchars($row['tingkat']) ?></span>
                                </div>
                                <div class="user-lomba-meta-item">
                                    <span class="meta-lbl">Biaya:</span>
                                    <span class="meta-val-price"><?= $biaya_txt ?></span>
                                </div>
                                <div class="user-lomba-meta-item">
                                    <span class="meta-lbl">Batas Daftar:</span>
                                    <span class="meta-val-date"><?= $deadline_reg ?></span>
                                </div>
                            </div>

                            <div class="user-lomba-actions">
                                <a href="detail-lomba.php?id=<?= $row['lomba_id'] ?>" class="btn-detail-lomba" style="background: var(--background-color); color: var(--gray); padding: 8px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; border: 1px solid var(--surface-color);">
                                    <i data-feather="eye" style="width: 15px; height: 15px;"></i> Detail
                                </a>

                                <?php if ($sudah_daftar): ?>
                                    <form action="" method="POST" class="form-ikuti" style="margin: 0;">
                                        <input type="hidden" name="lomba_id" value="<?= $row['lomba_id'] ?>">
                                        <button type="submit" name="batal_ikuti" class="btn-batal-ikut" style="background: #ef4444; color: white; padding: 8px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer;" onclick="return confirm('Yakin ingin membatalkan keikutsertaan Anda di lomba ini?')">Batal Ikut</button>
                                    </form>
                                <?php else: ?>
                                    <form action="" method="POST" class="form-ikuti" style="margin: 0;">
                                        <input type="hidden" name="lomba_id" value="<?= $row['lomba_id'] ?>">
                                        <button type="submit" name="ikuti_lomba" class="btn-ikuti-lomba" style="background: var(--primary-color); color: white; padding: 8px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer;" onclick="return confirm('Apakah Anda yakin ingin mendaftar di lomba <?= htmlspecialchars($row['nama_lomba'], ENT_QUOTES) ?>?')">Ikuti Lomba</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>

        </main>
    </div>

    <div id="modalNote" class="modal-backdrop">
        <div class="modal-konten">
            <div class="modal-header">
                <h3>Tambah Tugas / Catatan</h3>
                <button class="btn-tutup" onclick="tutupModal()">&times;</button>
            </div>
            <form action="../auth/tambah-note.php" method="POST">
                <div class="form-grup">
                    <textarea name="isi_note" rows="4" placeholder="Apa yang ingin kamu kerjakan hari ini?" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-batal" onclick="tutupModal()">Batal</button>
                    <button type="submit" class="btn-simpan">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditNote" class="modal-backdrop">
        <div class="modal-konten">
            <div class="modal-header">
                <h3>Edit Tugas / Catatan</h3>
                <button class="btn-tutup" onclick="tutupModalEdit()">&times;</button>
            </div>
            <form action="../auth/edit-note.php" method="POST">
                <input type="hidden" name="note_id" id="edit_note_id">
                <div class="form-grup">
                    <textarea name="isi_note" id="edit_isi_note" rows="4" placeholder="Ubah tugas kamu..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-batal" onclick="tutupModalEdit()">Batal</button>
                    <button type="submit" name="update_note" class="btn-simpan">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/home-user.js"></script>
</body>
</html>