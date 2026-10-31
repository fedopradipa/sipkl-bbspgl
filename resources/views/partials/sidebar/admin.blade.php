<ul class="nav nav-pills flex-column mb-auto">

  {{-- ========================================================== --}}
  {{-- DASHBOARD --}}
  {{-- ========================================================== --}}
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-dark' }}"
      href="{{ route('admin.dashboard') }}">
      <i class="fas fa-home fa-fw me-2"></i>Dashboard
    </a>
  </li>

  {{-- ========================================================== --}}
  {{-- GROUP 1: MASTER DATA --}}
  {{-- ========================================================== --}}
  <li class="nav-heading px-3 mt-4 mb-1 text-muted text-uppercase small fw-bold">
    Master Data
  </li>

  <li class="nav-item">
    {{-- Lowongan & Pembimbing ada di Shared Route, tidak pakai prefix admin. --}}
    <a class="nav-link {{ request()->routeIs('lowongan.*') ? 'active' : 'text-dark' }}"
      href="{{ route('lowongan.index') }}">
      <i class="fas fa-briefcase fa-fw me-2"></i>Lowongan
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('pembimbing.*') ? 'active' : 'text-dark' }}"
      href="{{ route('pembimbing.index') }}">
      <i class="fas fa-user-tie fa-fw me-2"></i>Pembimbing Lapangan
    </a>
  </li>

  {{-- ========================================================== --}}
  {{-- GROUP 2: PENERIMAAN PKL (REKRUTMEN) --}}
  {{-- ========================================================== --}}
  <li class="nav-heading px-3 mt-4 mb-1 text-muted text-uppercase small fw-bold">
    Penerimaan PKL
  </li>

  <li class="nav-item">
    {{-- UPDATE: admin.lamaran.* --}}
    <a class="nav-link {{ request()->routeIs('admin.lamaran.*') ? 'active' : 'text-dark' }} d-flex justify-content-between align-items-center"
      href="{{ route('admin.lamaran.index') }}">
      <div>
        <i class="fas fa-file-import fa-fw me-2"></i>Verifikasi Lamaran
      </div>

      {{-- Counter Logic --}}
      @php $pendingLamaran = \App\Models\Lamaran::where('status', 'pending')->count(); @endphp
      @if($pendingLamaran > 0)
      <span class="badge bg-danger rounded-pill">{{ $pendingLamaran }}</span>
      @endif
    </a>
  </li>

  {{-- ========================================================== --}}
  {{-- GROUP 3: MONITORING & AKTIVITAS (PESERTA AKTIF) --}}
  {{-- ========================================================== --}}
  <li class="nav-heading px-3 mt-4 mb-1 text-muted text-uppercase small fw-bold">
    Monitoring & Aktivitas
  </li>

  {{-- Peserta Aktif --}}
  <li class="nav-item">
    {{-- UPDATE: admin.peserta_aktif.* --}}
    <a class="nav-link {{ request()->routeIs('admin.peserta_aktif.*') ? 'active' : 'text-dark' }}"
      href="{{ route('admin.peserta_aktif.index') }}">
      <i class="fas fa-users fa-fw me-2"></i>Peserta Aktif
    </a>
  </li>

  {{-- Presensi Harian --}}
  <li class="nav-item">
    {{-- UPDATE: admin.presensi.* --}}
    <a class="nav-link {{ request()->routeIs('admin.presensi.*') ? 'active' : 'text-dark' }}"
      href="{{ route('admin.presensi.index') }}">
      <i class="fas fa-clock fa-fw me-2"></i>Presensi Harian
    </a>
  </li>

  {{-- Validasi Izin/Sakit --}}
  <li class="nav-item">
    {{-- UPDATE: admin.izin.* --}}
    <a class="nav-link {{ request()->routeIs('admin.izin.*') ? 'active' : 'text-dark' }} d-flex justify-content-between align-items-center"
      href="{{ route('admin.izin.index') }}">
      <div>
        <i class="fas fa-envelope-open-text fa-fw me-2"></i>Validasi Izin/Sakit
      </div>

      {{-- Counter Logic --}}
      @php $pendingIzin = \App\Models\PengajuanIzin::where('status', 'pending')->count(); @endphp
      @if($pendingIzin > 0)
      <span class="badge bg-warning text-dark rounded-pill">{{ $pendingIzin }}</span>
      @endif
    </a>
  </li>

  {{-- ========================================================== --}}
  {{-- GROUP 4: LAPORAN & ARSIP --}}
  {{-- ========================================================== --}}
  <li class="nav-heading px-3 mt-4 mb-1 text-muted text-uppercase small fw-bold">
    Laporan & Arsip
  </li>

  {{-- [BARU] Laporan Penerimaan --}}
  <li class="nav-item">
    {{-- PERBAIKAN: Tambahkan 'admin.' pada routeIs dan route --}}
    <a class="nav-link {{ request()->routeIs('admin.laporan.penerimaan.*') ? 'active' : 'text-dark' }}"
      href="{{ route('admin.laporan.penerimaan.index') }}">
      <i class="fas fa-clipboard-list fa-fw me-2"></i>Laporan Penerimaan
    </a>
  </li>

  {{-- Rekap Presensi --}}
  <li class="nav-item">
    {{-- UPDATE: admin.reports.* --}}
    <a class="nav-link  {{ request()->routeIs('admin.reports.*') ? 'active' : 'text-dark' }}"
      href="{{ route('admin.reports.attendance.index') }}">
      <i class="fas fa-print fa-fw me-2"></i>Rekap Presensi
    </a>
  </li>

  {{-- Data Alumni --}}
  <li class="nav-item">
    {{-- UPDATE: admin.alumni.* --}}
    <a class="nav-link {{ request()->routeIs('admin.alumni.*') ? 'active' : 'text-dark' }}"
      href="{{ route('admin.alumni.index') }}">
      <i class="fas fa-graduation-cap fa-fw me-2"></i>Data Alumni
    </a>
  </li>
  <!-- <li class="nav-heading px-3 mt-4 mb-1 text-muted text-uppercase small fw-bold">
    manajemen sistem
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : 'text-dark' }}" href="{{ route('user.index') }}">
      <i class="fas fa-users-cog fa-fw me-2"></i> Kelola User
    </a>
  </li> -->
</ul>