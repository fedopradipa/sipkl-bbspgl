@extends('layouts.app')

@push('styles')
@include('admin.reports.attendance.partials.styles')
@endpush

@section('content')
<div class="container-fluid py-4">

  {{-- 1. BAGIAN FILTER --}}
  @include('admin.reports.attendance.partials.filter')

  {{-- 2. BAGIAN TABEL MATRIX --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold text-dark">
        LAPORAN PRESENSI: <span class="text-primary text-uppercase">{{ $bulanNama }} {{ $tahun }}</span>
      </h5>
      <small class="text-muted d-none d-md-block">
        <i class="fas fa-arrows-alt-h me-1"></i>Scroll untuk melihat data tanggal
      </small>
    </div>

    {{-- KUNCI PERBAIKAN ZOOM: --}}
    {{-- Gunakan 'display: grid' pada parent container. --}}
    {{-- Ini memaksa anak elemen (wrapper tabel) untuk patuh pada lebar parent --}}
    <div class="card-body p-0" style="display: grid; min-width: 0;">

      {{-- Sertakan Partial Tabel --}}
      @include('admin.reports.attendance.partials.table')

    </div>


    <div class="card-footer bg-white">
      <div class="legend-items">
        <strong>Legenda:</strong>
        <span class="legend-item"><span class="legend-color bg-alpha"></span> Alpha (A)</span>
        <span class="legend-item"><span class="legend-color bg-libur"></span> Diluar Jadwal Kerja</span>
        <span class="legend-item"><span class="legend-color bg-sakit"></span> Sakit/Izin</span>
        <span class="legend-item"><span class="legend-color bg-hadir"></span> Hadir</span>
        {{-- Penambahan Legenda Abu-abu --}}
        <span class="legend-item"><span class="legend-color bg-belum"></span> Belum Absen</span>
      </div>
    </div>
  </div>
</div>
</div>
</div>

{{-- SCRIPT: Force Repaint jika browser 'lupa' memunculkan scrollbar --}}
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
      const wrapper = document.querySelector('.table-scroll-wrapper');
      if (wrapper) {
        wrapper.scrollLeft = 0; // Reset posisi
        // Trigger reflow
        wrapper.style.display = 'none';
        wrapper.offsetHeight;
        wrapper.style.display = 'block';
      }
    }, 200);
  });
</script>
@endpush

@endsection