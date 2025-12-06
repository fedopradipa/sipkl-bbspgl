{{-- File: resources/views/admin/izin/partials/reject_modal.blade.php --}}

<div class="modal fade" id="rejectIzinModal" tabindex="-1" aria-labelledby="rejectIzinLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formRejectIzin" method="POST" action="" class="modal-content">
      @csrf

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="rejectIzinLabel">Tolak Pengajuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
          <textarea name="catatan_admin" id="catatan_admin_izin" class="form-control" rows="3" required placeholder="Contoh: Bukti surat tidak valid..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Tolak Izin</button>
      </div>
    </form>
  </div>
</div>

{{-- SCRIPT KHUSUS MODAL INI --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const rejectModal = document.getElementById('rejectIzinModal');
    if (rejectModal) {
      rejectModal.addEventListener('show.bs.modal', function(event) {
        // 1. Tombol yang diklik
        const button = event.relatedTarget;

        // 2. Ambil URL dari atribut data-url (Lebih aman daripada rakit manual)
        const url = button.getAttribute('data-url');

        // 3. Isi Action Form
        const form = document.getElementById('formRejectIzin');
        form.action = url;

        // 4. Reset Textarea (Supaya bersih saat dibuka)
        document.getElementById('catatan_admin_izin').value = '';
      });
    }
  });
</script>