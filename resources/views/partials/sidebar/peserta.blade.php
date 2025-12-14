<ul class="nav nav-pills flex-column mb-auto">

  {{-- ================================================================= --}}
  {{-- 1. LOGIC PENENTUAN STATUS --}}
  {{-- ================================================================= --}}
  @php
  $user = auth()->user();
  // Ambil lamaran TERAKHIR (Latest)
  $lamaran = \App\Models\Lamaran::where('pengguna_id', $user->id)->latest()->first();

  $status = $lamaran ? $lamaran->status : null;

  // Definisi State (Keadaan)
  $isAktif = ($status === 'diterima'); // Sedang Magang
  $isSelesai = ($status === 'selesai'); // Alumni
  $isBerhenti = ($status === 'berhenti'); // Drop Out

  // Siapa yang boleh mencari lowongan?
  // Semua orang KECUALI yang sedang aktif magang.
  // Alumni & Drop Out boleh cari lagi (untuk re-apply).
  $canSearchJob = !$isAktif;
  @endphp

  {{-- ================================================================= --}}
  {{-- MENU UTAMA (SELALU MUNCUL) --}}
  {{-- ================================================================= --}}
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : 'text-dark' }}"
      href="{{ route('peserta.dashboard') }}">
      <i class="fas fa-home fa-fw me-2"></i> Dashboard Saya
    </a>
  </li>

  {{-- ================================================================= --}}
  {{-- GROUP: PENCARIAN LOWONGAN --}}
  {{-- Muncul jika: Belum Punya, Pending, Ditolak, Selesai, atau Berhenti --}}
  {{-- Hilang jika: Sedang Aktif (Diterima) --}}
  {{-- ================================================================= --}}
  @if($canSearchJob)
  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Bursa Magang
  </li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('guest.home') ? 'active' : 'text-dark' }}"
      href="{{ route('guest.home') }}">
      <i class="fas fa-search fa-fw me-2"></i> Cari Lowongan
    </a>
  </li>
  @endif

  {{-- ================================================================= --}}
  {{-- GROUP: AKTIVITAS HARIAN (HANYA SAAT AKTIF) --}}
  {{-- ================================================================= --}}
  @if($isAktif)
  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Aktivitas Magang
  </li>

  {{-- Menu Absensi (Biasanya Check-in ada di Dashboard, ini ke Riwayat) --}}
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('presensi.riwayat') ? 'active' : 'text-dark' }}"
      href="{{ route('presensi.riwayat') }}">
      <i class="fas fa-fingerprint fa-fw me-2"></i> Riwayat Kehadiran
    </a>
  </li>

  {{-- Menu Pengajuan Izin --}}
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('izin.*') ? 'active' : 'text-dark' }}"
      href="{{ route('izin.index') }}">
      <i class="fas fa-envelope-open-text fa-fw me-2"></i> Pengajuan Izin
    </a>
  </li>
  @endif

  {{-- ================================================================= --}}
  {{-- GROUP: ARSIP & DOKUMEN (HANYA ALUMNI / SELESAI) --}}
  {{-- ================================================================= --}}
  @if($isSelesai)
  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Arsip & Dokumen
  </li>

  {{-- Alumni boleh lihat riwayat kehadiran masa lalu (Read Only) --}}
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('presensi.riwayat') ? 'active' : 'text-dark' }}"
      href="{{ route('presensi.riwayat') }}">
      <i class="fas fa-history fa-fw me-2"></i> Arsip Kehadiran
    </a>
  </li>

  {{-- Fitur Download Sertifikat --}}
  <li class="nav-item">
    {{-- Ganti '#' dengan route download sertifikat jika sudah ada --}}
    <a class="nav-link text-dark" href="#">
      <i class="fas fa-certificate fa-fw me-2"></i> E-Sertifikat
    </a>
  </li>
  @endif



</ul>