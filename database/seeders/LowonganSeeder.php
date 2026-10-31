<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lowongan;
use Carbon\Carbon;

class LowonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // --- Data Lama (IT & Admin) ---
        Lowongan::create([
            'posisi' => 'Web Developer Intern (Laravel)',
            'deskripsi' => 'Membangun sistem informasi internal.',
            'is_open' => true,
            'window_start_date' => Carbon::now(),
            'window_end_date' => Carbon::now()->addMonth(),
            'min_duration' => 3, // Ubah ke 3 bulan (standar magang)
            'jurusan_terkait' => 'Teknik Informatika, Sistem Informasi, RPL',
        ]);

        Lowongan::create([
            'posisi' => 'Staff Administrasi',
            'deskripsi' => 'Membantu pengarsipan dokumen dan input data.',
            'is_open' => true,
            'window_start_date' => Carbon::now(),
            'window_end_date' => Carbon::now()->addWeeks(2),
            'min_duration' => 3,
            'jurusan_terkait' => 'Administrasi Perkantoran, Manajemen',
        ]);

        // --- TAMBAHAN BARU ---

        // 3. Bidang Kreatif / Desain
        Lowongan::create([
            'posisi' => 'Graphic Designer Intern',
            'deskripsi' => 'Membuat aset visual untuk media sosial dan materi pemasaran perusahaan.',
            'is_open' => true,
            'window_start_date' => Carbon::now()->subDays(2), // Sudah buka 2 hari lalu
            'window_end_date' => Carbon::now()->addWeeks(3),
            'min_duration' => 3,
            'jurusan_terkait' => 'Desain Komunikasi Visual (DKV), Multimedia, Seni Rupa',
        ]);

        // 4. Bidang Marketing
        Lowongan::create([
            'posisi' => 'Social Media Specialist',
            'deskripsi' => 'Merancang content plan, copywriting, dan analisa engagement Instagram & TikTok.',
            'is_open' => true,
            'window_start_date' => Carbon::now(),
            'window_end_date' => Carbon::now()->addMonth(),
            'min_duration' => 3,
            'jurusan_terkait' => 'Ilmu Komunikasi, Pemasaran, Bisnis Digital',
        ]);

        // 5. Bidang Keuangan
        Lowongan::create([
            'posisi' => 'Finance & Accounting Staff',
            'deskripsi' => 'Membantu penyusunan laporan keuangan bulanan dan verifikasi invoice.',
            'is_open' => true,
            'window_start_date' => Carbon::now(),
            'window_end_date' => Carbon::now()->addDays(10), // Waktu singkat
            'min_duration' => 6, // Magang lebih lama
            'jurusan_terkait' => 'Akuntansi, Manajemen Keuangan, Perbankan',
        ]);

        // 6. Bidang HRD
        Lowongan::create([
            'posisi' => 'Recruitment Intern (HR)',
            'deskripsi' => 'Membantu proses screening CV, penjadwalan interview, dan onboarding karyawan baru.',
            'is_open' => false, // Contoh lowongan yang sudah tutup
            'window_start_date' => Carbon::now()->subMonth(),
            'window_end_date' => Carbon::now()->subDay(),
            'min_duration' => 3,
            'jurusan_terkait' => 'Psikologi, Manajemen SDM, Hukum',
        ]);

        // 7. Bidang IT (Varian Lain)
        Lowongan::create([
            'posisi' => 'UI/UX Designer',
            'deskripsi' => 'Melakukan riset pengguna dan membuat wireframe/prototype aplikasi mobile.',
            'is_open' => true,
            'window_start_date' => Carbon::now(),
            'window_end_date' => Carbon::now()->addWeeks(4),
            'min_duration' => 3,
            'jurusan_terkait' => 'Sistem Informasi, DKV, Teknik Informatika',
        ]);

        // 8. Bidang Videografi
        Lowongan::create([
            'posisi' => 'Video Editor',
            'deskripsi' => 'Mengedit video untuk konten Youtube dan Reels, serta pengambilan gambar (shooting).',
            'is_open' => true,
            'window_start_date' => Carbon::now(),
            'window_end_date' => Carbon::now()->addMonth(),
            'min_duration' => 3,
            'jurusan_terkait' => 'Broadcasting, Multimedia, Perfilman',
        ]);
    }
}
