<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Presensi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $stats = $this->buildStats();
            $chartSeries = $this->buildChartSeries();
            $latestLamarans = $this->buildLatestLamarans();
            return view('admin.dashboard.index', [
                'title' => 'Dashboard Admin',
                'header' => 'Overview Sistem',
                'user'           => $user,
                'stats'          => $stats,
                'chartSeries'    => $chartSeries,
                'latestLamarans' => $latestLamarans
            ]);
        }
        abort(403, 'Akses hanya untuk Admin');
    }
    private function buildStats(): array
    {
        return [
            'pelamar_pending' => Lamaran::where('status', 'pending')->count(),
            'peserta_aktif'   => Lamaran::where('status', 'diterima')->count(),
            'lowongan_aktif'  => Lowongan::where('is_open', true)->count(),
            'izin_hari_ini'   => Presensi::whereDate('tanggal', Carbon::today())
                ->whereIn('status', ['sakit', 'izin'])
                ->count(),
        ];
    }
    private function buildChartSeries(): array
    {
        $chartData = Lamaran::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->where('status', 'diterima')
            ->whereYear('created_at', date('Y'))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();
        $chartSeries = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartSeries[] = $chartData[$i] ?? 0;
        }
        return $chartSeries;
    }
    private function buildLatestLamarans()
    {
        return Lamaran::with(['pengguna', 'lowongan'])
            ->latest()
            ->take(5)
            ->get();
    }
}
