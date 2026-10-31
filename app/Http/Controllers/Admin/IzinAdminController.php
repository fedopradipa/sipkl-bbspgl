<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IzinAdminIndexRequest;
use App\Http\Requests\Admin\IzinAdminApproveRequest;
use App\Http\Requests\Admin\IzinAdminRejectRequest;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;

class IzinAdminController extends Controller
{
    public function index(IzinAdminIndexRequest $request)
    {
        $request->validated();
        $status = $request->input('status', 'pending');
        $search = $request->input('search');
        $dataIzin = PengajuanIzin::with(['lamaran.pengguna', 'lamaran.lowongan'])
            ->when($status !== 'all', function ($q) use ($status) {
                return $q->where('status', $status);
            })
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
    public function approve(IzinAdminApproveRequest $request, $id)
    {
        $request->validated();
        DB::transaction(function () use ($id) {
            $izin = PengajuanIzin::with('lamaran.pengguna')->findOrFail($id);
            if (!$izin->lamaran || !$izin->lamaran->pengguna) {
                throw new \Exception("Data Lamaran atau Pengguna tidak ditemukan untuk ID Izin: " . $id);
            }
            $userId = $izin->lamaran->pengguna->id;
            $presensiStatus = $izin->jenis_izin;
            $daftarTanggal = $izin->tanggal_izin ?? [];
            if (empty($daftarTanggal)) {
                throw new \Exception("Data tanggal izin kosong.");
            }
            $izin->update(['status' => 'disetujui']);
            $presensiData = [];
            foreach ($daftarTanggal as $tanggalString) {
                $exists = Presensi::where('pengguna_id', $userId)
                    ->whereDate('tanggal', $tanggalString)
                    ->exists();
                if (!$exists) {
                    $presensiData[] = [
                        'pengguna_id'    => $userId,
                        'lamaran_id' => $izin->lamaran_id,
                        'tanggal'        => $tanggalString,
                        'jam_masuk' => null,
                        'status'         => $presensiStatus,
                        'catatan_harian' => "Pengajuan " . ucfirst($izin->jenis_izin) . " Disetujui Admin.",
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
            }
            if (!empty($presensiData)) {
                Presensi::insert($presensiData);
            }
        });
        return back()->with('success', 'Izin disetujui. Data presensi telah digenerate sesuai tanggal yang diajukan.');
    }
    public function reject(IzinAdminRejectRequest $request, $id)
    {
        $validated = $request->validated();
        $izin = PengajuanIzin::findOrFail($id);
        $izin->update([
            'status' => 'ditolak',
            'catatan_admin' => $validated['catatan_admin']
        ]);
        return back()->with('success', 'Pengajuan izin ditolak.');
    }
}
