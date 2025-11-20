<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; // Pastikan Model Role Anda sudah dibuat (dari Model 121)

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan firstOrCreate untuk menghindari duplikat jika seeder dijalankan lagi
        
        // 1. Buat Superadmin (ID 1)
        Role::firstOrCreate(
            ['id_role' => 1], // Cek berdasarkan Primary Key (id_role)
            ['nama_role' => 'superadmin'] // Data yang akan dibuat jika tidak ada
        );
        
        // 2. Buat Admin (ID 2)
        Role::firstOrCreate(
            ['id_role' => 2], // Cek berdasarkan Primary Key (id_role)
            ['nama_role' => 'admin'] // Data yang akan dibuat jika tidak ada
        );
    }
}