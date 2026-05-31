<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

$id_lomba = isset($_GET['id']) && $_GET['id'] != '' ? intval($_GET['id']) : 1;

$q = "SELECT l.*, k.nama_kategori FROM lomba l 
      LEFT JOIN kategori k ON l.kategori_id = k.kategori_id 
      WHERE l.lomba_id = $id_lomba";
$res = mysqli_query($conn, $q);

if (!$res) {
    die("ERROR QUERY LOMBA: " . mysqli_error($conn));
}

$lomba = mysqli_fetch_assoc($res) ?: [
    'nama_lomba' => 'Lomba Tidak Ditemukan', 
    'nama_kategori' => '-', 
    'tingkat' => '-',
    'status_daftar' => 'belum',
    'deadline' => date('Y-m-d')
];

$sudah_daftar = (isset($lomba['status_daftar']) && $lomba['status_daftar'] == 'sudah');

$deadline_target = $lomba['deadline'];

if ($sudah_daftar) {
    $label_status = "Deadline Tahap 1";
} else {
    $label_status = "Batas Pendaftaran";
}

$asal_halaman = isset($_GET['from']) ? $_GET['from'] : 'lomba';
if ($asal_halaman == 'lomba') {
    $link_kembali = "lomba.php";
} else {
    $link_kembali = "home.php";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lomba - Yuk! Lomba</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../assets/css/home-user.css">
    <link rel="stylesheet" href="../assets/css/detail-lomba.css">
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
                <a href="home.php" class="link-menu-biasa"><i data-feather="home"></i> Dashboard</a>
                <a href="lomba.php" class="link-menu-biasa menu-nyala"><i data-feather="file-text"></i> Lomba Aktif</a>
            </div>
        </aside>

        <main class="isi-konten-kanan">
            
            <header class="bagian-atas-header header-detail-spesifik">
                <div class="wrapper-judul-kiri">
                    <a href="<?= $link_kembali ?>" class="tombol-kembali"><i data-feather="arrow-left"></i></a>
                    <h1 class="judul-halaman-teks">Detail Lomba</h1>
                </div>
            </header>

            <section class="kartu-progres-utama">
                <div class="box-putih-isi-lomba">
                    <div class="baris-judul-lomba">
                        <h2 class="nama-lomba-detail"><?= htmlspecialchars($lomba['nama_lomba']) ?></h2>
                        <span class="badge-aktif">AKTIF</span>
                    </div>
                    <p class="sub-info-lomba">
                        <?= htmlspecialchars($lomba['nama_kategori']) ?> &bull; <?= htmlspecialchars($lomba['tingkat']) ?>
                    </p>
                </div>

                <div class="ringkasan-deadline-progres">
                    <div class="label-deadline-merah">
                        <i data-feather="clock"></i> <span>DEADLINE</span>
                    </div>

                    <div class="baris-atas-ringkasan">
                        <div class="kolom-deadline">
                            <div class="info-tanggal-detail">
                                <?php $dl = strtotime($deadline_target); ?>
                                <p class="hari-nama"><?= date('l,', $dl) ?></p>
                                <h3 class="tgl-angka"><?= date('d F', $dl) ?></h3>
                                <p class="tahun-angka"><?= date('Y', $dl) ?></p>
                            </div>
                        </div>

                        <div class="pembagi-vertikal"></div>

                            <div class="kolom-progres-checklist">
                                <h4 class="judul-mini-box">Tahapan Selesai</h4>
                                <div class="daftar-list-todo-mini" style="max-height: 110px; overflow-y: auto; padding-right: 5px;">
                                    <?php
                                    $q_progress = mysqli_query($conn, "SELECT * FROM progress WHERE lomba_id = $id_lomba AND user_id = $user_id_aktif AND presentase = 100 ORDER BY progress_id DESC");

                                    if($q_progress && mysqli_num_rows($q_progress) > 0) {
                                        while($prog = mysqli_fetch_assoc($q_progress)) {
                                    ?>
                                    <div class="item-progress-lomba">
                                        <input type="checkbox" checked class="checkbox-progress" 
                                            data-id="<?= $prog['progress_id'] ?>" 
                                            onchange="updateStatusProgress(this)" style="cursor: pointer;"> 
                                        
                                        <span class="teks-progress">
                                            <?= htmlspecialchars($prog['deskripsi']) ?>
                                        </span>
                                    </div>
                                    <?php 
                                        }
                                    } else {
                                        echo "<p class='progress-kosong'>Belum ada progress selesai.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div> 
                    
                    <?php
                    $q_hitung = mysqli_query($conn, "SELECT 
                        COUNT(*) as total, 
                        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai 
                        FROM timeline WHERE lomba_id = $id_lomba");
                    $data_hitung = mysqli_fetch_assoc($q_hitung);
                    
                    $tot_tugas = $data_hitung['total'] ? $data_hitung['total'] : 0;
                    $tot_selesai = $data_hitung['selesai'] ? $data_hitung['selesai'] : 0;
                    
                    $persen_progress = ($tot_tugas > 0) ? round(($tot_selesai / $tot_tugas) * 100) : 0;
                    ?>
                    
                    <div class="baris-progres-bawah">
                        <div class="teks-label-progres">
                            <span class="label-total">Total Progress (<?= $tot_selesai ?>/<?= $tot_tugas ?> Tahapan)</span>
                            <span class="angka-persen"><?= $persen_progress ?>%</span>
                        </div>
                        <div class="track-progres-abu">
                            <div class="isi-progres-biru" style="width: <?= $persen_progress ?>%; transition: width 0.4s ease;"></div>
                        </div>
                    </div>
            </section>

            <div class="layout-bawah-detail">
                <section class="kotak-putih-biasa flex-1">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 class="judul-sub-detail" style="margin: 0;">Tahapan Lomba</h3>
                        <button type="button" class="btn-tambah-tugas-biru" onclick="bukaModalTambahTimeline()" style="padding: 5px 12px; font-size: 0.85rem; width: auto; height: auto;">
                            <i data-feather="plus" style="width: 14px; height: 14px;"></i> Tambah
                        </button>
                    </div>
                    <div class="timeline-container">
                        <?php
                        $q_timeline = "SELECT * FROM timeline WHERE lomba_id = $id_lomba ORDER BY timeline_id ASC";
                        $res_timeline = mysqli_query($conn, $q_timeline);

                        if (!$res_timeline) {
                            die("ERROR QUERY TIMELINE: " . mysqli_error($conn));
                        }

                        if(mysqli_num_rows($res_timeline) > 0) {
                            while($tl = mysqli_fetch_assoc($res_timeline)) {
                                
                                $status_db = strtoupper($tl['status']);
                                if ($status_db == 'SELESAI') {
                                    $class_item = 'selesai';
                                    $class_tag  = 'tag-hijau';
                                    $icon       = '<i data-feather="check"></i>';
                                } elseif ($status_db == 'DALAM PROSES') {
                                    $class_item = 'proses';
                                    $class_tag  = 'tag-biru';
                                    $icon       = '';
                                } else {
                                    $class_item = 'belum';
                                    $class_tag  = 'tag-abu';
                                    $icon       = '';
                                }
                        ?>
                        <div class="timeline-item <?= $class_item ?>">
                            <div class="titik-timeline"><?= $icon ?></div>
                            <div class="konten-timeline" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                <div>
                                    <span class="tag-tahap <?= $class_tag ?>"><?= htmlspecialchars($tl['status']) ?></span>
                                    <h4><?= htmlspecialchars($tl['step']) ?></h4>
                                    <p><?= date('d M Y', strtotime($tl['created_at'])) ?></p>
                                </div>
                                
                                <div class="aksi-timeline" style="display: flex; gap: 10px;">
                                    <button type="button" 
                                            onclick="bukaModalEditTimeline(<?= $tl['timeline_id'] ?>, '<?= htmlspecialchars($tl['step'], ENT_QUOTES) ?>', '<?= htmlspecialchars($tl['status'], ENT_QUOTES) ?>', '<?= date('Y-m-d', strtotime($tl['created_at'])) ?>')" 
                                            style="background:none; border:none; color:#1c7fff; cursor:pointer;" title="Edit Tahapan">
                                        <i data-feather="edit-2" style="width: 18px; height: 18px;"></i>
                                    </button>
                                    
                                    <a href="../auth/hapus-timeline.php?id=<?= $tl['timeline_id'] ?>&lomba_id=<?= $id_lomba ?>" 
                                       onclick="return confirm('Apakah kamu yakin ingin menghapus tahapan ini?')" 
                                       style="color:#ef4444; text-decoration:none;" title="Hapus Tahapan">
                                        <i data-feather="trash-2" style="width: 18px; height: 18px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo "<p style='color: #64748b; font-size: 0.9rem;'>Timeline tahapan lomba belum diatur.</p>";
                        }
                        ?>
                    </div>
                </section>

                <div class="kolom-kanan-detail flex-1">
                    <section class="kotak-putih-biasa margin-bawah">
                        <h3 class="judul-sub-detail">Fokus Hari Ini</h3>
                        <div class="daftar-todo-detail">
                            <?php
                            $q_fokus = mysqli_query($conn, "SELECT * FROM progress WHERE lomba_id = $id_lomba AND user_id = $user_id_aktif AND presentase = 0 ORDER BY progress_id DESC");
                            
                            if($q_fokus && mysqli_num_rows($q_fokus) > 0) {
                                while($row_fokus = mysqli_fetch_assoc($q_fokus)) { 
                            ?>
                            <label class="item-todo" style="display:flex; align-items:center; gap:8px;">
                                <input type="checkbox" 
                                    data-id="<?= $row_fokus['progress_id'] ?>" 
                                    onchange="updateStatusProgress(this)">
                                <span><?= htmlspecialchars($row_fokus['deskripsi']) ?></span>
                            </label>
                            <?php 
                                }
                            } else {
                                echo "<p style='color:#888; font-size:0.9rem;'>Belum ada tugas hari ini.</p>";
                            }
                            ?>
                            <button type="button" class="btn-tambah-tugas-biru" onclick="bukaModalTugas()" style="margin-top: 10px;">
                                <i data-feather="plus"></i> Tambah Tugas
                            </button>
                        </div>
                    </section>

                    <div class="berkas-lomba-box">
                        <h3><i data-feather="paperclip"></i> Dokumen Juknis Lomba</h3>
    
                        <div class="isi-berkas">
                            <?php 
                            if (!empty($lomba['juknis'])): 
                            ?>
                            <div class="berkas-ada" id="tampilan-berkas">
                                <div class="info-berkas">
                                    <i data-feather="file" class="ikon-file"></i>
                                    <span><?= htmlspecialchars($lomba['juknis']) ?></span>
                                </div>
                                <div class="aksi-berkas">
                                    <a href="../assets/uploads/file/<?= htmlspecialchars($lomba['juknis']) ?>" target="_blank" class="btn-aksi-berkas biru">Lihat</a>
                                    <button type="button" class="btn-aksi-berkas kuning" onclick="bukaUbahBerkas()">
                                        <i data-feather="edit-2"></i>
                                    </button>
                                    <a href="../auth/hapus-berkas.php?id=<?= $id_lomba ?>" class="btn-aksi-berkas merah" onclick="return confirm('Apakah kamu yakin ingin menghapus file juknis ini?')">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </div>
                            </div>
        
                            <form action="../auth/edit-berkas.php" method="POST" enctype="multipart/form-data" id="form-ubah-berkas" style="display:none; margin-top: 15px;">
                                <input type="hidden" name="lomba_id" value="<?= $id_lomba ?>">
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="file" name="file_berkas" required class="input-file-keren">
                                    <button type="submit" class="btn-simpan"><i data-feather="save" style="width: 16px;"></i> Simpan</button>
                                    <button type="button" class="btn-batal" onclick="batalUbahBerkas()">Batal</button>
                                </div>
                                <p class="text-muted" style="font-size: 0.8rem; margin-top: 5px;">*Mengunggah file baru akan menimpa file juknis yang lama.</p>
                            </form>

                            <?php else: ?>
                            <div class="berkas-kosong">
                                <p style="color: var(--gray); margin-bottom: 12px; font-size: 0.9rem;">
                                    <i data-feather="info" style="width: 16px; margin-right: 5px; vertical-align: middle;"></i> Belum ada dokumen juknis yang dilampirkan.
                                </p>
                                <form action="../auth/tambah-berkas.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="lomba_id" value="<?= $id_lomba ?>">
                                    <input type="file" name="file_berkas" required class="input-file-keren">
                                    <button type="submit" class="btn-aksi-berkas biru">
                                        <i data-feather="upload" style="width: 16px; margin-right: 5px;"></i> Upload
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <div id="modalTambahTugas" class="modal-tugas-overlay" style="display: none;">
        <div class="modal-tugas-box">
            <div class="header-modal-tugas">
                <h3>Tambah Tugas / Catatan</h3>
                <button type="button" class="btn-close-modal" onclick="tutupModalTugas()">&times;</button>
            </div>
            
            <form action="../auth/tambah-tugas.php" method="POST">
                <input type="hidden" name="lomba_id" value="<?= $id_lomba ?>">
                
                <div class="form-group-tugas">
                    <textarea name="deskripsi" rows="4" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 5px;" placeholder="Apa yang ingin kamu kerjakan hari ini?" required></textarea>
                </div>
                
                <div class="footer-modal-tugas">
                    <button type="button" class="btn-batal-tugas" onclick="tutupModalTugas()">Batal</button>
                    <button type="submit" name="simpan_tugas" class="btn-simpan-tugas">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditTimeline" class="modal-tugas-overlay" style="display: none;">
        <div class="modal-tugas-box">
            <div class="header-modal-tugas">
                <h3>Edit Tahapan Lomba</h3>
                <button type="button" class="btn-close-modal" onclick="tutupModalEditTimeline()">&times;</button>
            </div>
            
            <form action="../auth/edit-timeline.php" method="POST">
                <input type="hidden" name="lomba_id" value="<?= $id_lomba ?>">
                <input type="hidden" name="timeline_id" id="edit_timeline_id">
                
                <div class="form-group-tugas" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #475569;">Nama Tahapan</label>
                    <input type="text" name="step" id="edit_step" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 5px;">
                </div>
                
                <div class="form-group-tugas" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #475569;">Status</label>
                    <select name="status" id="edit_status" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 5px;">
                        <option value="Selesai">Selesai</option>
                        <option value="Dalam Proses">Dalam Proses</option>
                        <option value="Akan Datang">Akan Datang</option>
                    </select>
                </div>

                <div class="form-group-tugas" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #475569;">Tanggal</label>
                    <input type="date" name="created_at" id="edit_created_at" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 5px;">
                </div>
                
                <div class="footer-modal-tugas">
                    <button type="button" class="btn-batal-tugas" onclick="tutupModalEditTimeline()">Batal</button>
                    <button type="submit" class="btn-simpan-tugas">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalTambahTimeline" class="modal-tugas-overlay" style="display: none;">
        <div class="modal-tugas-box">
            <div class="header-modal-tugas">
                <h3>Tambah Tahapan Lomba</h3>
                <button type="button" class="btn-close-modal" onclick="tutupModalTambahTimeline()">&times;</button>
            </div>
            
            <form action="../auth/tambah-timeline.php" method="POST">
                <input type="hidden" name="lomba_id" value="<?= $id_lomba ?>">
                
                <div class="form-group-tugas" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #475569;">Nama Tahapan</label>
                    <input type="text" name="step" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 5px;" placeholder="Contoh: Babak Penyisihan">
                </div>
                
                <div class="form-group-tugas" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #475569;">Status</label>
                    <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 5px;">
                        <option value="Akan Datang">Akan Datang</option>
                        <option value="Dalam Proses">Dalam Proses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <div class="form-group-tugas" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #475569;">Tanggal</label>
                    <input type="date" name="created_at" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 5px;">
                </div>
                
                <div class="footer-modal-tugas">
                    <button type="button" class="btn-batal-tugas" onclick="tutupModalTambahTimeline()">Batal</button>
                    <button type="submit" class="btn-simpan-tugas">Tambah Tahapan</button>
                </div>
            </form>
        </div>
    </div>

    <script>feather.replace();</script>
    <script src="../assets/js/detail-lomba.js"></script>
</body>
</html>