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
        Pembimbing::create([
            'nama' => 'Robin',
            'jabatan' => 'Teknisi',
            'no_hp' => '082217327502',
            'is_active' => true,
        ]);
    }
}
