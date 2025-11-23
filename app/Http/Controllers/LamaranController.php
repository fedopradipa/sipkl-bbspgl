<?php

namespace App\Http\Controllers;

use App\Models\Lamaran; // Pastikan Model ini ada (atau LamaranPkl sesuai file Anda)
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LamaranController extends Controller
{
    /**
     * Menampilkan formulir lamaran
     */
    public function create(Lowongan $lowongan)
    {
        // 1. Security Layer: Cek Status Lowongan
        if (!$lowongan->is_open) {
            return redirect()->route('guest.home')
                ->with('error', 'Mohon maaf, lowongan ini sudah ditutup.');
        }

        // 2. BEST PRACTICE: Cek Lamaran Aktif Global
        // Jika punya lamaran 'pending' atau 'diterima' di TEMPAT LAIN, tolak disini.
        if ($this->hasAnyActiveApplication()) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Anda masih memiliki lamaran aktif. Tunggu verifikasi selesai atau status ditolak untuk melamar kembali.');
        }

        return view('peserta.lamaran.create', [
            'lowongan' => $lowongan,
            'title'    => 'Formulir Pendaftaran PKL',
            'header'   => 'Lamar Posisi: ' . $lowongan->judul
        ]);
    }

    /**
     * Memproses penyimpanan lamaran baru
     */
    public function store(Request $request, Lowongan $lowongan)
    {
        // 1. Cek Security Ulang (Double Check saat Submit)
        if (!$lowongan->is_open || $this->hasAnyActiveApplication()) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Validasi gagal. Lowongan tutup atau Anda memiliki lamaran aktif.');
        }

        // 2. Validasi Input Form (Terpusat)
        $this->validateRequest($request, $lowongan);

        // 3. Validasi Bisnis: Cek Durasi Minimal
        if (!$this->isValidDuration($request, $lowongan)) {
            return back()->withErrors([
                'min_duration' => "Durasi magang kurang. Minimal {$lowongan->min_duration} bulan."
            ])->withInput();
        }

        // 4. Persiapkan Data (Logic Hari Kerja + Translate Indo)
        $finalData = $this->prepareData($request, $lowongan);

        // 5. Simpan ke Database
        Lamaran::create($finalData);

        return redirect()->route('peserta.dashboard')
            ->with('success', 'Lamaran berhasil dikirim! Menunggu verifikasi admin.');
    }

    /**
     * -------------------------------------------------------
     * PRIVATE METHODS (HELPER)
     * -------------------------------------------------------
     */

    /**
     * LOGIKA BARU: Cek apakah user punya lamaran aktif dimanapun
     */
    private function hasAnyActiveApplication()
    {
        // Kita cek apakah user ini punya data di tabel lamaran
        // yang statusnya MASIH 'pending' ATAU 'diterima'.
        return Lamaran::where('pengguna_id', Auth::id())
            ->whereIn('status', ['pending', 'diterima'])
            ->exists();
    }

    private function validateRequest(Request $request, Lowongan $lowongan)
    {
        $rules = [
            'tgl_mulai_usulan'   => 'required|date|after_or_equal:' . $lowongan->window_start_date,
            'tgl_selesai_usulan' => 'required|date|after:tgl_mulai_usulan',
            'hari_kerja'         => $lowongan->is_fulltime ? 'nullable' : 'required|array|min:1',
            'alasan_hari_kerja'  => $lowongan->is_fulltime ? 'nullable' : 'required|string',
        ];

        $messages = [
            'tgl_mulai_usulan.after_or_equal' => 'Tanggal mulai tidak boleh sebelum periode lowongan.',
            'tgl_selesai_usulan.after'        => 'Tanggal selesai harus setelah tanggal mulai.',
            'hari_kerja.required'             => 'Anda wajib memilih minimal 1 hari kerja.',
        ];

        return $request->validate($rules, $messages);
    }

    private function isValidDuration(Request $request, Lowongan $lowongan)
    {
        $start = Carbon::parse($request->tgl_mulai_usulan);
        $end   = Carbon::parse($request->tgl_selesai_usulan);
        return $start->floatDiffInMonths($end) >= $lowongan->min_duration;
    }

    private function prepareData(Request $request, Lowongan $lowongan)
    {
        // Kamus Translate Inggris -> Indo
        $kamusHari = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        if ($lowongan->is_fulltime) {
            $hariKerja = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"];
            $alasan    = "Mengikuti aturan Fulltime (Senin-Jumat)";
        } else {
            // Translate input user
            $hariKerja = array_map(function ($h) use ($kamusHari) {
                return $kamusHari[$h] ?? $h;
            }, $request->hari_kerja);

            $alasan = $request->alasan_hari_kerja;
        }

        return [
            'pengguna_id'        => Auth::id(),
            'lowongan_id'        => $lowongan->id,
            'status'             => 'pending',
            'tgl_mulai_usulan'   => $request->tgl_mulai_usulan,
            'tgl_selesai_usulan' => $request->tgl_selesai_usulan,
            'hari_kerja_usulan'  => $hariKerja,
            'alasan_hari_kerja'  => $alasan,
        ];
    }
}
