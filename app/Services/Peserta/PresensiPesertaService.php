<?php

namespace App\Services\Peserta;

use App\Models\Presensi;
use App\Models\Lamaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PresensiPesertaService
{
  // Konfigurasi Kantor
  private $officeLat = -6.732644766512938;
  private $officeLng = 108.26252835603626;
  private $allowedRadius = 200; // Meter

  /**
   * Handle Check-In Logic
   */
  public function processCheckIn($lat, $long)
  {
    $user = Auth::user();

    // 1. Cek Radius
    $this->checkRadius($lat, $long);

    // 2. Ambil Lamaran Aktif
    $lamaran = $this->getActiveLamaran($user->id);

    // 3. Cek Double Absen
    if (Presensi::where('lamaran_id', $lamaran->id)->whereDate('tanggal', Carbon::today())->exists()) {
      throw new \Exception('Anda sudah melakukan Check-in hari ini.');
    }

    // 4. Hitung Status Waktu (Terlambat/Tepat)
    $jamMasukStandar = Carbon::parse($lamaran->lowongan->jam_masuk_standar);
    // Bandingkan jam sekarang dengan jam standar (abaikan tanggal)
    $status = Carbon::now()->format('H:i:s') > $jamMasukStandar->format('H:i:s')
      ? 'terlambat' : 'hadir'; // Ubah 'tepat_waktu' jadi 'hadir' sesuai ENUM DB Admin

    // 5. Simpan
    return Presensi::create([
      'lamaran_id' => $lamaran->id,
      'user_id'    => $user->id,
      'tanggal'    => Carbon::today(),
      'jam_masuk'  => Carbon::now()->format('H:i:s'),
      'status'     => $status,
      'lat_masuk'  => $lat,
      'long_masuk' => $long,
    ]);
  }

  /**
   * Handle Check-Out Logic
   */
  public function processCheckOut($lat, $long)
  {
    $user = Auth::user();

    // 1. Cek Radius
    $this->checkRadius($lat, $long);

    // 2. Ambil Lamaran
    $lamaran = $this->getActiveLamaran($user->id);

    // 3. Cek Presensi Masuk Hari Ini
    $presensi = Presensi::where('lamaran_id', $lamaran->id)
      ->whereDate('tanggal', Carbon::today())
      ->whereNotNull('jam_masuk')
      ->first();

    if (!$presensi) {
      throw new \Exception('Anda belum melakukan Check-in hari ini.');
    }

    // 4. Cek Waktu Pulang (Time Barrier)
    $jamPulangStandar = Carbon::parse($lamaran->lowongan->jam_pulang_standar);
    // Buat objek Carbon hari ini dengan jam pulang standar
    $batasWaktu = Carbon::today()->setTimeFrom($jamPulangStandar);

    if (Carbon::now()->lt($batasWaktu)) {
      throw new \Exception('Belum waktunya pulang! Jam pulang: ' . $jamPulangStandar->format('H:i'));
    }

    // 5. Update
    $presensi->update([
      'jam_pulang'  => Carbon::now()->format('H:i:s'),
      'lat_pulang'  => $lat,
      'long_pulang' => $long
    ]);

    return $presensi;
  }

  /**
   * Helper: Validasi Radius Geofencing
   */
  private function checkRadius($lat, $long)
  {
    $distance = $this->calculateDistance($this->officeLat, $this->officeLng, $lat, $long);

    if ($distance > $this->allowedRadius) {
      throw new \Exception('Anda berada di luar jangkauan kantor! Jarak: ' . floor($distance) . 'm.');
    }
  }

  /**
   * Helper: Ambil Lamaran Aktif User
   */
  private function getActiveLamaran($userId)
  {
    $lamaran = Lamaran::with('lowongan')
      ->where('pengguna_id', $userId)
      ->where('status', 'diterima')
      ->first();

    if (!$lamaran) {
      throw new \Exception('Data magang aktif tidak ditemukan.');
    }
    return $lamaran;
  }

  /**
   * Helper: Rumus Haversine
   */
  private function calculateDistance($lat1, $lon1, $lat2, $lon2)
  {
    $earthRadius = 6371000;
    $latDelta = deg2rad($lat2 - $lat1);
    $lonDelta = deg2rad($lon2 - $lon1);

    $a = sin($latDelta / 2) * sin($latDelta / 2) +
      cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
      sin($lonDelta / 2) * sin($lonDelta / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
  }

  /**
   * Ambil Data Riwayat & Statistik
   */
  public function getRiwayat($userId, $startDate, $endDate): array
  {
    // 1. Query Dasar
    $query = Presensi::where('user_id', $userId);

    // 2. Filter Tanggal
    if ($startDate && $endDate) {
      $query->whereBetween('tanggal', [$startDate, $endDate]);
    } else {
      // Default: Bulan ini jika tidak ada filter
      $query->whereMonth('tanggal', Carbon::now()->month)
        ->whereYear('tanggal', Carbon::now()->year);
    }

    // 3. Hitung Statistik (Summary)
    // Kita clone query agar tidak mengganggu query pagination nanti
    $statsQuery = clone $query;

    $summary = [
      'hadir'      => (clone $statsQuery)->where('status', 'hadir')->count(),
      'terlambat'  => (clone $statsQuery)->where('status', 'terlambat')->count(),
      // Gabungkan Sakit & Izin
      'izin_sakit' => (clone $statsQuery)->whereIn('status', ['sakit', 'izin'])->count(),
      'alpha'      => (clone $statsQuery)->where('status', 'alpha')->count(),
    ];

    // 4. Ambil Data Tabel (Pagination)
    // Urutkan dari tanggal terbaru
    $presensi = $query->orderBy('tanggal', 'desc')->paginate(10);

    return [
      'presensi' => $presensi,
      'summary'  => $summary
    ];
  }
}
