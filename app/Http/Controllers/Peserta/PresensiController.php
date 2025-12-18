<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peserta\PresensiLokasiRequest;
use App\Services\Peserta\PresensiPesertaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;

class PresensiController extends Controller
{
    protected $presensiService;

    /**
     * Inject Service ke Constructor
     */
    public function __construct(PresensiPesertaService $presensiService)
    {
        $this->presensiService = $presensiService;
    }

    /**
     * Handle Check-In Request
     */
    public function checkIn(PresensiLokasiRequest $request): RedirectResponse
    {
        try {
            // Panggil logic service processCheckIn
            $this->presensiService->processCheckIn(
                $request->input('latitude'),
                $request->input('longitude')
            );

            // Jika sukses, kembali ke halaman sebelumnya dengan pesan sukses
            return redirect()->back()->with('success', 'Berhasil Check-in! Selamat bekerja.');
        } catch (\Exception $e) {
            // Jika ada error (Radius jauh, double absen, dll), tangkap exception dari service
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle Check-Out Request
     */
    public function checkOut(PresensiLokasiRequest $request): RedirectResponse
    {
        try {
            // Panggil logic service processCheckOut
            $this->presensiService->processCheckOut(
                $request->input('latitude'),
                $request->input('longitude')
            );

            // Jika sukses
            return redirect()->back()->with('success', 'Berhasil Check-out! Hati-hati di jalan.');
        } catch (\Exception $e) {
            // Jika ada error (Belum waktunya pulang, di luar radius, dll)
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function riwayat(Request $request)
    {
        // 1. Ambil Input Tanggal (Default: Awal bulan ini s.d. Hari ini/Akhir bulan)
        $mulai = $request->input('mulai', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', Carbon::now()->endOfMonth()->toDateString());

        // 2. Panggil Service untuk dapatkan data
        $data = $this->presensiService->getRiwayat(
            Auth::id(),
            $mulai,
            $sampai
        );

        // 3. Return View dengan data yang dibutuhkan blade
        return view('peserta.presensi.index', [
            'presensi' => $data['presensi'], // Untuk Tabel
            'summary'  => $data['summary'],  // Untuk Cards Statistik
            'mulai'    => $mulai,            // Untuk value input date
            'sampai'   => $sampai,
            'title'   => 'Riwayat Presensi',       // Untuk value input date
        ]);
    }
}
