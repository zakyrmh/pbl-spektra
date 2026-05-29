<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeraiLoketController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SessionManagementController;
use App\Http\Controllers\UserController;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Halaman yang bisa diakses oleh siapa saja (Warga) tanpa harus login.
| Menggunakan sub-layout 'public'.
*/

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/cek-antrean', [PublicController::class, 'checkQueue'])->name('public.check');

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
| Hanya bisa diakses oleh user yang BELUM login.
| Jika sudah login dan mencoba akses ini, Laravel akan otomatis
| melempar ke '/' atau dashboard (tergantung setting di Middleware).
*/
Route::middleware('guest')->group(function () {
    // Halaman Login
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');

    // Halaman Registrasi (Jika dibutuhkan untuk warga)
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.process');

    // Lupa Password
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Private / Authenticated Routes
|--------------------------------------------------------------------------
| Hanya bisa diakses setelah login.
| Menggunakan sub-layout 'private'.
*/
Route::middleware('auth')->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ── Super Admin: Manajemen Pengguna ──────────────────────────────────────
    Route::middleware('role:super_admin')->group(function () {
        // CRUD Pengguna
        Route::get('/manajemen-pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('/manajemen-pengguna', [UserController::class, 'store'])->name('users.store');
        Route::put('/manajemen-pengguna/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/manajemen-pengguna/{user}/status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('/manajemen-pengguna/{user}/reset-pw', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Audit Trail: Log Aktivitas per User
        Route::get('/manajemen-pengguna/{user}/log', [UserController::class, 'activityLog'])->name('users.activity-log');

        // Session Management: daftar & force-revoke sesi aktif
        Route::get('/manajemen-pengguna/{user}/sessions', [SessionManagementController::class, 'index'])->name('users.sessions.index');
        Route::delete('/manajemen-pengguna/{user}/sessions/all', [SessionManagementController::class, 'destroyAll'])->name('users.sessions.destroy-all');
        Route::delete('/manajemen-pengguna/{user}/sessions/{session}', [SessionManagementController::class, 'destroy'])->name('users.sessions.destroy');

        // Konfigurasi Gerai / Loket (Instansi / Counter / Service)
        Route::get('/konfigurasi-gerai-loket', [GeraiLoketController::class, 'index'])->name('config.index');
        Route::post('/konfigurasi-gerai-loket/departments', [GeraiLoketController::class, 'storeDepartment'])->name('config.departments.store');
        Route::put('/konfigurasi-gerai-loket/departments/{department}', [GeraiLoketController::class, 'updateDepartment'])->name('config.departments.update');
        Route::delete('/konfigurasi-gerai-loket/departments/{department}', [GeraiLoketController::class, 'destroyDepartment'])->name('config.departments.destroy');

        Route::post('/konfigurasi-gerai-loket/counters', [GeraiLoketController::class, 'storeCounter'])->name('config.counters.store');
        Route::put('/konfigurasi-gerai-loket/counters/{counter}', [GeraiLoketController::class, 'updateCounter'])->name('config.counters.update');
        Route::delete('/konfigurasi-gerai-loket/counters/{counter}', [GeraiLoketController::class, 'destroyCounter'])->name('config.counters.destroy');
        Route::patch('/konfigurasi-gerai-loket/counters/{counter}/status', [GeraiLoketController::class, 'toggleCounterStatus'])->name('config.counters.toggle-status');

        Route::post('/konfigurasi-gerai-loket/services', [GeraiLoketController::class, 'storeService'])->name('config.services.store');
        Route::put('/konfigurasi-gerai-loket/services/{service}', [GeraiLoketController::class, 'updateService'])->name('config.services.update');
        Route::delete('/konfigurasi-gerai-loket/services/{service}', [GeraiLoketController::class, 'destroyService'])->name('config.services.destroy');
    });

    // Khusus Admin FO
    // Route::middleware('role:admin_fo')->group(function () {
    //     Route::get('/check-in', [CheckInController::class, 'index'])
    //         ->name('checkin.index');
    // });

    // Khusus Admin Gerai
    Route::middleware('role:admin_gerai')->group(function () {
        Route::get('/antrean', [DashboardController::class, 'manageQueue'])
            ->name('antrean.index');
    });

    // Proses Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Test Email Route (Hanya untuk testing, bisa dihapus nanti)
|--------------------------------------------------------------------------
| Route ini hanya untuk menguji pengiriman email menggunakan Mailable yang sudah dibuat.
| Jika ingin menguji, pastikan untuk membuat view 'emails.test' terlebih dahulu.
*/

Route::get('/test-mail', function () {
    Mail::to('zaxxyyramadhan@gmail.com')->send(new TestEmail);

    return 'Email terkirim!';
});
