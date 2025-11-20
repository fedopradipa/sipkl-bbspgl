<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kita modifikasi tabel 'users' yang sudah ada
        Schema::table('users', function (Blueprint $table) {
            // 1. Tambahkan kolom 'role_id'
            // Kolom ini adalah Foreign Key ke 'id_role' di tabel 'roles'
            // (Sesuai migrasi 'roles' di Model 120)
            $table->foreignId('role_id')
                  ->nullable() // Izinkan null (mungkin ada user tanpa role, atau role default)
                  ->after('email') // Letakkan setelah kolom email (atau 'password')
                  ->constrained('roles', 'id_role') // Terhubung ke tabel 'roles', kolom 'id_role'
                  ->onDelete('set null') // Jika role dihapus, set user.role_id ke NULL (aman)
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Hapus foreign key constraint terlebih dahulu
            // (Laravel otomatis menamai constraint: <nama_tabel>_<nama_kolom>_foreign)
            $table->dropForeign(['role_id']);
            
            // 2. Hapus kolom 'role_id'
            $table->dropColumn('role_id');
        });
    }
};