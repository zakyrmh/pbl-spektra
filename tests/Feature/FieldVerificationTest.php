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

beforeEach(function () {
    // Seed basic department, service, and counter
    $this->department = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
        'description' => 'Kependudukan dan Catatan Sipil',
    ]);

    $this->service = Service::create([
        'department_id' => $this->department->id,
        'name' => 'Cetak KTP',
        'description' => 'Layanan cetak KTP-el baru',
    ]);

    $this->counter = Counter::create([
        'department_id' => $this->department->id,
        'name' => 'Loket Disdukcapil 1',
        'location' => 'Lantai 1 Gerai 23',
        'status' => 'aktif',
    ]);

    // Create a schedule
    $this->schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 1,
        'is_open' => true,
    ]);

    // Setup visitor user
    $this->visitor = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);

    // Setup FO admin
    $this->foAdmin = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    // Create a booking
    $this->booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $this->schedule->id,
        'booking_code' => 'BOOK-12345',
        'status' => 'Pending',
        'booking_date' => $this->schedule->date,
    ]);
});

test('guests are redirected to login when accessing FO checkin routes', function () {
    $this->get(route('admin.fo.checkin'))->assertRedirect(route('login'));
    $this->post(route('admin.fo.checkin.verify'), ['booking_code' => 'BOOK-12345'])->assertRedirect(route('login'));
    $this->post(route('admin.fo.checkin.approve', $this->booking))->assertRedirect(route('login'));
    $this->post(route('admin.fo.checkin.reject', $this->booking), ['reason' => 'Persyaratan tidak sesuai'])->assertRedirect(route('login'));
});

test('visitor role cannot access checkin routes (403)', function () {
    $this->actingAs($this->visitor)->get(route('admin.fo.checkin'))->assertStatus(403);
    $this->actingAs($this->visitor)->post(route('admin.fo.checkin.verify'), ['booking_code' => 'BOOK-12345'])->assertStatus(403);
    $this->actingAs($this->visitor)->post(route('admin.fo.checkin.approve', $this->booking))->assertStatus(403);
    $this->actingAs($this->visitor)->post(route('admin.fo.checkin.reject', $this->booking), ['reason' => 'Persyaratan tidak sesuai'])->assertStatus(403);
});

test('FO admin can search and load booking verification details', function () {
    $response = $this->actingAs($this->foAdmin)
        ->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'BOOK-12345',
        ]);

    $response->assertStatus(200);
    $response->assertSee('Langkah Verifikasi Fisik');
    $response->assertSee('Cetak KTP');
    $response->assertSee($this->visitor->name);
});

test('searching booking with empty NIK prompts for NIK input', function () {
    // Set NIK to empty
    $this->visitor->update(['nik' => '']);

    $response = $this->actingAs($this->foAdmin)
        ->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'BOOK-12345',
        ]);

    $response->assertStatus(200);
    $response->assertSee('Profil Belum Lengkap');
    $response->assertSee('Masukkan NIK Warga');
});

test('submitting NIK updates visitor profile and displays verification panel', function () {
    // Set NIK to empty
    $this->visitor->update(['nik' => '']);

    $response = $this->actingAs($this->foAdmin)
        ->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'BOOK-12345',
            'nik_input' => '9876543210123456',
        ]);

    $response->assertStatus(200);
    $response->assertSee('Langkah Verifikasi Fisik');

    // Verify NIK updated in DB
    $this->visitor->refresh();
    expect($this->visitor->nik)->toBe('9876543210123456');

    // Verify activity log for NIK update
    $log = ActivityLog::where('action', 'UPDATE_NIK')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($this->visitor->id);
});

test('FO admin can approve checkin and issue a queue number', function () {
    Event::fake();

    $response = $this->actingAs($this->foAdmin)
        ->post(route('admin.fo.checkin.approve', $this->booking));

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('success');

    // Verify DB update
    $this->booking->refresh();
    expect($this->booking->status)->toBe('Checked-In');
    expect($this->booking->checked_in_at)->not->toBeNull();

    // Verify Queue record created
    $queue = Queue::where('booking_id', $this->booking->id)->first();
    expect($queue)->not->toBeNull();
    expect($queue->queue_number)->toBe('DDK-001'); // First counter of DDK
    expect($queue->status)->toBe('Waiting');

    // Verify event dispatched
    Event::assertDispatched(QueueCreated::class, function ($event) use ($queue) {
        return $event->queue->id === $queue->id;
    });

    // Verify activity log
    $log = ActivityLog::where('action', 'VERIFY_CHECKIN')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($this->foAdmin->id);
    expect($log->description)->toContain('DDK-001');
});

test('FO admin can reject a booking with a reason', function () {
    Mail::fake();

    $response = $this->actingAs($this->foAdmin)
        ->post(route('admin.fo.checkin.reject', $this->booking), [
            'reason' => 'Berkas persyaratan fotokopi KK tidak dilampirkan',
        ]);

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('success');

    // Verify DB update
    $this->booking->refresh();
    expect($this->booking->status)->toBe('Cancelled');
    expect($this->booking->cancel_reason)->toBe('Berkas persyaratan fotokopi KK tidak dilampirkan');

    // Verify database notification
    $notif = Notification::where('user_id', $this->visitor->id)->first();
    expect($notif)->not->toBeNull();
    expect($notif->title)->toBe('Booking Ditolak FO');

    // Verify email sent
    Mail::assertSent(BookingCancelledMail::class, function ($mail) {
        return $mail->hasTo($this->visitor->email) && $mail->booking->id === $this->booking->id;
    });

    // Verify activity log
    $log = ActivityLog::where('action', 'REJECT_BOOKING')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($this->foAdmin->id);
});
