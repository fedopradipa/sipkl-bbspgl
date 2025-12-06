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
        'tgl_mulai',
        'tgl_selesai',
        'alasan',
        'bukti_foto',
        'status',
        'catatan_admin'
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    // Relasi ke Lamaran
    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'lamaran_id');
    }

    // Helper untuk menghitung total hari
    public function getDurasiHariAttribute()
    {
        return $this->tgl_mulai->diffInDays($this->tgl_selesai) + 1;
    }
}
