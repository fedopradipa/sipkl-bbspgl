@extends('layouts.app')

@section('content')
<div class="container py-4">

  {{-- Header Sapaan --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h5 class="text-muted mb-0 small">Halo!</h5>
      <h2 class="fw-bold text-dark">{{ $user->name ?? $user->nama }}</h2>
    </div>
    <div class="bg-white p-1 rounded-circle shadow-sm border">
      @if($user->foto_profil)
      <img src="{{ asset('storage/foto_profil/' . $user->foto_profil) }}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
      @else
      <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
        <i class="fas fa-user text-secondary"></i>
      </div>
      @endif
    </div>
  </div>

  {{-- SECTION UTAMA: STATE-BASED UI --}}
  @switch($state)

  {{-- CASE 1: PENDING --}}
  @case('pending')
  <div class="card border-0 shadow-sm bg-warning bg-opacity-10 mb-4">
    <div class="card-body text-center py-5">
      <div class="mb-3">
        <div class="d-inline-block bg-white p-3 rounded-circle shadow-sm">
          <i class="fas fa-hourglass-half fa-3x text-warning"></i>
        </div>
      </div>
      <h4 class="fw-bold text-dark">Lamaran Sedang Diproses</h4>
      <p class="text-muted">
        Lamaran Anda untuk posisi <span class="badge bg-primary">{{ $lamaran->lowongan->judul }}</span> sedang diverifikasi.
      </p>
      <button class="btn btn-warning px-4 fw-bold rounded-pill text-white shadow-sm" disabled>
        <i class="fas fa-sync-alt fa-spin me-2"></i> Menunggu Verifikasi
      </button>
    </div>
  </div>
  @break

  {{-- CASE 2: DITOLAK --}}
  @case('ditolak')
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
        <strong>Catatan Admin:</strong> <br> {{ $lamaran->catatan_admin }}
      </div>
      @endif
      </p>
      <a href="{{ route('guest.home') }}" class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm">
        Cari Lowongan Lain
      </a>
    </div>
  </div>
  @break

  {{-- CASE 3: PRA-MULAI --}}
  @case('pra_mulai')
  <div class="card border-0 shadow-sm mb-4 border-start border-primary border-5">
    <div class="card-body p-4">
      <h4 class="fw-bold text-primary mb-2">Selamat! Anda Diterima 🎉</h4>
      <p class="text-muted mb-4">Masa magang Anda belum dimulai. Silakan persiapkan diri.</p>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="p-3 bg-light rounded border">
            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Tanggal Mulai</small>
            <span class="fs-5 fw-bold text-dark">{{ \Carbon\Carbon::parse($lamaran->tgl_mulai_resmi)->format('d F Y') }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-3 bg-light rounded border">
            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Pembimbing</small>
            <span class="fs-5 fw-bold text-dark">{{ $lamaran->pembimbing->nama ?? 'Belum Ditentukan' }}</span>
          </div>
        </div>
      </div>

      <div class="d-grid mt-4">
        @if($lamaran->path_surat_balasan)
        <a href="{{ asset($lamaran->path_surat_balasan) }}" target="_blank" class="btn btn-outline-primary btn-lg shadow-sm fw-bold">
          <i class="fas fa-download me-2"></i> Download Surat Penerimaan
        </a>
        @endif
      </div>
    </div>
  </div>
  @break
  {{-- CASE 4: ACTIVE (MASA MAGANG) --}}
  @case('active')

  {{-- LOGIKA UTAMA: CEK HARI KERJA --}}
  @if($isHariKerja)

  {{-- === TAMPILAN JIKA HARI KERJA === --}}
  <div class="card border-0 shadow-lg bg-gradient-primary text-white mb-4 overflow-hidden position-relative">
    <div class="card-body p-5 text-center position-relative z-1">
      <h6 class="text-uppercase letter-spacing-2 opacity-75 mb-1">Absensi Hari Ini</h6>
      {{-- Gunakan translatedFormat agar formatnya sama dengan PHP biasa --}}
      <h2 class="fw-bold mb-4">
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
      </h2>
      {{-- 1. BELUM MASUK --}}
      @if(!$presensi)
      <div class="py-2">
        <p class="mb-4 fs-5">Anda belum melakukan Check-in hari ini.</p>
        <form action="{{ route('presensi.checkin') }}" method="POST">
          @csrf
          <input type="hidden" name="latitude" id="lat">
          <input type="hidden" name="longitude" id="long">
          <button type="submit" id="btn-checkin" class="btn btn-light text-primary fw-bold btn-lg px-5 py-3 rounded-pill shadow-lg hover-scale" disabled>
            <i class="fas fa-spinner fa-spin me-2" id="loading-icon"></i> MENCARI LOKASI...
          </button>
        </form>
      </div>

      {{-- 2. SUDAH MASUK, BELUM PULANG --}}
      @elseif($presensi->jam_masuk && !$presensi->jam_pulang)
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="bg-white bg-opacity-25 p-3 rounded-3 mb-4">
            <span class="d-block small opacity-75">Waktu Masuk</span>
            <span class="fs-1 fw-bold">{{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}</span>
          </div>
        </div>
      </div>

      {{-- LOGIKA TOMBOL PULANG --}}
      @php
      $jamPulangStandar = \Carbon\Carbon::parse($lamaran->lowongan->jam_pulang_standar);
      $batasWaktuPulang = \Carbon\Carbon::today()->setTimeFrom($jamPulangStandar);
      $isWaktunyaPulang = \Carbon\Carbon::now()->gte($batasWaktuPulang);
      @endphp

      <form action="{{ route('presensi.checkout') }}" method="POST">
        @csrf
        <input type="hidden" name="latitude" id="lat_out">
        <input type="hidden" name="longitude" id="long_out">

        @if($isWaktunyaPulang)
        <button type="submit" id="btn-checkout" class="btn btn-danger fw-bold btn-lg px-5 py-3 rounded-pill shadow-lg hover-scale" disabled>
          <i class="fas fa-spinner fa-spin me-2" id="loading-out"></i> MENENTUKAN LOKASI...
        </button>
        @else
        <div class="d-inline-block px-4 py-2 rounded-pill bg-dark bg-opacity-25 border border-white border-opacity-25">
          <i class="fas fa-clock me-2"></i>
          Absen pulang dibuka pukul <strong>{{ $jamPulangStandar->format('H:i') }}</strong>
        </div>
        <br>
        <button type="button" class="btn btn-secondary fw-bold btn-lg px-5 mt-3 rounded-pill" disabled style="opacity: 0.7">
          BELUM WAKTUNYA
        </button>
        @endif
      </form>

      {{-- 3. SUDAH SELESAI --}}
      @else
      <div class="py-4">
        <div class="d-inline-block bg-white text-success p-3 rounded-circle shadow mb-3">
          <i class="fas fa-check fa-3x"></i>
        </div>
        <h4>Anda Sudah Selesai!</h4>
        <p class="opacity-75">Terima kasih atas kontribusi Anda hari ini.</p>
      </div>
      @endif
    </div>
  </div>

  @else
  {{-- === TAMPILAN JIKA LIBUR (BUKAN HARI KERJA) === --}}
  <div class="card border-0 shadow-sm bg-secondary bg-opacity-10 mb-4">
    <div class="card-body text-center py-5">
      <div class="mb-3">
        <div class="d-inline-block bg-white p-3 rounded-circle shadow-sm">
          <i class="fas fa-mug-hot fa-3x text-secondary"></i>
        </div>
      </div>
      <h3 class="fw-bold text-dark">Hari Ini Libur</h3>
      <p class="text-muted mb-0">
        Hari ini tidak termasuk dalam jadwal kerja Anda.
      </p>
      <div class="mt-3">
        <small class="text-muted fw-bold">Jadwal Anda:</small> <br>
        @foreach($lamaran->hari_kerja_resmi as $day)
        <span class="badge bg-secondary me-1">{{ $day }}</span>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  {{-- MENU CEPAT (Selalu Muncul) --}}
  <h6 class="text-muted fw-bold mb-3 ms-1">Menu Cepat</h6>
  <div class="row g-3">
    <div class="col-6">
      <a href="#" class="card border-0 shadow-sm h-100 text-decoration-none text-dark hover-up bg-white">
        <div class="card-body text-center py-4">
          <div class="mb-2 text-info"><i class="fas fa-book fa-2x"></i></div>
          <h6 class="fw-bold mb-0 small">Jurnal Kegiatan</h6>
        </div>
      </a>
    </div>
    <div class="col-6">
      <a href="{{route('presensi.riwayat')}}" class="card border-0 shadow-sm h-100 text-decoration-none text-dark hover-up bg-white">
        <div class="card-body text-center py-4">
          <div class="mb-2 text-primary"><i class="fas fa-history fa-2x"></i></div>
          <h6 class="fw-bold mb-0 small">Riwayat Absen</h6>
        </div>
      </a>
    </div>
  </div>
  @break

  {{-- CASE 5: SELESAI --}}
  @case('selesai')
  <div class="card border-0 shadow-sm border-top border-success border-5 mb-4">
    <div class="card-body text-center p-5">
      <i class="fas fa-graduation-cap fa-5x text-success mb-3"></i>
      <h3 class="fw-bold text-dark">Masa Magang Selesai!</h3>
      <p class="text-muted mb-4">Terima kasih telah menyelesaikan PKL dengan baik.</p>
      <div class="d-grid gap-2 col-md-8 mx-auto">
        <button class="btn btn-outline-primary p-3 fw-bold">
          <i class="fas fa-file-upload me-2"></i> Upload Laporan Akhir
        </button>
        <button class="btn btn-outline-success p-3 fw-bold">
          <i class="fas fa-certificate me-2"></i> Download Sertifikat
        </button>
      </div>
    </div>
  </div>
  @break

  @endswitch
</div>

{{-- Style Tambahan --}}
<style>
  .bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
  }

  .hover-up {
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .hover-up:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
  }

  .hover-scale:hover {
    transform: scale(1.05);
  }

  .letter-spacing-2 {
    letter-spacing: 2px;
  }
</style>

{{-- Script Geolocation --}}
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const btnIn = document.getElementById('btn-checkin');
    const btnOut = document.getElementById('btn-checkout');

    if (navigator.geolocation) {
      const options = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      };

      function success(position) {
        const latIn = document.getElementById('lat');
        const longIn = document.getElementById('long');
        const latOut = document.getElementById('lat_out');
        const longOut = document.getElementById('long_out');

        if (latIn) {
          latIn.value = position.coords.latitude;
          longIn.value = position.coords.longitude;
        }
        if (latOut) {
          latOut.value = position.coords.latitude;
          longOut.value = position.coords.longitude;
        }

        if (btnIn) {
          btnIn.disabled = false;
          btnIn.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i> CHECK IN MASUK';
        }
        if (btnOut) {
          btnOut.disabled = false;
          btnOut.innerHTML = '<i class="fas fa-sign-out-alt me-2"></i> CHECK OUT PULANG';
        }
      }

      function error(err) {
        console.warn(`ERROR(${err.code}): ${err.message}`);
        const msg = "Gagal mendeteksi lokasi.";
        if (btnIn) btnIn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + msg;
        if (btnOut) btnOut.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + msg;
      }

      navigator.geolocation.watchPosition(success, error, options);
    } else {
      alert("Browser tidak mendukung Geolocation.");
    }
  });
</script>
@endsection