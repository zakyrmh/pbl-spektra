<?php

use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed basic department and service
    $this->department = Department::create([
        'name' => 'Dinas Kesehatan',
        'inisial' => 'DKS',
        'description' => 'Pelayanan Kesehatan Kota Sawahlunto',
    ]);

    $this->service = Service::create([
        'department_id' => $this->department->id,
        'name' => 'Keluarga Berencana',
        'description' => 'Layanan KB gratis',
    ]);

    // Create a schedule for today
    $this->scheduleToday = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 5,
        'quota_used' => 0,
        'is_open' => true,
    ]);

    // Create a schedule for tomorrow
    $this->scheduleTomorrow = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->addDay()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 5,
        'quota_used' => 0,
        'is_open' => true,
    ]);

    // Setup roles
    $this->visitor = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);

    $this->foAdmin = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $this->superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    // Create a booking
    $this->booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $this->scheduleToday->id,
        'booking_code' => 'BOOK-TODAY-1',
        'status' => 'Pending',
        'booking_date' => $this->scheduleToday->date,
    ]);
});

test('guests are redirected to login when accessing FO booking cancellation pages', function () {
    $this->get(route('admin.fo.bookings.index'))->assertRedirect(route('login'));
    $this->post(route('admin.fo.bookings.cancel', $this->booking), ['reason' => 'Some reason'])->assertRedirect(route('login'));
});

test('visitor role is unauthorized (403) from FO booking cancellation pages', function () {
    $this->actingAs($this->visitor)->get(route('admin.fo.bookings.index'))->assertStatus(403);
    $this->actingAs($this->visitor)->post(route('admin.fo.bookings.cancel', $this->booking), ['reason' => 'Some reason'])->assertStatus(403);
});

test('FO admin and Super Admin can access FO booking cancellation index', function () {
    $this->actingAs($this->foAdmin)->get(route('admin.fo.bookings.index'))
        ->assertStatus(200)
        ->assertSee($this->booking->booking_code);

    $this->actingAs($this->superAdmin)->get(route('admin.fo.bookings.index'))
        ->assertStatus(200)
        ->assertSee($this->booking->booking_code);
});

test('FO admin can manually cancel a booking with valid reason', function () {
    Mail::fake();

    $response = $this->actingAs($this->foAdmin)
        ->post(route('admin.fo.bookings.cancel', $this->booking), [
            'reason' => 'Persyaratan dokumen kurang lengkap',
        ]);

    $response->assertRedirect(route('admin.fo.bookings.index'));
    $response->assertSessionHas('success');

    // Verify DB update
    $this->booking->refresh();
    expect($this->booking->status)->toBe('Cancelled');
    expect($this->booking->cancel_reason)->toBe('Persyaratan dokumen kurang lengkap');

    // Verify notification was sent to visitor
    $notif = Notification::where('user_id', $this->visitor->id)->first();
    expect($notif)->not->toBeNull();
    expect($notif->title)->toBe('Booking Dibatalkan oleh FO');
    expect($notif->message)->toContain('Persyaratan dokumen kurang lengkap');

    // Verify activity log
    $log = ActivityLog::where('action', 'CANCEL_BOOKING')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($this->foAdmin->id);
    expect($log->description)->toContain('Persyaratan dokumen kurang lengkap');

    // Verify email sent
    Mail::assertSent(BookingCancelledMail::class, function ($mail) {
        return $mail->hasTo($this->visitor->email) && $mail->booking->id === $this->booking->id;
    });
});

test('cancellation requires a reason with at least 5 characters', function () {
    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.bookings.index'))
        ->post(route('admin.fo.bookings.cancel', $this->booking), [
            'reason' => 'Opsi',
        ]);

    $response->assertRedirect(route('admin.fo.bookings.index'));
    $response->assertSessionHasErrors(['reason']);

    $this->booking->refresh();
    expect($this->booking->status)->toBe('Pending');
});

test('cancellation command cancel-expired cancels today or past pending bookings', function () {
    Mail::fake();

    // 1. Create a booking for yesterday (past)
    $yesterdayDate = now()->subDay()->toDateString();
    $scheduleYesterday = Schedule::create([
        'service_id' => $this->service->id,
        'date' => $yesterdayDate,
        'session_name' => 'Sore',
        'quota_total' => 5,
        'quota_used' => 1,
        'is_open' => true,
    ]);
    $bookingPast = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $scheduleYesterday->id,
        'booking_code' => 'BOOK-PAST-1',
        'status' => 'Pending',
        'booking_date' => $yesterdayDate,
    ]);

    // 2. Create a booking for tomorrow (future)
    $bookingFuture = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $this->scheduleTomorrow->id,
        'booking_code' => 'BOOK-FUTURE-1',
        'status' => 'Pending',
        'booking_date' => $this->scheduleTomorrow->date,
    ]);

    // 3. Create a booking for today but already checked in
    $bookingCheckedIn = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $this->scheduleToday->id,
        'booking_code' => 'BOOK-CHECKED-IN-1',
        'status' => 'Checked-In',
        'booking_date' => $this->scheduleToday->date,
    ]);

    // Run artisan command
    $this->artisan('bookings:cancel-expired')
        ->expectsOutput('Menemukan 2 booking pending kadaluarsa. Memulai proses pembatalan otomatis...')
        ->expectsOutput('Selesai! 2 dari 2 booking berhasil dibatalkan otomatis.')
        ->assertExitCode(0);

    // Verify today's booking is cancelled
    $this->booking->refresh();
    expect($this->booking->status)->toBe('Cancelled');
    expect($this->booking->cancel_reason)->toBe('Kadaluarsa');

    // Verify yesterday's booking is cancelled
    $bookingPast->refresh();
    expect($bookingPast->status)->toBe('Cancelled');
    expect($bookingPast->cancel_reason)->toBe('Kadaluarsa');

    // Verify tomorrow's booking remains pending
    $bookingFuture->refresh();
    expect($bookingFuture->status)->toBe('Pending');

    // Verify checked-in booking remains checked-in
    $bookingCheckedIn->refresh();
    expect($bookingCheckedIn->status)->toBe('Checked-In');

    // Verify database notification was created for today's expired booking
    $notif = Notification::where('user_id', $this->visitor->id)
        ->where('title', 'Booking Kadaluarsa')
        ->first();
    expect($notif)->not->toBeNull();

    // Verify activity log with AUTO_CANCEL action and null actor
    $log = ActivityLog::where('action', 'AUTO_CANCEL')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBeNull();

    // Verify emails were sent for today and past bookings
    Mail::assertSent(BookingCancelledMail::class, 2);
});
