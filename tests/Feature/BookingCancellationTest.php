<?php

use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

// Fungsi pembantu untuk membuat data awal (menggantikan beforeEach)
function createTestData()
{
    $department = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
        'description' => 'Kependudukan dan Catatan Sipil',
    ]);

    $service = Service::create([
        'department_id' => $department->id,
        'name' => 'Cetak KTP',
        'description' => 'Layanan cetak KTP-el baru',
    ]);

    $counter = Counter::create([
        'department_id' => $department->id,
        'name' => 'Loket Disdukcapil 1',
        'location' => 'Lantai 1 Gerai 23',
        'status' => 'aktif',
    ]);

    $scheduleToday = Schedule::create([
        'service_id' => $service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 1,
        'is_open' => true,
    ]);

    $scheduleTomorrow = Schedule::create([
        'service_id' => $service->id,
        'date' => now()->addDay()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 1,
        'is_open' => true,
    ]);

    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);

    /** @var User $foAdmin */
    $foAdmin = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    /** @var User $superAdmin */
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    $booking = Booking::create([
        'user_id' => $visitor->id,
        'service_id' => $service->id,
        'schedule_id' => $scheduleToday->id,
        'booking_code' => 'BOOK-12345',
        'status' => 'Pending',
        'booking_date' => $scheduleToday->date,
    ]);

    return compact('department', 'service', 'counter', 'scheduleToday', 'scheduleTomorrow', 'visitor', 'foAdmin', 'superAdmin', 'booking');
}

test('guests are redirected to login when accessing cancellation routes', function () {
    $data = createTestData();

    test()->get(route('profile.edit'))->assertRedirect(route('login'));
    test()->post(route('bookings.cancel', $data['booking']))->assertRedirect(route('login'));
});

test('visitor can cancel their own pending booking before the schedule date', function () {
    Mail::fake();
    $data = createTestData();

    // Ubah jadwal booking ke hari esok agar bisa dibatalkan mandiri oleh pengunjung
    $data['booking']->update([
        'schedule_id' => $data['scheduleTomorrow']->id,
        'booking_date' => $data['scheduleTomorrow']->date,
    ]);

    $response = test()->actingAs($data['visitor'])->post(route('bookings.cancel', $data['booking']));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Booking berhasil dibatalkan.');

    $data['booking']->refresh();
    expect($data['booking']->status)->toBe('Cancelled');
    expect($data['booking']->cancel_reason)->toBe('Dibatalkan oleh pengguna');

    // Cek Log audit menggunakan query()
    $log = ActivityLog::query()->where('action', 'CANCEL_BOOKING')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($data['visitor']->id);
});

test('visitor cannot cancel their booking on the same day as the schedule', function () {
    $data = createTestData();

    // Booking terjadwal hari ini (scheduleToday) tidak boleh dibatalkan mandiri oleh pengunjung
    $response = test()->actingAs($data['visitor'])->post(route('bookings.cancel', $data['booking']));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Booking hari ini tidak dapat dibatalkan secara mandiri. Silakan hubungi Front Office.');

    $data['booking']->refresh();
    expect($data['booking']->status)->toBe('Pending');
});

test('fo admin and super admin can cancel any active booking at any time', function () {
    Mail::fake();
    $data = createTestData();

    // Coba batalkan menggunakan FO Admin
    $response = test()->actingAs($data['foAdmin'])->post(route('bookings.cancel', $data['booking']), [
        'reason' => 'Berkas tidak lengkap di meja verifikasi',
    ]);

    $response->assertRedirect();
    $data['booking']->refresh();
    expect($data['booking']->status)->toBe('Cancelled');
    expect($data['booking']->cancel_reason)->toBe('Berkas tidak lengkap di meja verifikasi');

    // Notifikasi masuk ke database warga/visitor
    test()->assertDatabaseHas('notifications', [
        'user_id' => $data['visitor']->id,
        'title' => 'Booking Dibatalkan oleh Sistem',
    ]);

    // Verifikasi email terkirim
    Mail::assertSent(BookingCancelledMail::class, function ($mail) use ($data) {
        return $mail->hasTo($data['visitor']->email);
    });

    // Cek Log audit FO menggunakan query()
    $log = ActivityLog::query()->where('action', 'CANCEL_BOOKING')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($data['foAdmin']->id);
});
