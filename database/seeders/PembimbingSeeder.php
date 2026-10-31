<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pembimbing;

class PembimbingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Teknisi / IT Support (Data Awal Anda)
        Pembimbing::create([
            'nama' => 'Robin',
            'jabatan' => 'Teknisi',
            'no_hp' => '082217327502',
            'is_active' => true,
        ]);

        // --- TAMBAHAN DATA ---

        // 2. Senior IT (Untuk membimbing Web Dev & UI/UX)
        Pembimbing::create([
            'nama' => 'Aditya Pratama',
            'jabatan' => 'Senior Web Developer',
            'no_hp' => '081234567890',
            'is_active' => true,
        ]);

        // 3. HRD (Untuk membimbing Recruitment Intern & Staff Admin)
        Pembimbing::create([
            'nama' => 'Siti Aminah',
            'jabatan' => 'HR Manager',
            'no_hp' => '081398765432',
            'is_active' => true,
        ]);

        // 4. Creative Lead (Untuk membimbing Graphic Designer & Video Editor)
        Pembimbing::create([
            'nama' => 'Dimas Anggara',
            'jabatan' => 'Creative Director',
            'no_hp' => '085712340987',
            'is_active' => true,
        ]);

        // 5. Finance (Untuk membimbing Staff Finance)
        Pembimbing::create([
            'nama' => 'Ratna Sari',
            'jabatan' => 'Kepala Divisi Keuangan',
            'no_hp' => '082156784321',
            'is_active' => true,
        ]);

        // 6. Marketing (Untuk membimbing Social Media Specialist)
        Pembimbing::create([
            'nama' => 'Bayu Nugroho',
            'jabatan' => 'Digital Marketing Lead',
            'no_hp' => '087811223344',
            'is_active' => true,
        ]);

        // 7. Contoh Pembimbing Non-Aktif (Misal: Sedang Cuti Panjang / Non-job)
        Pembimbing::create([
            'nama' => 'Indah Pertiwi',
            'jabatan' => 'General Affair Supervisor',
            'no_hp' => '089677889900',
            'is_active' => false, // Set false untuk testing filter
        ]);
    }
}
