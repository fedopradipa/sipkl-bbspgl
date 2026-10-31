<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Lowongan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'posisi',
        'deskripsi',
        'is_open',
        'window_start_date',
        'window_end_date',
        'is_fulltime',
        'jam_masuk_standar',
        'min_duration',
        'jurusan_terkait',
        'hari_kerja',
        'jam_buka_absen',
        'jam_masuk_standar',
        'jam_tutup_absen',


    ];

    protected $casts = [
        'window_start_date' => 'date',
        'window_end_date' => 'date',
        'is_open' => 'boolean',
        'is_fulltime' => 'boolean',
        'hari_kerja' => 'array',
    ];
    /**

     * EVENT MODEL: Otomatis set is_open = false saat dihapus (Soft Delete)
     */
    protected static function booted()
    {
        static::deleting(function ($lowongan) {
            if (!$lowongan->isForceDeleting()) {
                $lowongan->is_open = false;
                $lowongan->save();
            }
        });
    }
    /**
     * -------------------------------------------------------------
     * SCOPE: QUERY FILTER OTOMATIS
     * -------------------------------------------------------------
     * Panggil dengan: Lowongan::tersedia()->get();
     * Digunakan di Dashboard Tamu/Peserta agar lowongan basi tidak muncul.
     */
    public function scopeTersedia($query)
    {
        return $query->where('is_open', true)
            // Cek 1: Apakah tanggal mulai sudah tiba (Hari ini atau sebelum)
            ->whereDate('window_start_date', '<=', Carbon::today())
            // Cek 2: Apakah tanggal akhir belum terlewati (Hari ini atau setelah)
            ->whereDate('window_end_date', '>=', Carbon::today());
    }
    /**
     * -------------------------------------------------------------
     * ACCESSOR: STATUS TEXT
     * -------------------------------------------------------------
     * Panggil dengan: $lowongan->status_text
     * Digunakan di Tabel Admin untuk memberi tahu jika lowongan expired.
     */
    // File: App\Models\Lowongan.php

    public function getStatusTextAttribute()
    {
        $now = Carbon::now();

        // Pastikan is_open di cek dulu
        if (!$this->is_open) {
            return 'Ditutup Admin'; // Admin manual tutup
        }

        // Tentukan tanggal akhir yang sebenarnya (akhir hari)
        $windowEndDateAtMidnight = $this->window_end_date->endOfDay();

        if ($now->lt($this->window_start_date)) {
            return 'Belum Dibuka'; // Tanggal belum mulai
        }

        // PERBAIKAN UTAMA: Kita bandingkan dengan akhir hari (23:59:59)
        if ($now->gt($windowEndDateAtMidnight)) {
            return 'Kedaluwarsa';
        }

        return 'Dibuka'; // Aktif & Dalam Periode
    }
}
