@extends('layouts.app')

@section('content')
<div class="container-fluid">

  {{-- Header Halaman --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-primary fw-bold">Monitoring Presensi</h1>
      <p class="text-muted small mb-0">Pantau kehadiran peserta PKL secara real-time.</p>
    </div>
  </div>

  {{-- 1. Kartu Statistik Ringkasan --}}
  @include('admin.presensi.components.stats-cards', ['ringkasan' => $ringkasan])

  {{-- 2. Filter & Pencarian --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      @include('admin.presensi.components.filter-bar', [
      'tanggal' => $tanggal,
      'posisi' => $posisi_list,
      'selected_posisi' => request('posisi')
      ])
    </div>
  </div>

  {{-- 3. Tabel Data --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
          <i class="fas fa-list-alt me-2"></i>Daftar Kehadiran: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
        </h6>
        <span class="badge bg-light text-secondary border">
          Total Data: {{ $presensis->count() }}
        </span>
      </div>
    </div>
    <div class="card-body p-0">
      @include('admin.presensi.components.attendance-table', ['presensis' => $presensis])
    </div>
  </div>

</div>

{{-- INCLUDE MODALS --}}
@include('admin.presensi.partials.create_modal')
@include('admin.presensi.partials.edit_modal')

@endsection