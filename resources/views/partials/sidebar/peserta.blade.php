<ul class="nav nav-pills flex-column mb-auto">

  {{-- ================================================================= --}}
  {{-- 1. LOGIC PENENTUAN STATUS --}}
  {{-- ================================================================= --}}
  @php
  $user = auth()->user();
  $lamaran = \App\Models\Lamaran::where('pengguna_id', $user->id)->latest()->first();

  $status = $lamaran ? $lamaran->status : null;

  // --- State User ---
  // Pastikan string 'pending' sesuai dengan value di database Anda (misal: 'menunggu_verifikasi' atau 'pending')
  $isPending = ($status === 'pending' || $status === 'menunggu_verifikasi');
  $isAktif = ($status === 'diterima'); // Sedang PKL
  $isSelesai = ($status === 'selesai'); // Alumni
  $isBerhenti = ($status === 'berhenti'); // Drop Out

  // --- Logic Menu ---

  // 1. Cari Lowongan:
  // Muncul jika TIDAK sedang aktif DAN TIDAK sedang menunggu hasil (pending)
  // Jadi kalau pending, menu ini hilang (sisa Dashboard saja)
  $canSearchJob = (!$isAktif && !$isPending);

  // 2. Menu Absen:
  // Muncul jika Aktif DAN Tanggal belum lewat
  $showMenuAbsen = false;
  if ($isAktif && $lamaran->tgl_selesai_resmi) {
  $hariIni = \Carbon\Carbon::now()->startOfDay();
  $tglSelesai = \Carbon\Carbon::parse($lamaran->tgl_selesai_resmi)->endOfDay();
  $showMenuAbsen = $hariIni->lte($tglSelesai);
  }

  // 3. Menu Riwayat:
  // Muncul untuk peserta Aktif, Alumni (Selesai), ATAU Berhenti
  $showRiwayat = ($isAktif || $isSelesai || $isBerhenti);
  @endphp

  {{-- ================================================================= --}}
  {{-- A. DASHBOARD (UMUM - SELALU MUNCUL) --}}
  {{-- ================================================================= --}}
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : 'text-dark' }}"
      href="{{ route('peserta.dashboard') }}">
      <i class="fas fa-home fa-fw me-2"></i> Dashboard Saya
    </a>
  </li>

  {{-- ================================================================= --}}
  {{-- B. BURSA PKL (PENCARIAN) --}}
  {{-- ================================================================= --}}
  {{-- Menu ini akan HILANG jika user sedang pending atau sudah diterima --}}
  @if($canSearchJob)
  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Bursa PKL
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('peserta.lowongan.*') ? 'active' : 'text-dark' }}"
      href="{{ route('peserta.lowongan.index') }}">
      <i class="fas fa-search fa-fw me-2"></i> Cari Lowongan
    </a>
  </li>
  @endif

  {{-- ================================================================= --}}
  {{-- C. AKTIVITAS (ACTION) - HANYA SAAT AKTIF --}}
  {{-- ================================================================= --}}
  @if($isAktif)
  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Aktivitas Harian
  </li>

  {{-- 1. Input Absen --}}
  @if($showMenuAbsen)
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('presensi.index') ? 'active' : 'text-dark' }}"
      href="{{ route('presensi.index') }}">
      <i class="fas fa-clock fa-fw me-2"></i> Presensi Harian
    </a>
  </li>
  @endif

  {{-- 2. Input Izin --}}
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('izin.*') ? 'active' : 'text-dark' }}"
      href="{{ route('izin.index') }}">
      <i class="fas fa-envelope-open-text fa-fw me-2"></i> Pengajuan Izin/Sakit
    </a>
  </li>
  @endif

  {{-- ================================================================= --}}
  {{-- D. DATA & RIWAYAT (REPORT) - AKTIF, ALUMNI & BERHENTI --}}
  {{-- ================================================================= --}}
  @if($showRiwayat)
  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Riwayat
  </li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('presensi.riwayat') ? 'active' : 'text-dark' }}"
      href="{{ route('presensi.riwayat') }}">
      <i class="fas fa-history fa-fw me-2"></i> Riwayat Presensi
    </a>
  </li>
  @endif

</ul>