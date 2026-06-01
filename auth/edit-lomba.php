<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

$id_lomba = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_lomba === 0) {
    echo "<script>alert('ID Lomba tidak valid!'); window.location.href='../user/lomba.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_edit'])) {
    
    $nama_lomba        = mysqli_real_escape_string($conn, $_POST['nama_lomba'] ?? '');
    $kategori_id       = isset($_POST['kategori_id']) ? intval($_POST['kategori_id']) : 0;
    $tingkat           = mysqli_real_escape_string($conn, $_POST['tingkat'] ?? '');
    $status_daftar     = mysqli_real_escape_string($conn, $_POST['status_daftar'] ?? 'belum');
    $biaya_pendaftaran = isset($_POST['biaya_pendaftaran']) ? intval($_POST['biaya_pendaftaran']) : 0;
    $deskripsi         = mysqli_real_escape_string($conn, $_POST['deskripsi'] ?? '');

    $deadline_pendaftaran = !empty($_POST['deadline_pendaftaran']) ? "'" . mysqli_real_escape_string($conn, $_POST['deadline_pendaftaran']) . "'" : "NULL";
    $deadline             = !empty($_POST['deadline']) ? "'" . mysqli_real_escape_string($conn, $_POST['deadline']) . "'" : "NULL";

    if ($kategori_id === 0 || $tingkat === '') {
        $error_msg = "Kategori dan Tingkat Lomba wajib dipilih!";
    } else {
        $query_update = "UPDATE lomba SET 
                         nama_lomba = '$nama_lomba', 
                         kategori_id = $kategori_id, 
                         tingkat = '$tingkat', 
                         status_daftar = '$status_daftar',
                         biaya_pendaftaran = $biaya_pendaftaran, 
                         deadline_pendaftaran = $deadline_pendaftaran,
                         deadline = $deadline,
                         deskripsi = '$deskripsi'
                         WHERE lomba_id = $id_lomba";

        if (mysqli_query($conn, $query_update)) {
            echo "<script>alert('Data lomba berhasil diperbarui!'); window.location.href='../user/lomba.php';</script>";
            exit();
        } else {
            $error_msg = "Gagal memperbarui data";
        }
    }
}

$q_lomba = mysqli_query($conn, "SELECT * FROM lomba WHERE lomba_id = $id_lomba");
if (!$q_lomba || mysqli_num_rows($q_lomba) == 0) {
    echo "<script>alert('Data lomba tidak ditemukan!'); window.location.href='../user/lomba.php';</script>";
    exit();
}
$dt_lomba = mysqli_fetch_assoc($q_lomba);

