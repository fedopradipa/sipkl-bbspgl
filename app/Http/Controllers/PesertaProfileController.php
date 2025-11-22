<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfilePeserta;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // <--- WAJIB: Import ini untuk enkripsi password

class PesertaProfileController extends Controller
{
    /**
     * Menampilkan Form Edit Profil Peserta (GET /profile/peserta)
     */
    public function edit()
    {
        $user = Auth::user();

        // Ambil data profil, jika kosong buat objek baru agar tidak error di view
        $ProfilePeserta = ProfilePeserta::firstOrNew(['pengguna_id' => $user->id]);

        return view('peserta.profile.edit', [
            'user' => $user,
            'profil' => $ProfilePeserta, // Variabel ini dipakai di view: value="{{ old('...', $profil->...) }}"
            'title' => 'Edit Profil Peserta',
            'header' => 'Kelola Data Diri'
        ]);
    }

    /**
     * Menyimpan Update Profil Peserta (PUT /profile/peserta)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. VALIDASI INPUT
        $validated = $request->validate([
            // Validasi User
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            // Validasi Password (Opsional, hanya wajib jika kolom diisi)
            'password' => 'nullable|string|min:8|confirmed',

            // Validasi Profil Peserta
            'nim_nis' => 'required|string|max:50',
            'institusi' => 'required|string|max:255',
            'jurusan' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        // 2. UPDATE TABEL USERS
        // Siapkan data dasar
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Cek apakah user mengisi password baru?
        if ($request->filled('password')) {
            // Jika ya, enkripsi password dan masukkan ke array update
            $userData['password'] = Hash::make($validated['password']);
        }

        // Eksekusi update user
        $user->update($userData);

        // 3. UPDATE TABEL PROFILE_PESERTA
        ProfilePeserta::updateOrCreate(
            ['pengguna_id' => $user->id], // Cari berdasarkan ID user
            [
                'nim_nis' => $validated['nim_nis'],
                'institusi' => $validated['institusi'],
                'jurusan' => $validated['jurusan'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
            ]
        );

        // 4. REDIRECT
        return redirect()->route('profile.peserta.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
