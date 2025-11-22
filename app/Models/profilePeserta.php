<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilePeserta extends Model
{
    use HasFactory;

    // WAJIB: Definisikan nama tabel karena tidak sesuai konvensi jamak standar Laravel
    protected $table = 'profil_peserta';

    protected $fillable = [
        'pengguna_id',
        'nim_nis',
        'institusi',
        'jurusan', // Jika ada di migrasi Anda
        'no_hp',
        'alamat' // Jika ada di migrasi Anda
    ];

    // Relasi ke Model User (Pengguna)
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
