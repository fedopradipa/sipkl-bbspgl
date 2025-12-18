<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'file_surat_pengantar',
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

    /**
     * Relasi ke Presensi
     */
    public function presensi()
    {
        return $this->hasMany(\App\Models\Presensi::class, 'lamaran_id');
    }

    
    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah masa magang sudah habis
     */
    public function isExpired(): bool
    {
        if (!$this->tgl_selesai_resmi) {
            return false;
        }

        return Carbon::now()->greaterThan(Carbon::parse($this->tgl_selesai_resmi));
    }

    /**
     * Hitung sisa hari masa magang
     */
    public function getDaysLeftAttribute(): int
    {
        if (!$this->tgl_selesai_resmi) {
            return 0;
        }

        $endDate = Carbon::parse($this->tgl_selesai_resmi);
        $now = Carbon::now();

        return $now->diffInDays($endDate, false);
    }

    /**
     * Cek apakah peserta masih aktif
     */
    public function isActive(): bool
    {
        return $this->status === 'diterima' && !$this->isExpired();
    }

    /**
     * Cek apakah peserta sudah alumni
     */
    public function isAlumni(): bool
    {
        return in_array($this->status, ['selesai', 'berhenti']);
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'diterima' => 'primary',
            'ditolak' => 'danger',
            'selesai' => 'success',
            'berhenti' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'diterima' => 'Aktif',
            'ditolak' => 'Ditolak',
            'selesai' => 'Lulus',
            'berhenti' => 'Berhenti',
            default => 'Unknown'
        };
    }
    public function getTotalHariEfektifAttribute()
    {
        // Jika data belum lengkap, return 0
        if (!$this->tgl_mulai_resmi || !$this->tgl_selesai_resmi || empty($this->hari_kerja_resmi)) {
            return 0;
        }

        $start = Carbon::parse($this->tgl_mulai_resmi);
        $end   = Carbon::parse($this->tgl_selesai_resmi);
        $daysArray = $this->hari_kerja_resmi; // Array ["Senin", "Rabu", ...]

        // Pastikan Locale Indonesia agar nama hari cocok
        // (Sebaiknya diatur di AppServiceProvider, tapi ini untuk memastikan)
        Carbon::setLocale('id');

        $count = 0;

        // Loop dari tanggal mulai sampai selesai
        while ($start->lte($end)) {
            // Cek apakah nama hari ini ada di jadwal peserta
            // translatedFormat('l') menghasilkan "Senin", "Selasa", dst.
            $namaHari = $start->translatedFormat('l');

            if (in_array($namaHari, $daysArray)) {
                $count++;
            }

            $start->addDay();
        }

        return $count;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'diterima');
    }

    public function scopeAlumni($query)
    {
        return $query->whereIn('status', ['selesai', 'berhenti']);
    }

    public function scopeExpiredButActive($query)
    {
        return $query->where('status', 'diterima')
            ->where('tgl_selesai_resmi', '<', Carbon::today());
    }
}
