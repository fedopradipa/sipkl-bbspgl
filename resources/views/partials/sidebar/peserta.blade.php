<ul class="nav nav-pills flex-column mb-auto">
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : 'text-dark' }}" href="{{ route('peserta.dashboard') }}">
      <i class="fas fa-home fa-fw me-2"></i> Dashboard Saya
    </a>
  </li>

  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Pencarian
  </li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('guest.home') ? 'active' : 'text-dark' }}" href="{{ route('guest.home') }}">
      <i class="fas fa-search fa-fw me-2"></i> Cari Lowongan
    </a>
  </li>

  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Aktivitas Magang
  </li>

  {{-- Link Absensi hanya muncul jika sudah diterima (Sesuai logic dashboard) --}}
  @php
  // Ambil data lamaran untuk cek status (Simple logic di view)
  $lamaran = \App\Models\Lamaran::where('pengguna_id', auth()->id())->latest()->first();
  $isMagangActive = $lamaran && $lamaran->status == 'diterima';
  @endphp

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('riwayat.kehadiran') ? 'active' : 'text-dark' }}" href="{{route('presensi.riwayat') }}"
      @if(!$isMagangActive) disabled @endif>
      <i class="fas fa-fingerprint fa-fw me-2"></i> Riwayat Kehadiran
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('jurnal.*') ? 'active' : 'text-dark' }}" href="#"
      @if(!$isMagangActive) disabled @endif>
      <i class="fas fa-book fa-fw me-2"></i> Jurnal Kegiatan
    </a>
  </li>

  <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
    Lainnya
  </li>

  <li class="nav-item">
    <a class="nav-link text-dark" href="#">
      <i class="fas fa-user fa-fw me-2"></i> Profil Saya
    </a>
  </li>
</ul>