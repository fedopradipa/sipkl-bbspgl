<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    use HasFactory;

    // Pastikan nama tabel benar (biasanya jamak/snake_case)
    // Jika di migrasi namanya 'lamaran_pkl', ubah ini jadi 'lamaran_pkl'
    protected $table = 'lamarans';

    protected $fillable = [
        'pengguna_id',
        'lowongan_id',
        'pembimbing_id',
        'status',
        'catatan_admin',
        'tgl_mulai_usulan',
        'tgl_selesai_usulan',
        'hari_kerja_usulan',
        'alasan_hari_kerja',
        'tgl_mulai_resmi',
        'tgl_selesai_resmi',
        'hari_kerja_resmi',
        'path_surat_balasan',
        'path_laporan_akhir'
    ];

    // Casting agar JSON dan Date otomatis terformat
    protected $casts = [
        'hari_kerja_usulan' => 'array', // Penting: JSON -> Array PHP
        'hari_kerja_resmi'  => 'array',
        'tgl_mulai_usulan'  => 'date',
        'tgl_selesai_usulan' => 'date',
        'tgl_mulai_resmi'   => 'date',
        'tgl_selesai_resmi' => 'date',
    ];

    /**
     * RELASI 1: Ke Tabel Lowongan
     * Penting: Pakai withTrashed() agar jika lowongan dihapus admin (soft delete),
     * data lamaran tidak error.
     */
    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'lowongan_id')->withTrashed();
    }

    /**
     * RELASI 2: Ke Tabel Pembimbing
     */
    public function pembimbing()
    {
        return $this->belongsTo(Pembimbing::class, 'pembimbing_id');
    }

    /**
     * RELASI 3: Ke Tabel Pengguna (Peserta)
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id'); // Atau User::class jika model usernya User
    }

    /**
     * Relasi ke Pengajuan Izin
     */
    public function izin()
    {
        return $this->hasMany(PengajuanIzin::class, 'lamaran_id');
    }
}
