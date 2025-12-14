<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileAdmin extends Model
{
  use HasFactory;

  // Nama tabel harus sesuai dengan migrasi Anda ('profile_admins')
  protected $table = 'profile_admins';

  // Kolom yang boleh diisi (WAJIB, sesuai field di migrasi)
  protected $fillable = [
    'user_id',
    'nip',
    'jabatan',
    'unit_kerja', // Opsional, jika Anda menambahkannya
    'no_hp',
    'foto_profil',
  ];

  // Relasi kembali ke model User
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  // Relasi untuk Lamaran (jika Admin ini ditetapkan sebagai Pembimbing)
  // Asumsi Foreign Key di lamarans adalah 'pembimbing_id'
  public function lamaransDibimbing()
  {
    return $this->hasMany(Lamaran::class, 'pembimbing_id');
  }
}
