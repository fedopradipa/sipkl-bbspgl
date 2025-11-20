@extends('layouts.admin')

@push('style')
<style>
    /* CSS Kustom untuk Kartu Statistik */
    .stat-card-icon {
        font-size: 3rem;
        opacity: 0.3;
        margin-right: 1.5rem;
    }
</style>
@endpush

@section('content')

{{-- 
  Ringkasan Status Inventaris (Memakai 6 Card, 3 di antaranya adalah Inbox Tugas)
--}}
<div class="row g-4 mb-4">
    
    {{-- Card 1: Total Unit (Read-only) --}}
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <i class="fas fa-box-open stat-card-icon text-dark"></i>
                <div>
                    <div class="fw-bold text-muted">TOTAL UNIT ALAT</div>
                    <div class="fs-4">#</div> 
                    <small class="text-muted">(Termasuk yang Dihapuskan)</small>
                </div>
            </div>
            <a href="#" class="card-footer text-decoration-none text-dark">
                Lihat Detail <i class="fas fa-arrow-right fa-xs ms-1"></i>
            </a>
        </div>
    </div>
    
</div>

{{-- Tambahkan Tabel Aktivitas Terbaru di sini --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                Selamat Datang di Sistem Manajemen Inventaris
            </div>
            <div class="card-body">
                <p>Gunakan menu navigasi di sebelah kiri untuk mengelola data inventaris.</p>
            </div>
        </div>
    </div>
</div>
@endsection