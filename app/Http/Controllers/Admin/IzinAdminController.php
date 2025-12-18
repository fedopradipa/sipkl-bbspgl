<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IzinAdminController extends Controller
{
    /**
     * Daftar Pengajuan Izin Masuk
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $search = $request->input('search');

        $dataIzin = PengajuanIzin::with(['lamaran.pengguna', 'lamaran.lowongan'])
            ->when($status !== 'all', function ($q) use ($status) {
                return $q->where('status', $status);
            })
            // Tambahkan Logika Search Ini:
            ->when($search, function ($q) use ($search) {
                $q->whereHas('lamaran.pengguna', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
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
     * LOGIC UTAMA: Setujui & Insert Presensi Berdasarkan Array Tanggal
     */
    public function approve(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            // 1. Ambil Data Izin
            $izin = PengajuanIzin::with('lamaran.pengguna')->findOrFail($id);

            // Validasi Data Relasi
            if (!$izin->lamaran || !$izin->lamaran->pengguna) {
                throw new \Exception("Data Lamaran atau Pengguna tidak ditemukan untuk ID Izin: " . $id);
            }

            $userId = $izin->lamaran->pengguna->id;
            $presensiStatus = $izin->jenis_izin; // 'sakit' atau 'izin'

            // 2. Ambil Array Tanggal dari Database
            // Karena di Model sudah di-cast 'array', ini otomatis jadi Array PHP
            $daftarTanggal = $izin->tanggal_izin ?? [];

            if (empty($daftarTanggal)) {
                throw new \Exception("Data tanggal izin kosong.");
            }

            // 3. Update Status Izin jadi Disetujui
            $izin->update(['status' => 'disetujui']);

            $presensiData = [];

            // 4. Loop setiap tanggal yang dipilih user
            foreach ($daftarTanggal as $tanggalString) {
                // $tanggalString formatnya sudah "Y-m-d" (contoh: "2025-10-01")

                // Cek apakah sudah ada presensi di tanggal & user tersebut?
                $exists = Presensi::where('user_id', $userId)
                    ->whereDate('tanggal', $tanggalString)
                    ->exists();

                if (!$exists) {
                    $presensiData[] = [
                        'user_id'          => $userId,
                        'lamaran_id'       => $izin->lamaran_id,
                        'tanggal'          => $tanggalString,
                        'jam_masuk'        => null, // Tidak ada jam masuk karena izin
                        'jam_pulang'       => null,
                        'status'           => $presensiStatus,
                        'catatan_harian'   => "Pengajuan " . ucfirst($izin->jenis_izin) . " Disetujui Admin. (Ref: #" . $izin->id . ")",
                        'is_auto_closed'   => true, // Ditandai tutup otomatis
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }

            // 5. Bulk Insert ke tabel Presensi
            if (!empty($presensiData)) {
                Presensi::insert($presensiData);
            }
        });

        return back()->with('success', 'Izin disetujui. Data presensi telah digenerate sesuai tanggal yang diajukan.');
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
