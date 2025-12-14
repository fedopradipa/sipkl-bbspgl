<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Lamaran;
use Carbon\Carbon;

class PresensiAdminController extends Controller
{
    /**
     * Menampilkan daftar presensi harian seluruh peserta
     */
    public function presensi(Request $request)
    {
        // 1. Ambil Parameter Filter
        $mulai = $request->input('mulai', Carbon::today()->format('Y-m-d'));
        $selesai = $request->input('selesai', Carbon::today()->format('Y-m-d'));
        $status = $request->input('status');

        // 2. Base Query Filter Tanggal
        $baseQuery = Presensi::whereBetween('tanggal', [$mulai, $selesai]);

        // 3. Hitung Ringkasan (Clone query agar tidak merusak query utama)
        $ringkasan = [
            'hadir'      => (clone $baseQuery)->where('status', 'hadir')->count(),
            // Perbaikan logika: hitung terlambat berdasarkan kolom status
            'terlambat'  => (clone $baseQuery)->where('status', 'terlambat')->count(),
            'sakit_izin' => (clone $baseQuery)->whereIn('status', ['sakit', 'izin'])->count(),
            'alpha'      => (clone $baseQuery)->where('status', 'alpha')->count(),
        ];

        // 4. Ambil Data Tabel dengan Eager Loading
        $presensis = $baseQuery->with(['lamaran.pengguna', 'lamaran.lowongan'])
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->get();

        // 5. Ambil Peserta Aktif (Untuk Modal Tambah)
        $pesertaAktif = Lamaran::with('pengguna')
            ->where('status', 'diterima') // Sesuaikan dengan value enum di DB Anda ('accepted'/'diterima')
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
    // FITUR ADMIN: CRUD PRESENSI (FIXED LOGIC)
    // =================================================================

    public function storeAdmin(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'lamaran_id' => 'required|exists:lamarans,id', // Pastikan nama tabel benar
            'tanggal'    => 'required|date',
            'status'     => 'required|in:hadir,sakit,izin,alpha',
            // Jam masuk wajib diisi jika status hadir
            'jam_masuk'  => 'required_if:status,hadir',
        ]);

        // 2. Persiapan Data Default (Untuk Sakit/Izin/Alpha)
        $jamMasuk  = null;
        $jamPulang = null;
        $ketWaktu  = null;
        $catatan   = 'Diinput Admin (' . ucfirst($request->status) . ')';

        // 3. LOGIKA KHUSUS JIKA HADIR (FIXED)
        if ($request->status == 'hadir') {
            $jamMasuk  = $request->jam_masuk;
            $jamPulang = $request->jam_pulang; // Bisa null jika belum pulang

            // --- PERBAIKAN: Hitung Telat vs Tepat Waktu ---

            // a. Ambil data lamaran beserta lowongan untuk tahu jam kerja
            $lamaran = Lamaran::with('lowongan')->findOrFail($request->lamaran_id);

            // b. Ambil jam standar masuk dari lowongan (default 08:00:00 jika tidak ada)
            // Pastikan kolom 'work_start_time' ada di tabel lowongan/vacancies
            $stdJamMasuk = $lamaran->lowongan->work_start_time ?? '08:00:00';

            // c. Bandingkan jam input vs jam standar
            $timeInput   = Carbon::parse($jamMasuk);
            $timeStandar = Carbon::parse($stdJamMasuk);

            if ($timeInput->gt($timeStandar)) {
                $ketWaktu = 'terlambat';
                $catatan .= ' - Terlambat';
            } else {
                $ketWaktu = 'tepat_waktu';
            }
        }

        // 4. Simpan ke Database
        Presensi::create([
            'lamaran_id'       => $request->lamaran_id,
            'tanggal'          => $request->tanggal,
            'status'           => $request->status,
            'jam_masuk'        => $jamMasuk,
            'jam_pulang'       => $jamPulang,
            'status' => $ketWaktu, // Hasil perhitungan di atas
            'catatan_harian'   => $catatan,
        ]);

        return back()->with('success', 'Berhasil menambahkan data presensi manual.');
    }

    public function updateAdmin(Request $request)
    {
        // 1. Ambil Data Presensi Lama beserta Relasinya
        $presensi = Presensi::with('lamaran.lowongan')->findOrFail($request->id);

        // 2. Persiapan Data Default
        // Gunakan data request jika ada, jika status berubah jadi selain hadir, null-kan waktu
        $jamMasuk  = ($request->status == 'hadir') ? $request->jam_masuk : null;
        $jamPulang = ($request->status == 'hadir') ? $request->jam_pulang : null;
        $ketWaktu  = null;

        // 3. LOGIKA UPDATE JIKA HADIR (FIXED)
        if ($request->status == 'hadir') {

            // --- PERBAIKAN: Hitung Ulang Keterlambatan ---

            // a. Ambil jam standar dari relasi yg sudah di-load
            $stdJamMasuk = $presensi->lamaran->lowongan->work_start_time ?? '08:00:00';

            // b. Bandingkan
            $timeInput   = Carbon::parse($jamMasuk);
            $timeStandar = Carbon::parse($stdJamMasuk);

            if ($timeInput->gt($timeStandar)) {
                $ketWaktu = 'terlambat';
            } else {
                $ketWaktu = 'tepat_waktu';
            }
        }

        // 4. Update Database
        $presensi->update([
            'status'           => $request->status,
            'jam_masuk'        => $jamMasuk,
            'jam_pulang'       => $jamPulang,
            'status' => $ketWaktu, // Update status telat/tepat waktu
        ]);

        return back()->with('success', 'Data presensi berhasil diperbarui (Status waktu dikalkulasi ulang).');
    }

    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return back()->with('success', 'Data presensi berhasil dihapus.');
    }
}
