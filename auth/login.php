<?php
session_start();
include "../include/koneksi.php";

if(isset($_POST['submit_form'])){
  $action = $_POST['action'];
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];

  if($action === 'login') {
      $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

      if(mysqli_num_rows($result) === 1){
        $user = mysqli_fetch_assoc($result);

          if(password_verify($password, $user['passwords'])){
            $_SESSION['login'] = true;
            
            $_SESSION['user_id'] = $user['user_id']; 
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            
            if($user['role'] === 'guru') {
                header("Location: ../admin/home.php"); 
            } else {
                header("Location: ../user/home.php"); 
            }
            exit;
          }
      }
      $error = "Email atau Password salah!";
  } 
  
  elseif ($action === 'register') {
      $nama = mysqli_real_escape_string($conn, $_POST['nama']);
      $role = mysqli_real_escape_string($conn, $_POST['role']); 
      
      $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
      if(mysqli_fetch_assoc($check_email)) {
          $error = "Email sudah terdaftar! Silakan Log In.";
      } else {
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);
          
          $query = "INSERT INTO users (nama, email, passwords, role) VALUES ('$nama', '$email', '$hashed_password', '$role')";
          
          if(mysqli_query($conn, $query)) {
              $success = "Akun berhasil dibuat! Silakan Log In.";
          } else {
              $error = "Gagal mendaftar. Silakan coba lagi: " . mysqli_error($conn);
          }
      }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Yuk! Lomba</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet" />
    
    <script src="https://unpkg.com/feather-icons"></script>

    <link rel="stylesheet" href="../assets/css/style-login.css" />
  </head>

  <body>
<div class="login-wrapper">
  <div class="left-side" id="slider-bg">
    <div class="logo-container">
      <div class="logo-icon">
        <img src="/project_ukl/assets/img/logoo.svg" alt="Logo" style="width: 100%; height: 100%;">
      </div>
      <a href="/project_ukl/index.php" class="logo-text">Yuk! lomba</a>
    </div>

    <div class="hero-content">
      <h1>Wujudkan Mimpi Prestasimu</h1>
      <p>Kemenangan didapat dari manajemen lomba yang baik, semua jadi lebih mudah karena kita akan menemani langkahmu sampai menang.</p>
        
      <div class="slider-dots">
        <div class="dot active" onclick="changeSlide(0)"></div>
      </div>
    </div>
  </div>

  <div class="right-side">
    <div class="form-container">
      <div class="form-header">
        <h2 id="form-title">Siap Menang Hari Ini?</h2>
        <p id="form-subtitle">Masuk Sekarang ke Akun mu!</p>
            
        <?php if(isset($error)) : ?>
          <p style="color: #ef4444; font-size: 14px; margin-top: 10px; background: #fee2e2; padding: 10px; border-radius: 8px; text-align:left;"><?= $error ?></p>
        <?php endif; ?>
        <?php if(isset($success)) : ?>
          <p style="color: #10b981; font-size: 14px; margin-top: 10px; background: #d1fae5; padding: 10px; border-radius: 8px; text-align:left;"><?= $success ?></p>
        <?php endif; ?>
      </div>

      <div class="auth-toggle">
        <button type="button" class="toggle-btn active" id="btn-login" onclick="switchTab('login')">Log In</button>
        <button type="button" class="toggle-btn" id="btn-signup" onclick="switchTab('signup')">Sign Up</button>
      </div>

      <form action="" method="POST">
        <input type="hidden" name="action" id="form-action" value="login">

        <div class="form-group" id="name-group" style="display: none;">
          <label for="nama">Nama</label>
          <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama lengkap">
        </div>
        
        <div class="form-group" id="role-group" style="display: none;">
          <label for="role">Daftar Sebagai</label>
          <select id="role" name="role" class="form-control">
              <option value="siswa">Siswa (Peserta Lomba)</option>
              <option value="guru">Guru Pembimbing</option>
          </select>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan Email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <input type="password" id="password" name="password" class="form-control" placeholder="********" required>
            <svg id="togglePassword" class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="cursor: pointer;">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
          </div>
        </div>

        <button type="submit" name="submit_form" id="submit-btn" class="btn-login">Login</button>
      </form>
    </div>
  </div>

    <script>
      feather.replace();
    </script>

    <script src="../assets/js/login.js"></script>
  </body>
</html>