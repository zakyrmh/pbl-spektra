<?php

declare(strict_types=1);

use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Mail\BookingMovedMail;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('it moves sesi 1 booking to sesi 2 if sesi 2 is not crowded', function () {
    Mail::fake();

    $citizen = User::factory()->create(['role' => UserRole::Pengunjung]);
    $department = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
        'nomor_loket' => '23',
        'is_open' => true,
    ]);

    // Set limit
    Setting::setVal('daily_quota_limit', '10'); // Sesi 2 limit = 5

    $booking = Queue::create([
        'user_id' => $citizen->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-TEST-123',
        'booking_date' => now()->toDateString(),
        'status' => QueueStatus::Booked->value,
        'purpose' => 'Cetak KTP-el',
        'session_name' => 'Sesi 1',
    ]);

    // Run command
    $this->artisan('bookings:move-session')
        ->expectsOutput('Menemukan 1 booking Sesi 1 yang belum check-in. Memproses pemindahan...')
        ->expectsOutput('Selesai! 1 booking berhasil dipindahkan ke Sesi 2, 0 booking dilewati karena kuota penuh.')
        ->assertExitCode(0);

    // Assert booking is updated
    $this->assertEquals('Sesi 2', $booking->fresh()->session_name);

    // Assert mail sent
    Mail::assertQueued(BookingMovedMail::class, function ($mail) use ($citizen, $booking) {
        return $mail->hasTo($citizen->email) && $mail->booking->id === $booking->id;
    });

    // Assert notification created
    $this->assertTrue(Notification::where('user_id', $citizen->id)->where('title', 'Pemindahan Sesi Antrean')->exists());
});

test('it does not move booking if sesi 2 is crowded', function () {
    Mail::fake();

    $citizen = User::factory()->create(['role' => UserRole::Pengunjung]);
    $department = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
        'nomor_loket' => '23',
        'is_open' => true,
    ]);

    // Set limit to 2 (Sesi 2 limit = 1)
    Setting::setVal('daily_quota_limit', '2');

    // Create 1 active booking in Sesi 2 to fill it up
    Queue::create([
        'user_id' => User::factory()->create()->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-ACTIVE-SESI2',
        'booking_date' => now()->toDateString(),
        'status' => QueueStatus::Booked->value,
        'purpose' => 'Cetak KK',
        'session_name' => 'Sesi 2',
    ]);

    // Create booking in Sesi 1 we want to move
    $booking = Queue::create([
        'user_id' => $citizen->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-TEST-123',
        'booking_date' => now()->toDateString(),
        'status' => QueueStatus::Booked->value,
        'purpose' => 'Cetak KTP-el',
        'session_name' => 'Sesi 1',
    ]);

    // Run command
    $this->artisan('bookings:move-session')
        ->expectsOutput('Menemukan 1 booking Sesi 1 yang belum check-in. Memproses pemindahan...')
        ->expectsOutput("Booking BK-TEST-123 dilewati karena Sesi 2 instansi {$department->name} sudah ramai (1/1).")
        ->expectsOutput('Selesai! 0 booking berhasil dipindahkan ke Sesi 2, 1 booking dilewati karena kuota penuh.')
        ->assertExitCode(0);

    // Assert booking is NOT updated
    $this->assertEquals('Sesi 1', $booking->fresh()->session_name);

    // Assert mail NOT sent
    Mail::assertNotQueued(BookingMovedMail::class);
});
