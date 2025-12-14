<div class="row justify-content-center">
  <div class="col-md-8">
    {{-- CARD UTAMA: STATUS BERHENTI --}}
    <div class="card border-0 shadow-lg border-top border-danger border-5 mb-4">
      <div class="card-body text-center p-5">

        {{-- Icon Besar --}}
        <div class="mb-4">
          <span class="fa-stack fa-3x">
            <i class="fas fa-circle fa-stack-2x text-danger bg-opacity-10" style="opacity: 0.1"></i>
            <i class="fas fa-ban fa-stack-1x text-danger"></i>
          </span>
        </div>

        <h3 class="fw-bold text-danger mb-2">Magang Dihentikan</h3>
        <p class="text-muted mb-4">
          Status kepesertaan magang Anda telah <strong>dinonaktifkan</strong>.
        </p>

        {{-- Detail Informasi --}}
        <div class="bg-light p-4 rounded-3 text-start border mb-4">
          <div class="row mb-2">
            <div class="col-4 text-muted small fw-bold text-uppercase">Tanggal Efektif</div>
            <div class="col-8 fw-bold text-dark">
              {{ \Carbon\Carbon::parse($lamaran->tgl_selesai_resmi)->translatedFormat('d F Y') }}
            </div>
          </div>
          <div class="row">
            <div class="col-4 text-muted small fw-bold text-uppercase">Alasan / Catatan</div>
            <div class="col-8 text-danger fw-bold">
              {{-- Menampilkan catatan admin (misal: Mengundurkan Diri - Sakit) --}}
              {{ $lamaran->catatan_admin ?? 'Tidak ada catatan khusus.' }}
            </div>
          </div>
        </div>

        {{-- Alert Info Akses --}}
        <div class="alert alert-warning d-flex align-items-center small" role="alert">
          <i class="fas fa-lock me-3 fa-2x"></i>
          <div class="text-start">
            <strong>Akses Terkunci:</strong>
            Anda tidak dapat lagi melakukan presensi, mengisi jurnal, atau mengajukan izin.
            Silakan hubungi Admin atau HRD jika Anda merasa ini adalah kesalahan.
          </div>
        </div>

        {{-- Tombol Hubungi Admin (Opsional) --}}
        <a href="https://wa.me/628123456789" target="_blank" class="btn btn-outline-secondary mt-3">
          <i class="fab fa-whatsapp me-2"></i> Hubungi Admin
        </a>
      </div>
    </div>
  </div>
</div>