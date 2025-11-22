<ul class="nav nav-pills flex-column mb-auto">
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }}" href="{{ route('dashboard') }}">
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
    <a class="nav-link {{ request()->routeIs('verifikasi.lamaran') ? 'active' : 'text-dark' }}" href="#">
      <i class="fas fa-check-double fa-fw me-2"></i>Verifikasi Lamaran
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link text-dark" href="#">
      <i class="fas fa-hand-holding fa-fw me-2"></i>Peminjaman Alat (Contoh)
    </a>
  </li>

  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Laporan
  </li>

  <li class="nav-item">
    <a class="nav-link text-dark" href="#">
      <i class="fas fa-file-pdf fa-fw me-2"></i>Laporan Presensi
    </a>
  </li>
</ul>