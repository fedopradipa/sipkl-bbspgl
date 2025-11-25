<?php

namespace App\Http\Controllers;

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
            // Validasi Foto (Best Practice: batasi tipe dan ukuran)
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Validasi Data Peserta
            'nim_nis' => 'required|string|max:50',
            'institusi' => 'required|string|max:255',
            'jurusan' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        // 2. SIAPKAN DATA USER (Nama & Email)
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // 3. LOGIC PASSWORD (Jika diisi)
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // 4. LOGIC FOTO PROFIL (Best Practice)
        if ($request->hasFile('foto_profil')) {

            // A. Hapus Foto Lama (PENTING: Agar storage tidak penuh)
            // Cek apakah user punya foto lama DAN filenya benar-benar ada di storage
            if ($user->foto_profil && Storage::exists('public/foto_profil/' . $user->foto_profil)) {
                Storage::delete('public/foto_profil/' . $user->foto_profil);
            }

            // B. Proses Upload Foto Baru
            $file = $request->file('foto_profil');

            // Buat nama unik: waktu_upload + string acak + ekstensi asli
            // Contoh: 17098822_ab123cd.jpg
            $filename = time() . '_' . $file->hashName();

            // Simpan ke folder: storage/app/public/foto_profil
            $file->storeAs('public/foto_profil', $filename);

            // C. Masukkan nama file ke array update database
            $userData['foto_profil'] = $filename;
        }

        // 5. EKSEKUSI UPDATE USER (Nama, Email, Password, Foto)
        $user->update($userData);

        // 6. UPDATE TABEL PROFILE_PESERTA
        ProfilePeserta::updateOrCreate(
            ['pengguna_id' => $user->id],
            [
                'nim_nis' => $validated['nim_nis'],
                'institusi' => $validated['institusi'],
                'jurusan' => $validated['jurusan'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
            ]
        );

        return redirect()->route('profile.peserta.edit')
            ->with('success', 'Profil dan Foto berhasil diperbarui.');
    }
}
