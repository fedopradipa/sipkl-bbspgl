<?php

namespace App\Services\Admin;

use App\Models\Presensi;
use App\Models\Lamaran;
use Carbon\Carbon;

class PresensiAdminService
{
  /**
   * Handle Create Manual by Admin
   */
  public function storeManual(array $data)
  {
    // 1. Cek Duplikasi
    $exists = Presensi::where('lamaran_id', $data['lamaran_id'])
      ->whereDate('tanggal', $data['tanggal'])
      ->exists();

    if ($exists) {
      throw new \Exception('Data presensi untuk peserta ini pada tanggal tersebut sudah ada.');
    }

    // 2. Ambil Lamaran & Lowongan (Eager Load untuk performa)
    $lamaran = Lamaran::with('lowongan')->findOrFail($data['lamaran_id']);

    // 3. Ambil Jam Standar dari Lowongan (Default 08:00 jika kosong)
    $jamStandar = $lamaran->lowongan->jam_masuk_standar ?? '08:00:00';

    // 4. Proses Status & Jam (Kirim Jam Standar ke Helper)
    $processed = $this->processStatusAndJam(
      $data['status'],
      $data['jam_masuk'],
      $data['jam_pulang'] ?? null,
      $data['catatan_harian'] ?? 'Diinput manual oleh Admin',
      $jamStandar // <--- Parameter Baru
    );
    // 4. Simpan ke Database
    return Presensi::create([
      'lamaran_id'     => $lamaran->id,
      'user_id'        => $lamaran->pengguna_id, // <--- INI PENTING (Fix Error 1364)
      'tanggal'        => $data['tanggal'],
      'jam_masuk'      => $processed['jam_masuk'],
      'jam_pulang'     => $processed['jam_pulang'],
      'status'         => $processed['status'],
      'catatan_harian' => $processed['catatan_harian'],
      'is_auto_closed' => false,
    ]);
  }

  /**
   * Handle Update Manual by Admin
   */
  public function updateManual(int $id, array $data)
  {
    // 1. Ambil Presensi beserta relasi ke Lowongan
    $presensi = Presensi::with('lamaran.lowongan')->findOrFail($id);

    // 2. Ambil Jam Standar dari Lowongan terkait
    $jamStandar = $presensi->lamaran->lowongan->jam_masuk_standar ?? '08:00:00';

    // 3. Proses Status & Jam (Kirim Jam Standar)
    $processed = $this->processStatusAndJam(
      $data['status'],
      $data['jam_masuk'],
      $data['jam_pulang'] ?? null,
      $data['catatan_harian'] ?? null,
      $jamStandar // <--- Parameter Baru
    );
    $presensi->update([
      'status'     => $processed['status'],
      'jam_masuk'  => $processed['jam_masuk'],
      'jam_pulang' => $processed['jam_pulang'],
      'catatan_harian' => $processed['catatan_harian'],

    ]);

    return $presensi;
  }

  /**
   * Helper: Logika Penentuan Terlambat & Nullify Jam
   */
  private function processStatusAndJam($status, $jamMasuk, $jamPulang, $catatan, $jamStandar)
  {
    // 1. Logika Pembersihan Jam jika tidak Hadir
    if (!in_array($status, ['hadir', 'terlambat'])) {

      // Cek khusus jika Alpha, set catatan default jika kosong
      if ($status === 'alpha' && empty($catatan)) {
        $catatan = 'Tanpa Keterangan (Alpha)';
      }

      return [
        'status'    => $status,
        'jam_masuk' => null, // PAKSA NULL (Database Nullable)
        'jam_pulang' => null, // PAKSA NULL
        'catatan_harian' => $catatan
      ];
    }

    // 2. LOGIKA TERLAMBAT DINAMIS
    // Hitung batas toleransi (Jam Standar + 10 Menit)
    $waktuBatas = Carbon::parse($jamStandar)->addMinutes(10);
    $waktuMasuk = Carbon::parse($jamMasuk);

    // Cek apakah input admin melebihi batas toleransi?
    if ($jamMasuk && $waktuMasuk->gt($waktuBatas)) {
      $status = 'terlambat';
    } elseif ($jamMasuk) {
      // Jika diedit jadi lebih pagi dari batas, kembalikan ke hadir
      $status = 'hadir';
    }

    return [
      'status'         => $status,
      'jam_masuk'      => $jamMasuk,
      'jam_pulang'     => $jamPulang,
      'catatan_harian' => $catatan
    ];
  }
}
