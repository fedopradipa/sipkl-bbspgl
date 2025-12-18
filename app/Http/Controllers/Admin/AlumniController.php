<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AlumniController extends Controller
{
  /**
   * Display alumni list
   */
  public function index(Request $request)
  {
    $status = $request->input('status', 'all');
    $search = $request->input('keyword');

    $alumni = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])
      ->whereIn('status', ['selesai', 'berhenti'])
      ->when($status !== 'all', fn($q) => $q->where('status', $status))
      ->when($search, function ($query, $search) {
        return $query->whereHas('pengguna', fn($q) => $q->where('name', 'like', "%{$search}%"));
      })
      ->latest('updated_at')
      ->paginate(10)
      ->withQueryString();

    return view('admin.alumni.index', [
      'alumni' => $alumni,
      'currentStatus' => $status,
      'title' => 'Data Alumni',
      'header' => 'Arsip Peserta Magang'
    ]);
  }

  /**
   * Show alumni detail
   */
  public function show($id)
  {
    $lamaran = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing', 'presensi', 'izin'])
      ->whereIn('status', ['selesai', 'berhenti'])
      ->findOrFail($id);

    return view('admin.alumni.show', [
      'item' => $lamaran, // Variable konsisten pakai $item
      'title' => 'Detail Alumni'
    ]);
  }

  public function export(Request $request)
  {
    $status = $request->input('status', 'all');
    $search = $request->input('keyword');

    // Query Data
    $alumni = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])
      ->whereIn('status', ['selesai', 'berhenti'])
      ->when($status !== 'all', fn($q) => $q->where('status', $status))
      ->when($search, function ($query, $search) {
        return $query->whereHas('pengguna', fn($q) => $q->where('name', 'like', "%{$search}%"));
      })
      ->latest('tgl_selesai_resmi')
      ->get();

    // Data yang dikirim ke View
    $data = [
      'data' => $alumni,
      'filterStatus' => $status,
      'printDate' => now()
    ];

    // --- LOGIKA BARU UNTUK DOWNLOAD PDF ---

    // 1. Load View
    $pdf = Pdf::loadView('admin.alumni.pdf_alumni', $data);

    // 2. Setup Kertas (A4 Landscape biasanya cukup untuk tabel lebar)
    $pdf->setPaper('a4', 'landscape');

    // 3. Langsung Download
    // Nama file otomatis: Rekap_Alumni_TANGGAL.pdf
    return $pdf->download('Rekap_Alumni_' . date('d-m-Y') . '.pdf');
  }
}
