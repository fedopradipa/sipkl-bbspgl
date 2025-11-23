<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\DashboardPesertaController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\PesertaProfileController; // Tambahkan controller profil khusus peserta

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===============================================
// 1. RUTE PUBLIK (GLOBAL GUEST ACCESS)
// ===============================================
Route::get('/', [GuestController::class, 'index'])->name('guest.home');
Route::get('/lowongan-public/{lowongan}', [GuestController::class, 'show'])->name('guest.lowongan.show');

// RUTE OTENTIKASI (LOGIN/LOGOUT)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ===============================================
// 2. RUTE GLOBAL TERPROTEKSI (Semua yang Sudah Login)
// ===============================================
Route::middleware('auth')->group(function () {

    //// ============================================================
    // GROUP A: ADMIN & SUPERADMIN (SHARED RESOURCES)
    // ============================================================
    // Middleware 'OR' (|) mengizinkan keduanya akses
    Route::middleware(['role:superadmin|admin'])->group(function () {

        // 1. Profile Management (Satu Controller untuk Admin & Superadmin)
        // Kita beri URL spesifik agar tidak bentrok dengan peserta
        Route::get('/profile/general', [ProfileController::class, 'edit'])->name('profile.general.edit');
        Route::put('/profile/general', [ProfileController::class, 'update'])->name('profile.general.update');

        // 2. Resource Lowongan & Pembimbing (Admin & Superadmin boleh akses)
        Route::resource('lowongan', LowonganController::class);
        Route::resource('pembimbing', PembimbingController::class);
    });
    // ===============================================
    // 3. RUTE KHUSUS SUPERADMIN
    // ===============================================
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/superadmin/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard'); // Dashboard Superadmin
        Route::resource('role', RoleController::class);
        Route::resource('user', UserController::class);
    });

    // ===============================================
    // 4. RUTE KHUSUS ADMIN (OPERATOR ONLY)
    // ===============================================
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard'); // Dashboard Admin
    });

    // ===============================================
    // 5. RUTE PESERTA (APPLICANT)
    // ===============================================
    // Catatan: Route ini harus memiliki unique name/URI jika Controller-nya beda.
    Route::middleware('role:peserta')->group(function () {

        // FIX PENTING: Panggil Dashboard Peserta dengan nama yang jelas
        // Ini adalah Dashboard state-based yang sesungguhnya
        Route::get('/peserta/dashboard', [DashboardPesertaController::class, 'index'])->name('peserta.dashboard');

        // FIX PENTING: Penggunaan Rute Profil yang Overwrite Global
        Route::get('/profile', [PesertaProfileController::class, 'edit'])->name('profile.peserta.edit');
        Route::put('/profile', [PesertaProfileController::class, 'update'])->name('profile.peserta.update');

        // Rute Lamaran
        Route::get('/lowongan/{lowongan}/apply', [LamaranController::class, 'create'])->name('lamaran.apply');
        Route::post('/lowongan/{lowongan}/apply', [LamaranController::class, 'store'])->name('lamaran.store');

        // Rute Presensi (Next Step)
    });
});
