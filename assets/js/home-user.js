document.addEventListener("DOMContentLoaded", () => {
  feather.replace();
  updateKalender();
});

function updateKalender() {
  const elHari = document.getElementById("hari-ini");
  const elTanggal = document.getElementById("tanggal-bulan-ini");

  if (elHari && elTanggal) {
    const deviceDate = new Date();
    const formatHari = deviceDate.toLocaleDateString(undefined, {
      weekday: "long",
    });
    const formatTanggalBulan = deviceDate.toLocaleDateString(undefined, {
      day: "numeric",
      month: "long",
    });

    elHari.innerText = formatHari + ",";
    elTanggal.innerText = formatTanggalBulan;
  }
}

function bukaModal() {
  document.getElementById("modalNote").style.display = "flex";
}

function tutupModal() {
  document.getElementById("modalNote").style.display = "none";
}

function bukaModalEdit(id, isi) {
  document.getElementById("edit_note_id").value = id;
  document.getElementById("edit_isi_note").value = isi;
  document.getElementById("modalEditNote").style.display = "flex";
}

function tutupModalEdit() {
  document.getElementById("modalEditNote").style.display = "none";
}

window.onclick = function (event) {
  let modalTambah = document.getElementById("modalNote");
  let modalEdit = document.getElementById("modalEditNote");
  if (event.target == modalTambah) {
    tutupModal();
  }
  if (event.target == modalEdit) {
    tutupModalEdit();
  }
};

function updateStatusNote(elemenCheckbox) {
  let noteId = elemenCheckbox.getAttribute("data-id");
  let statusCentang = elemenCheckbox.checked ? 1 : 0;

  let bungkusTugas = document.getElementById("bungkus-note-" + noteId);

  fetch("../auth/update-note.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "id=" + noteId + "&status=" + statusCentang,
  })
    .then((response) => response.text())
    .then((data) => {
      console.log("Respon Server:", data);

      if (statusCentang === 1 && bungkusTugas) {
        bungkusTugas.style.transition = "all 0.3s ease";
        bungkusTugas.style.textDecoration = "line-through";
        bungkusTugas.style.opacity = "0.5";
      }
      else if (statusCentang === 0 && bungkusTugas) {
        bungkusTugas.style.transition = "all 0.3s ease";
        bungkusTugas.style.textDecoration = "none";
        bungkusTugas.style.opacity = "1";
      }
    })
    .catch((error) => {
      console.error("Terjadi kesalahan:", error);
      elemenCheckbox.checked = !elemenCheckbox.checked;
      alert("Gagal menyimpan status tugas!");
    });
}
