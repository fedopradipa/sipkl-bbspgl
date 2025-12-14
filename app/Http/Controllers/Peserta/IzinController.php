<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Services\IzinService;           // <--- Panggil Service
use App\Http\Requests\StoreIzinRequest; // <--- Panggil Request
use App\Models\PengajuanIzin;
use App\Models\Lamaran; // Hanya untuk type hinting jika perlu
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    protected $izinService;

    // Inject Service
    public function __construct(IzinService $izinService)
    {
        $this->izinService = $izinService;
    }

    /**
     * Menampilkan Riwayat Pengajuan Izin
     */
    public function index()
    {
        $user = Auth::user();

        // Cek apakah punya akses magang (status diterima)
        // Kita pakai logic yang sama di service, atau query ringan manual disini juga boleh
        // Tapi untuk konsistensi, kita cari lamaran based on user
        $lamaran = Lamaran::where('pengguna_id', $user->id)->latest()->first();

        // Jika tidak ada data sama sekali atau belum diterima
        if (!$lamaran) {
            return redirect()->route('peserta.dashboard')->with('error', 'Data magang tidak ditemukan.');
        }

        // Ambil history izin berdasarkan lamaran_id
        $riwayatIzin = PengajuanIzin::where('lamaran_id', $lamaran->id)
            ->latest()
            ->paginate(10);

        return view('peserta.izin.index', [
            'title' => 'Riwayat Izin',
            'izin'  => $riwayatIzin
        ]);
    }

    /**
     * Form Pengajuan Izin Baru
     */
    public function create()
    {
        // SAFETY CHECK: Cegah akses URL manual jika status bukan 'diterima'
        $lamaran = $this->izinService->getActiveLamaran(Auth::id());

        if (!$lamaran) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Akses ditolak. Anda hanya bisa mengajukan izin jika status magang "Diterima".');
        }

        return view('peserta.izin.create', [
            'title' => 'Buat Pengajuan Izin'
        ]);
    }

    /**
     * Proses Simpan Pengajuan
     */
    public function store(StoreIzinRequest $request)
    {
        try {
            // 1. Dapatkan Lamaran Aktif (Validasi Status Diterima ada di dalam Service)
            $lamaran = $this->izinService->getActiveLamaran(Auth::id());

            if (!$lamaran) {
                return redirect()->route('peserta.dashboard')
                    ->with('error', 'Gagal. Status magang Anda tidak valid untuk mengajukan izin.');
            }

            // 2. Panggil Service untuk simpan data & file
            // Kita kirim request->validated() (data bersih) dan file object
            $this->izinService->createIzin(
                $lamaran,
                $request->validated(),
                $request->file('bukti_foto')
            );

            return redirect()->route('izin.index')
                ->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan.');
        } catch (\Exception $e) {
            // Error handling jika upload gagal atau DB error
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
