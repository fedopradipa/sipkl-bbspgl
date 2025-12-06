{{-- Card Absensi --}}
<div class="card border-0 shadow-lg text-white mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
  <div class="card-body p-5 text-center position-relative z-1">
    <h6 class="text-uppercase opacity-75 mb-1" style="letter-spacing: 2px;">Absensi Hari Ini</h6>
    <h2 class="fw-bold mb-4">
      {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </h2>

    @if(!$presensi)
    {{-- Belum Check-in --}}
    <div class="py-2">
      <p class="mb-4 fs-5">Anda belum melakukan Check-in hari ini.</p>
      <form action="{{ route('presensi.checkin') }}" method="POST">
        @csrf
        <input type="hidden" name="latitude" id="lat">
        <input type="hidden" name="longitude" id="long">
        <button
          type="submit"
          id="btn-checkin"
          class="btn btn-light text-primary fw-bold btn-lg px-5 py-3 rounded-pill shadow-lg"
          style="transition: transform 0.2s;"
          onmouseover="this.style.transform='scale(1.05)'"
          onmouseout="this.style.transform='scale(1)'"
          disabled>
          <i class="fas fa-spinner fa-spin me-2" id="loading-icon"></i> MENCARI LOKASI...
        </button>
      </form>
    </div>

    @elseif($presensi->jam_masuk && !$presensi->jam_pulang)
    {{-- Sudah Check-in, Belum Check-out --}}
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="bg-white bg-opacity-25 p-3 rounded-3 mb-4">
          <span class="d-block small opacity-75">Waktu Masuk</span>
          <span class="fs-1 fw-bold">{{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}</span>
        </div>
      </div>
    </div>

    @php
    $jamPulangStandar = \Carbon\Carbon::parse($lamaran->lowongan->jam_pulang_standar);
    $batasWaktuPulang = \Carbon\Carbon::today()->setTimeFrom($jamPulangStandar);
    $isWaktunyaPulang = \Carbon\Carbon::now()->gte($batasWaktuPulang);
    @endphp

    <form action="{{ route('presensi.checkout') }}" method="POST">
      @csrf
      <input type="hidden" name="latitude" id="lat_out">
      <input type="hidden" name="longitude" id="long_out">

      @if($isWaktunyaPulang)
      <button
        type="submit"
        id="btn-checkout"
        class="btn btn-danger fw-bold btn-lg px-5 py-3 rounded-pill shadow-lg"
        style="transition: transform 0.2s;"
        onmouseover="this.style.transform='scale(1.05)'"
        onmouseout="this.style.transform='scale(1)'"
        disabled>
        <i class="fas fa-spinner fa-spin me-2" id="loading-out"></i> MENENTUKAN LOKASI...
      </button>
      @else
      <div class="d-inline-block px-4 py-2 rounded-pill bg-dark bg-opacity-25 border border-white border-opacity-25">
        <i class="fas fa-clock me-2"></i>
        Absen pulang dibuka pukul <strong>{{ $jamPulangStandar->format('H:i') }}</strong>
      </div>
      <br>
      <button type="button" class="btn btn-secondary fw-bold btn-lg px-5 mt-3 rounded-pill" disabled style="opacity: 0.7">
        BELUM WAKTUNYA
      </button>
      @endif
    </form>

    @else
    {{-- Sudah Selesai Check-in & Check-out --}}
    <div class="py-4">
      <div class="d-inline-block bg-white text-success p-3 rounded-circle shadow mb-3">
        <i class="fas fa-check fa-3x"></i>
      </div>
      <h4>Anda Sudah Selesai!</h4>
      <p class="opacity-75">Terima kasih atas kontribusi Anda hari ini.</p>
    </div>
    @endif
  </div>
</div>