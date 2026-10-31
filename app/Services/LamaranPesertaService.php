<?php

namespace App\Services;

use App\Models\Lamaran;
use App\Models\Lowongan;
use Illuminate\Support\Facades\DB;

class LamaranPesertaService
{
  /**
   * Cek lamaran aktif (pending/diterima)
   */
  public function hasActiveApplication(int $userId): bool
  {
    return Lamaran::where('pengguna_id', $userId)
      ->whereIn('status', ['pending', 'diterima'])
      ->exists();
  }

  /**
   * Simpan lamaran baru (upload + prepare data + create)
   */
  public function storeLamaran(int $userId, Lowongan $lowongan, array $validated, $uploadedFile): void
  {
    DB::transaction(function () use ($userId, $lowongan, $validated, $uploadedFile) {
      $filename = null;

      if ($uploadedFile) {
        $filename = $this->uploadSuratPengantar($uploadedFile, $userId);
      }

      $data = $this->prepareData($validated, $lowongan, $filename, $userId);

      Lamaran::create($data);
    });
  }

  private function uploadSuratPengantar($file, int $userId): string
  {
    $filename = 'surat_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
    $file->storeAs('public/surat_pengantar', $filename);
    return $filename;
  }

  private function prepareData(array $input, Lowongan $lowongan, ?string $filename, int $userId): array
  {
    $kamusHari = [
      'Monday'    => 'Senin',
      'Tuesday'   => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday'  => 'Kamis',
      'Friday'    => 'Jumat',
      'Saturday'  => 'Sabtu',
      'Sunday'    => 'Minggu'
    ];

    if ($lowongan->is_fulltime) {
      $hariKerja = $lowongan->hari_kerja ?? ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"];
      $alasan    = "Mengikuti aturan jadwal terikat (Fulltime) dari lowongan";
    } else {
      $inputHari = $input['hari_kerja'] ?? [];
      $hariKerja = array_map(fn($h) => $kamusHari[$h] ?? $h, $inputHari);
      $alasan    = $input['alasan_hari_kerja'] ?? '-';
    }

    return [
      'pengguna_id'          => $userId,
      'lowongan_id'          => $lowongan->id,
      'status'               => 'pending',
      'tgl_mulai_usulan'     => $input['tgl_mulai_usulan'],
      'tgl_selesai_usulan'   => $input['tgl_selesai_usulan'],
      'hari_kerja_usulan'    => $hariKerja,
      'alasan_hari_kerja'    => $alasan,
      'file_surat_pengantar' => $filename,
    ];
  }
}
