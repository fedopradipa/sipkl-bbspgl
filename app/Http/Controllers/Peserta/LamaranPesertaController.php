<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLamaranRequest;
use App\Models\Lowongan;
use App\Services\LamaranService;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Auth;

class LamaranPesertaController extends Controller
{
  public function __construct(
    private LamaranService $lamaranService,
    private FileUploadService $fileService
  ) {}

  /**
   * Show lamaran form
   */
  public function create(Lowongan $lowongan)
  {
    $userId = Auth::id();

    // Security check: Lowongan closed
    if (!$lowongan->is_open) {
      return redirect()->route('guest.home')
        ->with('error', 'Mohon maaf, lowongan ini sudah ditutup.');
    }

    // 1. Cek Lamaran Aktif (Standard)
    if ($this->lamaranService->hasActiveApplication($userId)) {
      return redirect()->route('peserta.dashboard')
        ->with('error', 'Anda masih memiliki lamaran aktif. Selesaikan proses yang berjalan terlebih dahulu.');
    }

    // 2. [BARU] Cek Blacklist (Security Layer)
    if ($this->lamaranService->isBlacklisted($userId)) {
      return redirect()->route('peserta.dashboard')
        ->with('error', 'Mohon maaf, Anda tidak dapat melamar kembali karena riwayat pelanggaran tata tertib pada periode sebelumnya.');
    }

    return view('peserta.lamaran.create', [
      'lowongan' => $lowongan,
      'title'    => 'Formulir Pendaftaran PKL',
      'header'   => 'Lamar Posisi: ' . $lowongan->judul
    ]);
  }

  /**
   * Store new lamaran
   */
  public function store(StoreLamaranRequest $request, Lowongan $lowongan)
  {
    // Double check security
    if (!$lowongan->is_open || $this->lamaranService->hasActiveApplication(Auth::id())) {
      return redirect()->route('peserta.dashboard')
        ->with('error', 'Validasi gagal. Lowongan tutup atau Anda memiliki lamaran aktif.');
    }

    // Handle file upload
    $filePath = null;
    if ($request->hasFile('file_surat_pengantar')) {
      $filePath = $this->fileService->uploadSuratPengantar(
        $request->file('file_surat_pengantar'),
        Auth::id()
      );
    }

    // Prepare data
    $data = $request->validated();
    $data['file_surat_pengantar'] = $filePath;

    // Create lamaran via service
    $this->lamaranService->createLamaran($data, $lowongan);

    return redirect()->route('peserta.dashboard')
      ->with('success', 'Lamaran berhasil dikirim! Menunggu verifikasi admin.');
  }
}
