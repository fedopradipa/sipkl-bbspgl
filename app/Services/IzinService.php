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
    return Lamaran::where('pengguna_id', $userId)
      ->where('status', 'diterima')
      ->latest()
      ->first();
  }

  /**
   * Menangani proses penyimpanan izin (Multi-Date) beserta file-nya
   */
  public function createIzin(Lamaran $lamaran, array $data, ?UploadedFile $file = null): PengajuanIzin
  {
    return DB::transaction(function () use ($lamaran, $data, $file) {

      // 1. Handle Upload File Bukti (Jika ada)
      $pathBukti = null;
      if ($file) {
        try {
          // Format nama file: izin_TIMESTAMP_USERID.ext
          $filename = 'izin_' . time() . '_' . $lamaran->pengguna_id . '.' . $file->getClientOriginalExtension();

          // Simpan ke storage/app/public/izin
          $path = $file->storeAs('public/izin', $filename);

          // Ambil path relatif untuk database (hapus 'public/')
          $pathBukti = str_replace('public/', '', $path);
        } catch (\Exception $e) {
          Log::error("Gagal upload bukti izin: " . $e->getMessage());
          throw new \Exception("Gagal mengunggah file bukti. Silakan coba lagi.");
        }
      }

      // 2. PROSES TANGGAL (PENTING!)
      // Data dari Request berupa string: "2025-12-01, 2025-12-03, 2025-12-05"
      // Kita ubah menjadi Array agar tersimpan rapi sebagai JSON di DB

      $rawDates = $data['tanggal_izin'];
      $datesArray = explode(', ', $rawDates); // Pecah berdasarkan koma+spasi

      // Opsional: Urutkan tanggal agar rapi (ascending)
      sort($datesArray);

      // 3. Simpan Data ke Database
      return PengajuanIzin::create([
        'lamaran_id'   => $lamaran->id,
        'jenis_izin'   => $data['jenis_izin'],

        // Ganti tgl_mulai/selesai dengan tanggal_izin (Array)
        // Pastikan di Model PengajuanIzin sudah ada protected $casts = ['tanggal_izin' => 'array'];
        'tanggal_izin' => $datesArray,

        'alasan'       => $data['alasan'],
        'bukti_foto'   => $pathBukti,
        'status'       => 'pending'
      ]);
    });
  }
}
