{{-- ========================================== --}}
{{-- MODAL APPROVE (UX: Visual Jelas + Loading) --}}
{{-- ========================================== --}}
<div class="modal fade" id="approveIzinModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formApproveIzin" method="POST" action="" class="modal-content border-0 shadow-lg">
      @csrf
      @method('PUT')

      <div class="modal-header bg-success text-white border-0">
        <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2"></i>Konfirmasi Persetujuan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center py-4">
        <div class="mb-3 text-success">
          <i class="fas fa-calendar-check fa-5x"></i>
        </div>
        <h5 class="fw-bold mb-2">Setujui Pengajuan Ini?</h5>
        <p class="text-muted mb-0">
          Sistem akan otomatis <strong>mengenerate data presensi/absensi</strong> untuk peserta ini sesuai tanggal yang diajukan.
        </p>
        <div class="alert alert-light border mt-3 small text-start">
          <i class="fas fa-info-circle text-primary me-1"></i>
          Pastikan tanggal izin sudah benar sebelum melanjutkan.
        </div>
      </div>

      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
        {{-- Tombol dengan ID khusus untuk Loading State --}}
        <button type="submit" id="btnApproveSubmit" class="btn btn-success px-4 rounded-pill fw-bold">
          <span class="d-none spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
          <i class="fas fa-check me-1"></i> Ya, Proses Sekarang
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ========================================== --}}
{{-- MODAL REJECT (Fix: Method PUT + Validasi)  --}}
{{-- ========================================== --}}
<div class="modal fade" id="rejectIzinModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formRejectIzin" method="POST" action="" class="modal-content border-0 shadow-lg">
      @csrf
      @method('PUT') {{-- KUNCI AGAR TIDAK ERROR POST NOT SUPPORTED --}}

      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title fw-bold"><i class="fas fa-times-circle me-2"></i>Tolak Pengajuan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="alert alert-warning d-flex align-items-center small" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <div>Alasan penolakan akan muncul di dashboard peserta.</div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
          <textarea name="catatan_admin" id="catatan_admin_izin" class="form-control shadow-sm" rows="4"
            required minlength="5"
            placeholder="Contoh: Bukti surat sakit buram, harap upload ulang..."></textarea>
          <div class="form-text text-end" id="charCount">0 karakter</div>
        </div>
      </div>

      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
        <button type="submit" id="btnRejectSubmit" class="btn btn-danger px-4 rounded-pill fw-bold">
          <span class="d-none spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
          <i class="fas fa-times me-1"></i> Tolak Izin
        </button>
      </div>
    </form>
  </div>
</div>