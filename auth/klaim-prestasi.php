<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_klaim'])) {
    $lomba_id = (int)$_POST['lomba_id'];
    $hasil_klaim = mysqli_real_escape_string($conn, $_POST['hasil_klaim']);
    
    $file_name  = $_FILES['file_sertifikat']['name'];
    $file_tmp   = $_FILES['file_sertifikat']['tmp_name'];
    $file_size  = $_FILES['file_sertifikat']['size'];
    $file_error = $_FILES['file_sertifikat']['error'];
    
    $ekstensi_diperbolehkan = ['pdf', 'jpg', 'jpeg', 'png'];
    $x = explode('.', $file_name);
    $ekstensi = strtolower(end($x));
    
    if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
        if ($file_size <= 10485760) {
            
            $nama_file_baru = trim(uniqid() . '.' . $ekstensi);
            $target_dir = "../assets/uploads/img/"; 
            
            if (move_uploaded_file($file_tmp, $target_dir . $nama_file_baru)) {
                
                $query_klaim = "INSERT INTO klaim_prestasi 
                                (klaim_id, user_id, lomba_id, hasil_klaim, file_sertifikat, status_validasi, tanggal_klaim) 
                                VALUES 
                                (NULL, '$user_id_aktif', '$tipe_lomba', '$hasil_klaim', '$nama_file_baru', 'Menunggu Pengecekan', NOW())";
                
                if (mysqli_query($conn, $query_klaim)) {
                    mysqli_query($conn, "UPDATE pendaftaran SET status = 'selesai' WHERE user_id = '$user_id_aktif' AND lomba_id = '$lomba_id'");
                    echo "<script>alert('Berhasil mengirimkan klaim prestasi!'); window.location.href='../user/lomba.php';</script>";
                } else {
                    $error_db = str_replace("'", "", mysqli_error($conn));
                    echo "<script>alert('Gagal menyimpan data: " . $error_db . "'); window.location.href='klaim-prestasi.php?id=$lomba_id';</script>";
                }
            } else {
                echo "<script>alert('Gagal mengunggah file!'); window.location.href='klaim-prestasi.php?id=$lomba_id';</script>";
            }
        } else {
            echo "<script>alert('Maksimal 10MB.'); window.location.href='klaim-prestasi.php?id=$lomba_id';</script>";
        }
    } else {
        echo "<script>alert('Format salah!'); window.location.href='klaim-prestasi.php?id=$lomba_id';</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lapor Hasil - Yuk! Lomba</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>

<style>
:root {
  --primary-color: #1c7fff;
  --secondary-color: #005ed8;
  --background-color: #f8fafc;
  --surface-white: #ffffff;
  --surface-color: #bedeff;
  --dark: #1e293b;
  --gray: #64748b;
  --white: #ffffff;
}

* {
  font-family: "Nunito", sans-serif;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  outline: none;
  border: none;
  text-decoration: none;
}

body {
  background-color: var(--background-color);
  color: var(--dark);
}

.claim-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 20px;
  background-color: var(--background-color);
  min-height: calc(100vh - 5rem);
}

.claim-card {
  background: var(--surface-white);
  border-radius: 16px;
  box-shadow: 0px 10px 30px rgba(28, 127, 255, 0.06);
  padding: 35px;
  width: 100%;
  max-width: 550px;
}

.claim-header {
  text-align: center;
  margin-bottom: 30px;
}

.claim-header h2 {
  color: var(--dark);
  font-size: 1.4rem;
  font-weight: 800;
  margin-bottom: 6px;
}

.claim-header p {
  color: var(--gray);
  font-size: 0.9rem;
  line-height: 1.5;
}

.input-group-modern {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 22px;
}

.input-group-modern label {
  color: var(--dark);
  font-size: 0.9rem;
  font-weight: 700;
}

.input-wrapper-icon {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper-icon svg {
  position: absolute;
  left: 14px;
  color: var(--gray);
  width: 18px;
  height: 18px;
  pointer-events: none;
}

.input-wrapper-icon select,
.input-wrapper-icon input[type="file"] {
  width: 100%;
  padding: 12px 14px 12px 42px;
  background-color: #f8fafc;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  color: var(--dark);
  font-size: 0.95rem;
  font-family: "Nunito", sans-serif;
  transition: all 0.2s ease;
}

.input-wrapper-icon select:focus {
  border-color: var(--primary-color);
  background-color: var(--surface-white);
  box-shadow: 0 0 0 4px rgba(28, 127, 255, 0.08);
}

.input-wrapper-icon input[type="file"] {
  padding: 9px 14px 9px 42px;
  cursor: pointer;
}

.input-help-text {
  color: var(--gray);
  font-size: 0.8rem;
  margin-top: 2px;
}

.claim-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #f1f5f9;
}

.btn-claim-submit {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background-color: var(--primary-color);
  color: var(--surface-white);
  border: none;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-claim-submit:hover {
  background-color: var(--secondary-color, #005ed8);
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(28, 127, 255, 0.2);
}

.btn-claim-submit svg {
  width: 16px;
  height: 16px;
  stroke-width: 3;
}

.btn-claim-cancel {
  padding: 12px 20px;
  background-color: transparent;
  color: var(--gray);
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  text-align: center;
}

.btn-claim-cancel:hover {
  background-color: #f1f5f9;
  color: var(--dark);
}
</style>
</head>
<body> <div class="claim-container">
    <div class="claim-card">
        <div class="claim-header">
            <h2>Form Pelaporan Hasil & Prestasi</h2>
            <p>Isi detail capaian kompetisi siswa dengan benar untuk divalidasi oleh guru.</p>
        </div>

        <?php $lomba_id = isset($_GET['id']) ? intval($_GET['id']) : 0; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="claim-form">
            <input type="hidden" name="lomba_id" value="<?= $lomba_id ?>">

            <div class="input-group-modern">
                <label for="hasil_klaim">Capaian / Juara</label>
                <div class="input-wrapper-icon">
                    <i data-feather="bookmark"></i>
                    <select name="hasil_klaim" id="hasil_klaim" required>
                        <option value="">-- Pilih Pencapaian --</option>
                        <option value="Juara 1">Juara 1</option>
                        <option value="Juara 2">Juara 2</option>
                        <option value="Juara 3">Juara 3</option>
                        <option value="Harapan 1">Juara Harapan 1</option>
                        <option value="Harapan 2">Juara Harapan 2</option>
                        <option value="Harapan 3">Juara Harapan 3</option>
                        <option value="Peserta">Apresiasi / Peserta</option>
                    </select>
                </div>
            </div>

            <div class="input-group-modern">
                <label for="file_sertifikat">Dokumen Bukti / Sertifikat</label>
                <div class="input-wrapper-icon">
                    <i data-feather="file-text"></i>
                    <input type="file" name="file_sertifikat" id="file_sertifikat" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                <small class="input-help-text">Format yang didukung: PDF, JPG, PNG (Maksimal 10MB)</small>
            </div>

            <div class="claim-actions">
                <a href="home.php" class="btn-claim-cancel">Batal</a>
                <button type="submit" name="kirim_klaim" class="btn-claim-submit">
                    <i data-feather="check"></i>Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
  </script>
  
  <script src="../assets/js/lomba.js"></script>
</body>
</html>