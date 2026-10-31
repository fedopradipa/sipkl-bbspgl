<?php

namespace App\Services;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PresensiAdminService
{
  public function getHariIndoFromTanggal(string $tanggal): string
  {
    $mapHari = [
      'Sunday'    => 'Minggu',
      'Monday'    => 'Senin',
      'Tuesday'   => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday'  => 'Kamis',
      'Friday'    => 'Jumat',
      'Saturday'  => 'Sabtu'
    ];

    return $mapHari[Carbon::parse($tanggal)->format('l')] ?? Carbon::parse($tanggal)->format('l');
  }

  public function getPesertaWajibHadir(string $tanggal, ?int $lowonganId, ?string $keyword): Collection
  {
    $hariIni = $this->getHariIndoFromTanggal($tanggal);

    $query = Lamaran::with([
      'pengguna',
      'lowongan',
      'presensi' => function ($q) use ($tanggal) {
        $q->whereDate('tanggal', $tanggal);
      }
    ])
      ->where('status', 'diterima')
      ->whereDate('tgl_mulai_resmi', '<=', $tanggal)
      ->whereDate('tgl_selesai_resmi', '>=', $tanggal)
      ->whereJsonContains('hari_kerja_resmi', $hariIni);

    if ($lowonganId) {
      $query->where('lowongan_id', $lowonganId);
    }

    if ($keyword) {
      $query->whereHas('pengguna', function ($q) use ($keyword) {
        $q->where('name', 'like', "%{$keyword}%");
      });
    }

    return $query->get();
  }

  public function buildFinalData(Collection $pesertaWajibHadir, string $tanggal): Collection
  {
    return $pesertaWajibHadir->map(function ($item) use ($tanggal) {
      $absen = $item->presensi->first();

      if ($absen) {
        $status     = $absen->status;
        $jamMasuk   = $absen->jam_masuk;
        $catatan    = $absen->catatan_harian;
        $presensiId = $absen->id;
        $isAuto     = $absen->is_auto_closed;
      } else {
        $isHariLewat = Carbon::now()->gt(Carbon::parse($tanggal)->endOfDay());
        $status     = $isHariLewat ? 'alpha' : 'belum_absen';
        $jamMasuk   = null;
        $catatan    = '-';
        $presensiId = null;
        $isAuto     = false;
      }

      return (object) [
        'id'             => $presensiId,
        'lamaran'        => $item,
        'tanggal'        => Carbon::parse($tanggal),
        'jam_masuk'      => $jamMasuk,
        'status'         => $status,
        'catatan_harian' => $catatan,
        'is_auto_closed' => $isAuto,
      ];
    });
  }

  public function filterByStatus(Collection $finalData, ?string $statusFilter): Collection
  {
    if ($statusFilter) {
      return $finalData->where('status', $statusFilter);
    }

    return $finalData;
  }

  public function buildRingkasan(Collection $finalData): array
  {
    return [
      'hadir'      => $finalData->where('status', 'hadir')->count(),
      'terlambat'  => $finalData->where('status', 'terlambat')->count(),
      'sakit_izin' => $finalData->whereIn('status', ['sakit', 'izin'])->count(),
      'alpha'      => $finalData->whereIn('status', ['alpha', 'belum_absen'])->count(),
    ];
  }

  public function getPosisiList()
  {
    return Lowongan::select('id', 'posisi')->orderBy('posisi')->get();
  }

  public function getPesertaAktifForDropdown(string $tanggal)
  {
    return Lamaran::with(['pengguna', 'lowongan'])
      ->where('status', 'diterima')
      ->whereDate('tgl_mulai_resmi', '<=', $tanggal)
      ->whereDate('tgl_selesai_resmi', '>=', $tanggal)
      ->get();
  }

  public function storeAdmin(array $validated): void
  {
    $lamaran = Lamaran::with('pengguna')->findOrFail($validated['lamaran_id']);

    DB::transaction(function () use ($validated, $lamaran) {
      Presensi::updateOrCreate(
        [
          'lamaran_id' => $lamaran->id,
          'tanggal'    => $validated['tanggal'],
        ],
        [
          'pengguna_id'     => $lamaran->pengguna_id,
          'jam_masuk'       => $validated['jam_masuk'] ?? null,
          'status'          => $validated['status'],
          'catatan_harian'  => $validated['catatan_harian'] ?? null,
          'is_auto_closed'  => false,
        ]
      );
    });
  }

  public function updateAdmin(int $id, array $validated): void
  {
    $presensi = Presensi::findOrFail($id);

    DB::transaction(function () use ($presensi, $validated) {
      $presensi->update([
        'status'         => $validated['status'],
        'jam_masuk'      => $validated['jam_masuk'] ?? null,
        'catatan_harian' => $validated['catatan_harian'] ?? null,
      ]);
    });
  }
}
