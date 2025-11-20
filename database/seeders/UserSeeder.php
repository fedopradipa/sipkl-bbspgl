<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Model User bawaan Laravel (sudah kita edit)
use App\Models\Role;
use Illuminate\Support\Facades\Hash; // Untuk enkripsi password

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari Role 'superadmin' (yang ID-nya 1, dari RoleSeeder)
        $superadminRole = Role::find(1);

        // 2. Pastikan role-nya ada
        if ($superadminRole) {
            
            // 3. Buat User Superadmin (sesuai permintaan Anda)
            // Gunakan firstOrCreate untuk menghindari duplikat email
            User::firstOrCreate(
                ['email' => 'haris@gmail.com'], // Cek berdasarkan email
                [
                    'name' => 'Haris (Superadmin)', // Anda bisa ubah namanya nanti
                    'password' => Hash::make('haris123'), // Enkripsi password 'haris123'
                    'role_id' => $superadminRole->id_role // Hubungkan ke role_id 1
                ]
            );

        } else {
            // Beri peringatan di terminal jika RoleSeeder belum dijalankan
            $this->command->error('Role "superadmin" (ID 1) tidak ditemukan. Pastikan RoleSeeder dijalankan terlebih dahulu.');
        }
    }
}