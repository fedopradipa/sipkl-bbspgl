<?php

namespace App\Http\Controllers\Peserta;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Lamaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PresensiController extends Controller
{
    // ==================================================================
    // KONFIGURASI LOKASI KANTOR (BISA PINDAH KE DATABASE/CONFIG NANTI)
    // ==================================================================
    // GANTI DENGAN TITIK KOORDINAT ASLI BBSPGL
    private $officeLat = -6.730536628420504;
    private $officeLng = 108.26825074170313;
    private $allowedRadius = 100; // Dalam Meter (m)

    /**
     * Proses Check-In (Masuk)
     */
    public function checkin(Request $request)
    {
        // 1. Validasi Input Lokasi (Wajib Ada)
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'latitude.required' => 'Lokasi tidak terdeteksi. Pastikan GPS aktif.',
        ]);

        $user = Auth::user();

        // 2. VALIDASI RADIUS (GEOFENCING)
        $distance = $this->calculateDistance(
            $this->officeLat,
            $this->officeLng,
            $request->latitude,
            $request->longitude
        );

        // Jika jarak user > radius yang diizinkan, tolak!
        if ($distance > $this->allowedRadius) {
            return back()->with('error', 'Anda berada di luar jangkauan kantor! Jarak Anda: ' . floor($distance) . ' meter.');
        }

        // 3. Ambil Lamaran Aktif
        $lamaran = Lamaran::with('lowongan')
            ->where('pengguna_id', $user->id)
            ->where('status', 'diterima')
            ->first();

        if (!$lamaran) {
            return back()->with('error', 'Data magang tidak ditemukan.');
        }

        // 4. Cek Double Absen
        $today = Carbon::today();
        $cek = Presensi::where('lamaran_id', $lamaran->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($cek) {
            return back()->with('warning', 'Anda sudah melakukan Check-in hari ini.');
        }

        // 5. Cek Keterlambatan
        $jamMasukStandar = Carbon::parse($lamaran->lowongan->jam_masuk_standar);
        $jamSekarang = Carbon::now();

        // Bandingkan jam saja (abaikan tanggal)
        $jamMasukLimit = Carbon::createFromTime(
            $jamMasukStandar->hour,
            $jamMasukStandar->minute,
            $jamMasukStandar->second
        );

        $statusWaktu = $jamSekarang->gt($jamMasukLimit) ? 'terlambat' : 'tepat_waktu';

        // 6. Simpan Data
        Presensi::create([
            'lamaran_id' => $lamaran->id,
            'tanggal' => $today,
            'jam_masuk' => $jamSekarang->format('H:i:s'),
            'status' => 'hadir',
            'status' => $statusWaktu,
            'lat_masuk' => $request->latitude,
            'long_masuk' => $request->longitude,
        ]);

        return back()->with('success', 'Berhasil Check-in! Jarak: ' . floor($distance) . 'm.');
    }

    /**
     * Proses Check-Out (Pulang)
     */
    /**
     * Proses Check-Out (Pulang)
     */
    public function checkout(Request $request)
    {
        // 1. Validasi Lokasi
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $distance = $this->calculateDistance(
            $this->officeLat,
            $this->officeLng,
            $request->latitude,
            $request->longitude
        );

        if ($distance > $this->allowedRadius) {
            return back()->with('error', 'Anda harus berada di kantor untuk Check-out. Jarak: ' . floor($distance) . 'm.');
        }

        $user = Auth::user();
        $today = Carbon::today();

        // 2. Ambil Lamaran + Data Lowongan
        // PENTING: Tambahkan 'with' untuk mengambil data jam kerja lowongan
        $lamaran = Lamaran::with('lowongan')
            ->where('pengguna_id', $user->id)
            ->latest()
            ->first();

        // ============================================================
        // LOGIKA KEAMANAN WAKTU (Time Barrier)
        // ============================================================

        // Ambil jam pulang standar dari database (misal: 16:00:00)
        $jamPulangStandar = Carbon::parse($lamaran->lowongan->jam_pulang_standar);

        // Gabungkan tanggal HARI INI dengan jam pulang tersebut
        // Contoh hasil: 2025-11-29 16:00:00
        $batasWaktuPulang = Carbon::today()->setTimeFrom($jamPulangStandar);

        // Cek: Apakah Waktu Sekarang (Now) KURANG DARI Batas Waktu?
        if (Carbon::now()->lt($batasWaktuPulang)) {
            return back()->with('error', 'Belum waktunya pulang! Jam pulang standar: ' . $jamPulangStandar->format('H:i'));
        }
        // ============================================================

        // 3. Cek Presensi Hari Ini
        $presensi = Presensi::where('lamaran_id', $lamaran->id)
            ->whereDate('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->first();

        if (!$presensi) {
            return back()->with('error', 'Anda belum melakukan Check-in hari ini.');
        }

        // 4. Lakukan Update
        $presensi->update([
            'jam_pulang' => Carbon::now()->format('H:i:s'),
            'lat_pulang' => $request->latitude,
            'long_pulang' => $request->longitude
        ]);

        return back()->with('success', 'Berhasil Check-out');
    }

    /**
     * Menampilkan Riwayat Kehadiran Peserta
     */
    public function riwayat(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil Lamaran Aktif
        $lamaran = \App\Models\Lamaran::where('pengguna_id', $user->id)
            ->where('status', 'diterima')
            ->first();

        if (!$lamaran) {
            return redirect()->route('peserta.dashboard')->with('error', 'Data tidak ditemukan.');
        }

        // 2. Filter Tanggal (Default: Bulan Ini)
        $mulai = $request->input('mulai', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->input('sampai', now()->endOfMonth()->format('Y-m-d'));

        // 3. Query Data
        $presensi = \App\Models\Presensi::where('lamaran_id', $lamaran->id)
            ->whereBetween('tanggal', [$mulai, $sampai])
            ->latest('tanggal')
            ->paginate(10)
            ->withQueryString();

        // 4. Hitung Statistik Sederhana (Untuk periode yang dipilih)
        $summary = [
            'hadir' => $presensi->where('status', 'hadir')->count(),
            'terlambat' => $presensi->where('status', 'terlambat')->count(),
            'izin_sakit' => $presensi->whereIn('status', ['sakit', 'izin'])->count(),
            'alpha' => $presensi->where('status', 'alpha')->count(),
        ];

        return view('peserta.presensi.index', [
            'title' => 'Riwayat Kehadiran',
            'presensi' => $presensi,
            'summary' => $summary,
            'mulai' => $mulai,
            'sampai' => $sampai
        ]);
    }




    /**
     * ------------------------------------------------------------------
     * HELPER: RUMUS HAVERSINE (MENGHITUNG JARAK 2 KOORDINAT GPS)
     * ------------------------------------------------------------------
     * Mengembalikan jarak dalam satuan METER
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance; // Hasil dalam Meter
    }
}
