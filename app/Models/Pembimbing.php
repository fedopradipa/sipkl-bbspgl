<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembimbing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembimbings'; // Pastikan nama tabel sesuai migrasi (singular)

    protected $fillable = [
        'nama',
        'jabatan',
        'no_hp',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    /**
     * EVENT MODEL: Otomatis set is_active = false saat dihapus
     */
    protected static function booted()
    {
        static::deleting(function ($pembimbing) {
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($pembimbing))) {
                if (!$pembimbing->isForceDeleting()) {
                    $pembimbing->is_active = false;
                    $pembimbing->save();
                }
            } else {
                // Jika hard delete biasa, tidak perlu update karena data hilang
            }
        });
    }
}
