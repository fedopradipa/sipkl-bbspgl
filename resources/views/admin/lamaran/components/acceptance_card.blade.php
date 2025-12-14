{{--
  Acceptance Card Component (Data Penerimaan Resmi)
  Props: $lamaran (object)
--}}

@if($lamaran->status == 'diterima')
<div class="card shadow border-success border-3 mt-4">
  <div class="card-header py-3 fw-bold bg-success text-white">
    Data Penerimaan Resmi
  </div>
  <div class="card-body">

    {{-- Pembimbing --}}
    <h5 class="fw-bold text-dark">Pembimbing Lapangan</h5>
    <p class="fs-5 mb-1">{{ $lamaran->pembimbing->nama ?? '—' }}</p>
    <p class="text-muted small">{{ $lamaran->pembimbing->jabatan ?? 'Belum Ditetapkan' }}</p>

    {{-- Jadwal Resmi --}}
    <h5 class="fw-bold mt-4 text-dark">Jadwal Resmi</h5>
    <p class="mb-1">
      <strong>Mulai Efektif:</strong>
      <span class="text-success fw-bold">
        {{ $lamaran->tgl_mulai_resmi->format('d M Y') }}
      </span>
    </p>
    <p>
      <strong>Berakhir:</strong>
      <span class="text-danger fw-bold">
        {{ $lamaran->tgl_selesai_resmi->format('d M Y') }}
      </span>
    </p>

    {{-- Hari Kerja Resmi --}}
    <h5 class="fw-bold mt-4 text-dark">Hari Kerja Resmi</h5>
    <div class="d-flex flex-wrap gap-2">
      @foreach($lamaran->hari_kerja_resmi as $day)
      <span class="badge bg-primary fs-6">{{ $day }}</span>
      @endforeach
    </div>
  </div>


</div>
@endif