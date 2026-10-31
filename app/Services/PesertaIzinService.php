<?php

namespace App\Services;

use App\Models\Lamaran;
use App\Models\PengajuanIzin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PesertaIzinService
{
  public function getLatestLamaranForHistory(int $userId): ?Lamaran
  {
    return Lamaran::where('pengguna_id', $userId)
      ->whereIn('status', ['diterima', 'selesai'])
      ->latest()
      ->first();
  }

  public function getActiveLamaran(int $userId): ?Lamaran
  {
    return Lamaran::where('pengguna_id', $userId)
      ->where('status', 'diterima')
      ->latest()
      ->first();
  }

  public function createPengajuanIzin(Lamaran $lamaran, array $payload, ?UploadedFile $file): void
  {
    DB::transaction(function () use ($lamaran, $payload, $file) {
      $pathBukti = null;

      if ($file) {
        $filename = 'izin_' . time() . '_' . $lamaran->pengguna_id . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/izin', $filename);
        $pathBukti = str_replace('public/', '', $path);
      }

      $datesArray = explode(', ', $payload['tanggal_izin']);
      sort($datesArray);

      PengajuanIzin::create([
        'lamaran_id'     => $lamaran->id,
        'jenis_izin'     => $payload['jenis_izin'],
        'tanggal_izin'   => $datesArray,
        'alasan'         => $payload['alasan'],
        'bukti_lampiran' => $pathBukti,
        'status'         => 'pending',
      ]);
    });
  }

  public function updatePengajuanIzin(PengajuanIzin $izin, array $payload, ?UploadedFile $file): void
  {
    DB::transaction(function () use ($izin, $payload, $file) {
      $datesArray = explode(', ', $payload['tanggal_izin']);
      sort($datesArray);

      $dataToUpdate = [
        'jenis_izin'   => $payload['jenis_izin'],
        'tanggal_izin' => $datesArray,
        'alasan'       => $payload['alasan'],
      ];

      if ($file) {
        // hapus file lama
        if ($izin->bukti_lampiran && Storage::exists('public/' . $izin->bukti_lampiran)) {
          Storage::delete('public/' . $izin->bukti_lampiran);
        }

        $filename = 'izin_' . time() . '_' . $izin->lamaran->pengguna_id . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/izin', $filename);
        $dataToUpdate['bukti_lampiran'] = str_replace('public/', '', $path);
      }

      $izin->update($dataToUpdate);
    });
  }
}
