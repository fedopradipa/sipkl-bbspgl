@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 800px;"> {{-- Membatasi lebar agar enak dilihat di PC juga --}}

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-dark mb-0">Riwayat Kehadiran</h5>
  </div>

  {{-- 1. CARD STATISTIK --}}
  @include('peserta.presensi.components.stats-cards', ['summary' => $summary])

  {{-- 2. FILTER --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 py-2" data-bs-toggle="collapse" href="#filterCollapse" role="button">
      <div class="d-flex justify-content-between align-items-center text-primary" style="cursor: pointer;">
        <span class="small fw-bold"><i class="fas fa-filter me-2"></i>Filter Tanggal</span>
        <i class="fas fa-chevron-down small"></i>
      </div>
    </div>
    <div class="collapse {{ request('mulai') ? 'show' : '' }}" id="filterCollapse">
      <div class="card-body pt-0 pb-3">
        <form action="{{ route('presensi.riwayat') }}" method="GET">
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label small text-muted mb-1">Mulai</label>
              <input type="date" name="mulai" class="form-control form-control-sm" value="{{ $mulai }}">
            </div>
            <div class="col-6">
              <label class="form-label small text-muted mb-1">Sampai</label>
              <input type="date" name="sampai" class="form-control form-control-sm" value="{{ $sampai }}">
            </div>
            <div class="col-12 mt-2">
              <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- 3. TABEL DATA --}}
  @include('peserta.presensi.components.history-table', ['presensi' => $presensi])

</div>
@endsection