<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    private $officeLat = -6.914744; // Contoh: Bandung
    private $officeLng = 107.609810;
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

        // 2. VALIDASI RADIUS (GEOFENCING) - BEST PRACTICE
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
            'keterangan_waktu' => $statusWaktu,
            'lat_masuk' => $request->latitude,
            'long_masuk' => $request->longitude,
        ]);

        return back()->with('success', 'Berhasil Check-in! Jarak: ' . floor($distance) . 'm.');
    }

    /**
     * Proses Check-Out (Pulang)
     */
    public function checkout(Request $request)
    {
        // Validasi Lokasi juga saat pulang (Opsional, tapi disarankan)
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
        $lamaran = Lamaran::where('pengguna_id', $user->id)->latest()->first();

        $presensi = Presensi::where('lamaran_id', $lamaran->id)
            ->whereDate('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->first();

        if (!$presensi) {
            return back()->with('error', 'Anda belum melakukan Check-in hari ini.');
        }

        $presensi->update([
            'jam_pulang' => Carbon::now()->format('H:i:s'),
            'lat_pulang' => $request->latitude,
            'long_pulang' => $request->longitude
        ]);

        return back()->with('success', 'Berhasil Check-out.');
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
