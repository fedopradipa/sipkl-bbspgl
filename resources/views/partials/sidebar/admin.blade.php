<ul class="nav nav-pills flex-column mb-auto">
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-dark' }}" href="{{ route('admin.dashboard') }}">
      <i class="fas fa-home fa-fw me-2"></i>Dashboard
    </a>
  </li>

  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Master Data
  </li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('lowongan.*') ? 'active' : 'text-dark' }}" href="{{ route('lowongan.index') }}">
      <i class="fas fa-briefcase fa-fw me-2"></i>Lowongan
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('pembimbing.*') ? 'active' : 'text-dark' }}" href="{{ route('pembimbing.index') }}">
      <i class="fas fa-user-tie fa-fw me-2"></i>Pembimbing Lapangan
    </a>
  </li>

  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Verifikasi & Transaksi
  </li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('lamaran.*') ? 'active' : 'text-dark' }} d-flex justify-content-between align-items-center"
      href="{{ route('lamaran.index.admin') }}">

      {{-- Label Menu --}}
      <div>
        <i class="fas fa-check-double fa-fw me-2"></i>Verifikasi Lamaran
      </div>

      {{-- LOGIC COUNTER: Hitung lamaran status 'pending' --}}
      @php
      $pendingLamaranCount = \App\Models\Lamaran::where('status', 'pending')->count();
      @endphp

      {{-- Tampilkan Badge jika ada yang pending --}}
      @if($pendingLamaranCount > 0)
      <span class="badge bg-danger rounded-pill ms-auto">
        {{ $pendingLamaranCount }}
      </span>
      @endif
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('izin.*') ? 'active' : 'text-dark' }}" href="{{ route('izin.index.admin') }}">
      <i class="fas fa-envelope-open-text fa-fw me-2"></i> Verifikasi Izin
      {{-- Opsional: Badge hitung pending --}}
      @php $countIzin = \App\Models\PengajuanIzin::where('status', 'pending')->count(); @endphp
      @if($countIzin > 0)
      <span class="badge bg-danger rounded-pill ms-auto">{{ $countIzin }}</span>
      @endif
    </a>
  </li>
  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Presensi
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('presensi.index.admin') ? 'active' : 'text-dark' }}"
      href="{{ route('presensi.index.admin') }}">
      <i class="fas fa-file-pdf fa-fw me-2"></i>Presensi Peserta
    </a>
  </li>

  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Manajemen Sistem
  </li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('role.*') ? 'active' : 'text-dark' }}" href="{{ route('role.index') }}">
      <i class="fas fa-user-shield fa-fw me-2"></i> Kelola Role
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : 'text-dark' }}" href="{{ route('user.index') }}">
      <i class="fas fa-users-cog fa-fw me-2"></i> Kelola User
    </a>
  </li>
</ul>