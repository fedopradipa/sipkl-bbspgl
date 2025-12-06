{{-- State: Pending (Menunggu Verifikasi) --}}
<div class="card border-0 shadow-sm bg-warning bg-opacity-10 mb-4">
  <div class="card-body text-center py-5">
    <div class="mb-3">
      <div class="d-inline-block bg-white p-3 rounded-circle shadow-sm">
        <i class="fas fa-hourglass-half fa-3x text-warning"></i>
      </div>
    </div>
    <h4 class="fw-bold text-dark">Lamaran Sedang Diproses</h4>
    <p class="text-muted">
      Lamaran Anda untuk posisi
      <span class="badge bg-primary">{{ $lamaran->lowongan->judul }}</span>
      sedang diverifikasi.
    </p>
    <button class="btn btn-warning px-4 fw-bold rounded-pill text-white shadow-sm" disabled>
      <i class="fas fa-sync-alt fa-spin me-2"></i> Menunggu Verifikasi
    </button>
  </div>
</div>