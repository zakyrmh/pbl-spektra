<?php

use App\Events\QueueCreated;
use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
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

    $schedule = Schedule::create([
        'service_id' => $service->id,
        'date' => now()->toDateString(),
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

    $booking = Booking::create([
        'user_id' => $visitor->id,
        'service_id' => $service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BOOK-12345',
        'status' => 'Pending',
        'booking_date' => $schedule->date,
    ]);

    return compact('department', 'service', 'counter', 'schedule', 'visitor', 'foAdmin', 'booking');
}

test('guests are redirected to login when accessing FO checkin routes', function () {
    $data = createTestData();

    test()->get(route('admin.fo.checkin'))->assertRedirect(route('login'));
    test()->post(route('admin.fo.checkin.verify'), ['booking_code' => 'BOOK-12345'])->assertRedirect(route('login'));
    test()->post(route('admin.fo.checkin.approve', $data['booking']))->assertRedirect(route('login'));
    test()->post(route('admin.fo.checkin.reject', $data['booking']), ['reason' => 'Persyaratan tidak sesuai'])->assertRedirect(route('login'));
});

test('visitor role cannot access checkin routes (403)', function () {
    $data = createTestData();

    test()->actingAs($data['visitor'])->get(route('admin.fo.checkin'))->assertStatus(403);
    test()->actingAs($data['visitor'])->post(route('admin.fo.checkin.verify'), ['booking_code' => 'BOOK-12345'])->assertStatus(403);
    test()->actingAs($data['visitor'])->post(route('admin.fo.checkin.approve', $data['booking']))->assertStatus(403);
    test()->actingAs($data['visitor'])->post(route('admin.fo.checkin.reject', $data['booking']), ['reason' => 'Persyaratan tidak sesuai'])->assertStatus(403);
});

test('FO admin can search and load booking verification details', function () {
    $data = createTestData();

    $response = test()->actingAs($data['foAdmin'])
        ->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'BOOK-12345',
        ]);

    $response->assertStatus(200);
    $response->assertSee('Langkah Verifikasi Fisik');
    $response->assertSee('Cetak KTP');
    $response->assertSee($data['visitor']->name);
});

test('searching booking with empty NIK prompts for NIK input', function () {
    $data = createTestData();
    $data['visitor']->update(['nik' => '']);

    $response = test()->actingAs($data['foAdmin'])
        ->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'BOOK-12345',
        ]);

    $response->assertStatus(200);
    $response->assertSee('Profil Belum Lengkap');
    $response->assertSee('Masukkan NIK Warga');
});

test('submitting NIK updates visitor profile and displays verification panel', function () {
    $data = createTestData();
    $data['visitor']->update(['nik' => '']);

    $response = test()->actingAs($data['foAdmin'])
        ->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'BOOK-12345',
            'nik_input' => '9876543210123456',
        ]);

    $response->assertStatus(200);
    $response->assertSee('Langkah Verifikasi Fisik');

    $data['visitor']->refresh();
    expect($data['visitor']->nik)->toBe('9876543210123456');

    $log = ActivityLog::query()->where('action', 'UPDATE_NIK')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($data['foAdmin']->id);
    expect($log->model_id)->toBe($data['visitor']->id);
});

test('FO admin can approve checkin and issue a queue number', function () {
    Event::fake();
    $data = createTestData();

    $response = test()->actingAs($data['foAdmin'])
        ->post(route('admin.fo.checkin.approve', $data['booking']));

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('success');

    $data['booking']->refresh();
    expect($data['booking']->status)->toBe('Checked-In');
    expect($data['booking']->checked_in_at)->not->toBeNull();

    $queue = Queue::where('booking_id', $data['booking']->id)->first();
    expect($queue)->not->toBeNull();
    expect($queue->queue_number)->toBe('DDK-001');
    expect($queue->status)->toBe('Waiting');

    Event::assertDispatched(QueueCreated::class, function ($event) use ($queue) {
        return $event->queue->id === $queue->id;
    });

    $log = ActivityLog::query()->where('action', 'VERIFY_CHECKIN')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($data['foAdmin']->id);
    expect($log->description)->toContain('DDK-001');
});

test('FO admin can reject a booking with a reason', function () {
    Mail::fake();
    $data = createTestData();

    $response = test()->actingAs($data['foAdmin'])
        ->post(route('admin.fo.checkin.reject', $data['booking']), [
            'reason' => 'Berkas persyaratan fotokopi KK tidak dilampirkan',
        ]);

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('success');

    $data['booking']->refresh();
    expect($data['booking']->status)->toBe('Cancelled');
    expect($data['booking']->cancel_reason)->toBe('Berkas persyaratan fotokopi KK tidak dilampirkan');

    $notif = Notification::where('user_id', $data['visitor']->id)->first();
    expect($notif)->not->toBeNull();
    expect($notif->title)->toBe('Booking Ditolak FO');

    Mail::assertSent(BookingCancelledMail::class, function ($mail) use ($data) {
        return $mail->hasTo($data['visitor']->email) && $mail->booking->id === $data['booking']->id;
    });

    $log = ActivityLog::query()->where('action', 'REJECT_BOOKING')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($data['foAdmin']->id);
});
