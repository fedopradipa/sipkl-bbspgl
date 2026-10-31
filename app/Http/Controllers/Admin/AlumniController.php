<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlumniIndexRequest;
use App\Http\Requests\Admin\AlumniExportRequest;
use App\Models\Lamaran;
use Barryvdh\DomPDF\Facade\Pdf;

class AlumniController extends Controller
{
  public function index(AlumniIndexRequest $request)
  {
    $request->validated();
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
      'header' => 'Arsip Peserta PKL'
    ]);
  }
  public function show($id)
  {
    $lamaran = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing', 'presensi', 'izin'])
      ->whereIn('status', ['selesai', 'berhenti'])
      ->findOrFail($id);
    return view('admin.alumni.show', [
      'item' => $lamaran,
      'title' => 'Detail Alumni'
    ]);
  }
  public function export(AlumniExportRequest $request)
  {
    $request->validated();
    $status = $request->input('status', 'all');
    $search = $request->input('keyword');
    $alumni = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])
      ->whereIn('status', ['selesai', 'berhenti'])
      ->when($status !== 'all', fn($q) => $q->where('status', $status))
      ->when($search, function ($query, $search) {
        return $query->whereHas('pengguna', fn($q) => $q->where('name', 'like', "%{$search}%"));
      })
      ->latest('tgl_selesai_resmi')
      ->get();
    $data = [
      'data' => $alumni,
      'filterStatus' => $status,
      'printDate' => now()
    ];
    $pdf = Pdf::loadView('admin.alumni.pdf_alumni', $data);
    $pdf->setPaper('a4', 'landscape');
    return $pdf->download('Rekap_Alumni_' . date('d-m-Y') . '.pdf');
  }
}
