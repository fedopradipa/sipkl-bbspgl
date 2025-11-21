@extends('layouts.app') {{-- Atau layouts.guest jika ada --}}

@section('content')

{{-- SECTION 1: HERO & SEARCH (Warna Biru seperti Astra) --}}
<div class="bg-primary py-5 mb-5">
  <div class="container">
    <div class="row justify-content-center text-center mb-4">
      <div class="col-md-8 text-white">
        <h1 class="fw-bold">Temukan Lowongan PKL</h1>
        <p class="lead text-white-50">Bergabunglah bersama kami di BBSPGL untuk pengalaman magang yang berharga.</p>
      </div>
    </div>

    {{-- Search Bar Card --}}
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-8">
        <div class="card border-0 shadow-lg p-2">
          <form action="{{ route('guest.home') }}" method="GET">
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-white border-0 text-muted">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" name="keyword" class="form-control border-0 shadow-none"
                placeholder="Cari posisi atau divisi (cth: Programmer)..."
                value="{{ request('keyword') }}">
              <button class="btn btn-primary px-4 fw-bold" type="submit">Cari</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- SECTION 2: LIST LOWONGAN --}}
<div class="container mb-5">
  {{-- Header Kecil --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="text-muted fw-bold">
      {{ $lowongans->total() }} Posisi Tersedia
    </h5>
  </div>

  <div class="row">
    @forelse($lowongans as $item)
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card h-100 border-0 shadow-sm hover-card transition-all">
        <div class="card-body p-4 d-flex flex-column">

          {{-- Header Card: Logo/Icon & Bookmark --}}
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="bg-light rounded p-3">
              {{-- Ikon Divisi (Placeholder Logo) --}}
              <i class="fas fa-building text-primary fa-lg"></i>
            </div>
            <span class="badge bg-light text-dark border">
              {{ $item->is_fulltime ? 'Full Time' : 'Flexible' }}
            </span>
          </div>

          {{-- Job Title --}}
          <h5 class="card-title fw-bold text-dark mb-1">
            {{ $item->judul }}
          </h5>
          <p class="text-muted small mb-3">{{ $item->divisi }}</p>

          {{-- Metadata (Icons) --}}
          <div class="mb-4 mt-auto">
            <div class="d-flex align-items-center text-secondary small mb-2">
              <i class="fas fa-clock me-2 text-warning" style="width: 20px"></i>
              <span>Min. {{ $item->min_duration }} Bulan</span>
            </div>
            <div class="d-flex align-items-center text-secondary small">
              <i class="fas fa-calendar-alt me-2 text-danger" style="width: 20px"></i>
              <span>Tutup: {{ $item->window_end_date->format('d M Y') }}</span>
            </div>
          </div>

          {{-- Action Buttons --}}
          <div class="d-grid gap-2 d-md-flex mt-3">
            <a href="{{ route('guest.lowongan.show', $item->id) }}" class="btn btn-outline-primary flex-grow-1 fw-bold">
              Lihat Detail
            </a>
            <a href="{{ route('lowongan.apply', $item->id) }}" class="btn btn-primary flex-grow-1 fw-bold">
              Lamar
            </a>
          </div>

        </div>
      </div>
    </div>
    @empty
    {{-- State Kosong --}}
    <div class="col-12 text-center py-5">
      <div class="mb-3">
        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Magnifying%20Glass%20Tilted%20Left.png" alt="Search" width="80">
      </div>
      <h4 class="fw-bold text-secondary">Lowongan tidak ditemukan</h4>
      <p class="text-muted">Coba ubah kata kunci pencarian Anda atau cek lagi nanti.</p>
      <a href="{{ route('guest.home') }}" class="btn btn-outline-secondary mt-2">Reset Pencarian</a>
    </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="d-flex justify-content-center mt-4">
    {{ $lowongans->links() }}
  </div>
</div>

{{-- Style Tambahan Khusus Halaman Ini --}}
<style>
  /* Efek Hover Halus mirip UI modern */
  .hover-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s;
  }

  .hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
  }
</style>
@endsection