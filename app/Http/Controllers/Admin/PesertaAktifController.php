<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lamaran;
use App\Models\Pembimbing;

class PesertaAktifController extends Controller
{
  public function index(Request $request)
  {
    // Query Peserta (Kode lama Anda)
    $peserta = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])
      ->where('status', 'diterima')
      ->when($request->search, function ($q) use ($request) {
        $q->whereHas('pengguna', function ($user) use ($request) {
          $user->where('name', 'like', '%' . $request->search . '%');
        })->orWhereHas('lowongan', function ($lowongan) use ($request) {
          $lowongan->where('divisi', 'like', '%' . $request->search . '%');
        });
      })
      ->latest()
      ->paginate(10);

    // 2. AMBIL DATA MENTORS UNTUK DROPDOWN MODAL
    $mentors = Pembimbing::orderBy('nama', 'asc')->get(); // <--- TAMBAHKAN INI

    return view('admin.peserta_aktif.index', [
      'title'   => 'Daftar Peserta Aktif',
      'peserta' => $peserta,
      'mentors' => $mentors
    ]);
  }

  public function show($id)
  {
    $lamaran = Lamaran::with([
      'pengguna.profile',
      'lowongan',
      'pembimbing',
      'presensi',
      'izin'
    ])
      ->findOrFail($id);
    $mentors = Pembimbing::orderBy('nama', 'asc')->get();
    return view('admin.peserta_aktif.show', [
      'title' => 'Detail Peserta Magang',
      'item' => $lamaran,
      'mentors' => $mentors
    ]);
  }
}
