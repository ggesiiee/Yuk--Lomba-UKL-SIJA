<div class="update-action-container">
    <form action="" method="POST" style="display: inline-block;">
        <input type="hidden" name="lomba_id" value="Essay">
        
        <select name="status_baru" onchange="this.form.submit()" class="pilih-status-sederhana">
            <option value="aktif">🔵 Aktif Ikut</option>
            <option value="selesai">🟢 Selesai</option>
            <option value="batal">🔴 Batalkan</option>
        </select>
        
        <input type="hidden" name="ubah_status_keikutsertaan" value="1">
    </form>
</div>

<style>
.pilih-status-sederhana {
  padding: 5px 10px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #475569;
  background-color: #f1f5f9;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  cursor: pointer;
  font-family: "Nunito", sans-serif;
  transition: all 0.2s;
}

.pilih-status-sederhana:hover {
  background-color: #e2e8f0;
  border-color: #94a3b8;
}

.pilih-status-sederhana:focus {
  outline: 2px solid #3b82f6;
  border-color: transparent;
}
</style>