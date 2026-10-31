@php
$startDate = \Carbon\Carbon::parse($start);
$endDate = \Carbon\Carbon::parse($end);
$now = \Carbon\Carbon::now()->startOfDay(); // Reset jam ke 00:00 agar hitungan hari akurat

// 1. Cek Status Waktu
$isPreStart = $now->lt($startDate); // Belum mulai
$isFinished = $now->gt($endDate); // Sudah lewat

// 2. Hitung Total Durasi Kontrak (Inklusif)
$totalDuration = $startDate->diffInDays($endDate) + 1;

// 3. Logika Progress & Label
if ($isPreStart) {
// KASUS: BELUM MULAI
$progress = 0;
$passedDays = 0;
$sisaHari = $totalDuration; // Sisa masih utuh
$statusLabel = "Belum Mulai";
$statusColor = "text-secondary";
$barColor = "bg-secondary";

// Hitung H-berapa mulai
$daysToStart = $now->diffInDays($startDate);
$subLabel = "Mulai dalam $daysToStart hari";

} elseif ($isFinished) {
// KASUS: SUDAH SELESAI / LEWAT
$progress = 100;
$passedDays = $totalDuration;
$sisaHari = 0;
$statusLabel = "Masa PKL Habis";
$statusColor = "text-danger";
$barColor = "bg-danger";

// Hitung lewat berapa hari
$overdueDays = $endDate->diffInDays($now);
$subLabel = "Lewat $overdueDays hari";

} else {
// KASUS: SEDANG BERJALAN (ACTIVE)

// Hari yang sudah berlalu (dari start sampai now)
$passedDays = $startDate->diffInDays($now) + 1;

// Persentase
$progress = ($passedDays / $totalDuration) * 100;

// Sisa hari (dari now sampai end)
$sisaHari = $now->diffInDays($endDate);

$subLabel = "Sisa " . ceil($sisaHari) . " hari";

// Tentukan Warna Bar berdasarkan sisa waktu
if ($sisaHari > 14) {
$barColor = 'bg-primary';
$statusLabel = 'Berjalan (Aman)';
$statusColor = 'text-primary';
} elseif ($sisaHari > 5) {
$barColor = 'bg-info';
$statusLabel = 'Berjalan';
$statusColor = 'text-info';
} else {
$barColor = 'bg-warning';
$statusLabel = 'Segera Berakhir';
$statusColor = 'text-warning';
}
}
@endphp

<div class="w-100">
    {{-- Header: Label Status --}}
    <div class="d-flex justify-content-between align-items-end mb-1">
        <div class="d-flex align-items-center">
            <small class="text-muted fw-bold me-2" style="font-size: 0.7rem;">PROGRESS</small>
            @if(!$isPreStart)
            <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">{{ round($progress) }}%</span>
            @endif
        </div>

        {{-- Label Kanan (Status Sisa Hari) --}}
        <small class="{{ $statusColor }} fw-bold" style="font-size: 0.75rem;">
            @if($isPreStart)
            <i class="far fa-calendar me-1"></i> {{ $subLabel }}
            @elseif($isFinished)
            <i class="fas fa-exclamation-circle me-1"></i> {{ $statusLabel }}
            @else
            <i class="fas fa-clock me-1"></i> {{ $subLabel }}
            @endif
        </small>
    </div>

    {{-- Bar --}}
    <div class="progress shadow-sm bg-light"
        style="height: 10px; border-radius: 10px;"
        title="{{ $isPreStart ? 'Menunggu tanggal mulai' : "Hari ke-$passedDays dari $totalDuration hari" }}">

        <div class="progress-bar {{ $barColor }} progress-bar-striped {{ (!$isPreStart && !$isFinished) ? 'progress-bar-animated' : '' }}"
            role="progressbar"
            style="width: {{ $progress }}%; border-radius: 10px;"
            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
        </div>
    </div>

    {{-- Footer: Tanggal --}}
    <div class="d-flex justify-content-between mt-1">
        <small class="text-muted {{ $isPreStart ? 'fw-bold text-dark' : '' }}" style="font-size: 0.7rem;">
            {{ $startDate->format('d M') }}
        </small>
        <small class="text-muted fw-bold" style="font-size: 0.7rem;">
            {{ $endDate->format('d M Y') }}
        </small>
    </div>
</div>