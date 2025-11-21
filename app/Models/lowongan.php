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
        'judul',
        'divisi',
        'kuota',
        'deskripsi',
        'is_open',
        'window_start_date',
        'window_end_date',
        'is_fulltime',
        'jam_masuk_standar',
        'jam_pulang_standar',
        'min_duration'
    ];

    protected $casts = [
        'window_start_date' => 'date',
        'window_end_date' => 'date',
        'is_open' => 'boolean',
        'is_fulltime' => 'boolean',
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
            ->whereDate('window_start_date', '<=', Carbon::now())
            ->whereDate('window_end_date', '>=', Carbon::now());
    }

    /**
     * -------------------------------------------------------------
     * ACCESSOR: STATUS TEXT
     * -------------------------------------------------------------
     * Panggil dengan: $lowongan->status_text
     * Digunakan di Tabel Admin untuk memberi tahu jika lowongan expired.
     */
    public function getStatusTextAttribute()
    {
        $now = Carbon::now();

        if (!$this->is_open) {
            return 'Ditutup Admin';
        }

        if ($now->lt($this->window_start_date)) {
            return 'Belum Dibuka';
        }

        if ($now->gt($this->window_end_date)) {
            return 'Kedaluwarsa';
        }

        return 'Dibuka';
    }
}
