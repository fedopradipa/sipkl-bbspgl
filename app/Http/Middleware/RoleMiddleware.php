<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $rawRole = $user->role->nama_role ?? 'guest';
        $userRoleClean = strtolower(trim($rawRole)); // Role yang sedang dimiliki user
        $allowedRoles = []; // Daftar role yang diizinkan rute ini

        // 1. BERSIHKAN DAN PECAH DAFTAR ROLE YANG DIIZINKAN DARI ROUTE
        foreach ($roles as $role) {
            $parts = explode('|', $role);
            foreach ($parts as $part) {
                $allowedRoles[] = strtolower(trim($part));
            }
        }

        // 2. LOGIKA BYPASS SUPERADMIN
        if ($userRoleClean === 'superadmin') {
            return $next($request);
        }

        // 3. CEK KECOCOKAN
        if (in_array($userRoleClean, $allowedRoles)) {
            return $next($request);
        }

        // 4. JIKA GAGAL (Akses Ditolak), TENTUKAN KEMANA HARUS DIKEMBALIKAN

        $errorMessage = 'Akses Ditolak. Anda tidak diizinkan mengakses fitur ' . strtoupper($allowedRoles[0] ?? 'ini') . '.';

        // Tentukan rute dashboard spesifik untuk user yang sedang login
        if ($user->isSuperadmin()) {
            $redirectRoute = 'superadmin.dashboard';
        } elseif ($user->isAdmin()) {
            $redirectRoute = 'admin.dashboard';
        } elseif ($user->isPeserta()) {
            $redirectRoute = 'peserta.dashboard';
        } else {
            // Fallback jika role tidak jelas
            $redirectRoute = '/';
        }

        // Redirect ke dashboard yang benar dengan pesan error
        return redirect()->route($redirectRoute)->with('error', $errorMessage);
    }
}
