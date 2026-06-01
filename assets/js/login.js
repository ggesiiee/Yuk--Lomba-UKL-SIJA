const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

if (togglePassword && passwordInput) {
  togglePassword.addEventListener("click", function () {
    const type =
      passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    this.style.color = type === "text" ? "#1c7fff" : "#94a3b8";
  });
}

function switchTab(tab) {
  const btnlogin = document.getElementById("btn-login");
  const btnSignUp = document.getElementById("btn-signup");
  const nameGroup = document.getElementById("name-group");
  const namaInput = document.getElementById("nama");
  const roleGroup = document.getElementById("role-group");

  const formTitle = document.getElementById("form-title");
  const formSubtitle = document.getElementById("form-subtitle");
  const submitBtn = document.getElementById("submit-btn");
  const formAction = document.getElementById("form-action");
  const formOptions = document.getElementById("form-options");
  const bottomLink = document.getElementById("bottom-link-container");

  if (tab === "signup") {
    if (btnlogin) btnlogin.classList.remove("active");
    if (btnSignUp) btnSignUp.classList.add("active");

    if (nameGroup) nameGroup.style.display = "block";
    if (namaInput) namaInput.required = true;
    if (roleGroup) roleGroup.style.display = "block";

    if (formOptions) formOptions.style.display = "none";
    if (bottomLink) bottomLink.style.display = "none";

    if (formTitle) formTitle.textContent = "Buat Akun Baru";
    if (formSubtitle)
      formSubtitle.textContent = "Sign up untuk mulai bertanding!";
    if (submitBtn) submitBtn.textContent = "Sign Up";
    if (formAction) formAction.value = "register";
  } else {
    if (btnSignUp) btnSignUp.classList.remove("active");
    if (btnlogin) btnlogin.classList.add("active");

    if (nameGroup) nameGroup.style.display = "none";
    if (namaInput) namaInput.required = false;
    if (roleGroup) roleGroup.style.display = "none";

    if (formOptions) formOptions.style.display = "flex";
    if (bottomLink) bottomLink.style.display = "block";

    if (formTitle) formTitle.textContent = "Siap Menang Hari Ini?";
    if (formSubtitle) formSubtitle.textContent = "Masuk Sekarang ke Akun mu!";
    if (submitBtn) submitBtn.textContent = "Login";
    if (formAction) formAction.value = "login";
  }
}

window.addEventListener("load", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const tabParam = urlParams.get("tab");

  if (tabParam === "signup") {
    switchTab("signup");
  } else {
    switchTab("login");
  }
});

const images = [
  "../assets/img/header-bg.png",
];
const sliderBg = document.getElementById("slider-bg");
const dots = document.querySelectorAll(".dot");
let currentIndex = 0;

if (sliderBg) {
  sliderBg.style.backgroundImage = `url('${images}')`;
}
