<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod; // Pastikan ini di-use
use App\Models\Presensi;
use App\Models\User;
use App\Models\Lamaran;
use App\Models\Lowongan;

class ReportController extends Controller
{
  /**
   * Menampilkan halaman filter dan preview Rekap Absensi (Sudah Diperbaiki).
   */
  public function index(Request $request)
  {
    // 1. Definisikan Rentang Filter Admin
    $filterStart = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
    $filterEnd = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

    if ($filterStart->greaterThan($filterEnd)) {
      $filterStart = $filterEnd;
    }

    // Total Hari Kerja Global (Hanya untuk header display)
    $totalEffectiveDaysGlobal = $this->calculateWorkingDaysGlobal($filterStart, $filterEnd);

    // 2. Query Data Absensi (Diperlukan di awal untuk GROUP BY)
    $rekapAbsensi = Presensi::select(
      'presensis.user_id',

      DB::raw('(SELECT users.name FROM users WHERE users.id = presensis.user_id) as nama_peserta'),
      DB::raw('(
              SELECT w.divisi 
              FROM lamarans l
              JOIN lowongans w ON w.id = l.lowongan_id  
              WHERE l.pengguna_id = presensis.user_id 
              LIMIT 1
          ) as divisi_peserta')
    )
      ->whereBetween('tanggal', [$filterStart->toDateString(), $filterEnd->toDateString()])
      ->groupBy('presensis.user_id')
      ->get();


    // 3. Ambil Data Lamaran (Jadwal dan Periode Resmi) untuk Semua Peserta
    $userIds = $rekapAbsensi->pluck('user_id');

    // Ambil tgl_mulai_resmi dan tgl_selesai_resmi
    $dataLamaran = Lamaran::whereIn('pengguna_id', $userIds)
      ->select('pengguna_id', 'hari_kerja_resmi', 'tgl_mulai_resmi', 'tgl_selesai_resmi')
      ->get()
      ->keyBy('pengguna_id');

    // 4. Proses Perhitungan Berdasarkan Irisan Periode
    $finalRekap = [];

    foreach ($rekapAbsensi as $rekap) {
      $lamaran = $dataLamaran->get($rekap->user_id);

      // Jika lamaran atau periode resmi tidak ada, lewati
      if (!$lamaran || !$lamaran->tgl_mulai_resmi || !$lamaran->tgl_selesai_resmi) {
        continue;
      }

      $jadwalHari = $lamaran->hari_kerja_resmi ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

      // --- LOGIKA IRISAN KRUSIAL ---
      $irisanStart = $lamaran->tgl_mulai_resmi->greaterThan($filterStart) ? $lamaran->tgl_mulai_resmi : $filterStart;
      $irisanEnd = $lamaran->tgl_selesai_resmi->lessThan($filterEnd) ? $lamaran->tgl_selesai_resmi : $filterEnd;

      // Jika periode irisan tidak valid, set 0
      if ($irisanStart->greaterThan($irisanEnd)) {
        $rekap->total_days = 0;
        $rekap->persentase = 0;
        $rekap->total_hadir_terlambat = $rekap->total_terlambat = $rekap->total_sakit = $rekap->total_izin = $rekap->total_alpha = 0;
        $finalRekap[] = $rekap;
        continue;
      }

      // Hitung Hari Efektif Individu dalam Rentang Irisan
      $totalIndividualDays = $this->calculateIndividualWorkingDays($irisanStart, $irisanEnd, $jadwalHari);
      $rekap->total_days = $totalIndividualDays;

      // Ambil Data Absensi HANYA dalam rentang IRISAN
      $absensi = Presensi::where('user_id', $rekap->user_id)
        ->whereBetween('tanggal', [$irisanStart->toDateString(), $irisanEnd->toDateString()])
        ->select(
          DB::raw('COUNT(CASE WHEN presensis.status IN ("hadir", "terlambat") THEN 1 END) as total_hadir_terlambat'),
          DB::raw('COUNT(CASE WHEN presensis.status = "terlambat" THEN 1 END) as total_terlambat'),
          DB::raw('COUNT(CASE WHEN presensis.status = "sakit" THEN 1 END) as total_sakit'),
          DB::raw('COUNT(CASE WHEN presensis.status = "izin" THEN 1 END) as total_izin'),
          DB::raw('COUNT(CASE WHEN presensis.status = "alpha" THEN 1 END) as total_alpha')
        )
        ->first();

      // Salin hasil absensi ke objek rekap
      $rekap->total_hadir_terlambat = $absensi->total_hadir_terlambat;
      $rekap->total_terlambat = $absensi->total_terlambat;
      $rekap->total_sakit = $absensi->total_sakit;
      $rekap->total_izin = $absensi->total_izin;
      $rekap->total_alpha = $absensi->total_alpha;


      // Hitung Persentase
      if ($totalIndividualDays > 0) {
        $rekap->persentase = number_format(($rekap->total_hadir_terlambat / $totalIndividualDays) * 100, 2);
      } else {
        $rekap->persentase = 0;
      }

      $finalRekap[] = $rekap;
    }

    return view('admin.reports.attendance.index', [
      'rekapAbsensi' => collect($finalRekap),
      'startDate' => $filterStart,
      'endDate' => $filterEnd,
      'totalEffectiveDays' => $totalEffectiveDaysGlobal,
      'title' => 'Laporan Rekap Absensi',
      'header' => 'Rekap Absensi Peserta Magang',
    ]);
  }

