<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\QueueMonitorController;
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

    // Khusus Super Admin
    // Route::middleware('role:super_admin')->group(function () {
    //     Route::get('/manajemen-pengguna', [UserController::class, 'index'])
    //         ->name('users.index');
    // });

    // Khusus Admin FO
    Route::middleware('role:admin_fo')->group(function () {
        Route::get('/fo/monitor', [QueueMonitorController::class, 'index'])
            ->name('admin.fo.monitor');
    });

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
