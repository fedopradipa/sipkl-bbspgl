<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Lamaran;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveLamaran
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    $userId = auth()->id();

    $hasActive = Lamaran::where('pengguna_id', $userId)
      ->whereIn('status', ['pending', 'diterima'])
      ->exists();

    if ($hasActive) {
      return redirect()->route('peserta.dashboard')
        ->with('error', 'Anda masih memiliki lamaran aktif. Selesaikan dulu sebelum melamar lagi.');
    }

    return $next($request);
  }
}
