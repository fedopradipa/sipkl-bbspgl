<?php

use Illuminate\Support\Facades\Route;

// --- CONTROLLERS GLOBAL / AUTH ---
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GuestController;

// --- CONTROLLERS ADMIN  ---
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\LamaranAdminController;
use App\Http\Controllers\Admin\AlumniController;
use App\Http\Controllers\Admin\PesertaAktifController;
use App\Http\Controllers\Admin\PresensiAdminController;
use App\Http\Controllers\Admin\IzinAdminController;
use App\Http\Controllers\Admin\RekapPresensiController;
use App\Http\Controllers\Admin\PembimbingController;
use App\Http\Controllers\Admin\LowonganController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\UserController;

// --- CONTROLLERS PESERTA ---
use App\Http\Controllers\peserta\LamaranPesertaController;
use App\Http\Controllers\peserta\DashboardPesertaController;
use App\Http\Controllers\peserta\PesertaProfileController;
use App\Http\Controllers\peserta\PresensiController;
use App\Http\Controllers\peserta\IzinController;

// --- MIDDLEWARE ---
use App\Http\Middleware\CheckActiveLamaran;

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

// AUTHENTICATION
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ===============================================
// 2. RUTE GLOBAL TERPROTEKSI (Shared Resources)
// ===============================================
Route::middleware('auth')->group(function () {

    // SUPERADMIN & ADMIN SHARED
    Route::middleware(['role:superadmin|admin'])->group(function () {
        // Profile Management Admin
        Route::get('/profile/general', [ProfileController::class, 'edit'])->name('profile.general.edit');
        Route::put('/profile/general', [ProfileController::class, 'update'])->name('profile.general.update');

        // Master Data
        Route::resource('lowongan', LowonganController::class);
        Route::resource('pembimbing', PembimbingController::class);
        Route::resource('role', RoleController::class);
        Route::resource('user', UserController::class);
    });

    // SUPERADMIN ONLY
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/superadmin/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');
    });

    // ===============================================
    // 3. GROUP KHUSUS ADMIN (REFACTORED)
    // ===============================================
    Route::middleware(['role:admin'])
        ->prefix('admin')       // Semua URL diawali /admin/...
        ->name('admin.')        // Semua Route Name diawali admin... (ex: admin.lamaran.index)
        ->group(function () {

            // A. DASHBOARD
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // B. MANAJEMEN LAMARAN (VERIFIKASI)
            // URL: /admin/lamaran/...
            Route::controller(LamaranAdminController::class)->prefix('lamaran')->name('lamaran.')->group(function () {
                Route::get('/', 'index')->name('index'); // admin.lamaran.index
                Route::get('/{id}', 'show')->name('show');

                // Action Buttons
                Route::post('/{lamaran}/approve', 'approve')->name('approve');
                Route::post('/{lamaran}/reject', 'reject')->name('reject');
                Route::put('/{lamaran}/status', 'updateStatus')->name('updateStatus'); // Untuk Lulus/DropOut
                Route::delete('/{lamaran}', 'destroy')->name('destroy');
            });

            // C. MANAJEMEN ALUMNI & ARSIP
            // URL: /admin/alumni/...
            Route::controller(AlumniController::class)->prefix('alumni')->name('alumni.')->group(function () {
                // 1. Daftar Alumni
                Route::get('/', 'index')->name('index'); // route('admin.alumni.index')
                // 2. Export / Cetak Rekapitulasi (WAJIB DI ATAS SHOW)
                Route::get('/export', 'export')->name('export'); // route('admin.alumni.export')
                // 3. Detail Alumni (Wildcard ID harus paling bawah)
                Route::get('/{id}', 'show')->name('show'); // route('admin.alumni.show')
            });

            // D. PESERTA AKTIF
            // URL: /admin/peserta-aktif/...
            Route::controller(PesertaAktifController::class)->prefix('peserta-aktif')->name('peserta_aktif.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
            });

            // E. PRESENSI ADMIN
            Route::controller(PresensiAdminController::class)->prefix('presensi')->name('presensi.')->group(function () {
                Route::get('/', 'index')->name('index'); // admin.presensi.index
                Route::post('/store', 'storeAdmin')->name('store');
                Route::put('/{id}', 'updateAdmin')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

            // F. IZIN ADMIN
            Route::controller(IzinAdminController::class)->prefix('izin')->name('izin.')->group(function () {
                Route::get('/', 'index')->name('index'); // admin.izin.index
                Route::put('/{id}/approve', 'approve')->name('approve');
                Route::put('/{id}/reject', 'reject')->name('reject');
            });

            // G. LAPORAN
            Route::get('/reports/attendance', [RekapPresensiController::class, 'index'])->name('reports.attendance.index');
            Route::get('/reports/attendance/export-pdf', [RekapPresensiController::class, 'exportPdf'])->name('reports.attendance.pdf');

            Route::resource('role', RoleController::class);
            Route::resource('user', UserController::class);
        });

    // ===============================================
    // 4. GROUP KHUSUS PESERTA
    // ===============================================
    Route::middleware('role:peserta')->group(function () {

        // Dashboard & Profil
        Route::get('/peserta/dashboard', [DashboardPesertaController::class, 'index'])->name('peserta.dashboard');
        Route::get('/profile', [PesertaProfileController::class, 'edit'])->name('profile.peserta.edit');
        Route::put('/profile', [PesertaProfileController::class, 'update'])->name('profile.peserta.update');

        // Proses Lamaran (Menggunakan Controller Baru)
        Route::controller(LamaranPesertaController::class)->group(function () {
            // Middleware CheckActiveLamaran: Mencegah apply jika sudah punya lamaran aktif
            Route::get('/lowongan/{lowongan}/apply', 'create')
                ->middleware(CheckActiveLamaran::class)
                ->name('lamaran.apply');

            Route::post('/lowongan/{lowongan}/apply', 'store')
                ->middleware(CheckActiveLamaran::class)
                ->name('lamaran.store');
        });

        // Presensi
        Route::controller(PresensiController::class)->prefix('presensi')->name('presensi.')->group(function () {
            Route::post('/checkin', 'checkin')->name('checkin');
            Route::post('/checkout', 'checkout')->name('checkout');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
        });

        // Izin
        Route::resource('izin', IzinController::class)->except(['destroy']);

        Route::post('/lamaran/upload-laporan', [LamaranPesertaController::class, 'uploadLaporan'])
            ->name('peserta.lamaran.uploadLaporan');
    });
});

/*
|--------------------------------------------------------------------------
| BACKWARD COMPATIBILITY (Opsional)
|--------------------------------------------------------------------------
| Gunakan ini jika View blade Anda masih memanggil nama route lama.
| Hapus bagian ini jika Anda sudah merapikan semua file view.
*/
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
//     // Mapping Lamaran Lama ke Controller Baru
//     Route::get('/lamaran-legacy', [LamaranAdminController::class, 'index'])->name('lamaran.index.admin');
//     Route::get('/lamaran-legacy/{id}', [LamaranAdminController::class, 'show'])->name('lamaran.show.admin');
//     // Mapping Presensi Lama
//     Route::get('/laporan/presensi-legacy', [PresensiAdminController::class, 'presensi'])->name('presensi.index.admin');
// });
