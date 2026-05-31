feather.replace();

const filterKategori = document.getElementById("filterKategori");
const filterTingkat = document.getElementById("filterTingkat");
const filterStatus = document.getElementById("filterStatus");
const semuaKartu = document.querySelectorAll(".kartu-item");
const pesanKosong = document.getElementById("pesanDataKosong");

function jalankanFilter() {
  let jumlahTerlihat = 0;
  const nilaiKategori = filterKategori.value;
  const nilaiTingkat = filterTingkat.value;
  const nilaiStatus = filterStatus.value;

  semuaKartu.forEach((kartu) => {
    const katKartu = kartu.getAttribute("data-kategori");
    const tingKartu = kartu.getAttribute("data-tingkat");
    const statKartu = kartu.getAttribute("data-status");

    const cocokKategori =
      nilaiKategori === "semua" || katKartu === nilaiKategori;
    const cocokTingkat = nilaiTingkat === "semua" || tingKartu === nilaiTingkat;
    const cocokStatus = nilaiStatus === "semua" || statKartu === nilaiStatus;

    if (cocokKategori && cocokTingkat && cocokStatus) {
      kartu.style.display = "flex";
      jumlahTerlihat++;
    } else {
      kartu.style.display = "none";
    }
  });

  if (jumlahTerlihat === 0) {
    pesanKosong.style.display = "block";
  } else {
    pesanKosong.style.display = "none";
  }
}

filterKategori.addEventListener("change", jalankanFilter);
filterTingkat.addEventListener("change", jalankanFilter);
filterStatus.addEventListener("change", jalankanFilter);
