{{--
  Info Lamaran Card Component
  Props: $lamaran (object)
--}}

<div class="card shadow mb-4">
  <div class="card-header py-3 bg-primary text-white">
    <h6 class="m-0 font-weight-bold">Info Lamaran</h6>
  </div>
  <div class="card-body">

    {{-- Posisi --}}
    <div class="mb-3">
      <label class="small text-muted">Posisi Dilamar</label>
      <div class="fw-bold">{{ $lamaran->lowongan->judul }}</div>
      <small class="text-secondary">{{ $lamaran->lowongan->divisi }}</small>
    </div>

    {{-- Tanggal Melamar --}}
    <div class="mb-3">
      <label class="small text-muted">Tanggal Melamar</label>
      <div>{{ $lamaran->created_at->format('d F Y, H:i') }}</div>
    </div>

    {{-- Durasi Usulan --}}
    <div class="mb-3">
      <label class="small text-muted">Durasi Usulan</label>
      <div>
        {{ $lamaran->tgl_mulai_usulan->format('d M Y') }} -
        {{ $lamaran->tgl_selesai_usulan->format('d M Y') }}
      </div>
    </div>

    {{-- Hari Kerja Usulan --}}
    <div class="mb-3">
      <label class="small text-muted">Hari Kerja Usulan</label>
      <div>
        @if(is_array($lamaran->hari_kerja_usulan))
        @foreach($lamaran->hari_kerja_usulan as $day)
        <span class="badge bg-secondary me-1">{{ $day }}</span>
        @endforeach
        @else
        <span class="text-muted">-</span>
        @endif
      </div>
    </div>

    {{-- Status --}}
    <div class="mb-3">
      <label class="small text-muted">Status</label> <br>
      @include('admin.lamaran.components.status_badge', ['status' => $lamaran->status])
    </div>

    {{-- Catatan Admin (Jika ditolak) --}}
    @if($lamaran->status == 'ditolak')
    <div class="alert alert-danger small mt-3 p-2">
      <strong>Catatan Admin:</strong>
      {{ $lamaran->catatan_admin ?? 'Tidak ada catatan.' }}
    </div>
    @endif
  </div>
</div>