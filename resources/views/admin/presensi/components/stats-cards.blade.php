<div class="row g-2 mb-4"> {{-- Ganti g-3 jadi g-2 agar jarak antar card lebih rapat --}}
  {{-- Card Generator Loop --}}
  @php
  $cards = [
  ['title' => 'Hadir', 'val' => $ringkasan['hadir'], 'color' => 'success', 'icon' => 'fa-check-circle'],
  ['title' => 'Terlambat', 'val' => $ringkasan['terlambat'], 'color' => 'warning', 'icon' => 'fa-clock'],
  ['title' => 'Sakit / Izin', 'val' => $ringkasan['sakit_izin'], 'color' => 'info', 'icon' => 'fa-file-medical'],
  ['title' => 'Alpha / Absen', 'val' => $ringkasan['alpha'], 'color' => 'danger', 'icon' => 'fa-times-circle'],
  ];
  @endphp

  @foreach($cards as $c)
  <div class="col-xl-3 col-md-6 col-6"> {{-- Tambahkan col-6 agar di HP berjejer 2 (lebih hemat tempat) --}}
    <div class="card border-0 shadow-sm h-100 overflow-hidden">
      {{-- Kurangi padding (p-3 standard, kita coba sesuaikan visual) --}}
      <div class="card-body position-relative p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          {{-- Judul lebih kecil --}}
          <h6 class="text-uppercase fw-bold text-{{ $c['color'] }}" style="font-size: 0.7rem;">
            {{ $c['title'] }}
          </h6>
        </div>

        {{-- Angka lebih kecil (ganti display-6 jadi fs-3) --}}
        <h2 class="fs-2 fw-bold text-dark mb-0">{{ $c['val'] }}</h2>

        {{-- Icon Decoration lebih kecil --}}
        <i class="fas {{ $c['icon'] }} position-absolute text-{{ $c['color'] }}"
          style="bottom: -5px; right: -5px; font-size: 3.5rem; opacity: 0.1; transform: rotate(-15deg);"></i>
      </div>
    </div>
  </div>
  @endforeach
</div>