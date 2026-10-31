{{-- File: resources/views/partials/lowongan-detail-content.blade.php --}}

@php
// Fallback agar tidak error Undefined variable $backUrl
// Guest -> Home (/), Auth -> Dashboard (/dashboard)
$backUrl = $backUrl ?? (auth()->check() ? route('peserta.dashboard') : route('guest.home'));@endphp

<div class="page-header-detail">
  <div class="container position-relative z-1">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-4">
        {{-- LINK KEMBALI DINAMIS (Guest ke Home, Auth ke Dashboard) --}}
        <li class="breadcrumb-item">
          <a href="{{ $backUrl }}" class="text-white-50 text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Kembali
          </a>
        </li>
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
            <h1 class="fw-bold text-white mb-2">{{ $lowongan->posisi }}</h1>

            {{-- posisi DIHAPUS --}}

            <div class="d-flex flex-wrap gap-2 mt-3">
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

    {{-- KOLOM KIRI: KONTEN UTAMA --}}
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
            @if(is_array($lowongan->kualifikasi))
            @foreach($lowongan->kualifikasi as $req)
            <li>{{ $req }}</li>
            @endforeach
            @else
            <li>{{ $lowongan->kualifikasi }}</li>
            @endif
          </ul>
          @endif

          {{-- FASILITAS DIHAPUS --}}

        </div>
      </div>
    </div>

    {{-- KOLOM KANAN: SIDEBAR INFO --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-lg rounded-4 sticky-top" style="top: 20px; z-index: 10;">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-4 text-dark border-bottom pb-3">Ringkasan Posisi</h5>

          {{-- Info Item: Periode --}}
          <div class="info-item">
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <div>
              <small>Periode Pendaftaran</small>
              <strong>Hingga {{ \Carbon\Carbon::parse($lowongan->window_end_date)->format('d M Y') }}</strong>
            </div>
          </div>

          {{-- Info Item: Jurusan --}}
          <div class="info-item">
            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
            <div>
              <small>Jurusan / Prodi Terkait</small>
              <strong class="text-dark">{{ $lowongan->jurusan_terkait ?? 'Semua Jurusan' }}</strong>
            </div>
          </div>

          {{-- Action Button --}}
          <div class="mt-4 pt-3 border-top">
            @auth
            <a href="{{ route('lamaran.apply', $lowongan->id) }}" class="btn btn-primary w-100 btn-lg shadow-sm btn-apply-pulse">
              Lamar Posisi Ini <i class="fas fa-arrow-right ms-2"></i>
            </a>
            @else
            <a href="{{ route('lamaran.apply', $lowongan->id) }}" class="btn btn-primary w-100 btn-lg shadow-sm btn-apply-pulse">
              Lamar sekarang <i class="fas fa-sign-in-alt ms-2"></i>
            </a>
            @endauth

          </div>

        </div>
      </div>
    </div>

  </div>
</div>