  /**
   * Export Rekap Absensi ke PDF (Sudah Diperbaiki).
   */
  public function exportPdf(Request $request)
  {
    // 1. Definisikan Rentang Filter Admin
    $filterStart = Carbon::parse($request->input('start_date'));
    $filterEnd = Carbon::parse($request->input('end_date'));

    $totalEffectiveDaysGlobal = $this->calculateWorkingDaysGlobal($filterStart, $filterEnd);

    // 2. Query Data Absensi (Diperlukan di awal untuk GROUP BY)
    $rekapAbsensi = Presensi::select(
      'presensis.user_id',
      DB::raw('(SELECT users.name FROM users WHERE users.id = presensis.user_id) as nama_peserta'),
      DB::raw('(
              SELECT w.divisi 
              FROM lamarans l
              JOIN lowongans w ON w.id = l.lowongan_id  
              WHERE l.pengguna_id = presensis.user_id 
              LIMIT 1
          ) as divisi_peserta')
    )
      ->whereBetween('tanggal', [$filterStart->toDateString(), $filterEnd->toDateString()])
      ->groupBy('presensis.user_id')
      ->get();

    // 3. Ambil Data Lamaran (Jadwal dan Periode Resmi)
    $userIds = $rekapAbsensi->pluck('user_id');
    $dataLamaran = Lamaran::whereIn('pengguna_id', $userIds)
      ->select('pengguna_id', 'hari_kerja_resmi', 'tgl_mulai_resmi', 'tgl_selesai_resmi')
      ->get()
      ->keyBy('pengguna_id');

    // 4. Proses Perhitungan Berdasarkan Irisan Periode
    $finalRekap = [];

    foreach ($rekapAbsensi as $rekap) {
      $lamaran = $dataLamaran->get($rekap->user_id);

      if (!$lamaran || !$lamaran->tgl_mulai_resmi || !$lamaran->tgl_selesai_resmi) {
        continue;
      }

      $jadwalHari = $lamaran->hari_kerja_resmi ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

      // --- LOGIKA IRISAN KRUSIAL ---
      $irisanStart = $lamaran->tgl_mulai_resmi->greaterThan($filterStart) ? $lamaran->tgl_mulai_resmi : $filterStart;
      $irisanEnd = $lamaran->tgl_selesai_resmi->lessThan($filterEnd) ? $lamaran->tgl_selesai_resmi : $filterEnd;

      if ($irisanStart->greaterThan($irisanEnd)) {
        $rekap->total_days = 0;
        $rekap->persentase = 0;
        $rekap->total_hadir_terlambat = $rekap->total_terlambat = $rekap->total_sakit = $rekap->total_izin = $rekap->total_alpha = 0;
        $finalRekap[] = $rekap;
        continue;
      }

      // Hitung Hari Efektif Individu dalam Rentang Irisan
      $totalIndividualDays = $this->calculateIndividualWorkingDays($irisanStart, $irisanEnd, $jadwalHari);
      $rekap->total_days = $totalIndividualDays;

      // Ambil Data Absensi HANYA dalam rentang IRISAN
      $absensi = Presensi::where('user_id', $rekap->user_id)
        ->whereBetween('tanggal', [$irisanStart->toDateString(), $irisanEnd->toDateString()])
        ->select(
          DB::raw('COUNT(CASE WHEN presensis.status IN ("hadir", "terlambat") THEN 1 END) as total_hadir_terlambat'),
          DB::raw('COUNT(CASE WHEN presensis.status = "terlambat" THEN 1 END) as total_terlambat'),
          DB::raw('COUNT(CASE WHEN presensis.status = "sakit" THEN 1 END) as total_sakit'),
          DB::raw('COUNT(CASE WHEN presensis.status = "izin" THEN 1 END) as total_izin'),
          DB::raw('COUNT(CASE WHEN presensis.status = "alpha" THEN 1 END) as total_alpha')
        )
        ->first();

      // Salin hasil absensi ke objek rekap
      $rekap->total_hadir_terlambat = $absensi->total_hadir_terlambat;
      $rekap->total_terlambat = $absensi->total_terlambat;
      $rekap->total_sakit = $absensi->total_sakit;
      $rekap->total_izin = $absensi->total_izin;
      $rekap->total_alpha = $absensi->total_alpha;


      // Hitung Persentase
      if ($totalIndividualDays > 0) {
        $rekap->persentase = number_format(($rekap->total_hadir_terlambat / $totalIndividualDays) * 100, 2);
      } else {
        $rekap->persentase = 0;
      }

      $finalRekap[] = $rekap;
    }

    // Render PDF
    $pdf = Pdf::loadView('admin.reports.attendance.pdf', [
      'rekapAbsensi' => collect($finalRekap),
      'startDate' => $filterStart,
      'endDate' => $filterEnd,
      'totalEffectiveDaysGlobal' => $totalEffectiveDaysGlobal
    ]);
    $fileName = 'Laporan_Kehadiran_' . $filterStart->format('Ymd') . '-' . $filterEnd->format('Ymd') . '.pdf';

    return $pdf->download($fileName);
  }

