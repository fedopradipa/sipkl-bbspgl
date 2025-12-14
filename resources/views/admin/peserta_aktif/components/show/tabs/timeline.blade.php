{{-- Tab Content: Timeline --}}

<div class="timeline">

  {{-- Event 1: Pendaftaran --}}
  <div class="timeline-item">
    <div class="timeline-icon bg-primary text-white">
      <i class="fas fa-file-signature"></i>
    </div>
    <div class="ms-3">
      <div class="d-flex justify-content-between align-items-start mb-1">
        <h6 class="fw-bold mb-0">Pendaftaran Lamaran</h6>
        <small class="text-muted">{{ $item->created_at->format('d M Y, H:i') }}</small>
      </div>
      <p class="text-muted small mb-0">
        Peserta mengajukan lamaran untuk posisi <strong>{{ $item->lowongan->judul }}</strong>
      </p>
    </div>
  </div>

  {{-- Event 2: Verifikasi/Approval --}}
  @if($item->status == 'diterima')
  <div class="timeline-item">
    <div class="timeline-icon bg-success text-white">
      <i class="fas fa-check-circle"></i>
    </div>
    <div class="ms-3">
      <div class="d-flex justify-content-between align-items-start mb-1">
        <h6 class="fw-bold mb-0">Lamaran Diterima</h6>
        <small class="text-muted">{{ $item->updated_at->format('d M Y, H:i') }}</small>
      </div>
      <p class="text-muted small mb-2">
        Admin menyetujui lamaran dan menetapkan jadwal resmi
      </p>
      <div class="bg-light p-2 rounded small">
        <strong>Pembimbing:</strong> {{ $item->pembimbing->nama ?? 'Belum ditentukan' }}
      </div>
    </div>
  </div>
  @endif

  {{-- Event 3: Mulai Magang --}}
  @if(\Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($item->tgl_mulai_resmi)))
  <div class="timeline-item">
    <div class="timeline-icon bg-info text-white">
      <i class="fas fa-play"></i>
    </div>
    <div class="ms-3">
      <div class="d-flex justify-content-between align-items-start mb-1">
        <h6 class="fw-bold mb-0">Memulai Magang</h6>
        <small class="text-muted">{{ \Carbon\Carbon::parse($item->tgl_mulai_resmi)->format('d M Y') }}</small>
      </div>
      <p class="text-muted small mb-0">
        Peserta resmi memulai program magang
      </p>
    </div>
  </div>
  @endif

  {{-- Event 4: Presensi Pertama --}}
  @php
  $firstPresensi = $item->presensi ? $item->presensi->sortBy('tanggal')->first() : null;
  @endphp
  @if($firstPresensi)
  <div class="timeline-item">
    <div class="timeline-icon bg-warning text-white">
      <i class="fas fa-calendar-check"></i>
    </div>
    <div class="ms-3">
      <div class="d-flex justify-content-between align-items-start mb-1">
        <h6 class="fw-bold mb-0">Presensi Pertama</h6>
        <small class="text-muted">{{ \Carbon\Carbon::parse($firstPresensi->tanggal)->format('d M Y') }}</small>
      </div>
      <p class="text-muted small mb-0">
        Check-in: {{ \Carbon\Carbon::parse($firstPresensi->jam_masuk)->format('H:i') }}
      </p>
    </div>
  </div>
  @endif

  {{-- Event 5: Progress Saat Ini --}}
  @if(\Carbon\Carbon::now()->lt(\Carbon\Carbon::parse($item->tgl_selesai_resmi)))
  <div class="timeline-item">
    <div class="timeline-icon bg-primary text-white">
      <i class="fas fa-spinner fa-pulse"></i>
    </div>
    <div class="ms-3">
      <div class="d-flex justify-content-between align-items-start mb-1">
        <h6 class="fw-bold mb-0">Sedang Berjalan</h6>
        <small class="text-muted">Hari ini</small>
      </div>
      <p class="text-muted small mb-2">
        Peserta masih dalam masa aktif magang
      </p>
      <div class="progress" style="height: 8px;">
        @php
        $totalDays = \Carbon\Carbon::parse($item->tgl_mulai_resmi)->diffInDays(\Carbon\Carbon::parse($item->tgl_selesai_resmi));
        $passedDays = \Carbon\Carbon::parse($item->tgl_mulai_resmi)->diffInDays(\Carbon\Carbon::now());
        $percentage = $totalDays > 0 ? round(($passedDays / $totalDays) * 100) : 0;
        @endphp
        <div
          class="progress-bar bg-primary"
          role="progressbar"
          style="width: {{ $percentage }}%">
        </div>
      </div>
      <small class="text-muted d-block mt-1">{{ $percentage }}% selesai</small>
    </div>
  </div>
  @endif

  {{-- Event 6: Akan Berakhir / Sudah Berakhir --}}
  <div class="timeline-item">
    <div class="timeline-icon {{ \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($item->tgl_selesai_resmi)) ? 'bg-danger' : 'bg-secondary' }} text-white">
      <i class="fas fa-flag-checkered"></i>
    </div>
    <div class="ms-3">
      <div class="d-flex justify-content-between align-items-start mb-1">
        <h6 class="fw-bold mb-0">
          @if(\Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($item->tgl_selesai_resmi)))
          Masa Magang Berakhir
          @else
          Target Selesai
          @endif
        </h6>
        <small class="text-muted">{{ \Carbon\Carbon::parse($item->tgl_selesai_resmi)->format('d M Y') }}</small>
      </div>
      <p class="text-muted small mb-0">
        @if(\Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($item->tgl_selesai_resmi)))
        Periode magang telah berakhir. Menunggu proses kelulusan.
        @else
        Perkiraan selesai dalam {{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->tgl_selesai_resmi)) }} hari lagi
        @endif
      </p>
    </div>
  </div>

</div>

{{-- Statistics Summary --}}
<div class="card border-0 bg-light mt-4">
  <div class="card-body">
    <h6 class="fw-bold mb-3">
      <i class="fas fa-chart-line me-2 text-primary"></i>Statistik
    </h6>
    <div class="row g-3">
      <div class="col-6">
        <div class="text-center">
          <div class="fs-3 fw-bold text-primary">
            {{ $item->presensi ? $item->presensi->count() : 0 }}
          </div>
          <small class="text-muted">Total Presensi</small>
        </div>
      </div>
      <div class="col-6">
        <div class="text-center">
          <div class="fs-3 fw-bold text-success">
            {{ $item->presensi ? $item->presensi->where('status', 'hadir')->count() : 0 }}
          </div>
          <small class="text-muted">Hari Hadir</small>
        </div>
      </div>
      <div class="col-6">
        <div class="text-center">
          <div class="fs-3 fw-bold text-warning">
            {{ $item->izin ? $item->izin->count() : 0 }}
          </div>
          <small class="text-muted">Izin/Sakit</small>
        </div>
      </div>
      <div class="col-6">
        <div class="text-center">
          <div class="fs-3 fw-bold text-danger">
            {{ $item->presensi ? $item->presensi->where('status', 'alpha')->count() : 0 }}
          </div>
          <small class="text-muted">Alpha</small>
        </div>
      </div>
    </div>
  </div>
</div>