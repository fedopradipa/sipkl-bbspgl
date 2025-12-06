<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanIzin;
use App\Models\Lamaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IzinController extends Controller
{
    /**
     * Menampilkan Riwayat Pengajuan Izin
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil lamaran aktif
        $lamaran = Lamaran::where('pengguna_id', $user->id)->latest()->first();

        if (!$lamaran) {
            return redirect()->route('peserta.dashboard')->with('error', 'Tidak ada data magang aktif.');
        }

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
        return view('peserta.izin.create', [
            'title' => 'Buat Pengajuan Izin'
        ]);
    }

    /**
     * Proses Simpan Pengajuan
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $lamaran = Lamaran::where('pengguna_id', $user->id)->latest()->first();

        // 1. Validasi Input
        $request->validate([
            'jenis_izin'  => 'required|in:sakit,izin',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'alasan'      => 'required|string|max:255',
            'bukti_foto'  => 'nullable|image|max:2048|mimes:jpg,jpeg,png,pdf', // Opsional jika izin, Wajib jika sakit (bisa diatur logika validasi if)
        ], [
            'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.'
        ]);

        // 2. Handle Upload Bukti (Wajib jika ada file)
        $pathBukti = null;
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            // Simpan di folder: storage/app/public/izin
            $filename = 'izin_' . time() . '.' . $file->getClientOriginalExtension();
            $pathBukti = $file->storeAs('public/izin', $filename);

            // Kita simpan path relatifnya saja di DB (izin/filename.jpg)
            $pathBukti = str_replace('public/', '', $pathBukti);
        }

        // 3. Simpan ke Database
        PengajuanIzin::create([
            'lamaran_id'  => $lamaran->id,
            'jenis_izin'  => $request->jenis_izin,
            'tgl_mulai'   => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'alasan'      => $request->alasan,
            'bukti_foto'  => $pathBukti,
            'status'      => 'pending' // Default status
        ]);

        return redirect()->route('izin.index')->with('success', 'Pengajuan berhasil dikirim. Menunggu persetujuan Pembimbing/Admin.');
    }
}
