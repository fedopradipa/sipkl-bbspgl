<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lamaran;
use App\Models\Presensi;
use App\Models\PengajuanIzin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoAlphaPresensi extends Command
{
    protected $signature = 'presensi:auto-alpha';
    protected $description = 'Otomatis set Alpha jika hari kerja, sudah lewat jam_tutup_absen, dan belum absen';

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Map hari ke Indonesia
        $namaHariInggris = Carbon::today()->format('l');
        $mapHari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hariIniIndo = $mapHari[$namaHariInggris] ?? $namaHariInggris;

        $this->info("Memproses auto-alpha tanggal: $today ($hariIniIndo) ...");

        DB::beginTransaction();
        try {
            // Ambil peserta aktif yang hari ini termasuk hari kerja
            // sekaligus ambil lowongan untuk cek jam_tutup_absen
            $pesertaAktif = Lamaran::with('lowongan')
                ->where('status', 'diterima')
                ->whereDate('tgl_mulai_resmi', '<=', $today)
                ->whereDate('tgl_selesai_resmi', '>=', $today)
                ->whereJsonContains('hari_kerja_resmi', $hariIniIndo)
                ->get();

            $now = Carbon::now();
            $countAlpha = 0;

            foreach ($pesertaAktif as $peserta) {
                // Ambil jam tutup absen dari lowongan
                $jamTutup = $peserta->lowongan->jam_tutup_absen ?? null;
                if (!$jamTutup) {
                    // kalau tidak ada jam tutup, skip biar tidak salah
                    continue;
                }

                $batasTutup = Carbon::today()->setTimeFrom(Carbon::parse($jamTutup));

                // Kalau sekarang belum lewat jam tutup, jangan alpha dulu
                if ($now->lt($batasTutup)) {
                    continue;
                }

                // Jika sudah ada presensi hari ini, skip
                $sudahAbsen = Presensi::where('lamaran_id', $peserta->id)
                    ->whereDate('tanggal', $today)
                    ->exists();

                if ($sudahAbsen) {
                    continue;
                }

                // Jika ada izin/sakit disetujui untuk hari ini, skip
                $adaIzin = PengajuanIzin::where('lamaran_id', $peserta->id)
                    ->where('status', 'disetujui')
                    ->whereJsonContains('tanggal_izin', $today)
                    ->exists();

                if ($adaIzin) {
                    continue;
                }

                // Buat alpha
                Presensi::create([
                    'lamaran_id'     => $peserta->id,
                    'pengguna_id'    => $peserta->pengguna_id,
                    'tanggal'        => $today,
                    'status'         => 'alpha',
                    'jam_masuk'      => null,
                    'catatan_harian' => 'Alpha (Tidak ada presensi hingga jam tutup absen)',
                ]);

                $countAlpha++;
            }

            DB::commit();
            $this->info("Selesai. Berhasil generate $countAlpha data Alpha.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
