<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod; // Library untuk looping tanggal

class IzinAdminController extends Controller
{
    /**
     * Daftar Pengajuan Izin Masuk
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending'); // Default tampilkan yang pending

        $dataIzin = PengajuanIzin::with(['lamaran.pengguna', 'lamaran.lowongan'])
            ->when($status !== 'all', function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.izin.index', [
            'title' => 'Verifikasi Izin/Sakit',
            'dataIzin' => $dataIzin,
            'currentStatus' => $status
        ]);
    }

    /**
     * LOGIC UTAMA: Setujui & Bulk Insert Presensi
     */
    public function approve(Request $request, $id)
    {
        // Gunakan Transaction agar jika insert presensi gagal, status izin batal berubah
        DB::transaction(function () use ($id) {
            $izin = PengajuanIzin::findOrFail($id);

            // 1. Update Status Izin
            $izin->update(['status' => 'disetujui']);

            // 2. BULK INSERT KE TABEL PRESENSI
            // Buat rentang tanggal dari Mulai s/d Selesai
            $periode = CarbonPeriod::create($izin->tgl_mulai, $izin->tgl_selesai);

            foreach ($periode as $date) {
                $tanggal = $date->format('Y-m-d');

                // Cek dulu: Apakah sudah ada presensi di tanggal itu? (Supaya tidak duplikat)
                $exists = Presensi::where('lamaran_id', $izin->lamaran_id)
                    ->whereDate('tanggal', $tanggal)
                    ->exists();

                if (!$exists) {
                    Presensi::create([
                        'lamaran_id'       => $izin->lamaran_id,
                        'tanggal'          => $tanggal,
                        'jam_masuk'        => null, // Kosong karena tidak hadir fisik
                        'jam_pulang'       => null,
                        'status'           => $izin->jenis_izin, // 'sakit' atau 'izin'
                        'keterangan_waktu' => null,
                        'catatan_harian'   => "Pengajuan " . ucfirst($izin->jenis_izin) . " Disetujui Admin. (Ref: #" . $izin->id . ")",
                        'is_auto_closed'   => true // Tandai ditutup sistem
                    ]);
                }
            }
        });

        return back()->with('success', 'Izin disetujui. Data presensi telah digenerate otomatis.');
    }

    /**
     * Tolak Izin
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['catatan_admin' => 'required|string|min:5']);

        $izin = PengajuanIzin::findOrFail($id);
        $izin->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->catatan_admin
        ]);

        return back()->with('success', 'Pengajuan izin ditolak.');
    }
}
