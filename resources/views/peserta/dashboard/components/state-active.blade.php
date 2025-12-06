{{-- State: Active (Masa Magang) --}}

@if($isHariKerja)
{{-- Hari Kerja: Tampilkan Absensi --}}
@include('peserta.dashboard.components.absensi-card', [
'presensi' => $presensi,
'lamaran' => $lamaran
])
@else
{{-- Hari Libur --}}
<div class="card border-0 shadow-sm bg-secondary bg-opacity-10 mb-4">
  <div class="card-body text-center py-5">
    <div class="mb-3">
      <div class="d-inline-block bg-white p-3 rounded-circle shadow-sm">
        <i class="fas fa-mug-hot fa-3x text-secondary"></i>
      </div>
    </div>
    <h3 class="fw-bold text-dark">Hari Ini Libur</h3>
    <p class="text-muted mb-0">
      Hari ini tidak termasuk dalam jadwal kerja Anda.
    </p>
    <div class="mt-3">
      <small class="text-muted fw-bold">Jadwal Anda:</small> <br>
      @foreach($lamaran->hari_kerja_resmi as $day)
      <span class="badge bg-secondary me-1">{{ $day }}</span>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- Menu Cepat (Selalu Tampil) --}}
@include('peserta.dashboard.components.quick-menu')