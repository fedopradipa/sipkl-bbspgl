<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Services\LamaranService;    // <--- Import Service
use App\Models\Lamaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardPesertaController extends Controller
{
    protected $lamaranService;

    // Inject Service
    public function __construct(LamaranService $lamaranService)
    {
        $this->lamaranService = $lamaranService;
    }

    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Data Lamaran Terkini
        $lamaran = Lamaran::with(['lowongan', 'pembimbing'])
            ->where('pengguna_id', $user->id)
            ->latest()
            ->first();

        // Jika belum melamar sama sekali
        if (!$lamaran) {
            return view('peserta.dashboard.empty', ['title' => 'Selamat Datang']);
        }

        // 2. Logic Service: Tentukan State (Pending/Active/Selesai/Berhenti)
        $state = $this->lamaranService->determineDashboardState($lamaran);

        // 3. Logic Service: Auto Close Absen Kemarin
        $this->lamaranService->runAutoCloseCheck($lamaran->id);

        // 4. Siapkan Data Presensi (Hanya jika Active)
        $isHariKerja = false;
        $presensi    = null;

        if ($state == 'active') {
            $isHariKerja = $this->lamaranService->isWorkingDay($lamaran);

            if ($isHariKerja) {
                $presensi = $this->lamaranService->getTodayPresensi($lamaran->id);
            }
        }

        return view('peserta.dashboard.index', [
            'title'       => 'Dashboard Peserta',
            'user'        => $user,
            'lamaran'     => $lamaran,
            'state'       => $state,
            'presensi'    => $presensi,
            'isHariKerja' => $isHariKerja,
        ]);
    }
}
