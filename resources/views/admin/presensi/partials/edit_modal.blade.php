<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('presensi.update.admin') }}" method="POST" class="modal-content">
      @csrf
      @method('PUT')
      <input type="hidden" name="id" id="edit_id">

      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Koreksi Absensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        {{-- Pilihan Status --}}
        <div class="mb-3">
          <label class="form-label">Status Kehadiran</label>
          <select name="status" id="edit_status" class="form-select">
            <option value="hadir">Hadir</option>
            <option value="sakit">Sakit</option>
            <option value="izin">Izin</option>
            <option value="alpha">Alpha</option>
          </select>
        </div>

        {{-- Input Jam (Bungkus dengan ID editJamContainer) --}}
        <div class="row" id="editJamContainer">
          <div class="col-6 mb-3">
            <label class="form-label">Jam Masuk</label>
            <input type="time" name="jam_masuk" id="edit_masuk" class="form-control">
          </div>
          <div class="col-6 mb-3">
            <label class="form-label">Jam Pulang</label>
            <input type="time" name="jam_pulang" id="edit_pulang" class="form-control">
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning">Update Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- SCRIPT JS KHUSUS EDIT --}}
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editModal');

    if (editModal) {
      // Definisi Elemen
      const statusSelect = document.getElementById('edit_status');
      const jamContainer = document.getElementById('editJamContainer');
      const jamMasuk = document.getElementById('edit_masuk');
      const jamPulang = document.getElementById('edit_pulang');

      // Fungsi Toggle Tampilan
      function toggleEditJam() {
        if (statusSelect.value === 'hadir') {
          // Jika HADIR: Tampilkan & Wajib
          jamContainer.style.display = 'flex';
          jamMasuk.required = true;
        } else {
          // Jika NON-HADIR: Sembunyikan & Tidak Wajib
          jamContainer.style.display = 'none';
          jamMasuk.required = false;

          // Reset nilai agar menjadi NULL saat disubmit
          jamMasuk.value = '';
          jamPulang.value = '';
        }
      }

      // 1. Event saat Modal Dibuka (Populate Data)
      editModal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;

        // Isi data ID & Status
        document.getElementById('edit_id').value = btn.getAttribute('data-id');
        statusSelect.value = btn.getAttribute('data-status');

        // Isi data Jam (ambil 5 karakter pertama: HH:mm)
        const masuk = btn.getAttribute('data-masuk');
        const pulang = btn.getAttribute('data-pulang');

        jamMasuk.value = masuk ? masuk.substring(0, 5) : '';
        jamPulang.value = pulang ? pulang.substring(0, 5) : '';

        // PENTING: Panggil fungsi toggle agar UI menyesuaikan status saat ini
        toggleEditJam();
      });

      // 2. Event saat Dropdown Berubah
      statusSelect.addEventListener('change', toggleEditJam);
    }
  });
</script>
@endpush