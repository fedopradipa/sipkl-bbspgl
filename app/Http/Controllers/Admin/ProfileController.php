<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use  App\Models\ProfilePeserta;
use App\Models\ProfileAdmin;

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit profil pengguna yang sedang login.
     */
    public function edit()
    {
        $user = Auth::user();

        // 1. DISPATCHER LOGIC: Jika user adalah Peserta (Sudah Benar)
        if ($user->isPeserta()) {
            $pesertaController = app(\App\Http\Controllers\PesertaProfileController::class);
            return $pesertaController->edit();
        }

        // 2. LOGIC ADMIN (PERBAIKAN DIMULAI DI SINI)
        // Eager load relasi adminProfile untuk menghindari N+1 query
        $user->load('adminProfile');

        // Tentukan variabel $profile. Jika relasi tidak ada (Admin baru), nilainya null.
        // Ini penting agar View tidak error.
        $profile = $user->adminProfile;

        return view('admin.profile.edit', [
            'user' => $user,
            'profile' => $profile, // <-- WAJIB: Variabel $profile kini dikirim!
            'title' => 'Edit Profil Saya',
            'header' => 'Edit Profil Saya'
        ]);
    }
    /**
     * Memperbarui profil pengguna yang sedang login di database.
     */


    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. CEK ROLE (Logika ini sudah benar)
        // Jika Peserta punya controller sendiri, jangan proses di sini.
        if ($user->isPeserta()) {
            // Arahkan Peserta ke rute update yang benar
            // Ini akan error jika route/controller ini belum ada
            return redirect()->route('profile.peserta.edit')->with('error', 'Silakan simpan data profil melalui halaman khusus Anda.');
        }

        // 2. Validasi input (DIUBAH)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            // Validasi NIP/Jabatan di profile_admins (tidak di tabel users!)
            'nip' => [
                'nullable',
                'string',
                'max:50',
                // Cek unik di tabel profile_admins, abaikan NIP milik user ini sendiri
                // PENTING: Asumsi nama kolom di tabel profile_admins adalah 'nip'
                Rule::unique('profile_admins', 'nip')->ignore($user->id, 'user_id'),
            ],
            'jabatan' => 'nullable|string|max:100',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'email.unique' => 'Email ini sudah digunakan.',
            'nip.unique' => 'NIP ini sudah digunakan oleh pengguna lain.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 3. Update data dasar user (Tabel users)
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            // Hapus 'nip' dan 'jabatan' dari update tabel users!
        ]);

        // 4. Update/Create data Profil Admin (Tabel profile_admins)
        $profileAdmin = ProfileAdmin::firstOrNew(['user_id' => $user->id]);

        // 5. Handle Upload Foto (Harus ke model Profil Admin)
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama hanya jika file lama ada
            if ($profileAdmin->foto_profil) {
                Storage::delete('public/foto_profil/' . $profileAdmin->foto_profil);
            }

            $file = $request->file('foto_profil');
            $fileName = 'admin_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/foto_profil', $fileName);

            $profileAdmin->foto_profil = $fileName;
        }

        // 6. Simpan data NIP dan Jabatan ke Profil Admin
        $profileAdmin->nip = $validatedData['nip'];
        $profileAdmin->jabatan = $validatedData['jabatan'];
        $profileAdmin->save(); // Simpan model ProfileAdmin

        // 7. Handle Update Password (Jika diisi)
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
            $user->save(); // Update password di tabel users
        }

        // 8. Redirect
        return redirect()->route('profile.general.edit')->with('success', 'Profil Admin berhasil diperbarui.');
    }
}
