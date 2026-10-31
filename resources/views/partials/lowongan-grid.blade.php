{{-- File: resources/views/partials/lowongan-grid.blade.php --}}

{{-- INFO JUMLAH --}}
<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="text-primary fw-bold border-start border-4 border-primary ps-3">
    {{ $lowongans->total() }} Posisi Tersedia
  </h5>
</div>

<div class="row g-4">
  @forelse($lowongans as $item)
  <div class="col-md-6 col-lg-4">
    <div class="card h-100 job-card">
      <div class="card-body p-4 d-flex flex-column">

        {{-- Header Card --}}
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="icon-box">
            <i class="fas fa-building"></i>
          </div>
          <span class="badge {{ $item->is_fulltime ? 'badge-fulltime' : 'badge-flexible' }}">
            {{ $item->is_fulltime ? 'Full Time' : 'Flexible' }}
          </span>
        </div>

        {{-- Title & posisi --}}
        <h5 class="card-title">
          {{-- LOGIKA LINK: Cek Auth untuk menentukan arah link --}}
          <a href="{{ auth()->check() ? route('peserta.lowongan.show', $item->id) : route('guest.lowongan.show', $item->id) }}"
            class="text-decoration-none text-dark stretched-link">
            {{ $item->posisi }}
          </a>
        </h5>
        <p class="card-subtitle mb-3">{{ $item->posisi }}</p>

        <hr class="card-divider">

        {{-- Metadata --}}
        <div class="job-meta mb-4">
          <div class="meta-item">
            <i class="fas fa-clock text-warning"></i>
            <span>Min. {{ $item->min_duration }} Bulan</span>
          </div>
          <div class="meta-item">
            <i class="fas fa-calendar-alt text-danger"></i>
            <span>Tutup: {{ \Carbon\Carbon::parse($item->window_end_date)->format('d M Y') }}</span>
          </div>
        </div>

        {{-- Action Button --}}
        <div class="mt-auto d-grid z-2 position-relative">
          @auth
          {{-- Tombol untuk Peserta --}}
          <a href="{{ route('lamaran.apply', $item->id) }}" class="btn btn-apply">
            Lamar Sekarang <i class="fas fa-paper-plane ms-2"></i>
          </a>
          @else
          <a href="{{ route('lamaran.apply', $item->id) }}" class="btn btn-apply">
            Lamar Sekarang <i class="fas fa-sign-in-alt ms-2"></i>
          </a>
          @endauth

        </div>

      </div>
    </div>
  </div>
  @empty
  {{-- STATE KOSONG --}}
  <div class="col-12">
    <div class="alert-empty">
      <div class="empty-icon">
        <i class="fas fa-search"></i>
      </div>
      <div class="empty-content">
        <h5>Lowongan Tidak Ditemukan</h5>
        <p>Coba kata kunci lain atau cek kembali nanti.</p>
        <a href="{{ url()->current() }}" class="btn btn-outline-primary btn-sm mt-2">Reset Filter</a>
      </div>
    </div>
  </div>
  @endforelse
</div>

{{-- PAGINATION --}}
<div class="d-flex justify-content-center mt-5">
  {{ $lowongans->withQueryString()->links() }}
</div>