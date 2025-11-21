@extends('layouts.app')

@section('content')
{{-- HERO HEADER KECIL --}}
<div class="bg-primary py-4 mb-4">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('guest.home') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
        <li class="breadcrumb-item active text-white" aria-current="page">Detail Lowongan</li>
      </ol>
    </nav>
  </div>
</div>

<div class="container mb-5">
  <div class="row">
    {{-- KOLOM KIRI: KONTEN UTAMA --}}
    <div class="col-lg-8 mb-4">
      <div class="card border-0 shadow-sm p-4">
        <div class="d-flex align-items-center mb-4">
          <div class="bg-light rounded p-3 me-3">
            <i class="fas fa-laptop-code fa-2x text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">{{ $lowongan->judul }}</h2>
            <p class="text-muted mb-0">{{ $lowongan->divisi }}</p>
          </div>
        </div>

        <hr class="my-4">

        <h5 class="fw-bold mb-3">Deskripsi Pekerjaan</h5>
        <div class="text-secondary lh-lg">
          {{-- Menampilkan deskripsi dengan format paragraf --}}
          {!! nl2br(e($lowongan->deskripsi ?? 'Tidak ada deskripsi detail.')) !!}
        </div>
      </div>
    </div>

    {{-- KOLOM KANAN: SIDEBAR INFO (Sticky) --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 1;">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-4">Ringkasan</h5>

          {{-- Item Info --}}
          <div class="mb-3">
            <small class="text-muted d-block">Tipe Magang</small>
            <span class="fw-bold text-dark">
              <i class="fas fa-briefcase me-2 text-primary"></i>
              {{ $lowongan->is_fulltime ? 'Full Time (WFO)' : 'Flexible / Hybrid' }}
            </span>
          </div>

          <div class="mb-3">
            <small class="text-muted d-block">Durasi Minimal</small>
            <span class="fw-bold text-dark">
              <i class="fas fa-hourglass-half me-2 text-warning"></i>
              {{ $lowongan->min_duration }} Bulan
            </span>
          </div>

          <div class="mb-3">
            <small class="text-muted d-block">Jam Kerja Standar</small>
            <span class="fw-bold text-dark">
              <i class="fas fa-clock me-2 text-info"></i>
              {{ \Carbon\Carbon::parse($lowongan->jam_masuk_standar)->format('H:i') }} -
              {{ \Carbon\Carbon::parse($lowongan->jam_pulang_standar)->format('H:i') }}
            </span>
          </div>

          <div class="mb-3">
            <small class="text-muted d-block">Periode Pendaftaran</small>
            <span class="fw-bold text-danger">
              <i class="fas fa-calendar-alt me-2"></i>
              Sampai {{ $lowongan->window_end_date->format('d M Y') }}
            </span>
          </div>

          <div class="mb-4">
            <small class="text-muted d-block">Sisa Kuota</small>
            <span class="fw-bold text-success">
              <i class="fas fa-users me-2"></i>
              {{ $lowongan->kuota }} Orang
            </span>
          </div>

          {{-- TOMBOL AKSI --}}
          <div class="d-grid">
            {{-- Jika user adalah Admin, jangan tampilkan tombol lamar --}}
            @if(auth()->check() && auth()->user()->peran === 'admin')
            <div class="alert alert-info text-center small">
              Anda login sebagai Admin.
            </div>
            @else
            {{-- Logika Tombol Apply --}}
            <a href="{{ route('lowongan.apply', $lowongan->id) }}" class="btn btn-primary btn-lg fw-bold shadow-sm">
              Lamar Posisi Ini
            </a>

            @endif
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection