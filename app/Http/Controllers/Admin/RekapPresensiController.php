<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RekapPresensiRequest;
use App\Services\RekapPresensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RekapPresensiController extends Controller
{
  public function __construct(private RekapPresensiService $service) {}

  public function index(RekapPresensiRequest $request)
  {
    $validated = $request->validated();

    $bulan      = $validated['bulan'] ?? Carbon::now()->month;
    $tahun      = $validated['tahun'] ?? Carbon::now()->year;
    $lowonganId = $validated['lowongan_id'] ?? null;

    $data = $this->service->getRekapMatrix($bulan, $tahun, $lowonganId);

    return view('admin.reports.attendance.index', [
      'title'          => 'Rekap Presensi Bulanan',
      'dates'          => $data['header_dates'],
      'rekapData'      => $data['data'],
      'posisiList'     => $data['posisi_list'],
      'bulanNama'      => $data['bulan_nama'],
      'tahun'          => $data['tahun'],
      'selectedBulan'  => $bulan,
      'selectedTahun'  => $tahun,
      'selectedPosisi' => $lowonganId,
    ]);
  }

  public function exportPdf(RekapPresensiRequest $request)
  {
    $validated = $request->validated();

    $bulan      = $validated['bulan'] ?? Carbon::now()->month;
    $tahun      = $validated['tahun'] ?? Carbon::now()->year;
    $lowonganId = $validated['lowongan_id'] ?? null;

    $data = $this->service->getRekapMatrix($bulan, $tahun, $lowonganId);

    $pdf = Pdf::loadView('admin.reports.attendance.pdf', [
      'dates'     => $data['header_dates'],
      'rekapData' => $data['data'],
      'bulanNama' => $data['bulan_nama'],
      'tahun'     => $data['tahun'],
    ])->setPaper('a4', 'landscape');

    $fileName = 'Rekap_Absensi_' . $data['bulan_nama'] . '_' . $tahun . '.pdf';
    return $pdf->download($fileName);
  }
}
