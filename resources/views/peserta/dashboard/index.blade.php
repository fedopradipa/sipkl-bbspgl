@extends('layouts.app')

@section('content')
<div class="container py-4">
  {{-- SECTION HEADER: Sapaan --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h5 class="text-muted mb-0 small">Halo, Semangat Pagi!</h5>
      <h2 class="fw-bold text-dark">{{ $user->nama }}</h2>
    </div>
    <div class="bg-white p-2 rounded-circle shadow-sm">
      {{-- Placeholder Foto Profil --}}
      <i class="fas fa-user-circle fa-2x text-secondary"></i>
    </div>
  </div>

  {{-- SECTION UTAMA: STATE-BASED UI --}}
  @switch($state)

  {{-- CASE 1: PENDING (Menunggu) --}}
  @case('pending')
  <div class="card border-0 shadow-sm bg-warning bg-opacity-10 mb-4">
    <div class="card-body text-center py-5">
      <div class="mb-3">
        <i class="fas fa-hourglass-half fa-3x text-warning"></i>
      </div>
      <h4 class="fw-bold text-dark">Lamaran Sedang Diproses</h4>
      <p class="text-muted">
        Lamaran Anda untuk posisi <strong>{{ $lamaran->lowongan->judul }}</strong> sedang diverifikasi oleh Admin BBSPGL.
        Mohon cek secara berkala.
      </p>
      <button class="btn btn-warning px-4 fw-bold" disabled>Menunggu Verifikasi</button>
    </div>
  </div>
  @break

  {{-- CASE 2: DITOLAK --}}
  @case('ditolak')
  <div class="card border-0 shadow-sm bg-danger bg-opacity-10 mb-4">
    <div class="card-body text-center py-5">
      <div class="mb-3">
        <i class="fas fa-times-circle fa-3x text-danger"></i>
      </div>
      <h4 class="fw-bold text-danger">Lamaran Tidak Diterima</h4>
      <p class="text-muted mb-4">
        Mohon maaf, kualifikasi Anda belum sesuai dengan kebutuhan kami saat ini.
        <br>
        <strong>Catatan Admin:</strong>
        <span class="fst-italic">"{{ $lamaran->catatan_admin ?? 'Tidak ada catatan khusus.' }}"</span>
      </p>
      {{-- Tombol Cari Lowongan Lain --}}
      <a href="{{ route('guest.home') }}" class="btn btn-outline-danger fw-bold">
        Cari Lowongan Lain
      </a>
    </div>
  </div>
  @break

  {{-- CASE 3: PRA-MULAI (Diterima, tapi belum tanggalnya) --}}
  @case('pra_mulai')
  <div class="card border-0 shadow-sm mb-4 border-start border-primary border-4">
    <div class="card-body p-4">
      <h5 class="fw-bold text-primary mb-3">Selamat! Anda Diterima 🎉</h5>
      <p class="text-muted mb-3">
        Status Anda sudah aktif, namun periode magang belum dimulai.
        Silakan persiapkan diri Anda.
      </p>
      <div class="alert alert-light border d-flex align-items-center" role="alert">
        <i class="fas fa-calendar-alt text-primary me-3 fa-lg"></i>
        <div>
          <small class="text-muted d-block">Tanggal Mulai Resmi</small>
          <strong class="text-dark">{{ \Carbon\Carbon::parse($lamaran->tgl_mulai_resmi)->format('d F Y') }}</strong>
        </div>
      </div>

      {{-- Info Pembimbing --}}
      @if($lamaran->pembimbing)
      <h6 class="fw-bold mt-4 text-secondary">Pembimbing Lapangan</h6>
      <div class="d-flex align-items-center bg-white p-3 rounded border shadow-sm">
        <div class="bg-light p-2 rounded-circle me-3">
          <i class="fas fa-user-tie fa-lg text-secondary"></i>
        </div>
        <div>
          <h6 class="mb-0 fw-bold">{{ $lamaran->pembimbing->nama }}</h6>
          <small class="text-muted">{{ $lamaran->pembimbing->jabatan }}</small>
          @if($lamaran->pembimbing->no_hp)
          <a href="https://wa.me/{{ $lamaran->pembimbing->no_hp }}" target="_blank" class="d-block text-success mt-1 small text-decoration-none">
            <i class="fab fa-whatsapp me-1"></i> Hubungi via WhatsApp
          </a>
          @endif
        </div>
      </div>
      @endif

      {{-- Download Surat Balasan --}}
      <div class="d-grid mt-4">
        <button class="btn btn-primary btn-lg shadow-sm">
          <i class="fas fa-download me-2"></i> Download Surat Penerimaan
        </button>
      </div>
    </div>
  </div>
  @break

  {{-- CASE 4: ACTIVE (MASA MAGANG) --}}
  @case('active')
  {{-- 4.A. Card Absensi (Paling Penting) --}}
  <div class="card border-0 shadow bg-primary text-white mb-4" style="background: linear-gradient(45deg, #0d6efd, #0a58ca);">
    <div class="card-body p-4 text-center">
      <h5 class="mb-4 opacity-75">Absensi Hari Ini ({{ \Carbon\Carbon::now()->format('d M Y') }})</h5>

      {{-- LOGIKA TOMBOL ABSEN --}}
      @if(!$presensi)
      {{-- Belum Check In --}}
      <div class="py-3">
        <i class="fas fa-fingerprint fa-4x mb-3 text-white-50"></i>
        <br>
        <p>Anda belum melakukan Check-in hari ini.</p>
        <form action="{{ route('presensi.checkin') }}" method="POST">
          @csrf
          {{-- Kirim lokasi geolokasi nanti via JS --}}
          <input type="hidden" name="latitude" id="lat">
          <input type="hidden" name="longitude" id="long">
          <button type="submit" class="btn btn-light text-primary fw-bold btn-lg px-5 rounded-pill shadow">
            CHECK IN MASUK
          </button>
        </form>
      </div>

      @elseif($presensi->jam_masuk && !$presensi->jam_pulang)
      {{-- Sudah Masuk, Belum Pulang --}}
      <div class="py-3">
        <h1 class="fw-bold mb-0">{{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}</h1>
        <small class="opacity-75 d-block mb-4">Waktu Masuk</small>

        <form action="{{ route('presensi.checkout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-danger fw-bold btn-lg px-5 rounded-pill shadow">
            CHECK OUT PULANG
          </button>
        </form>
      </div>

      @else
      {{-- Sudah Selesai Hari Ini --}}
      <div class="py-3">
        <i class="fas fa-check-circle fa-4x mb-3 text-light"></i>
        <h4>Anda Sudah Selesai!</h4>
        <p class="opacity-75">Terima kasih atas kontribusi Anda hari ini. Selamat beristirahat.</p>
      </div>
      @endif
    </div>
  </div>

  {{-- 4.B. Menu Cepat --}}
  <div class="row g-3">
    <div class="col-6">
      <a href="#" class="card border-0 shadow-sm h-100 text-decoration-none text-dark hover-up">
        <div class="card-body text-center py-4">
          <i class="fas fa-book fa-2x text-info mb-2"></i>
          <h6 class="fw-bold mb-0">Jurnal Kegiatan</h6>
        </div>
      </a>
    </div>
    <div class="col-6">
      <a href="#" class="card border-0 shadow-sm h-100 text-decoration-none text-dark hover-up">
        <div class="card-body text-center py-4">
          <i class="fas fa-history fa-2x text-secondary mb-2"></i>
          <h6 class="fw-bold mb-0">Riwayat Absen</h6>
        </div>
      </a>
    </div>
  </div>
  @break

  {{-- CASE 5: SELESAI (FINISH) --}}
  @case('selesai')
  <div class="card border-0 shadow-sm border-top border-success border-4 mb-4">
    <div class="card-body text-center p-5">
      <i class="fas fa-graduation-cap fa-4x text-success mb-3"></i>
      <h3 class="fw-bold">Masa Magang Selesai!</h3>
      <p class="text-muted">Terima kasih telah menyelesaikan PKL di BBSPGL.</p>

      <hr>

      <h6 class="fw-bold text-start mb-3">Langkah Terakhir:</h6>
      <div class="d-grid gap-2">
        <button class="btn btn-outline-primary text-start p-3">
          <i class="fas fa-file-upload me-2"></i> Upload Laporan Akhir
        </button>
        <button class="btn btn-outline-success text-start p-3">
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
  .hover-up {
    transition: transform 0.2s;
  }

  .hover-up:hover {
    transform: translateY(-5px);
  }
</style>

{{-- Script Geolocation Sederhana (Untuk persiapan Absen) --}}
<script>
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      if (document.getElementById('lat')) {
        document.getElementById('lat').value = position.coords.latitude;
        document.getElementById('long').value = position.coords.longitude;
      }
    });
  }
</script>
@endsection