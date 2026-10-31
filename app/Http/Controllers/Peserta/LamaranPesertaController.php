<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peserta\LamaranStoreRequest;
use App\Models\Lowongan;
use App\Services\LamaranPesertaService;
use Illuminate\Support\Facades\Auth;

class LamaranPesertaController extends Controller
{
  public function __construct(private LamaranPesertaService $service) {}
  public function create(Lowongan $lowongan)
  {
    $userId = Auth::id();
    if (!$lowongan->is_open) {
      return redirect()->route('guest.home')
        ->with('error', 'Mohon maaf, lowongan ini sudah ditutup.');
    }
    if ($this->service->hasActiveApplication($userId)) {
      return redirect()->route('peserta.dashboard')
        ->with('error', 'Anda masih memiliki lamaran aktif. Selesaikan proses yang berjalan terlebih dahulu.');
    }
    return view('peserta.lamaran.create', [
      'lowongan' => $lowongan,
      'title'    => 'Formulir Pendaftaran PKL',
      'header'   => 'Lamar Posisi: ' . $lowongan->posisi
    ]);
  }
  public function store(LamaranStoreRequest $request, Lowongan $lowongan)
  {
    $userId = Auth::id();
    if (!$lowongan->is_open || $this->service->hasActiveApplication($userId)) {
      return redirect()->route('peserta.dashboard')
        ->with('error', 'Validasi gagal. Lowongan tutup atau Anda memiliki lamaran aktif.');
    }
    $validated = $request->validated();
    $this->service->storeLamaran(
      $userId,
      $lowongan,
      $validated,
      $request->file('file_surat_pengantar')
    );
    return redirect()->route('peserta.dashboard')
      ->with('success', 'Lamaran berhasil dikirim! Menunggu verifikasi admin.');
  }
}
