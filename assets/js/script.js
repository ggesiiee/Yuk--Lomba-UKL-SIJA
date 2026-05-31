// Toggle class active
const navbarNav = document.querySelector(".navbar-nav");

// Ketika hamburger menu diklik
document.querySelector("#hamburger").onclick = () => {
  navbarNav.classList.toggle("active");
};

// Klik di luar sidebar untuk menghilangkan nav
const hamburger = document.querySelector("#hamburger");

document.addEventListener("click", function (e) {
  if (!hamburger.contains(e.target) && !navbarNav.contains(e.target)) {
    navbarNav.classList.remove("active");
  }
});

timeline(document.querySelectorAll(".timeline"));

window.onload = function () {
  const urlParams = new URLSearchParams(window.location.search);
  const tabParam = urlParams.get("tab");

  if (tabParam === "signup") {
    switchTab("signup");
  } else {
    switchTab("login");
  }
};
