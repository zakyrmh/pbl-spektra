<?php

use App\Http\Controllers\Admin\FO\Api\CheckInApiController;
use App\Http\Controllers\Admin\FO\BookingCancellationController;
use App\Http\Controllers\Admin\FO\CheckInController;
use App\Http\Controllers\Admin\FO\ReportController;
use App\Http\Controllers\Admin\FO\WalkInTicketController;
use App\Http\Controllers\AdminGerai\CounterController;
use App\Http\Controllers\AdminGerai\DaftarTungguController;
use App\Http\Controllers\AdminGerai\LogPelayananController;
// PapanPanggilController deprecated — functionality merged into CounterController/admin_gerai dashboard
// ScheduleController deprecated — Schedule model deleted
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Public\AuthController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\FeedbackController;
use App\Http\Controllers\Public\NotificationController;
use App\Http\Controllers\Public\ProfileController;
use App\Http\Controllers\Public\PublicController;
use App\Http\Controllers\Public\QueueMonitorController;
use App\Http\Controllers\SuperAdmin\GeraiLoketController;
use App\Http\Controllers\SuperAdmin\SessionManagementController;
use App\Http\Controllers\SuperAdmin\SettingController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Halaman yang bisa diakses oleh siapa saja (Warga) tanpa harus login.
*/

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/display', [QueueMonitorController::class, 'publicDisplay'])->name('display.index');
Route::get('/api/display/data', [QueueMonitorController::class, 'publicDisplayData'])->name('display.data');

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.process');

    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/
