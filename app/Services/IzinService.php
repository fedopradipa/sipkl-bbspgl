<?php

namespace App\Services;

use App\Models\PengajuanIzin;
use App\Models\Lamaran;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IzinService
{
  /**
   * Mendapatkan Data Lamaran Peserta yang VALID untuk Izin.
   * Syarat: Status harus 'diterima'.
   */
  public function getActiveLamaran(int $userId): ?Lamaran
  {
    // LOGIKA KETAT: Hanya ambil yang statusnya 'diterima'
    // Peserta 'pending' atau 'berhenti' TIDAK BOLEH mengajukan izin.
    return Lamaran::where('pengguna_id', $userId)
      ->where('status', 'diterima')
      ->latest()
      ->first();
  }

  /**
   * Menangani proses penyimpanan izin beserta file-nya
   */
  public function createIzin(Lamaran $lamaran, array $data, ?UploadedFile $file = null): PengajuanIzin
  {
    return DB::transaction(function () use ($lamaran, $data, $file) {

      $pathBukti = null;

      // 1. Handle Upload File
      if ($file) {
        try {
          $filename = 'izin_' . time() . '_' . $lamaran->pengguna_id . '.' . $file->getClientOriginalExtension();
          // Simpan ke storage/app/public/izin
          $path = $file->storeAs('public/izin', $filename);

          // Ambil nama file saja untuk disimpan di DB
          $pathBukti = str_replace('public/', '', $path);
        } catch (\Exception $e) {
          Log::error("Gagal upload bukti izin: " . $e->getMessage());
          throw new \Exception("Gagal mengunggah file bukti.");
        }
      }

      // 2. Simpan Data ke Database
      return PengajuanIzin::create([
        'lamaran_id'  => $lamaran->id,
        'jenis_izin'  => $data['jenis_izin'],
        'tgl_mulai'   => $data['tgl_mulai'],
        'tgl_selesai' => $data['tgl_selesai'],
        'alasan'      => $data['alasan'],
        'bukti_foto'  => $pathBukti, // Bisa null jika tidak upload
        'status'      => 'pending'   // Default status pengajuan
      ]);
    });
  }
}
