<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Services\IzinService;           // Import Service
use App\Http\Requests\StoreIzinRequest; // Import Request Validasi
use App\Models\PengajuanIzin;
use App\Models\Lamaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk hapus foto lama

class IzinController extends Controller
{
    protected $izinService;

    // Dependency Injection Service
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

        // Ambil data lamaran user
        $lamaran = Lamaran::where('pengguna_id', $user->id)
            ->whereIn('status', ['diterima', 'selesai'])
            ->latest()
            ->first();

        if (!$lamaran) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Data magang tidak ditemukan.');
        }

        // Ambil history izin
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
        $lamaran = $this->izinService->getActiveLamaran(Auth::id());

        if (!$lamaran) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Akses ditolak. Anda hanya bisa mengajukan izin pada periode magang aktif.');
        }

        return view('peserta.izin.create', [
            'title'   => 'Buat Pengajuan Izin',
            'lamaran' => $lamaran
        ]);
    }

    /**
     * Proses Simpan Pengajuan Baru
     */
    public function store(StoreIzinRequest $request)
    {
        try {
            $lamaran = $this->izinService->getActiveLamaran(Auth::id());

            if (!$lamaran) {
                return redirect()->route('peserta.dashboard')
                    ->with('error', 'Gagal. Status magang Anda tidak valid.');
            }

            $this->izinService->createIzin(
                $lamaran,
                $request->validated(),
                $request->file('bukti_foto')
            );

            return redirect()->route('izin.index')
                ->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Izin (METHOD BARU)
     */
    public function edit($id)
    {
        // 1. Cari data izin
        $izin = PengajuanIzin::findOrFail($id);

        // 2. Validasi Keamanan: Pastikan izin ini milik user yang login
        if ($izin->lamaran->pengguna_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        // 3. Validasi Status: Hanya status 'pending' yang boleh diedit
        if ($izin->status !== 'pending') {
            return redirect()->route('izin.index')
                ->with('error', 'Izin yang sudah diproses tidak dapat diedit.');
        }

        // 4. Ambil data lamaran (Penting untuk batas tanggal di kalender/script)
        $lamaran = $izin->lamaran;

        return view('peserta.izin.edit', [
            'title'   => 'Edit Pengajuan Izin',
            'izin'    => $izin,
            'lamaran' => $lamaran
        ]);
    }

    /**
     * Proses Update Izin (METHOD BARU)
     */
    public function update(StoreIzinRequest $request, $id)
    {
        $izin = PengajuanIzin::findOrFail($id);

        // 1. Validasi Keamanan & Status
        if ($izin->lamaran->pengguna_id !== Auth::id() || $izin->status !== 'pending') {
            return redirect()->route('izin.index')->with('error', 'Tidak dapat mengubah data ini.');
        }

        try {
            // 2. Olah Tanggal (Flatpickr mengirim string "2025-01-01, 2025-01-02")
            // Kita ubah jadi array agar sesuai dengan kolom JSON di database
            // Jika Anda menggunakan Service, logika ini bisa dipindah ke Service.
            $datesArray = explode(', ', $request->tanggal_izin);

            $dataToUpdate = [
                'jenis_izin'   => $request->jenis_izin,
                'tanggal_izin' => $datesArray, // Pastikan Model dicast 'array'
                'alasan'       => $request->alasan,
            ];

            // 3. Cek Upload Bukti Baru
            if ($request->hasFile('bukti_foto')) {
                // Hapus foto lama jika ada
                if ($izin->bukti_foto && Storage::disk('public')->exists($izin->bukti_foto)) {
                    Storage::disk('public')->delete($izin->bukti_foto);
                }

                // Simpan foto baru
                $path = $request->file('bukti_foto')->store('bukti_izin', 'public');
                $dataToUpdate['bukti_foto'] = $path;
            }

            // 4. Update Database
            $izin->update($dataToUpdate);

            return redirect()->route('izin.index')
                ->with('success', 'Perubahan pengajuan izin berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
