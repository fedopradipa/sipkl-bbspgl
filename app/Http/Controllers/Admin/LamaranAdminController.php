<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pembimbing;
use App\Services\LamaranService;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateStatusRequest;

class LamaranAdminController extends Controller
{
  protected $lamaranService;

  // 1. INJECT SERVICE DI CONSTRUCTOR
  public function __construct(LamaranService $lamaranService)
  {
    $this->lamaranService = $lamaranService;
  }

  /**
   * Menampilkan daftar lamaran masuk (Verifikasi).
   * Route: GET /admin/lamaran
   */
  public function index(Request $request)
  {
    $status     = $request->input('status', 'pending');
    $search     = $request->input('keyword');
    $lowonganId = $request->input('lowongan_id');

    $lamarans = Lamaran::with(['pengguna.profile', 'lowongan'])
      ->when($status !== 'all', fn($q) => $q->where('status', $status))
      ->when($lowonganId, fn($q) => $q->where('lowongan_id', $lowonganId))
      ->when($search, function ($query, $search) {
        return $query->where(function ($q) use ($search) {
          $q->whereHas('pengguna', fn($subQ) => $subQ->where('name', 'like', "%{$search}%"))
            ->orWhereHas('lowongan', function ($subQ) use ($search) {
              $subQ->where('judul', 'like', "%{$search}%")
                ->orWhere('divisi', 'like', "%{$search}%");
            });
        });
      })
      ->latest()
      ->paginate(15)
      ->withQueryString();

    return view('admin.lamaran.index', [
      'lamarans'      => $lamarans,
      'mentors'       => Pembimbing::where('is_active', true)->orderBy('nama')->get(['id', 'nama']),
      'lowongans'     => Lowongan::orderBy('judul')->get(['id', 'judul']),
      'currentStatus' => $status,
      'title'         => 'Verifikasi Lamaran',
      'header'        => 'Daftar Lamaran Masuk'
    ]);
  }

  /**
   * Menampilkan detail lamaran.
   * Route: GET /admin/lamaran/{id}
   */
  public function show($id)
  {
    $lamaran = Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])->findOrFail($id);

    return view('admin.lamaran.show', [
      'lamaran' => $lamaran,
      'mentors' => Pembimbing::where('is_active', true)->orderBy('nama')->get(['id', 'nama']),
      'title'   => 'Detail Lamaran'
    ]);
  }

  /**
   * Menerima Peserta (Approval Awal).
   * Route: POST /admin/lamaran/{id}/approve
   */
  // CONTROLLER
  public function approve(Request $request, Lamaran $lamaran)
  {
    // 1. Validasi Input Data Magang
    // (Bisa dipindah ke FormRequest class jika ingin lebih clean, tapi di sini juga oke)
    $validated = $request->validate([
      'pembimbing_id'     => 'required|exists:pembimbings,id',
      'tgl_mulai_resmi'   => 'required|date',
      'tgl_selesai_resmi' => 'required|date|after_or_equal:tgl_mulai_resmi',
      'hari_kerja_resmi'  => 'required|array',
      'request_mode'      => 'nullable|string'
    ]);

    // 2. Panggil Service untuk Eksekusi Logic Bisnis (Database)
    // Service tidak perlu tahu soal 'request_mode', dia cuma butuh data magang.
    $this->lamaranService->approve($lamaran, $validated);

    // 3. LOGIKA TRAFFIC (Controller Only)
    // Ambil input hidden 'request_mode' dari Modal tadi
    $mode = $request->input('request_mode');

    if ($mode === 'approve') {
      // SKENARIO 1: APPROVE AWAL (Dari status Pending -> Diterima)
      // Admin biasanya ingin kembali ke daftar antrian untuk memproses pelamar lain.
      return redirect()->route('admin.lamaran.index')
        ->with('success', 'Peserta berhasil diterima. Status kini Aktif.');
    } else {
      // SKENARIO 2: EDIT DATA (Dari status Diterima -> Diterima/Update)
      // Admin sedang mengedit jadwal di halaman Detail/Peserta Aktif.
      // Jangan lempar ke Index, tetap di halaman itu saja (Back).
      return back()
        ->with('success', 'Data magang berhasil diperbarui.');
    }
  }

  /**
   * Menolak Lamaran (Reject).
   * Route: POST /admin/lamaran/{id}/reject
   */
  public function reject(Request $request, Lamaran $lamaran)
  {
    $request->validate([
      'catatan_admin' => 'required|string|max:500',
    ]);

    // Panggil Service
    $this->lamaranService->reject($lamaran, $request->catatan_admin);

    return redirect()->route('admin.lamaran.index', ['status' => 'ditolak'])
      ->with('success', 'Lamaran berhasil ditolak.');
  }

  /**
   * Mengubah Status (Lulus / Drop Out / Batal).
   * Route: PUT /admin/lamaran/{id}/status
   */
  // Jangan lupa import Request yang sudah Anda buat


  public function updateStatus(UpdateStatusRequest $request, $id)
  {
    // 1. Validasi sudah otomatis ditangani oleh UpdateStatusRequest
    // Jadi kita tidak perlu menulis $request->validate(...) lagi di sini.

    $lamaran = Lamaran::findOrFail($id);

    // 2. Panggil Service
    $this->lamaranService->updateStatus($lamaran, $request->status, $request->all());

    // 3. Tentukan Pesan & Redirect
    $msg = 'Status diperbarui.';

    // Logika Redirect Khusus
    if ($request->status == 'berhenti') {
      $msg = 'Peserta diberhentikan. Akses ditutup per hari ini.';
      // Redirect ke Index Peserta Aktif
      return redirect()->route('admin.peserta_aktif.index')->with('success', $msg);
    }

    if ($request->status == 'selesai') {
      $msg = 'Peserta berhasil diluluskan.';
      // Redirect ke Index Peserta Aktif
      return redirect()->route('admin.peserta_aktif.index')->with('success', $msg);
    }

    // Default (misal status lain): Tetap bisa pakai back() atau redirect ke index juga
    return redirect()->route('admin.peserta_aktif.index')->with('success', $msg);
  }
}
