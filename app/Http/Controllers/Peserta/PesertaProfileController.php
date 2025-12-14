<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfilePeserta;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <--- WAJIB: Import Storage untuk kelola file

class PesertaProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $ProfilePeserta = ProfilePeserta::firstOrNew(['pengguna_id' => $user->id]);

        return view('peserta.profile.edit', [
            'user' => $user,
            'profil' => $ProfilePeserta,
            'title' => 'Edit Profil Peserta',
            'header' => 'Kelola Data Diri'
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. VALIDASI INPUT
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB

            // Validasi Data Peserta
            'nim_nis' => 'required|string|max:50',
            'institusi' => 'required|string|max:255',
            'jurusan' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 2. SIAPKAN DATA UNTUK TABEL USERS (Hanya Nama, Email, Password)
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // 3. LOGIC PASSWORD
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // 4. Update data dasar user
        $user->update($userData);


        // 5. SIAPKAN/AMBIL DATA PROFILE PESERTA
        // Ambil objek profil yang sudah ada atau buat baru
        $profile = ProfilePeserta::firstOrNew(['pengguna_id' => $user->id]);

        // Siapkan data non-foto untuk profil
        $profileData = [
            'nim_nis' => $validated['nim_nis'],
            'institusi' => $validated['institusi'],
            'jurusan' => $validated['jurusan'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
        ];

        // 6. LOGIC FOTO PROFIL (DISIMPAN KE TABEL PROFILE_PESERTA)
        if ($request->hasFile('foto_profil')) {

            // A. Hapus Foto Lama (Menggunakan data dari model ProfilePeserta)
            if ($profile->foto_profil && Storage::exists('public/foto_profil/' . $profile->foto_profil)) {
                Storage::delete('public/foto_profil/' . $profile->foto_profil);
            }

            // B. Proses Upload Foto Baru
            $file = $request->file('foto_profil');
            // Buat nama unik (gunakan hashName() agar nama file tidak bentrok)
            $filename = time() . '_' . $file->hashName();
            $file->storeAs('public/foto_profil', $filename);

            // C. Masukkan nama file ke array update database Profile Peserta
            $profileData['foto_profil'] = $filename;
        }

        // 7. EKSEKUSI UPDATE/CREATE TABEL PROFILE_PESERTA
        $profile->fill($profileData)->save();


        return redirect()->route('profile.peserta.edit')
            ->with('success', 'Profil dan Foto berhasil diperbarui.');
    }
}
