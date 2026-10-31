<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LamaranIndexRequest;
use App\Http\Requests\Admin\LamaranApproveRequest;
use App\Http\Requests\Admin\LamaranRejectRequest;
use App\Http\Requests\Admin\LamaranUpdateStatusRequest;
use App\Models\Lamaran;
use App\Services\LamaranAdminService;

class LamaranAdminController extends Controller
{
  public function __construct(private LamaranAdminService $service) {}
  public function index(LamaranIndexRequest $request)
  {
    $request->validated();

    $status     = $request->input('status', 'pending');
    $search     = $request->input('keyword');
    $lowonganId = $request->input('lowongan_id');

    $lamarans = $this->service->getLamaransForIndex($status, $search, $lowonganId);

    return view('admin.lamaran.index', [
      'lamarans'      => $lamarans,
      'mentors'       => $this->service->getMentors(),
      'lowongans'     => $this->service->getLowongans(),
      'currentStatus' => $status,
      'title'         => 'Verifikasi Lamaran',
      'header'        => 'Daftar Lamaran Masuk'
    ]);
  }

  public function show($id)
  {
    $lamaran = $this->service->getLamaranDetail((int) $id);

    return view('admin.lamaran.show', [
      'lamaran' => $lamaran,
      'mentors' => $this->service->getMentors(),
      'title'   => 'Detail Lamaran'
    ]);
  }

  public function approve(LamaranApproveRequest $request, Lamaran $lamaran)
  {
    $validated = $request->validated();

    $this->service->approve($lamaran, $validated);

    $mode = $request->input('request_mode');

    if ($mode === 'approve') {
      return redirect()->route('admin.lamaran.index')
        ->with('success', 'Peserta berhasil diterima. Status kini Aktif.');
    }

    return back()->with('success', 'Data PKL berhasil diperbarui.');
  }

  public function reject(LamaranRejectRequest $request, Lamaran $lamaran)
  {
    $validated = $request->validated();

    $this->service->reject($lamaran, $validated);

    return redirect()->route('admin.lamaran.index', ['status' => 'ditolak'])
      ->with('success', 'Lamaran berhasil ditolak.');
  }

  public function updateStatus(LamaranUpdateStatusRequest $request, $id)
  {
    abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

    $validated = $request->validated();

    $this->service->updateStatus((int) $id, $validated);

    if ($validated['status'] === 'berhenti') {
      return redirect()->route('admin.peserta_aktif.index')
        ->with('success', 'Peserta dihentikan. Akses ditutup per hari ini.');
    }

    if ($validated['status'] === 'selesai') {
      return redirect()->route('admin.peserta_aktif.index')
        ->with('success', 'Peserta berhasil diluluskan.');
    }

    return redirect()->route('admin.peserta_aktif.index')
      ->with('success', 'Status diperbarui.');
  }
}
