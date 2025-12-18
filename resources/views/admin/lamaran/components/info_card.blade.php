<div class="card shadow-sm border-0 h-100">
  <div class="card-header bg-white py-3 border-bottom">
    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-briefcase me-2"></i>Informasi Lowongan</h6>
  </div>
  <div class="card-body">

    {{-- Status Badge Besar --}}
    <div class="text-center mb-4 pb-3 border-bottom">
      <span class="text-muted small text-uppercase d-block mb-2">Status Saat Ini</span>
      @include('admin.lamaran.components.status_badge', ['status' => $lamaran->status])
    </div>

    <ul class="list-group list-group-flush">
      <li class="list-group-item px-0">
        <small class="text-muted d-block">Posisi Dilamar</small>
        <div class="fw-bold text-dark">{{ $lamaran->lowongan->judul }}</div>
        <div class="text-secondary small">{{ $lamaran->lowongan->divisi }}</div>
      </li>

      <li class="list-group-item px-0">
        <small class="text-muted d-block">Tanggal Melamar</small>
        <div class="fw-bold text-dark">{{ $lamaran->created_at->format('d F Y, H:i') }}</div>
      </li>

      <li class="list-group-item px-0">
        <small class="text-muted d-block">Durasi Pengajuan</small>
        <div class="fw-bold text-dark">
          <i class="far fa-calendar-alt me-1 text-primary"></i>
          {{ $lamaran->tgl_mulai_usulan->format('d M Y') }}
          <span class="mx-1 text-muted">&rarr;</span>
          {{ $lamaran->tgl_selesai_usulan->format('d M Y') }}
        </div>
        <small class="text-muted">({{ $lamaran->tgl_mulai_usulan->diffInMonths($lamaran->tgl_selesai_usulan) }} Bulan)</small>
      </li>

      <li class="list-group-item px-0">
        <small class="text-muted d-block mb-1">Hari Kerja Diusulkan</small>
        <div class="mb-2">
          @if(is_array($lamaran->hari_kerja_usulan))
          @foreach($lamaran->hari_kerja_usulan as $day)
          <span class="badge bg-light text-dark border me-1 mb-1">{{ $day }}</span>
          @endforeach
          @else
          <span class="text-muted">-</span>
          @endif
        </div>

        {{-- [BARU] Menampilkan Alasan Hari Kerja --}}
        <div class="bg-light p-2 rounded border border-light">
          <small class="text-muted d-block" style="font-size: 0.7rem;">CATATAN / ALASAN JADWAL:</small>
          <div class="text-dark small fst-italic">
            "{{ $lamaran->alasan_hari_kerja ?? '-' }}"
          </div>
        </div>
      </li>
    </ul>
  </div>
</div>