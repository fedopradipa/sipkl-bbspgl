<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'foto_profil',
        'jabatan',
        'nip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi "One-to-Many" (Inverse): Satu User memiliki satu Role.
     * (Sesuai diskusi arsitektur kita di Model 118).
     */
    public function role()
    {
        // Foreign key di tabel 'users' ini adalah 'role_id'
        // Primary key di tabel 'roles' adalah 'id_role'
        return $this->belongsTo(Role::class, 'role_id', 'id_role');
    }

// =================================================================
    // BAGIAN PENTING: HELPER ROLE (WAJIB ADA)
    // =================================================================

    /**
     * Cek apakah user adalah Superadmin
     */
    public function isSuperadmin(): bool
    {
        // Menggunakan strtolower agar aman dari perbedaan huruf besar/kecil
        return $this->role && strtolower($this->role->nama_role) === 'superadmin';
    }

    /**
     * Cek apakah user adalah Admin
     * (Fungsi ini WAJIB ADA karena dipanggil oleh Middleware/View)
     */
    public function isAdmin(): bool
    {
        return $this->role && strtolower($this->role->nama_role) === 'admin';
    }

    /**
     * Cek apakah user adalah Peserta
     * (Wajib ada untuk logika dashboard peserta)
     */
    public function isPeserta(): bool
    {
        return $this->role && strtolower($this->role->nama_role) === 'peserta';
    }
}
