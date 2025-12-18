<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLamaranRequest;
use App\Http\Requests\UploadLaporanRequest;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Services\LamaranService;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


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

  /**
   * Handle Upload Laporan Akhir (State Finalisasi)
   */
  public function uploadLaporan(UploadLaporanRequest $request)
  {
    // 1. Ambil Data Lamaran
    $lamaran = Lamaran::findOrFail($request->id);

    // 2. Security Check: Pastikan milik user yang login
    if ($lamaran->pengguna_id !== Auth::id()) {
      abort(403, 'Akses ditolak.');
    }

    // 3. Handle File Upload via Service
    if ($request->hasFile('file_laporan')) {

      // Optional: Hapus file lama jika ini adalah REVISI
      if ($lamaran->path_laporan_akhir) {
        Storage::delete('public/laporan_akhir/' . $lamaran->path_laporan_akhir);
      }

      // Gunakan FileUploadService Anda
      $filename = $this->fileService->uploadLaporanAkhir(
        $request->file('file_laporan'),
        Auth::id()
      );

      // Update nama file di model
      $lamaran->path_laporan_akhir = $filename;
    }

    // 4. Update Link & Simpan
    $lamaran->save();

    return back()->with('success', 'Laporan akhir berhasil diunggah. Menunggu verifikasi Admin.');
  }
}
