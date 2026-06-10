<?php

use App\Mail\BookingSuccessMail;
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

    // Create a schedule
    $this->schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->addDay()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 5,
        'quota_used' => 0,
        'is_open' => true,
    ]);

    // Setup visitor
    $this->visitor = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);
});

test('guests are redirected to login when accessing booking pages', function () {
    $this->get(route('booking.index'))->assertRedirect(route('login'));
    $this->get(route('booking.create'))->assertRedirect(route('login'));
    $this->post(route('booking.store'), [])->assertRedirect(route('login'));
});

test('authenticated visitor can access booking index and create form', function () {
    $response = $this->actingAs($this->visitor)->get(route('booking.index'));
    $response->assertStatus(200);
    $response->assertSee('Riwayat & Status Antrean', false);

    $response = $this->actingAs($this->visitor)->get(route('booking.create'));
    $response->assertStatus(200);
    $response->assertSee('Ambil Nomor Antrean Mandiri');
    $response->assertSee('Dinas Kesehatan');
});

test('visitor can book a slot successfully', function () {
    Mail::fake();

    // Create an FO admin to test notification delivery to FO
    $foAdmin = User::factory()->create(['role' => 'admin_fo']);

    $response = $this->actingAs($this->visitor)->post(route('booking.store'), [
        'department_id' => $this->department->id,
        'keperluan' => 'Ketik keperluan di sini',
        'booking_date' => $this->schedule->date->toDateString(),
    ]);

    // Should redirect to ticket show page
    $booking = Booking::first();
    expect($booking)->not->toBeNull();
    $response->assertRedirect(route('booking.show', $booking));

    // Verify schedule quota was updated
    $this->schedule->refresh();
    expect($this->schedule->quota_used)->toBe(1);

    // Verify booking attributes
    expect($booking->status)->toBe('Pending');
    expect($booking->user_id)->toBe($this->visitor->id);
    expect($booking->service_id)->toBe($this->service->id);
    expect($booking->schedule_id)->toBe($this->schedule->id);
    expect($booking->booking_date->toDateString())->toBe($this->schedule->date->toDateString());
    expect($booking->purpose)->toBe('Ketik keperluan di sini');

    // Verify confirmation email was sent
    Mail::assertSent(BookingSuccessMail::class, function ($mail) use ($booking) {
        return $mail->hasTo($this->visitor->email) && $mail->booking->id === $booking->id;
    });

    // Verify notification records for customer and FO admin
    $custNotif = Notification::where('user_id', $this->visitor->id)->first();
    expect($custNotif)->not->toBeNull();
    expect($custNotif->title)->toBe('Booking Antrean Berhasil');

    $foNotif = Notification::where('user_id', $foAdmin->id)->first();
    expect($foNotif)->not->toBeNull();
    expect($foNotif->title)->toBe('Booking Baru Masuk');

    // Verify activity logs
    $log = ActivityLog::where('action', 'CREATE_BOOKING')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($this->visitor->id);
    expect($log->description)->toContain($booking->booking_code);
});

test('validation rejects missing fields', function () {
    $response = $this->actingAs($this->visitor)
        ->from(route('booking.create'))
        ->post(route('booking.store'), [
            'department_id' => '',
            'keperluan' => '',
            'booking_date' => '',
        ]);

    $response->assertRedirect(route('booking.create'));
    $response->assertSessionHasErrors(['department_id', 'keperluan', 'booking_date']);
});

test('visitor cannot book the same service twice for the same date (BR-06)', function () {
    // Create first booking
    Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $this->schedule->id,
        'booking_code' => 'booking-1',
        'status' => 'Pending',
        'booking_date' => $this->schedule->date,
    ]);

    // Try booking again for same service & schedule (same date)
    $response = $this->actingAs($this->visitor)
        ->from(route('booking.create'))
        ->post(route('booking.store'), [
            'department_id' => $this->department->id,
            'keperluan' => 'Ketik keperluan di sini',
            'booking_date' => $this->schedule->date->toDateString(),
        ]);

    $response->assertRedirect(route('booking.create'));
    $response->assertSessionHasErrors(['error']);

    // Check custom error message
    $errors = session('errors');
    expect($errors->first('error'))->toContain('Anda sudah memiliki booking aktif (Pending) untuk layanan ini pada tanggal tersebut.');
});

test('visitor cannot book if schedule quota is full (BR-03)', function () {
    // Fill the quota
    $this->schedule->update(['quota_used' => 5]);

    $response = $this->actingAs($this->visitor)
        ->from(route('booking.create'))
        ->post(route('booking.store'), [
            'department_id' => $this->department->id,
            'keperluan' => 'Ketik keperluan di sini',
            'booking_date' => $this->schedule->date->toDateString(),
        ]);

    $response->assertRedirect(route('booking.create'));
    $response->assertSessionHasErrors(['booking_date']);

    $errors = session('errors');
    expect($errors->first('booking_date'))->toContain('Jadwal tidak tersedia atau kuota penuh pada tanggal terpilih untuk instansi ini.');
});

test('booking ticket view has access control checks', function () {
    $owner = $this->visitor;
    $otherUser = User::factory()->create(['role' => 'pengunjung']);
    $foAdmin = User::factory()->create(['role' => 'admin_fo']);
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    $booking = Booking::create([
        'user_id' => $owner->id,
        'service_id' => $this->service->id,
        'schedule_id' => $this->schedule->id,
        'booking_code' => 'booking-uuid-xyz',
        'status' => 'Pending',
        'booking_date' => $this->schedule->date,
    ]);

    // 1. Owner can view
    $this->actingAs($owner)->get(route('booking.show', $booking))->assertStatus(200);

    // 2. FO Admin can view
    $this->actingAs($foAdmin)->get(route('booking.show', $booking))->assertStatus(200);

    // 3. Super Admin can view
    $this->actingAs($superAdmin)->get(route('booking.show', $booking))->assertStatus(200);

    // 4. Other visitor is forbidden (403)
    $this->actingAs($otherUser)->get(route('booking.show', $booking))->assertStatus(403);
});
