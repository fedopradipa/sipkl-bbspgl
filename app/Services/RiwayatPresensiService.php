<?php

namespace App\Services;

use App\Models\Lamaran;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class RiwayatPresensiService
{
  public function getLamaranTerbaruUntukRiwayat(int $userId): ?Lamaran
  {
    return Lamaran::where('pengguna_id', $userId)
      ->whereIn('status', ['diterima', 'selesai'])
      ->latest()
      ->first();
  }

  public function emptyPaginator(int $perPage = 10): LengthAwarePaginator
  {
    return new LengthAwarePaginator([], 0, $perPage);
  }

  /**
   * Return array: ['presensi' => paginator, 'summary' => [], 'mulai' => ..., 'sampai' => ...]
   */
  public function buildRiwayatData(int $lamaranId, ?string $mulai, ?string $sampai, int $perPage = 10): array
  {
    $mulai = $mulai ?: Carbon::now()->startOfMonth()->toDateString();
    $sampai = $sampai ?: Carbon::now()->endOfMonth()->toDateString();

    $query = Presensi::where('lamaran_id', $lamaranId);

    if ($mulai && $sampai) {
      $query->whereBetween('tanggal', [$mulai, $sampai]);
    }

    $statsQuery = clone $query;

    $summary = [
      'hadir'      => (clone $statsQuery)->where('status', 'hadir')->count(),
      'terlambat'  => (clone $statsQuery)->where('status', 'terlambat')->count(),
      'izin_sakit' => (clone $statsQuery)->whereIn('status', ['sakit', 'izin'])->count(),
      'alpha'      => (clone $statsQuery)->where('status', 'alpha')->count(),
    ];

    $presensi = $query->orderBy('tanggal', 'desc')->paginate($perPage);

    return compact('presensi', 'summary', 'mulai', 'sampai');
  }
}
