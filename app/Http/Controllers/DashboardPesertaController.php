<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lamaran;   // Pastikan sesuai nama Model Anda (Lamaran atau LamaranPkl)
use App\Models\Presensi;
use Carbon\Carbon;

class DashboardPesertaController extends Controller
{
    /**
     * Halaman Utama Dashboard Peserta
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Data Lamaran
        $lamaran = Lamaran::with(['lowongan', 'pembimbing'])
            ->where('pengguna_id', $user->id)
            ->latest()
            ->first();

        if (!$lamaran) {
            return view('peserta.dashboard.empty', ['title' => 'Selamat Datang']);
        }

        // 2. Auto-Close Check
        $this->runAutoCloseCheck($lamaran->id);

        // 3. Tentukan State UI
        $state = $this->determineState($lamaran);

        // 4. LOGIC BARU: CEK HARI KERJA (Sabtu/Minggu vs Jadwal Resmi)
        $presensiHariIni = null;
        $isHariKerja = false; // Default: Bukan hari kerja

        if ($state == 'active') {
            // A. Konversi Hari Ini ke Bahasa Indonesia
            $englishDay = Carbon::now()->format('l');
            $indoDays = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];
            $hariIni = $indoDays[$englishDay];

            // B. Cek apakah hari ini ada di array 'hari_kerja_resmi'
            // Gunakan array kosong [] sebagai fallback jika null
            $jadwalKerja = $lamaran->hari_kerja_resmi ?? [];

            // LOGIC UTAMA: Hanya jika hari ini ada di jadwal, aktifkan tombol
            if (is_array($jadwalKerja) && in_array($hariIni, $jadwalKerja)) {
                $isHariKerja = true;

                // Ambil data presensi
                $presensiHariIni = Presensi::where('lamaran_id', $lamaran->id)
                    ->whereDate('tanggal', Carbon::today())
                    ->first();
            }
        }

        return view('peserta.dashboard.index', [
            'title'        => 'Dashboard Peserta',
            'header'       => 'Dashboard Saya',
            'user'         => $user,
            'lamaran'      => $lamaran,
            'state'        => $state,
            'presensi'     => $presensiHariIni,
            'isHariKerja'  => $isHariKerja, // <-- Variabel penting untuk View
        ]);
    }

    /**
     * ----------------------------------------------------------------
     * PRIVATE HELPER METHODS
     * ----------------------------------------------------------------
     */

    /**
     * Menentukan Fase Peserta untuk Tampilan UI
     */
    private function determineState($lamaran)
    {
        // 1. Jika status dasar masih pending/ditolak
        if ($lamaran->status == 'pending' || $lamaran->status == 'ditolak') {
            return $lamaran->status;
        }

        // 2. Jika status 'diterima', kita cek Tanggalnya
        if ($lamaran->status == 'diterima') {
            $today = Carbon::today();

            // Prioritaskan tanggal resmi dari Admin. 
            // Jika admin belum set (bug), fallback ke tanggal usulan.
            $start = Carbon::parse($lamaran->tgl_mulai_resmi ?? $lamaran->tgl_mulai_usulan);
            $end   = Carbon::parse($lamaran->tgl_selesai_resmi ?? $lamaran->tgl_selesai_usulan);

            if ($today->lt($start)) {
                return 'pra_mulai'; // Diterima, tapi belum tanggal masuk
            }

            if ($today->gt($end)) {
                return 'selesai'; // Tanggal magang sudah lewat
            }

            return 'active'; // MASA MAGANG AKTIF (Munculkan Absensi)
        }

        return 'selesai'; // Default fallback
    }

    /**
     * Mengecek presensi kemarin yang bolong (Lupa Checkout)
     */
    private function runAutoCloseCheck($lamaranId)
    {
        $kemarin = Carbon::yesterday()->format('Y-m-d');

        // Cari data: Tanggal kemarin + Sudah Masuk + TAPI Belum Pulang
        $lupaAbsen = Presensi::where('lamaran_id', $lamaranId)
            ->whereDate('tanggal', $kemarin)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->first();

        if ($lupaAbsen) {
            $lupaAbsen->update([
                'jam_pulang'     => '16:00:00', // Set jam pulang default
                'catatan_harian' => 'System Auto-Close: Peserta lupa checkout kemarin.',
                'is_auto_closed' => true
            ]);
        }
    }
}
