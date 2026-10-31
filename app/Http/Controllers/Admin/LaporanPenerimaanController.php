<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // PENTING: Import Facade PDF

class LaporanPenerimaanController extends Controller
{
  /**
   * TAMPILKAN HALAMAN FILTER & TABEL DATA
   */
  public function index(Request $request)
  {
    $bulan = $request->input('bulan', date('m'));
    $tahun = $request->input('tahun', date('Y'));

    // Ambil data (Aktif, Alumni, Drop Out)
    $laporan = Lamaran::with(['pengguna', 'lowongan', 'pembimbing'])
      ->whereIn('status', ['diterima', 'selesai', 'berhenti'])
      ->whereMonth('tgl_mulai_resmi', $bulan)
      ->whereYear('tgl_mulai_resmi', $tahun)
      ->orderBy('tgl_mulai_resmi', 'asc')
      ->get();

    return view('admin.laporan_penerimaan.index', [
      'title'   => 'Laporan Penerimaan',
      'laporan' => $laporan,
      'bulan'   => $bulan,
      'tahun'   => $tahun
    ]);
  }

  /**
   * DOWNLOAD FILE PDF
   */
  public function cetak(Request $request)
  {
    // 1. Ambil Filter
    $bulan = $request->input('bulan', date('m'));
    $tahun = $request->input('tahun', date('Y'));

    // 2. Query Data (Sama persis dengan index)
    $laporan = Lamaran::with(['pengguna', 'lowongan', 'pembimbing'])
      ->whereIn('status', ['diterima', 'selesai', 'berhenti'])
      ->whereMonth('tgl_mulai_resmi', $bulan)
      ->whereYear('tgl_mulai_resmi', $tahun)
      ->orderBy('tgl_mulai_resmi', 'asc')
      ->get();

    // 3. Siapkan Nama File
    $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
    $fileName  = "Laporan_Penerimaan_{$namaBulan}_{$tahun}.pdf";

    // 4. Generate PDF
    $pdf = Pdf::loadView('admin.laporan_penerimaan.cetak', [
      'laporan' => $laporan,
      'bulan'   => $bulan,
      'tahun'   => $tahun
    ]);

    // Set Ukuran Kertas
    $pdf->setPaper('a4', 'portrait');

    // 5. Download Langsung
    return $pdf->download($fileName);
  }
}
