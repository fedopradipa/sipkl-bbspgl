@php
// Logika Hitung Tanggal
$start = \Carbon\Carbon::parse($start);
$end = \Carbon\Carbon::parse($end);
$now = \Carbon\Carbon::now();

$totalDays = $start->diffInDays($end);
$daysGone = $start->diffInDays($now);

// Hitung persentase (cegah division by zero)
$progress = $totalDays > 0 ? ($daysGone / $totalDays) * 100 : 0;

// Limit range 0-100
$progress = min(100, max(0, $progress));

// Hitung sisa hari (bisa negatif jika sudah lewat)
$sisaHari = $now->diffInDays($end, false);
@endphp

<div class="d-flex justify-content-between small mb-1">
    <span class="text-muted">{{ $start->format('d M') }}</span>

    @if($sisaHari > 0)
    <span class="text-warning fw-bold">Sisa {{ ceil($sisaHari) }} hari</span>
    @elseif($sisaHari == 0)
    <span class="text-danger fw-bold">Hari Terakhir!</span>
    @else
    <span class="text-success fw-bold">Selesai (+{{ abs(ceil($sisaHari)) }} hari)</span>
    @endif

    <span class="text-muted">{{ $end->format('d M y') }}</span>
</div>

<div class="progress" style="height: 6px;">
    <div class="progress-bar {{ $progress >= 100 ? 'bg-success' : 'bg-warning' }}"
        role="progressbar"
        style="width: {{ $progress }}%"
        aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
    </div>
</div>