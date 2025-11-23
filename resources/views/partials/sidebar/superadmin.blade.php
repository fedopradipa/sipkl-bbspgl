<ul class="nav nav-pills flex-column mb-auto">
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : 'text-dark' }}" href="{{ route('superadmin.dashboard') }}">
      <i class="fas fa-chart-pie fa-fw me-2"></i> Dashboard Pusat
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

  <li class="nav-header mt-3 mb-1 text-muted small fw-bold ps-3">Master Data</li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('lowongan.*') ? 'active' : 'text-dark' }}" href="{{ route('lowongan.index') }}">
      <i class="fas fa-briefcase fa-fw me-2"></i> Lowongan
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('pembimbing.*') ? 'active' : 'text-dark' }}" href="{{ route('pembimbing.index') }}">
      <i class="fas fa-user-tie fa-fw me-2"></i> Pembimbing Lapangan
    </a>
  </li>

  <li class="nav-header mt-3 mb-1 text-muted small fw-bold ps-3">Transaksi</li>
  {{-- Superadmin punya semua transaksi/verifikasi --}}
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('verifikasi.lamaran') ? 'active' : 'text-dark' }}" href="#">
      <i class="fas fa-check-double fa-fw me-2"></i> Verifikasi Lamaran
    </a>
  </li>
</ul>