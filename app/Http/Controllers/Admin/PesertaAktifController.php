<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PesertaAktifIndexRequest;
use App\Models\Lamaran;
use App\Models\Pembimbing;

class PesertaAktifController extends Controller
{
  public function index(PesertaAktifIndexRequest $request)
  {
    $request->validated();
    $peserta = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])
      ->where('status', 'diterima')
      ->when($request->search, function ($q) use ($request) {
        $search = $request->search;
        $q->where(function ($sub) use ($search) {
          $sub->whereHas('pengguna', function ($user) use ($search) {
            $user->where('name', 'like', '%' . $search . '%');
          })
            ->orWhereHas('lowongan', function ($lowongan) use ($search) {
              $lowongan->where('posisi', 'like', '%' . $search . '%');
            });
        });
      })
      ->latest()
      ->paginate(10)
      ->withQueryString();;
    $mentors = Pembimbing::orderBy('nama', 'asc')->get();
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
    ])->findOrFail($id);
    $mentors = Pembimbing::orderBy('nama', 'asc')->get();
    return view('admin.peserta_aktif.show', [
      'title'   => 'Detail Peserta PKL',
      'item'    => $lamaran,
      'mentors' => $mentors
    ]);
  }
}
