<div class="row g-2 mb-4">
  {{-- Konfigurasi Data --}}
  @php
  $hadir = $summary['hadir'] ?? 0;
  $terlambat = $summary['terlambat'] ?? 0;
  $izinSakit = $summary['izin_sakit'] ?? 0;
  $alpha = $summary['alpha'] ?? 0;

  $totalHadir = $hadir + $terlambat;

  $cards = [
  ['title' => 'Hadir', 'val' => $totalHadir, 'color' => 'success', 'icon' => 'fa-check-circle'],
  ['title' => 'Terlambat', 'val' => $terlambat, 'color' => 'warning', 'icon' => 'fa-clock'],
  ['title' => 'Sakit / Izin', 'val' => $izinSakit, 'color' => 'info', 'icon' => 'fa-file-medical'],
  ['title' => 'Alpha', 'val' => $alpha, 'color' => 'danger', 'icon' => 'fa-times-circle'],
  ];
  @endphp

  @foreach($cards as $c)
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm h-100 overflow-hidden">
      <div class="card-body position-relative p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <h6 class="text-uppercase fw-bold text-{{ $c['color'] }} small mb-0" style="font-size: 0.7rem;">
            {{ $c['title'] }}
          </h6>
        </div>
        <h2 class="fw-bold text-dark mb-0">{{ $c['val'] }}</h2>

        {{-- Dekorasi Icon Background --}}
        <i class="fas {{ $c['icon'] }} position-absolute text-{{ $c['color'] }}"
          style="bottom: -10px; right: -5px; font-size: 3.5rem; opacity: 0.15; transform: rotate(-15deg);"></i>
      </div>
    </div>
  </div>
  @endforeach
</div>