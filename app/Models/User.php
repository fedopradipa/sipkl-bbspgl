<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ProfilePeserta;
use App\Models\ProfileAdmin; // <-- WAJIB: Tambah import ProfilAdmin

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_role');
    }

    // ===========================================
    // RELASI PROFIL (WAJIB DIBUAT)
    // ===========================================

    // Relasi 1: Profil Admin/Pembimbing
    public function adminProfile()
    {
        return $this->hasOne(ProfileAdmin::class, 'user_id');
    }

    // Relasi 2: Profil Peserta (Menggantikan 'biodata()')
    public function participantProfile()
    {
        return $this->hasOne(ProfilePeserta::class, 'pengguna_id');
    }

    // [OPSIONAL]: Pertahankan biodata() sebagai alias agar kode lama tidak error
    public function biodata()
    {
        return $this->participantProfile();
    }
    public function profile()
    {
        // Arahkan ke participantProfile (karena view peserta aktif butuh data peserta)
        return $this->participantProfile();
    }

    // =================================================================
    // BAGIAN PENTING: HELPER ROLE (Sama seperti sebelumnya, tidak diubah)
    // =================================================================

    public function isSuperadmin(): bool
    {
        return $this->role && strtolower($this->role->nama_role) === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role && strtolower($this->role->nama_role) === 'admin';
    }

    public function isPeserta(): bool
    {
        return $this->role && strtolower($this->role->nama_role) === 'peserta';
    }

    // ===========================================
    // ACCESSOR UNTUK ROUTE EDIT PROFIL
    // ===========================================

    public function getEditProfileRouteAttribute()
    {
        $role = $this->role->nama_role ?? 'guest';

        if ($role === 'peserta') {
            return route('profile.peserta.edit');
        }

        if ($role === 'admin' || $role === 'superadmin') {
            // Mengarahkan ke route general untuk Admin/Superadmin
            return route('profile.general.edit');
        }

        return '#';
    }

    // public function participantProfile() { ... }

    // ACCESSOR BARU: Ambil foto profil secara dinamis
    public function getFotoProfilAttribute()
    {
        // A. Jika dia Admin/Superadmin
        if ($this->isAdmin() || $this->isSuperadmin()) {
            // Asumsi foto Admin ada di kolom 'foto_profil' di tabel 'profil_admins'
            return optional($this->adminProfile)->foto_profil;
        }

        // B. Jika dia Peserta
        if ($this->isPeserta()) {
            // Asumsi foto Peserta ada di kolom 'foto_profil' di tabel 'profil_peserta'
            return optional($this->participantProfile)->foto_profil;
        }

        return null; // Jika role tidak terdefinisi
    }
}
