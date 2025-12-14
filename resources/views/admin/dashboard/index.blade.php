@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- 1. KARTU STATISTIK --}}
    <div class="row g-4 mb-4">
        {{-- Card: Lamaran Pending --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Lamaran Masuk (Pending)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pelamar_pending'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-inbox fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Peserta Aktif --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Peserta Magang Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['peserta_aktif'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Izin Hari Ini --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Izin / Sakit Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['izin_hari_ini'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clinic-medical fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Lowongan Buka --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Lowongan Dibuka</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['lowongan_aktif'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-briefcase fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. KONTEN UTAMA (GRAFIK & TABEL) --}}
    <div class="row">
        {{-- Grafik Pendaftaran --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Penerimaan Peserta ({{ date('Y') }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Peserta Terbaru --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Lamaran Terbaru</h6>
                    <a href="{{ route('admin.lamaran.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama</th>
                                    <th>Posisi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestLamarans as $l)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold small">{{ $l->pengguna->name }}</div>
                                        <div class="text-muted small" style="font-size: 10px;">{{ $l->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="small">{{ $l->lowongan->divisi }}</td>
                                    <td>
                                        @if($l->status == 'pending')
                                        <span class="badge bg-warning text-dark" style="font-size: 10px;">Pending</span>
                                        @elseif($l->status == 'diterima')
                                        <span class="badge bg-success" style="font-size: 10px;">Terima</span>
                                        @else
                                        <span class="badge bg-danger" style="font-size: 10px;">Tolak</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 small text-muted">Belum ada data lamaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('myAreaChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                datasets: [{
                    label: "Peserta Diterima",
                    data: @json($chartSeries), // Data dari Controller
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.3 // Membuat garis melengkung halus
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection