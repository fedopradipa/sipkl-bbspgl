<?php

namespace App\Services\Admin;

use App\Models\Lamaran;
use App\Models\Lowongan;
use Carbon\Carbon;

class RekapPresensiService
{
  /**
   * Generate Data Matriks Rekapitulasi
   */
  public function getRekapMatrix($bulan, $tahun, $lowonganId = null): array
  {
    // 1. Generate Header Tanggal
    $dates = $this->generateDateHeader($bulan, $tahun);

    // Tentukan Range Tanggal Query
    $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
    $endDate   = $startDate->copy()->endOfMonth()->endOfDay();

    // 2. Query Peserta (Status Diterima)
    // Eager Loading Presensi HANYA di range tanggal tersebut
    $query = Lamaran::with(['pengguna', 'lowongan', 'presensi' => function ($q) use ($startDate, $endDate) {
      $q->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
    }])
      ->where('status', 'diterima');

    if ($lowonganId) {
      $query->where('lowongan_id', $lowonganId);
    }

    $pesertas = $query->get();

    // 3. Mapping Data per Peserta
    $rekapData = $pesertas->map(function ($peserta) use ($dates) {
      return $this->processPesertaRow($peserta, $dates);
    });

    // 4. Data Pendukung
    $divisiList = Lowongan::select('id', 'divisi')->distinct()->get();

    return [
      'header_dates' => $dates,
      'data'         => $rekapData,
      'divisi_list'  => $divisiList,
      'bulan_nama'   => Carbon::createFromDate($tahun, $bulan)->locale('id')->isoFormat('MMMM'),
      'tahun'        => $tahun,
      'bulan'        => $bulan
    ];
  }

  /**
   * Helper: Membuat Array Tanggal Header (1-31)
   */
  private function generateDateHeader($bulan, $tahun): array
  {
    $daysInMonth = Carbon::createFromDate($tahun, $bulan)->daysInMonth;
    $dates = [];

    for ($i = 1; $i <= $daysInMonth; $i++) {
      $date = Carbon::createFromDate($tahun, $bulan, $i);
      // Key format 'Y-m-d' agar mudah dicocokkan
      $dates[$date->format('Y-m-d')] = [
        'tgl'        => $i,
        'hari'       => $date->locale('id')->isoFormat('ddd'), // Sen, Sel (Singkatan)
        'nama_hari'  => $date->locale('id')->isoFormat('dddd'), // Senin, Selasa (Lengkap - PENTING utk Cek Jadwal)
        'is_weekend' => $date->isWeekend(),
      ];
    }

    return $dates;
  }

  /**
   * Helper: Memproses Logic Baris per Peserta
   */
  private function processPesertaRow($peserta, $dates)
  {
    // 1. TRANSFORMASI DATA PRESENSI
    $logs = $peserta->presensi->mapWithKeys(function ($item) {
      return [Carbon::parse($item->tanggal)->format('Y-m-d') => $item];
    });

    // 2. AMBIL JADWAL KERJA
    $jadwalKerja = $peserta->hari_kerja_resmi;
    if (is_string($jadwalKerja)) {
      $jadwalKerja = json_decode($jadwalKerja, true) ?? [];
    }
    if (empty($jadwalKerja)) {
      $jadwalKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    }

    $row = [
      'nama'    => $peserta->pengguna->name ?? 'Tanpa Nama',
      'divisi'  => $peserta->lowongan->divisi ?? '-',
      'harian'  => [],
      'total'   => [
        'hadir' => 0,
        'sakit' => 0,
        'izin' => 0,
        'alpha' => 0,
        'terlambat' => 0
      ]
    ];

    foreach ($dates as $ymd => $info) {
      $carbonDate = Carbon::createFromFormat('Y-m-d', $ymd);

      $status = '-';
      $jamMasuk = null;
      $jamPulang = null;

      $startMagang = Carbon::parse($peserta->tgl_mulai_resmi)->startOfDay();
      $endMagang   = Carbon::parse($peserta->tgl_selesai_resmi)->endOfDay();

      $isHariKerja = in_array($info['nama_hari'], $jadwalKerja);

      if ($carbonDate->lt($startMagang) || $carbonDate->gt($endMagang)) {
        $status = 'off_period';
      } elseif ($logs->has($ymd)) {
        $data = $logs->get($ymd);

        // [PERBAIKAN 1] Ubah ke huruf kecil agar pencocokan di View aman
        $status = strtolower($data->status);

        // [PERBAIKAN 2] Format Jam langsung disini agar aman dari null/object
        $jamMasuk = $data->jam_masuk ? Carbon::parse($data->jam_masuk)->format('H:i') : null;
        $jamPulang = $data->jam_pulang ? Carbon::parse($data->jam_pulang)->format('H:i') : null;

        if (array_key_exists($status, $row['total'])) {
          $row['total'][$status]++;
        }
      } else {
        if (!$isHariKerja) {
          $status = 'libur';
        } elseif ($carbonDate->isFuture()) {
          $status = 'belum';
        } else {
          $status = 'alpha';
          $row['total']['alpha']++;
        }
      }

      $row['harian'][$ymd] = [
        'status'     => $status,
        'jam_masuk'  => $jamMasuk,
        'jam_pulang' => $jamPulang,
      ];
    }

    return $row;
  }
}
