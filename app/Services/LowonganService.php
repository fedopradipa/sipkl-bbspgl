<?php

namespace App\Services;

use App\Models\Lowongan;

class LowonganService
{
  /**
   * Handle pembuatan lowongan baru
   */
  public function createLowongan(array $data): Lowongan
  {
    // Logika Bisnis Tambahan (Jika Ada)
    // Contoh: Jika is_fulltime FALSE, pastikan working_days NULL (Clean up data)
    if (!$data['is_fulltime']) {
      $data['working_days'] = null;
    }

    return Lowongan::create($data);
  }

  /**
   * Handle update lowongan
   */
  public function updateLowongan(Lowongan $lowongan, array $data): bool
  {
    // Logic yang sama: bersihkan working_days jika diganti jadi fleksibel
    if (!$data['is_fulltime']) {
      $data['working_days'] = null;
    }

    return $lowongan->update($data);
  }
}
