<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Services\PesertaPresensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
  public function __construct(private PesertaPresensiService $service) {}
  public function index()
  {
    $user = Auth::user();
    $lamaran = $this->service->getActiveLamaranWithLowongan($user->id);
    $izinHariIni = $this->service->getIzinHariIni($lamaran->id);
    $isHariKerja = $this->service->isHariKerja($lamaran);
    $presensi = $this->service->getPresensiHariIni($lamaran->id);
    return view('peserta.presensi.index', [
      'title' => 'Presensi Harian',
      'user' => $user,
      'lamaran' => $lamaran,
      'presensi' => $presensi,
      'isHariKerja' => $isHariKerja,
      'izinHariIni' => $izinHariIni,
      'lowongan' => $lamaran->lowongan,
    ]);
  }
  public function checkIn(Request $request)
  {
    try {
      $pesan = $this->service->checkIn(Auth::id());
      return redirect()->route('presensi.index')->with('success', $pesan);
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());
    }
  }
}
