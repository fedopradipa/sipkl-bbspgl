<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\PengajuanIzin;
use App\Services\RekapPresensiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private RekapPresensiService $rekapService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->isAdmin(), 403, 'Akses hanya untuk Admin');

        $validated = $request->validate([
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'between:2020,2100'],
            'lowongan_id' => ['nullable', 'integer', 'exists:lowongans,id'],
        ]);

        $bulan = (int) ($validated['bulan'] ?? now()->month);
        $tahun = (int) ($validated['tahun'] ?? now()->year);
        $lowonganId = isset($validated['lowongan_id']) ? (int) $validated['lowongan_id'] : null;
        $today = Carbon::today();

        // Pakai service Rekap Presensi yang sama agar hitungan dashboard
        // dan halaman Rekap konsisten. Tidak menyimpan/mengubah data.
        $rekapBulanan = $this->rekapService->getRekapMatrix($bulan, $tahun, $lowonganId);
        $rekapHariIni = ($bulan === $today->month && $tahun === $today->year)
            ? $rekapBulanan
            : $this->rekapService->getRekapMatrix($today->month, $today->year, $lowonganId);

        $todayAttendance = $this->summarizeToday($rekapHariIni, $today);

        return view('admin.dashboard.index', [
            'title' => 'Dashboard Admin',
            'header' => 'Monitoring & Analitik PKL',
            'user' => $user,
            'stats' => $this->buildStats($today, $lowonganId, $todayAttendance),
            'chartSeries' => $this->buildChartSeries($tahun, $lowonganId),
            'attendanceSeries' => $this->summarizeMonth($rekapBulanan, $today),
            'todayAttendance' => $todayAttendance,
            'positionSeries' => $this->buildPositionSeries($today, $lowonganId),
            'latestLamarans' => $this->buildLatestLamarans($lowonganId),
            'positionOptions' => Lowongan::withTrashed()->orderBy('posisi')->get(['id', 'posisi']),
            'filters' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'lowongan_id' => $lowonganId,
            ],
            'periodLabel' => Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y'),
            'todayLabel' => $today->copy()->locale('id')->translatedFormat('d F Y'),
        ]);
    }

    /** KPI terkini; filter bulan/tahun berlaku untuk analisis bulanan saja. */
    private function buildStats(Carbon $today, ?int $lowonganId, array $todayAttendance): array
    {
        $tanggal = $today->toDateString();
        $pesertaAktif = Lamaran::query()
            ->where('status', 'diterima')
            ->whereDate('tgl_mulai_resmi', '<=', $tanggal)
            ->whereDate('tgl_selesai_resmi', '>=', $tanggal)
            ->when($lowonganId, fn ($q) => $q->where('lowongan_id', $lowonganId));

        $wajibHadir = array_sum($todayAttendance);
        $hadirHariIni = $todayAttendance['hadir'] + $todayAttendance['terlambat'];

        return [
            'pelamar_pending' => Lamaran::where('status', 'pending')
                ->when($lowonganId, fn ($q) => $q->where('lowongan_id', $lowonganId))
                ->count(),
            'peserta_aktif' => (clone $pesertaAktif)->count(),
            'lowongan_aktif' => Lowongan::tersedia()
                ->when($lowonganId, fn ($q) => $q->whereKey($lowonganId))
                ->count(),
            'izin_hari_ini' => $todayAttendance['izin'] + $todayAttendance['sakit'],
            'izin_pending' => PengajuanIzin::where('status', 'pending')
                ->when($lowonganId, fn ($q) => $q->whereHas('lamaran', fn ($sub) => $sub->where('lowongan_id', $lowonganId)))
                ->count(),
            'hadir_hari_ini' => $hadirHariIni,
            'wajib_hadir' => $wajibHadir,
            'belum_hari_ini' => $todayAttendance['belum'],
            'kehadiran_persen' => $wajibHadir > 0 ? round(($hadirHariIni / $wajibHadir) * 100, 1) : null,
            'segera_selesai' => (clone $pesertaAktif)
                ->whereDate('tgl_selesai_resmi', '<=', $today->copy()->addDays(7)->toDateString())
                ->count(),
        ];
    }

    /** Hitung hanya tanggal yang SUDAH terjadi, bukan hari kerja di masa depan. */
    private function summarizeMonth(array $matrix, Carbon $today): array
    {
        $counts = $this->emptyStatusCounts();
        $todayKey = $today->toDateString();

        foreach ($matrix['data'] as $participant) {
            foreach ($participant['harian'] as $tanggal => $log) {
                if ($tanggal > $todayKey) {
                    continue;
                }
                $status = $log['status'] ?? '';
                if (array_key_exists($status, $counts)) {
                    $counts[$status]++;
                }
            }
        }

        return $counts;
    }

    /** Kondisi khusus tanggal hari ini, tidak dipengaruhi filter bulan/tahun. */
    private function summarizeToday(array $matrix, Carbon $today): array
    {
        $counts = $this->emptyStatusCounts();
        $todayKey = $today->toDateString();

        foreach ($matrix['data'] as $participant) {
            $status = $participant['harian'][$todayKey]['status'] ?? '';
            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }
        }

        return $counts;
    }

    private function emptyStatusCounts(): array
    {
        return [
            'hadir' => 0,
            'terlambat' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'belum' => 0,
        ];
    }

    /** Tren berdasarkan tanggal MULAI PKL resmi, termasuk peserta yang sudah selesai/berhenti. */
    private function buildChartSeries(int $tahun, ?int $lowonganId): array
    {
        $byMonth = Lamaran::query()
            ->selectRaw('MONTH(tgl_mulai_resmi) AS bulan, COUNT(*) AS total')
            ->whereIn('status', ['diterima', 'selesai', 'berhenti'])
            ->whereNotNull('tgl_mulai_resmi')
            ->whereYear('tgl_mulai_resmi', $tahun)
            ->whereDate('tgl_mulai_resmi', '<=', Carbon::today()->toDateString())
            ->when($lowonganId, fn ($q) => $q->where('lowongan_id', $lowonganId))
            ->groupByRaw('MONTH(tgl_mulai_resmi)')
            ->pluck('total', 'bulan');

        return array_map(fn ($bulan) => (int) ($byMonth[$bulan] ?? 0), range(1, 12));
    }

    /** Enam posisi dengan jumlah peserta aktif terbanyak pada tanggal hari ini. */
    private function buildPositionSeries(Carbon $today, ?int $lowonganId): array
    {
        $tanggal = $today->toDateString();

        return Lamaran::query()
            ->selectRaw('lowongan_id, COUNT(*) AS total')
            ->with('lowongan')
            ->where('status', 'diterima')
            ->whereDate('tgl_mulai_resmi', '<=', $tanggal)
            ->whereDate('tgl_selesai_resmi', '>=', $tanggal)
            ->when($lowonganId, fn ($q) => $q->where('lowongan_id', $lowonganId))
            ->groupBy('lowongan_id')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($item) => [
                'label' => $item->lowongan->posisi ?? 'Posisi tidak tersedia',
                'total' => (int) $item->total,
            ])
            ->all();
    }

    private function buildLatestLamarans(?int $lowonganId)
    {
        return Lamaran::with(['pengguna', 'lowongan'])
            ->when($lowonganId, fn ($q) => $q->where('lowongan_id', $lowonganId))
            ->latest()
            ->limit(5)
            ->get();
    }

}