  // ------------------------------------------------------------------
  // HELPER FUNCTIONS
  // ------------------------------------------------------------------

  /**
   * Helper: Menghitung hari kerja efektif global (Senin - Jumat) untuk Header Display
   */
  private function calculateWorkingDaysGlobal(Carbon $startDate, Carbon $endDate): int
  {
    $workingDays = 0;
    $currentDate = $startDate->clone();

    while ($currentDate->lessThanOrEqualTo($endDate)) {
      if ($currentDate->isWeekday()) {
        $workingDays++;
      }
      $currentDate->addDay();
    }

    return $workingDays;
  }

  /**
   * Helper: Menghitung hari kerja efektif individual berdasarkan jadwal array Indonesia.
   */
  private function calculateIndividualWorkingDays(Carbon $startDate, Carbon $endDate, array $workingDaysInIndo): int
  {
    $count = 0;
    $currentDate = $startDate->clone();

    // Mapping nama hari Carbon (English) ke nama hari Indonesia yang ada di DB
    $dayMap = [
      'Monday'    => 'Senin',
      'Tuesday'   => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday'  => 'Kamis',
      'Friday'    => 'Jumat',
      'Saturday'  => 'Sabtu',
      'Sunday'    => 'Minggu',
    ];

    while ($currentDate->lessThanOrEqualTo($endDate)) {
      $dayNameEnglish = $currentDate->format('l');
      $dayNameIndo = $dayMap[$dayNameEnglish] ?? null;

      if (in_array($dayNameIndo, $workingDaysInIndo)) {
        $count++;
      }
      $currentDate->addDay();
    }

    return $count;
  }
}
