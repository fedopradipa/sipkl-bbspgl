<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Lamaran; // Import Model Lamaran
use Carbon\Carbon;

class PresensiAdminController extends Controller
{
    /**
     * Menampilkan daftar presensi harian seluruh peserta
     */
    public function presensi(Request $request)
    {
        // 1. Ambil Parameter Filter (Rentang Tanggal)
        $mulai = $request->input('mulai', Carbon::today()->format('Y-m-d'));
        $selesai = $request->input('selesai', Carbon::today()->format('Y-m-d'));
        $status = $request->input('status');

        // 2. BUAT BASE QUERY (Hanya filter tanggal)
        // Ini adalah sumber data mentah sebelum difilter statusnya
        $baseQuery = Presensi::whereBetween('tanggal', [$mulai, $selesai]);

        // 3. HITUNG RINGKASAN STATISTIK (Gunakan clone $baseQuery)
        // Statistik harus menghitung SEMUA data pada tanggal tersebut, 
        // tidak peduli apa status yang sedang dipilih di dropdown filter.
        $ringkasan = [
            'hadir'      => (clone $baseQuery)->where('status', 'hadir')->count(),
            'terlambat'  => (clone $baseQuery)->where('keterangan_waktu', 'terlambat')->count(),
            'sakit_izin' => (clone $baseQuery)->whereIn('status', ['sakit', 'izin'])->count(),
            'alpha'      => (clone $baseQuery)->where('status', 'alpha')->count(),
        ];

        // 4. QUERY DATA TABEL (Baru kita terapkan filter status disini)
        $presensis = $baseQuery->with(['lamaran.pengguna', 'lamaran.lowongan'])
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->get();

        // 5. Ambil Peserta Aktif (Untuk Modal Tambah Manual)
        $pesertaAktif = Lamaran::with('pengguna')
            ->where('status', 'diterima')
            ->get();

        return view('admin.presensi.index', [
            'title'        => 'Laporan Presensi',
            'header'       => 'Monitoring Kehadiran',
            'mulai'        => $mulai,
            'selesai'      => $selesai,
            'presensis'    => $presensis,
            'ringkasan'    => $ringkasan,
            'pesertaAktif' => $pesertaAktif,
        ]);
    }

    // =================================================================
    // FITUR ADMIN: CRUD PRESENSI
    // =================================================================
    public function storeAdmin(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'lamaran_id' => 'required|exists:lamarans,id',
            'tanggal'    => 'required|date',
            'status'     => 'required|in:hadir,sakit,izin,alpha',
            // Jam masuk wajib HANYA jika Hadir
            'jam_masuk'  => 'required_if:status,hadir',
        ]);

        // 2. Tentukan Nilai Jam & Keterangan
        // Jika status HADIR, pakai input admin. Jika TIDAK, set NULL.
        $isHadir   = ($request->status == 'hadir');
        $jamMasuk  = $isHadir ? $request->jam_masuk : null;
        $jamPulang = $isHadir ? $request->jam_pulang : null;
        $ketWaktu  = $isHadir ? 'tepat_waktu' : null;

        // 3. Simpan
        Presensi::create([
            'lamaran_id'       => $request->lamaran_id,
            'tanggal'          => $request->tanggal,
            'status'           => $request->status,

            'jam_masuk'        => $jamMasuk,  // Ini akan jadi NULL
            'jam_pulang'       => $jamPulang, // Ini akan jadi NULL
            'keterangan_waktu' => $ketWaktu,

            'catatan_harian'   => 'Diinput oleh Admin (' . ucfirst($request->status) . ')',
        ]);

        return back()->with('success', 'Berhasil menambahkan data presensi.');
    }

    public function updateAdmin(Request $request)
    {
        $presensi = Presensi::findOrFail($request->id);

        $presensi->update([
            'status'     => $request->status,
            'jam_masuk'  => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
        ]);

        return back()->with('success', 'Data presensi berhasil diubah.');
    }

    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return back()->with('success', 'Data presensi berhasil dihapus.');
    }
}
