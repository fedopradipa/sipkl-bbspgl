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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $userRole = $user->role->nama_role ?? 'guest'; // Dapatkan nama role pengguna

        // Jika pengguna adalah Superadmin, selalu izinkan akses
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        // Periksa apakah role pengguna ada dalam daftar role yang diizinkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

    return redirect()->route('dashboard')->with('error', 'Akses Ditolak. Anda tidak memiliki izin untuk mengakses fitur ini.');
    }
}