$status_database = isset($dt_lomba['status_daftar']) ? $dt_lomba['status_daftar'] : 'belum';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lomba - Yuk! Lomba</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    
    <link rel="stylesheet" href="../assets/css/home-user.css">
    
    <style>
        .header-detail-spesifik {
            display: flex;
            align-items: center;
            justify-content: space-between; 
            width: 100%;
            max-width: 800px;
        }

        .wrapper-judul-kiri {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .judul-halaman-teks {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--dark, #1e293b);
            margin: 0;
        }

        .tombol-kembali {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            background: white;
            transition: 0.3s;
        }

        .tombol-kembali:hover {
            background: #f1f5f9;
            color: var(--primary-color, #1c7fff);
        }

        .btn-progress { 
            background-color: #10b981; 
            color: white; 
            padding: 10px 18px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 700; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            font-size: 0.9rem; 
            transition: 0.2s; 
        }
        .btn-progress:hover { background-color: #059669; color: white;}

        .form-container {
            background-color: var(--surface-white, #ffffff);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            max-width: 800px;
            margin-top: 20px;
            border: 1px solid #f1f5f9;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #b91c1c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }
        
        .input-grup { 
            margin-bottom: 20px; 
            width: 100%; 
        }

        .input-grup label { 
            display: block; 
            font-weight: 700; 
            color: #334155; 
            margin-bottom: 8px; 
            font-size: 0.95rem; 
        }

        .input-grup input[type="text"], .input-grup input[type="number"], .input-grup input[type="date"], .input-grup select, .input-grup textarea {
            width: 100%; 
            padding: 12px 15px; 
            border: 1px solid #cbd5e1; 
            border-radius: 10px; 
            font-family: 'Nunito', sans-serif; 
            font-size: 0.95rem; 
            background-color: #f8fafc; 
            transition: 0.2s; 
            box-sizing: border-box;
        }

        .input-grup input:focus, .input-grup select:focus, .input-grup textarea:focus { 
            border-color: #1c7fff; 
            background-color: #ffffff; 
            outline: none; 
        }
        
        .dua-kolom { 
            display: flex; 
            gap: 20px; 
        }

        .dua-kolom .input-grup { 
            flex: 1; 
        }
        
        .btn-simpan { 
            background-color: #1c7fff; 
            color: white; 
            border: none; 
            padding: 14px 24px; 
            border-radius: 10px; 
            font-weight: 700; 
            font-size: 1rem; 
            cursor: pointer; 
            transition: 0.2s; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            margin-top: 10px; 
        }

        .btn-simpan:hover { 
            background-color: #005ed8; 
        }
    </style>
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
                <a href="../user/home.php" class="link-menu-biasa"><i data-feather="home"></i> Dashboard</a>
                <a href="../user/lomba.php" class="link-menu-biasa menu-nyala"><i data-feather="file-text"></i> Lomba</a>
            </div>
        </aside>

        <main class="isi-konten-kanan">
            <header class="bagian-atas-header header-detail-spesifik">
                <div class="wrapper-judul-kiri">
                    <a href="../user/lomba.php" class="tombol-kembali"><i data-feather="arrow-left"></i></a>
                    <h1 class="judul-halaman-teks">Edit Lomba</h1>
                </div>
            </header>

            <?php if(isset($error_msg)): ?>
                <div class="alert-error">
                    <i data-feather="alert-circle"></i> <?= $error_msg ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form action="" method="POST">
                    
                    <div class="input-grup">
                        <label>Nama Lomba</label>
                        <input type="text" name="nama_lomba" value="<?= htmlspecialchars($dt_lomba['nama_lomba']) ?>" required>
                    </div>

                    <div class="dua-kolom">
                        <div class="input-grup">
                            <label>Kategori</label>
                            <select name="kategori_id" required>
                                <option value="" disabled <?= empty($dt_lomba['kategori_id']) ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                                <?php
                                $q_kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                while($kat = mysqli_fetch_assoc($q_kategori)) {
                                    $selected = ($kat['kategori_id'] == $dt_lomba['kategori_id']) ? 'selected' : '';
                                    echo "<option value='{$kat['kategori_id']}' $selected>{$kat['nama_kategori']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="input-grup">
                            <label>Tingkat Lomba</label>
                            <select name="tingkat" required>
                                <option value="" disabled <?= empty($dt_lomba['tingkat']) ? 'selected' : '' ?>>-- Pilih Tingkat --</option>
                                <?php
                                $tingkatan = ['Sekolah', 'Kota/Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];
                                foreach ($tingkatan as $t) {
                                    $selected = ($t == $dt_lomba['tingkat']) ? 'selected' : '';
                                    echo "<option value='$t' $selected>$t</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="dua-kolom">
                        <div class="input-grup">
                            <label>Status Pendaftaran</label>
                            <div class="opsi-radio" style="display: flex; gap: 20px; margin-top: 10px;">
                                <label><input type="radio" name="status_daftar" value="sudah" id="status_sudah" <?= ($status_database == 'sudah') ? 'checked' : '' ?> required> Sudah Daftar</label>
                                <label><input type="radio" name="status_daftar" value="belum" id="status_belum" <?= ($status_database == 'belum') ? 'checked' : '' ?> required> Belum Daftar</label>
                            </div>
                        </div>

                        <div class="input-grup">
                            <label>Biaya Pendaftaran (Rp)</label>
                            <input type="number" name="biaya_pendaftaran" value="<?= htmlspecialchars($dt_lomba['biaya_pendaftaran']) ?>" required min="0">
                        </div>
                    </div>

                    <div class="input-grup" id="grup-deadline-daftar">
                        <label>Tanggal Deadline Pendaftaran</label>
                        <input type="date" name="deadline_pendaftaran" id="input_daftar" 
                               value="<?= (!empty($dt_lomba['deadline_pendaftaran']) && $dt_lomba['deadline_pendaftaran'] != '0000-00-00') ? date('Y-m-d', strtotime($dt_lomba['deadline_pendaftaran'])) : '' ?>">
                    </div>

                    <div class="input-grup" id="grup-deadline-tahap1" style="display: none;">
                        <label>Tanggal Deadline Tahap 1</label>
                        <input type="date" name="deadline" id="input_tahap1" 
                               value="<?= (!empty($dt_lomba['deadline']) && $dt_lomba['deadline'] != '0000-00-00') ? date('Y-m-d', strtotime($dt_lomba['deadline'])) : '' ?>">
                    </div>

                    <div class="input-grup">
                        <label>Deskripsi Singkat Lomba</label>
                        <textarea name="deskripsi" rows="4"><?= htmlspecialchars($dt_lomba['deskripsi'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" name="simpan_edit" class="btn-simpan">
                        Simpan Perubahan <i data-feather="check-circle" style="margin-left:5px;"></i>
                    </button>

                </form>
            </div>
        </main>
    </div>

    <script>
        feather.replace();

        const radioSudah = document.getElementById('status_sudah');
        const radioBelum = document.getElementById('status_belum');
        
        const grupDaftar = document.getElementById('grup-deadline-daftar');
        const grupTahap1 = document.getElementById('grup-deadline-tahap1');
        const inputDaftar = document.getElementById('input_daftar');
        const inputTahap1 = document.getElementById('input_tahap1');

        function sesuaikanLabelDeadline() {
            if (radioSudah.checked) {
                grupTahap1.style.display = 'block';
                grupDaftar.style.display = 'none';
                inputTahap1.setAttribute('required', 'required');
                inputDaftar.removeAttribute('required');
            } else {
                grupDaftar.style.display = 'block';
                grupTahap1.style.display = 'none';
                inputDaftar.setAttribute('required', 'required');
                inputTahap1.removeAttribute('required');
            }
        }
        
        sesuaikanLabelDeadline();
        
        radioSudah.addEventListener('change', sesuaikanLabelDeadline);
        radioBelum.addEventListener('change', sesuaikanLabelDeadline);
    </script>
</body>
</html>