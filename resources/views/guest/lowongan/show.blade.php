@extends('layouts.guest')

@section('content')

{{-- 1. HERO HEADER (Premium Blue Gradient) --}}
<div class="page-header-detail">
  <div class="container position-relative z-1">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('guest.home') }}" class="text-white-50 text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Kembali</a></li>
        <li class="breadcrumb-item active text-white" aria-current="page">Detail Lowongan</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <div class="d-flex align-items-start">
          <div class="icon-box-lg me-4 d-none d-md-flex">
            <i class="fas fa-laptop-code"></i>
          </div>
          <div>
            <h1 class="fw-bold text-white mb-2">{{ $lowongan->judul }}</h1>
            <p class="text-white-50 mb-0 fs-5">{{ $lowongan->divisi }}</p>

            <div class="d-flex flex-wrap gap-2 mt-3">
              <span class="badge bg-white text-primary bg-opacity-10 border border-white border-opacity-25 backdrop-blur">
                <i class="fas fa-clock me-1"></i> Full Time
              </span>
              <span class="badge bg-white text-warning bg-opacity-10 border border-white border-opacity-25 backdrop-blur">
                <i class="fas fa-hourglass-half me-1"></i> Min. {{ $lowongan->min_duration }} Bulan
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- Background Pattern --}}
  <div class="hero-pattern"></div>
</div>

<div class="container mt-n5 position-relative z-2 mb-5">
  <div class="row g-4">

    {{-- 2. KOLOM KIRI: KONTEN UTAMA --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-4 p-md-5">

          <h5 class="section-title text-primary">
            <i class="fas fa-align-left me-2"></i>Deskripsi Pekerjaan
          </h5>
          <div class="text-secondary lh-lg mb-5">
            {!! nl2br(e($lowongan->deskripsi ?? 'Tidak ada deskripsi detail.')) !!}
          </div>

          @if(!empty($lowongan->kualifikasi))
          <h5 class="section-title text-primary">
            <i class="fas fa-check-circle me-2"></i>Kualifikasi & Syarat
          </h5>
          <ul class="requirement-list">
            @foreach($lowongan->kualifikasi as $req)
            <li>{{ $req }}</li>
            @endforeach
          </ul>
          @endif

          @if(!empty($lowongan->fasilitas))
          <div class="alert alert-info bg-opacity-10 border-0 d-flex align-items-center mt-4">
            <div class="fs-2 text-info me-3"><i class="fas fa-gift"></i></div>
            <div>
              <h6 class="fw-bold text-info mb-1">Fasilitas</h6>
              <p class="mb-0 small text-muted">{{ $lowongan->fasilitas }}</p>
            </div>
          </div>
          @endif

        </div>
      </div>
    </div>

    {{-- 3. KOLOM KANAN: SIDEBAR INFO (Sticky) --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-lg rounded-4 sticky-top" style="top: 20px; z-index: 10;">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-4 text-dark border-bottom pb-3">Ringkasan Posisi</h5>

          {{-- Info Items --}}
          <div class="info-item">
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <div>
              <small>Periode Pendaftaran</small>
              <strong>Hingga {{ $lowongan->window_end_date->format('d M Y') }}</strong>
            </div>
          </div>

          <div class="info-item">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <div>
              <small>Jam Kerja Standar</small>
              <strong>{{ \Carbon\Carbon::parse($lowongan->jam_masuk_standar)->format('H:i') }} - {{ \Carbon\Carbon::parse($lowongan->jam_pulang_standar)->format('H:i') }}</strong>
            </div>
          </div>

          {{-- INFO JURUSAN (BARU) --}}
          <div class="info-item">
            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
            <div>
              <small>Jurusan / Prodi Terkait</small>
              <strong class="text-dark">{{ $lowongan->jurusan_terkait ?? 'Semua Jurusan' }}</strong>
            </div>
          </div>

          {{-- Action Button --}}
          <div class="mt-4 pt-3 border-top">
            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="alert alert-warning small text-center mb-0">
              <i class="fas fa-user-shield me-1"></i> Mode Admin (View Only)
            </div>
            @else
            <a href="{{ route('lamaran.apply', $lowongan->id) }}" class="btn btn-primary w-100 btn-lg shadow-sm btn-apply-pulse">
              Lamar Posisi Ini <i class="fas fa-arrow-right ms-2"></i>
            </a>
            @endif
          </div>

        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('css/guest-landing.css') }}">
@endpush