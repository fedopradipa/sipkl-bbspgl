<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
  /**
   * Upload surat pengantar
   */
  public function uploadSuratPengantar(UploadedFile $file, int $userId): string
  {
    $filename = 'surat_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();

    $file->storeAs('public/surat_pengantar', $filename);

    return $filename;
  }

  /**
   * Upload laporan akhir
   */
  public function uploadLaporanAkhir(UploadedFile $file, int $userId): string
  {
    $filename = 'laporan_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();

    $file->storeAs('public/laporan_akhir', $filename);

    return $filename;
  }

  /**
   * Delete file
   */
  public function deleteFile(string $path): bool
  {
    if (Storage::exists('public/' . $path)) {
      return Storage::delete('public/' . $path);
    }

    return false;
  }

  /**
   * Get file URL
   */
  public function getFileUrl(string $filename, string $folder = 'surat_pengantar'): string
  {
    return asset('storage/' . $folder . '/' . $filename);
  }
}
