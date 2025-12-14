<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold text-primary mb-1">Peserta Magang Aktif</h4>
    {{-- Mengambil total data dari pagination object --}}
    <p class="text-muted small mb-0">Memantau {{ $peserta->total() }} peserta yang sedang melaksanakan PKL.</p>
  </div>
  <div class="col-md-3">
    <form action="{{ route('admin.peserta_aktif.index') }}" method="GET">
      <div class="input-group shadow-sm">
        <span class="input-group-text bg-white border-end-0">
          <i class="fas fa-search text-muted"></i>
        </span>
        <input type="text" name="search" class="form-control border-start-0 ps-0"
          placeholder="Cari nama atau divisi..."
          value="{{ request('search') }}">
      </div>
    </form>
  </div>
</div>