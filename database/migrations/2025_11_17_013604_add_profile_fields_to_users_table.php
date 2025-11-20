<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * (Menambahkan kolom untuk fitur Profil - Sesuai Model 190)
     */
    public function up(): void
    {
        // Kita menggunakan Schema::table() untuk MENGUBAH tabel 'users' yang sudah ada
        Schema::table('users', function (Blueprint $table) {
            
            // Kolom untuk menyimpan nama file foto (misal: 'user_5_avatar.png')
            $table->string('foto_profil', 255)->nullable()->after('remember_token');
            
            // Kolom untuk jabatan (misal: 'Teknisi', 'Admin Logistik')
            $table->string('jabatan', 100)->nullable()->after('foto_profil');
            
            // Kolom untuk NIP (Nomor Induk Pegawai)
            $table->string('nip', 50)->nullable()->unique()->after('jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom-kolom jika migrasi di-rollback
            $table->dropColumn(['foto_profil', 'jabatan', 'nip']);
        });
    }
};