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
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [GuestController::class, 'index'])->name('guest.home');
Route::get('/lowongan-public/{lowongan}', [GuestController::class, 'show'])->name('guest.lowongan.show');
// ===============================================
// RUTE OTENTIKASI (LOGIN/LOGOUT)
// ===============================================
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');


// ===============================================
// RUTE GLOBAL (Semua yang Sudah Login)
// ===============================================
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // RUTE PROFIL PENGGUNA (Akses mandiri)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');


    // ===============================================
    // RUTE KHUSUS SUPERADMIN
    // ===============================================
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('role', RoleController::class);
        Route::resource('user', UserController::class);
    });

    // ===============================================
    // RUTE OPERASIONAL & TRANSAKSI (ADMIN / OPERATOR ONLY)
    // ===============================================
    // Semua fitur ini dikunci untuk role 'admin'
    Route::middleware('role:admin')->group(function () {
        Route::resource('lowongan', LowonganController::class);
        Route::resource('pembimbing', PembimbingController::class);
    });


    // ===============================================
    // RUTE SISWA
    // ===============================================
    Route::get('/lowongan/{lowongan}/apply', function () {
        // Nanti kita ganti ini dengan Controller pelamaran beneran
        return "Halaman Formulir Lamaran (Akan Dibuat)";
    })->name('lowongan.apply');
});
