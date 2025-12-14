<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Tambahkan Log untuk debugging (opsional)

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
            'dataIzin' => $dataIzin, // <-- Variabel sudah benar: 'dataIzin'
            'currentStatus' => $status
        ]);
    }

    /**
     * LOGIC UTAMA: Setujui & Bulk Insert Presensi (FIXED untuk Jadwal Fleksibel)
     */
    public function approve(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            // Eager load relasi yang dibutuhkan: lamaran -> pengguna
            $izin = PengajuanIzin::with('lamaran.pengguna', 'lamaran')->findOrFail($id);

            if (!$izin->lamaran || !$izin->lamaran->pengguna) {
                throw new \Exception("Data Lamaran atau Pengguna tidak ditemukan untuk ID Izin: " . $id);
            }

            $userId = $izin->lamaran->pengguna->id;
            $presensiStatus = $izin->jenis_izin;

            // Ambil Jadwal Kerja Resmi Peserta (dari Lamaran)
            $jadwalResmi = $izin->lamaran->hari_kerja_resmi ?? []; // Harus berupa array Indonesia, misal ['Senin', 'Kamis']

            // 1. Filter Tanggal Periode Izin hanya pada Hari Kerja Resmi Peserta
            $tanggalKerjaYangIzin = $this->filterIzinBySchedule($izin->tgl_mulai, $izin->tgl_selesai, $jadwalResmi);

            // 2. Update Status Izin
            $izin->update(['status' => 'disetujui']);

            $presensiData = [];

            foreach ($tanggalKerjaYangIzin as $tanggal) {
                // Konversi objek Carbon/DateTime ke format string Y-m-d untuk query
                $tanggalString = $tanggal->format('Y-m-d');

                // Cek dulu: Apakah sudah ada presensi di tanggal itu?
                // Penting: Gunakan kolom 'user_id' untuk cek exist
                $exists = Presensi::where('user_id', $userId)
                    ->whereDate('tanggal', $tanggalString)
                    ->exists();

                if (!$exists) {
                    $presensiData[] = [
                        'user_id'          => $userId,
                        'lamaran_id'       => $izin->lamaran_id,
                        'tanggal'          => $tanggalString,
                        'jam_masuk'        => null,
                        'jam_pulang'       => null,
                        'status'           => $presensiStatus,
                        'catatan_harian'   => "Pengajuan " . ucfirst($izin->jenis_izin) . " Disetujui Admin. (Ref: #" . $izin->id . ")",
                        'is_auto_closed'   => true,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }

            if (!empty($presensiData)) {
                Presensi::insert($presensiData);
            }
        });

        return back()->with('success', 'Izin disetujui. Data presensi untuk hari kerja yang relevan telah digenerate otomatis.');
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


// ------------------------------------------------------------------
    // NEW HELPER FUNCTION
    // ------------------------------------------------------------------

    /**
     * Helper: Memfilter rentang tanggal izin hanya pada hari kerja peserta.
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $workingDaysInIndo ['Senin', 'Kamis', ...]
     * @return array of Carbon objects
     */
    private function filterIzinBySchedule(Carbon $startDate, Carbon $endDate, array $workingDaysInIndo): array
    {
        if (empty($workingDaysInIndo)) {
            // Jika jadwal kosong (fallback safety), asumsikan 5 hari kerja
            $workingDaysInIndo = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        }

        $periode = CarbonPeriod::create($startDate, $endDate);
        $filteredDates = [];

        // Mapping Carbon Day (English) ke nama hari Indonesia
        $dayMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];

        foreach ($periode as $date) {
            $dayNameEnglish = $date->format('l');
            $dayNameIndo = $dayMap[$dayNameEnglish] ?? null;

            // Cek apakah hari ini termasuk dalam jadwal kerja resmi peserta
            if (in_array($dayNameIndo, $workingDaysInIndo)) {
                $filteredDates[] = $date->clone();
            }
        }

        return $filteredDates;
    }
}
