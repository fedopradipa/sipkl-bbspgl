{{-- State: Ditolak --}}
<div class="card border-0 shadow-sm bg-danger bg-opacity-10 mb-4">
  <div class="card-body text-center py-5">
    <div class="mb-3">
      <div class="d-inline-block bg-white p-3 rounded-circle shadow-sm">
        <i class="fas fa-times-circle fa-3x text-danger"></i>
      </div>
    </div>
    <h4 class="fw-bold text-danger">Lamaran Tidak Diterima</h4>
    <p class="text-muted mb-4">
      Mohon maaf, kualifikasi Anda belum sesuai. <br>

      @if($lamaran->catatan_admin)
    <div class="alert alert-light border-danger d-inline-block text-start mt-2">
      <strong>Catatan Admin:</strong> <br>
      {{ $lamaran->catatan_admin }}
    </div>
    @endif
    </p>
    <a href="{{ route('guest.home') }}" class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm">
      Cari Lowongan Lain
    </a>
  </div>
</div>