let formBerubah = false;
const formLomba = document.querySelector("form");

formLomba.addEventListener("input", () => {
  formBerubah = true;
});

function handleBatal() {
  if (formBerubah) {
    if (
      confirm(
        "Kamu sudah mengisi data, yakin mau keluar? Data yang kamu isi akan hilang.",
      )
    ) {
      window.location.href = "home.php";
    }
  } else {
    window.location.href = "home.php";
  }
}

formLomba.addEventListener("submit", () => {
  formBerubah = false;
});

document.getElementById("juknis").addEventListener("change", function (e) {
  const areaDrop = document.querySelector(".area-drop");
  const spanTeks = areaDrop.querySelector("span");
  const icon = areaDrop.querySelector("i");

  if (this.files && this.files.length > 0) {
    const namaFile = this.files[0].name;

    spanTeks.innerText = "File terpilih: " + namaFile;
    areaDrop.classList.add("file-terpilih");

    icon.setAttribute("data-feather", "check-circle");
    feather.replace();
  } else {
    spanTeks.innerText = "Klik untuk pilih file atau drag ke sini";
    areaDrop.classList.remove("file-terpilih");
    icon.setAttribute("data-feather", "upload-cloud");
    feather.replace();
  }
});
