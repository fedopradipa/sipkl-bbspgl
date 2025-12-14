<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
  use HasFactory;

  // PENTING: Karena nama tabel di migrasi 'presensi' (bukan presensis), 
  // kita harus definisikan secara manual.
  protected $table = 'presensis';

  protected $fillable = [
    'user_id',
    'lamaran_id',
    'tanggal',
    'jam_masuk',
    'jam_pulang',
    'status',           // hadir, sakit, izin, alpha
    'lat_masuk',
    'long_masuk',
    'lat_pulang',
    'long_pulang',
    'catatan_harian',
    'is_auto_closed'
  ];

  protected $casts = [
    'tanggal' => 'date',
    'is_auto_closed' => 'boolean',
    // jam_masuk dan jam_pulang biarkan string/time agar mudah dimanipulasi
  ];

  /**
   * Relasi ke Lamaran
   * (Setiap data absen pasti milik satu lamaran)
   */
  public function lamaran()
  {
    return $this->belongsTo(Lamaran::class, 'lamaran_id');
  }

  // Relasi ke User 
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
