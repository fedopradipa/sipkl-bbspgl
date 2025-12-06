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
        // Contoh 1: IT
        Lowongan::create([
            'judul' => 'Web Developer Intern (Laravel)',
            'divisi' => 'Teknologi Informasi',
            'deskripsi' => 'Membangun sistem informasi internal.',
            'is_open' => true,
            'window_start_date' => Carbon::now(),
            'window_end_date' => Carbon::now()->addMonth(),
            'min_duration' => 1,

            'jurusan_terkait' => 'Teknik Informatika, Sistem Informasi, RPL',
            'fasilitas' => 'Makan Siang Gratis, Sertifikat Resmi.',
        ]);

        // Contoh 2: Admin
        Lowongan::create([
            'judul' => 'Staff Administrasi',
            'divisi' => 'Tata Usaha',
            'deskripsi' => 'Membantu pengarsipan dokumen.',
            'is_open' => true,
            'window_start_date' => Carbon::now(),
            'window_end_date' => Carbon::now()->addWeeks(2),
            'min_duration' => 1,

            'jurusan_terkait' => 'Administrasi Perkantoran, Manajemen',
            'fasilitas' => 'Sertifikat Magang, Ruang Kerja Nyaman.',
        ]);
    }
}
