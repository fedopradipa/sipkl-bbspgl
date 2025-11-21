<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    use HasFactory;
    protected $fillable = [
        'pengguna_id',
        'lowongan_id',
        'status',
        'tgl_mulai_usulan',
        'tgl_selesai_usulan',
        'hari_kerja_usulan',
        'alasan_hari_kerja',
        // ... field lainnya
    ];

    protected $casts = [
        'hari_kerja_usulan' => 'array', // INI WAJIB AGAR OTOMATIS JADI JSON
        'hari_kerja_resmi' => 'array',
        'tgl_mulai_usulan' => 'date',
        'tgl_selesai_usulan' => 'date',
    ];
}
