<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login', [
            'title' => 'Login',
        ]);
    }
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        $remember = $request->filled('remember');
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->isSuperadmin()) {
                $dashboardRoute = route('superadmin.dashboard');
            } elseif ($user->isAdmin()) {
                $dashboardRoute = route('admin.dashboard');
            } elseif ($user->isPeserta()) {
                $dashboardRoute = route('peserta.dashboard');
            } else {
                $dashboardRoute = route('guest.home');
            }
            return redirect()->intended($dashboardRoute);
        }
        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }
    public function logout(\Illuminate\Http\Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('guest.home');
    }
}
