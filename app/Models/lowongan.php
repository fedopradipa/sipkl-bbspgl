<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
