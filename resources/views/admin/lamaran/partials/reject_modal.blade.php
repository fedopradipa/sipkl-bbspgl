<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="rejectModalLabel">Konfirmasi Penolakan Lamaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- Form akan diisi action-nya via JavaScript --}}
      <form id="rejectForm" method="POST" action="">
        @csrf
        <div class="modal-body">
          <p class="mb-3">Anda akan menolak lamaran ini. **Tolong berikan catatan yang jelas** mengapa peserta ini ditolak.</p>

          <div class="mb-3">
            <label for="catatan_admin" class="form-label">Catatan Penolakan <span class="text-danger">*</span></label>
            <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="4" required placeholder="Contoh: Durasi magang kurang dari minimal 3 bulan."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak Permanen</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const rejectModal = document.getElementById('rejectModal');

    // Cek element agar tidak error di halaman lain
    if (rejectModal) {
      rejectModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const lamaranId = button.getAttribute('data-lamaran-id');

        const form = document.getElementById('rejectForm');

        // === PERBAIKAN DI SINI ===
        // Tambahkan '/' di paling depan
        // Dari: 'admin/lamaran/'...
        // Jadi: '/admin/lamaran/'...
        form.action = '/admin/lamaran/' + lamaranId + '/reject';
      });
    }
  });
</script>