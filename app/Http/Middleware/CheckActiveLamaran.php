<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LamaranService;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveLamaran
{
  public function __construct(
    private LamaranService $lamaranService
  ) {}

  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Cek apakah user punya lamaran aktif
    if ($this->lamaranService->hasActiveApplication(auth()->id())) {
      return redirect()->route('peserta.dashboard')
        ->with('error', 'Anda masih memiliki lamaran aktif. Selesaikan dulu sebelum melamar lagi.');
    }

    return $next($request);
  }
}
