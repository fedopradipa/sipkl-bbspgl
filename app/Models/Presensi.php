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
    'pengguna_id',
    'lamaran_id',
    'tanggal',
    'jam_masuk',
    'status',
    'catatan_harian',
  ];

  protected $casts = [
    'tanggal' => 'date',
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
    return $this->belongsTo(User::class, 'pengguna_id');
  }
}
