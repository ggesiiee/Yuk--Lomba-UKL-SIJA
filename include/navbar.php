<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <head>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
      rel="stylesheet"
    />

    <script src="https://unpkg.com/feather-icons"></script>
    <style>
:root {
  --primary-color: #1c7fff;
  --secondary-color: #005ed8;
  --background-color: #f0f2f5;
  --urface-color: #eaf4ff;
  --dark: #1e1e1e;
  --gray: #777;
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
  font-family: "Nunito", sans-serif;
  color: var(--primary-color);
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 7%;
  gap: 50px;
  position: fixed;
  background-color: #fff;
  top: 0;
  left: 0;
  right: 0;
  z-index: 9999;
}

.navbar img {
  width: 25px;
  height: 25px;
}

.navbar .navbar-logo {
  margin-left: -200px;
  font-size: 1.5rem;
  font-weight: 900;
  color: var(--primary-color);
}

.navbar .navbar-nav a {
  color: var(--primary-color);
  display: inline-block;
  font-size: 1rem;
  gap: 50px;
  font-weight: 700;
  margin-left: 2rem;
}

.navbar .navbar-nav a::after {
  content: "";
  display: block;
  width: 0;
  height: 2px;
  background-color: var(--primary-color);
  transition: 0.3s;
}

.navbar .navbar-nav a:hover::after {
  width: 100%;
}

.navbar .navbar-extra a {
  color: var(--primary-color);
  margin: 2px;
}

.navbar .navbar-extra a:hover {
  color: var(--secondary-color);
  transition: 0.3s;
}

.navbar .navbar-extra .btn-register {
  font-weight: 700;
}

.navbar .navbar-extra .btn-register:hover {
  text-shadow: var(--primary-color) 0px 0px 40px;
}

.navbar .navbar-extra .btn-login {
  justify-content: right;
  padding: 0.5rem 1rem;
  background-color: var(--primary-color);
  color: #fff;
  border-radius: 100px;
  font-weight: 700;
}

.navbar .navbar-extra .btn-login:hover {
  color: #fff;
  background-color: var(--secondary-color);
  transition: 0.3s;
}

#hamburger {
  display: none;
}

</style>
</head>
<body>
    
    <nav class="navbar" style="gap: 50px;">
      <img src="/project_ukl/assets/img/logoo.svg">
      <a href="/project_ukl/user/home.php" class="navbar-logo">Yuk! Lomba</a>

      <div class="navbar-nav">
        <a href="/project_ukl/user/home.php">Home</a>
        <a href="/project_ukl/user/lomba.php">Lomba</a>
        <a href="/project_ukl/admin/home.php">Guru</a>
        <a href="/project_ukl/user/profil.php">Profil</a>
      </div>

      <div class="navbar-extra">
        <a href="/project_ukl/auth/login.php" class="btn-login">Log In</a>
        <a href="/project_ukl/auth/login.php?tab=signup" class="btn-register">Sign Up</a>
        <a href="#" id="hamburger"><i data-feather="menu"></i></a>
      </div>
    </nav>

    <script>
      feather.replace();
    </script>

    <script src="js/script.js"></script>
</nav>
</body>
</html>