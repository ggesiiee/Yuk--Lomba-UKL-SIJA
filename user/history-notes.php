<?php
require_once '../include/session.php';
require_once '../include/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>History Notes - Yuk! Lomba</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
  
  <link rel="stylesheet" href="../assets/css/home-user.css">

  <style>
    .header-detail-spesifik {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      background: var(--surface-white);
      padding: 15px 25px;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
      margin-bottom: 15px;
    }

    .wrapper-judul-kiri {
      display: flex;
      align-items: center;
      gap: 15px;
    } 

    .judul-halaman-teks {
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--dark);
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
      color: var(--gray);
      background: white;
      transition: 0.3s;
    }

    .tombol-kembali:hover {
      background: #f1f5f9;
      color: var(--primary-color);
    }

    .kontainer-history {
      background: transparent;
      padding: 0; 
    }

    .header-teks-history {
      margin-bottom: 25px;
      padding-left: 5px;
    }

    .wrapper-list-tugas {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .kartu-tugas-modern {
      display: flex;
      align-items: center;
      background: #ffffff;
      padding: 16px 20px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
      transition: transform 0.2s;
      border: 1px solid rgba(0,0,0,0.02);
    }

    .kartu-tugas-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
    }

    .kotak-ikon-tugas {
      width: 50px;
      height: 50px;
      background-color: #f1f5f9;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
      flex-shrink: 0;
    }

    .isi-tugas {
      flex-grow: 1;
      margin-left: 18px;
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .isi-tugas h4 {
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--dark);
      margin: 0;
    }

    .badge-kapsul {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background-color: #e6fffa;
      color: #319795;
      padding: 4px 12px;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 700;
      width: fit-content;
    }

    .badge-kapsul svg {
      width: 13px;
      height: 13px;
    }

    .status-kanan {
      margin-left: 15px;
    }

    .tampilan-kosong {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 16px;
      color: var(--gray);
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
        <a href="home.php" class="link-menu-biasa">
          <i data-feather="home"></i> Dashboard
        </a>
        <a href="history-notes.php" class="link-menu-biasa menu-nyala">
          <i data-feather="clock"></i> History Notes
        </a>
      </div>
    </aside>

    <main class="isi-konten-kanan">
        
      <header class="bagian-atas-header header-detail-spesifik">
        <div class="wrapper-judul-kiri">
          <a href="home.php" class="tombol-kembali"><i data-feather="arrow-left"></i></a>
          <h1 class="judul-halaman-teks">History Notes</h1>
        </div>
      </header>

      <div class="kontainer-history">
        <div class="wrapper-list-tugas">
          <?php
          $q_history = mysqli_query($conn, "SELECT * FROM notes WHERE user_id = $user_id_aktif AND status = 1 ORDER BY tanggal_selesai DESC, created_at DESC");
          
          if ($q_history && mysqli_num_rows($q_history) > 0) {
              while ($row_hist = mysqli_fetch_assoc($q_history)) {
                  $tgl_selesai = isset($row_hist['tanggal_selesai']) ? date('d M Y', strtotime($row_hist['tanggal_selesai'])) : '-';
                  ?>
                  
                  <div class="kartu-tugas-modern">
                    <div class="kotak-ikon-tugas">
                      <i data-feather="file-text"></i>
                    </div>

                    <div class="isi-tugas">
                      <h4><?= htmlspecialchars($row_hist['isi_note']) ?></h4>
                      <div class="badge-kapsul">
                        <i data-feather="calendar"></i>
                        <span>Selesai pada: <?= $tgl_selesai ?></span>
                      </div>
                    </div>

                    <div class="status-kanan">
                      <div class="badge-kapsul">
                        <i data-feather="check-circle"></i>
                        <span>Selesai</span>
                      </div>
                    </div>
                  </div>

                  <?php
              }
          } else {
              ?>
              <div class="tampilan-kosong">
                <i data-feather="info" style="width: 48px; height: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <p>Belum ada history tugas yang diselesaikan.</p>
              </div>
              <?php
          }
          ?>
        </div>
      </div>

    </main>
  </div>

  <script>
    feather.replace();
  </script>
</body>
</html>