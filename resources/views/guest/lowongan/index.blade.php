@extends('layouts.guest')

@section('content')

{{-- === 1. HERO SECTION (PREMIUM BLUE) === --}}
<section class="page-header">
  <div class="container text-center position-relative z-1">
    <div class="header-ornament top"></div>
    <h1 class="display-4 fw-bold header-title">Lowongan Magang & PKL</h1>
    <p class="lead header-subtitle">Bergabunglah bersama Balai Besar Survei Pemetaan dan Geologi Kelautan</p>
    <div class="header-divider mx-auto mt-4"></div>
    <div class="header-ornament bottom"></div>
  </div>
  {{-- Background Pattern --}}
  <div class="hero-pattern"></div>
</section>

<main class="py-5 main-content">
  <div class="container">

    {{-- === 2. FILTER & SEARCH SECTION === --}}
    <div class="filter-section mb-5">
      <form action="{{ route('guest.home') }}" method="GET" class="row g-3 justify-content-center">
        <div class="col-lg-8">
          <div class="search-wrapper">
            <div class="search-icon text-primary">
              <i class="fas fa-search"></i>
            </div>
            <input type="text" name="keyword" class="form-control search-input"
              placeholder="Cari posisi, divisi, atau keahlian..."
              value="{{ request('keyword') }}">
            <button class="btn btn-search" type="submit">
              <span>Temukan</span>
              <i class="fas fa-arrow-right ms-2"></i>
            </button>
            @if(request('keyword'))
            <a href="{{ route('guest.home') }}" class="btn btn-reset" title="Reset">
              <i class="fas fa-times"></i>
            </a>
            @endif
          </div>
        </div>
      </form>
    </div>

    {{-- INFO JUMLAH --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="text-primary fw-bold border-start border-4 border-primary ps-3">
        {{ $lowongans->total() }} Posisi Tersedia
      </h5>
    </div>

    {{-- === 3. LOWONGAN GRID === --}}
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

            {{-- Title & Divisi --}}
            <h5 class="card-title">
              <a href="{{ route('guest.lowongan.show', $item->id) }}" class="text-decoration-none text-dark stretched-link">
                {{ $item->judul }}
              </a>
            </h5>
            <p class="card-subtitle mb-3">{{ $item->divisi }}</p>

            {{-- Divider Halus --}}
            <hr class="card-divider">

            {{-- Metadata (Ikon) --}}
            <div class="job-meta mb-4">
              <div class="meta-item">
                <i class="fas fa-clock text-warning"></i>
                <span>Min. {{ $item->min_duration }} Bulan</span>
              </div>
              <div class="meta-item">
                <i class="fas fa-calendar-alt text-danger"></i>
                <span>Tutup: {{ $item->window_end_date->format('d M Y') }}</span>
              </div>
            </div>

            {{-- Action Button (Bottom) --}}
            <div class="mt-auto d-grid z-2 position-relative">
              <a href="{{ route('lamaran.apply', $item->id) }}" class="btn btn-apply">
                Lamar Sekarang <i class="fas fa-paper-plane ms-2"></i>
              </a>
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
            <a href="{{ route('guest.home') }}" class="btn btn-outline-primary btn-sm mt-2">Reset Filter</a>
          </div>
        </div>
      </div>
      @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-5">
      {{ $lowongans->links() }}
    </div>

  </div>
</main>
@endsection

@push('style')
{{-- Panggil file CSS eksternal --}}
<link rel="stylesheet" href="{{ asset('css/guest-landing.css') }}">
@endpush