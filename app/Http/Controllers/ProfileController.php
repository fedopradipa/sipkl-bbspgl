<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use  App\Models\ProfilePeserta;

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit profil pengguna yang sedang login.
     * Menggunakan Internal Delegation (Best Practice).
     */
    public function edit()
    {
        $user = Auth::user();

        // 1. DISPATCHER LOGIC: Jika user adalah Peserta
        if ($user->isPeserta()) {

            // BEST PRACTICE: Panggil Controller lain secara internal.
            // Kita instansiasi PesertaProfileController
            $pesertaController = app(\App\Http\Controllers\PesertaProfileController::class);

            // Lalu, kita panggil method 'edit' milik controller tersebut
            // Ini akan merender View milik Peserta secara langsung, TANPA HTTP Redirect.
            return $pesertaController->edit();
        }

        // 2. LOGIC ADMIN (Original Code)
        // Jika bukan Peserta (Admin/Superadmin/Lainnya), jalankan View Admin.
        return view('admin.profile.edit', [
            'user' => $user,
            'title' => 'Edit Profil Saya',
            'header' => 'Edit Profil Saya'
        ]);
    }
    /**
     * Memperbarui profil pengguna yang sedang login di database.
     * (Langkah 4 - PUT /profile)
     */
    public function update(Request $request)
    {
        // 1. Ambil user yang sedang login
        $user = Auth::user();
        // 1. CEK ROLE (PENTING! Jangan proses Peserta di sini)
        if ($user->isPeserta()) {
            // Arahkan Peserta ke rute update yang benar
            return redirect()->route('profile.peserta.edit')->with('error', 'Silakan simpan data profil melalui halaman khusus Anda.');
        }
        // 2. Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Pastikan email unik, KECUALI untuk ID user ini sendiri
                Rule::unique('users')->ignore($user->id),
            ],
            // Validasi kolom baru (dari Model 192)
            'nip' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users')->ignore($user->id),
            ],
            'jabatan' => 'nullable|string|max:100',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Maks 2MB

            // Validasi password (opsional)
            'password' => 'nullable|string|min:8|confirmed', // 'confirmed' akan mencari 'password_confirmation'
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'nip.unique' => 'NIP ini sudah digunakan oleh pengguna lain.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 3. Siapkan data untuk diupdate
        $dataToUpdate = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'nip' => $validatedData['nip'],
            'jabatan' => $validatedData['jabatan'],
        ];

        // 4. Handle Upload Foto (Jika ada file baru)
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama (jika ada)
            if ($user->foto_profil) {
                Storage::delete('public/foto_profil/' . $user->foto_profil);
            }

            // Simpan foto baru
            $file = $request->file('foto_profil');
            // Buat nama file unik: user_{id}_{timestamp}.ext
            $fileName = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            // Simpan di storage/app/public/foto_profil
            $file->storeAs('public/foto_profil', $fileName);

            // Simpan nama file ke database
            $dataToUpdate['foto_profil'] = $fileName;
        }

        // 5. Handle Update Password (Jika diisi)
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($validatedData['password']);
        }

        // 6. Update data user
        $user->update($dataToUpdate);

        // 7. Redirect kembali ke halaman edit profil
        return redirect()->route('profile.general.edit')->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
