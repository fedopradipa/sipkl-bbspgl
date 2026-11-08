<?php

namespace App\Services;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RekapPresensiService
{
  public function getRekapMatrix(int $bulan, int $tahun, ?int $lowonganId): array
  {
    $start = Carbon::create($tahun, $bulan, 1)->startOfDay();
    $end   = Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();
    $today = Carbon::today();

    $hariIndo = [
      'Sunday' => 'Minggu',
      'Monday' => 'Senin',
      'Tuesday' => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday' => 'Kamis',
      'Friday' => 'Jumat',
      'Saturday' => 'Sabtu'
    ];

    // 1) Header dates
    $dates = [];
    $cursor = $start->copy();
    while ($cursor->lte($end)) {
      $ymd = $cursor->format('Y-m-d');
      $dates[$ymd] = [
        'tgl'  => $cursor->format('d'),
        'hari' => $hariIndo[$cursor->format('l')] ?? $cursor->format('l'),
      ];
      $cursor->addDay();
    }

    // 2) Dropdown posisi
    $posisiList = Lowongan::select('id', 'posisi')->orderBy('posisi')->get();

    // 3) Ambil lamaran aktif yang overlap
    $lamaranQuery = Lamaran::with(['pengguna.profile', 'lowongan'])
      ->where('status', 'diterima')
      ->whereDate('tgl_mulai_resmi', '<=', $end->toDateString())
      ->whereDate('tgl_selesai_resmi', '>=', $start->toDateString());

    if ($lowonganId) {
      $lamaranQuery->where('lowongan_id', $lowonganId);
    }

    $lamarans = $lamaranQuery->get();
    $lamaranIds = $lamarans->pluck('id')->all();

    // 4) Presensi map
    $presensi = Presensi::whereIn('lamaran_id', $lamaranIds)
      ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
      ->get();

    $presensiMap = [];
    foreach ($presensi as $p) {
      $key = $p->lamaran_id . '|' . Carbon::parse($p->tanggal)->format('Y-m-d');
      $presensiMap[$key] = $p;
    }

    // 5) Izin disetujui map
    $izinApproved = DB::table('pengajuan_izins')
      ->whereIn('lamaran_id', $lamaranIds)
      ->where('status', 'disetujui')
      ->get();

    $izinMap = [];
    foreach ($izinApproved as $row) {
      $tglList = json_decode($row->tanggal_izin ?? '[]', true);
      if (!is_array($tglList)) $tglList = [];

      foreach ($tglList as $tgl) {
        if ($tgl >= $start->toDateString() && $tgl <= $end->toDateString()) {
          $izinMap[$row->lamaran_id . '|' . $tgl] = $row->jenis_izin; // izin/sakit
        }
      }
    }

    // 6) Bangun rekapData
    $rekapData = [];

    foreach ($lamarans as $lamaran) {
      $jadwal = $lamaran->hari_kerja_resmi ?? [];
      if (!is_array($jadwal)) $jadwal = [];

      $mulai  = Carbon::parse($lamaran->tgl_mulai_resmi);
      $selesai = Carbon::parse($lamaran->tgl_selesai_resmi);

      $row = [
        'nama'   => $lamaran->pengguna->name,
        'posisi' => $lamaran->lowongan->posisi ?? '-',
        'harian' => [],
        'total'  => [
          'hadir' => 0,
          'terlambat' => 0,
          'sakit' => 0,
          'izin' => 0,
          'alpha' => 0,
        ],
      ];

      foreach ($dates as $ymd => $info) {
        $d   = Carbon::parse($ymd);
        $key = $lamaran->id . '|' . $ymd;

        $log = [
          'status' => '-',
          'jam_masuk' => null,
        ];

        // a) di luar periode
        if ($d->lt($mulai) || $d->gt($selesai)) {
          $log['status'] = 'off_period';
          $row['harian'][$ymd] = $log;
          continue;
        }

        // b) bukan hari kerja
        $hari = $hariIndo[$d->format('l')] ?? $d->format('l');
        if (!in_array($hari, $jadwal)) {
          $log['status'] = 'libur';
          $row['harian'][$ymd] = $log;
          continue;
        }

        // c) presensi ada
        if (isset($presensiMap[$key])) {
          $p = $presensiMap[$key];
          $log['status'] = $p->status;
          $log['jam_masuk'] = $p->jam_masuk;

          if (isset($row['total'][$p->status])) {
            $row['total'][$p->status]++;
          }

          $row['harian'][$ymd] = $log;
          continue;
        }

        // d) izin disetujui
        if (isset($izinMap[$key])) {
          $st = $izinMap[$key];
          $log['status'] = $st;
          $row['total'][$st]++;
          $row['harian'][$ymd] = $log;
          continue;
        }

        // e) Tentukan Belum Absen / Alfa untuk tanggal tanpa catatan.
        // Pada hari ini, peserta tidak dihitung Alfa sebelum jam tutup presensi.
        // Jika jam tutup tidak tersedia, jangan menyimpulkan Alfa untuk hari ini.
        $belumTutupHariIni = false;
        if ($d->isSameDay($today)) {
          $jamTutup = $lamaran->lowongan?->jam_tutup_absen;
          $belumTutupHariIni = !$jamTutup || Carbon::now()->lt(
            $today->copy()->setTimeFrom(Carbon::parse($jamTutup))
          );
        }

        if ($d->gt($today) || $belumTutupHariIni) {
          $log['status'] = 'belum';
        } else {
          $log['status'] = 'alpha';
          $row['total']['alpha']++;
        }

        $row['harian'][$ymd] = $log;
      }

      $rekapData[] = $row;
    }

    return [
      'header_dates' => $dates,
      'data'         => $rekapData,
      'posisi_list'  => $posisiList,
      'bulan_nama'   => Carbon::create($tahun, $bulan, 1)->translatedFormat('F'),
      'tahun'        => $tahun,
    ];
  }

  public function mapStatusToCode(string $status): string
  {
    return match ($status) {
      'hadir'      => 'H',
      'terlambat'  => 'T',
      'sakit'      => 'S',
      'izin'       => 'I',
      'alpha'      => 'A',
      default      => '',
    };
  }
}
