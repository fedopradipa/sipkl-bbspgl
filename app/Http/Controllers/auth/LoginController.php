<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Gunakan Auth Facade bawaan Laravel
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman formulir login.
     * (Akan dipanggil oleh rute GET /login)
     */
    public function showLoginForm(): View
    {
        // Kita akan buat view ini di langkah selanjutnya
        return view('auth.login', [
            'title' => 'Login',
            // <-- BARIS 'HEADER' DIHAPUS (Sesuai Permintaan)
        ]);
    }

    /**
     * Menangani upaya login (autentikasi).
     * (Akan dipanggil oleh rute POST /login)
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validasi input (email & password wajib diisi)
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Coba lakukan login (menggunakan Auth bawaan Laravel)
        // Kita tambahkan 'remember' (opsional)
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            // 3. Jika berhasil:
            // Regenerasi session ID untuk keamanan (mencegah session fixation)
            $request->session()->regenerate();

            // Redirect ke halaman dashboard
            return redirect()->intended('/'); // '/' adalah rute Dashboard kita
        }

        // 4. Jika gagal:
        // Kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email'); // Hanya kembalikan input 'email', jangan 'password'
    }

    /**
     * Menangani logout pengguna.
     * (Akan dipanggil oleh rute POST /logout)
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout(); // Logout pengguna

        $request->session()->invalidate(); // Hapus session
        $request->session()->regenerateToken(); // Buat token CSRF baru

        // Redirect ke halaman login
        return redirect()->route('login');
    }
}