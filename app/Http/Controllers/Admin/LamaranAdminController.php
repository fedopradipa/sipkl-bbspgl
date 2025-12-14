<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pembimbing;
use App\Services\LamaranService; // <--- Import Service
use Illuminate\Http\Request;

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
  public function approve(Request $request, Lamaran $lamaran)
  {
    // 1. Validasi di Controller
    $validated = $request->validate([
      'pembimbing_id'     => 'required|exists:pembimbings,id',
      'tgl_mulai_resmi'   => 'required|date',
      'tgl_selesai_resmi' => 'required|date|after_or_equal:tgl_mulai_resmi',
      'hari_kerja_resmi'  => 'required|array',
    ]);

    // 2. Panggil Service untuk Eksekusi Logic
    $this->lamaranService->approve($lamaran, $validated);

    return back()->with('success', 'Peserta resmi diterima & data magang disimpan.');
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
  public function updateStatus(Request $request, $id)
  {
    // 1. Validasi
    $request->validate([
      'status'  => 'required|in:pending,diterima,ditolak,selesai,berhenti',
      'catatan' => 'nullable|string|max:500',
    ]);

    $lamaran = Lamaran::findOrFail($id);

    // 2. Panggil Service (Service yang akan menangani logic Cut-off tanggal, dll)
    // Kita kirim seluruh input ($request->all()) karena service butuh 'kategori' dan 'catatan_detail'
    $this->lamaranService->updateStatus($lamaran, $request->status, $request->all());

    // 3. Feedback Message
    $msg = 'Status diperbarui.';
    if ($request->status == 'berhenti') $msg = 'Peserta diberhentikan. Akses ditutup per hari ini.';
    if ($request->status == 'selesai')  $msg = 'Peserta berhasil diluluskan.';

    return back()->with('success', $msg);
  }

  /**
   * Hapus Data Lamaran.
   * Route: DELETE /admin/lamaran/{id}
   */
  public function destroy(Lamaran $lamaran)
  {
    $lamaran->delete();
    return back()->with('success', 'Data lamaran dihapus permanen.');
  }
}
