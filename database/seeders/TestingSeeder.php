<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProfilePeserta; // Sesuaikan jika nama modelnya UserProfile
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pembimbing;
use App\Models\Presensi;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestingSeeder extends Seeder
{
  public function run()
  {
    DB::transaction(function () {
      $this->command->info('Memulai seeding data testing (Updated Schema)...');

      // 0. AMBIL ID ROLE PESERTA
      $rolePeserta = Role::where('nama_role', 'peserta')->first();

      if (!$rolePeserta) {
        $this->command->error('Role "peserta" tidak ditemukan! Jalankan RoleSeeder dulu.');
        return;
      }

      // 1. BUAT DATA MASTER
      $pembimbing = Pembimbing::create([
        'nama' => 'Budi Santoso, S.T.',
        'jabatan' => 'Teknologi Informasi',
        'no_hp' => '081234567890',
        'is_active' => true
      ]);

      $lowongan = Lowongan::create([
        'judul' => 'Programmer Web Apps',
        'divisi' => 'IT Development',
        'deskripsi' => 'Lowongan untuk testing sistem.',
        'is_open' => true,
        'is_fulltime' => true,
        'window_start_date' => now()->subMonth(),
        'window_end_date' => now()->addMonth(),
        'min_duration' => 1,
        'jurusan_terkait' => 'Teknik Informatika, Sistem Informasi, RPL',
        'fasilitas' => 'Makan Siang Gratis, Sertifikat Resmi.',
      ]);

      // 2. BUAT 6 USER PESERTA
      $scenarios = [
        ['name' => 'Peserta Pending', 'email' => 'pending@test.com', 'status' => 'pending'],
        ['name' => 'Peserta Aktif', 'email' => 'aktif@test.com', 'status' => 'diterima'],
        ['name' => 'Peserta Ditolak', 'email' => 'ditolak@test.com', 'status' => 'ditolak'],
        ['name' => 'Peserta Berhenti', 'email' => 'berhenti@test.com', 'status' => 'berhenti'],
        ['name' => 'Peserta Lulus', 'email' => 'lulus@test.com', 'status' => 'selesai'],
        ['name' => 'Peserta Baru', 'email' => 'baru@test.com', 'status' => null],
      ];

      foreach ($scenarios as $index => $data) {
        // A. Create User
        $user = User::create([
          'name' => $data['name'],
          'email' => $data['email'],
          'password' => Hash::make('12345678'),
          'role_id' => $rolePeserta->id_role,
        ]);

        // B. Create Profile
        ProfilePeserta::create([
          'pengguna_id' => $user->id,
          'nim_nis' => '1011202' . $index,
          'institusi' => 'Universitas Teknologi Bandung',
          'jurusan' => 'Teknik Informatika',
          'no_hp' => '089999900' . $index,
          'alamat' => 'Jl. Percobaan No. ' . $index,
        ]);

        if ($data['status'] === null) continue;

        // C. Siapkan Data Lamaran
        $lamaranData = [
          'pengguna_id' => $user->id,
          'lowongan_id' => $lowongan->id,
          'status' => $data['status'],
          'tgl_mulai_usulan' => now()->addDays(7),
          'tgl_selesai_usulan' => now()->addDays(37),
          'hari_kerja_usulan' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
          'alasan_hari_kerja' => 'Sesuai jadwal fulltime',
          'file_surat_pengantar' => 'dummy_surat.pdf',
        ];

        // D. Logika Per Status
        if ($data['status'] == 'diterima') {
          $lamaranData['pembimbing_id'] = $pembimbing->id;
          $lamaranData['tgl_mulai_resmi'] = now()->subDays(10);
          $lamaranData['tgl_selesai_resmi'] = now()->addDays(20);
          $lamaranData['hari_kerja_resmi'] = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        } elseif ($data['status'] == 'ditolak') {
          $lamaranData['catatan_admin'] = 'Mohon maaf, kualifikasi belum sesuai.';
        } elseif ($data['status'] == 'berhenti') {
          $lamaranData['pembimbing_id'] = $pembimbing->id;
          $lamaranData['tgl_mulai_resmi'] = now()->subMonth();
          $lamaranData['tgl_selesai_resmi'] = now();
          $lamaranData['hari_kerja_resmi'] = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
          $lamaranData['catatan_admin'] = 'Mengundurkan Diri - Sakit Berkepanjangan';
        } elseif ($data['status'] == 'selesai') {
          $lamaranData['pembimbing_id'] = $pembimbing->id;
          $lamaranData['tgl_mulai_resmi'] = now()->subMonths(3);
          $lamaranData['tgl_selesai_resmi'] = now()->subDay();
          $lamaranData['hari_kerja_resmi'] = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
          $lamaranData['path_laporan_akhir'] = 'dummy_laporan.pdf';
        }

        $lamaran = Lamaran::create($lamaranData);

        // F. Buat Presensi untuk yang 'diterima' (Schema Baru)
        if ($data['status'] == 'diterima') {
          $this->createPresensiForActiveUser($lamaran);
        }
      }
      $this->command->info('Seeding selesai!');
    });
  }

  private function createPresensiForActiveUser($lamaran)
  {
    $startDate = Carbon::today()->subDays(9);
    $patterns = ['hadir', 'hadir', 'sakit', 'sakit', 'izin', 'izin', 'alpha', 'alpha'];

    foreach ($patterns as $index => $status) {
      $currentDate = $startDate->copy()->addDays($index + 1);
      if ($currentDate->isWeekend()) $currentDate->addDays(2);

      // DATA DASAR (Wajib ada di fillable)
      $presensiData = [
        'user_id' => $lamaran->pengguna_id, // <-- UPDATE: Tambah user_id
        'lamaran_id' => $lamaran->id,
        'tanggal' => $currentDate->format('Y-m-d'),
        'status' => $status,
        'is_auto_closed' => false,
      ];

      // LOGIKA PER STATUS
      if ($status == 'hadir') {
        $presensiData['jam_masuk'] = '08:00:00';
        $presensiData['jam_pulang'] = '16:00:00';
        $presensiData['catatan_harian'] = 'Mengerjakan fitur login.';

        // UPDATE: Koordinat dipisah (Lat/Long)
        $presensiData['lat_masuk'] = '-6.914744';
        $presensiData['long_masuk'] = '107.609810';
        $presensiData['lat_pulang'] = '-6.914744';
        $presensiData['long_pulang'] = '107.609810';
      } elseif ($status == 'sakit') {
        $presensiData['catatan_harian'] = 'Demam';
        // Note: 'bukti_foto' dihapus karena tidak ada di fillable baru Anda
      } elseif ($status == 'izin') {
        $presensiData['catatan_harian'] = 'Acara keluarga';
      } elseif ($status == 'alpha') {
        $presensiData['catatan_harian'] = '-';
      }

      Presensi::create($presensiData);
    }
  }
}
