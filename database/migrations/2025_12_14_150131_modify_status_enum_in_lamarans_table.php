<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Menambahkan status 'berhenti' ke dalam ENUM
        // Menggunakan raw SQL agar kompatibel dengan existing data
        DB::statement("ALTER TABLE lamarans MODIFY COLUMN status ENUM('pending', 'diterima', 'ditolak', 'selesai', 'berhenti') DEFAULT 'pending'");
    }

    public function down()
    {
        // Rollback (Hati-hati, data 'berhenti' bisa error jika di-rollback)
        DB::statement("ALTER TABLE lamarans MODIFY COLUMN status ENUM('pending', 'diterima', 'ditolak', 'selesai') DEFAULT 'pending'");
    }
};
