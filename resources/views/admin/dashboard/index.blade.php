@extends('layouts.app')

@push('styles')
<style>
    .stat-card {
        min-height: 110px;
    }

    .badge-status {
        font-size: 10px;
    }

    .muted-10 {
        font-size: 10px;
    }

    .chart-wrap {
        height: 320px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">

    @php
    // fallback aman kalau key stats tidak ada
    $stats = $stats ?? [];
    $pelamarPending = $stats['pelamar_pending'] ?? 0;
    $pesertaAktif = $stats['peserta_aktif'] ?? 0;
    $izinHariIni = $stats['izin_hari_ini'] ?? 0;
    $lowonganAktif = $stats['lowongan_aktif'] ?? 0;

    // mapping badge status biar gak if-elseif panjang
    $statusMap = [
    'pending' => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
    'diterima' => ['label' => 'Diterima', 'class' => 'bg-success'],
    'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
    'selesai' => ['label' => 'Selesai', 'class' => 'bg-info text-dark'],
    'berhenti' => ['label' => 'Berhenti', 'class' => 'bg-secondary'],
    ];

    $year = now()->year;
    @endphp

    {{-- 1) KARTU STATISTIK --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-warning fw-bold small mb-1">Lamaran Masuk (Pending)</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $pelamarPending }}</div>
                        </div>
                        <i class="fas fa-inbox fa-2x text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-success fw-bold small mb-1">Peserta PKL Aktif</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $pesertaAktif }}</div>
                        </div>
                        <i class="fas fa-users fa-2x text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-info fw-bold small mb-1">Izin / Sakit Hari Ini</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $izinHariIni }}</div>
                        </div>
                        <i class="fas fa-clinic-medical fa-2x text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-primary fw-bold small mb-1">Lowongan Dibuka</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $lowonganAktif }}</div>
                        </div>
                        <i class="fas fa-briefcase fa-2x text-secondary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2) KONTEN UTAMA --}}
    <div class="row">

        {{-- Grafik --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Tren Penerimaan Peserta ({{ $year }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-wrap">
                        <canvas id="myAreaChart"></canvas>
                    </div>

                    @php
                    $series = $chartSeries ?? [];
                    @endphp

                    @if(empty($series))
                    <div class="text-muted small mt-2">
                        Belum ada data untuk grafik tahun ini.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabel Lamaran Terbaru --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Lamaran Terbaru</h6>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama</th>
                                    <th>Posisi</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($latestLamarans ?? []) as $l)
                                @php
                                $st = $l->status ?? '-';
                                $badge = $statusMap[$st] ?? ['label' => $st, 'class' => 'bg-light text-dark border'];
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold small">{{ $l->pengguna->name ?? '-' }}</div>
                                        <div class="text-muted muted-10">{{ optional($l->created_at)->diffForHumans() }}</div>
                                    </td>
                                    <td class="small">{{ $l->lowongan->posisi ?? '-' }}</td>
                                    <td class="text-end pe-4">
                                        <span class="badge {{ $badge['class'] }} badge-status">{{ $badge['label'] }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 small text-muted">
                                        Belum ada data lamaran.
                                    </td>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const el = document.getElementById('myAreaChart');
        if (!el) return;

        // Pastikan selalu 12 bulan (kalau controller belum menjamin)
        const raw = @json($chartSeries ?? []);
        const data12 = Array.from({
            length: 12
        }, (_, i) => Number(raw[i] ?? 0));

        new Chart(el.getContext('2d'), {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                datasets: [{
                    label: "Peserta Diterima",
                    data: data12,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
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
@endpush