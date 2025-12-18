<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lamaran;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoTutupPresensi extends Command
{
    /**
     * Nama perintah yang akan dijalankan (misal: php artisan presensi:autoclose)
     */
    protected $signature = 'presensi:autoclose';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Otomatis set Alpha jika tidak hadir, dan Auto-Checkout jika lupa pulang';

    public function handle()
    {
        $kemarin = Carbon::yesterday()->format('Y-m-d');
        // Kita gunakan 'kemarin' karena script ini biasanya jalan jam 01:00 pagi
        // untuk memproses data hari sebelumnya.

        // Cek hari apa kemarin (untuk filter jadwal kerja)
        // Format Carbon 'l' -> Monday, Tuesday, dst.
        $namaHariInggris = Carbon::yesterday()->format('l');
        $mapHari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hariKemarinIndo = $mapHari[$namaHariInggris];

        $this->info("Memproses data tanggal: $kemarin ($hariKemarinIndo)...");

        DB::beginTransaction();
        try {
            // ============================================================
            // 1. KASUS: LUPA CHECKOUT (Hadir tapi Jam Pulang Kosong)
            // ============================================================
            $lupaPulang = Presensi::whereDate('tanggal', $kemarin)
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_pulang')
                ->get();

            foreach ($lupaPulang as $row) {
                // Ambil jam pulang standar dari lowongan dia
                $jamStandar = $row->lamaran->lowongan->jam_pulang_standar ?? '16:00:00';

                $row->update([
                    'jam_pulang' => $jamStandar, // Set ke jam pulang standar
                    'catatan_harian' => $row->catatan_harian . ' (Auto-Checkout by System)',
                    'is_auto_closed' => true // Flag tanda sistem yang update
                ]);
            }
            $this->info("Berhasil update " . $lupaPulang->count() . " peserta yang lupa checkout.");

            // ============================================================
            // 2. KASUS: TIDAK ADA KABAR (Otomatis Alpha)
            // ============================================================

            // Ambil semua peserta aktif yang SEHARUSNYA masuk kemarin
            $pesertaAktif = Lamaran::where('status', 'diterima')
                ->whereDate('tgl_mulai_resmi', '<=', $kemarin)
                ->whereDate('tgl_selesai_resmi', '>=', $kemarin)
                ->whereJsonContains('hari_kerja_resmi', $hariKemarinIndo) // Cek Jadwal
                ->get();

            $countAlpha = 0;
            foreach ($pesertaAktif as $peserta) {
                // Cek apakah dia punya data presensi di tanggal kemarin?
                $cekAbsen = Presensi::where('lamaran_id', $peserta->id)
                    ->whereDate('tanggal', $kemarin)
                    ->exists();

                // Jika TIDAK ADA data sama sekali -> Buatkan Alpha
                if (!$cekAbsen) {
                    Presensi::create([
                        'lamaran_id' => $peserta->id,
                        'user_id'    => $peserta->pengguna_id,
                        'tanggal'    => $kemarin,
                        'status'     => 'alpha',
                        'jam_masuk'  => null,
                        'jam_pulang' => null,
                        'catatan_harian' => 'Alpha (Tidak ada kabar hingga pergantian hari)',
                        'is_auto_closed' => true
                    ]);
                    $countAlpha++;
                }
            }
            $this->info("Berhasil generate $countAlpha data Alpha.");

            DB::commit();
            $this->info('Selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
