@extends('layouts.app')

@push('styles')
<style>
  .sipkl-dash { --d-ink: #14243d; --d-muted: #667892; --d-border: #e8eef6; --d-blue: #2563eb; color: var(--d-ink); }
  .sipkl-dash .hero { border-radius: 20px; background: linear-gradient(115deg, #0f2345 0%, #153e73 68%, #2563eb 135%); padding: 27px 30px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 15px 32px rgba(18, 45, 86, .14); }
  .sipkl-dash .hero:after { content:""; position:absolute; border:1px solid rgba(255,255,255,.10); width:290px; height:290px; border-radius:50%; right:-60px; top:-155px; box-shadow:0 0 0 55px rgba(255,255,255,.035),0 0 0 110px rgba(255,255,255,.02); pointer-events:none; }
  .sipkl-dash .hero h2 { font-weight: 750; font-size: 1.55rem; margin: 0 0 4px; letter-spacing: -.035em; }
  .sipkl-dash .hero p { color: #c6d9f7; margin: 0; font-size: .9rem; }
  .sipkl-dash .hero-date { display: inline-flex; align-items: center; gap:8px; border: 1px solid rgba(255,255,255,.25); background: rgba(255,255,255,.12); border-radius: 11px; padding: 9px 13px; font-size: .83rem; white-space: nowrap; }
  .sipkl-dash .surface { background:#fff; border:1px solid var(--d-border); border-radius:16px; box-shadow:0 6px 20px rgba(15,35,69,.035); }
  .sipkl-dash .filters { padding:15px 18px; margin-top:16px; margin-bottom:19px; }
  .sipkl-dash .filters .form-select, .sipkl-dash .filters .form-control { border-color:#dce6f3; border-radius:9px; font-size:.87rem; min-height:39px; }
  .sipkl-dash .filters label { font-size:.74rem; font-weight:700; color:var(--d-muted); margin-bottom:5px; }
  .sipkl-dash .btn-filter { min-height:39px; border-radius:9px; font-size:.84rem; font-weight:700; }
  .sipkl-dash .section-kicker { font-size:.70rem; text-transform:uppercase; font-weight:800; color:#64748b; letter-spacing:.11em; }
  .sipkl-dash .section-title { font-size:1.08rem; font-weight:780; margin:0; }
  .sipkl-dash .section-help { font-size:.77rem; color:var(--d-muted); margin-top:3px; }
  .sipkl-dash .stat { padding:18px 19px 16px; height:100%; transition: transform .18s, box-shadow .18s; display:block; color:inherit; text-decoration:none; }
  .sipkl-dash a.stat:hover { color:inherit; transform:translateY(-2px); box-shadow:0 13px 25px rgba(18,45,86,.11); }
  .sipkl-dash .stat .stat-title { font-size:.78rem; font-weight:650; color:var(--d-muted); }
  .sipkl-dash .stat .stat-number { font-weight:800; font-size:1.9rem; letter-spacing:-.05em; line-height:1.2; font-variant-numeric:tabular-nums; }
  .sipkl-dash .stat .stat-sub { font-size:.72rem; color:#8793a4; margin-top:8px; }
  .sipkl-dash .stat .stat-icon { width:41px; height:41px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.02rem; }
  .sipkl-dash .stat .stat-icon.blue { background:#e7efff; color:#2563eb; }
  .sipkl-dash .stat .stat-icon.green { background:#e5f9ee; color:#159968; }
  .sipkl-dash .stat .stat-icon.amber { background:#fff3da; color:#c58613; }
  .sipkl-dash .stat .stat-icon.purple { background:#efe9ff; color:#7546d7; }
  .sipkl-dash .stat .stat-icon.cyan { background:#def8ff; color:#0e91b7; }
  .sipkl-dash .stat .stat-icon.rose { background:#ffe8ed; color:#d94a75; }
  .sipkl-dash .panel { overflow:hidden; height:100%; }
  .sipkl-dash .panel-heading { padding:19px 21px 12px; display:flex; flex-wrap:wrap; gap:8px; justify-content:space-between; align-items:flex-start; }
  .sipkl-dash .panel-title { font-size:.97rem; font-weight:760; margin:0; }
  .sipkl-dash .panel-help { color:var(--d-muted); font-size:.76rem; margin:5px 0 0; }
  .sipkl-dash .tag { background:#edf4ff; color:#275bb4; border-radius:7px; padding:5px 9px; font-size:.72rem; font-weight:750; }
  .sipkl-dash .chart-space { height:270px; position:relative; margin: 8px 19px 18px; }
  .sipkl-dash .chart-space.compact { height:235px; }
  .sipkl-dash .chart-empty { position:absolute; inset:0; display:flex; flex-direction:column; gap:10px; align-items:center; justify-content:center; text-align:center; padding:15px; color:#8a9bb1; font-size:.82rem; background:linear-gradient(180deg, #fff, #f9fbff); border:1px dashed #dfe8f5; border-radius:11px; z-index:2; }
  .sipkl-dash .chart-empty i { font-size:1.6rem; color:#acbcd4; }
  .sipkl-dash .legend-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px 15px; padding:0 22px 20px; }
  .sipkl-dash .legend-item { display:flex; justify-content:space-between; align-items:center; gap:9px; font-size:.76rem; color:#65748a; }
  .sipkl-dash .legend-item b { font-weight:750; color:var(--d-ink); }
  .sipkl-dash .legend-dot { display:inline-block; width:8px; height:8px; border-radius:3px; margin-right:6px; }
  .sipkl-dash .action-list { padding: 0 19px 19px; }
  .sipkl-dash .action-row { display:flex; align-items:center; text-decoration:none; color:var(--d-ink); gap:13px; padding:14px 12px; border:1px solid #ebf0f7; background:#fbfcff; border-radius:11px; margin-top:9px; transition:background .18s; }
  .sipkl-dash .action-row:hover { background:#f1f6ff; color:#184b99; }
  .sipkl-dash .action-row .action-count { margin-left:auto; font-size:1.1rem; font-weight:790; }
  .sipkl-dash .action-row .action-desc { color:var(--d-muted); font-size:.74rem; }
  .sipkl-dash .action-row .action-icon { width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:9px;background:#e8f0ff;color:#3672cc;flex-shrink:0; }
  .sipkl-dash .table thead th { color:#70839c; font-size:.70rem; font-weight:800; text-transform:uppercase; letter-spacing:.045em; border-bottom-color:#e9eef5; padding:12px 17px; white-space:nowrap; }
  .sipkl-dash .table tbody td { padding:13px 17px; font-size:.82rem; border-bottom-color:#edf2f7; vertical-align:middle; }
  .sipkl-dash .table tbody tr:last-child td { border-bottom:0; }
  .sipkl-dash .name { font-weight:750; }
  .sipkl-dash .small-muted { color:var(--d-muted); font-size:.72rem; }
  .sipkl-dash .status-pill { border-radius:7px; padding:5px 8px; font-size:.68rem; font-weight:800; white-space:nowrap; }
  .sipkl-dash .status-pending { color:#97610d; background:#fff3d9; }
  .sipkl-dash .status-diterima { color:#107450; background:#e0f8eb; }
  .sipkl-dash .status-ditolak, .sipkl-dash .status-berhenti { color:#b64456; background:#ffe8ed; }
  .sipkl-dash .status-selesai { color:#2058b3; background:#e7efff; }
  .sipkl-dash .note { font-size:.76rem; color:#6c7c94; line-height:1.55; }
  .sipkl-dash .daily-box { padding:19px 21px; margin:0 0 16px; }
  .sipkl-dash .daily-list { display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); gap:9px; margin-top:16px; }
  .sipkl-dash .daily-item { border:1px solid #e8eef6; background:#fbfdff; border-radius:11px; padding:12px; }
  .sipkl-dash .daily-item .daily-number { font-size:1.22rem; font-weight:800; letter-spacing:-.03em; }
  .sipkl-dash .daily-item .daily-label { font-size:.73rem; color:#667892; }
  .sipkl-dash .daily-progress { display:flex; gap:2px; height:9px; border-radius:99px; overflow:hidden; background:#e7ebf1; margin-top:14px; }
  .sipkl-dash .daily-progress span { height:100%; display:block; }
  @media(max-width:1050px) { .sipkl-dash .daily-list { grid-template-columns:repeat(3,minmax(0,1fr)); } }
  @media(max-width:540px) { .sipkl-dash .daily-list { grid-template-columns:repeat(2,minmax(0,1fr)); } }
  @media(max-width:767px) { .sipkl-dash .hero { padding:21px; } .sipkl-dash .hero h2 { font-size:1.3rem; } .sipkl-dash .stat { padding:14px; } .sipkl-dash .stat .stat-number { font-size:1.55rem; } .sipkl-dash .chart-space { height:230px; } }
</style>
@endpush

@section('content')
<div class="sipkl-dash container-fluid px-3 px-lg-4 pb-5">
    @php
        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $statusLabels = ['pending'=>'Menunggu','diterima'=>'Diterima','ditolak'=>'Ditolak','selesai'=>'Selesai','berhenti'=>'Berhenti'];
        $totalPresensi = array_sum($attendanceSeries);
        $attendanceChartValues = array_values($attendanceSeries);
        $selectedPosisi = $filters['lowongan_id'];
    @endphp

    <section class="hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="position-relative" style="z-index:1">
            <div class="mb-2" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.14em;color:#afcafa;font-weight:750">SIPKL BBSPGL / Admin Workspace</div>
            <h2>Monitoring & Analitik PKL</h2>
            <p>Pantau peserta, presensi, lamaran, dan aktivitas administrasi dari satu halaman.</p>
        </div>
        <div class="hero-date position-relative" style="z-index:1"><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $todayLabel }}</div>
    </section>

    <section class="surface filters" aria-label="Filter dashboard">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-end">
            <div class="col-lg-3 col-sm-6">
                <label for="filter-bulan">Bulan analitik</label>
                <select id="filter-bulan" name="bulan" class="form-select">
                    @foreach($months as $number => $name)
                    <option value="{{ $number }}" @selected($filters['bulan'] === $number)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-sm-6">
                <label for="filter-tahun">Tahun analitik</label>
                <input id="filter-tahun" name="tahun" type="number" min="2020" max="2100" class="form-control" value="{{ $filters['tahun'] }}" required>
            </div>
            <div class="col-lg-3 col-sm-6">
                <label for="filter-posisi">Posisi PKL</label>
                <select id="filter-posisi" name="lowongan_id" class="form-select">
                    <option value="">Semua posisi</option>
                    @foreach($positionOptions as $position)
                    <option value="{{ $position->id }}" @selected($selectedPosisi === (int) $position->id)>{{ $position->posisi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 col-sm-6 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary btn-filter px-3"><i class="fas fa-filter me-1" aria-hidden="true"></i> Terapkan</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-filter px-3">Reset</a>
                <a href="{{ route('admin.reports.attendance.pdf', array_filter(['bulan'=>$filters['bulan'],'tahun'=>$filters['tahun'],'lowongan_id'=>$selectedPosisi], fn($v)=>$v!==null)) }}" class="btn btn-outline-primary btn-filter px-3" title="Ekspor rekap presensi yang sudah tersedia"><i class="far fa-file-pdf me-1" aria-hidden="true"></i> Rekap PDF</a>
            </div>
        </form>
        <p class="note mt-2 mb-0"><i class="fas fa-info-circle me-1" aria-hidden="true"></i> Bulan/tahun berlaku untuk grafik dan rekap bulanan. Kartu statistik dan distribusi posisi menunjukkan kondisi saat ini. Filter posisi berlaku untuk keduanya.</p>
    </section>

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
        <div><div class="section-kicker mb-1">Ringkasan operasional</div><h3 class="section-title">Indikator hari ini</h3></div>
        <span class="small-muted">Berdasarkan data sistem • {{ $todayLabel }}</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <a class="surface stat" href="{{ route('admin.peserta_aktif.index') }}">
                <div class="d-flex justify-content-between align-items-start"><span class="stat-title">Peserta PKL Aktif</span><span class="stat-icon blue"><i class="fas fa-user-graduate" aria-hidden="true"></i></span></div>
                <div class="stat-number">{{ number_format($stats['peserta_aktif'], 0, ',', '.') }}</div>
                <div class="stat-sub">Status diterima dan tanggal PKL sedang berlangsung <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a class="surface stat" href="{{ route('admin.lamaran.index', ['status'=>'pending'] + ($selectedPosisi ? ['lowongan_id'=>$selectedPosisi] : [])) }}">
                <div class="d-flex justify-content-between align-items-start"><span class="stat-title">Lamaran Menunggu</span><span class="stat-icon amber"><i class="fas fa-inbox" aria-hidden="true"></i></span></div>
                <div class="stat-number">{{ number_format($stats['pelamar_pending'], 0, ',', '.') }}</div>
                <div class="stat-sub">Lamaran yang belum diverifikasi admin <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a class="surface stat" href="{{ route('admin.presensi.index', ['tanggal'=>now()->toDateString()] + ($selectedPosisi ? ['lowongan_id'=>$selectedPosisi] : [])) }}">
                <div class="d-flex justify-content-between align-items-start"><span class="stat-title">Kehadiran Hari Ini</span><span class="stat-icon green"><i class="fas fa-calendar-check" aria-hidden="true"></i></span></div>
                <div class="stat-number">{{ $stats['kehadiran_persen'] === null ? '—' : number_format($stats['kehadiran_persen'], 1, ',', '.') . '%' }}</div>
                <div class="stat-sub">{{ $stats['hadir_hari_ini'] }} hadir/terlambat dari {{ $stats['wajib_hadir'] }} peserta terjadwal <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a class="surface stat" href="{{ route('lowongan.index') }}">
                <div class="d-flex justify-content-between align-items-start"><span class="stat-title">Lowongan Tersedia</span><span class="stat-icon purple"><i class="fas fa-briefcase" aria-hidden="true"></i></span></div>
                <div class="stat-number">{{ number_format($stats['lowongan_aktif'], 0, ',', '.') }}</div>
                <div class="stat-sub">Dibuka dan masih dalam periode pendaftaran <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a class="surface stat" href="{{ route('admin.izin.index', ['status'=>'pending']) }}">
                <div class="d-flex justify-content-between align-items-start"><span class="stat-title">Pengajuan Izin Pending</span><span class="stat-icon rose"><i class="fas fa-clipboard-list" aria-hidden="true"></i></span></div>
                <div class="stat-number">{{ number_format($stats['izin_pending'], 0, ',', '.') }}</div>
                <div class="stat-sub">Pengajuan menunggu keputusan admin <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a class="surface stat" href="{{ route('admin.presensi.index', ['tanggal'=>now()->toDateString()] + ($selectedPosisi ? ['lowongan_id'=>$selectedPosisi] : [])) }}">
                <div class="d-flex justify-content-between align-items-start"><span class="stat-title">Izin / Sakit Hari Ini</span><span class="stat-icon cyan"><i class="fas fa-notes-medical" aria-hidden="true"></i></span></div>
                <div class="stat-number">{{ number_format($stats['izin_hari_ini'], 0, ',', '.') }}</div>
                <div class="stat-sub">Peserta dengan izin/sakit yang disetujui atau tercatat hari ini <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></div>
            </a>
        </div>
    </div>

    <section class="surface daily-box" aria-label="Status presensi hari ini">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><h3 class="panel-title">Status Presensi Hari Ini</h3><p class="panel-help">Berdasarkan peserta aktif yang memiliki jadwal kerja hari ini.</p></div>
            <span class="tag">{{ number_format($stats['wajib_hadir'],0,',','.') }} peserta terjadwal</span>
        </div>
        @php
            $dailyItems = [
                ['hadir','Hadir','#23a57d'],
                ['terlambat','Terlambat','#f59e0b'],
                ['sakit','Sakit','#3b82f6'],
                ['izin','Izin','#8b5cf6'],
                ['alpha','Alfa','#f06474'],
                ['belum','Belum Absen','#94a3b8'],
            ];
        @endphp
        <div class="daily-list">
            @foreach($dailyItems as $item)
            <div class="daily-item">
                <div class="daily-number" style="color:{{ $item[2] }}">{{ $todayAttendance[$item[0]] }}</div>
                <div class="daily-label">{{ $item[1] }}</div>
            </div>
            @endforeach
        </div>
        @if($stats['wajib_hadir'] > 0)
        <div class="daily-progress" role="img" aria-label="Perbandingan status presensi hari ini">
            @foreach($dailyItems as $item)
                @if($todayAttendance[$item[0]] > 0)
                <span style="width:{{ ($todayAttendance[$item[0]] / $stats['wajib_hadir']) * 100 }}%;background:{{ $item[2] }}" title="{{ $item[1] }}: {{ $todayAttendance[$item[0]] }}"></span>
                @endif
            @endforeach
        </div>
        @endif
        <p class="note mt-2 mb-0">Belum Absen = belum ada catatan dan jam tutup belum lewat. Alfa = tidak ada catatan atau izin sah pada/ setelah jam tutup.</p>
    </section>

    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <section class="surface panel">
                <div class="panel-heading"><div><h3 class="panel-title">Tren Peserta Mulai PKL</h3><p class="panel-help">Dihitung berdasarkan tanggal mulai resmi, termasuk peserta yang telah menyelesaikan PKL.</p></div><span class="tag">{{ $filters['tahun'] }}</span></div>
                <div class="chart-space">
                    <canvas id="chart-tren" role="img" aria-label="Grafik jumlah peserta mulai PKL per bulan"></canvas>
                    @if(array_sum($chartSeries) === 0)<div class="chart-empty"><i class="far fa-chart-bar" aria-hidden="true"></i><span>Belum ada peserta mulai PKL pada tahun dan posisi ini.</span></div>@endif
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="surface panel">
                <div class="panel-heading"><div><h3 class="panel-title">Distribusi Presensi Bulanan</h3><p class="panel-help">Akumulasi kesempatan presensi sampai hari ini untuk peserta yang masih aktif, periode {{ $periodLabel }}.</p></div><span class="tag">{{ number_format($totalPresensi,0,',','.') }} kesempatan</span></div>
                <div class="chart-space compact">
                    <canvas id="chart-presensi" role="img" aria-label="Diagram distribusi status presensi"></canvas>
                    @if($totalPresensi === 0)<div class="chart-empty"><i class="far fa-calendar-times" aria-hidden="true"></i><span>Belum ada kesempatan presensi pada periode ini.</span></div>@endif
                </div>
                <div class="legend-grid">
                    @foreach([['hadir','Hadir','#23a57d'],['terlambat','Terlambat','#f59e0b'],['sakit','Sakit','#3b82f6'],['izin','Izin','#8b5cf6'],['alpha','Alfa','#f06474'],['belum','Belum Absen','#94a3b8']] as $item)
                    <div class="legend-item"><span><span class="legend-dot" style="background:{{ $item[2] }}"></span>{{ $item[1] }}</span><b>{{ $attendanceSeries[$item[0]] }}</b></div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-7">
            <section class="surface panel">
                <div class="panel-heading"><div><h3 class="panel-title">Peserta Aktif per Posisi</h3><p class="panel-help">Maksimal 6 posisi dengan jumlah peserta aktif terbanyak saat ini.</p></div><span class="tag">Per hari ini</span></div>
                <div class="chart-space compact">
                    <canvas id="chart-posisi" role="img" aria-label="Grafik jumlah peserta aktif menurut posisi PKL"></canvas>
                    @if(count($positionSeries) === 0)<div class="chart-empty"><i class="fas fa-users-slash" aria-hidden="true"></i><span>Belum ada peserta aktif untuk posisi ini.</span></div>@endif
                </div>
            </section>
        </div>
        <div class="col-xl-5">
            <section class="surface panel">
                <div class="panel-heading"><div><h3 class="panel-title">Perlu Tindakan</h3><p class="panel-help">Pekerjaan administratif yang membutuhkan perhatian.</p></div></div>
                <div class="action-list">
                    <a href="{{ route('admin.lamaran.index', ['status'=>'pending'] + ($selectedPosisi ? ['lowongan_id'=>$selectedPosisi] : [])) }}" class="action-row"><span class="action-icon"><i class="fas fa-envelope-open-text" aria-hidden="true"></i></span><span><strong class="d-block" style="font-size:.82rem">Verifikasi lamaran</strong><span class="action-desc">Menunggu keputusan penerimaan</span></span><span class="action-count">{{ $stats['pelamar_pending'] }}</span></a>
                    <a href="{{ route('admin.izin.index', ['status'=>'pending']) }}" class="action-row"><span class="action-icon"><i class="fas fa-file-medical" aria-hidden="true"></i></span><span><strong class="d-block" style="font-size:.82rem">Tinjau pengajuan izin</strong><span class="action-desc">Izin dan sakit belum diproses</span></span><span class="action-count">{{ $stats['izin_pending'] }}</span></a>
                    <a href="{{ route('admin.peserta_aktif.index') }}" class="action-row"><span class="action-icon"><i class="fas fa-hourglass-half" aria-hidden="true"></i></span><span><strong class="d-block" style="font-size:.82rem">Peserta segera selesai</strong><span class="action-desc">Masa PKL berakhir dalam 7 hari</span></span><span class="action-count">{{ $stats['segera_selesai'] }}</span></a>
                </div>
            </section>
        </div>
    </div>

    <section class="surface panel">
        <div class="panel-heading"><div><h3 class="panel-title">Lamaran Terbaru</h3><p class="panel-help">Lima lamaran paling baru berdasarkan waktu pendaftaran.</p></div><a href="{{ route('admin.lamaran.index', ['status'=>'all']) }}" class="small text-decoration-none fw-bold">Lihat semua <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Pelamar</th><th>Posisi</th><th>Waktu</th><th class="text-end">Status</th></tr></thead>
                <tbody>
                    @forelse($latestLamarans as $lamaran)
                    <tr>
                        <td><a class="name text-decoration-none" href="{{ route('admin.lamaran.show', $lamaran->id) }}">{{ $lamaran->pengguna->name ?? 'Pengguna tidak tersedia' }}</a></td>
                        <td>{{ $lamaran->lowongan->posisi ?? 'Posisi tidak tersedia' }}</td>
                        <td><span class="small-muted">{{ optional($lamaran->created_at)->diffForHumans() ?? '-' }}</span></td>
                        <td class="text-end"><span class="status-pill status-{{ $lamaran->status }}">{{ $statusLabels[$lamaran->status] ?? ucfirst($lamaran->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 small-muted">Belum ada data lamaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tren = @json($chartSeries);
    const presensi = @json($attendanceChartValues);
    const positions = @json($positionSeries);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    if (typeof Chart === 'undefined') {
        ['chart-tren','chart-presensi','chart-posisi'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el && el.parentElement && !el.parentElement.querySelector('.chart-empty')) {
                const message = document.createElement('div');
                message.className = 'chart-empty';
                message.textContent = 'Grafik tidak dapat dimuat. Periksa koneksi internet atau library Chart.js.';
                el.parentElement.appendChild(message);
            }
        });
        return;
    }

    const common = { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } } };
    const chartTren = document.getElementById('chart-tren');
    if (chartTren && tren.some(v => Number(v) > 0)) {
        new Chart(chartTren, { type:'line', data:{ labels:months, datasets:[{ label:'Peserta mulai PKL', data:tren, fill:true, borderColor:'#2563eb', backgroundColor:'rgba(37,99,235,.10)', pointBackgroundColor:'#2563eb', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4, pointHoverRadius:6, borderWidth:3, tension:.35 }] }, options:{ ...common, scales:{ y:{ beginAtZero:true, ticks:{ precision:0 }, grid:{ color:'#edf2f8' } }, x:{ grid:{ display:false } } }, plugins:{ ...common.plugins, tooltip:{ callbacks:{ label: ctx => `${ctx.parsed.y} peserta` } } } } });
    }

    const chartPresensi = document.getElementById('chart-presensi');
    if (chartPresensi && presensi.some(v => Number(v) > 0)) {
        new Chart(chartPresensi, { type:'doughnut', data:{ labels:['Hadir','Terlambat','Sakit','Izin','Alfa','Belum Absen'], datasets:[{ data:presensi, backgroundColor:['#23a57d','#f59e0b','#3b82f6','#8b5cf6','#f06474','#94a3b8'], borderColor:'#fff', borderWidth:4, hoverOffset:5 }] }, options:{ ...common, cutout:'68%', plugins:{ ...common.plugins, tooltip:{ callbacks:{ label: ctx => `${ctx.label}: ${ctx.parsed} kesempatan` } } } } });
    }

    const chartPosisi = document.getElementById('chart-posisi');
    if (chartPosisi && positions.length > 0) {
        new Chart(chartPosisi, { type:'bar', data:{ labels:positions.map(p => p.label), datasets:[{ label:'Peserta aktif', data:positions.map(p => p.total), backgroundColor:'#5b88f5', hoverBackgroundColor:'#2563eb', borderRadius:6, maxBarThickness:24 }] }, options:{ ...common, indexAxis:'y', scales:{ x:{ beginAtZero:true, ticks:{ precision:0 }, grid:{ color:'#edf2f8' } }, y:{ grid:{ display:false }, ticks:{ autoSkip:false } } }, plugins:{ ...common.plugins, tooltip:{ callbacks:{ label: ctx => `${ctx.parsed.x} peserta aktif` } } } } });
    }
});
</script>
@endpush
