<?php

namespace App\Services;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Presensi; // Ditambahkan untuk fitur Dashboard Peserta
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LamaranService
{
  /**
   * ====================================================================
   * SECTION 1: LOGIC KHUSUS PESERTA (APPLICANT)
   * Digunakan oleh: LamaranPesertaController, DashboardPesertaController
   * ====================================================================
   */

  /**
   * Cek apakah user memiliki lamaran yang sedang berjalan (Pending/Aktif).
   * Mencegah double application.
   */
  public function hasActiveApplication(int $userId): bool
  {
    return Lamaran::where('pengguna_id', $userId)
      ->whereIn('status', ['pending', 'diterima'])
      ->exists();
  }

  /**
   * Cek apakah user masuk daftar hitam (Blacklist).
   * Mencegah user yang dipecat (Drop Out Pelanggaran) melamar lagi.
   */
  public function isBlacklisted(int $userId): bool
  {
    return Lamaran::where('pengguna_id', $userId)
      ->where('status', 'berhenti')
      ->where('catatan_admin', 'LIKE', '%Diberhentikan%')
      ->exists();
  }

  /**
   * Membuat Lamaran Baru (Store).
   * Membungkus proses prepare data dan insert DB dalam transaksi.
   */
  public function createLamaran(array $data, Lowongan $lowongan): Lamaran
  {
    return DB::transaction(function () use ($data, $lowongan) {
      $preparedData = $this->prepareData($data, $lowongan);
      return Lamaran::create($preparedData);
    });
  }

  /**
   * Menentukan State Tampilan Dashboard Peserta.
   * Mengubah logika status DB menjadi status UI (pra_mulai, active, selesai).
   */
  public function determineDashboardState(Lamaran $lamaran): string
  {
    // 1. Status Statis (langsung return)
    if (in_array($lamaran->status, ['pending', 'ditolak', 'selesai', 'berhenti'])) {
      return $lamaran->status;
    }

    // 2. Status 'diterima' (butuh cek tanggal)
    if ($lamaran->status == 'diterima') {
      $today = Carbon::today();
      // Prioritaskan tanggal resmi, fallback ke usulan jika null
      $start = Carbon::parse($lamaran->tgl_mulai_resmi ?? $lamaran->tgl_mulai_usulan);
      $end   = Carbon::parse($lamaran->tgl_selesai_resmi ?? $lamaran->tgl_selesai_usulan);

      if ($today->lt($start)) return 'pra_mulai'; // Belum tanggal masuk
      if ($today->gt($end))   return 'finalisasi';   // Tanggal lewat (Menunggu Admin Luluskan)

      return 'active'; // Masa Magang Aktif
    }

    return 'kosong';
  }

  /**
   * Cek apakah HARI INI adalah jadwal kerja peserta.
   * Digunakan untuk mengaktifkan tombol presensi.
   */
  public function isWorkingDay(Lamaran $lamaran): bool
  {
    $hariIndo = [
      'Sunday' => 'Minggu',
      'Monday' => 'Senin',
      'Tuesday' => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday' => 'Kamis',
      'Friday' => 'Jumat',
      'Saturday' => 'Sabtu'
    ];

    $hariIni = $hariIndo[Carbon::now()->format('l')];
    $jadwal  = $lamaran->hari_kerja_resmi ?? []; // Array jadwal dari DB

    return is_array($jadwal) && in_array($hariIni, $jadwal);
  }

  /**
   * Mengambil data presensi hari ini (jika ada).
   */
  public function getTodayPresensi(int $lamaranId): ?Presensi
  {
    return Presensi::where('lamaran_id', $lamaranId)
      ->whereDate('tanggal', Carbon::today())
      ->first();
  }

  /**
   * Menjalankan Auto-Close Absensi Kemarin (System Maintenance).
   * Menutup absen yang lupa checkout.
   */
  public function runAutoCloseCheck(int $lamaranId): void
  {
    $lupaAbsen = Presensi::where('lamaran_id', $lamaranId)
      ->whereDate('tanggal', Carbon::yesterday())
      ->whereNotNull('jam_masuk')
      ->whereNull('jam_pulang')
      ->first();

    if ($lupaAbsen) {
      $lupaAbsen->update([
        'jam_pulang'     => '16:00:00',
        'catatan_harian' => 'System Auto-Close: Lupa checkout.',
        'status'         => 'hadir'
      ]);
    }
  }


  /**
   * ====================================================================
   * SECTION 2: LOGIC KHUSUS ADMIN (VERIFICATOR)
   * Digunakan oleh: LamaranAdminController
   * ====================================================================
   */

  /**
   * Menyetujui Lamaran (Approve Awal).
   * Mengupdate status jadi 'diterima' dan menyimpan jadwal resmi.
   */
  public function approve(Lamaran $lamaran, array $data): Lamaran
  {
    return DB::transaction(function () use ($lamaran, $data) {
      $lamaran->update([
        'status'            => 'diterima',
        'pembimbing_id'     => $data['pembimbing_id'],
        'tgl_mulai_resmi'   => $data['tgl_mulai_resmi'],
        'tgl_selesai_resmi' => $data['tgl_selesai_resmi'],
        'hari_kerja_resmi'  => $data['hari_kerja_resmi'],
      ]);

      return $lamaran;
    });
  }

  /**
   * Menolak Lamaran (Reject).
   */
  public function reject(Lamaran $lamaran, string $reason): Lamaran
  {
    return DB::transaction(function () use ($lamaran, $reason) {
      $lamaran->update([
        'status'        => 'ditolak',
        'catatan_admin' => $reason,
      ]);

      return $lamaran;
    });
  }

  /**
   * Mengubah Status Akhir (Lulus / Drop Out).
   * Menangani logika 'Cut-off' tanggal jika berhenti di tengah jalan.
   */
  public function updateStatus(Lamaran $lamaran, string $status, ?array $options = []): Lamaran
  {
    return DB::transaction(function () use ($lamaran, $status, $options) {

      if ($status === 'berhenti') {
        // LOGIC CUT-OFF: Set tanggal selesai jadi HARI INI
        $lamaran->status = 'berhenti';
        $lamaran->tgl_selesai_resmi = now();

        // Format Catatan: "Kategori - Detail Alasan"
        $kategori = $options['kategori'] ?? 'Berhenti';
        $detail   = $options['catatan_detail'] ?? ($options['catatan'] ?? '-');
        $lamaran->catatan_admin = "$kategori - $detail";
      } elseif ($status === 'selesai') {
        // LOGIC LULUS
        $lamaran->status = 'selesai';
        // Tanggal selesai dibiarkan sesuai jadwal asli
      } else {
        // Fallback untuk status lain
        $lamaran->status = $status;
        if (!empty($options['catatan'])) {
          $lamaran->catatan_admin = $options['catatan'];
        }
      }

      $lamaran->save();
      return $lamaran;
    });
  }


  /**
   * ====================================================================
   * SECTION 3: PRIVATE HELPER (INTERNAL SERVICE USE ONLY)
   * ====================================================================
   */

  /**
   * Membersihkan dan mempersiapkan data input dari form lamaran.
   * Translate hari Inggris -> Indo, dan set jadwal Fulltime otomatis.
   */
  private function prepareData(array $input, Lowongan $lowongan): array
  {
    $kamusHari = [
      'Monday' => 'Senin',
      'Tuesday' => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday' => 'Kamis',
      'Friday' => 'Jumat',
      'Saturday' => 'Sabtu',
      'Sunday' => 'Minggu'
    ];

    if ($lowongan->is_fulltime) {
      // Jika null (antisipasi error), fallback ke Senin-Jumat standar
      $hariKerja = $lowongan->working_days ?? ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"];
      $alasan    = "Mengikuti aturan jadwal terikat (Fulltime) dari lowongan";
    } else {
      // Logika Flexible: User yang memilih hari
      // Kita mapping nama hari Inggris (dari input datepicker/checkbox) ke Indo
      $inputHari = $input['hari_kerja'] ?? [];

      $hariKerja = array_map(function ($h) use ($kamusHari) {
        return $kamusHari[$h] ?? $h;
      }, $inputHari);

      $alasan = $input['alasan_hari_kerja'] ?? '-';
    }

    return [
      'pengguna_id'        => Auth::id(),
      'lowongan_id'        => $lowongan->id,
      'status'             => 'pending',
      'tgl_mulai_usulan'   => $input['tgl_mulai_usulan'],
      'tgl_selesai_usulan' => $input['tgl_selesai_usulan'],
      'hari_kerja_usulan'  => $hariKerja,
      'alasan_hari_kerja'  => $alasan,
      'file_surat_pengantar' => $input['file_surat_pengantar'] ?? null,
    ];
  }
}
