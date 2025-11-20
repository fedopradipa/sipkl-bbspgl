<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'roles';

    /**
     * Primary key untuk tabel.
     * (Sesuai permintaan Anda di Model 120)
     */
    protected $primaryKey = 'id_role';

    /**
     * Kolom yang boleh diisi secara massal (mass assignable).
     */
    protected $fillable = ['nama_role'];

    /**
     * Menonaktifkan timestamps (created_at, updated_at)
     * karena kita tidak menambahkannya di migrasi (Model 120).
     */
    public $timestamps = false;

    /**
     * Relasi "One-to-Many": Satu Role dimiliki oleh Banyak User.
     * (Sesuai diskusi arsitektur kita di Model 118).
     */
    public function users()
    {
        // Foreign key di tabel 'users' adalah 'role_id'
        // Primary key di tabel 'roles' ini adalah 'id_role'
        return $this->hasMany(User::class, 'role_id', 'id_role');
    }
}