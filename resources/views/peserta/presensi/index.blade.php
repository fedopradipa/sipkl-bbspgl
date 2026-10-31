@extends('layouts.app')

@section('content')
<div class="container py-4">

  {{-- 1. HEADER DASHBOARD --}}
  @include('peserta.dashboard.components.header', ['user' => $user])

  {{-- 2. LOGIC TAMPILAN UTAMA --}}

  {{-- ================================================================================== --}}
  {{-- PRIORITAS 1: DATA PRESENSI FINAL (INPUTAN ADMIN / HASIL REKAM) --}}
  {{-- ================================================================================== --}}
  @if($presensi && in_array($presensi->status, ['sakit', 'izin', 'alpha']))

  <div class="card border-0 shadow-lg text-white mb-4 overflow-hidden position-relative"
    style="background: {{ $presensi->status == 'alpha' ? 'linear-gradient(135deg, #e74a3b 0%, #be2617 100%)' : 'linear-gradient(135deg, #f6c23e 0%, #dda20a 100%)' }};">

    <div class="position-absolute top-0 start-0 w-100 h-100 bg-pattern opacity-10"></div>

    <div class="card-body p-5 text-center position-relative z-1">
      <div class="mb-3">
        <div class="d-inline-block bg-white {{ $presensi->status == 'alpha' ? 'text-danger' : 'text-warning' }} p-3 rounded-circle shadow">
          @if($presensi->status == 'alpha')
          <i class="fas fa-times-circle fa-3x"></i>
          @else
          <i class="fas fa-file-medical fa-3x"></i>
          @endif
        </div>
      </div>

      <h2 class="fw-bold text-uppercase">
        @if($presensi->status == 'alpha')
        ALPHA (TIDAK HADIR)
        @else
        SEDANG {{ strtoupper($presensi->status) }}
        @endif
      </h2>

      <p class="fs-5 opacity-75 mb-4">
        Status kehadiran Anda hari ini tercatat sebagai: <br>
        <strong>{{ ucfirst($presensi->status) }}</strong>
      </p>

      @if($presensi->catatan_harian)
      <div class="alert {{ $presensi->status == 'alpha' ? 'alert-danger text-danger' : 'alert-warning text-dark' }} d-inline-block px-4 py-2 shadow-sm border-0 bg-white">
        <small class="fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Catatan Admin:</small>
        <i class="fas fa-info-circle me-1"></i> {{ $presensi->catatan_harian }}
      </div>
      @else
      <div class="alert {{ $presensi->status == 'alpha' ? 'alert-danger text-danger' : 'alert-warning text-dark' }} d-inline-block px-4 py-2 shadow-sm border-0 bg-white">
        <i class="fas fa-check-circle me-1"></i> Data Final Admin
      </div>
      @endif
    </div>
  </div>

  {{-- ================================================================================== --}}
  {{-- PRIORITAS 2: PENGAJUAN IZIN DISETUJUI --}}
  {{-- ================================================================================== --}}
  @elseif($izinHariIni && !$presensi)

  <div class="card border-0 shadow-lg text-white mb-4 overflow-hidden position-relative"
    style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
    <div class="card-body p-5 text-center position-relative z-1">
      <div class="mb-3">
        <div class="d-inline-block bg-white text-success p-3 rounded-circle shadow">
          <i class="fas fa-file-medical fa-3x"></i>
        </div>
      </div>
      <h2 class="fw-bold">Sedang Izin</h2>
      <p class="fs-5 opacity-75 mb-4">
        Pengajuan izin Anda telah disetujui.<br>
        <strong>Kategori: {{ ucfirst($izinHariIni->jenis_izin ?? 'Izin') }}</strong>
      </p>
      <div class="alert alert-white text-success d-inline-block px-4 py-2 rounded-pill fw-bold shadow-sm">
        <i class="fas fa-check-circle me-1"></i> Disetujui Admin
      </div>
    </div>
  </div>

  {{-- ================================================================================== --}}
  {{-- PRIORITAS 3 [BARU]: PROGRAM BELUM DIMULAI --}}
  {{-- Ini solusi agar tidak dianggap Alpha jika login sebelum tanggal mulai --}}
  {{-- ================================================================================== --}}
  @elseif(\Carbon\Carbon::now()->lt(\Carbon\Carbon::parse($lamaran->tgl_mulai_resmi)->startOfDay()))

  <div class="card border-0 shadow-lg text-white mb-4 overflow-hidden position-relative"
    style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);"> {{-- Warna Cyan/Info --}}

    <div class="position-absolute top-0 start-0 w-100 h-100 bg-pattern opacity-10"></div>

    <div class="card-body p-5 text-center position-relative z-1">
      <div class="mb-3">
        <div class="d-inline-block bg-white text-info p-3 rounded-circle shadow">
          <i class="fas fa-hourglass-start fa-3x"></i>
        </div>
      </div>

      <h2 class="fw-bold">Program Belum Dimulai</h2>

      <p class="fs-5 opacity-75 mb-4">
        Halo, <strong>{{ $user->name }}</strong>. Periode magang Anda belum dimulai. <br>
        Silakan kembali lagi pada tanggal resmi di bawah ini.
      </p>

      <div class="bg-white bg-opacity-25 p-4 rounded-3 d-inline-block border border-white border-opacity-25">
        <small class="d-block text-uppercase opacity-75 fw-bold mb-1">Tanggal Mulai Resmi</small>
        <h1 class="fw-bold m-0 display-5">
          {{ \Carbon\Carbon::parse($lamaran->tgl_mulai_resmi)->translatedFormat('d F Y') }}
        </h1>
      </div>
    </div>
  </div>

  {{-- ================================================================================== --}}
  {{-- PRIORITAS 4: HARI KERJA NORMAL (Presensi / CHECK-IN) --}}
  {{-- ================================================================================== --}}
  @elseif($isHariKerja)

  <div class="card border-0 shadow-lg text-white mb-4 overflow-hidden position-relative"
    style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-pattern opacity-10"></div>

    <div class="card-body p-5 text-center position-relative z-1">
      <h6 class="text-uppercase opacity-75 mb-1" style="letter-spacing: 2px;">Presensi Hari Ini</h6>
      <h2 class="fw-bold mb-4">
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
      </h2>

      {{-- KONDISI 1: BELUM CHECK-IN --}}
      @if(!$presensi)

      @php
      $jamSekarang = \Carbon\Carbon::now();

      // Hitung Waktu Absen
      $jamBuka = \Carbon\Carbon::parse($lowongan->jam_buka_absen);
      $waktuBuka = \Carbon\Carbon::today()->setTimeFrom($jamBuka);

      $jamTutup = \Carbon\Carbon::parse($lowongan->jam_tutup_absen);
      $waktuTutup = \Carbon\Carbon::today()->setTimeFrom($jamTutup);

      $isBelumBuka = $jamSekarang->lt($waktuBuka);
      $isSudahTutup = $jamSekarang->gt($waktuTutup);
      $isBisaAbsen = !$isBelumBuka && !$isSudahTutup;
      @endphp

      <div class="py-2">
        @if($isBisaAbsen)
        {{-- CASE: WAKTUNYA ABSEN --}}
        <p class="mb-4 fs-5 opacity-75">Silakan lakukan Presensi masuk.</p>
        <form action="{{ route('presensi.checkin') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-light text-primary fw-bold btn-lg px-5 py-3 rounded-pill shadow-lg hover-scale">
            <i class="fas fa-sign-in-alt me-2"></i> Absen
          </button>
        </form>

        <div class="mt-4 small opacity-75">
          <i class="far fa-clock me-1"></i>
          Jadwal Masuk:
          <strong>{{ \Carbon\Carbon::parse($lowongan->jam_masuk_standar)->format('H:i') }}</strong>
          <span class="d-block text-warning small mt-1 fw-bold">
            (Ditutup pukul {{ $jamTutup->format('H:i') }})
          </span>
        </div>

        @elseif($isBelumBuka)
        {{-- CASE: KEPAGIAN --}}
        <div class="bg-white bg-opacity-10 p-4 rounded-3 d-inline-block border border-white border-opacity-25">
          <i class="fas fa-coffee fa-3x mb-3 opacity-75"></i>
          <h4 class="fw-bold">Belum Waktunya</h4>
          <p class="mb-0 opacity-75">Buka pukul: {{ $jamBuka->format('H:i') }}</p>
        </div>

        @elseif($isSudahTutup)
        {{-- CASE: KESIANGAN --}}
        <div class="bg-danger bg-opacity-25 p-4 rounded-3 d-inline-block border border-danger border-opacity-25 shadow-sm mb-4">
          <i class="fas fa-ban fa-3x mb-3 text-white"></i>
          <h4 class="fw-bold text-white">Absen Ditutup</h4>
          <p class="mb-0 text-white opacity-75">Batas waktu: {{ $jamTutup->format('H:i') }}</p>
        </div>

        <div class="alert alert-warning d-block text-start text-dark shadow-sm border-0">
          <div class="d-flex align-items-start">
            <i class="fas fa-exclamation-triangle me-3 mt-1 fa-lg text-warning"></i>
            <div>
              <p class="fw-bold mb-1">Informasi:</p>
              <p class="mb-2 small">Jika sakit/berhalangan, ajukan izin segera.</p>
              <a href="{{ route('izin.create') }}" class="btn btn-sm btn-outline-dark fw-bold rounded-pill">
                <i class="fas fa-paper-plane me-1"></i> Ajukan Izin
              </a>
            </div>
          </div>
        </div>
        @endif
      </div>

      {{-- KONDISI 2: SUDAH CHECK-IN (TANPA CHECK-OUT) --}}
      @else
      <div class="py-4">
        <div class="d-inline-block bg-white text-primary p-4 rounded-circle shadow mb-3">
          <i class="fas fa-check-double fa-3x"></i>
        </div>
        <h3 class="fw-bold">Presensi Anda Sudah Tercatat!</h3>
        <p class="opacity-75 mb-0">Terima kasih atas kontribusi Anda hari ini.</p>

        <div class="row justify-content-center mt-4">
          <div class="col-auto">
            <div class="badge bg-success bg-opacity-75 p-2 px-3">
              Masuk: {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>


  {{-- ================================================================================== --}}
  {{-- PRIORITAS 5: HARI LIBUR --}}
  {{-- ================================================================================== --}}
  @else
  <div class="card border-0 shadow-sm bg-secondary bg-opacity-10 mb-4">
    <div class="card-body text-center py-5">
      <div class="mb-3">
        <div class="d-inline-block bg-white p-3 rounded-circle shadow-sm">
          <i class="fas fa-mug-hot fa-3x text-secondary"></i>
        </div>
      </div>
      <h3 class="fw-bold text-dark">Hari Ini Libur</h3>
      <p class="text-muted mb-3">Tidak ada jadwal kerja hari ini.</p>
      <div class="mt-3 p-3 bg-white rounded shadow-sm d-inline-block">
        <small class="text-muted fw-bold d-block mb-2 text-uppercase">Jadwal Kerja Anda</small>
        @if(isset($lowongan->hari_kerja) && is_array($lowongan->hari_kerja))
        @foreach($lowongan->hari_kerja as $day)
        <span class="badge bg-secondary me-1 mb-1">{{ $day }}</span>
        @endforeach
        @endif
      </div>
    </div>
  </div>
  @endif

</div>

@push('styles')
<style>
  .hover-scale {
    transition: transform 0.2s;
  }

  .hover-scale:hover {
    transform: scale(1.05);
  }

  .bg-pattern {
    background-image: radial-gradient(rgba(255, 255, 255, 0.2) 1px, transparent 1px);
    background-size: 20px 20px;
  }
</style>
@endpush
@endsection