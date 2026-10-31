<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

  {{-- BAGIAN KIRI: posisi & Counter --}}
  <div>
    <h4 class="fw-bold text-primary mb-1">Peserta PKL Aktif</h4>
    <p class="text-muted small mb-0">
      <i class="fas fa-users me-1"></i> Memantau {{ $peserta->total() }} peserta yang sedang melaksanakan PKL.
    </p>
  </div>

  {{-- BAGIAN KANAN: Form Pencarian (Style Baru) --}}
  <div class="col-12 col-md-auto">
    <form action="{{ route('admin.peserta_aktif.index') }}" method="GET">
      <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
        <input
          type="text"
          name="search"
          class="form-control"
          placeholder="Cari nama atau posisi..."
          value="{{ request('search') }}">

        <button class="btn btn-primary" type="submit" title="Cari">
          <i class="fas fa-search"></i>
        </button>

        {{-- Tombol Reset hanya muncul jika sedang mencari --}}
        @if(request('search'))
        <a href="{{ route('admin.peserta_aktif.index') }}" class="btn btn-outline-secondary" title="Hapus Filter">
          <i class="fas fa-times"></i>
        </a>
        @endif
      </div>
    </form>
  </div>

</div>