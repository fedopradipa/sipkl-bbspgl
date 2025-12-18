{{-- Tab Content: Placement Details --}}

{{-- ======================================================================= --}}
{{-- 1. DATA RESMI (PRIMARY VIEW) --}}
{{-- ======================================================================= --}}
<div class="mb-4">
  {{-- Header Section --}}
  <div class="d-flex align-items-center mb-4">
    <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
      <i class="fas fa-check-circle text-success fa-lg"></i>
    </div>
    <div>
      <h5 class="fw-bold text-dark mb-0">Data Resmi Penempatan</h5>
      <small class="text-muted">Data final yang berlaku saat ini.</small>
    </div>
  </div>

  {{-- Grid Layout yang Lebih Rapi --}}
  <div class="card bg-light border-0 shadow-sm rounded-3">
    <div class="card-body p-4">

      <div class="row g-4 mb-4">
        {{-- Kiri: Posisi & Jam --}}
        <div class="col-md-6 border-end-md">
          <div class="mb-4">
            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">POSISI & DIVISI</small>
            <div class="d-flex align-items-center mt-2">
              <div class="bg-white p-2 rounded border me-3">
                <i class="fas fa-briefcase text-primary"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-0 text-dark">{{ $item->lowongan->judul }}</h6>
                <span class="text-muted small">{{ $item->lowongan->divisi ?? 'Divisi Umum' }}</span>
              </div>
            </div>
          </div>

          <div>
            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">JAM KERJA STANDAR</small>
            <div class="d-flex align-items-center mt-2">
              <div class="bg-white p-2 rounded border me-3">
                <i class="far fa-clock text-primary"></i>
              </div>
              <div class="fw-bold text-dark">
                {{ $item->lowongan->jam_masuk_standar ?? '08:00' }} - {{ $item->lowongan->jam_pulang_standar ?? '16:00' }}
                <span class="badge bg-white text-secondary border ms-2 fw-normal">WIB</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Kanan: Periode & Hari --}}
        <div class="col-md-6 ps-md-4">
          <div class="mb-4">
            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">PERIODE & DURASI</small>
            <div class="mt-2">
              <div class="d-flex align-items-center mb-2">
                <i class="far fa-calendar-alt text-primary me-2"></i>
                <span class="fw-bold text-dark">
                  {{ \Carbon\Carbon::parse($item->tgl_mulai_resmi)->format('d M Y') }}
                  <span class="mx-1 text-muted">-</span>
                  {{ \Carbon\Carbon::parse($item->tgl_selesai_resmi)->format('d M Y') }}
                </span>
              </div>
              <div class="d-flex gap-2 flex-wrap">
                {{-- Badge Kalkulasi Hari --}}
                <span class="badge bg-white text-secondary border fw-normal" title="Total hari kalender">
                  <i class="fas fa-calendar-day me-1"></i>
                  {{ \Carbon\Carbon::parse($item->tgl_mulai_resmi)->diffInDays(\Carbon\Carbon::parse($item->tgl_selesai_resmi)) + 1 }} Hari Kalender
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary fw-bold" title="Kewajiban masuk">
                  <i class="fas fa-briefcase me-1"></i>
                  {{ $item->total_hari_efektif }} Hari Kerja
                </span>
              </div>
            </div>
          </div>

          <div>
            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">HARI KERJA WAJIB</small>
            <div class="d-flex flex-wrap gap-1 mt-2">
              @if($item->hari_kerja_resmi)
              @foreach($item->hari_kerja_resmi as $day)
              <span class="badge bg-primary shadow-sm">{{ $day }}</span>
              @endforeach
              @else
              <span class="text-muted small fst-italic">Tidak ada data jadwal</span>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Progress Bar Full Width --}}
      <hr class="border-secondary opacity-10 my-4">

      <div class="row">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">PROGRESS MASA MAGANG</small>
          </div>
          @include('admin.peserta_aktif.components.progress-bar', [
          'start' => $item->tgl_mulai_resmi,
          'end' => $item->tgl_selesai_resmi
          ])
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ======================================================================= --}}
{{-- 2. DATA PENGAJUAN AWAL (SECONDARY VIEW - ACCORDION) --}}
{{-- ======================================================================= --}}

@php
$startChanged = $item->tgl_mulai_usulan != $item->tgl_mulai_resmi;
$endChanged = $item->tgl_selesai_usulan != $item->tgl_selesai_resmi;
$daysChanged = $item->hari_kerja_usulan != $item->hari_kerja_resmi;
$hasChanges = $startChanged || $endChanged || $daysChanged;
@endphp

<div class="accordion accordion-flush border rounded-3 overflow-hidden" id="historyAccordion">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingHistory">
      <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistory">
        <div class="d-flex align-items-center w-100 me-3">
          <div class="bg-secondary bg-opacity-10 rounded p-2 me-3">
            <i class="fas fa-history text-secondary"></i>
          </div>
          <div>
            <h6 class="fw-bold text-dark mb-0">Riwayat Pengajuan Awal</h6>
            <small class="text-muted">Klik untuk melihat usulan asli peserta</small>
          </div>

          {{-- Badge jika ada perubahan --}}
          @if($hasChanges)
          <span class="badge bg-warning text-dark ms-auto me-2">
            <i class="fas fa-info-circle me-1"></i> Terdapat Penyesuaian
          </span>
          @endif
        </div>
      </button>
    </h2>
    <div id="collapseHistory" class="accordion-collapse collapse" data-bs-parent="#historyAccordion">
      <div class="accordion-body bg-light">

        {{-- Alert Perubahan di dalam Accordion --}}
        @if($hasChanges)
        <div class="alert alert-warning d-flex align-items-start small mb-4 border-0 shadow-sm">
          <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
          <div>
            <strong>Catatan Admin:</strong> Data resmi di atas berbeda dengan usulan peserta.
            <ul class="mb-0 mt-1 ps-3">
              @if($startChanged) <li>Tanggal mulai disesuaikan.</li> @endif
              @if($endChanged) <li>Tanggal selesai disesuaikan.</li> @endif
              @if($daysChanged) <li>Jadwal hari kerja disesuaikan.</li> @endif
            </ul>
          </div>
        </div>
        @endif

        <div class="row g-4">
          <div class="col-md-6">
            <small class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 0.7rem;">PERIODE DIUSULKAN</small>
            <div class="bg-white p-3 rounded border">
              <i class="far fa-calendar text-muted me-2"></i>
              {{ \Carbon\Carbon::parse($item->tgl_mulai_usulan)->format('d M Y') }} -
              {{ \Carbon\Carbon::parse($item->tgl_selesai_usulan)->format('d M Y') }}
            </div>
          </div>

          <div class="col-md-6">
            <small class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 0.7rem;">HARI KERJA DIUSULKAN</small>
            <div class="bg-white p-3 rounded border">
              <div class="d-flex flex-wrap gap-1">
                @if(is_array($item->hari_kerja_usulan))
                @foreach($item->hari_kerja_usulan as $day)
                <span class="badge bg-secondary opacity-75">{{ $day }}</span>
                @endforeach
                @else
                <span class="text-muted">{{ $item->hari_kerja_usulan ?? '-' }}</span>
                @endif
              </div>
            </div>
          </div>

          <div class="col-12">
            <small class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 0.7rem;">ALASAN / MOTIVASI JADWAL</small>
            <div class="bg-white p-3 rounded border">
              <p class="mb-0 text-muted fst-italic small">
                <i class="fas fa-quote-left me-2 text-secondary opacity-25"></i>
                {{ $item->alasan_hari_kerja ?? 'Tidak ada keterangan' }}
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>