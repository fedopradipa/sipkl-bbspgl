{{-- Header with Breadcrumb & Quick Info --}}
<div class="d-flex justify-content-between align-items-center mb-4">

  {{-- Left: Back Button & Title --}}
  <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item">
          <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
          <a href="{{ route('admin.peserta_aktif.index') }}">Peserta Aktif</a>
        </li>
        <li class="breadcrumb-item active">Detail</li>
      </ol>
    </nav>
    <h4 class="fw-bold mb-0">Detail Peserta Magang</h4>
  </div>

  {{-- Right: Status Badge & Actions --}}
  <div class="d-flex gap-2 align-items-center">

    {{-- Status Badge --}}
    @php
    $now = \Carbon\Carbon::now();
    $endDate = \Carbon\Carbon::parse($item->tgl_selesai_resmi);
    $isExpired = $now->greaterThan($endDate);
    @endphp

    @if($isExpired)
    <span class="badge bg-warning text-dark badge-status">
      <i class="fas fa-clock me-1"></i> Masa Habis
    </span>
    @else
    <span class="badge bg-success badge-status">
      <i class="fas fa-check-circle me-1"></i> Aktif
    </span>
    @endif

    {{-- Action Buttons --}}
    <div class="btn-group">
      <button
        type="button"
        class="btn btn-primary btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#editMagangModal"
        title="Edit Data">
        <i class="fas fa-edit me-1"></i> Edit
      </button>

      <button
        type="button"
        class="btn btn-outline-primary btn-sm dropdown-toggle dropdown-toggle-split"
        data-bs-toggle="dropdown">
        <span class="visually-hidden">Toggle Dropdown</span>
      </button>

      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <button
            class="dropdown-item text-success"
            data-bs-toggle="modal"
            data-bs-target="#lulusModal{{ $item->id }}">
            <i class="fas fa-graduation-cap me-2"></i> Luluskan
          </button>
        </li>
        <li>
          <button
            class="dropdown-item text-danger"
            data-bs-toggle="modal"
            data-bs-target="#dropoutModal{{ $item->id }}">
            <i class="fas fa-ban me-2"></i> Hentikan
          </button>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('admin.peserta_aktif.index') }}">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

{{-- Quick Stats Row --}}
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
      <div class="card-body text-center py-3">
        <div class="text-primary mb-1">
          <i class="fas fa-calendar-check fa-2x"></i>
        </div>
        <h6 class="mb-0 text-primary fw-bold">
          {{ \Carbon\Carbon::parse($item->tgl_mulai_resmi)->diffInDays(\Carbon\Carbon::parse($item->tgl_selesai_resmi)) }}
        </h6>
        <small class="text-muted">Total Hari</small>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card border-0 shadow-sm bg-success bg-opacity-10">
      <div class="card-body text-center py-3">
        <div class="text-success mb-1">
          <i class="fas fa-check-double fa-2x"></i>
        </div>
        <h6 class="mb-0 text-success fw-bold">
          {{ $item->presensi ? $item->presensi->where('status', 'hadir')->count() : 0 }}
        </h6>
        <small class="text-muted">Hari Hadir</small>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
      <div class="card-body text-center py-3">
        <div class="text-warning mb-1">
          <i class="fas fa-clock fa-2x"></i>
        </div>
        <h6 class="mb-0 text-warning fw-bold">
          {{ abs(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->tgl_selesai_resmi), false)) }}
        </h6>
        <small class="text-muted">Sisa Hari</small>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card border-0 shadow-sm bg-info bg-opacity-10">
      <div class="card-body text-center py-3">
        <div class="text-info mb-1">
          <i class="fas fa-percentage fa-2x"></i>
        </div>
        <h6 class="mb-0 text-info fw-bold">
          {{ round((\Carbon\Carbon::parse($item->tgl_mulai_resmi)->diffInDays(\Carbon\Carbon::now()) / \Carbon\Carbon::parse($item->tgl_mulai_resmi)->diffInDays(\Carbon\Carbon::parse($item->tgl_selesai_resmi))) * 100) }}%
        </h6>
        <small class="text-muted">Progress</small>
      </div>
    </div>
  </div>
</div>