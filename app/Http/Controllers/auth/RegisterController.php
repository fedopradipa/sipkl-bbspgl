<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfilePeserta;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Menampilkan Form Registrasi
     */
    public function showRegistrationForm()
    {
        return view('auth.register', [
            'title' => 'Daftar Akun Baru'
        ]);
    }

    /**
     * Proses Pendaftaran
     */
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            // Data Akun Utama
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed', // butuh field password_confirmation di view

            // Data Profil Peserta
            'nim_nis'   => 'required|string|max:50',
            'institusi' => 'required|string|max:100', // Sekolah/Kampus
            'jurusan'   => 'required|string|max:100',
            'no_hp'     => 'required|string|max:20',
            'alamat'    => 'required|string',
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        try {
            // 2. Mulai Transaksi Database
            DB::beginTransaction();

            // Cari Role ID untuk 'peserta' (Agar dinamis)
            // Asumsi tabel roles punya kolom 'nama_role' dan primary key 'id_role'
            $rolePeserta = Role::where('nama_role', 'peserta')->first();

            if (!$rolePeserta) {
                throw new \Exception('Role peserta belum disetting di database.');
            }

            // A. Simpan ke Tabel Users
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $rolePeserta->id_role, // Atau $rolePeserta->id tergantung PK anda
            ]);

            // B. Simpan ke Tabel Profil Peserta
            ProfilePeserta::create([
                'pengguna_id' => $user->id,
                'nim_nis'     => $request->nim_nis,
                'institusi'   => $request->institusi,
                'jurusan'     => $request->jurusan,
                'no_hp'       => $request->no_hp,
                'alamat'      => $request->alamat,
            ]);

            // Jika semua sukses, commit ke database
            DB::commit();

            // 3. Login Otomatis setelah daftar
            Auth::login($user);
            // 4. REDIRECT CERDAS (PERBAIKAN DISINI)
            // Cek: Apakah user tadi mau melamar? (Ada data intended?)
            // Jika YA -> Kirim ke Form Lamaran
            // Jika TIDAK (Daftar biasa) -> Kirim ke Dashboard Peserta

            return redirect()->intended(route('peserta.dashboard'))
                ->with('success', 'Pendaftaran berhasil! Selamat datang.');
        } catch (\Exception $e) {
            // Jika gagal, batalkan semua perubahan
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mendaftar: ' . $e->getMessage())->withInput();
        }
    }
}
