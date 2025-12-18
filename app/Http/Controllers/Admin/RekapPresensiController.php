<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterRekapRequest;
use App\Services\Admin\RekapPresensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RekapPresensiController extends Controller
{
  protected $rekapService;

  public function __construct(RekapPresensiService $rekapService)
  {
    $this->rekapService = $rekapService;
  }

  /**
   * Menampilkan Halaman Filter & Preview Matriks
   */
  public function index(FilterRekapRequest $request)
  {
    // Ambil input atau default ke bulan ini
    $bulan = $request->input('bulan', Carbon::now()->month);
    $tahun = $request->input('tahun', Carbon::now()->year);
    $lowonganId = $request->input('lowongan_id');

    // Panggil Service
    $data = $this->rekapService->getRekapMatrix($bulan, $tahun, $lowonganId);

    return view('admin.reports.attendance.index', [
      'title'     => 'Rekap Presensi Bulanan',
      'dates'     => $data['header_dates'],
      'rekapData' => $data['data'],
      'divisiList' => $data['divisi_list'],
      'bulanNama' => $data['bulan_nama'],
      'tahun'     => $data['tahun'],
      'selectedBulan' => $bulan,
      'selectedTahun' => $tahun,
      'selectedDivisi' => $lowonganId
    ]);
  }

  /**
   * Export ke PDF
   */
  public function exportPdf(FilterRekapRequest $request)
  {
    $bulan = $request->input('bulan', Carbon::now()->month);
    $tahun = $request->input('tahun', Carbon::now()->year);
    $lowonganId = $request->input('lowongan_id');

    $data = $this->rekapService->getRekapMatrix($bulan, $tahun, $lowonganId);

    $pdf = Pdf::loadView('admin.reports.attendance.pdf', [
      'dates'     => $data['header_dates'],
      'rekapData' => $data['data'],
      'bulanNama' => $data['bulan_nama'],
      'tahun'     => $data['tahun'],
    ])->setPaper('a4', 'landscape'); // Landscape agar muat tanggal 1-31

    $fileName = 'Rekap_Absensi_' . $data['bulan_nama'] . '_' . $tahun . '.pdf';

    return $pdf->download($fileName);
  }
}
