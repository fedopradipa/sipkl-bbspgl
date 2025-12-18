<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="" method="POST" class="modal-content" id="editForm">
      @csrf
      @method('PUT')

      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Koreksi Absensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        {{-- 1. Pilihan Status --}}
        <div class="mb-3">
          <label class="form-label fw-bold small text-uppercase text-muted">Status Kehadiran</label>
          <select name="status" id="edit_status" class="form-select">
            <option value="hadir">Hadir</option>
            <option value="sakit">Sakit</option>
            <option value="izin">Izin</option>
            <option value="alpha">Alpha</option>
          </select>
        </div>

        {{-- 2. Input Jam (Muncul HANYA jika Hadir/Terlambat) --}}
        <div class="row" id="editJamContainer">
          <div class="col-6 mb-3">
            <label class="form-label small">Jam Masuk</label>
            <input type="time" name="jam_masuk" id="edit_masuk" class="form-control">
          </div>
          <div class="col-6 mb-3">
            <label class="form-label small">Jam Pulang</label>
            <input type="time" name="jam_pulang" id="edit_pulang" class="form-control">
          </div>
        </div>

        {{-- 3. Input Catatan (Muncul HANYA jika Sakit/Izin/Alpha) --}}
        <div class="mb-3" id="editNoteContainer" style="display: none;">
          <label class="form-label small">Catatan / Keterangan (Opsional)</label>
          <textarea name="catatan_harian" id="edit_catatan" class="form-control" rows="2"
            placeholder="Contoh: Surat dokter terlampir..."></textarea>
          <div class="form-text small text-muted">
            Kosongkan jika tidak ada keterangan tambahan.
          </div>
        </div>

      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning">Update Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- SCRIPT JS UPDATE --}}
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editModal');

    if (editModal) {
      // Definisi Elemen
      const editForm = document.getElementById('editForm');
      const statusSelect = document.getElementById('edit_status');

      // Container
      const jamContainer = document.getElementById('editJamContainer');
      const noteContainer = document.getElementById('editNoteContainer');

      // Inputs
      const jamMasuk = document.getElementById('edit_masuk');
      const jamPulang = document.getElementById('edit_pulang');
      const catatanInput = document.getElementById('edit_catatan');

      // Base URL Route
      const baseUrl = "{{ route('admin.presensi.index') }}";

      // Fungsi Toggle Tampilan (UX Logic)
      function toggleEditUI() {
        const status = statusSelect.value;

        if (status === 'hadir' || status === 'terlambat') {
          // MODE HADIR: Tampilkan Jam, Sembunyikan Catatan
          jamContainer.style.display = 'flex';
          noteContainer.style.display = 'none';
          jamMasuk.required = true;
        } else {
          // MODE NON-HADIR: Sembunyikan Jam, Tampilkan Catatan
          jamContainer.style.display = 'none';
          noteContainer.style.display = 'block';
          jamMasuk.required = false;

          // Bersihkan jam agar tidak tersimpan data jam sampah
          jamMasuk.value = '';
          jamPulang.value = '';
        }
      }

      // Event saat Modal Dibuka
      editModal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;

        // 1. Ambil Data dari Tombol
        const id = btn.getAttribute('data-id');
        const status = btn.getAttribute('data-status');
        const masuk = btn.getAttribute('data-masuk');
        const pulang = btn.getAttribute('data-pulang');
        const catatan = btn.getAttribute('data-catatan'); // <--- PASTIKAN INI ADA DI TOMBOL

        // 2. Update Action Form
        editForm.action = `${baseUrl}/${id}`;

        // 3. Isi Nilai Input
        statusSelect.value = status;
        jamMasuk.value = masuk ? masuk.substring(0, 5) : '';
        jamPulang.value = pulang ? pulang.substring(0, 5) : '';
        catatanInput.value = (catatan && catatan !== '-') ? catatan : '';

        // 4. Atur Tampilan UI
        toggleEditUI();
      });

      // Event saat Status Berubah
      statusSelect.addEventListener('change', toggleEditUI);
    }
  });
</script>
@endpush