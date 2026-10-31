<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peserta\PesertaLowonganIndexRequest;
use App\Models\Lamaran;
use App\Models\Lowongan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardPesertaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $lamaran = Lamaran::with(['lowongan', 'pembimbing'])
            ->where('pengguna_id', $user->id)
            ->latest()
            ->first();
        if (!$lamaran) {
            return view('peserta.dashboard.empty', ['title' => 'Selamat Datang']);
        }
        $state = $this->determineDashboardState($lamaran);
        return view('peserta.dashboard.index', [
            'title'   => 'Dashboard Peserta',
            'user'    => $user,
            'lamaran' => $lamaran,
            'state'   => $state,
        ]);
    }
    public function indexLowongan(PesertaLowonganIndexRequest $request)
    {
        $request->validated();
        $search = $request->input('keyword');

        $lowongans = Lowongan::tersedia()
            ->when($search, function ($query, $search) {
                return $query->where('posisi', 'like', "%{$search}%");
            })
            ->latest('window_start_date')
            ->paginate(9)
            ->appends(['keyword' => $search]);

        return view('peserta.lowongan.index', [
            'lowongans' => $lowongans,
            'title' => 'Bursa Lowongan PKL'
        ]);
    }

    public function showLowongan($id)
    {
        $lowongan = Lowongan::findOrFail($id);

        if (!$lowongan->is_open) {
            abort(404);
        }

        return view('peserta.lowongan.show', [
            'lowongan' => $lowongan,
            'title' => $lowongan->posisi
        ]);
    }

    private function determineDashboardState(Lamaran $lamaran): string
    {
        if (in_array($lamaran->status, ['pending', 'ditolak', 'selesai', 'berhenti'])) {
            return $lamaran->status;
        }
        if ($lamaran->status == 'diterima') {
            $today = Carbon::today();
            $start = Carbon::parse($lamaran->tgl_mulai_resmi ?? $lamaran->tgl_mulai_usulan);
            $end   = Carbon::parse($lamaran->tgl_selesai_resmi ?? $lamaran->tgl_selesai_usulan);
            if ($today->lt($start)) return 'pra_mulai';
            if ($today->gt($end)) return 'finalisasi';
            return 'active';
        }
        return 'kosong';
    }
}
