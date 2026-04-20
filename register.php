<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    	<div class="form-box register">
		<form action="register.php" method="post">
			<h1>Register</h1>
			<div class="input-box">
				<input type="text" placeholder="Username" name="username" required />
				<i data-feather="user"></i>
			</div>
			<div class="input-box">
				<input type="email" placeholder="Email" name="email" required />
				<i data-feather="mail"></i>
			</div>
			<div class="input-box">
				<input type="password" placeholder="Password" name="password" required />
				<i data-feather="lock"></i>
			</div>
			<button type="submit" class="btn">Registrasi</button>
			<p>atau registrasi dengan cara lain</p>
			<div class="social-icons">
				<a href="#"><i data-feather="chrome"></i></a>
				<a href="#"><i data-feather="facebook"></i></a>
				<a href="#"><i data-feather="twitter"></i></a>
				<a href="#"><i data-feather="github"></i></a>
			</div>
		</form>
	</div>

		<div class="toggle-panel toggle-right">
			<h1>Selamat Datang Kembali!</h1>
			<p>Sudah punya akun?</p>
			<button class="btn login-btn">Login</button>

        <!-- Feather Icons -->
        <script src="https://unpkg.com/feather-icons"></script>
        <script>
            feather.replace();
        </script>

        
</body>
</html>