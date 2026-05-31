let formBerubah = false;
const formLomba = document.querySelector("form");

// Deteksi jika ada perubahan input agar muncul peringatan saat batal
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

// Perbaikan Interaksi Upload Juknis
document.getElementById("juknis").addEventListener("change", function (e) {
  const areaDrop = document.querySelector(".area-drop");
  const spanTeks = areaDrop.querySelector("span");
  const icon = areaDrop.querySelector("i");

  // Perbaikan: Pastikan properti files valid
  if (this.files && this.files.length > 0) {
    // KOREKSI UTAMA: Tambahkan [0] setelah this.files untuk mengambil file pertama
    const namaFile = this.files[0].name;

    spanTeks.innerText = "File terpilih: " + namaFile;
    areaDrop.classList.add("file-terpilih");

    // Ganti ikon menjadi check-circle
    icon.setAttribute("data-feather", "check-circle");
    feather.replace();
  } else {
    // Jika batal pilih file
    spanTeks.innerText = "Klik untuk pilih file atau drag ke sini";
    areaDrop.classList.remove("file-terpilih");
    icon.setAttribute("data-feather", "upload-cloud");
    feather.replace();
  }
});
