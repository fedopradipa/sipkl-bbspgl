<?php

namespace App\Services;

use App\Models\Lamaran;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use Carbon\Carbon;

class PesertaPresensiService
{
  public function getActiveLamaranWithLowongan(int $userId): Lamaran
  {
    return Lamaran::with('lowongan')
      ->where('pengguna_id', $userId)
      ->where('status', 'diterima')
      ->firstOrFail();
  }

  /**
   * Ambil izin/sakit yang disetujui untuk hari ini (jika ada).
   */
  public function getIzinHariIni(int $lamaranId): ?PengajuanIzin
  {
    $hariIniString = Carbon::today()->format('Y-m-d');

    return PengajuanIzin::where('lamaran_id', $lamaranId)
      ->where('status', 'disetujui')
      ->whereJsonContains('tanggal_izin', $hariIniString)
      ->first();
  }

  /**
   * Cek apakah hari ini termasuk hari kerja peserta.
   */
  public function isHariKerja(Lamaran $lamaran): bool
  {
    $hariIndo = [
      'Sunday' => 'Minggu',
      'Monday' => 'Senin',
      'Tuesday' => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday' => 'Kamis',
      'Friday' => 'Jumat',
      'Saturday' => 'Sabtu'
    ];

    $namaHariIni = $hariIndo[Carbon::now()->format('l')];

    $workingDays = $lamaran->hari_kerja_resmi ?? $lamaran->lowongan->hari_kerja ?? [];
    return is_array($workingDays) && in_array($namaHariIni, $workingDays);
  }

  /**
   * Ambil presensi hari ini (jika ada).
   */
  public function getPresensiHariIni(int $lamaranId): ?Presensi
  {
    return Presensi::where('lamaran_id', $lamaranId)
      ->whereDate('tanggal', Carbon::today())
      ->first();
  }

  /**
   * Proses check-in. Return pesan sukses.
   */
  public function checkIn(int $userId): string
  {
    $lamaran  = $this->getActiveLamaranWithLowongan($userId);
    $lowongan = $lamaran->lowongan;

    $now = Carbon::now();
    $jamSekarang = $now->format('H:i:s');

    // 1) Cek double absen
    if (Presensi::where('lamaran_id', $lamaran->id)->whereDate('tanggal', Carbon::today())->exists()) {
      throw new \Exception('Anda sudah melakukan Check-in hari ini.');
    }

    // 2) Validasi jam buka/tutup absen
    $jamBuka  = $lowongan->jam_buka_absen;   // contoh: 07:00:00
    $jamTutup = $lowongan->jam_tutup_absen;  // contoh: 09:00:00

    if ($jamSekarang < $jamBuka) {
      throw new \Exception("Absen belum dibuka. Silakan tunggu hingga pukul $jamBuka.");
    }

    if ($jamSekarang > $jamTutup) {
      throw new \Exception("Absen masuk sudah ditutup pada pukul $jamTutup. Silakan hubungi pembimbing.");
    }

    // 3) Hitung status hadir / terlambat
    $jamMasukStandar = Carbon::parse($lowongan->jam_masuk_standar);

    // fallback 0 jika kolom toleransi_keterlambatan tidak ada
    $toleransiMenit = (int) ($lowongan->toleransi_keterlambatan ?? 0);

    $batasToleransi = $jamMasukStandar->copy()->addMinutes($toleransiMenit)->format('H:i:s');

    if ($jamSekarang <= $batasToleransi) {
      $status = 'hadir';
      $pesan = 'Berhasil Check-in Tepat Waktu!';
    } else {
      $status = 'terlambat';
      $pesan = 'Berhasil Check-in, namun Terlambat.';
    }

    // 4) Simpan presensi
    Presensi::create([
      'lamaran_id'  => $lamaran->id,
      'pengguna_id' => $userId,
      'tanggal'     => $now->toDateString(),
      'jam_masuk'   => $jamSekarang,
      'status'      => $status,
    ]);

    return $pesan;
  }
}
