<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izins';

    protected $fillable = [
        'lamaran_id',
        'jenis_izin',
        'tanggal_izin',
        'alasan',
        'bukti_lampiran',
        'status',
        'catatan_admin'
    ];

    protected $casts = [
        'tanggal_izin' => 'array',
    ];

    // Relasi ke Lamaran
    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'lamaran_id');
    }
    // Helper untuk menghitung total hari
    public function getTotalHariAttribute()
    {
        return count($this->tanggal_izin ?? []);
    }
}
