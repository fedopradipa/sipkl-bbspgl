<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek Login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        // Ambil role user, pastikan lowercase agar tidak sensitif huruf besar/kecil
        $userRole = strtolower(trim($user->role->nama_role ?? 'guest'));

        // 2. JALUR KHUSUS SUPERADMIN (VIP Pass)
        // Superadmin selalu boleh lewat kemanapun
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        // 3. BERSIHKAN DAN PECAH DAFTAR ROLE YANG DIIZINKAN
        // Masalah utama Anda ada disini sebelumnya.
        // Kita harus memecah string "superadmin|admin" menjadi array terpisah.
        $allowedRoles = [];

        foreach ($roles as $role) {
            // Pecah berdasarkan pipa '|' jika ada
            $parts = explode('|', $role);

            foreach ($parts as $part) {
                // Masukkan ke array baru dalam format lowercase
                $allowedRoles[] = strtolower(trim($part));
            }
        }

        // 4. CEK APAKAH ROLE USER ADA DI ARRAY YANG SUDAH BERSIH
        // Sekarang kita bandingkan "admin" dengan ["superadmin", "admin"]
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // Jika gagal
        return redirect()->route('dashboard')->with('error', 'Akses Ditolak.');
    }
}
