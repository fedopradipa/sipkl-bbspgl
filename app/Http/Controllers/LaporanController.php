<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan daftar presensi harian seluruh peserta
     */
    public function presensi(Request $request)
    {
        // 1. Ambil Parameter Filter
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d')); // Default hari ini
        $status = $request->input('status');

        // 2. Query Data Presensi
        // Kita gunakan Eager Loading ke Lamaran -> Pengguna & Lowongan
        $presensis = Presensi::with(['lamaran.pengguna', 'lamaran.lowongan'])
            ->whereDate('tanggal', $tanggal)
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->latest('jam_masuk')
            ->get(); // Gunakan get() karena laporan harian biasanya tidak terlalu banyak (bisa diganti paginate)

        // 3. Hitung Ringkasan untuk Card di atas tabel
        $ringkasan = [
            'hadir' => $presensis->where('status', 'hadir')->count(),
            'terlambat' => $presensis->where('keterangan_waktu', 'terlambat')->count(),
            'sakit_izin' => $presensis->whereIn('status', ['sakit', 'izin'])->count(),
            'alpha' => $presensis->where('status', 'alpha')->count(),
        ];

        return view('admin.laporan.presensi', [
            'title' => 'Laporan Presensi Harian',
            'header' => 'Monitoring Kehadiran',
            'presensis' => $presensis,
            'tanggal' => $tanggal,
            'ringkasan' => $ringkasan
        ]);
    }
}
