<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peserta\RiwayatPresensiIndexRequest;
use App\Services\RiwayatPresensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RiwayatPresensiController extends Controller
{
    public function __construct(private RiwayatPresensiService $service) {}
    public function index(RiwayatPresensiIndexRequest $request)
    {
        $validated = $request->validated();
        $userId = Auth::id();

        $lamaran = $this->service->getLamaranTerbaruUntukRiwayat($userId);

        if (!$lamaran) {
            $emptyPaginator = $this->service->emptyPaginator(10);

            return view('peserta.riwayatpresensi.index', [
                'title'    => 'Riwayat Presensi',
                'presensi' => $emptyPaginator,
                'summary'  => ['hadir' => 0, 'terlambat' => 0, 'izin_sakit' => 0, 'alpha' => 0],
                'mulai'    => Carbon::now()->startOfMonth()->toDateString(),
                'sampai'   => Carbon::now()->endOfMonth()->toDateString(),
            ])->with('warning', 'Tidak ada riwayat untuk periode aktif.');
        }

        $mulai  = $validated['mulai'] ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai = $validated['sampai'] ?? Carbon::now()->endOfMonth()->toDateString();

        $data = $this->service->buildRiwayatData($lamaran->id, $mulai, $sampai, 10);

        return view('peserta.riwayatpresensi.index', [
            'title'    => 'Riwayat Presensi',
            'presensi' => $data['presensi'],
            'summary'  => $data['summary'],
            'mulai'    => $data['mulai'],
            'sampai'   => $data['sampai'],
        ]);
    }
}
