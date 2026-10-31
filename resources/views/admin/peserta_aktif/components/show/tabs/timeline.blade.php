{{-- Enhanced Timeline with Better UX --}}

{{-- Quick Summary Cards (At a Glance) --}}
<div class="row g-3 mb-4">
  @php
  $now = \Carbon\Carbon::now();
  $startDate = \Carbon\Carbon::parse($item->tgl_mulai_resmi);
  $endDate = \Carbon\Carbon::parse($item->tgl_selesai_resmi);

  $totalDays = $startDate->diffInDays($endDate);
  $passedDays = $startDate->diffInDays($now);
  $remainingDays = $now->diffInDays($endDate, false);
  $percentage = $totalDays > 0 ? round(($passedDays / $totalDays) * 100) : 0;

  $hasStarted = $now->gte($startDate);
  $hasEnded = $now->gte($endDate);
  @endphp

  {{-- Timeline Events --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
      <h6 class="fw-bold mb-0">
        <i class="fas fa-history me-2 text-primary"></i>Riwayat Aktivitas
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="timeline-container p-4">

        {{-- Event 1: Registration --}}
        <div class="timeline-event">
          <div class="timeline-marker bg-primary">
            <i class="fas fa-file-signature"></i>
          </div>
          <div class="timeline-content">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="fw-bold mb-1">
                  <i class="fas fa-paper-plane me-2 text-primary"></i>Pendaftaran Lamaran
                </h6>
                <p class="text-muted small mb-0">
                  Peserta mengajukan lamaran untuk posisi <span class="badge bg-primary">{{ $item->lowongan->posisi }}</span>
                </p>
              </div>
              <span class="badge bg-light text-dark">
                {{ $item->created_at->format('d M Y, H:i') }}
              </span>
            </div>
          </div>
        </div>

        {{-- Event 2: Approval --}}
        @if($item->status == 'diterima')
        <div class="timeline-event">
          <div class="timeline-marker bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="timeline-content">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="fw-bold mb-1">
                  <i class="fas fa-thumbs-up me-2 text-success"></i>Lamaran Diterima
                </h6>
                <p class="text-muted small mb-2">
                  Admin menyetujui lamaran dan menetapkan pembimbing lapangan
                </p>
                <div class="bg-success bg-opacity-10 border border-success rounded p-2">
                  <small class="fw-bold text-success">
                    <i class="fas fa-user-tie me-1"></i>Pembimbing:
                    {{ $item->pembimbing->nama ?? 'Belum ditentukan' }}
                  </small>
                </div>
              </div>
              <span class="badge bg-success">
                {{ $item->updated_at->format('d M Y') }}
              </span>
            </div>
          </div>
        </div>
        @endif

        {{-- Event 3: Start Internship --}}
        @if($hasStarted)
        <div class="timeline-event">
          <div class="timeline-marker bg-info">
            <i class="fas fa-play"></i>
          </div>
          <div class="timeline-content">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="fw-bold mb-1">
                  <i class="fas fa-door-open me-2 text-info"></i>Memulai PKL
                </h6>
                <p class="text-muted small mb-0">
                  Peserta resmi memulai program PKL
                </p>
              </div>
              <span class="badge bg-info">
                {{ $startDate->format('d M Y') }}
              </span>
            </div>
          </div>
        </div>
        @endif

        {{-- Event 5: Current Progress --}}
        @if($hasStarted && !$hasEnded)
        <div class="timeline-event">
          <div class="timeline-marker bg-primary">
            <i class="fas fa-spinner fa-pulse"></i>
          </div>
          <div class="timeline-content">
            <div class="mb-2">
              <h6 class="fw-bold mb-3">
                <i class="fas fa-sync me-2 text-primary"></i>Sedang Berjalan
              </h6>

              {{-- MENGGUNAKAN COMPONENT SHARED --}}
              <div class="bg-white p-2 rounded border">
                @include('admin.peserta_aktif.components.progress-bar', [
                'start' => $item->tgl_mulai_resmi,
                'end' => $item->tgl_selesai_resmi
                ])
              </div>

            </div>
          </div>
        </div>
        @endif

        {{-- Event 6: End Status --}}
        <div class="timeline-event">
          <div class="timeline-marker {{ $hasEnded ? 'bg-danger' : 'bg-secondary' }}">
            <i class="fas fa-flag-checkered"></i>
          </div>
          <div class="timeline-content">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h6 class="fw-bold mb-1">
                  <i class="fas {{ $hasEnded ? 'fa-stop-circle' : 'fa-bullseye' }} me-2 {{ $hasEnded ? 'text-danger' : 'text-secondary' }}"></i>
                  {{ $hasEnded ? 'Masa PKL Berakhir' : 'Target Selesai' }}
                </h6>
                <p class="text-muted small mb-0">
                  @if($hasEnded)
                  Periode PKL telah berakhir.
                  @if($item->status == 'diterima')
                  <span class="badge bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle me-1"></i>Menunggu Proses Kelulusan
                  </span>
                  @endif
                  @else
                  @endif
                </p>
              </div>
              <span class="badge {{ $hasEnded ? 'bg-danger' : 'bg-secondary' }}">
                {{ $endDate->format('d M Y') }}
              </span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- Attendance Statistics --}}
  @php
  // 1. Ambil semua data presensi agar tidak query berulang
  $allPresensi = $item->presensi ?? collect([]);

  // 2. Hitung masing-masing status dari SATU SUMBER (tabel presensis)
  // Menggunakan strtolower untuk memastikan huruf besar/kecil tidak masalah
  $totalHadir = $allPresensi->filter(fn($p) => strtolower($p->status) === 'hadir')->count();
  $totalTerlambat = $allPresensi->filter(fn($p) => strtolower($p->status) === 'terlambat')->count();

  // Jika Izin/Sakit disimpan di tabel presensi:
  $totalSakit = $allPresensi->filter(fn($p) => strtolower($p->status) === 'sakit')->count();
  $totalIzin = $allPresensi->filter(fn($p) => strtolower($p->status) === 'izin')->count();

  // Gabungan Sakit & Izin
  $totalSakitIzin = $totalSakit + $totalIzin;

  $totalAlpha = $allPresensi->filter(fn($p) => strtolower($p->status) === 'alpha')->count();

  // Total data yang masuk
  $totalLogs = $allPresensi->count();

  // Hitung Persentase Kehadiran (Hadir + Terlambat dianggap masuk)
  $persentaseKehadiran = 0;
  if ($totalLogs > 0) {
  $persentaseKehadiran = round((($totalHadir + $totalTerlambat) / $totalLogs) * 100);
  }
  @endphp

  <div class="row g-3 row-cols-2 row-cols-md-5 mb-4">

    {{-- 1. Total Attendance --}}
    <div class="col">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center p-3">
          <div class="mb-2">
            <i class="fas fa-calendar-alt fa-2x text-secondary"></i>
          </div>
          <h3 class="fw-bold mb-0">{{ $totalLogs }}</h3>
          <small class="text-muted" style="font-size: 0.75rem;">Total Hari</small>
        </div>
      </div>
    </div>

    {{-- 2. Present Days (Hadir Tepat Waktu) --}}
    <div class="col">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center p-3">
          <div class="mb-2">
            <i class="fas fa-check-circle fa-2x text-success"></i>
          </div>
          <h3 class="fw-bold mb-0 text-success">{{ $totalHadir }}</h3>
          <small class="text-muted" style="font-size: 0.75rem;">Tepat Waktu</small>

          {{-- Progress Bar Kehadiran --}}
          <div class="progress mt-2 bg-light" style="height: 4px;">
            <div class="progress-bar bg-success" style="width: {{ $persentaseKehadiran }}%"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- 3. Late (Terlambat) - BARU --}}
    <div class="col">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center p-3">
          <div class="mb-2">
            <i class="fas fa-running fa-2x text-warning"></i>
          </div>
          <h3 class="fw-bold mb-0 text-warning">{{ $totalTerlambat }}</h3>
          <small class="text-muted" style="font-size: 0.75rem;">Terlambat</small>
        </div>
      </div>
    </div>

    {{-- 4. Leave/Sick --}}
    <div class="col">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center p-3">
          <div class="mb-2">
            <i class="fas fa-file-medical fa-2x text-info"></i>
          </div>
          <h3 class="fw-bold mb-0 text-info">
            {{ $totalSakitIzin }}
          </h3>
          <small class="text-muted" style="font-size: 0.75rem;">Izin/Sakit</small>
        </div>
      </div>
    </div>

    {{-- 5. Absent --}}
    <div class="col">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center p-3">
          <div class="mb-2">
            <i class="fas fa-times-circle fa-2x text-danger"></i>
          </div>
          <h3 class="fw-bold mb-0 text-danger">
            {{ $totalAlpha }}
          </h3>
          <small class="text-muted" style="font-size: 0.75rem;">Alpha</small>
        </div>
      </div>
    </div>

  </div>
</div>

<style>
  .timeline-container {
    position: relative;
  }

  .timeline-event {
    position: relative;
    padding-left: 50px;
    padding-bottom: 30px;
  }

  .timeline-event:last-child {
    padding-bottom: 0;
  }

  .timeline-event:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 40px;
    bottom: -10px;
    width: 2px;
    background: linear-gradient(to bottom, #dee2e6 0%, #dee2e6 100%);
  }

  .timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    box-shadow: 0 0 0 4px #fff, 0 0 0 5px #dee2e6;
    z-index: 1;
  }

  .timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #dee2e6;
    transition: all 0.3s ease;
  }

  .timeline-content:hover {
    background: #e9ecef;
    border-left-color: #0d6efd;
    transform: translateX(5px);
  }

  .progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
  }
</style>