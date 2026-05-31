<?php
session_start();
include "include\koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Yuk! Lomba</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
      rel="stylesheet"
    />

    <script src="https://unpkg.com/feather-icons"></script>

    <link rel="stylesheet" href="assets/css/style.css" />
  </head>

  <body>
    <?php include "include/navbar.php"; ?>

    <section class="hero" id="home">
      <div class="content">
        <h1>Well Planed, Well Achieved</h1>
        <span>Sekarang manajemen lomba lebih mudah bersama dengan kami.</span>
        <p>
          <span>'Yuk! Lomba'</span> adalah sebuah platform aplikasi yang
          menghadirkan fitur-fitur seperti Monitoring lomba, Pengingat Deadline,
          To Do List untuk membantumu lebih mudah memulai langkah pengerjaan
          lomba, Pencarian guru pembimbing, dan masih banyak lagi!
        </p>
        <a href="user/profil/profil.php" class="cta">Manage Now!</a>
      </div>
    </section>

<section class="fitur">
  <h2>Tentang Kami</h2> 
  <h1>Latar Belakang</h1>

  <div class="konten-deskripsi">
    <p>
      Banyak siswa memiliki beragam potensi dalam berbagai bidang perlombaan. Namun, jadwal aktivitas sehari-hari yang padat menyebabkan mereka kesulitan dalam mencari waktu untuk mengikuti perlombaan dan bahkan seringkali lupa deadline perlombaan tersebut.
      <br><br>
      Disisi lain, guru ingin para siswa nya memiliki performa perlombaan yang baik dan berkualitas. Akan tetapi, tidak semua siswa merasa percaya diri dengan performa mereka, sehingga guru memiliki keterbatasan data untuk mengetahui performa perombaan siswa sebenarnya.
    </p>
    
    <img src="assets/img/about.jpg" alt="Tentang Kami" class="gambar-about">
  </div>
</section>

    <section class="steps">
      <h2>Fitur</h2>

      <div class="konten-steps">
        <img src="assets/img/fitur.png" alt="Tentang Kami" class="gambar-steps">

    <div class="daftar-fitur-kanan">
      <h1>Jelajahi Cara Efektif dan Seru Dalam Memanajemen Perlombaan</h1>
      <div class="item-fitur">
        <i data-feather="check-circle" class="ikon-biru"></i>
        <span>Pantau deadline mulai dari pendaftaran hingga berlombaan dimulai</span>
      </div>
      
      <div class="item-fitur">
        <i data-feather="check-circle" class="ikon-biru"></i>
        <span>Progress terakhirmu akan selalu tersimpan dan siap dilanjutkan lagi</span>
      </div>
      
      <div class="item-fitur">
        <i data-feather="check-circle" class="ikon-biru"></i>
        <span>Buat catatan tugas terkumpul dalam satu tempat</span>
      </div>
      
      <div class="item-fitur">
        <i data-feather="check-circle" class="ikon-biru"></i>
        <span>Lebih mudah terhubung dengan lomba rekomendasi guru</span>
      </div>
    </div>
  </div>
      </div>

    </section>

<section class="panduan-section">
    <div class="badge-container">
        <span class="badge-panduan">Panduan</span>
    </div>

    <h2 class="judul-panduan">Ikuti 4 Langkah Simple untuk Efektifitas Manajemen Lomba mu!</h2>

    <div class="grid-langkah">
        <div class="kartu-langkah">
            <h3 class="nomor-langkah">1</h3>
            <h4 class="sub-judul-langkah">Buat Akun</h4>
            <p class="desc-langkah">Daftar dan Login untuk mulai mengakses seluruh fitur Yuk! Lomba</p>
        </div>

        <div class="kartu-langkah">
            <h3 class="nomor-langkah">2</h3>
            <h4 class="sub-judul-langkah">Tambah Lomba</h4>
            <p class="desc-langkah">Awali dengan menambahkan lomba yang ingin/sedang di ikuti</p>
        </div>

        <div class="kartu-langkah">
            <h3 class="nomor-langkah">3</h3>
            <h4 class="sub-judul-langkah">Mulai Manajemen</h4>
            <p class="desc-langkah">Jelajahi fitur Yuk! Lomba yang akan mempermudah manajemen lombamu</p>
        </div>

        <div class="kartu-langkah">
            <h3 class="nomor-langkah">4</h3>
            <h4 class="sub-judul-langkah">Terhubung Dengan Guru</h4>
            <p class="desc-langkah">Lebih mudah dalam menjangkau perlombaan rekomendasi guru</p>
        </div>
    </div>
</section>

<footer class="footer-utama">
    <div class="footer-konten-atas">
        <div class="footer-brand">
            <i data-feather="award" class="ikon-tropi"></i>
            <span class="nama-brand">Yuk! Lomba</span>
        </div>
        <p class="footer-deskripsi">
            Tujuan utama kami adalah untuk membantu pelajar merencanakan, memantau, dan menyelesaikan perlombaan secara terstruktur.
        </p>
    </div>

    <hr class="footer-divider">

    <div class="footer-konten-bawah">
        <p class="copyright">&copy; 2026 Yuk! Lomba. All rights reserved.</p>
        <p class="developer">Developed by Ramadhani Gesya Nuraini | SMK Telkom Sidoarjo</p>
    </div>
</footer>

    <script>
      feather.replace();
    </script>

    <script src="js/script.js"></script>
  </body>
</html>
