<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peserta\IzinStoreRequest;
use App\Http\Requests\Peserta\IzinUpdateRequest;
use App\Models\PengajuanIzin;
use App\Models\Lamaran;
use App\Services\PesertaIzinService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IzinController extends Controller
{
    public function __construct(private PesertaIzinService $service) {}
    public function index()
    {
        $user = Auth::user();

        $lamaran = $this->service->getLatestLamaranForHistory($user->id);

        if (!$lamaran) {
            return view('peserta.izin.index', [
                'title' => 'Riwayat Izin',
                'izin'  => collect([])
            ]);
        }

        $riwayatIzin = PengajuanIzin::where('lamaran_id', $lamaran->id)
            ->latest()
            ->paginate(10);

        return view('peserta.izin.index', [
            'title' => 'Riwayat Izin',
            'izin'  => $riwayatIzin
        ]);
    }
    public function create()
    {
        $lamaran = $this->service->getActiveLamaran(Auth::id());

        if (!$lamaran) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Akses ditolak. Anda hanya bisa mengajukan izin pada periode PKL aktif.');
        }

        return view('peserta.izin.create', [
            'title'   => 'Buat Pengajuan Izin',
            'lamaran' => $lamaran
        ]);
    }
    public function store(IzinStoreRequest $request)
    {
        try {
            $lamaran = $this->service->getActiveLamaran(Auth::id());

            if (!$lamaran) {
                return redirect()->route('peserta.dashboard')
                    ->with('error', 'Gagal. Status PKL Anda tidak valid.');
            }
            $this->service->createPengajuanIzin(
                $lamaran,
                $request->validated(),
                $request->file('bukti_lampiran')
            );
            return redirect()->route('izin.index')
                ->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan.');
        } catch (\Exception $e) {
            Log::error("Error Store Izin: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $izin = PengajuanIzin::findOrFail($id);

        if ($izin->lamaran->pengguna_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }
        if ($izin->status !== 'pending') {
            return redirect()->route('izin.index')
                ->with('error', 'Izin yang sudah diproses tidak dapat diedit.');
        }

        return view('peserta.izin.edit', [
            'title'   => 'Edit Pengajuan Izin',
            'izin'    => $izin,
            'lamaran' => $izin->lamaran
        ]);
    }
    public function update(IzinUpdateRequest $request, $id)
    {
        $izin = PengajuanIzin::findOrFail($id);

        if ($izin->lamaran->pengguna_id !== Auth::id() || $izin->status !== 'pending') {
            return redirect()->route('izin.index')->with('error', 'Tidak dapat mengubah data ini.');
        }

        try {
            $this->service->updatePengajuanIzin(
                $izin,
                $request->validated(),
                $request->file('bukti_lampiran')
            );

            return redirect()->route('izin.index')
                ->with('success', 'Perubahan pengajuan izin berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
