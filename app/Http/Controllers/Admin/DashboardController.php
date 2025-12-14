<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 1. WAJIB: Import Facade Auth
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // 2. WAJIB: Import Carbon untuk tanggal
// 3. WAJIB: Import Model
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Presensi;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama dengan ringkasan data.
     */
    public function index()
    {
        // 4. PERBAIKAN UTAMA: Definisikan variabel $user di sini
        $user = Auth::user();

        // ==========================================
        // JALUR 1: KHUSUS ADMIN & SUPERADMIN
        // ==========================================
        // Kita cek apakah dia Admin ATAU Superadmin
        if ($user->isAdmin()) {

            // A. KARTU STATISTIK
            $stats = [
                'pelamar_pending' => Lamaran::where('status', 'pending')->count(),
                'peserta_aktif'   => Lamaran::where('status', 'diterima')->count(),
                'lowongan_aktif'  => Lowongan::where('is_open', true)->count(),
                // Hitung yang izin/sakit HARI INI
                'izin_hari_ini'   => Presensi::whereDate('tanggal', Carbon::today())
                    ->whereIn('status', ['sakit', 'izin'])->count(),
            ];

            // B. DATA GRAFIK (Peserta diterima per bulan tahun ini)
            $chartData = Lamaran::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
                ->where('status', 'diterima')
                ->whereYear('created_at', date('Y'))
                ->groupBy('bulan')
                ->pluck('total', 'bulan')->toArray();

            // Normalisasi data grafik (isi bulan kosong dengan 0)
            $chartSeries = [];
            for ($i = 1; $i <= 12; $i++) {
                $chartSeries[] = $chartData[$i] ?? 0;
            }

            // C. TABEL RINGKAS (5 Lamaran Terbaru)
            $latestLamarans = Lamaran::with(['pengguna', 'lowongan'])
                ->latest()
                ->take(5)
                ->get();

            return view('admin.dashboard.index', [
                'title'          => 'Dashboard Admin',
                'header'         => 'Overview Sistem',
                'user'           => $user, // Variabel user dikirim ke View
                'stats'          => $stats,
                'chartSeries'    => $chartSeries,
                'latestLamarans' => $latestLamarans
            ]);
        }

        // Fallback jika bukan admin (Safety)
        abort(403, 'Akses hanya untuk Admin');
    }
}
