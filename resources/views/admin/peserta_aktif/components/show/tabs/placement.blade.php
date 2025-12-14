{{-- Tab Content: Placement Details --}}

{{-- Section 1: Official Data --}}
<div class="mb-4">
  <div class="d-flex align-items-center mb-3">
    <div class="bg-success bg-opacity-10 rounded p-2 me-3">
      <i class="fas fa-check-circle text-success fa-lg"></i>
    </div>
    <div>
      <h5 class="fw-bold mb-0">Data Resmi Penempatan</h5>
      <small class="text-muted">Data final yang telah disetujui admin</small>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="p-3 bg-light rounded border">
        <div class="info-label">Posisi Magang</div>
        <div class="info-value text-primary">
          <i class="fas fa-briefcase me-2"></i>{{ $item->lowongan->judul }}
        </div>
        <small class="text-muted">{{ $item->lowongan->divisi ?? 'Divisi Umum' }}</small>
      </div>
    </div>

    <div class="col-md-6">
      <div class="p-3 bg-light rounded border">
        <div class="info-label">Periode Magang</div>
        <div class="info-value">
          <i class="far fa-calendar me-2 text-primary"></i>
          {{ \Carbon\Carbon::parse($item->tgl_mulai_resmi)->format('d M Y') }}
        </div>
        <small class="text-muted">
          s/d {{ \Carbon\Carbon::parse($item->tgl_selesai_resmi)->format('d M Y') }}
          ({{ \Carbon\Carbon::parse($item->tgl_mulai_resmi)->diffInDays(\Carbon\Carbon::parse($item->tgl_selesai_resmi)) }} hari)
        </small>
      </div>
    </div>

    <div class="col-md-6">
      <div class="p-3 bg-light rounded border">
        <div class="info-label">Jadwal Kerja</div>
        <div class="d-flex flex-wrap gap-1 mt-1">
          @if($item->hari_kerja_resmi)
          @foreach($item->hari_kerja_resmi as $day)
          <span class="badge bg-primary">{{ $day }}</span>
          @endforeach
          @else
          <span class="text-muted">Tidak ada data</span>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="p-3 bg-light rounded border">
        <div class="info-label">Jam Kerja</div>
        <div class="info-value">
          <i class="far fa-clock me-2 text-primary"></i>
          {{ $item->lowongan->jam_masuk_standar ?? '08:00' }} -
          {{ $item->lowongan->jam_pulang_standar ?? '16:00' }}
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="p-3 bg-primary bg-opacity-10 rounded border border-primary">
        <div class="info-label text-primary">Progress Masa Magang</div>
        @include('admin.peserta_aktif.components.progress-bar', [
        'start' => $item->tgl_mulai_resmi,
        'end' => $item->tgl_selesai_resmi
        ])
      </div>
    </div>
  </div>
</div>

<hr class="my-4">

{{-- Section 2: Initial Application --}}
<div>
  <div class="d-flex align-items-center mb-3">
    <div class="bg-info bg-opacity-10 rounded p-2 me-3">
      <i class="fas fa-file-alt text-info fa-lg"></i>
    </div>
    <div>
      <h5 class="fw-bold mb-0">Data Pengajuan Awal</h5>
      <small class="text-muted">Usulan yang diajukan peserta saat melamar</small>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="info-label">Periode Diusulkan</div>
      <div class="info-value">
        {{ \Carbon\Carbon::parse($item->tgl_mulai_usulan)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($item->tgl_selesai_usulan)->format('d M Y') }}
      </div>
    </div>

    <div class="col-md-6">
      <div class="info-label">Hari Kerja Diusulkan</div>
      <div class="d-flex flex-wrap gap-1 mt-1">
        @if(is_array($item->hari_kerja_usulan))
        @foreach($item->hari_kerja_usulan as $day)
        <span class="badge bg-secondary">{{ $day }}</span>
        @endforeach
        @else
        <span class="text-muted">{{ $item->hari_kerja_usulan ?? '-' }}</span>
        @endif
      </div>
    </div>

    <div class="col-12">
      <div class="info-label">Alasan/Motivasi Jadwal</div>
      <div class="bg-light p-3 rounded border">
        <p class="mb-0 text-muted fst-italic">
          <i class="fas fa-quote-left me-2"></i>
          {{ $item->alasan_hari_kerja ?? 'Tidak ada keterangan' }}
        </p>
      </div>
    </div>
  </div>
</div>

{{-- Comparison Alert --}}
@php
$startChanged = $item->tgl_mulai_usulan != $item->tgl_mulai_resmi;
$endChanged = $item->tgl_selesai_usulan != $item->tgl_selesai_resmi;
$daysChanged = $item->hari_kerja_usulan != $item->hari_kerja_resmi;
@endphp

@if($startChanged || $endChanged || $daysChanged)
<div class="alert alert-info mt-4 mb-0">
  <div class="d-flex align-items-start">
    <i class="fas fa-info-circle me-2 mt-1"></i>
    <div>
      <strong>Perubahan dari Usulan:</strong>
      <ul class="mb-0 mt-2 ps-3">
        @if($startChanged)
        <li>Tanggal mulai diubah admin</li>
        @endif
        @if($endChanged)
        <li>Tanggal selesai diubah admin</li>
        @endif
        @if($daysChanged)
        <li>Jadwal hari kerja disesuaikan</li>
        @endif
      </ul>
    </div>
  </div>
</div>
@endif