function updateStatusProgress(elemenCheckbox) {
  let progressId = elemenCheckbox.getAttribute("data-id");
  let statusCentang = elemenCheckbox.checked ? 1 : 0;

  fetch("../auth/update-tugas.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "id=" + progressId + "&status=" + statusCentang,
  })
    .then((response) => response.text())
    .then((data) => {
      location.reload();
    })
    .catch((error) => {
      console.error("Terjadi kesalahan:", error);
      elemenCheckbox.checked = !elemenCheckbox.checked;
      alert("Gagal menyimpan status tugas!");
    });
}

function bukaUbahBerkas() {
  document.getElementById("tampilan-berkas").style.display = "none";
  document.getElementById("form-ubah-berkas").style.display = "block";
}
function batalUbahBerkas() {
  document.getElementById("tampilan-berkas").style.display = "flex";
  document.getElementById("form-ubah-berkas").style.display = "none";
}

function bukaModalTugas() {
  document.getElementById("modalTambahTugas").style.display = "flex";
}

function tutupModalTugas() {
  document.getElementById("modalTambahTugas").style.display = "none";
}

window.onclick = function (event) {
  let modal = document.getElementById("modalTambahTugas");
  if (event.target == modal) {
    tutupModalTugas();
  }
};

function bukaModalEditTimeline(id, step, status, createdAt) {
  document.getElementById("edit_timeline_id").value = id;
  document.getElementById("edit_step").value = step;

  let statusSelect = document.getElementById("edit_status");
  for (let i = 0; i < statusSelect.options.length; i++) {
    if (statusSelect.options[i].value.toLowerCase() === status.toLowerCase()) {
      statusSelect.selectedIndex = i;
      break;
    }
  }

  document.getElementById("edit_created_at").value = createdAt;

  document.getElementById("modalEditTimeline").style.display = "flex";
}

function tutupModalEditTimeline() {
  document.getElementById("modalEditTimeline").style.display = "none";
}

function bukaModalTambahTimeline() {
  document.getElementById("modalTambahTimeline").style.display = "flex";
}

function tutupModalTambahTimeline() {
  document.getElementById("modalTambahTimeline").style.display = "none";
}