Route::get('/email/verify', [AuthController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resend'])
    ->middleware(['throttle:6,1'])
    ->name('verification.send');

/*
|--------------------------------------------------------------------------
| Private / Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard Utama (dispatcher ke controller per-role)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    // Pusat Notifikasi
    Route::get('/notifikasi', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::get('/notifikasi/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');

    // Feedback & Rating Pelayanan
    Route::get('/feedback/create', [FeedbackController::class, 'create'])
        ->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])
        ->name('feedback.store');

    // Profil Pengunjung
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // Booking Antrean Mandiri
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/baru', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');

    // ─────────────────────────────────────────────────────────────────────────
    // Super Admin Routes
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:super_admin')->group(function () {

        // ── Manajemen Pengguna ────────────────────────────────────────────
        Route::get('/manajemen-pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('/manajemen-pengguna', [UserController::class, 'store'])->name('users.store');
        Route::put('/manajemen-pengguna/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/manajemen-pengguna/{user}/status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('/manajemen-pengguna/{user}/reset-pw', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Audit Trail: Log Aktivitas per User
        Route::get('/manajemen-pengguna/{user}/log', [UserController::class, 'activityLog'])->name('users.activity-log');

        // Session Management
        Route::get('/manajemen-pengguna/{user}/sessions', [SessionManagementController::class, 'index'])->name('users.sessions.index');
        Route::delete('/manajemen-pengguna/{user}/sessions/all', [SessionManagementController::class, 'destroyAll'])->name('users.sessions.destroy-all');
        Route::delete('/manajemen-pengguna/{user}/sessions/{session}', [SessionManagementController::class, 'destroy'])->name('users.sessions.destroy');

        // ── Konfigurasi Gerai / Loket ─────────────────────────────────────
        Route::get('/konfigurasi-gerai-loket', [GeraiLoketController::class, 'index'])->name('config.index');

        // CRUD Gerai (Department)
        Route::post('/konfigurasi-gerai-loket/departments', [GeraiLoketController::class, 'store'])->name('config.departments.store');
        Route::put('/konfigurasi-gerai-loket/departments/{department}', [GeraiLoketController::class, 'update'])->name('config.departments.update');
        Route::delete('/konfigurasi-gerai-loket/departments/{department}', [GeraiLoketController::class, 'destroy'])->name('config.departments.destroy');

        // ── Pengaturan Sistem ─────────────────────────────────────────────
        Route::get('/pengaturan-sistem', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::put('/pengaturan-sistem', [SettingController::class, 'update'])->name('admin.settings.update');

        // ── Laporan & Analitik (Super Admin) ──────────────────────────────
        Route::get('/laporan-analitik', [ReportController::class, 'adminIndex'])->name('admin.reports.index');
        Route::get('/laporan-analitik/{report}', [ReportController::class, 'adminShow'])->name('admin.reports.show');
        Route::get('/laporan-analitik/{report}/export/excel', [ReportController::class, 'exportExcel'])->name('admin.reports.export.excel');
        Route::get('/laporan-analitik/{report}/export/pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.export.pdf');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // Admin FO Routes
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:admin_fo')->group(function () {

        // Monitor FO
        Route::get('/fo/monitor', [QueueMonitorController::class, 'index'])->name('admin.fo.monitor');

        // Verifikasi & Check-In (web)
        Route::get('/fo/check-in', [CheckInController::class, 'index'])->name('admin.fo.checkin');
        Route::post('/fo/check-in/verify', [CheckInController::class, 'verify'])->name('admin.fo.checkin.verify');
        Route::post('/fo/check-in/{booking}/approve', [CheckInController::class, 'approve'])->name('admin.fo.checkin.approve');
        Route::post('/fo/check-in/{booking}/reject', [CheckInController::class, 'reject'])->name('admin.fo.checkin.reject');

        // Pembatalan Booking
        Route::get('/fo/bookings', [BookingCancellationController::class, 'index'])->name('admin.fo.bookings.index');
        Route::post('/fo/bookings/{booking}/cancel', [BookingCancellationController::class, 'cancel'])->name('admin.fo.bookings.cancel');

        // Tiket Walk-In
        Route::get('/fo/ticket/create', [WalkInTicketController::class, 'create'])->name('admin.fo.ticket.create');
        Route::post('/fo/ticket', [WalkInTicketController::class, 'store'])->name('admin.fo.ticket.store');

        // Laporan (FO)
        Route::get('/fo/reports', [ReportController::class, 'foIndex'])->name('admin.fo.reports.index');
        Route::post('/fo/reports', [ReportController::class, 'foStore'])->name('admin.fo.reports.store');
        Route::put('/fo/reports/{report}', [ReportController::class, 'foUpdate'])->name('admin.fo.reports.update');
        Route::delete('/fo/reports/{report}', [ReportController::class, 'foDestroy'])->name('admin.fo.reports.destroy');
        Route::post('/fo/reports/{report}/send', [ReportController::class, 'foSend'])->name('admin.fo.reports.send');

        // ── API Endpoints FO (AJAX/Fetch) ─────────────────────────────────
        Route::get('/api/fo/bookings/verify', [CheckInApiController::class, 'verify'])->name('api.fo.bookings.verify');
        Route::post('/api/fo/bookings/{booking}/checkin', [CheckInApiController::class, 'checkIn'])->name('api.fo.bookings.checkin');
        Route::post('/api/fo/queues/walkin', [CheckInApiController::class, 'walkIn'])->name('api.fo.queues.walkin');
        Route::get('/api/fo/visitors/check-nik', [CheckInApiController::class, 'checkNik'])->name('api.fo.visitors.check-nik');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // Admin Gerai Routes
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:admin_gerai')->group(function () {

        // ── Daftar Tunggu ─────────────────────────────────────────────────
        Route::get('/admin/daftar-tunggu', [DaftarTungguController::class, 'index'])->name('admin.daftar-tunggu');
        Route::post('/admin/daftar-tunggu/{booking}/check-in', [DaftarTungguController::class, 'checkIn'])->name('admin.daftar-tunggu.check-in');
        Route::post('/admin/daftar-tunggu/{booking}/restore', [DaftarTungguController::class, 'restore'])->name('admin.daftar-tunggu.restore');
        Route::post('/admin/daftar-tunggu/{booking}/cancel', [DaftarTungguController::class, 'cancel'])->name('admin.daftar-tunggu.cancel');

        // ── Log Pelayanan ─────────────────────────────────────────────────
        Route::get('/admin/log-pelayanan', [LogPelayananController::class, 'index'])->name('admin.log-pelayanan');
        Route::get('/admin/log-pelayanan/export', [LogPelayananController::class, 'export'])->name('admin.log-pelayanan.export');

        // ── API Endpoints Gerai ───────────────────────────────────────────
        Route::post('/api/counter/status', [CounterController::class, 'updateStatus'])->name('gerai.status');
        Route::post('/api/department/toggle-status', [CounterController::class, 'toggleDepartmentStatus'])->name('gerai.department.toggle');
        Route::post('/api/queues/call-next', [CounterController::class, 'callNext'])->name('gerai.call-next');
        Route::post('/api/queues/{queue}/call', [CounterController::class, 'callQueue'])->name('gerai.call');
        Route::post('/api/queues/{queue}/finish', [CounterController::class, 'finishService'])->name('gerai.finish');
        Route::post('/api/queues/{queue}/skip', [CounterController::class, 'skipQueue'])->name('gerai.skip');
        Route::post('/api/queues/{queue}/hold', [CounterController::class, 'holdQueue'])->name('gerai.hold');
        Route::post('/api/queues/{queue}/forward', [CounterController::class, 'forwardQueue'])->name('gerai.forward');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Test Email Route (Hanya untuk testing lokal)
|--------------------------------------------------------------------------
*/
Route::get('/test-mail', function () {
    Mail::to('zaxxyyramadhan@gmail.com')->send(new TestEmail);

    return 'Email terkirim!';
});
