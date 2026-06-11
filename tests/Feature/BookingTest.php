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

// Fungsi pembantu untuk membuat data awal (menggantikan beforeEach)
function createTestData()
{
    $department = Department::create([
        'name' => 'Dinas Kesehatan',
        'inisial' => 'DKS',
        'description' => 'Pelayanan Kesehatan Kota Sawahlunto',
    ]);

    $service = Service::create([
        'department_id' => $department->id,
        'name' => 'Keluarga Berencana',
        'description' => 'Layanan KB gratis',
    ]);

    $schedule = Schedule::create([
        'service_id' => $service->id,
        'date' => now()->addDay()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 5,
        'quota_used' => 0,
        'is_open' => true,
    ]);

    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);

    return compact('department', 'service', 'schedule', 'visitor');
}

test('guests are redirected to login when accessing booking pages', function () {
    createTestData(); // Jalankan seeding dasar

    test()->get(route('booking.index'))->assertRedirect(route('login'));
    test()->get(route('booking.create'))->assertRedirect(route('login'));
    test()->post(route('booking.store'), [])->assertRedirect(route('login'));
});

test('authenticated visitor can access booking index and create form', function () {
    $data = createTestData();

    $response = test()->actingAs($data['visitor'])->get(route('booking.index'));
    $response->assertStatus(200);
    $response->assertSee('Riwayat & Status Antrean', false);

    $response = test()->actingAs($data['visitor'])->get(route('booking.create'));
    $response->assertStatus(200);
    $response->assertSee('Ambil Nomor Antrean Mandiri');
    $response->assertSee('Dinas Kesehatan');
});

test('visitor can book a slot successfully', function () {
    Mail::fake();
    $data = createTestData();

    /** @var User $foAdmin */
    $foAdmin = User::factory()->create(['role' => 'admin_fo']);

    $response = test()->actingAs($data['visitor'])->post(route('booking.store'), [
        'service_id' => $data['service']->id,
        'schedule_id' => $data['schedule']->id,
    ]);

    $booking = Booking::first();
    expect($booking)->not->toBeNull();
    $response->assertRedirect(route('booking.show', $booking));

    $data['schedule']->refresh();
    expect($data['schedule']->quota_used)->toBe(1);

    expect($booking->status)->toBe('Pending');
    expect($booking->user_id)->toBe($data['visitor']->id);
    expect($booking->service_id)->toBe($data['service']->id);
    expect($booking->schedule_id)->toBe($data['schedule']->id);
    expect($booking->booking_date->toDateString())->toBe($data['schedule']->date->toDateString());

    Mail::assertSent(BookingSuccessMail::class, function ($mail) use ($data, $booking) {
        return $mail->hasTo($data['visitor']->email) && $mail->booking->id === $booking->id;
    });

    $custNotif = Notification::where('user_id', $data['visitor']->id)->first();
    expect($custNotif)->not->toBeNull();
    expect($custNotif->title)->toBe('Booking Antrean Berhasil');

    $foNotif = Notification::where('user_id', $foAdmin->id)->first();
    expect($foNotif)->not->toBeNull();
    expect($foNotif->title)->toBe('Booking Baru Masuk');

    $log = ActivityLog::query()->where('action', 'CREATE_BOOKING')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($data['visitor']->id);
    expect($log->description)->toContain($booking->booking_code);
});

test('validation rejects missing fields', function () {
    $data = createTestData();

    $response = test()->actingAs($data['visitor'])
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
    $data = createTestData();

    Booking::create([
        'user_id' => $data['visitor']->id,
        'service_id' => $data['service']->id,
        'schedule_id' => $data['schedule']->id,
        'booking_code' => 'booking-1',
        'status' => 'Pending',
        'booking_date' => $data['schedule']->date,
    ]);

    $response = test()->actingAs($data['visitor'])
        ->from(route('booking.create'))
        ->post(route('booking.store'), [
            'service_id' => $data['service']->id,
            'schedule_id' => $data['schedule']->id,
        ]);

    $response->assertRedirect(route('booking.create'));
    $response->assertSessionHasErrors(['error']);

    $errors = session('errors');
    expect($errors->first('error'))->toContain('Anda sudah memiliki booking aktif (Pending) untuk layanan ini pada tanggal tersebut.');
});

test('visitor cannot book if schedule quota is full (BR-03)', function () {
    $data = createTestData();
    $data['schedule']->update(['quota_used' => 5]);

    $response = test()->actingAs($data['visitor'])
        ->from(route('booking.create'))
        ->post(route('booking.store'), [
            'service_id' => $data['service']->id,
            'schedule_id' => $data['schedule']->id,
        ]);

    $response->assertRedirect(route('booking.create'));
    $response->assertSessionHasErrors(['booking_date']);

    $errors = session('errors');
    expect($errors->first('booking_date'))->toContain('Jadwal tidak tersedia atau kuota penuh pada tanggal terpilih untuk instansi ini.');
});

test('booking ticket view has access control checks', function () {
    $data = createTestData();
    $owner = $data['visitor'];

    /** @var User $otherUser */
    $otherUser = User::factory()->create(['role' => 'pengunjung']);
    /** @var User $foAdmin */
    $foAdmin = User::factory()->create(['role' => 'admin_fo']);
    /** @var User $superAdmin */
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    $booking = Booking::create([
        'user_id' => $owner->id,
        'service_id' => $data['service']->id,
        'schedule_id' => $data['schedule']->id,
        'booking_code' => 'booking-uuid-xyz',
        'status' => 'Pending',
        'booking_date' => $data['schedule']->date,
    ]);

    // 1. Owner can view
    test()->actingAs($owner)->get(route('booking.show', $booking))->assertStatus(200);

    // 2. FO Admin can view
    test()->actingAs($foAdmin)->get(route('booking.show', $booking))->assertStatus(200);

    // 3. Super Admin can view
    test()->actingAs($superAdmin)->get(route('booking.show', $booking))->assertStatus(200);

    // 4. Other visitor is forbidden (403)
    test()->actingAs($otherUser)->get(route('booking.show', $booking))->assertStatus(403);
});
