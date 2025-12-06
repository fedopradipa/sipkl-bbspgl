{{-- Menu Cepat --}}
<h6 class="text-muted fw-bold mb-3 ms-1">Menu Cepat</h6>
<div class="row g-3">
  <div class="col-6">
    <a
      href="#"
      class="card border-0 shadow-sm h-100 text-decoration-none text-dark hover-up bg-white">
      <div class="card-body text-center py-4">
        <div class="mb-2 text-info">
          <i class="fas fa-book fa-2x"></i>
        </div>
        <h6 class="fw-bold mb-0 small">Jurnal Kegiatan</h6>
      </div>
    </a>
  </div>

  <div class="col-6">
    <a
      href="{{ route('presensi.riwayat') }}"
      class="card border-0 shadow-sm h-100 text-decoration-none text-dark hover-up bg-white">
      <div class="card-body text-center py-4">
        <div class="mb-2 text-primary">
          <i class="fas fa-history fa-2x"></i>
        </div>
        <h6 class="fw-bold mb-0 small">Riwayat Absen</h6>
      </div>
    </a>
  </div>
</div>