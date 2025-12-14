<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
  /**
   * Display alumni list
   */
  public function index(Request $request)
  {
    $status = $request->input('status', 'all'); // all, selesai, berhenti
    $search = $request->input('keyword');

    $alumni = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])
      ->whereIn('status', ['selesai', 'berhenti'])
      ->when($status !== 'all', fn($q) => $q->where('status', $status))
      ->when($search, function ($query, $search) {
        return $query->whereHas('pengguna', fn($q) => $q->where('name', 'like', "%{$search}%"));
      })
      ->latest('updated_at')
      ->paginate(20)
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
    $lamaran = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])
      ->whereIn('status', ['selesai', 'berhenti'])
      ->findOrFail($id);

    return view('admin.alumni.show', [
      'lamaran' => $lamaran,
      'title' => 'Detail Alumni'
    ]);
  }
}
